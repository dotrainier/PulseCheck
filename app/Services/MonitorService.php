<?php

namespace App\Services;

use App\Mail\MonitorDownAlert;
use App\Mail\MonitorRecoveredAlert;
use App\Models\Incident;
use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MonitorService
{
    public function runCheck(Monitor $monitor): MonitorCheck
    {
        $start = microtime(true);
        $success = false;
        $statusCode = null;
        $responseTime = null;
        $message = null;

        try {
            $response = Http::timeout($monitor->timeout)
                ->withOptions(['verify' => false])
                ->get($monitor->url);

            $responseTime = (int) ((microtime(true) - $start) * 1000);
            $statusCode = $response->status();

            if ($monitor->expected_status_code) {
                $success = $statusCode === (int) $monitor->expected_status_code;
            } else {
                $success = $response->successful();
            }

            $message = $success ? "OK - Status {$statusCode}" : "HTTP {$statusCode}";

            if ($monitor->track_ssl && str_starts_with($monitor->url, 'https://')) {
                $this->updateSslInfo($monitor);
            }
        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $start) * 1000);
            $message = substr($e->getMessage(), 0, 255);
            $success = false;
        }

        $check = $monitor->checks()->create([
            'success' => $success,
            'status_code' => $statusCode,
            'response_time' => $responseTime,
            'message' => $message,
        ]);

        $this->updateMonitorStats($monitor, $success, $responseTime);
        $this->handleIncidents($monitor, $success, $message, $statusCode);

        return $check;
    }

    private function updateMonitorStats(Monitor $monitor, bool $success, ?int $responseTime): void
    {
        $thirtyDaysAgo = now()->subDays(30);
        $totalChecks = $monitor->checks()->where('created_at', '>=', $thirtyDaysAgo)->count();
        $successfulChecks = $monitor->checks()
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->where('success', true)
            ->count();

        $uptime = $totalChecks > 0 ? round(($successfulChecks / $totalChecks) * 100, 2) : 100;

        $avgResponseTime = $monitor->checks()
            ->where('created_at', '>=', now()->subDay())
            ->where('success', true)
            ->avg('response_time');

        $recentChecks = $monitor->checks()->latest()->take(3)->get();
        $consecutiveFailures = 0;
        foreach ($recentChecks as $check) {
            if (! $check->success) {
                $consecutiveFailures++;
            } else {
                break;
            }
        }

        $status = 'operational';
        if ($consecutiveFailures >= 3) {
            $status = 'down';
        } elseif ($consecutiveFailures >= 1 || ($avgResponseTime && $avgResponseTime > 1000)) {
            $status = 'degraded';
        }

        $monitor->update([
            'status' => $status,
            'uptime' => $uptime,
            'avg_response_time' => $avgResponseTime ? (int) $avgResponseTime : $monitor->avg_response_time,
            'total_checks' => $monitor->total_checks + 1,
            'last_checked_at' => now(),
        ]);
    }

    private function handleIncidents(Monitor $monitor, bool $success, ?string $message, ?int $statusCode): void
    {
        $activeIncident = $monitor->incidents()
            ->whereIn('status', ['investigating', 'identified'])
            ->latest()
            ->first();

        if (! $success) {
            if ($activeIncident) {
                $activeIncident->increment('failed_checks');
            } else {
                $severity = $statusCode === null ? 'critical' : 'warning';
                $incident = $monitor->incidents()->create([
                    'severity' => $severity,
                    'status' => 'investigating',
                    'message' => $message ?? 'Service unreachable',
                    'error_details' => $message,
                    'impact' => $severity === 'critical' ? 'Complete outage' : 'Degraded performance',
                    'failed_checks' => 1,
                ]);
                $incident->updates()->create([
                    'message' => 'Incident detected. Team has been notified.',
                ]);

                $ownerEmail = $monitor->user->email ?? null;
                if ($ownerEmail) {
                    Mail::to($ownerEmail)->queue(new MonitorDownAlert($monitor, $incident));
                }
            }
        } elseif ($activeIncident) {
            $activeIncident->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
            $activeIncident->updates()->create([
                'message' => 'Service has recovered. All systems operational.',
            ]);

            $ownerEmail = $monitor->user->email ?? null;
            if ($ownerEmail) {
                $activeIncident->refresh();
                Mail::to($ownerEmail)->queue(new MonitorRecoveredAlert($monitor, $activeIncident));
            }
        }
    }

    private function updateSslInfo(Monitor $monitor): void
    {
        try {
            $host = parse_url($monitor->url, PHP_URL_HOST);
            $port = parse_url($monitor->url, PHP_URL_PORT) ?? 443;

            $context = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
            $socket = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno, $errstr, 10,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if ($socket) {
                $params = stream_context_get_params($socket);
                $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);

                if ($cert) {
                    $expiryTimestamp = $cert['validTo_time_t'];
                    $daysRemaining = (int) ceil(($expiryTimestamp - time()) / 86400);

                    $monitor->update([
                        'ssl_expiry_date' => date('M j, Y', $expiryTimestamp),
                        'ssl_issuer' => $cert['issuer']['O'] ?? 'Unknown',
                        'ssl_days_remaining' => $daysRemaining,
                        'ssl_expiring' => $daysRemaining <= 30,
                    ]);
                }

                fclose($socket);
            }
        } catch (\Exception $e) {
            Log::warning("SSL check failed for monitor {$monitor->id}: {$e->getMessage()}");
        }
    }

    public function getUptimeHistory(Monitor $monitor, int $days = 30): array
    {
        $history = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->toDateString();

            $total = $monitor->checks()->whereDate('created_at', $dateStr)->count();
            $successful = $monitor->checks()
                ->whereDate('created_at', $dateStr)
                ->where('success', true)
                ->count();

            $history[] = [
                'date' => $date->format('M j'),
                'uptime' => $total > 0 ? round(($successful / $total) * 100, 2) : ($i === 0 ? 100 : 100),
            ];
        }

        return $history;
    }

    public function getResponseTimeHistory(Monitor $monitor, int $hours = 24): array
    {
        return $monitor->checks()
            ->where('created_at', '>=', now()->subHours($hours))
            ->where('success', true)
            ->orderBy('created_at')
            ->get(['created_at', 'response_time'])
            ->map(fn ($check) => [
                'time' => $check->created_at->format('H:i'),
                'value' => $check->response_time,
            ])
            ->toArray();
    }
}
