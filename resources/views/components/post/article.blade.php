<article id="post-article" itemscope itemtype="https://schema.org/Article"
    @class(['mx-auto prose', 'torchlight-enabled' => Hyde\Framework\Hyde::hasTorchlight()])>
    <meta itemprop="identifier" content="{{ $post->slug }}">
    @if(Hyde::uriPath())
    <meta itemprop="url" content="{{ Hyde::uriPath($post->slug) }}">
    @endif
    
    <header role="doc-pageheader">
        <h1 itemprop="headline" class="mb-4">{{ $title ?? 'Blog Post' }}</h1>
		<div id="byline" aria-label="About the post" role="doc-introduction">
            @includeWhen($date, 'hyde::components.post.datePublished')
		    @includeWhen($author, 'hyde::components.post.author')
            @includeWhen($category, 'hyde::components.post.category')
        </div>
    </header>
    <div itemprop="articleBody">
        {!! $markdown !!}
    </div>
</article>
