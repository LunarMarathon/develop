{{-- The Post Page Layout --}}
@extends('hyde::layouts.app')
@section('content')

@php
$title = $post->matter['title'] ?? false;
$date = $post->matter['date'] ?? false;
$description = $post->matter['description'] ?? false;
$category = $post->matter['category'] ?? false;
$author = $post->matter['author'] ?? false;
@endphp

@push('meta')
<!-- Blog Post Meta Tags -->
@if($description) <meta name="description" content="{{ $description }}"> @endif
@if($author) <meta name="author" content="{{ $author }}"> @endif
@if($category) <meta name="keywords" content="{{ $category }}"> @endif

<meta property="og:type" content="article" />
@if($title) <meta property="og:title" content="{{ $title }}"> @endif
@if($date) <meta property="og:article:published_time" content="{{ $date }}"> @endif
@if(Hyde::uriPath())
<meta property="og:url" content="{{ Hyde::uriPath($post->slug) }}">
@endif

@endpush

<main class="mx-auto max-w-7xl py-16 px-8">
	@include('hyde::components.post.article')
</main>

@endsection
