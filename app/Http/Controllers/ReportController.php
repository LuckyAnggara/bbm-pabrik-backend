<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function reportProduction()
    {
        return view('production.report');

        $pdf = PDF::loadView('production.report');

        return $pdf->download('nicesnippets.pdf');
        // $pdf = PDF::loadView('myPDF');

        // return $pdf->download('nicesnippets.pdf');
    }
}
