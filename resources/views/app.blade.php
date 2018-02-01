<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Plain Js app with Laravel Backend</title>
        
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,600" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <style>
            html{
                font-family: 'Roboto';
                font-weight: 300;
                font-size: 18px;
                line-height: 1.7;
            }

            h1{
                font-family: 'Raleway';
                font-weight: 100;
                font-size: 3em;
                line-height: 1.7;
            }

            #current-tasks > h3{ 
                font-family: 'Raleway';
                font-weight: 600;
                font-size: 1.5em;
            }

            li.new-item{
                animation: fade-in;
                animation-duration: .3s;
            }

            @keyframes fade-in{
                from{
                    opacity: 0;
                }
                to{
                    opacity: 1;
                }
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function(){

            placeTasks();    

            function placeTasks(){
                let app = $("#current-tasks");
                let taskList = document.createElement('ul');
                app.append(taskList);
                loadTasks().then((tasks)=>{
                    tasks.forEach(task=>{
                        let taskItem = document.createElement('li');
                        taskItem.innerHTML = task.name;
                        taskList.append(taskItem);
                    });
                });
            }

            $('.addTask button').on('click', addTask);

            function loadTasks(){
                return fetch("/task/json").then(response=>response.json());
            }

            function addTask(){
                let formData  = new FormData();
                let taskValue = $('.addTask input[name="task"]').val();
                formData.append('name', taskValue);
                console.log($('meta[name="csrf-token"]').attr('content'));
                task = 
                fetch('/task', {
                    method: 'POST',
                    body: formData,
                    headers: new Headers({
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    })
                }).then((response)=>{
                    console.log("done posting");
                    if(response.ok)
                        newTask(taskValue);
                });
                formData.delete('name');
                $('.addTask input[name="task"]').val('');
            }

            function newTask(taskValue){
                let taskList = $('#current-tasks ul');
                loadTasks().then((tasks)=>{
                    tasks.forEach(task=>{
                        let name = task.name;
                        if(name === taskValue) {
                            $('.new-item').removeClass('new-item');
                            let taskItem = `<li class="new-item">${name}</li>`;
                            taskList.append(taskItem);
                            taskValue = '';
                        }
                    });

        });
        </script>
    </head>
<body>
    <div id="app" style="padding: 64px; width: 75%; margin: auto;">
        <h1>Task List</h1>
        <hr/>
        <div class="addTask">
            {{ csrf_field() }}
            Add Task: <input name="task"/></input><button>Add Task</button>
                <div id="current-tasks">
                    <h3>Current Tasks:</h3>
                </div>

        </div>
    </div>    
</body>
</html>