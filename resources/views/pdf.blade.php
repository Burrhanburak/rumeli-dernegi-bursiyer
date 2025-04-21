<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Burs Başvuru Formu</title>
    <style>
        @page {
            margin: 2cm;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            position: relative;
            padding-right: 120px; /* Add padding for image */
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            letter-spacing: 0px;
        }
        
        .header h2 {
            font-size: 16px;
            color: #000;
            margin-top: 0;
            margin-bottom: 10px;
            font-weight: normal;
        }
        
        .application-info {
            margin-top: 10px;
            text-align: left;
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .form-field {
            border: 1px solid #000;
            padding: 5px;
            min-height: 20px;
            background-color: #fff;
            display: block;
            width: 100%;
            box-sizing: border-box;
            font-size: 12px;
        }
        
        .field-row {
            margin-bottom: 5px;
        }
        
        .field-label {
            display: block;
            font-weight: normal;
            margin-bottom: 3px;
            font-size: 12px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            margin-top: 15px;
        }
        
        .two-col {
            width: 48%;
            float: left;
        }
        
        .right {
            margin-left: 4%;
        }
        
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        
        .profile-image {
            width: 120px;
            height: 120px;
            border: 1px solid #000;
            background-color: #fff;
            object-fit: contain;
        }
        
        .image-container {
            position: absolute;
            top: 10px;
            right: 0;
            width: 120px;
            height: 120px;
            border: 1px solid #000;
            overflow: hidden;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #000;
            padding-top: 8px;
            border-top: 1px solid #000;
        }
        
        /* Multi-row fields for siblings */
        .sibling-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        
        .sibling-table th, .sibling-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            font-size: 12px;
        }
        
        .sibling-table th {
            font-weight: normal;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <h1>RUMELİ TÜRKLERİ KÜLTÜR VE DAYANIŞMA DERNEĞİ</h1>
        <h2>Burs Başvuru Formu</h2>
        
        <div class="application-info">
            <span style="font-weight: normal;">Başvuru Tarihi:</span> 
            <span>{{ $record->application_date ? date('d.m.Y', strtotime($record->application_date)) : now()->format('d.m.Y') }}</span>
        </div>
        
        <!-- Logo/Profile Image -->
        @if($record->image)
            <div class="image-container">
                <img class="profile-image" src="{{ storage_path('app/public/' . $record->image) }}" alt="Profil Resmi" onerror="this.style.display='none'">
            </div>
        @endif
    </div>

    <!-- General Information -->
    <div class="section-title">Genel Bilgiler</div>
    
    <div class="field-row">
        <label class="field-label">TC Kimlik No</label>
        <div class="form-field">{{ $record->national_id ?? '-' }}</div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Ad</label>
                <div class="form-field">{{ $record->name ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Soyad</label>
                <div class="form-field">{{ $record->surname ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Doğum Tarihi</label>
                <div class="form-field">{{ $record->birth_date ? date('d.m.Y', strtotime($record->birth_date)) : '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Doğum Yeri</label>
                <div class="form-field">{{ $record->birth_place ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Uyruğu</label>
                <div class="form-field">{{ $record->nationality ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Cinsiyeti</label>
                <div class="form-field">{{ $record->gender == 'Male' ? 'Erkek' : ($record->gender == 'Female' ? 'Kadın' : '-') }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Telefon</label>
                <div class="form-field">{{ $record->phone ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Email</label>
                <div class="form-field">{{ $record->email ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Adres</label>
        <div class="form-field">{{ $record->address ?? '-' }}</div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Şehir</label>
                <div class="form-field">{{ $record->city ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Posta Kodu</label>
                <div class="form-field">{{ $record->postal_code ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Nüfusa Kayıtlı Olduğu İl</label>
                <div class="form-field">{{ $record->registered_province ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Nüfusa Kayıtlı Olduğu İlçe</label>
                <div class="form-field">{{ $record->registered_district ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Özür Durumu</label>
        <div class="form-field">
            @php
                $disabilityStatus = [
                    'None' => 'Engel Yok',
                    'Mild' => 'Hafif',
                    'Moderate' => 'Orta',
                    'Severe' => 'Ağır'
                ];
                $disability = isset($record->disability_status) && isset($disabilityStatus[$record->disability_status]) ? 
                    $disabilityStatus[$record->disability_status] : '-';
                
                if ($disability != 'Engel Yok' && isset($record->disability_description) && $record->disability_description) {
                    $disability .= ' - ' . $record->disability_description;
                }
            @endphp
            {{ $disability }}
        </div>
    </div>

    <!-- Education Information -->
    <div class="section-title">Öğrenim Bilgileri</div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Okul Adı</label>
                <div class="form-field">{{ $record->school_name ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Bölüm</label>
                <div class="form-field">{{ $record->school_department ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Sınıf</label>
                <div class="form-field">{{ $record->grade ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Öğrenci Numarası</label>
                <div class="form-field">{{ $record->student_id ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Giriş Yılı</label>
                <div class="form-field">{{ $record->enrollment_year ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Üniversite Giriş Puanı</label>
                <div class="form-field">{{ $record->university_entrance_score ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Burs Oranı (%)</label>
                <div class="form-field">{{ $record->scholarship_rate ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Bitirdiğiniz Okulların adı ve derecesi (İlkokul)</label>
                <div class="form-field">{{ $record->primary_school_name ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Bitirme Yılı</label>
                <div class="form-field">{{ $record->primary_school_graduation_year ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Bitirdiğiniz Okulların adı ve derecesi (Lise)</label>
                <div class="form-field">{{ $record->high_school_name ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Bitirme Yılı</label>
                <div class="form-field">{{ $record->high_school_graduation_year ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Family Information -->
    <div class="section-title">Aile Bilgileri</div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Baba Adı</label>
                <div class="form-field">{{ $record->father_name ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Baba Soyadı</label>
                <div class="form-field">{{ $record->father_surname ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Baba Doğum Yılı</label>
                <div class="form-field">{{ $record->father_birth_year ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Baba Doğum Yeri</label>
                <div class="form-field">{{ $record->father_birth_place ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Baba Mesleği</label>
                <div class="form-field">{{ $record->father_occupation ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Baba Vefat Yılı</label>
                <div class="form-field">{{ $record->father_death_year ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Anne Adı</label>
                <div class="form-field">{{ $record->mother_name ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Anne Soyadı</label>
                <div class="form-field">{{ $record->mother_surname ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Anne Doğum Yılı</label>
                <div class="form-field">{{ $record->mother_birth_year ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Anne Doğum Yeri</label>
                <div class="form-field">{{ $record->mother_birth_place ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Anne Mesleği</label>
                <div class="form-field">{{ $record->mother_occupation ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Anne Vefat Yılı</label>
                <div class="form-field">{{ $record->mother_death_year ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Siblings Information -->
    <div class="section-title">Kardeş Bilgileri</div>
    
    <table class="sibling-table">
        <tr>
            <th>Kardeş Adı</th>
            <th>Kardeş Soyadı</th>
            <th>Kardeş Yaşı</th>
            <th>Öğrenim Durumu</th>
        </tr>
        <!-- 1. Kardeş -->
        <tr>
            <td>{{ $record->sibling1_name ?? '-' }}</td>
            <td>{{ $record->sibling1_surname ?? '-' }}</td>
            <td>{{ $record->sibling1_age ?? '-' }}</td>
            <td>{{ $record->sibling1_education ?? '-' }}</td>
        </tr>
        <!-- 2. Kardeş -->
        <tr>
            <td>{{ $record->sibling2_name ?? '-' }}</td>
            <td>{{ $record->sibling2_surname ?? '-' }}</td>
            <td>{{ $record->sibling2_age ?? '-' }}</td>
            <td>{{ $record->sibling2_education ?? '-' }}</td>
        </tr>
        <!-- 3. Kardeş -->
        <tr>
            <td>{{ $record->sibling3_name ?? '-' }}</td>
            <td>{{ $record->sibling3_surname ?? '-' }}</td>
            <td>{{ $record->sibling3_age ?? '-' }}</td>
            <td>{{ $record->sibling3_education ?? '-' }}</td>
        </tr>
        <!-- 4. Kardeş -->
        <tr>
            <td>{{ $record->sibling4_name ?? '-' }}</td>
            <td>{{ $record->sibling4_surname ?? '-' }}</td>
            <td>{{ $record->sibling4_age ?? '-' }}</td>
            <td>{{ $record->sibling4_education ?? '-' }}</td>
        </tr>
        <!-- 5. Kardeş -->
        <tr>
            <td>{{ $record->sibling5_name ?? '-' }}</td>
            <td>{{ $record->sibling5_surname ?? '-' }}</td>
            <td>{{ $record->sibling5_age ?? '-' }}</td>
            <td>{{ $record->sibling5_education ?? '-' }}</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- Residence Information -->
    <div class="section-title">İkamet Bilgileri</div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Aile Reisinin Bakmakta Olduğu Fert Sayısı :</label>
                <div class="form-field">{{ $record->family_head_dependent_count ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Sizin ve Ailenizin Geçim Sorumluluğu Kimin Üzerindedir :</label>
                <div class="form-field">{{ $record->family_subsistence_responsibility ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Aile İkametgah Adresi :</label>
        <div class="form-field">{{ $record->family_residence_address ?? '-' }}</div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">İkametgah İli</label>
                <div class="form-field">{{ $record->residence_province ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">İkametgah İlçesi</label>
                <div class="form-field">{{ $record->residence_district ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Aile Telefon</label>
        <div class="form-field">{{ $record->family_phone ?? '-' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Ailenin Yanında Kalarak Okula Devam</label>
        <div class="form-field">{{ $record->resides_with_family == 'Yes' ? 'Evet' : 'Hayır' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Yurtta Kalıyorsa Aylık Ödeme</label>
        <div class="form-field">{{ $record->dormitory_monthly_payment ?? '-' }}</div>
    </div>

    <div class="field-row">
        <label class="field-label">Öğrenim Sırasında Kaldığınız İkametgah Adresiniz:</label>
        <div class="form-field">{{ $record->education_residence_address ?? '-' }}</div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Öğrenim İli</label>
                <div class="form-field">{{ $record->education_province ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Öğrenim İlçesi</label>
                <div class="form-field">{{ $record->education_district ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Financial Information -->
    <div class="section-title">Mali Bilgiler</div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Babanızın Aylık Net Geliri (TL):</label>
                <div class="form-field">{{ $record->father_monthly_income ?? '0' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Annenizin Aylık Net Geliri (TL):</label>
                <div class="form-field">{{ $record->mother_monthly_income ?? '0' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Kardeşinizin Aylık Net Geliri (TL):</label>
                <div class="form-field">{{ $record->sibling_monthly_income ?? '0' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Eşinizin Aylık Net Geliri (TL):</label>
                <div class="form-field">{{ $record->spouse_monthly_income ?? '0' }}</div>
            </div>
        </div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Babanızın yada Annenizin Vefatı ile Size Bağlanan Maaşın Yıllık Net Geliri (TL):</label>
        <div class="form-field">{{ $record->death_benefit_annual_income ?? '0' }}</div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Ailenizin Evi Var Mı?</label>
                <div class="form-field">{{ $record->family_owns_house == 'Yes' ? 'Evet' : 'Hayır' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Kirada Oturuyor ise Kira Miktarı:</label>
                <div class="form-field">{{ $record->rent_payment_amount ?? '0' }}</div>
            </div>
        </div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Oturulan Evden Başka Ailenizin Gayrimenkulu Varsa Takribi Değeri ve Geliri :</label>
        <div class="form-field">{{ $record->real_estate_value_and_income ?? '-' }}</div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Aileye Ait Otomobil Model Yılı :</label>
                <div class="form-field">{{ $record->car_model_year ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Ailenin Başka Gelirinin Miktarı :</label>
                <div class="form-field">{{ $record->other_income_amount ?? '0' }}</div>
            </div>
        </div>
    </div>

    <!-- Other Information / Personal Information -->
    <div class="section-title">Kişisel Bilgiler</div>
    
    <div class="field-row">
        <label class="field-label">Öğrenim gördüğünüz bölümde branş seçme söz konusu ise hangi branşı seçmeyi düşünüyorsunuz?</label>
        <div class="form-field">{{ $record->field_selection ?? '-' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Okulunuzda herhangi bir kulübe üye misiniz? Henüz üye değilseniz üye olmayı düşündüğünüz bir kulüp var mı?</label>
        <div class="form-field">{{ $record->club_membership ?? '-' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Kütüphane Kullanımı</label>
        <div class="form-field">{{ $record->library_usage ?? '-' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Resim, müzik, edebiyat, sinema vb. bir hobiniz var mı?</label>
        <div class="form-field">{{ $record->hobby ?? '-' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">İleride bu çatı altında siz de bir kişiye burs olarak yardımcı olmak isteyip bunun sözünü verir misiniz?</label>
        <div class="form-field">{{ $record->scholarship_commitment == 'Yes' ? 'Evet' : 'Hayır' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Sosyal medyayı (Facebook, instagram, linkedin, twitter vb.) ne yoğunlukta kullanıyorsunuz ve hangisini daha çok kullanıyorsunuz?</label>
        <div class="form-field">{{ $record->social_media_usage ?? '-' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Bugüne kadar herhangi bir sosyal sorumluluk projesinde yer aldınız mı?</label>
        <div class="form-field">{{ $record->social_responsibility_project ?? '-' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">İş Hayatında Başarı Görüşü</label>
        <div class="form-field">{{ $record->professional_success_opinion ?? '-' }}</div>
    </div>
    
    <div class="field-row">
        <label class="field-label">Mezuniyet Sonrası Hedef</label>
        <div class="form-field">{{ $record->post_graduation_goal ?? '-' }}</div>
    </div>
    
    <!-- Reference Information -->
    <div class="section-title">Referans Bilgileri</div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">1. Referans Adı</label>
                <div class="form-field">{{ $record->reference1_name ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">1. Referans Telefon</label>
                <div class="form-field">{{ $record->reference1_phone ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">2. Referans Adı</label>
                <div class="form-field">{{ $record->reference2_name ?? '-' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">2. Referans Telefon</label>
                <div class="form-field">{{ $record->reference2_phone ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <!-- Scholarship Information -->
    <div class="section-title">Burs Bilgileri</div>
    
    <div class="clearfix">
        <div class="two-col">
            <div class="field-row">
                <label class="field-label">Başka Burs/Kredi Alımı</label>
                <div class="form-field">{{ $record->receiving_other_scholarship == 'Yes' ? 'Evet' : 'Hayır' }}</div>
            </div>
        </div>
        
        <div class="two-col right">
            <div class="field-row">
                <label class="field-label">Başka Burs/Kredi Kurumu</label>
                <div class="form-field">{{ $record->other_scholarship_institution ?? '-' }}</div>
            </div>
        </div>
    </div>
    
    <div class="field-row">
        <label class="field-label">IBAN</label>
        <div class="form-field">{{ $record->iban ?? '-' }}</div>
    </div>
    
    <div class="page-break"></div>
    
    <!-- Notes -->
    <div class="section-title">Notlar</div>
    
    <div class="field-row">
        <label class="field-label">Notlar</label>
        <div class="form-field">{{ $record->notes ?? '-' }}</div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Bu belge {{ date('d.m.Y H:i') }} tarihinde oluşturulmuştur.</p>
    </div>
</body>
</html>