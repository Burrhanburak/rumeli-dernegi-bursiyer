<x-mail::message>
# Burs Başvuru Sistemine Hoş Geldiniz, {{ $userName }}!

Burs başvuru sistemimize kaydınız başarıyla oluşturulmuştur.

Aşağıdaki bilgileri kullanarak sisteme giriş yapabilirsiniz:

**E-posta Adresiniz (Kullanıcı Adı):** {{ $userEmail }}
**Geçici Şifreniz:** {{ $temporaryPassword }}

Lütfen sisteme ilk girişinizde güvenliğiniz için bu geçici şifreyi değiştiriniz.

<x-mail::button :url="$loginUrl">
Sisteme Giriş Yap
</x-mail::button>

Herhangi bir sorunuz olursa bizimle iletişime geçmekten çekinmeyin.

Teşekkürler,
{{ config('app.name') }}
</x-mail::message>
