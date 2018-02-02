<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPA Javascript Tasklist</title>
    <!-- FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,600" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,600" rel="stylesheet" type="text/css">
    <!-- STYLE -->
    <link href="/css/appStyle.css" rel="stylesheet" type="text/css">
    <!-- SCRIPTS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="/js/fontawesome-all.min.js"></script>
    <script src="/js/taskList.js"></script>

</head>

<body>
    <div id="app" style="padding: 64px; width: 75%; margin: auto;">
        <h1>Task List</h1>
        <hr/>
        <form id="form" class="addTask" action="{{ url('/task') }}">
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
            <h3>Add Task:</h3>
            <input id="task-input" name="task" />
            <button style="padding: 5px 10px;" class="theme-btn">Add Task</button>
        </form>
        <div id="current-tasks">
            <h3>Current Tasks:</h3>
        </div>

    </div>
</body>
</html>