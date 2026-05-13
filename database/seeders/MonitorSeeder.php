<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\Monitor;
use App\Models\MonitorCheck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MonitorSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $monitorsData = [
            [
                'name' => 'Production API',
                'url' => 'https://httpbin.org/status/200',
                'check_interval' => '30s',
                'expected_status_code' => 200,
                'timeout' => 30,
                'track_ssl' => true,
                'status' => 'operational',
                'uptime' => 99.98,
                'avg_response_time' => 45,
                'ssl_expiry_date' => 'Aug 15, 2026',
                'ssl_issuer' => "Let's Encrypt",
                'ssl_days_remaining' => 95,
                'ssl_expiring' => false,
            ],
            [
                'name' => 'Marketing Website',
                'url' => 'https://httpbin.org/get',
                'check_interval' => '1m',
                'expected_status_code' => 200,
                'timeout' => 30,
                'track_ssl' => true,
                'status' => 'operational',
                'uptime' => 100.00,
                'avg_response_time' => 38,
                'ssl_expiry_date' => 'Sep 22, 2026',
                'ssl_issuer' => 'DigiCert',
                'ssl_days_remaining' => 133,
                'ssl_expiring' => false,
            ],
            [
                'name' => 'CDN Service',
                'url' => 'https://httpbin.org/status/200',
                'check_interval' => '5m',
                'expected_status_code' => 200,
                'timeout' => 30,
                'track_ssl' => true,
                'status' => 'operational',
                'uptime' => 99.95,
                'avg_response_time' => 52,
                'ssl_expiry_date' => 'May 26, 2026',
                'ssl_issuer' => 'Cloudflare',
                'ssl_days_remaining' => 14,
                'ssl_expiring' => true,
            ],
            [
                'name' => 'Auth Service',
                'url' => 'https://httpbin.org/delay/1',
                'check_interval' => '30s',
                'expected_status_code' => 200,
                'timeout' => 30,
                'track_ssl' => true,
                'status' => 'degraded',
                'uptime' => 99.97,
                'avg_response_time' => 165,
                'ssl_expiry_date' => 'Oct 10, 2026',
                'ssl_issuer' => "Let's Encrypt",
                'ssl_days_remaining' => 151,
                'ssl_expiring' => false,
            ],
            [
                'name' => 'Database Health',
                'url' => 'https://httpbin.org/status/200',
                'check_interval' => '1m',
                'expected_status_code' => 200,
                'timeout' => 30,
                'track_ssl' => true,
                'status' => 'operational',
                'uptime' => 99.99,
                'avg_response_time' => 28,
                'ssl_expiry_date' => 'Dec 1, 2026',
                'ssl_issuer' => 'DigiCert',
                'ssl_days_remaining' => 203,
                'ssl_expiring' => false,
            ],
            [
                'name' => 'Legacy API',
                'url' => 'https://httpbin.org/status/500',
                'check_interval' => '5m',
                'expected_status_code' => 200,
                'timeout' => 30,
                'track_ssl' => false,
                'status' => 'down',
                'uptime' => 98.50,
                'avg_response_time' => null,
                'ssl_expiry_date' => null,
                'ssl_issuer' => null,
                'ssl_days_remaining' => null,
                'ssl_expiring' => false,
            ],
        ];

        foreach ($monitorsData as $data) {
            $monitor = $user->monitors()->create([
                ...$data,
                'total_checks' => 0,
                'last_checked_at' => now()->subMinutes(rand(1, 5)),
            ]);

            $this->seedCheckHistory($monitor, $data['status']);
            $this->seedIncidents($monitor, $data['name']);
        }
    }

    private function seedCheckHistory(Monitor $monitor, string $status): void
    {
        $checks = [];
        $now = now();

        // Generate 288 checks over the last 24 hours (one every 5 minutes)
        for ($i = 287; $i >= 0; $i--) {
            $timestamp = $now->copy()->subMinutes($i * 5);
            $isSuccess = $this->shouldSucceed($status, $i);
            $responseTime = $isSuccess ? rand(20, 200) : null;

            $checks[] = [
                'monitor_id' => $monitor->id,
                'success' => $isSuccess,
                'status_code' => $isSuccess ? 200 : ($status === 'down' ? null : 500),
                'response_time' => $responseTime,
                'message' => $isSuccess ? 'OK - Status 200' : ($status === 'down' ? 'Connection refused' : 'HTTP 500'),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($checks, 100) as $chunk) {
            MonitorCheck::insert($chunk);
        }

        $monitor->update(['total_checks' => count($checks)]);
    }

    private function shouldSucceed(string $status, int $minutesAgo): bool
    {
        return match ($status) {
            'down' => $minutesAgo > 24,  // was up earlier, down recently
            'degraded' => rand(1, 100) > 10,
            default => rand(1, 100) > 1, // 99% success for operational
        };
    }

    private function seedIncidents(Monitor $monitor, string $name): void
    {
        if ($name === 'Legacy API') {
            $incident = $monitor->incidents()->create([
                'severity' => 'critical',
                'status' => 'investigating',
                'message' => 'Service unreachable - Connection refused',
                'error_details' => 'ECONNREFUSED: Connection refused',
                'impact' => 'Complete outage',
                'failed_checks' => 24,
                'resolved_at' => null,
                'created_at' => now()->subHours(2),
            ]);
            $incident->updates()->createMany([
                ['message' => 'Team notified. Investigating root cause.', 'created_at' => now()->subMinutes(105)],
                ['message' => 'Identified network connectivity issue with legacy infrastructure.', 'created_at' => now()->subMinutes(90)],
                ['message' => 'Working with infrastructure team to restore connectivity.', 'created_at' => now()->subMinutes(60)],
            ]);
        }

        if ($name === 'Auth Service') {
            $incident = $monitor->incidents()->create([
                'severity' => 'warning',
                'status' => 'resolved',
                'message' => 'High response time detected (>500ms average)',
                'error_details' => null,
                'impact' => 'Degraded performance',
                'failed_checks' => 15,
                'resolved_at' => now()->subHours(4),
                'created_at' => now()->subHours(5),
            ]);
            $incident->updates()->createMany([
                ['message' => 'Elevated response times detected. Investigating database queries.', 'created_at' => now()->subMinutes(285)],
                ['message' => 'Identified slow query causing bottleneck. Optimizing indexes.', 'created_at' => now()->subMinutes(270)],
                ['message' => 'Database optimization complete. Service restored to normal.', 'created_at' => now()->subMinutes(240)],
            ]);
        }

        if ($name === 'CDN Service') {
            $incident = $monitor->incidents()->create([
                'severity' => 'warning',
                'status' => 'identified',
                'message' => 'SSL certificate expiring in 14 days',
                'error_details' => 'SSL certificate expires on May 26, 2026',
                'impact' => 'None (preventive)',
                'failed_checks' => 0,
                'resolved_at' => null,
                'created_at' => now()->subDay(),
            ]);
            $incident->updates()->createMany([
                ['message' => 'SSL certificate renewal process initiated automatically.', 'created_at' => now()->subHours(23)],
                ['message' => 'Awaiting certificate authority verification.', 'created_at' => now()->subHours(18)],
            ]);
        }

        if ($name === 'Production API') {
            $incident = $monitor->incidents()->create([
                'severity' => 'critical',
                'status' => 'resolved',
                'message' => 'HTTP 500 Internal Server Error returned',
                'error_details' => 'HTTP 500: Database connection pool exhausted',
                'impact' => 'Partial outage',
                'failed_checks' => 83,
                'resolved_at' => now()->subDays(2)->addHours(1),
                'created_at' => now()->subDays(2),
            ]);
            $incident->updates()->createMany([
                ['message' => 'Multiple 500 errors detected. Investigating immediately.', 'created_at' => now()->subDays(2)->addMinutes(5)],
                ['message' => 'Root cause: Database connection pool exhaustion.', 'created_at' => now()->subDays(2)->addMinutes(20)],
                ['message' => 'Increased pool size and restarted service. Monitoring recovery.', 'created_at' => now()->subDays(2)->addMinutes(50)],
                ['message' => 'All systems operational. Incident resolved.', 'created_at' => now()->subDays(2)->addHours(1)],
            ]);
        }
    }
}
