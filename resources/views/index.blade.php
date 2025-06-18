@extends('layouts.auth')
@section('title','Welcome')
@section('content')
<div class="w-1/2 bg-cover bg-center flex flex-col justify-center items-center text-center relative" 
    style="background-image: url('/images/bgawal.png'); background-color: #f5f5f5;">
        <img src="{{ asset('images/Logobesar.png') }}" alt="DengueCare Logo" 
            class="w-[70%] max-w-[300px] mb-[200px] animate-slide-left">
        <h1 class="text-2xl text-white mb-5 animate-fade-in">
            Selamat Datang di <br> 
            <span class="font-bold text-white">DengueCare!</span>
        </h1>
        <p class="text-base text-white animate-fade-in">Platform inovatif untuk mengingkatkan</p>
        <p class="text-base text-white animate-fade-in">kesadaran dan informasi mengenai DBD</p>
        <a href="#" class="mt-4 text-white font-bold no-underline animate-fade-in hover:underline">
            Pelajari lebih lanjut
        </a>
    </div>

    <!-- Right Side -->
    <div class="w-1/2 bg-white flex flex-col justify-center items-center text-center p-12">
        <img src="{{ asset('images/Logokecil.png') }}" alt="DengueCare2 Logo" 
            class="w-[200px] mb-[60px] animate-slide-right">
        <h2 class="text-xl text-[#1D3557] mb-8 animate-fade-in">Masuk sebagai</h2>
        
        <div class="w-[80%] max-w-[400px] space-y-4">
            <!-- Button Warga -->
            <a href="{{ route('warga.login') }}" class="btn-hover-effect w-full py-3 px-4 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer animate-fade-in flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                Warga
            </a>
            
            <!-- Button Kader -->
            <a href="{{ route('kader.login') }}" class="btn-hover-effect w-full py-3 px-4 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer animate-fade-in flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z" />
                </svg>
                Kader Kesehatan
            </a>
        </div>
        
        <p class="text-base text-[#858585] mt-4 animate-fade-in">Pilih peran Anda untuk melanjutkan</p>
    </div>
@endsection