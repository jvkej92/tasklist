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
        <script src="/js/fontawesome-all.min.js"></script>
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
                width: 40%;
                padding: 10px!important;
                background: #eaebed;
                border-radius: 3px;
            }

            li{
                text-transform: capitalize;
                position: relative;
                width: 100%;
                list-style: none;
                margin-bottom: 16px;
            }

            li.new-item{
                animation: fade-in;
                animation-duration: .4s;
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
                cursor: pointer;
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

            p.error-message, p.task-error{
                font-weight: 600;
                background: #ffe2e2;
                color: #913838;
                width: 100%;
                padding: 5px 10px;
                box-sizing: border-box;
            }

            svg{
                animation: fade-in;
                animation-duration: .3s;
                fill: #586c99;
            }

            svg.fa-minus-circle>path{
                fill: #913838;
            }

            .fade-in-and-out{
                display: none;
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
                        newTask(task.name, task.id);
                    });
                });
            }

            //Triggers AddTask
            $('.addTask button').on('click', function(evt){
                evt.preventDefault();
                addTask();
            });
            //Triggers Edit for element
            $(document).on('click', '.edit-list', function(evt){
                evt.preventDefault();
                if(!$(this).siblings('.delete').length) {
                    let content = $(this).parent().text();
                    $(this).empty();
                    let deleteBtn = '<a href="#" style="float: right;" class="delete"><i class="fas fa-minus-circle"></i></a>';
                    let cancelBtn = '<a href="#" class="edit-list"><i class="far fa-times-circle fa-sm"></i></a>';
                    $(this).parent().html(`${content}` + " " + cancelBtn + deleteBtn);
                }
                else{
                    $(this).siblings('.delete').remove();
                    $(this).empty();
                    let content = $(this).parent().text();
                    let editBtn = '<a href="#" class="edit-list fade-in-and-out"><i class="far fa-edit fa-xs"></i></a>'
                    $(this).parent().html(content + " " + editBtn);
                }
            });

            //Triggers Delete for element
            $(document).on('click','.delete',function(evt) {
                evt.preventDefault();
                let clicked = $(this).parent();
                deleteTask(clicked.data('id')).then((response)=>{
                    if(response.ok)
                        clicked.remove(); 
                });
            });

            //Makes an http GET request for all tasks
            function loadTasks(){
                return fetch("/task/json").then(response=>response.json());
            }
            //Makes an http GET request with name of task 
            function loadTaskName(name){
                let reqUrl = `/task/${name}`;
                return fetch(reqUrl).then(response=>response.json());
            }
            //Makes an http POST reqeust with new task
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
            //Makes an http DELETE request with an ID
            function deleteTask(id){
                var myHeaders = new Headers({"X-CSRF-TOKEN": $("input[name='_token']").val()});
                return fetch(`/task/${id}`, {
                    method: 'DELETE',
                    headers: myHeaders,
                    credentials: "same-origin"
                });
            }

            function addTask(){
                let taskValue = $('#task-input').val();
                if(taskValue){
                    postTask(taskValue).then((response)=>{
                        if(response.ok){
                            loadTaskName(taskValue).then((task)=>{
                                newTask(task[0].name, task[0].id);
                            });                         
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

            function newTask(taskName, taskId){
                let taskList = $('#current-tasks ul');
                $('.new-item').removeClass('new-item');
                let edit = `<a href="#" class="edit-list fade-in-and-out"><i class="far fa-edit fa-xs"></i></a>`;
                let listItem = `class="new-item "data-id="${taskId}">${taskName} ${edit}`;
                $(taskList).append(`<li ${listItem}</li>`);
            }

            function emptyTask(){
                let errorMsg = "Input cannot be empty";
                $('#task-input').addClass('input-error');
                if(!$('.error-message').length)
                    $('#task-input').before(`<p class="error-message">${errorMsg}</p>`);
            }

            function taskError(){
                let errorMsg = "There was an error adding your task";
                if(!$('.task-error').length)
                    $('#task-input').before(`<p class="task-error">${errorMsg}</p>`);
            }

            $(document).on('mouseenter', 'li', function(){
                $(this).find('.fade-in-and-out').fadeIn(100);
            });

            $(document).on('mouseleave', 'li', function(){
                $(this).find('.fade-in-and-out').fadeOut(100);
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