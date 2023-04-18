<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="Saquib" content="Blade">
    <title>Checkout our layout</title>
    <!-- load bootstrap from a cdn -->
    @yield('css')
</head>

<body>
    <div class="container">
        <div id="main" class="row">
            @yield('content')
        </div>
        <footer class="row">

        </footer>
    </div>
</body>
@stack('scripts')

</html>
