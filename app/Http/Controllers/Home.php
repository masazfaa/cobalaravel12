<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Home extends Controller
{
    function index() {
        return view ('map.map1');
    }

    function geoserver() {
        return view ('map.map2');
    }

    function cesium() {
        return view ('map.map3');
    }
    function cesiumion() {
        return view ('map.map4');
    }
}
