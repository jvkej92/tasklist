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
            let editBtn = '<a href="#" class="edit-list fade-in-and-out"><i class="far fa-edit fa-xs" data-fa-transform="up-3"></i></a>'
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
        let edit = `<a href="#" class="edit-list fade-in-and-out"><i class="far fa-edit fa-xs" data-fa-transform="up-3"></i></a>`;
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