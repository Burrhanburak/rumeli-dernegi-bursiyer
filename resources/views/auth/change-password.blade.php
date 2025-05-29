@extends('layouts.app') {{-- Varsa ana layout'unuzu kullanın, yoksa basit bir HTML yapısı --}}

@section('content')
<div class="container mx-auto mt-10">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl font-bold text-center mb-6">Şifrenizi Güncelleyin</h1>

            @if (session('warning'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p>{{ session('warning') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Yeni Şifre
                    </label>
                    <input class="shadow appearance-none border @error('password') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" name="password" required autocomplete="new-password" placeholder="Yeni şifreniz">
                    @error('password')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password-confirm">
                        Yeni Şifre (Tekrar)
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Yeni şifrenizi tekrar girin">
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Şifreyi Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 