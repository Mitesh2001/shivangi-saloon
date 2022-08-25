<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="robots" content="noindex, nofollow">
    {{-- Title Section --}}
        <title>{{ config('app.name') }} | @yield('title', $page_title ?? '')</title>
		
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
		
	@foreach(config('layout.resources.logincss') as $style)
		<link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}?v=1.1.2" rel="stylesheet" type="text/css"/>
	@endforeach

	{{-- Global Theme Styles (used by all pages) --}}
	@foreach(config('layout.resources.css') as $style)
		<link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}?v=1.1.1" rel="stylesheet" type="text/css"/>
	@endforeach

	{{-- Layout Themes (used by all pages) --}}
	@foreach (Metronic::initThemes() as $theme)
		<link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($theme)) : asset($theme) }}?v=1.1.1" rel="stylesheet" type="text/css"/>
	@endforeach

	
	{{-- load jquery --}}
	<script src="{{ asset('plugins/custom/jquery/jquery.min.js') }}?v=1.1.4" type="text/javascript"></script>
        

	{{-- Includable CSS --}}
	@yield('styles')
    
</head>
<body {{ Metronic::printAttrs('body') }} {{ Metronic::printClasses('body') }}>


    @yield('content')

	{{-- Global Config (global config for global JS scripts) --}}
        <script>
            var KTAppSettings = {!! json_encode(config('layout.js'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
        </script>

        {{-- Global Theme JS Bundle (used by all pages)  --}}
        @foreach(config('layout.resources.js') as $script)
            <script src="{{ asset($script) }}?v=1.1.4" type="text/javascript"></script>
        @endforeach
		
		@foreach(config('layout.resources.loginjs') as $script)
            <script src="{{ asset($script) }}?v=1.1.1" type="text/javascript"></script>
        @endforeach

        {{-- Includable JS --}}
        @yield('scripts') 
</body>
</html>
