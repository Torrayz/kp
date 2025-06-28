{{-- resources/views/partials/twitter-article.blade.php --}}
<div class="twitter-article-container">
    {{-- Header Twitter --}}
    <div class="twitter-header">
        <div class="twitter-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="#1da1f2">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
            </svg>
        </div>
        <span class="twitter-label">Dari Twitter</span>
        <a href="{{ $article->twitter_url }}" target="_blank" class="twitter-original-link">
            Lihat Tweet Asli <i class="fa fa-external-link"></i>
        </a>
    </div>

    {{-- Content Twitter --}}
    <div class="twitter-content article">
        {!! $article->content !!}
    </div>

    {{-- Gambar jika ada --}}
    @if($article->featured_image)
        <div class="twitter-image">
            <img src="{{ $article->featured_image }}" alt="Twitter Image" class="twitter-img">
        </div>
    @endif

    {{-- Footer Twitter --}}
    <div class="twitter-footer">
        <small class="twitter-date font-italic">
            <i class="fa fa-clock-o"></i> {{ date('d M Y, H:i', strtotime($article->created_at)) }}
        </small>
    </div>
</div>

<style>
.twitter-article-container {
    border: 2px solid #1da1f2;
    border-radius: 12px;
    padding: 20px;
    margin: 16px 0;
    background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
    box-shadow: 0 4px 12px rgba(29, 161, 242, 0.1);
    position: relative;
}

.twitter-article-container::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #1da1f2, #0d8bd9);
    border-radius: 12px;
    z-index: -1;
}

.twitter-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e1e8ed;
}

.twitter-icon {
    margin-right: 10px;
}

.twitter-label {
    color: #1da1f2;
    font-weight: 600;
    font-size: 16px;
    margin-right: auto;
}

.twitter-original-link {
    color: #1da1f2;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

.twitter-original-link:hover {
    text-decoration: underline;
    color: #0d8bd9;
}

.twitter-content {
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 15px;
    text-align: justify;
}

.twitter-content .twitter-link {
    color: #1da1f2;
    text-decoration: none;
    font-weight: 500;
}

.twitter-content .twitter-mention {
    color: #1da1f2;
    text-decoration: none;
    font-weight: 500;
}

.twitter-content .twitter-hashtag {
    color: #1da1f2;
    text-decoration: none;
    font-weight: 500;
}

.twitter-content .twitter-link:hover,
.twitter-content .twitter-mention:hover,
.twitter-content .twitter-hashtag:hover {
    text-decoration: underline;
    color: #0d8bd9;
}

.twitter-image {
    margin: 15px 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.twitter-img {
    width: 100%;
    height: auto;
    display: block;
}

.twitter-footer {
    padding-top: 10px;
    border-top: 1px solid #e1e8ed;
}

.twitter-date {
    color: #657786;
    font-size: 14px;
}
</style>