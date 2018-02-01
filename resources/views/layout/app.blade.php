<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Laravel Tasklist</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <style>
            body{
                font-family: 'Raleway:100';
                font-size: 16px;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <nav class="navbar navbar-default">
                <!-- Navbar Contents -->
            </nav>
        </div>

        @yield('content')
               
    </body>
</html>