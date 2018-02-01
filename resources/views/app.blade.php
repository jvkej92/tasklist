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

            body{
                background: #fff7f7;
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

            ul{
                position: relative;
                width: 30%;
            }

            li{
                text-transform: capitalize;
                width: 100%;
                list-style: none;
            }

            li.new-item{
                animation: fade-in;
                animation-duration: .3s;
            }

            #form{
                position: relative;
                width: 30%;
            }

            .theme-btn{
                font-family: 'Raleway';
                background:#586c99;
                color: #ffffff;
                border-radius: 6px;
                border: 0px solid #0f4e82;
                border-bottom-width: 2px;
                margin-bottom: 8px;
                transition-duration: .05s;
                vertical-align: middle;
                float: right;
            }

            .theme-btn:hover{
                background: #728dc9;
            }

            .theme-btn:active{
                border-width: 0px;
                border-top: 4px solid #fff7f7; 
                margin-bottom: 6px;        
            }

            input{
                background: #ffffff;
                border: 1px solid #e8e8e8;
                border-radius: 4px;
                font-size: 18px;
                line-height: 1.5;
                padding: 1px 5px;
                margin-top: -8px;
                vertical-align: middle;
            }

            .input-error{
                border: 1px solid #913838;
                background: #ffe2e2;
            }

            p.error-message{
                font-weight: 600;
                background: #ffe2e2;
                color: #913838;
                width: 100%;
                padding: 5px 10px;
                box-sizing: border-box;
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

            //On ready executere the placeTasks function to load the list
            placeTasks();    

            //Creates the unordered list and calls the loadTasks function
            //Populates the unordered list with returned data from loadTasks
            function placeTasks(){
                let app = $("#current-tasks");
                let taskList = document.createElement('ul');
                app.append(taskList);
                loadTasks().then((tasks)=>{
                    tasks.forEach(task=>{
                        let listItem = `data-id="${task.id}">${task.name}`;
                        $(taskList).append(`<li ${listItem} </li>`);
                    });
                });
                $(app).append(`<a href="#" id="edit-list">edit</a>`)
            }

            //Watches the addTask button and triggers the addTask function
            $('.addTask button').on('click', function(evt){
                evt.preventDefault();
                addTask();
            });

            //Calls the uri /task/json and returns the response
            function loadTasks(){
                return fetch("/task/json").then(response=>response.json());
            }

            //Calls the uri /task/{name} and returns the response
            function loadTaskName(name){
                let reqUrl = `/task/${name}`;
                return fetch(reqUrl).then(response=>response.json());
            }

            //Gets value from the input and places it in taskValue
            //Sets header with proper X-CSRF-TOKEN
            //Creates a FormData object and populates it with #form
            //appends new task to formData
            //Sends POST request to /task and if response is OK calls the newTask function
            //Clears input
            function addTask(){
                let taskValue = $('#task-input').val();
                if(taskValue){
                    postTask(taskValue).then((response)=>{
                        if(response.ok){
                            newTask(taskValue);
                            $('#task-input').val('').removeClass('input-error');
                            $('p.error-message').remove();
                        }
                        else
                            taskError();
                    });
                }
                else{
                    emptyTask();
                }
            }

            function postTask(taskValue){
                var myHeaders = new Headers({"X-CSRF-TOKEN": $("input[name='_token']").val()});
                let formData = new FormData($('#form'));
                formData.append('name', taskValue);
                return fetch('/task', {
                    method: 'POST',
                    headers: myHeaders,
                    credentials: "same-origin",
                    body: formData
                });
            }

            function deleteTask(id){
                var myHeaders = new Headers({"X-CSRF-TOKEN": $("input[name='_token']").val()});
                return fetch(`/task/${id}`, {
                    method: 'DELETE',
                    headers: myHeaders,
                    credentials: "same-origin"
                });
            }


            //Takes value taskValue. Then creates a variabel with unordered list
            //Calls the loadTaskName function and passes it the taskValue variable
            //Appends the returned value as a list element to the unordered list. 
            function newTask(taskValue){
                let taskList = $('#current-tasks ul');
        
                loadTaskName(taskValue).then((newTask)=>{
                    $('.new-item').removeClass('new-item');
                    taskList.append(`<li data-id="${newTask[0].id}" class="new-item">${newTask[0].name}</li>`);
                });
            }

            function emptyTask(){
                let errorMsg = "Input cannot be empty";
                $('#task-input').addClass('input-error');
                if(!$('.error-message').length)
                    $('#task-input').before(`<p class="error-message">${errorMsg}</p>`);
            }

            $('#edit-list').on('click', function(evt){
                evt.preventDefault();
                $('li').each(function(){
                    if(!$(this).find('.delete').length) {
                        let content = $(this).html();
                        $(this).html(content + ' <a href="#" style="float: right;" class="delete">Delete</a>');
                    }
                });
            });

            $(document).on('click','.delete',function(evt) {
                evt.preventDefault();
                let clicked = $(this).parent();
                deleteTask(clicked.data('id')).then((response)=>{
                    if(response.ok)
                        clicked.remove(); 
                });
            });

        });
        
        </script>
    </head>
<body>
    <div id="app" style="padding: 64px; width: 75%; margin: auto;">
        <h1>Task List</h1>
        <hr/>
        <form id="form" class="addTask" action="{{ url('/task') }}">
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
            <h3>Add Task:</h3> 
            <input id="task-input" name="task"/>
            <button style="padding: 5px 10px;" class="theme-btn">Add Task</button>
        </form>
                <div id="current-tasks">
                    <h3>Current Tasks:</h3>
                </div>

        </div>
    </div>    
</body>
</html>