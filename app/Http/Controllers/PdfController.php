<?php

namespace App\Http\Controllers;
use App\Models\Applications;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function pdf(Applications $application)
    {
        // Generate a filename using ID if number is not available
        $filename = ($application->number ?? 'application_' . $application->id) . '.pdf';
        
        $pdf = Pdf::loadView('pdf', ['record' => $application]);
        
        return $pdf->download($filename);
    }
    
    // Alternative method using stream if download doesn't work
    public function __invoke(Applications $application)
    {
        // Generate a filename using ID if number is not available
        $filename = ($application->number ?? 'application_' . $application->id) . '.pdf';
        
        $pdf = Pdf::loadView('pdf', ['record' => $application]);
        
        // Force download with proper headers
        return $pdf->download($filename);
    }
}
