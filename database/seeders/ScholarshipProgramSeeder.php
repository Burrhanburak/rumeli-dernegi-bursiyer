<?php

namespace Database\Seeders;

use App\Models\ScholarshipProgram;
use Illuminate\Database\Seeder;

class ScholarshipProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Lisans Bursu',
                'description' => '<p>Bu program, lisans öğrencilerine yönelik olarak tasarlanmış burs programıdır. Başarılı öğrencilere aylık burs desteği sağlanacaktır.</p>',
                'default_amount' => 2500,
                'requirements' => 'Lisans öğrencisi olmak, Not ortalaması en az 3.0/4.0 olmak',
                'notes' => 'Kamu üniversitelerinde okuyan öğrencilere öncelik verilecektir.',
                'application_start_date' => '2023-06-01',
                'application_end_date' => '2023-07-15',
                'program_start_date' => '2023-09-01',
                'program_end_date' => '2024-06-30',
                'is_active' => true,
                'max_recipients' => 50,
            ],
            [
                'name' => 'Yüksek Lisans Bursu',
                'description' => '<p>Yüksek lisans öğrencileri için akademik başarıyı desteklemek amacıyla hazırlanmış burs programıdır.</p>',
                'default_amount' => 3500,
                'requirements' => 'Yüksek lisans öğrencisi olmak, Akademik başarı göstermek',
                'notes' => 'Araştırma projelerinde aktif olan öğrencilere öncelik verilecektir.',
                'application_start_date' => '2023-05-15',
                'application_end_date' => '2023-06-30',
                'program_start_date' => '2023-09-01',
                'program_end_date' => '2024-08-31',
                'is_active' => true,
                'max_recipients' => 25,
            ],
            [
                'name' => 'Doktora Bursu',
                'description' => '<p>Doktora öğrencilerine yönelik araştırma ve tez çalışmalarını desteklemek amacıyla oluşturulmuş burs programıdır.</p>',
                'default_amount' => 5000,
                'requirements' => 'Doktora öğrencisi olmak, Araştırma projeleri yürütüyor olmak',
                'notes' => 'Bilimsel yayın yapan adaylara öncelik verilecektir.',
                'application_start_date' => '2023-04-01',
                'application_end_date' => '2023-05-15',
                'program_start_date' => '2023-09-01',
                'program_end_date' => '2024-08-31',
                'is_active' => true,
                'max_recipients' => 15,
            ],
            [
                'name' => 'Başarı Bursu',
                'description' => '<p>Lisans öğrencilerine yönelik başarı odaklı burs programıdır.</p>',
                'default_amount' => 2000,
                'requirements' => 'Not ortalaması en az 3.5/4.0 olmak',
                'notes' => 'Ekonomik durumu dezavantajlı öğrencilere öncelik verilecektir.',
                'application_start_date' => '2022-05-01',
                'application_end_date' => '2022-06-15',
                'program_start_date' => '2022-09-01',
                'program_end_date' => '2023-06-30',
                'is_active' => false,
                'max_recipients' => 40,
            ],
            [
                'name' => 'Araştırma Bursu',
                'description' => '<p>Bilimsel araştırma projelerinde yer alan öğrencilere destek sağlamak amacıyla oluşturulmuş burs programıdır.</p>',
                'default_amount' => 4000,
                'requirements' => 'Bir araştırma projesinde aktif olarak çalışıyor olmak',
                'notes' => 'Teknik ve mühendislik alanlarına öncelik verilecektir.',
                'application_start_date' => '2023-07-01',
                'application_end_date' => '2023-08-15',
                'program_start_date' => '2023-10-01',
                'program_end_date' => '2024-09-30',
                'is_active' => true,
                'max_recipients' => 20,
            ],
        ];

        foreach ($programs as $program) {
            ScholarshipProgram::create($program);
        }
    }
} 