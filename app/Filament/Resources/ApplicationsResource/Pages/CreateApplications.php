<?php

namespace App\Filament\Resources\ApplicationsResource\Pages;

use App\Filament\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserWelcomeEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateApplications extends CreateRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // 1. Güvenli bir geçici şifre oluştur
        $temporaryPassword = Str::random(16); // Daha güvenli bir şifre

        $userImageToStore = null;
        if (!empty($data['image'])) {
            // $data['image'] bir string (dosya yolu) veya UploadedFile nesnesi olabilir.
            // Filament FileUpload genellikle bir string (geçici dosya yolu) olarak iletir.
            // Bu dosyayı kalıcı bir yere taşıyıp yolunu almamız gerekiyor.
            // ApplicationsResource'daki FileUpload ->directory('applications/images') olarak ayarlı.
            // Kullanıcı için farklı bir yol kullanabiliriz: 'profile-photos'
            $originalPath = $data['image']; // Veya 'applications/images/' . $data['image'] eğer sadece dosya adıysa
            
            if (Storage::disk('public')->exists($originalPath)) {
                $filename = basename($originalPath);
                $newPath = 'profile-photos/' . $filename; // Veya uniqid() . '.' . pathinfo($filename, PATHINFO_EXTENSION) gibi yeni bir isim
                Storage::disk('public')->copy($originalPath, $newPath);
                $userImageToStore = $newPath; // Bu, storage/app/public/profile-photos/filename.ext olur
            }
            
            // $data['image'] zaten bir yol string'i ise ve Applications modeli bunu kendi kaydediyorsa,
            // ve bu yol User için de kullanılacaksa, doğrudan $data['image'] kullanılabilir.
            // Ancak genellikle FileUpload, yükleme işlemini yaptıktan sonra asıl dosya yolunu
            // component'in state'inde tutar ve bu $data['image'] içinde gelir.
            
            // Eğer $data['image'] sadece dosya adıysa ve diskte 'applications/images' altındaysa:
            // $sourcePath = 'applications/images/' . $data['image'];
            // $destinationPath = 'profile-photos/' . $data['image']; // Veya yeni bir ad
            // if (Storage::disk('public')->exists($sourcePath)) {
            //     Storage::disk('public')->copy($sourcePath, $destinationPath);
            //     $userImageToStore = $destinationPath;
            // }

            // Daha güvenli bir yaklaşım, eğer $data['image'] hala TemporaryUploadedFile ise veya
            // ApplicationsResource'daki FileUpload'ın state'inden gelen tam yol ise:
            // Bu kısım ApplicationsResource'daki FileUpload'ın nasıl yapılandırıldığına bağlı.
            // Şimdilik, $data['image']'in zaten applications/images/ altında kalıcı bir yol olduğunu varsayalım
            // ve bunu User için de kullanabileceğimizi düşünelim.
            // Ancak bu ideal değil, çünkü bir başvuru silinirse kullanıcının fotoğrafı da gidebilir.
            // İdeal olanı, kullanıcı için ayrı bir kopya oluşturmaktır.

            // Basit bir yaklaşımla, $data['image'] başvuru formundan gelen dosya adı/yolu ise:
             $userImageToStore = $data['image']; // Bu satırı direkt kullanmadan önce $data['image']'in ne içerdiğini dd($data['image']) ile kontrol edin.
                                              // Eğer bu sadece bir dosya adıysa, başına 'applications/images/' eklemeniz gerekebilir.
                                              // Veya ApplicationsResource ->image() ->store() ile saklıyorsa, o yolu kullanın.
        }

        // 2. Yeni Kullanıcı için verileri hazırla
        $userData = [
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            
            // Eksik olduğu belirtilen ve eklenecek alanlar:
            'birth_date' => $data['birth_date'] ?? null,
            'birth_place' => $data['birth_place'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'physical_disability' => $data['physical_disability'] ?? null, // Bu alan User modelinde olmayabilir, kontrol edin
            'image' => $userImageToStore, // Kullanıcının fotoğraf yolu

            'password' => Hash::make($temporaryPassword),
            'email_verified_at' => now(), // E-postayı şimdilik doğrulanmış kabul edebiliriz veya bu adımı e-posta ile birleştirebiliriz
            'is_admin' => false, 
            'must_change_password' => true, // Kullanıcının ilk girişte şifresini değiştirmesi gerektiğini işaretle
        ];

        // Kullanıcıyı oluştur
        $user = User::create($userData);
        
        

        // 3. Kullanıcıya hoş geldin e-postası ve geçici şifreyi gönder
        try {
            Mail::to($user->email)->send(new NewUserWelcomeEmail($user, $temporaryPassword));
        } catch (\Exception $e) {
            Log::error('Yeni kullanıcı e-postası gönderilemedi: ' . $e->getMessage(), ['user_id' => $user->id, 'email' => $user->email]);
            Notification::make()
                ->title('E-posta Gönderim Hatası')
                ->body("Yeni kullanıcı {$user->email} için hoş geldin e-postası gönderilemedi. Lütfen şifreyi manuel olarak iletin ve durumu kontrol edin.")
                ->warning()
                ->sendToDatabase(auth()->user()); // Sadece mevcut admin için veya belirli adminlere
        }

        // 4. Başvuru için verileri hazırla
        $applicationData = $data;
        $applicationData['user_id'] = $user->id;

        $applicationData['application_date'] = $applicationData['application_date'] ?? now()->format('Y-m-d');
        $applicationData['are_documents_approved'] = $applicationData['are_documents_approved'] ?? false;
        $applicationData['is_interview_completed'] = $applicationData['is_interview_completed'] ?? false;
        $applicationData['status'] = $applicationData['status'] ?? 'awaiting_evaluation';
        if (empty($applicationData['application_id'])) {
            $applicationData['application_id'] = uniqid('APP-', true);
        }

        // 5. Başvuru kaydını oluştur
        return parent::handleRecordCreation($applicationData);
    }

    protected static ?string $title = 'Yeni Başvuru';

    protected static ?string $breadcrumb = 'Başvurular';

    protected static ?string $breadcrumbParent = 'Yeni Başvuru';

    protected static ?string $createButtonIcon = 'heroicon-o-plus';

    protected static ?string $createButtonColor = 'success';

    protected static ?string $createButtonTooltip = 'Yeni Başvuru Oluştur';

    protected static ?string $createButtonAriaLabel = 'Yeni Başvuru Oluştur';

    protected static ?string $createButtonLabel = 'Yeni Başvuru';


    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Oluştur');
    }

    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Oluştur ve başka ekle');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('İptal');
    }

    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Kaydet');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Başvuru Oluşturuldu')
            ->success()
            ->send();
    }

}
