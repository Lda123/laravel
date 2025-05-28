{{-- resources/views/auth/input_foto.blade.php --}}
@extends('layouts.auth')

@section('title', 'Unggah KTP - DengueCare')

@section('content')
<div class="flex h-screen w-full font-sans">
    <!-- Left Section -->
    <div class="w-1/2 bg-cover bg-center flex flex-col justify-center items-center text-center relative" 
        style="background-image: url('/images/bgawal.png'); background-color: #f5f5f5;">
        <img src="{{ asset('/images/Logobesar.png') }}" alt="DengueCare Logo" 
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

    <!-- Right Section -->
    <div class="w-1/2 bg-white flex flex-col justify-center items-center text-center p-12 overflow-y-auto">
        <img src="{{ asset('/images/Logokecil.png') }}" alt="DengueCare Logo" 
             class="w-[200px] mt-[100px] mb-[30px] animate-slide-right">
        <h2 class="text-xl text-[#1D3557] mb-4 animate-fade-in">
             Foto KTP & KK</h2>
        <p class="text-base text-gray-600 mb-8 animate-fade-in">Silakan unggah foto KTP dan KK Anda dengan jelas dan sesuai contoh di bawah:</p>
        
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg animate-fade-in w-[80%] max-w-[400px]">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="flex space-x-4 mb-8 animate-fade-in">
            <div class="border-2 border-gray-200 rounded-lg p-2">
                <img src="{{ asset('/images/contoh_ktp.png') }}" alt="Contoh KTP" class="h-40 object-contain" />
                <p class="text-sm text-gray-600 mt-2">Contoh Foto KTP</p>
            </div>
            <div class="border-2 border-gray-200 rounded-lg p-2">
                <img src="{{ asset('/images/contoh_diri.png') }}" alt="Contoh KK" class="h-40 object-contain" />
                <p class="text-sm text-gray-600 mt-2">Contoh Foto Diri dengan KTP</p>
            </div>
        </div>
        
        <form action="{{ route('register.upload-foto.store') }}" method="POST" enctype="multipart/form-data" class="w-[80%] max-w-[400px] animate-fade-in space-y-6">
            @csrf
            
            <div class="file-upload">
                <label for="ktp-upload" class="cursor-pointer bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-lg border-2 border-dashed border-gray-300 w-full block transition-all duration-300">
                    <div class="flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span class="font-medium">Pilih Foto KTP</span>
                        <span id="ktp-preview" class="text-sm text-gray-500 mt-1">Belum ada file yang dipilih</span>
                    </div>
                </label>
                <input type="file" id="ktp-upload" name="ktp" accept="image/*" required class="hidden" onchange="previewFile('ktp-upload', 'ktp-preview')" />
            </div>
            
            <div class="file-upload">
                <label for="diri-upload" class="cursor-pointer bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-lg border-2 border-dashed border-gray-300 w-full block transition-all duration-300">
                    <div class="flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span class="font-medium">Pilih Foto Diri dengan KTP</span>
                        <span id="diri-preview" class="text-sm text-gray-500 mt-1">Belum ada file yang dipilih</span>
                    </div>
                </label>
                <input type="file" id="diri-upload" name="foto_diri" accept="image/*" required class="hidden" onchange="previewFile('diri-upload', 'diri-preview')" />
            </div>
            
            <button type="submit" class="btn-hover-effect w-full py-3 px-4 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer">
                Simpan
            </button>
        </form>
    </div>
</div>

<script>
    function previewFile(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const file = input.files[0];

        if (file) {
            preview.textContent = "File dipilih: " + file.name;
            preview.classList.remove('text-gray-500');
            preview.classList.add('text-green-600', 'font-medium');
        } else {
            preview.textContent = "Belum ada file yang dipilih";
            preview.classList.remove('text-green-600', 'font-medium');
            preview.classList.add('text-gray-500');
        }
    }
</script>
@endsection