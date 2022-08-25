<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ Metronic::printAttrs('html') }} {{ Metronic::printClasses('html') }}>
    <head>
        <meta charset="utf-8"/>

        @php 
            $user = Auth::user(); 
            if($user->user_type !== 1) {
                $app_title = config('app.name');
            } else {

                $app_title = $user->getDistibutor->name;
                if(empty($app_title)) {
                    $app_title = config('app.name');
                } 
            } 
        @endphp

        {{-- Title Section --}}
        <title>{{ $app_title }} | @yield('title', $page_title ?? '')</title>

        {{-- Meta Data --}}
        <meta name="description" content="@yield('page_description', $page_description ?? '')"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

        {{-- Favicon --}}
        <link rel="shortcut icon" href="{{ asset('storage/assets/site_identity/favicon-icon.png') }}" />
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/assets/site_identity/favicon_io/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/assets/site_identity/favicon_io/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/assets/site_identity/favicon_io/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('storage/assets/site_identity/favicon_io/site.webmanifest') }}">

        {{-- Fonts --}}
        {{ Metronic::getGoogleFontsInclude() }}

        {{-- Global Theme Styles (used by all pages) --}}
        @foreach(config('layout.resources.css') as $style)
            <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}?v=1.1.1" rel="stylesheet" type="text/css"/>
        @endforeach

        {{-- load jquery --}}
        <script src="{{ asset('plugins/custom/jquery/jquery.min.js') }}?v=1.1.4" type="text/javascript"></script> 

        {{-- Layout Themes (used by all pages) --}}
        @foreach (Metronic::initThemes() as $theme)
            <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($theme)) : asset($theme) }}?v=1.1.1" rel="stylesheet" type="text/css"/>
        @endforeach
 
        @yield('styles')
        <style>
            body { 
                font-family: 'Montserrat Alternates', sans-serif;
            } 
        </style>
    </head>

    <body {{ Metronic::printAttrs('body') }} {{ Metronic::printClasses('body') }}>

        @if (config('layouts.page-loader.type') != '')
            @include('layouts.partials._page-loader')
        @endif

        @include('layouts.base._layout')
        <script>var HOST_URL = "{{-- route('quick-search') --}}";</script>

        {{-- Global Config (global config for global JS scripts) --}}
        <script>
            var KTAppSettings = {!! json_encode(config('layout.js'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
        </script>

        {{-- Global Theme JS Bundle (used by all pages)  --}}
        @foreach(config('layout.resources.js') as $script)
            <script src="{{ asset($script) }}?v=1.1.4" type="text/javascript"></script>
        @endforeach
        <script>
            $(document).ready(function (){
                $.fn.datepicker.defaults.format = "dd-mm-yyyy";
                $(".date-picker-start").datepicker({
                    startDate: '-0d',
                    todayHighlight: true,
                });	
                $(".date-picker-end").datepicker({
                    endDate: '0d',
                    todayHighlight: true,
                });	
                $(".date-picker-today").datepicker({
                    startDate: '-0d',
                    endDate: '0d',
                    todayHighlight: true,
                });		

                $(document).on('click', 'input:reset', function (e) { 
                    $(e.target).blur();
                });
            })
        </script>

        {{-- Includable JS --}}
        @yield('scripts')
 
    </body>
</html>



