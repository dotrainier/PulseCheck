<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Incident::query()
            ->whereHas('monitor', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['monitor:id,name', 'updates'])
            ->latest();

        if ($request->filled('severity') && in_array($request->severity, ['critical', 'warning'])) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status') && in_array($request->status, ['resolved', 'investigating', 'identified'])) {
            $query->where('status', $request->status);
        }

        $incidents = $query->get()->map(fn ($incident) => [
            ...$incident->toArray(),
            'duration' => $incident->duration,
            'start_time' => $incident->created_at->diffForHumans(),
        ]);

        $resolved = $incidents->filter(fn ($i) => $i['status'] === 'resolved' && isset($i['resolved_at']));

        $avgDowntime = $resolved->isNotEmpty()
            ? round($resolved->avg(fn ($i) => Carbon::parse($i['created_at'])->diffInMinutes(Carbon::parse($i['resolved_at'])))) . 'min'
            : 'N/A';

        return response()->json([
            'data' => $incidents,
            'stats' => [
                'total' => $incidents->count(),
                'critical' => $incidents->where('severity', 'critical')->count(),
                'average_downtime' => $avgDowntime,
                'mttr' => $avgDowntime,
            ],
        ]);
    }

    public function show(Request $request, Incident $incident): JsonResponse
    {
        $this->authorize('view', $incident);
        $incident->load(['monitor:id,name', 'updates']);

        return response()->json([
            'data' => [
                ...$incident->toArray(),
                'duration' => $incident->duration,
                'start_time' => $incident->created_at->diffForHumans(),
            ],
        ]);
    }
}
