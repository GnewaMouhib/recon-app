<?php

namespace App\Jobs;

use App\Models\Scan;
use App\Models\ScanResult;
use App\Services\Pentest\ReconService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunReconScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;

    public function __construct(public Scan $scan)
    {
    }

    public function handle(ReconService $reconService): void
    {
        $this->scan->update([
            'status'     => 'running',
            'started_at' => now(),
        ]);

        try {
            $results = $reconService->run($this->scan->type, $this->scan->target);

            foreach ($results as $item) {
                ScanResult::create([
                    'scan_id'        => $this->scan->id,
                    'title'          => $item['title'] ?? null,
                    'severity'       => $item['severity'] ?? 'info',
                    'raw_data'       => $item['raw_data'] ?? null,
                    'description'    => $item['description'] ?? null,
                    'recommendation' => $item['recommendation'] ?? null,
                ]);
            }

            $this->scan->update([
                'status'      => 'completed',
                'finished_at' => now(),
            ]);
        } catch (Exception $e) {
            $this->scan->update([
                'status'        => 'failed',
                'finished_at'   => now(),
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}