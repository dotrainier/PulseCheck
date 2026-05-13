<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use App\Services\MonitorService;
use Illuminate\Console\Command;

class RunMonitorChecks extends Command
{
    protected $signature = 'monitors:check {--monitor= : Run check for a specific monitor ID}';

    protected $description = 'Run health checks for all due monitors';

    public function __construct(private MonitorService $monitorService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($monitorId = $this->option('monitor')) {
            $monitor = Monitor::find($monitorId);
            if (! $monitor) {
                $this->error("Monitor #{$monitorId} not found.");

                return 1;
            }
            $this->runCheck($monitor);

            return 0;
        }

        $monitors = Monitor::all();
        $due = $monitors->filter(fn ($m) => $m->isDueForCheck());

        if ($due->isEmpty()) {
            $this->info('No monitors due for checking.');

            return 0;
        }

        $this->info("Running checks for {$due->count()} monitor(s)...");

        foreach ($due as $monitor) {
            $this->runCheck($monitor);
        }

        $this->info('Done.');

        return 0;
    }

    private function runCheck(Monitor $monitor): void
    {
        try {
            $check = $this->monitorService->runCheck($monitor);
            $status = $check->success ? '<fg=green>OK</>' : '<fg=red>FAIL</>';
            $this->line("  [{$status}] {$monitor->name} — {$check->response_time}ms");
        } catch (\Exception $e) {
            $this->error("  [ERROR] {$monitor->name}: {$e->getMessage()}");
        }
    }
}
