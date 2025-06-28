<?php
// app/Console/Commands/SyncTwitterCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TwitterService;

class SyncTwitterCommand extends Command
{
    protected $signature = 'twitter:sync {--count=10 : Number of tweets to fetch}';
    protected $description = 'Sync latest tweets to articles';
    
    public function handle()
    {
        $this->info('🐦 Starting Twitter sync...');
        
        $count = $this->option('count');
        $twitterService = new TwitterService();
        
        try {
            $synced = $twitterService->syncTweetsToArticles();
            
            if ($synced > 0) {
                $this->info("✅ Successfully synced {$synced} new tweets!");
            } else {
                $this->info("ℹ️  No new tweets to sync.");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}