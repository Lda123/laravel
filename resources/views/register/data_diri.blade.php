{{-- resources/views/auth/inputdatadiri.blade.php --}}
@extends('layouts.auth')

@section('title', 'Input Data Diri')

@section('content')
<div class="flex h-screen w-full font-sans">
    <!-- Left Section -->
    <div class="w-1/2 bg-cover bg-center flex flex-col justify-center items-center text-center relative" 
        style="background-image: url('/images/bgawal.png'); background-color: #f5f5f5;">
        <img src="{{ asset('/images/Logobesar.png') }}" alt="DengueCare Logo" 
             class="w-[70%] max-w-[300px] mb-[200px] animate-slide-left">
        <h1 class="text-2xl text-white mb-5 animate-fade-in">
            Halo Warga Surabaya! <br> 
            <span class="font-bold text-white">Ayo Peduli DBD</span>
        </h1>
        <p class="text-base text-white animate-fade-in">Platform inovatif untuk meningkatkan kesadaran dan informasi mengenai DBD</p>
        <a href="#" class="mt-4 text-white font-bold no-underline animate-fade-in hover:underline">
            Pelajari lebih lanjut
        </a>
    </div>

    <!-- Right Section -->
    <div class="w-1/2 bg-white flex flex-col justify-center items-center text-center p-12 overflow-y-auto">
        <img src="{{ asset('/images/Logokecil.png') }}" alt="DengueCare Logo" 
             class="w-[200px] mb-[30px] mt-[200px] animate-slide-right">
        <h2 class="text-xl text-[#1D3557] mb-8 animate-fade-in">Input Data Diri</h2>
        
        <form method="POST" action="{{ route('register.datadiri.store') }}" class="w-[80%] max-w-[400px] animate-fade-in space-y-4">
            @csrf
            
            <input type="text" name="nik" placeholder="NIK" required
                   class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
                   value="{{ old('nik', session('nik')) }}">
                   
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required
                   class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
                   value="{{ old('nama_lengkap', session('nama_lengkap')) }}">
                   
            <input type="text" name="tempat_lahir" placeholder="Tempat Lahir" required
                   class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
                   value="{{ old('tempat_lahir', session('tempat_lahir')) }}">
                   
            <input type="date" name="tanggal_lahir" required
                   class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
                   value="{{ old('tanggal_lahir', session('tanggal_lahir')) }}"
                   max="{{ \Carbon\Carbon::now()->subYears(18)->format('Y-m-d') }}">
                   
            <select name="jenis_kelamin" required
                    class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
                <option value="">Pilih Jenis Kelamin</option>
                <option value="Laki-laki" {{ (old('jenis_kelamin', session('jenis_kelamin')) === 'Laki-laki') ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ (old('jenis_kelamin', session('jenis_kelamin')) === 'Perempuan') ? 'selected' : '' }}>Perempuan</option>
            </select>
            
            <input type="text" name="alamat_lengkap" placeholder="Alamat Lengkap" required
                   class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
                   value="{{ old('alamat_lengkap', session('alamat_lengkap')) }}">
                   
            <select id="kecamatan" name="kecamatan_id" required
                    class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
                <option value="">Pilih Kecamatan</option>
                @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                        {{ $kecamatan->nama_kecamatan }}
                    </option>
                @endforeach
            </select>
            
            <select id="kelurahan" name="kelurahan_id" required
                    class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
                <option value="">Pilih Kelurahan</option>
            </select>
            
            <select id="rw" name="rw_id" required
                    class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
                <option value="">Pilih RW</option>
            </select>
            
            <select id="rt" name="rt_id" required
                    class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
                <option value="">Pilih RT</option>
                @if(session('rt_id'))
                    <option value="{{ session('rt_id') }}" selected>RT {{ session('rt_id') }}</option>
                @endif
            </select>
            
            <button type="submit" class="btn-hover-effect w-full py-3 px-4 mt-6 text-base bg-[#226BD2] text-white border-none rounded-lg cursor-pointer">
                Lanjut
            </button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#kecamatan').change(function() {
        var kecamatan_id = $(this).val();
        if(kecamatan_id) {
            $.ajax({
                url: "{{ route('register.get.kelurahan') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    kecamatan_id: kecamatan_id
                },
                success: function(data) {
                    $('#kelurahan').html(data).prop('disabled', false);
                    $('#rw').html('<option value="">Pilih RW</option>').prop('disabled', true);
                    $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
                },
                error: function() {
                    alert('Gagal memuat data kelurahan');
                }
            });
        } else {
            $('#kelurahan, #rw, #rt').html('<option value="">Pilih terlebih dahulu</option>').prop('disabled', true);
        }
    });

    $('#kelurahan').change(function() {
        var kelurahan_id = $(this).val();
        if(kelurahan_id) {
            $.ajax({
                url: "{{ route('register.get.rw') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    kelurahan_id: kelurahan_id
                },
                success: function(data) {
                    $('#rw').html(data).prop('disabled', false);
                    $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
                },
                error: function() {
                    alert('Gagal memuat data RW');
                }
            });
        } else {
            $('#rw, #rt').html('<option value="">Pilih terlebih dahulu</option>').prop('disabled', true);
        }
    });

    $('#rw').change(function() {
        var rw_id = $(this).val();
        if(rw_id) {
            $.ajax({
                url: "{{ route('register.get.rt') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    rw_id: rw_id
                },
                success: function(data) {
                    $('#rt').html(data).prop('disabled', false);
                },
                error: function() {
                    alert('Gagal memuat data RT');
                }
            });
        } else {
            $('#rt').html('<option value="">Pilih terlebih dahulu</option>').prop('disabled', true);
        }
    });
});
</script>

@if(session('rt_id'))
    <script>
        $(document).ready(function() {
            // Initialize selects if there's session data
            $('#rt').prop('disabled', false);
        });
    </script>
@endif

@endsection