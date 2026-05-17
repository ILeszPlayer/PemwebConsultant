<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CounselingService;

class HomeController extends Controller
{
    public function index()
    {
        $services = CounselingService::where('is_active', true)->get();
        return view('home', compact('services'));
    }
}
