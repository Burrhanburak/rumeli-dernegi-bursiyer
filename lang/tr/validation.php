<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Doğrulama Dil Satırları
    |--------------------------------------------------------------------------
    |
    | Aşağıdaki dil satırları doğrulama sınıfı tarafından kullanılan varsayılan
    | hata mesajlarını içerir. Bazı kuralların birden çok versiyonu bulunur.
    | Bu mesajlar burada kolayca özelleştirilebilir.
    |
    */

    'accepted' => ':attribute kabul edilmelidir.',
    'accepted_if' => ':other :value olduğunda :attribute kabul edilmelidir.',
    'active_url' => ':attribute geçerli bir URL olmalıdır.',
    'after' => ':attribute değeri :date tarihinden sonra olmalıdır.',
    'after_or_equal' => ':attribute değeri :date tarihinden sonra veya eşit olmalıdır.',
    'alpha' => ':attribute sadece harflerden oluşmalıdır.',
    'alpha_dash' => ':attribute sadece harfler, rakamlar ve tirelerden oluşmalıdır.',
    'alpha_num' => ':attribute sadece harfler ve rakamlar içermelidir.',
    'array' => ':attribute dizi olmalıdır.',
    'before' => ':attribute değeri :date tarihinden önce olmalıdır.',
    'before_or_equal' => ':attribute değeri :date tarihinden önce veya eşit olmalıdır.',
    'between' => [
        'numeric' => ':attribute :min - :max arasında olmalıdır.',
        'file' => ':attribute :min - :max arasındaki kilobayt değeri olmalıdır.',
        'string' => ':attribute :min - :max arasında karakterden oluşmalıdır.',
        'array' => ':attribute :min - :max arasında nesneye sahip olmalıdır.',
    ],
    'boolean' => ':attribute sadece doğru veya yanlış olmalıdır.',
    'confirmed' => ':attribute tekrarı eşleşmiyor.',
    'current_password' => 'Şifre geçersiz.',
    'date' => ':attribute geçerli bir tarih olmalıdır.',
    'date_equals' => ':attribute ile :date aynı tarihler olmalıdır.',
    'date_format' => ':attribute :format biçimi ile eşleşmiyor.',
    'declined' => ':attribute kabul edilmemelidir.',
    'declined_if' => ':other :value olduğunda :attribute kabul edilmemelidir.',
    'different' => ':attribute ile :other birbirinden farklı olmalıdır.',
    'digits' => ':attribute :digits rakam olmalıdır.',
    'digits_between' => ':attribute :min ile :max arasında rakam olmalıdır.',
    'dimensions' => ':attribute görsel ölçüleri geçersiz.',
    'distinct' => ':attribute alanı yinelenen bir değere sahip.',
    'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
    'ends_with' => ':attribute, şunlardan biriyle bitmelidir: :values',
    'exists' => 'Seçili :attribute geçersiz.',
    'file' => ':attribute dosya olmalıdır.',
    'filled' => ':attribute alanının doldurulması zorunludur.',
    'gt' => [
        'numeric' => ':attribute, :value değerinden büyük olmalıdır.',
        'file'    => ':attribute, :value kilobayt boyutundan büyük olmalıdır.',
        'string'  => ':attribute, :value karakterden uzun olmalıdır.',
        'array'   => ':attribute, :value adetten fazla olmalıdır.',
    ],
    'gte' => [
        'numeric' => ':attribute, :value kadar veya daha fazla olmalıdır.',
        'file'    => ':attribute, :value kilobayt boyutu kadar veya daha büyük olmalıdır.',
        'string'  => ':attribute, :value karakter kadar veya daha uzun olmalıdır.',
        'array'   => ':attribute, :value adet veya daha fazla olmalıdır.',
    ],
    'image' => ':attribute alanı resim dosyası olmalıdır.',
    'in' => ':attribute değeri geçersiz.',
    'in_array' => ':attribute alanı :other içinde mevcut değil.',
    'integer' => ':attribute tamsayı olmalıdır.',
    'ip' => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json' => ':attribute geçerli bir JSON dizesi olmalıdır.',
    'lt' => [
        'numeric' => ':attribute, :value değerinden küçük olmalıdır.',
        'file'    => ':attribute, :value kilobayt boyutundan küçük olmalıdır.',
        'string'  => ':attribute, :value karakterden kısa olmalıdır.',
        'array'   => ':attribute, :value adetten az olmalıdır.',
    ],
    'lte' => [
        'numeric' => ':attribute, :value kadar veya daha küçük olmalıdır.',
        'file'    => ':attribute, :value kilobayt boyutu kadar veya daha küçük olmalıdır.',
        'string'  => ':attribute, :value karakter kadar veya daha kısa olmalıdır.',
        'array'   => ':attribute, :value adet veya daha az olmalıdır.',
    ],
    'max' => [
        'numeric' => ':attribute değeri :max değerinden küçük olmalıdır.',
        'file' => ':attribute değeri :max kilobayt değerinden küçük olmalıdır.',
        'string' => ':attribute değeri :max karakterden küçük olmalıdır.',
        'array' => ':attribute değeri :max adedinden az nesneye sahip olmalıdır.',
    ],
    'mimes' => ':attribute dosya biçimi :values olmalıdır.',
    'mimetypes' => ':attribute dosya biçimi :values olmalıdır.',
    'min' => [
        'numeric' => ':attribute değeri :min değerinden büyük olmalıdır.',
        'file' => ':attribute değeri :min kilobayt değerinden büyük olmalıdır.',
        'string' => ':attribute değeri :min karakterden büyük olmalıdır.',
        'array' => ':attribute en az :min nesneye sahip olmalıdır.',
    ],
    'multiple_of' => ':attribute, :value değerinin katları olmalıdır.',
    'not_in' => 'Seçili :attribute geçersiz.',
    'not_regex' => ':attribute biçimi geçersiz.',
    'numeric' => ':attribute sayı olmalıdır.',
    'password' => 'Şifre geçersiz.',
    'present' => ':attribute alanı mevcut olmalıdır.',
    'prohibited' => ':attribute alanını gönderemezsiniz.',
    'prohibited_if' => ':other değeri :value olduğunda :attribute alanını gönderemezsiniz.',
    'prohibited_unless' => ':other değeri :values içinde olmadığı sürece :attribute alanını gönderemezsiniz.',
    'prohibits' => ':attribute alanı ile :other alanını birlikte gönderemezsiniz.',
    'regex' => ':attribute biçimi geçersiz.',
    'required' => ':attribute alanı gereklidir.',
    'required_if' => ':attribute alanı, :other :value değerine sahip olduğunda zorunludur.',
    'required_unless' => ':attribute alanı, :other alanı :values değerlerinden birine sahip olmadığında zorunludur.',
    'required_with' => ':attribute alanı :values varken zorunludur.',
    'required_with_all' => ':attribute alanı herhangi bir :values değeri varken zorunludur.',
    'required_without' => ':attribute alanı :values yokken zorunludur.',
    'required_without_all' => ':attribute alanı :values değerlerinden herhangi biri yokken zorunludur.',
    'same' => ':attribute ile :other eşleşmelidir.',
    'size' => [
        'numeric' => ':attribute :size olmalıdır.',
        'file' => ':attribute :size kilobyte olmalıdır.',
        'string' => ':attribute :size karakter olmalıdır.',
        'array' => ':attribute :size nesneye sahip olmalıdır.',
    ],
    'starts_with' => ':attribute şunlardan biri ile başlamalıdır: :values',
    'string' => ':attribute dizge olmalıdır.',
    'timezone' => ':attribute geçerli bir saat dilimi olmalıdır.',
    'unique' => ':attribute daha önceden kayıt edilmiş.',
    'uploaded' => ':attribute yüklemesi başarısız.',
    'url' => ':attribute biçimi geçersiz.',
    'uuid' => ':attribute bir UUID formatına uygun olmalıdır.',

    /*
    |--------------------------------------------------------------------------
    | Özelleştirilmiş Doğrulama Mesajları
    |--------------------------------------------------------------------------
    |
    | Bu alanda her niteleyici (attribute) ve kural (rule) ikilisine özel hata
    | mesajları tanımlayabilirsiniz. Bu özellik, son kullanıcıya daha gerçekçi
    | metinler göstermeniz için oldukça yararlıdır.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'özel-mesaj',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Özelleştirilmiş Niteleyici İsimleri
    |--------------------------------------------------------------------------
    |
    | Bu alan, her niteleyicinin (attribute) dilimizde nasıl adlandırılacağını
    | belirlemenizi sağlar. Örnek olarak, 'email' niteleyicisini "e-posta adresi"
    | olarak çevirebilirsiniz.
    |
    */

    'attributes' => [
        'name' => 'Ad',
        'surname' => 'Soyad',
        'email' => 'E-posta',
        'phone' => 'Telefon',
        'birth_date' => 'Doğum Tarihi',
        'birth_place' => 'Doğum Yeri',
        'nationality' => 'Uyruk',
        'gender' => 'Cinsiyet',
        'national_id' => 'T.C. Kimlik No',
        'address' => 'Adres',
        'city' => 'Şehir',
        'postal_code' => 'Posta Kodu',
        'registered_province' => 'Nüfusa Kayıtlı Olduğu İl',
        'registered_district' => 'Nüfusa Kayıtlı Olduğu İlçe',
        'school_name' => 'Okul Adı',
        'school_department' => 'Bölüm',
        'grade' => 'Sınıf',
        'enrollment_year' => 'Giriş Yılı',
        'scholarship_rate' => 'Burs Oranı',
        'university_entrance_score' => 'Üniversite Giriş Puanı',
        'student_id' => 'Öğrenci Numarası',
        'father_name' => 'Baba Adı',
        'mother_name' => 'Anne Adı',
        'password' => 'Şifre',
        'password_confirmation' => 'Şifre Tekrarı',
    ],
];