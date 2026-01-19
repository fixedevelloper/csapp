<?php


namespace App\Http\Controllers;


use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{

    public function index()
    {

        return view('category.index',[
        ]);
    }
    public function tags()
    {

        return view('category.tag',[
        ]);
    }
}
