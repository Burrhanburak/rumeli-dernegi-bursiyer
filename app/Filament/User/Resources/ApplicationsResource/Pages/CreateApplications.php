<?php

namespace App\Filament\User\Resources\ApplicationsResource\Pages;

use App\Filament\User\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CreateApplications extends CreateRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected static ?string $title = 'Başvuru Oluştur';

    protected static ?string $breadcrumb = 'Başvuru Oluştur';

    protected static ?string $breadcrumbParent = 'Başvurular';
    


    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    // Add a mount method to set default values when the form loads
    // public function mount(): void 
    // {
    //     parent::mount();
        
    //     // Ensure critical fields have defaults
    //     $this->form->fill([
    //         'are_documents_approved' => false,
    //         'is_interview_completed' => false,
    //         'status' => 'awaiting_evaluation',
    //         'application_date' => now()->format('Y-m-d')
    //     ]);
        
    //     // Log that we're setting up the form with defaults
    //     Log::info('Form mounted with default values');
    // }

    
    // Automatically set the user_id to the current logged-in user's ID
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Core required fields that must be set
        $data['user_id'] = auth()->id();
        $data['application_date'] = now()->format('Y-m-d');
        $data['are_documents_approved'] = false;
        $data['is_interview_completed'] = false;
        $data['status'] = 'awaiting_evaluation';
        
        // Generate a unique application ID if needed
        if (!isset($data['application_id']) || empty($data['application_id'])) {
            $data['application_id'] = uniqid('APP-', true);
        }
        
        // Get all fillable fields from the model
        $model = app(ApplicationsResource::getModel());
        $fillable = $model->getFillable();
        
        // Ensure all fillable fields have at least a null value in $data
        foreach ($fillable as $field) {
            if (!array_key_exists($field, $data)) {
                $data[$field] = null;
            }
        }
        
        // Log all form data to debug what's being submitted
        Log::info('Application form data before create', [
            'user_id' => auth()->id(),
            'application_id' => $data['application_id'] ?? null,
            'form_data_keys' => array_keys($data),
            'filled_fields' => array_filter($data, function ($value) {
                return $value !== null;
            }),
            'all_data' => $data,
        ]);

        dd($data);
        
        return $data;
    }
    
    // Override the default notification title
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Başvurunuz başarıyla oluşturuldu.';
    }
    
    // Use handleRecordCreation to log after record is created
    protected function handleRecordCreation(array $data): Model
    {
        try {
            // Set critical fields explicitly one last time
            $data['user_id'] = auth()->id();
            $data['application_date'] = now()->format('Y-m-d');
            $data['are_documents_approved'] = false;
            $data['is_interview_completed'] = false;
            $data['status'] = 'awaiting_evaluation';
            
            if (empty($data['application_id'])) {
                $data['application_id'] = uniqid('APP-', true);
            }
            
            // Try standard Eloquent creation first
            try {
                $record = parent::handleRecordCreation($data);
            } catch (\Exception $e) {
                // If it fails due to NULL values, try a more direct approach
                Log::warning('Standard record creation failed, trying direct insert', [
                    'error' => $e->getMessage()
                ]);
                
                // Get only the fillable fields
                $model = app(ApplicationsResource::getModel());
                $fillableFields = $model->getFillable();
                $filteredData = array_intersect_key($data, array_flip($fillableFields));
                
                // Ensure we have values for critical fields
                $filteredData['user_id'] = auth()->id();
                $filteredData['application_date'] = now()->format('Y-m-d');
                $filteredData['are_documents_approved'] = false;
                $filteredData['is_interview_completed'] = false;
                $filteredData['status'] = 'awaiting_evaluation';
                $filteredData['application_id'] = $data['application_id'] ?? uniqid('APP-', true);
                $filteredData['created_at'] = now();
                $filteredData['updated_at'] = now();
                
                // Try direct insert
                DB::table('applications')->insert($filteredData);
                
                // Get the record we just created
                $record = $model::where('application_id', $filteredData['application_id'])->first();
                
                if (!$record) {
                    throw new \Exception('Failed to create record using direct insert');
                }
            }
            
            // Log the entire record to see what was actually saved to the database
            Log::info('Application successfully created', [
                'user_id' => auth()->id(),
                'application_id' => $record->application_id,
                'id' => $record->id,
                'record_attributes' => $record->getAttributes(),
            ]);
            
            return $record;
        } catch (\Exception $e) {
            Log::error('Exception in handleRecordCreation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Hook that is called after the record is created.
     * This ensures that the user_id is properly set.
     */
    protected function afterCreate(): void
    {
        // Verify the user_id is correctly set
        if (!$this->record->user_id || $this->record->user_id != auth()->id()) {
            // Force update the user_id if it's not correct
            $this->record->user_id = auth()->id();
            $this->record->save();
            
            Log::info('Fixed user_id after creation', [
                'application_id' => $this->record->application_id,
                'user_id' => auth()->id()
            ]);
        }
        
        // Verify the relationship works
        $user = $this->record->user;
        
        Log::info('Application user relationship check', [
            'application_id' => $this->record->application_id,
            'has_user_relationship' => !is_null($user),
            'user_id_in_model' => $this->record->user_id,
            'user_info' => $user ? "{$user->name} {$user->surname}" : 'NULL'
        ]);
    }
    
    // Override the create method to ensure data is processed correctly
    public function create(bool $another = false): void
    {
        $this->authorizeAccess();
        
        try {
            // Log at the beginning of the process
            Log::info('Starting application creation process');
            
            $this->callHook('beforeValidate');
            
            $data = $this->form->getState();
            
            // Log the form state before validation
            Log::info('Application form state before validation', [
                'data_keys' => array_keys($data),
                'data' => $data,
            ]);
            
            $this->callHook('afterValidate');
            
            $this->callHook('beforeCreate');
            
            $data = $this->mutateFormDataBeforeCreate($data);
            
            // Extra safety: ensure boolean fields are explicitly set to false, not null
            $data['are_documents_approved'] = false;
            $data['is_interview_completed'] = false;
            
            // Debugging: Check if any data is being lost between form collection and saving
            $modelInstance = $this->getModel()::make($data);
            $fillableAttributes = $modelInstance->getFillable();
            $missingAttributes = array_diff($fillableAttributes, array_keys($data));
            
            if (!empty($missingAttributes)) {
                Log::warning('Missing attributes in form data', [
                    'missing_attributes' => $missingAttributes,
                ]);
                
                // Add the missing attributes with default values
                foreach ($missingAttributes as $attribute) {
                    $data[$attribute] = null;
                }
            }
            
            // Log the final data before saving
            Log::info('Final application data before save', [
                'data' => $data
            ]);
            
            // Create the record with all form data
            $this->record = $this->handleRecordCreation($data);
            
            $this->form->model($this->record)->saveRelationships();
            
            $this->callHook('afterCreate');
            
            // Success notification
            if (filled($this->getCreatedNotificationTitle())) {
                Notification::make()
                    ->title($this->getCreatedNotificationTitle())
                    ->success()
                    ->send();
            }
            
            Log::info('Application creation successful, redirecting...');
            
            // Redirect to appropriate page
            if ($another) {
                // Redirect to create page to create another record
                $this->form->fill();
                return;
            }
            
            $this->redirect($this->getRedirectUrl());
            
        } catch (\Exception $e) {
            // Detailed error logging to diagnose the issue
            Log::error('Error creating application', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Show detailed error notification to the user
            Notification::make()
                ->title('Hata: Başvuru oluşturulamadı')
                ->body('Hata detayı: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }
    
    // Custom hook to prepare data before validation
    protected function beforeValidate(): void
    {
        // Get the current form data
        $data = $this->form->getRawState();
        
        // Log the raw state before any modifications
        Log::info('Raw form state before validation', [
            'raw_data_keys' => array_keys($data),
            'raw_data' => $data,
        ]);
    }
    
    // Override Filament's validation to make it more lenient
    public function getFormStatePath(): string
    {
        return 'data';
    }
    
    // Redirect to applications list after successful creation
    protected function getRedirectUrl(): string
    {
        // Get the resource index URL and log it for debugging
        $redirectUrl = $this->getResource()::getUrl('index');
        Log::info('Redirecting to: ' . $redirectUrl);
        return $redirectUrl;
    }


}
