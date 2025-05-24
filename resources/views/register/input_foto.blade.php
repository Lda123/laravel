@extends('layouts.auth')

@section('title', 'Upload Foto KTP dan Selfie')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-xl font-semibold text-center text-blue-700 mb-6">Upload Foto KTP & Diri dengan KTP</h2>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('warga.foto.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label class="block mb-2 font-medium">Foto KTP</label>
            <input type="file" name="ktp" accept="image/*" required class="w-full border rounded p-2">
            @error('ktp') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-2 font-medium">Foto Diri dengan KTP</label>
            <input type="file" name="foto_diri" accept="image/*" required class="w-full border rounded p-2">
            @error('foto_diri') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Simpan dan Selesaikan
        </button>
    </form>
</div>
@endsection
