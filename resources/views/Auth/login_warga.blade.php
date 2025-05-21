@extends('layouts.auth')

@section('title', 'Login Warga')
@section('content')
    <!-- kiri -->
    <div class="w-1/2 bg-cover bg-center flex flex-col justify-center items-center text-center relative" 
         style="background-image: url('/images/bgawal.png'); background-color: #f5f5f5;">
        <img src="/images/Logobesar.png" alt="DengueCare Logo" 
             class="w-[70%] max-w-[300px] mb-[200px] animate-slide-left">
        <h1 class="text-2xl text-white mb-5 animate-fade-in">
            Selamat Datang Warga Surabaya! <br> 
            <span class="font-bold text-white">Bersama Lawan DBD</span>
        </h1>
        <p class="text-base text-white animate-fade-in">Platform inovatif untuk meningkatkan kesadaran dan informasi mengenai DBD</p>
        <a href="#" class="mt-4 text-white font-bold no-underline animate-fade-in hover:underline">
            Pelajari lebih lanjut
        </a>
    </div>

    <!-- kanan -->
    <div class="w-1/2 bg-white flex flex-col justify-center items-center text-center p-12">
        <img src="/images/Logokecil.png" alt="DengueCare Logo" 
             class="w-[200px] mb-[60px] animate-slide-right">
        <h2 class="text-xl text-[#1D3557] mb-8 animate-fade-in">Masuk ke Halaman Utama</h2>
        
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg animate-fade-in w-[80%] max-w-[400px]">
                {{ $errors->first() }}
            </div>
        @endif
        
        <form action="{{ route('warga.login.submit') }}" method="post" class="w-[80%] max-w-[400px] animate-fade-in">
            @csrf
            <input type="text" name="telepon" placeholder="Masukkan Nomor Telepon" required
                   class="input-focus-effect w-full py-3 px-4 my-3 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
                   value="{{ old('telepon') }}">
                   
            <input type="password" name="password" placeholder="Masukkan Kata Sandi" required
                   class="input-focus-effect w-full py-3 px-4 my-3 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
                   
            <button type="submit" class="btn-hover-effect w-full py-3 px-4 my-3 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer">
                Masuk
            </button>
        </form>

        <p class="text-base text-[#858585] mt-4 animate-fade-in">
            Belum memiliki akun? 
            <a href="{{ route('warga.register') }}" class="text-[#226BD2] hover:underline">Daftar Sekarang</a>
        </p>
    </div>
@endsection