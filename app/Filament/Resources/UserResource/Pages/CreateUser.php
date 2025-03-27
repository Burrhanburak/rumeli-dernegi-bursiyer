<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Yeni Kullanıcı';

    protected static ?string $breadcrumb = 'Kullanıcılar';

    protected static ?string $breadcrumbParent = 'Kullanıcılar';

}
