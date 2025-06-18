@extends('layouts.warga')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <h1 class="text-3xl font-bold text-[#1D3557]">Laporan Warga</h1>
        <div class="flex items-center space-x-2 mt-4 md:mt-0">
            <span class="text-sm text-gray-600">Tanggal Hari Ini:</span>
            <span class="font-medium text-gray-800">{{ date('d F Y') }}</span>
        </div>
    </div>

    <!-- Form Laporan -->
    <form id="reportForm" action="{{ route('warga.laporan.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg p-6 space-y-6">
        @csrf
        
        <!-- Jenis Laporan -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                <i class="fas fa-tag text-gray-500 mr-2 text-sm"></i>
                Jenis Laporan <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach(['Jentik Nyamuk', 'Kasus DBD', 'Lingkungan Kotor'] as $jenis)
                <div class="report-type-option rounded-lg p-4 text-center cursor-pointer border-2 border-gray-200 transition-all duration-300" 
                    data-value="{{ $jenis }}"
                    role="button" 
                    tabindex="0"
                    aria-label="Pilih {{ $jenis }}">
                    <div class="icon-container p-3 rounded-full inline-block mb-2 transition-all duration-300
                        @if($jenis == 'Jentik Nyamuk') bg-blue-100 text-blue-600
                        @elseif($jenis == 'Kasus DBD') bg-red-100 text-red-600
                        @else bg-yellow-100 text-yellow-600 @endif">
                        <i class="fas 
                            @if($jenis == 'Jentik Nyamuk') fa-bug
                            @elseif($jenis == 'Kasus DBD') fa-exclamation-triangle
                            @else fa-trash @endif"></i>
                    </div>
                    <h3 class="font-medium text-gray-800">{{ $jenis }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($jenis == 'Jentik Nyamuk') Temuan jentik nyamuk di lingkungan
                        @elseif($jenis == 'Kasus DBD') Laporan kasus DBD di sekitar
                        @else Tempat berpotensi sarang nyamuk @endif
                    </p>
                </div>
                @endforeach
            </div>
            
            <input type="hidden" name="jenis_laporan" id="jenis_laporan" required value="{{ old('jenis_laporan') }}">
            
            @error('jenis_laporan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Lokasi Kejadian -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Kecamatan -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                    <i class="fas fa-map-marker-alt text-gray-500 mr-2 text-sm"></i>
                    Kecamatan <span class="text-red-500">*</span>
                </label>
                <select name="kecamatan_id" id="kecamatan" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                            {{ $kecamatan->nama_kecamatan }}
                        </option>
                    @endforeach
                </select>
                @error('kecamatan_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Kelurahan -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                    <i class="fas fa-map-marker-alt text-gray-500 mr-2 text-sm"></i>
                    Kelurahan <span class="text-red-500">*</span>
                </label>
                <select name="kelurahan_id" id="kelurahan" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        {{ !old('kecamatan_id') ? 'disabled' : '' }}>
                    <option value="">Pilih Kecamatan terlebih dahulu</option>
                    @if(old('kecamatan_id'))
                        @foreach(\App\Models\Kelurahan::where('kecamatan_id', old('kecamatan_id'))->get() as $kelurahan)
                            <option value="{{ $kelurahan->id }}" {{ old('kelurahan_id') == $kelurahan->id ? 'selected' : '' }}>
                                {{ $kelurahan->nama_kelurahan }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('kelurahan_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- RW -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                    <i class="fas fa-map-marker-alt text-gray-500 mr-2 text-sm"></i>
                    RW <span class="text-red-500">*</span>
                </label>
                <select name="rw_id" id="rw" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        {{ !old('kelurahan_id') ? 'disabled' : '' }}>
                    <option value="">Pilih Kelurahan terlebih dahulu</option>
                    @if(old('kelurahan_id'))
                        @foreach(\App\Models\Rw::where('kelurahan_id', old('kelurahan_id'))->get() as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                                RW {{ $rw->nomor_rw }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('rw_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- RT -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                    <i class="fas fa-map-marker-alt text-gray-500 mr-2 text-sm"></i>
                    RT <span class="text-red-500">*</span>
                </label>
                <select name="rt_id" id="rt" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        {{ !old('rw_id') ? 'disabled' : '' }}>
                    <option value="">Pilih RW terlebih dahulu</option>
                    @if(old('rw_id'))
                        @foreach(\App\Models\Rt::where('rw_id', old('rw_id'))->get() as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                                RT {{ $rt->nomor_rt }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('rt_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Alamat Detail -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                <i class="fas fa-map-pin text-gray-500 mr-2 text-sm"></i>
                Alamat Detail <span class="text-red-500">*</span>
            </label>
            <input type="text" name="alamat_detail" required 
                   placeholder="Contoh: Jl. Merdeka No. 10, depan warung Bu Siti" 
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                   value="{{ old('alamat_detail') }}">
            @error('alamat_detail')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Deskripsi -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                <i class="fas fa-align-left text-gray-500 mr-2 text-sm"></i>
                Deskripsi Lengkap <span class="text-red-500">*</span>
            </label>
            <textarea name="deskripsi" required placeholder="Jelaskan secara detail apa yang terjadi..." 
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm h-32">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Upload Foto -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                <i class="fas fa-camera text-gray-500 mr-2 text-sm"></i>
                Unggah Foto Bukti
            </label>
            <input type="file" name="foto_pelaporan" id="foto_pelaporan" 
                   class="block w-full text-sm text-gray-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-lg file:border-0
                          file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-700
                          hover:file:bg-blue-100"
                   accept="image/jpeg,image/png,image/jpg,image/webp">
            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, atau WEBP. Maksimal 2MB.</p>
            
            <div id="imagePreviewContainer" class="mt-4 hidden">
                <div class="relative">
                    <img id="imagePreview" src="#" alt="Preview Gambar" class="max-w-full h-auto rounded-lg border">
                    <button type="button" id="removeImage" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
            @error('foto_pelaporan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Submit -->
        <div class="flex justify-between pt-3">
            <a href="{{ route('warga.dashboard') }}" 
               class="inline-flex items-center px-5 py-2.5 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim Laporan
            </button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.report-type-option').click(function() {
        // Remove active class from all options
        $('.report-type-option').removeClass('border-blue-500 bg-blue-50');
        
        // Add active class to selected option
        $(this).addClass('border-blue-500 bg-blue-50');
        
        // Set the hidden input value
        const selectedValue = $(this).data('value');
        $('#jenis_laporan').val(selectedValue);
    });

    // Set initial selection if there's old input
    const oldJenisLaporan = "{{ old('jenis_laporan') }}";
    if (oldJenisLaporan) {
        $(`.report-type-option[data-value="${oldJenisLaporan}"]`).addClass('border-blue-500 bg-blue-50');
    }

    // Fungsi untuk reset dropdown yang tergantung
    function resetDependentDropdowns(currentLevel) {
        const levels = ['kecamatan', 'kelurahan', 'rw', 'rt'];
        const currentIndex = levels.indexOf(currentLevel);
        
        // Reset semua dropdown setelah current level
        for (let i = currentIndex + 1; i < levels.length; i++) {
            const dropdown = levels[i];
            $('#' + dropdown).html('<option value="">Pilih ' + dropdown.toUpperCase() + ' terlebih dahulu</option>').prop('disabled', true);
        }
    }

    // Fungsi untuk toggle dropdown dan loading
    function toggleDropdown(dropdownId, enable, loadingId = null) {
        const $dropdown = $('#' + dropdownId);
        $dropdown.prop('disabled', !enable);
        
        if (loadingId) {
            if (enable) {
                $('#' + loadingId).addClass('hidden');
            } else {
                $('#' + loadingId).removeClass('hidden');
            }
        }
    }

    // Kecamatan change handler
    $('#kecamatan').change(function() {
        const kecamatan_id = $(this).val();
        resetDependentDropdowns('kecamatan');

        if (kecamatan_id) {
            toggleDropdown('kelurahan', false);
            
            // Tambahkan loading indicator
            $('#kelurahan').html('<option value="">Memuat data...</option>');

            $.ajax({
                url: "{{ route('warga.laporan.get.kelurahan') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    kecamatan_id: kecamatan_id
                },
                success: function(data) {
                    if (data.options) {
                        $('#kelurahan').html(data.options);
                        toggleDropdown('kelurahan', true);
                    } else {
                        $('#kelurahan').html('<option value="">Data tidak ditemukan</option>');
                        toggleDropdown('kelurahan', false);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#kelurahan').html('<option value="">Gagal memuat data</option>');
                    toggleDropdown('kelurahan', false);
                }
            });
        }
    });

    // Kelurahan change handler
    $('#kelurahan').change(function() {
        const kelurahan_id = $(this).val();
        resetDependentDropdowns('kelurahan');

        if (kelurahan_id) {
            toggleDropdown('rw', false);
            
            // Tambahkan loading indicator
            $('#rw').html('<option value="">Memuat data...</option>');

            $.ajax({
                url: "{{ route('warga.laporan.get.rw') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    kelurahan_id: kelurahan_id
                },
                success: function(data) {
                    if (data.options) {
                        $('#rw').html(data.options);
                        toggleDropdown('rw', true);
                    } else {
                        $('#rw').html('<option value="">Data tidak ditemukan</option>');
                        toggleDropdown('rw', false);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#rw').html('<option value="">Gagal memuat data</option>');
                    toggleDropdown('rw', false);
                }
            });
        }
    });

    // RW change handler
    $('#rw').change(function() {
        const rw_id = $(this).val();
        resetDependentDropdowns('rw');

        if (rw_id) {
            toggleDropdown('rt', false);
            
            // Tambahkan loading indicator
            $('#rt').html('<option value="">Memuat data...</option>');

            $.ajax({
                url: "{{ route('warga.laporan.get.rt') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    rw_id: rw_id
                },
                success: function(data) {
                    if (data.options) {
                        $('#rt').html(data.options);
                        toggleDropdown('rt', true);
                    } else {
                        $('#rt').html('<option value="">Data tidak ditemukan</option>');
                        toggleDropdown('rt', false);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#rt').html('<option value="">Gagal memuat data</option>');
                    toggleDropdown('rt', false);
                }
            });
        }
    });

    // Preview image before upload
    $('#foto_pelaporan').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreviewContainer').removeClass('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    // Remove image
    $('#removeImage').click(function() {
        $('#foto_pelaporan').val('');
        $('#imagePreviewContainer').addClass('hidden');
    });
    // Inisialisasi dropdown jika ada data old
    var oldKecamatanId = "{{ old('kecamatan_id') }}";
    if (oldKecamatanId) {
        $('#kecamatan').val(oldKecamatanId).trigger('change');
        
        // Gunakan promise untuk menangani async chain
        setTimeout(function() {
            var oldKelurahanId = "{{ old('kelurahan_id') }}";
            if (oldKelurahanId) {
                $('#kelurahan').val(oldKelurahanId).trigger('change');
                
                setTimeout(function() {
                    var oldRwId = "{{ old('rw_id') }}";
                    if (oldRwId) {
                        $('#rw').val(oldRwId).trigger('change');
                        
                        setTimeout(function() {
                            var oldRtId = "{{ old('rt_id') }}";
                            if (oldRtId) {
                                $('#rt').val(oldRtId);
                            }
                        }, 300);
                    }
                }, 300);
            }
        }, 300);
    }
});
</script>

@endsection