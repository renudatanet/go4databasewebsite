<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class CheckBrokenLinksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $table;
    public $content;
    public $title;

    public $timeout = 120;
    public $tries = 2;

public function __construct($table, $title, $content)
{
    $this->table = $table;
    $this->title = $title;
    $this->content = $content;
}

 public function handle()
{
    Log::info("Started scanning {$this->table}");
    $checked = [];
    $processedCount = 0;

    DB::table($this->table)
        ->select('id', $this->title, $this->content)
        ->orderBy('id') // ✅ REQUIRED
        ->chunkById(2, function ($rows) use (&$checked, &$processedCount) {

            foreach ($rows as $row) {

$html = $row->{$this->content} ?? '';

Log::info([
    'table' => $this->table,
    'id' => $row->id,
    'title' => $row->{$this->title},
    'content' => $row->{$this->content},
    'html' => $html,
]);

           $result = preg_match_all('/href\s*=\s*["\']([^"\']+)["\']/i', $html, $matches);

Log::info([
    'preg_result' => $result,
    'preg_error' => preg_last_error(),
    'matches' => $matches,
]);
                if (empty($matches[1])) continue;

                foreach ($matches[1] as $url) {

                    if (!filter_var($url, FILTER_VALIDATE_URL)) continue;
                    if (isset($checked[$url])) continue;

                    $checked[$url] = true;

                    try {
                        $response = Http::timeout(5)
    ->retry(2, 1000)
    ->withHeaders([
        'User-Agent' => 'Mozilla/5.0',
    ])
    ->get($url);

$status = $response->status();

Log::info([
    'url' => $url,
    'status' => $status,
]);

                    } catch (\Exception $e) {
                        $status = 'error';
                    }
               // ✅ Save broken link
                    if ($status == 'error' ||($status >= 400 && $status < 600)) {
                        DB::table('broken_links')->updateOrInsert(
                            ['url' => $url],
                            [
                                'module' => $this->table,
                                'record_id' => $row->id,
                                'title' => $row->{$this->title},
                                'status' => $status,
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        ); 
                    }

                    // ✅ Count processed URLs
                    $processedCount++;

                    // ✅ Update DB every 5 URLs (performance boost)
                    if ($processedCount % 5 == 0) {
                        DB::table('scan_progress')
                            ->where('id', 1)
                            ->increment('processed', 5);
                    }

                    usleep(400000); // 0.4 sec
                }
            }
        });

    // ✅ Push remaining count
    if ($processedCount % 5 != 0) {
        DB::table('scan_progress')
            ->where('id', 1)
            ->increment('processed', $processedCount % 5);
    }

    // ✅ Mark this job completed
    DB::table('scan_progress')
        ->where('id', 1)
        ->increment('completed_jobs');

    // ✅ When ALL jobs done → stop
    $progress = DB::table('scan_progress')->where('id', 1)->first();

    if ($progress->completed_jobs >= $progress->total_jobs) {
        DB::table('scan_progress')->where('id', 1)->update([
            'is_running' => 0
        ]);
    }
    Log::info("Finished scanning {$this->table}");
}
}