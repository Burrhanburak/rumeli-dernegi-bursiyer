<?php

namespace App\Http\Controllers;
use App\Models\Applications;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function pdf(Applications $application)
    {
        return $this->generatePdf($application, 'download');
    }
    
    // Alternative method using stream if download doesn't work
    public function __invoke(Applications $application)
    {
        return $this->generatePdf($application, 'download');
    }
    
    protected function generatePdf(Applications $application, $outputMethod = 'download')
    {
        // Generate a filename using ID if number is not available
        $filename = ($application->number ?? 'application_' . $application->id) . '.pdf';
        
        // Prepare the image data if available
        if ($application->image) {
            // Log image file details for debugging
            $imagePath = 'public/' . $application->image;
            $fullStoragePath = storage_path('app/public/' . $application->image);
            $fullPublicPath = public_path('storage/' . $application->image);
            $imageUrl = asset('storage/' . $application->image);
            
            Log::info("PDF Image Debug", [
                'image_field' => $application->image,
                'storage_exists' => Storage::exists($imagePath),
                'file_exists_storage' => file_exists($fullStoragePath),
                'file_exists_public' => file_exists($fullPublicPath),
                'image_url' => $imageUrl
            ]);
            
            try {
                // Method 1: Try Storage facade directly
                if (Storage::exists($imagePath)) {
                    Log::info("Loading image via Storage facade");
                    $imageData = base64_encode(Storage::get($imagePath));
                    $extension = pathinfo($application->image, PATHINFO_EXTENSION) ?: 'jpeg';
                    $application->image_data = 'data:image/' . $extension . ';base64,' . $imageData;
                }
                // Method 2: Try direct file access from storage path
                elseif (file_exists($fullStoragePath)) {
                    Log::info("Loading image via direct storage path");
                    $imageData = base64_encode(file_get_contents($fullStoragePath));
                    $extension = pathinfo($fullStoragePath, PATHINFO_EXTENSION) ?: 'jpeg';
                    $application->image_data = 'data:image/' . $extension . ';base64,' . $imageData;
                }
                // Method 3: Try direct file access from public path 
                elseif (file_exists($fullPublicPath)) {
                    Log::info("Loading image via public path");
                    $imageData = base64_encode(file_get_contents($fullPublicPath));
                    $extension = pathinfo($fullPublicPath, PATHINFO_EXTENSION) ?: 'jpeg';
                    $application->image_data = 'data:image/' . $extension . ';base64,' . $imageData;
                }
                // Method 4: Use direct URL (not ideal but may work in some cases)
                else {
                    Log::info("Loading image via direct URL");
                    $application->image_data = $imageUrl;
                }
            } catch (\Exception $e) {
                Log::error("Failed to load image: " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'file' => $application->image
                ]);
                $application->image_data = null;
            }
        }
        
        // Configure PDF options
        $options = [
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ];
        
        $pdf = Pdf::loadView('pdf', ['record' => $application])
                  ->setOptions($options);
        
        if ($outputMethod === 'stream') {
            return $pdf->stream($filename);
        } else {
            return $pdf->download($filename);
        }
    }
}
