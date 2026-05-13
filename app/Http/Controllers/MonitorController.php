<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use App\Services\MonitorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function __construct(private MonitorService $monitorService) {}

    public function index(Request $request): JsonResponse
    {
        $monitors = $request->user()->monitors()->latest()->get();

        return response()->json(['data' => $monitors]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'check_interval' => 'required|in:30s,1m,5m,15m,30m,1h,6h,24h',
            'expected_status_code' => 'nullable|integer|between:100,599',
            'timeout' => 'nullable|integer|between:1,120',
            'track_ssl' => 'boolean',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['track_ssl'] = $validated['track_ssl'] ?? false;
        $validated['timeout'] = $validated['timeout'] ?? 30;

        $monitor = Monitor::create($validated);

        // Run initial check in background-compatible way
        try {
            $this->monitorService->runCheck($monitor);
        } catch (\Exception $e) {
            // Don't fail the create if initial check fails
        }

        return response()->json(['data' => $monitor->fresh()], 201);
    }

    public function show(Request $request, Monitor $monitor): JsonResponse
    {
        $this->authorize('view', $monitor);

        $uptimeHistory = $this->monitorService->getUptimeHistory($monitor);
        $responseHistory = $this->monitorService->getResponseTimeHistory($monitor);

        return response()->json([
            'data' => $monitor,
            'uptime_history' => $uptimeHistory,
            'response_history' => $responseHistory,
        ]);
    }

    public function update(Request $request, Monitor $monitor): JsonResponse
    {
        $this->authorize('update', $monitor);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'url' => 'sometimes|url|max:2048',
            'check_interval' => 'sometimes|in:30s,1m,5m,15m,30m,1h,6h,24h',
            'expected_status_code' => 'nullable|integer|between:100,599',
            'timeout' => 'nullable|integer|between:1,120',
            'track_ssl' => 'boolean',
        ]);

        $monitor->update($validated);

        return response()->json(['data' => $monitor->fresh()]);
    }

    public function destroy(Request $request, Monitor $monitor): JsonResponse
    {
        $this->authorize('delete', $monitor);
        $monitor->delete();

        return response()->json(['message' => 'Monitor deleted']);
    }

    public function checks(Request $request, Monitor $monitor): JsonResponse
    {
        $this->authorize('view', $monitor);

        $checks = $monitor->checks()->latest()->take(50)->get();

        return response()->json(['data' => $checks]);
    }

    public function check(Request $request, Monitor $monitor): JsonResponse
    {
        $this->authorize('update', $monitor);

        $check = $this->monitorService->runCheck($monitor);

        return response()->json(['data' => $check, 'monitor' => $monitor->fresh()]);
    }
}
