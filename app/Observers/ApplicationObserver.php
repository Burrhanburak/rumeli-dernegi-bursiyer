<?php

namespace App\Observers;

use App\Models\Applications;
use App\Models\Documents;
use App\Models\ProgramDocumentRequirement;

class ApplicationObserver
{
    /**
     * Handle the Applications "created" event.
     */
    public function created(Applications $applications): void
    {
        // Check document approval status when application is created
        $this->checkDocumentApprovalStatus($applications);
    }

    /**
     * Handle the Applications "updated" event.
     */
    public function updated(Applications $applications): void
    {
        // Check document approval status when application is updated
        $this->checkDocumentApprovalStatus($applications);
    }

    /**
     * Handle the Applications "deleted" event.
     */
    public function deleted(Applications $applications): void
    {
        //
    }

    /**
     * Handle the Applications "restored" event.
     */
    public function restored(Applications $applications): void
    {
        // Check document approval status when application is restored
        $this->checkDocumentApprovalStatus($applications);
    }

    /**
     * Handle the Applications "force deleted" event.
     */
    public function forceDeleted(Applications $applications): void
    {
        //
    }

    /**
     * Check document approval status and update the application's are_documents_approved flag
     * 
     * @param Applications $application
     * @return void
     */
    protected function checkDocumentApprovalStatus(Applications $application)
    {
        // Get program requirements
        $programId = $application->program_id;
        $requiredDocTypes = ProgramDocumentRequirement::where('program_id', $programId)
            ->pluck('document_type_id')
            ->toArray();
        
        // Get user documents
        $userDocuments = $application->documents()->get();
        $userDocTypes = $userDocuments->pluck('document_type_id')->toArray();
        
        // Default to false
        $areDocumentsApproved = false;
        
        // If no requirements defined but has documents, check if all are approved
        if (empty($requiredDocTypes) && $userDocuments->isNotEmpty()) {
            $areDocumentsApproved = $userDocuments->every(function($doc) {
                return $doc->status === 'approved';
            });
        }
        
        // If requirements exist, check if all required documents exist and are approved
        if (!empty($requiredDocTypes)) {
            // Check if all required documents are uploaded
            $missingDocTypes = array_diff(
                array_map('strval', $requiredDocTypes), 
                array_map('strval', $userDocTypes)
            );
            if (empty($missingDocTypes)) {
                // Check if all required documents are approved
                $pendingDocs = $userDocuments->filter(function($doc) use ($requiredDocTypes) {
                    return in_array($doc->document_type_id, $requiredDocTypes, false) && $doc->status !== 'approved';
                });
                
                $areDocumentsApproved = $pendingDocs->isEmpty();
            }
        }
        
        // Update application's are_documents_approved flag if it changed
        if ($application->are_documents_approved !== $areDocumentsApproved) {
            $application->are_documents_approved = $areDocumentsApproved;
            
            // Also update status if documents are now approved
            if ($areDocumentsApproved) {
                $application->status = 'dogrulama_tamamlandi';
                $application->document_reviewed_by = auth()->id();
                $application->document_reviewed_at = now();
            }
            
            // Save the application without triggering events to avoid infinite loops
            $application->saveQuietly();
        }
    }
}
