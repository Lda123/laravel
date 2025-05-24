@extends('layouts.auth') {{-- Atau ganti sesuai layout --}}
@section('content')

<div class="flex h-screen w-full font-sans">
    <!-- Kiri -->
    <div class="w-1/2 bg-cover bg-center flex flex-col justify-center items-center text-center relative" 
         style="background-image: url('/images/bgawal.png'); background-color: #f5f5f5;">
        <img src="{{ asset('/images/Logobesar.png') }}" alt="DengueCare Logo"
            class="w-[70%] max-w-[300px] mb-[200px] animate-slide-left">
        <h1 class="text-2xl text-white mb-5 animate-fade-in">
            Halo Warga Surabaya! <br>
            <span class="font-bold text-white">Ayo Peduli DBD</span>
        </h1>
        <p class="text-base text-white animate-fade-in">Platform inovatif untuk meningkatkan kesadaran dan informasi mengenai DBD</p>
    </div>

    <!-- Kanan -->
    <div class="w-1/2 bg-white flex flex-col justify-center items-center text-center p-12">
        <img src="{{ asset('/images/Logokecil.png') }}" alt="DengueCare Logo"
            class="w-[200px] mb-[60px] animate-slide-right">
        <h2 class="text-xl text-[#1D3557] mb-4 animate-fade-in">Verifikasi Kode OTP</h2>
        <p class="text-base text-gray-600 mb-8 animate-fade-in">Kode telah dikirim ke WhatsApp Anda!</p>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg animate-fade-in w-[80%] max-w-[400px]">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ route('register.otp.submit') }}" method="POST" class="w-[80%] max-w-[400px] animate-fade-in">
    @csrf
    <input type="text" name="otp" required
        class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
        placeholder="Masukkan 5 digit kode OTP">
    <button type="submit"
        class="btn-hover-effect w-full py-3 px-4 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer mt-4">
        Verifikasi
    </button>
</form>


        <p class="text-base text-[#858585] mt-6 animate-fade-in">
            Tidak menerima kode?
            <a href="#" class="text-[#226BD2] hover:underline">Kirim Lagi</a>
        </p>
    </div>
</div>

<!-- Popup OTP untuk testing -->
<div id="otpPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full mx-4 otp-popup">
        <h3 class="text-xl font-bold text-[#1D3557] mb-4">Kode Verifikasi Anda</h3>
        <p class="text-gray-600 mb-6">Silakan gunakan kode berikut untuk verifikasi:</p>
        <div class="bg-blue-50 p-4 rounded-lg mb-6">
            <p class="text-2xl font-bold text-center text-[#226BD2]">{{ session('kode_otp') }}</p>
        </div>
        <button onclick="document.getElementById('otpPopup').classList.add('hidden')"
            class="btn-hover-effect w-full py-2 px-4 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer">
            Tutup
        </button>
    </div>
</div>

<script>
    // Tampilkan popup OTP otomatis
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            document.getElementById('otpPopup').classList.remove('hidden');
        }, 500);
    });
</script>

@endsection
