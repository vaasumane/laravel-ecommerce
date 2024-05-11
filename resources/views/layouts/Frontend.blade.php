<!doctype html>
<html>

<head>
    @include('includes.head')
</head>

<body>
    <div>
        <header>
            @include('includes.header')
        </header>
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
</body>

</html>