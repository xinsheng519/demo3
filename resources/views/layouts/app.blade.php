<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{asset('asset/css/foundation.min.css')}}">
        <link rel="stylesheet" href="{{asset('asset/css/dataTables.foundation.css')}}">
        <link rel="stylesheet" href="{{asset('asset/css/style.css')}}">
        <link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap.min.css')}}">

        <!-- <link rel="icon" href="{{ asset('logos/logo.png') }}" type="image/png" /> -->

        <title>Demo3</title>

    </head>
    <body>
        <header class="l-v1-header">
        <div class="l-v1-header-left">
            <button class="l-v1-toggle-btn l-v1-left-toggle">≡</button>
        </div>
        <div class="l-v1-header-center">
            <!-- <img src="{{ asset('logos/logo.png') }}" alt="Logo" class="l-v1-logo" /> -->

            <h5>Demo3</h5>
        </div>
            <!-- <button class="l-v1-toggle-btn l-v1-right-toggle">≡</button> -->
        </header>

        <div class="l-v1-body-container">
            <nav class="l-v1-sidebar l-v1-left-sidebar" id="leftSidebar">
                    <ul>
                        <li class="l-v1-menu-item {{ ($activeMenu ?? '') === 'home' ? 'active' : '' }}">
                            <a href="{{ route('index') }}">Dashboard</a>
                        </li>
                    </ul>
            </nav>
            <main class="l-v1-main-content">
                @yield('content')
            </main>
            <!-- <nav class="l-v1-sidebar l-v1-right-sidebar" id="rightSidebar">
                <ul>
                    <li class="menu-item">右栏信息</li>
                    <li class="menu-item">快捷链接</li>
                </ul>
            </nav> -->
        </div>
    </body>
</html>
