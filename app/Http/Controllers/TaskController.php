<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller {

    //******************//
    //Display All Tasks//
    //****************//
    public function index() {
        $tasks = Task::orderBy('created_at', 'asc')->get();
        return view('welcome');
    }

    public function taskList() {
        return view('app');
    }
    
    //***************//
    //Add A New Task//
    //*************//
    public function createTask(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|',
        ]);
        if ($validator->fails()) {
            return redirect('/')
                ->withInput()
                ->withErrors($validator);
        }
        $task = new Task;
        $task->name = $request->name;
        $task->save();
        // return redirect('/');
    }

    //*************************//
    // Delete An Existing Task//
    //***********************//
    public function deleteTask($id){
        Task::findOrFail($id)->delete();
        // return redirect('/');
    }

    //********************//
    //Return Task in json//
    //*******************//
    public function json(){
        return Task::orderBy('created_at', 'asc')->get();
    }

    //**********************//
    //Display Tasks by Name//
    //********************//
    public function taskName($name) {
        $newTask = Task::where('name', '=', $name)->get();
        return $newTask;
    }
}
