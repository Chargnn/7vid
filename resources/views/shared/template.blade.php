<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        @if(isset($video))
            <meta name="description" content="{{ $video->getDescription() ?: $video->getTitle() . ' - by ' . $video->author->name }}">
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="canonical" href="{{ url()->current() }}"/>

        <title>{{ config('app.name') }} - @yield('title')</title>

        <link href="{{ asset('css/app.css') . '?v=' . filemtime(public_path('css/app.css')) }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/style.css') . '?v=' . filemtime(public_path('css/style.css')) }}" rel="stylesheet" type="text/css">
        @yield('head')
    </head>
    <body id="page-top" class="sidebar-toggled">
        <div id="overlay"></div>
        @include('shared.navbar')

        <div id="wrapper">
            @include('shared.sidenav')
            <div id="content-wrapper">
                <div class="container-fluid">
                    @include('shared.navbar.mobile-search')
                    <div class="mb-2">
                        @include('shared.message')
                    </div>
                    @yield('content')
                </div>
            </div>
        </div>

        @include('shared.footer')

        <script src="https://browser.sentry-cdn.com/5.7.1/bundle.min.js" integrity="sha384-KMv6bBTABABhv0NI+rVWly6PIRvdippFEgjpKyxUcpEmDWZTkDOiueL5xW+cztZZ" crossorigin="anonymous"></script>
        <script> Sentry.init({ dsn:  '{{ env('SENTRY_LARAVEL_DSN') }}' });</script>
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
        @yield('footer')
    </body>
</html>
