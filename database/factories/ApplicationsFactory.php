<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Applications;
use App\Models\User;
use App\Models\ScholarshipProgram;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Applications>
 */
class ApplicationsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Applications::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genders = ['Male', 'Female', 'Other'];
        $yesNo = ['Yes', 'No'];
        
        // Create a custom phone formatter that ensures phone numbers are under 15 chars
        $shortPhoneNumber = function() {
            return substr($this->faker->numerify('0534-544-4322'), 0, 14);
        };
        
        return [
            'user_id' => User::factory(),
            'program_id' => ScholarshipProgram::factory(),
            'application_id' => $this->faker->unique()->uuid(),
            'application_date' => $this->faker->date(),
            'status' => $this->faker->randomElement([
                'scholarship_pool',
                'pre_approved',
                'rejected',
                'awaiting_documents',
                'documents_under_review',
                'interview_pool',
                'awaiting_evaluation',
                'interview_scheduled',
                'interview_completed',
                'accepted',
                'final_acceptance',
                'previous_scholar'
            ]),
            'pre_approved_by' => null,
            'pre_approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'document_reviewed_by' => null,
            'document_reviewed_at' => null,
            'interview_pool_by' => null,
            'interview_pool_at' => null,
            'accepted_by' => null,
            'accepted_at' => null,
            'final_acceptance_by' => null,
            'final_acceptance_at' => null,
            'notes' => $this->faker->text(),
            'rejection_reason' => $this->faker->optional()->sentence(),
            'are_documents_approved' => $this->faker->boolean(),
            'is_interview_completed' => $this->faker->boolean(),
            'approval_date' => $this->faker->optional()->date(),
            
            // Personal Information
            'image' => null,
            'national_id' => $this->faker->numerify('###########'),
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'birth_date' => $this->faker->date(),
            'birth_place' => $this->faker->city(),
            'nationality' => $this->faker->country(),
            'gender' => $this->faker->randomElement($genders),
            'phone' => $shortPhoneNumber(),
            'email' => $this->faker->email(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'physical_disability' => $this->faker->optional()->sentence(),
            'registered_province' => $this->faker->city(),
            'registered_district' => $this->faker->city(),
            'disability_description' => $this->faker->optional()->paragraph(),
            'disability_status' => $this->faker->optional()->randomElement(['None', 'Mild', 'Moderate', 'Severe']),
            
            // Education Information
            'school_name' => $this->faker->company() . ' University',
            'school_department' => $this->faker->randomElement([
                'Computer Engineering', 
                'Electrical Engineering', 
                'Mechanical Engineering',
                'Business Administration',
                'Economics',
                'Medicine',
                'Law'
            ]),
            'grade' => $this->faker->randomElement(['1', '2', '3', '4', 'Master', 'PhD']),
            'enrollment_year' => $this->faker->numberBetween(2019, 2024),
            'scholarship_rate' => $this->faker->optional()->randomElement(['25%', '50%', '75%', '100%']),
            'university_entrance_score' => $this->faker->randomFloat(3, 300, 500),
            'student_id' => $this->faker->numerify('##########'),
            'primary_school_name' => $this->faker->company() . ' Primary School',
            'primary_school_graduation_year' => $this->faker->numberBetween(2010, 2015),
            'high_school_name' => $this->faker->company() . ' High School',
            'high_school_graduation_year' => $this->faker->numberBetween(2015, 2020),
            
            // Family Information
            'father_name' => $this->faker->firstName('male'),
            'father_surname' => $this->faker->lastName(),
            'father_birth_year' => $this->faker->numberBetween(1960, 1985),
            'father_birth_place' => $this->faker->city(),
            'father_occupation' => $this->faker->jobTitle(),
            'father_death_year' => $this->faker->optional(0.1)->numberBetween(2000, 2024),
            'mother_name' => $this->faker->firstName('female'),
            'mother_surname' => $this->faker->lastName(),
            'mother_birth_year' => $this->faker->numberBetween(1960, 1985),
            'mother_birth_place' => $this->faker->city(),
            'mother_occupation' => $this->faker->jobTitle(),
            'mother_death_year' => $this->faker->optional(0.1)->numberBetween(2000, 2024),
            
            // Siblings Information
            'sibling1_name' => $this->faker->optional()->firstName(),
            'sibling1_surname' => $this->faker->optional()->lastName(),
            'sibling1_age' => $this->faker->optional()->numberBetween(5, 30),
            'sibling1_education' => $this->faker->optional()->randomElement(['Primary School', 'High School', 'University', 'Graduate']),
            'sibling2_name' => $this->faker->optional(0.7)->firstName(),
            'sibling2_surname' => $this->faker->optional(0.7)->lastName(),
            'sibling2_age' => $this->faker->optional(0.7)->numberBetween(5, 30),
            'sibling2_education' => $this->faker->optional(0.7)->randomElement(['Primary School', 'High School', 'University', 'Graduate']),
            'sibling3_name' => $this->faker->optional(0.5)->firstName(),
            'sibling3_surname' => $this->faker->optional(0.5)->lastName(),
            'sibling3_age' => $this->faker->optional(0.5)->numberBetween(5, 30),
            'sibling3_education' => $this->faker->optional(0.5)->randomElement(['Primary School', 'High School', 'University', 'Graduate']),
            'sibling4_name' => $this->faker->optional(0.3)->firstName(),
            'sibling4_surname' => $this->faker->optional(0.3)->lastName(),
            'sibling4_age' => $this->faker->optional(0.3)->numberBetween(5, 30),
            'sibling4_education' => $this->faker->optional(0.3)->randomElement(['Primary School', 'High School', 'University', 'Graduate']),
            'sibling5_name' => $this->faker->optional(0.1)->firstName(),
            'sibling5_surname' => $this->faker->optional(0.1)->lastName(),
            'sibling5_age' => $this->faker->optional(0.1)->numberBetween(5, 30),
            'sibling5_education' => $this->faker->optional(0.1)->randomElement(['Primary School', 'High School', 'University', 'Graduate']),
            
            // Residence Information
            'family_head_dependent_count' => $this->faker->numberBetween(1, 8),
            'family_subsistence_responsibility' => $this->faker->randomElement(['Father', 'Mother', 'Self', 'Other']),
            'family_residence_address' => $this->faker->address(),
            'residence_province' => $this->faker->city(),
            'residence_district' => $this->faker->citySuffix(),
            'family_phone' => $shortPhoneNumber(),
            'resides_with_family' => $this->faker->randomElement($yesNo),
            'dormitory_monthly_payment' => $this->faker->optional()->randomFloat(2, 500, 3000),
            'education_residence_address' => $this->faker->optional()->address(),
            'education_province' => $this->faker->optional()->city(),
            'education_district' => $this->faker->optional()->citySuffix(),
            
            // Financial Information
            'father_monthly_income' => $this->faker->randomFloat(2, 0, 20000),
            'mother_monthly_income' => $this->faker->randomFloat(2, 0, 20000),
            'sibling_monthly_income' => $this->faker->optional()->randomFloat(2, 0, 10000),
            'spouse_monthly_income' => $this->faker->optional()->randomFloat(2, 0, 15000),
            'death_benefit_annual_income' => $this->faker->optional(0.1)->randomFloat(2, 10000, 50000),
            'family_owns_house' => $this->faker->randomElement($yesNo),
            'rent_payment_amount' => $this->faker->optional()->randomFloat(2, 1000, 8000),
            'real_estate_value_and_income' => $this->faker->optional()->sentence(),
            'car_model_year' => $this->faker->optional()->numberBetween(2000, 2023),
            'other_income_amount' => $this->faker->optional()->randomFloat(2, 0, 5000),
            
            // Other Information
            'field_selection' => $this->faker->optional()->randomElement(['Science', 'Art', 'Sports', 'Literature']),
            'club_membership' => $this->faker->optional()->sentence(),
            'library_usage' => $this->faker->optional()->sentence(),
            'hobby' => $this->faker->optional()->sentence(),
            'scholarship_commitment' => $this->faker->randomElement($yesNo),
            'social_media_usage' => $this->faker->optional()->sentence(),
            'social_responsibility_project' => $this->faker->optional()->paragraph(),
            'professional_success_opinion' => $this->faker->optional()->paragraph(),
            'post_graduation_goal' => $this->faker->optional()->paragraph(),
            
            // Reference Information
            'reference1_name' => $this->faker->name(),
            'reference1_phone' => $shortPhoneNumber(),
            'reference2_name' => $this->faker->optional()->name(),
            'reference2_phone' => $this->faker->optional() ? $shortPhoneNumber() : null,
            
            // Scholarship Information
            'receiving_other_scholarship' => $this->faker->randomElement($yesNo),
            'other_scholarship_institution' => $this->faker->optional()->company(),
            'iban' => $this->faker->iban('TR'),
        ];
    }

    /**
     * Create an application with pre-approved status
     */
    public function preApproved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pre_approved',
                'pre_approved_by' => User::factory()->create(['is_admin' => true])->id,
                'pre_approved_at' => now(),
            ];
        });
    }

    /**
     * Create a rejected application
     */
    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'rejected_by' => User::factory()->create(['is_admin' => true])->id,
                'rejected_at' => now(),
                'rejection_reason' => $this->faker->sentence(),
            ];
        });
    }

    /**
     * Create an application with documents under review
     */
    public function documentsUnderReview()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'documents_under_review',
                'document_reviewed_by' => User::factory()->create(['is_admin' => true])->id,
                'document_reviewed_at' => now(),
            ];
        });
    }

    /**
     * Create an application in interview pool
     */
    public function interviewPool()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'interview_pool',
                'interview_pool_by' => User::factory()->create(['is_admin' => true])->id,
                'interview_pool_at' => now(),
            ];
        });
    }

    /**
     * Create an accepted application
     */
    public function accepted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'accepted',
                'accepted_by' => User::factory()->create(['is_admin' => true])->id,
                'accepted_at' => now(),
            ];
        });
    }

    /**
     * Create an application with final acceptance
     */
    public function finalAcceptance()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'final_acceptance',
                'final_acceptance_by' => User::factory()->create(['is_admin' => true])->id,
                'final_acceptance_at' => now(),
                'approval_date' => now(),
            ];
        });
    }
}
