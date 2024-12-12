<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteOldPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:old-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily delete old posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('delete:old-posts started');
        $posts = Post::onlyTrashed()->where('deleted_at', '<', now()->subDays(30))->forceDelete();
        Log::info('Old posts deleted successfully');

        return 0;
    }
}
