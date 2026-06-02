<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CounselingService;

class CounselingServiceController extends Controller
{
    public function index()
    {
        $services = CounselingService::where('is_active', true)->get();
        return view('services.index', compact('services'));
    }
}
