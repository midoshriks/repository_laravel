<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DemoController extends Controller
{
    public function index($page)
    {
        // dd(view()->exists('demo.'.$page));
        if (view()->exists('demo.' . $page)) {
            return view('demo.' . $page);
        } else {
            return view('demo.404');
        }
        // return view('demo.404');
    }
}
