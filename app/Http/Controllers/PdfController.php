<?php

namespace App\Http\Controllers;
use App\Models\Applications;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function pdf(Applications $application)
    {
        // Generate a filename using ID if number is not available
        $filename = ($application->number ?? 'application_' . $application->id) . '.pdf';
        
        // Prepare the image data if available
        if ($application->image) {
            $imagePath = storage_path('app/public/' . $application->image);
            $publicPath = public_path('storage/' . $application->image);
            
            try {
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                } elseif (file_exists($publicPath)) {
                    $imageData = base64_encode(file_get_contents($publicPath));
                } else {
                    // Try direct storage access as last resort
                    $imageData = base64_encode(Storage::disk('public')->get($application->image));
                }
                
                $extension = pathinfo($application->image, PATHINFO_EXTENSION) ?: 'jpeg';
                $application->image_data = 'data:image/' . $extension . ';base64,' . $imageData;
            } catch (\Exception $e) {
                // Image couldn't be loaded - set to null so template can handle
                $application->image_data = null;
                // Log the error if desired
                // \Log::error("Failed to load image: " . $e->getMessage());
            }
        }
        
        $pdf = Pdf::loadView('pdf', ['record' => $application]);
        
        return $pdf->download($filename);
    }
    
    // Alternative method using stream if download doesn't work
    public function __invoke(Applications $application)
    {
        // Generate a filename using ID if number is not available
        $filename = ($application->number ?? 'application_' . $application->id) . '.pdf';
        
        // Prepare the image data if available
        if ($application->image) {
            $imagePath = storage_path('app/public/' . $application->image);
            $publicPath = public_path('storage/' . $application->image);
            
            try {
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                } elseif (file_exists($publicPath)) {
                    $imageData = base64_encode(file_get_contents($publicPath));
                } else {
                    // Try direct storage access as last resort
                    $imageData = base64_encode(Storage::disk('public')->get($application->image));
                }
                
                $extension = pathinfo($application->image, PATHINFO_EXTENSION) ?: 'jpeg';
                $application->image_data = 'data:image/' . $extension . ';base64,' . $imageData;
            } catch (\Exception $e) {
                // Image couldn't be loaded - set to null so template can handle
                $application->image_data = null;
                // Log the error if desired
                // \Log::error("Failed to load image: " . $e->getMessage());
            }
        }
        
        $pdf = Pdf::loadView('pdf', ['record' => $application]);
        
        // Force download with proper headers
        return $pdf->download($filename);
    }
}
