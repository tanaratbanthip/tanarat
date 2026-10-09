<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $post = $page['props']['post'] ?? null;
            $appUrl = config('app.url', url('/'));
            $title = $post ? $post['title'] . ' | MyNotes' : config('app.name', 'MyNotes');
            $desc = $post ? \Illuminate\Support\Str::limit(strip_tags($post['content']), 150) : 'พื้นที่จดบันทึก แลกเปลี่ยนความรู้ด้านการพัฒนาเว็บและเทคโนโลยี';
            $image = ($post && !empty($post['image'])) ? $appUrl . '/storage/' . $post['image'] : $appUrl . '/favicon.ico';
            $currentUrl = url()->current();
        @endphp

        <title>{{ $title }}</title>
        <meta name="description" content="{{ $desc }}">

        <!-- Open Graph / Facebook / LINE -->
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ $currentUrl }}">
        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $desc }}">
        <meta property="og:image" content="{{ $image }}">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ $currentUrl }}">
        <meta name="twitter:title" content="{{ $title }}">
        <meta name="twitter:description" content="{{ $desc }}">
        <meta name="twitter:image" content="{{ $image }}">

        <!-- RSS Feed Auto-Discovery -->
        <link rel="alternate" type="application/rss+xml" title="MyNotes RSS Feed" href="{{ url('/feed') }}">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        @inertia
    </body>
</html>