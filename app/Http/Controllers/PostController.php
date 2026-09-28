<?php


namespace App\Http\Controllers;



class PostController extends Controller
{

    public function index()
    {
        return view('post.index');
    }
    public function createOrEdit()
    {
        return view('post.create');
    }
    public function edit($id)
    {
        return view('post.edit',[
            'id'=>$id
        ]);
    }
}
