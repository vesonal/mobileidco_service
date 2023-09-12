@include('layouts.sections.header')

<!DOCTYPE html>
<html lang="en">
<head>
  @include('layouts.sections.head')
</head>

<body class="menu-padding" data-new-gr-c-s-check-loaded="14.1047.0" data-gr-ext-installed="" cz-shortcut-listen="true">
    <noscript><strong>We're sorry but sample-mobileid-inapp-common-backend doesn't work properly without JavaScript
            enabled. Please enable it to continue.</strong></noscript>
    <div id="app" class="background-dots">
        @yield('header')
        <div class="content-wrapper">
            <div id="routerView">
                @yield('content')
            </div>
        </div>
    </div>
  <!-- jQuery -->
  @include('layouts.sections.footer')
  @include('layouts.sections.footerjs')
  @stack('scripts')
</body>
</html>