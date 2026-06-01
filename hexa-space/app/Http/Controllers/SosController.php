<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SosController extends Controller
{
    public function index()
    {
        return view('sos.index');
    }

    public function breathing()
    {
        return view('sos.breathing');
    }

    public function grounding()
    {
        return view('sos.grounding');
    }
}
