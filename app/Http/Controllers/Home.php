<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Home extends Controller
{
    function index() {
        return view ('map.map1');
    }

    function about() {
        return view ('about');
    }
}
