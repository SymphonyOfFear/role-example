<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    public function index(): View
    {
        return view('student.results');
    }
}
