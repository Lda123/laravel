@extends('layouts.auth')

@section('title', 'DengueCare')
@section('content')
    <!-- Kiri -->
    <div class="w-1/2 bg-cover bg-center flex flex-col justify-center items-center text-center relative" 
         style="background-image: url('/images/bgawal.png'); background-color: #f5f5f5;">
        <img src="/images/Logobesar.png" alt="DengueCare Logo" 
             class="w-[70%] max-w-[300px] mb-[200px] animate-slide-left">
        <h1 class="text-2xl text-white mb-5 animate-fade-in">
            Selamat Datang di <br> ~
            <span class="font-bold text-white">DengueCare!</span>
        </h1>
        <p class="text-base text-white animate-fade-in">Platform inovatif untuk mengingkatkan</p>
        <p class="text-base text-white animate-fade-in">kesadaran dan informasi mengenai DBD</p>
        <a href="#" class="mt-4 text-white font-bold no-underline animate-fade-in hover:underline">
            Pelajari lebih lanjut
        </a>
    </div>

    <!-- Kanan -->
    <div class="w-1/2 bg-white flex flex-col justify-center items-center text-center p-12">
        <img src="/images/Logokecil.png" alt="DengueCare2 Logo" 
             class="w-[200px] mb-[60px] animate-slide-right">
        <h2 class="text-xl text-[#1D3557] mb-8 animate-fade-in">Masuk sebagai</h2>
         <div class="w-[80%] max-w-[400px]">
            <a href="{{ route('warga.login') }}" class="btn-hover-effect w-full py-3 px-4 my-3 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer animate-fade-in block">
                Warga
            </a>
        </div>
     
        <p class="text-base text-[#858585] animate-fade-in">atau</p>
        
        <div class="w-[80%] max-w-[400px]">
            <a href="{{ route('kader.login') }}" class="btn-hover-effect w-full py-3 px-4 my-3 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer animate-fade-in block">
                Kader Kesehatan
            </a>
        </div>
    </div>
@endsection