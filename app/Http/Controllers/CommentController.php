<?php


namespace App\Http\Controllers;


class CommentController extends Controller
{

    public function index()
    {
        return view('comment.index');
    }
}
