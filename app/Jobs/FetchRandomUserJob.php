<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchRandomUserJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $response = Http::get('https://randomuser.me/api/');

            if ($response->successful()) {
                $results = $response->json('results');
                Log::channel('job')->info('Fetched Random User Results:', $results);
            } else {
                Log::channel('job')->error('Failed to fetch data from Random User API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('job')->error('Error fetching data from Random User API.', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
