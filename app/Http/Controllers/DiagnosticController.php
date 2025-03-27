<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applications;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DiagnosticController extends Controller
{
    /**
     * Check application-user relationships
     */
    public function checkRelationships(Request $request)
    {
        // Get all applications
        $applications = Applications::all();
        
        $results = [];
        
        foreach ($applications as $application) {
            $user = null;
            
            // Try different ways to get the user
            if ($application->user_id) {
                $user = User::find($application->user_id);
            }
            
            $results[] = [
                'application_id' => $application->application_id,
                'id' => $application->id,
                'user_id' => $application->user_id,
                'user_exists' => $user ? true : false,
                'user_name' => $user ? $user->name . ' ' . $user->surname : 'N/A',
                'relationship_works' => $application->user ? true : false
            ];
        }
        
        // Check for applications with null user_id
        $nullUserIdCount = Applications::whereNull('user_id')->count();
        
        // Check schema for user_id column
        $userIdColumn = DB::select("SHOW COLUMNS FROM applications WHERE Field = 'user_id'");
        $userIdColumnDetails = $userIdColumn[0] ?? null;
        
        return response()->json([
            'applications_count' => $applications->count(),
            'null_user_id_count' => $nullUserIdCount,
            'user_id_column_details' => $userIdColumnDetails,
            'relationship_details' => $results
        ]);
    }
}
