<?php
// app/Services/TwitterService.php

namespace App\Services;

use App\Article;
use App\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class TwitterService
{
    private $client;
    
    public function __construct()
    {
        $this->client = new Client();
    }
    
    public function fetchLatestTweets($count = 10)
{
    try {
        $username = config('services.twitter.username');
        $bearerToken = config('services.twitter.bearer_token');
        
        // Debug logging
        Log::info("Twitter API Debug:", [
            'username' => $username,
            'bearer_token_length' => strlen($bearerToken),
            'count' => $count
        ]);
        
        if (!$username || !$bearerToken) {
            Log::error('Twitter credentials not configured');
            return [];
        }
        
        $url = "https://api.twitter.com/2/tweets/search/recent";
        $params = [
            'query' => "from:{$username} -is:retweet -is:reply",
            'max_results' => $count,
            'tweet.fields' => 'created_at,public_metrics,context_annotations,attachments',
            'expansions' => 'attachments.media_keys',
            'media.fields' => 'url,preview_image_url'
        ];
        
        // Debug query
        Log::info("Twitter API Query:", [
            'url' => $url,
            'query' => $params['query'],
            'params' => $params
        ]);
        
        $response = $this->client->get($url, [
    'headers' => [
        'Authorization' => 'Bearer ' . $bearerToken,
        'Content-Type' => 'application/json',
    ],
    'query' => $params,
    'verify' => false  // Tambahkan ini untuk disable SSL verification
]);
        
        $data = json_decode($response->getBody(), true);
        
        // Debug response
        Log::info("Twitter API Response:", [
            'status_code' => $response->getStatusCode(),
            'data' => $data
        ]);
        
        if (isset($data['data'])) {
            return $this->processTweets($data['data'], $data['includes'] ?? []);
        }
        
        return [];
        
    } catch (\Exception $e) {
        Log::error('Twitter API Error: ' . $e->getMessage());
        Log::error('Twitter API Error Details: ' . $e->getTraceAsString());
        return [];
    }
}
    
    
    private function processTweets($tweets, $includes = [])
    {
        $processedTweets = [];
        
        foreach ($tweets as $tweet) {
            // Skip jika tweet sudah ada
            if (Article::where('twitter_id', $tweet['id'])->exists()) {
                continue;
            }
            
            $processedTweets[] = [
                'twitter_id' => $tweet['id'],
                'title' => $this->generateTitle($tweet['text']),
                'slug' => $this->generateSlug($tweet['text'], $tweet['id']),
                'content' => $this->formatTweetContent($tweet['text']),
                'featured_image' => $this->extractImage($tweet, $includes),
                'twitter_data' => $tweet,
                'created_at' => $tweet['created_at']
            ];
        }
        
        return $processedTweets;
    }
    
    private function generateTitle($text)
    {
        // Hapus URLs dan mentions untuk title yang bersih
        $cleanText = preg_replace('/(https?:\/\/[^\s]+)/', '', $text);
        $cleanText = preg_replace('/@[a-zA-Z0-9_]+/', '', $cleanText);
        $cleanText = preg_replace('/#[a-zA-Z0-9_]+/', '', $cleanText);
        $cleanText = trim($cleanText);
        
        // Jika teks kosong setelah cleaning, gunakan teks asli
        if (empty($cleanText)) {
            $cleanText = $text;
        }
        
        return Str::limit($cleanText, 60, '...');
    }
    
    private function generateSlug($text, $tweetId)
    {
        $title = $this->generateTitle($text);
        $baseSlug = Str::slug($title);
        
        // Jika slug kosong, gunakan tweet ID
        if (empty($baseSlug)) {
            $baseSlug = 'tweet';
        }
        
        return $baseSlug . '-' . $tweetId;
    }
    
 private function formatTweetContent($text)
{
    $content = nl2br(htmlspecialchars($text));
    
    // Convert URLs menjadi links yang proper
    $content = preg_replace(
        '/(https?:\/\/[^\s]+)/',
        '<a href="$1" target="_blank" class="twitter-link">$1</a>',
        $content
    );
    
    // Convert mentions
    $content = preg_replace(
        '/@([a-zA-Z0-9_]+)/',
        '<a href="https://twitter.com/$1" target="_blank" class="twitter-mention">@$1</a>',
        $content
    );
    
    // Convert hashtags
    $content = preg_replace(
        '/#([a-zA-Z0-9_]+)/',
        '<a href="https://twitter.com/hashtag/$1" target="_blank" class="twitter-hashtag">#$1</a>',
        $content
    );
    
    return $content;
}
    
private function extractImage($tweet, $includes)
{
    if (isset($tweet['attachments']['media_keys']) && isset($includes['media'])) {
        foreach ($includes['media'] as $media) {
            if (in_array($media['media_key'], $tweet['attachments']['media_keys'])) {
                // Prioritas: url asli > preview_image_url
                return $media['url'] ?? $media['preview_image_url'] ?? null;
            }
        }
    }
    
    // Jika tidak ada media attachment, coba extract dari URL di text
    if (preg_match('/(https:\/\/t\.co\/[a-zA-Z0-9]+)/', $tweet['text'], $matches)) {
        // Untuk sementara return URL, nanti bisa di-expand jika perlu
        return null; // Twitter t.co links perlu di-expand untuk dapat gambar asli
    }
    
    return null;
}
    
    public function syncTweetsToArticles()
    {
        $tweets = $this->fetchLatestTweets();
        $synced = 0;
        
        // Pastikan kategori Twitter ada
        $twitterCategory = $this->ensureTwitterCategory();
        
        foreach ($tweets as $tweetData) {
            try {
                $article = Article::create([
                    'title' => $tweetData['title'],
                    'slug' => $tweetData['slug'],
                    'content' => $tweetData['content'],
                    'status' => 'PUBLISH',
                    'source' => 'TWITTER',
                    'twitter_id' => $tweetData['twitter_id'],
                    'twitter_data' => json_encode($tweetData['twitter_data']),
                    'featured_image' => $tweetData['featured_image'],
                    'create_by' => 1, // Sesuaikan dengan user ID admin Anda
                ]);
                
                // Assign ke kategori Twitter
                $article->categories()->attach($twitterCategory->id);
                
                $synced++;
                Log::info("Synced tweet: {$tweetData['twitter_id']} - {$tweetData['title']}");
                
            } catch (\Exception $e) {
                Log::error("Failed to sync tweet {$tweetData['twitter_id']}: " . $e->getMessage());
            }
        }
        
        return $synced;
    }
    
    private function ensureTwitterCategory()
{
    // Cari kategori Twitter yang sudah ada
    $category = Category::where('slug', 'twitter')->first();
    
    if (!$category) {
        // Buat kategori baru secara manual
        $category = new Category();
        $category->name = 'Twitter';
        $category->slug = 'twitter';
        $category->description = 'Artikel yang diambil otomatis dari Twitter'; // Tambahkan ini
        $category->image = 'twitter-icon.png';
        $category->create_by = 1;
        $category->save();
    }
    
    return $category;
}

}
