@extends('layouts.auth')
@section('title', 'Input Data Diri')
@section('content')
<div class="flex h-screen w-full font-sans">
    {{-- Kiri --}}
    <div class="w-1/2 bg-cover bg-center flex flex-col justify-center items-center text-center" 
        style="background-image: url('/images/bgawal.png'); background-color: #f5f5f5;">
        <img src="/images/Logobesar.png" class="w-[70%] max-w-[300px] mb-[200px] animate-slide-left">
        <h1 class="text-2xl text-white mb-5 animate-fade-in">
            Halo Warga Surabaya! <br><span class="font-bold">Ayo Peduli DBD</span>
        </h1>
        <p class="text-base text-white animate-fade-in">Platform inovatif untuk meningkatkan kesadaran dan informasi mengenai DBD</p>
    </div>

    {{-- Kanan --}}
    <div class="w-1/2 bg-white flex flex-col justify-center items-center text-center p-12 overflow-y-auto">
        <img src="/images/Logokecil.png" class="w-[200px] mb-[30px] mt-[200px] animate-slide-right">
        <h2 class="text-xl text-[#1D3557] mb-8 animate-fade-in">Input Data Diri</h2>

        <form method="POST" action="{{ route('register.data_diri.submit') }}" class="w-[80%] max-w-[400px] space-y-4 animate-fade-in">
    @csrf
    
    <!-- NIK -->
    <input type="text" name="nik" placeholder="NIK" required
           class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
           value="{{ old('nik', session('nik')) }}">
    
    <!-- Nama Lengkap -->
    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required
           class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
           value="{{ old('nama_lengkap', session('nama_lengkap')) }}">
    
    <!-- Tempat Lahir -->
    <input type="text" name="tempat_lahir" placeholder="Tempat Lahir" required
           class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
           value="{{ old('tempat_lahir', session('tempat_lahir')) }}">
    
    <!-- Tanggal Lahir -->
    <input type="date" name="tanggal_lahir" required
           class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
           value="{{ old('tanggal_lahir', session('tanggal_lahir')) }}">
    
    <!-- Jenis Kelamin -->
    <select name="jenis_kelamin" required
            class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
        <option value="">Pilih Jenis Kelamin</option>
        <option value="Laki-laki" {{ old('jenis_kelamin', session('jenis_kelamin')) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
        <option value="Perempuan" {{ old('jenis_kelamin', session('jenis_kelamin')) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
    </select>
    
    <!-- Alamat Lengkap -->
    <input type="text" name="alamat_lengkap" placeholder="Alamat Lengkap" required
           class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none"
           value="{{ old('alamat_lengkap', session('alamat_lengkap')) }}">
    <!-- Kecamatan -->
<select id="kecamatan" name="kecamatan_id" required
        class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
    <option value="">Pilih Kecamatan</option>
    @foreach($kecamatan as $kec)
        <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
    @endforeach
</select>

<!-- Kelurahan -->
<select id="kelurahan" name="kelurahan_id" required disabled
        class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
    <option value="">Pilih Kelurahan</option>
</select>

<!-- RW -->
<select id="rw" name="rw_id" required disabled
        class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
    <option value="">Pilih RW</option>
</select>

<!-- RT -->
<select id="rt" name="rt_id" required disabled
        class="input-focus-effect w-full py-3 px-4 text-base border-2 border-gray-300 rounded-lg transition-all duration-300 focus:outline-none">
    <option value="">Pilih RT</option>
</select>


    
    <button type="submit" class="btn-hover-effect w-full py-3 mt-6 bg-[#226BD2] text-white rounded-lg transition-all duration-300 hover:bg-[#1D3557]">
        Lanjut
    </button>
</form>
    </div>
</div>
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {
        // Ketika kecamatan berubah
        $('#kecamatan').change(function () {
            const kecamatanId = $(this).val();
            $('#kelurahan').prop('disabled', true).html('<option value="">Memuat...</option>');
            $('#rw').prop('disabled', true).html('<option value="">Pilih RW</option>');
            $('#rt').prop('disabled', true).html('<option value="">Pilih RT</option>');

            if (kecamatanId) {
                $.post('/ajax/get-kelurahan', {
                    _token: '{{ csrf_token() }}',
                    kecamatan_id: kecamatanId
                }, function (response) {
                    $('#kelurahan').html(response.options).prop('disabled', false);
                });
            }
        });

        // Ketika kelurahan berubah
        $('#kelurahan').change(function () {
            const kelurahanId = $(this).val();
            $('#rw').prop('disabled', true).html('<option value="">Memuat...</option>');
            $('#rt').prop('disabled', true).html('<option value="">Pilih RT</option>');

            if (kelurahanId) {
                $.post('/ajax/get-rw', {
                    _token: '{{ csrf_token() }}',
                    kelurahan_id: kelurahanId
                }, function (response) {
                    $('#rw').html(response.options).prop('disabled', false);
                });
            }
        });

        // Ketika rw berubah
        $('#rw').change(function () {
            const rwId = $(this).val();
            $('#rt').prop('disabled', true).html('<option value="">Memuat...</option>');

            if (rwId) {
                $.post('/ajax/get-rt', {
                    _token: '{{ csrf_token() }}',
                    rw_id: rwId
                }, function (response) {
                    $('#rt').html(response.options).prop('disabled', false);
                });
            }
        });
    });
</script>
@endpush
