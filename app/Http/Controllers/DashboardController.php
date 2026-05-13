<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $monitors = $request->user()->monitors;

        $totalMonitors = $monitors->count();
        $operationalCount = $monitors->where('status', 'operational')->count();
        $degradedCount = $monitors->where('status', 'degraded')->count();
        $downCount = $monitors->where('status', 'down')->count();

        $averageUptime = $monitors->isEmpty()
            ? 100
            : round($monitors->avg('uptime'), 2);

        $averageResponseTime = $monitors->whereNotNull('avg_response_time')->isEmpty()
            ? 0
            : (int) $monitors->whereNotNull('avg_response_time')->avg('avg_response_time');

        $systemStatus = match (true) {
            $downCount > 0 => 'down',
            $degradedCount > 0 => 'degraded',
            default => 'operational',
        };

        $recentIncidents = Incident::query()
            ->whereHas('monitor', fn ($q) => $q->where('user_id', $request->user()->id))
            ->where('created_at', '>=', now()->subDays(7))
            ->with('monitor:id,name')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($incident) => [
                ...$incident->toArray(),
                'duration' => $incident->duration,
                'timestamp' => $incident->created_at->diffForHumans(),
            ]);

        return response()->json([
            'total_monitors' => $totalMonitors,
            'operational_count' => $operationalCount,
            'degraded_count' => $degradedCount,
            'down_count' => $downCount,
            'average_uptime' => $averageUptime,
            'average_response_time' => $averageResponseTime,
            'system_status' => $systemStatus,
            'total_incidents' => $recentIncidents->count(),
            'recent_incidents' => $recentIncidents,
        ]);
    }
}
