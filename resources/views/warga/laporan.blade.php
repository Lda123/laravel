@extends('layouts.warga')

@section('title', 'Laporan Warga - DengueCare')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <h1 class="text-3xl font-bold text-[#1D3557]">Laporan Warga</h1>
        <div class="flex items-center space-x-2 mt-4 md:mt-0">
            <span class="text-sm text-gray-600">Tanggal Hari Ini:</span>
            <span class="font-medium text-gray-800">{{ date('d F Y') }}</span>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg p-6 border border-blue-100 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                        <i class="fas fa-exclamation-circle fa-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Total Laporan</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $laporans->total() }} Laporan
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                        <i class="fas fa-check-circle fa-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Laporan Diterima</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $laporans->where('status', 'Diterima')->count() }} Laporan
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                        <i class="fas fa-clock fa-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Dalam Proses</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $laporans->where('status', 'Diproses')->count() }} Laporan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Laporan Baru -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b border-gray-200 flex items-center">
            <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
            Buat Laporan Baru
        </h2>
        
        <form method="POST" action="{{ route('pelaporan.store') }}" enctype="multipart/form-data" class="space-y-5" id="reportForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Jenis Laporan -->
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-tag text-gray-500 mr-2 text-sm"></i>
                        Jenis Laporan <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="report-type rounded-lg p-4 text-center cursor-pointer" data-value="Jentik Nyamuk">
                            <div class="bg-blue-100 p-3 rounded-full inline-block mb-2">
                                <i class="fas fa-bug text-blue-600"></i>
                            </div>
                            <h3 class="font-medium text-gray-800">Jentik Nyamuk</h3>
                            <p class="text-sm text-gray-500 mt-1">Temuan jentik nyamuk di lingkungan</p>
                        </div>
                        
                        <div class="report-type rounded-lg p-4 text-center cursor-pointer" data-value="Kasus DBD">
                            <div class="bg-red-100 p-3 rounded-full inline-block mb-2">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <h3 class="font-medium text-gray-800">Kasus DBD</h3>
                            <p class="text-sm text-gray-500 mt-1">Laporan kasus DBD di sekitar</p>
                        </div>
                        
                        <div class="report-type rounded-lg p-4 text-center cursor-pointer" data-value="Lingkungan Kotor">
                            <div class="bg-yellow-100 p-3 rounded-full inline-block mb-2">
                                <i class="fas fa-trash text-yellow-600"></i>
                            </div>
                            <h3 class="font-medium text-gray-800">Lingkungan Kotor</h3>
                            <p class="text-sm text-gray-500 mt-1">Tempat berpotensi sarang nyamuk</p>
                        </div>
                    </div>
                    <input type="hidden" name="jenis_laporan" id="jenis_laporan" required>
                </div>

                <!-- Lokasi Kejadian -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-map-marker-alt text-gray-500 mr-2 text-sm"></i>
                        Kecamatan <span class="text-red-500">*</span>
                    </label>
                    <select name="kecamatan_id" id="kecamatan" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Pilih Kecamatan</option>
                        @foreach($kecamatans as $kecamatan)
                            <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-map-marked-alt text-gray-500 mr-2 text-sm"></i>
                        Kelurahan <span class="text-red-500">*</span>
                    </label>
                    <select name="kelurahan_id" id="kelurahan" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" disabled>
                        <option value="">Pilih Kelurahan</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-road text-gray-500 mr-2 text-sm"></i>
                        RW <span class="text-red-500">*</span>
                    </label>
                    <select name="rw_id" id="rw" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" disabled>
                        <option value="">Pilih RW</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-home text-gray-500 mr-2 text-sm"></i>
                        RT <span class="text-red-500">*</span>
                    </label>
                    <select name="rt_id" id="rt" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" disabled>
                        <option value="">Pilih RT</option>
                    </select>
                </div>
                
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-map-pin text-gray-500 mr-2 text-sm"></i>
                        Alamat Detail <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="alamat_detail" required 
                           placeholder="Contoh: Jl. Merdeka No. 10, depan warung Bu Siti" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                
                <!-- Deskripsi -->
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-align-left text-gray-500 mr-2 text-sm"></i>
                        Deskripsi Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" required placeholder="Jelaskan secara detail apa yang terjadi..." 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm h-32"></textarea>
                </div>
                
                <!-- Upload Foto -->
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-camera text-gray-500 mr-2 text-sm"></i>
                        Unggah Foto Bukti
                    </label>
                    <div class="drop-zone rounded-lg p-6 text-center cursor-pointer border-2 border-dashed border-gray-300" id="fileDropArea">
                        <i class="fas fa-image text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500 mt-2">Seret dan lepas file foto di sini atau klik untuk memilih</p>
                        <p class="text-xs text-gray-500 mt-2">Format: JPG/PNG, maksimal 2MB</p>
                        <input type="file" name="foto_pelaporan" id="fileInput" class="hidden" accept="image/*">
                    </div>
                    <div id="imagePreviewContainer" class="mt-4 hidden">
                        <div class="relative">
                            <img id="imagePreview" src="#" alt="Preview Gambar" class="max-w-full h-auto rounded-lg border">
                            <button type="button" id="removeImage" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
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

    <!-- Riwayat Laporan -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-[#1D3557] mb-4 flex items-center">
            <i class="fas fa-history text-blue-500 mr-2"></i>
            Riwayat Laporan Anda
        </h2>
        
        @if($laporans->isEmpty())
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-gray-400 mb-3">
                    <i class="fas fa-file-alt fa-3x"></i>
                </div>
                <p class="text-gray-600">Belum ada data laporan yang dibuat</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Laporan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($laporans as $laporan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $laporan->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $laporan->jenis_laporan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    RT {{ $laporan->rt->nomor_rt ?? '-' }}, RW {{ $laporan->rw->nomor_rw ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $statusClass = [
                                            'Diterima' => 'bg-green-100 text-green-800',
                                            'Diproses' => 'bg-yellow-100 text-yellow-800',
                                            'Ditolak' => 'bg-red-100 text-red-800',
                                            'Pending' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusClass = $statusClass[$laporan->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="{{ $statusClass }} px-2 py-1 rounded-full text-xs font-medium">
                                        {{ $laporan->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button type="button" 
                                            data-laporan-id="{{ $laporan->id }}" 
                                            class="detail-btn text-blue-500 hover:text-blue-700 transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 rounded px-2 py-1">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    {{ $laporans->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail Laporan -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-[#1D3557]">Detail Laporan</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="text-center py-8 hidden">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>
        
        <div id="modalContent" class="space-y-4 mb-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tanggal:</p>
                    <p id="detail-tanggal" class="text-gray-800 font-medium"></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Jenis Laporan:</p>
                    <p id="detail-jenis" class="text-gray-800 font-medium"></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Kecamatan:</p>
                    <p id="detail-kecamatan" class="text-gray-800 font-medium"></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Kelurahan:</p>
                    <p id="detail-kelurahan" class="text-gray-800 font-medium"></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">RW:</p>
                    <p id="detail-rw" class="text-gray-800 font-medium"></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">RT:</p>
                    <p id="detail-rt" class="text-gray-800 font-medium"></p>
                </div>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500">Alamat Detail:</p>
                <p id="detail-alamat" class="text-gray-800 font-medium"></p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500">Status:</p>
                <span id="detail-status-badge" class="px-3 py-1 rounded-full text-sm font-medium"></span>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500">Deskripsi:</p>
                <p id="detail-deskripsi" class="bg-gray-50 p-3 rounded text-gray-800"></p>
            </div>
            
            <div id="foto-container">
                <p class="text-sm font-medium text-gray-500 mb-2">Bukti Foto:</p>
                <div class="border rounded-lg overflow-hidden">
                    <img id="detail-foto" src="" class="w-full h-auto max-h-64 object-contain hidden" alt="Bukti Foto">
                    <div id="no-foto" class="p-4 text-center text-gray-500 bg-gray-50">
                        <i class="fas fa-image fa-2x mb-2"></i>
                        <p>Tidak ada foto</p>
                    </div>
                </div>
            </div>
            
            <div id="catatan-container" class="hidden">
                <p class="text-sm font-medium text-gray-500">Catatan Petugas:</p>
                <p id="detail-catatan" class="bg-blue-50 p-3 rounded text-gray-800"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Report type selection
    $('.report-type').on('click', function() {
        $('.report-type').removeClass('active border-blue-500 bg-blue-50');
        $(this).addClass('active border-blue-500 bg-blue-50');
        $('#jenis_laporan').val($(this).data('value'));
    });
    
    // Hierarchical dropdown functionality
    $('#kecamatan').on('change', function() {
        const kecamatanId = $(this).val();
        const kelurahanDropdown = $('#kelurahan');
        
        if (kecamatanId) {
            $.post('{{ route("get-kelurahan") }}', {
                _token: '{{ csrf_token() }}',
                kecamatan_id: kecamatanId
            })
            .done(function(data) {
                kelurahanDropdown.html(data).prop('disabled', false);
                $('#rw').html('<option value="">Pilih RW</option>').prop('disabled', true);
                $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
            });
        } else {
            kelurahanDropdown.html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
            $('#rw').html('<option value="">Pilih RW</option>').prop('disabled', true);
            $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
        }
    });
    
    $('#kelurahan').on('change', function() {
        const kelurahanId = $(this).val();
        const rwDropdown = $('#rw');
        
        if (kelurahanId) {
            $.post('{{ route("get-rw") }}', {
                _token: '{{ csrf_token() }}',
                kelurahan_id: kelurahanId
            })
            .done(function(data) {
                rwDropdown.html(data).prop('disabled', false);
                $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
            });
        } else {
            rwDropdown.html('<option value="">Pilih RW</option>').prop('disabled', true);
            $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
        }
    });
    
    $('#rw').on('change', function() {
        const rwId = $(this).val();
        const rtDropdown = $('#rt');
        
        if (rwId) {
            $.post('{{ route("get-rt") }}', {
                _token: '{{ csrf_token() }}',
                rw_id: rwId
            })
            .done(function(data) {
                rtDropdown.html(data).prop('disabled', false);
            });
        } else {
            rtDropdown.html('<option value="">Pilih RT</option>').prop('disabled', true);
        }
    });
    
    // File upload handling
    const fileInput = $('#fileInput');
    const fileDropArea = $('#fileDropArea');
    const imagePreviewContainer = $('#imagePreviewContainer');
    const imagePreview = $('#imagePreview');
    
    fileDropArea.on('click', function() {
        fileInput.click();
    });
    
    fileInput.on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.attr('src', e.target.result);
                imagePreviewContainer.removeClass('hidden');
                fileDropArea.addClass('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
    
    $('#removeImage').on('click', function() {
        fileInput.val('');
        imagePreviewContainer.addClass('hidden');
        fileDropArea.removeClass('hidden');
    });
    
    // Drag and drop functionality
    fileDropArea.on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('border-blue-500 bg-blue-50');
    });
    
    fileDropArea.on('dragleave dragend drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('border-blue-500 bg-blue-50');
    });
    
    fileDropArea.on('drop', function(e) {
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            fileInput[0].files = files;
            fileInput.trigger('change');
        }
    });
    
    // Form submission
    $('#reportForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate report type
        if (!$('#jenis_laporan').val()) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Silakan pilih jenis laporan terlebih dahulu',
                showConfirmButton: true
            });
            return;
        }
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Disable submit button
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: true,
                        timer: 3000
                    }).then(() => {
                        window.location.reload();
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat mengirim laporan';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: errorMessage,
                    showConfirmButton: true
                });
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Modal functionality
    const modal = $('#detailModal');
    const closeModal = $('#closeModal');
    const loadingIndicator = $('#loadingIndicator');
    const modalContent = $('#modalContent');
    
    // Show modal
    function showModal() {
        modal.removeClass('hidden');
        $('body').css('overflow', 'hidden');
    }
    
    // Hide modal
    function hideModal() {
        modal.addClass('hidden');
        $('body').css('overflow', 'auto');
    }
    
    // Close modal when clicking outside
    modal.on('click', function(e) {
        if ($(e.target).is(modal)) {
            hideModal();
        }
    });
    
    // Close modal button
    closeModal.on('click', hideModal);
    
    // Show loading state
    function showLoading(show) {
        if (show) {
            loadingIndicator.removeClass('hidden');
            modalContent.addClass('hidden');
        } else {
            loadingIndicator.addClass('hidden');
            modalContent.removeClass('hidden');
        }
    }
    
    // Detail button click
    $(document).on('click', '.detail-btn', function() {
        const laporanId = $(this).data('laporan-id');
        showLaporanDetail(laporanId);
    });
    
    // Show laporan detail
    function showLaporanDetail(laporanId) {
        showModal();
        showLoading(true);
        
        $.get(`/warga/pelaporan/${laporanId}`, {
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                populateModal(response.data);
            } else {
                throw new Error(response.message || 'Gagal memuat data');
            }
        })
        .fail(function(xhr) {
            let errorMessage = 'Gagal memuat detail laporan';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            throw new Error(errorMessage);
        })
        .always(function() {
            showLoading(false);
        })
        .catch(function(error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message,
                showConfirmButton: true
            });
            hideModal();
        });
    }
    
    // Populate modal with data
    function populateModal(data) {
        // Format tanggal
        const formattedDate = new Date(data.created_at).toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Isi data ke modal
        $('#detail-tanggal').text(formattedDate);
        $('#detail-jenis').text(data.jenis_laporan);
        $('#detail-kecamatan').text(data.kecamatan?.nama_kecamatan || '-');
        $('#detail-kelurahan').text(data.kelurahan?.nama_kelurahan || '-');
        $('#detail-rw').text(data.rw?.nomor_rw ? 'RW ' + data.rw.nomor_rw : '-');
        $('#detail-rt').text(data.rt?.nomor_rt ? 'RT ' + data.rt.nomor_rt : '-');
        $('#detail-alamat').text(data.alamat_detail || '-');
        $('#detail-deskripsi').text(data.deskripsi || 'Tidak ada deskripsi');
        
        // Set status badge
        const statusBadge = $('#detail-status-badge');
        statusBadge.text(data.status || '-');
        statusBadge.removeClass().addClass('px-3 py-1 rounded-full text-sm font-medium');
        
        switch(data.status) {
            case 'Diterima':
                statusBadge.addClass('bg-green-100 text-green-800');
                break;
            case 'Diproses':
                statusBadge.addClass('bg-yellow-100 text-yellow-800');
                break;
            case 'Ditolak':
                statusBadge.addClass('bg-red-100 text-red-800');
                break;
            default:
                statusBadge.addClass('bg-gray-100 text-gray-800');
        }
        
        // Handle photo
        const fotoElement = $('#detail-foto');
        const noFotoElement = $('#no-foto');
        
        if (data.foto_pelaporan) {
            fotoElement.attr('src', `/laporan_warga/${data.foto_pelaporan}`);
            fotoElement.removeClass('hidden');
            noFotoElement.hide();
            
            fotoElement.on('error', function() {
                fotoElement.addClass('hidden');
                noFotoElement.show().find('p').text('Gagal memuat foto');
            });
        } else {
            fotoElement.addClass('hidden');
            noFotoElement.show().find('p').text('Tidak ada foto');
        }
        
        // Handle catatan
        const catatanContainer = $('#catatan-container');
        const catatanElement = $('#detail-catatan');
        
        if (data.catatan) {
            catatanElement.text(data.catatan);
            catatanContainer.removeClass('hidden');
        } else {
            catatanContainer.addClass('hidden');
        }
    }
});
</script>
@endsection