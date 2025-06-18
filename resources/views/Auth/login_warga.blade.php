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
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg animate-fade-in w-[80%] max-w-[400px] relative">
                <button type="button" class="absolute top-2 right-2 text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <ul class="list-disc list-inside pr-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('warga.login.submit') }}" method="post" class="w-[80%] max-w-[400px] animate-fade-in">
            @csrf
            <div class="mb-4">
                <div class="relative">
                    <input type="text" name="telepon" placeholder="Masukkan Nomor Telepon" required
                           class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
                           value="{{ old('telepon') }}">
                    @error('telepon')
                        <span class="text-red-500 text-sm absolute -bottom-5 left-0">{{ $message }}</span>
                    @enderror
                </div>
            </div>
                   
            <div class="mb-6">
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="Masukkan Kata Sandi" required
                           class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none pr-10">
                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                            onclick="togglePasswordVisibility()">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                        <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                        </svg>
                    </button>
                    @error('password')
                        <span class="text-red-500 text-sm absolute -bottom-5 left-0">{{ $message }}</span>
                    @enderror
                </div>
            </div>
                   
            <button type="submit" class="btn-hover-effect w-full py-3 px-4 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer transition-all duration-300 hover:bg-[#1a56b5]">
                Masuk
            </button>
        </form>

        <p class="text-base text-[#858585] mt-4 animate-fade-in">
            Belum memiliki akun? 
            <a href="{{ route('register.signup') }}" class="text-[#226BD2] hover:underline">Daftar Sekarang</a>
        </p>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
@endsection