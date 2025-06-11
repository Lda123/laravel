@extends('layouts.kader')

@section('title', 'Tracking Harian - DengueCare')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <h1 class="text-3xl font-bold text-[#1D3557]">Tracking Harian</h1>
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
                        <i class="fas fa-user fa-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Nama Kader</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $kader ? $kader->nama_lengkap : 'Belum ditentukan' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                        <i class="fas fa-map-marker-alt fa-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Wilayah RT</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $kader && $kader->rt ? 'RT ' . $kader->rt->nomor_rt : 'Belum ditentukan' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                        <i class="fas fa-chart-line fa-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Total Laporan</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $trackings->total() }} Laporan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Tracking Baru -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b border-gray-200 flex items-center">
            <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
            Buat Tracking Baru
        </h2>
        
        <form method="POST" action="{{ route('tracking-harian.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Pilih Warga -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-user-tag text-gray-500 mr-2 text-sm"></i>
                        Pilih Warga
                    </label>
                    <select name="warga_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                        <option value="">-- Pilih Warga --</option>
                        @foreach($warga as $w)
                            <option value="{{ $w->id }}" {{ old('warga_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama_lengkap }} (NIK: {{ $w->nik }})
                            </option>
                        @endforeach
                    </select>
                    @error('warga_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Pengecekan -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-calendar-check text-gray-500 mr-2 text-sm"></i>
                        Tanggal Pengecekan
                    </label>
                    <input type="date" name="tanggal" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                           value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori Masalah -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-exclamation-triangle text-gray-500 mr-2 text-sm"></i>
                        Kategori Masalah
                    </label>
                    <select name="kategori_masalah" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Aman" {{ old('kategori_masalah') == 'Aman' ? 'selected' : '' }}>Aman</option>
                        <option value="Tidak Aman" {{ old('kategori_masalah') == 'Tidak Aman' ? 'selected' : '' }}>Tidak Aman</option>
                        <option value="Belum Dicek" {{ old('kategori_masalah') == 'Belum Dicek' ? 'selected' : '' }}>Belum Dicek</option>
                    </select>
                    @error('kategori_masalah')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Foto -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-camera text-gray-500 mr-2 text-sm"></i>
                        Upload Foto Bukti
                    </label>
                    <input type="file" name="bukti_foto" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" accept="image/*">
                    <p class="text-xs text-gray-500">Format: JPG, PNG (Maks. 2MB)</p>
                    @error('bukti_foto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-align-left text-gray-500 mr-2 text-sm"></i>
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" rows="4" 
                              placeholder="Deskripsi hasil pengecekan..." required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Tombol Submit -->
            <div class="flex justify-end pt-3">
                <button type="submit" 
                        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Tracking
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Tracking -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-[#1D3557] mb-4 flex items-center">
            <i class="fas fa-history text-blue-500 mr-2"></i>
            Riwayat Tracking Anda
        </h2>
        
        @if($trackings->isEmpty())
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-gray-400 mb-3">
                    <i class="fas fa-file-alt fa-3x"></i>
                </div>
                <p class="text-gray-600">Belum ada data tracking yang dibuat</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Warga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RT</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($trackings as $tracking)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $tracking->tanggal->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $tracking->nama_warga }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $tracking->warga && $tracking->warga->rt ? 'RT ' . $tracking->warga->rt->nomor_rt : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $statusClass = [
                                            'Aman' => 'bg-green-100 text-green-800',
                                            'Tidak Aman' => 'bg-red-100 text-red-800',
                                            'Belum Dicek' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                        $statusClass = $statusClass[$tracking->kategori_masalah] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="{{$statusClass}} px-2 py-1 rounded-full text-xs font-medium">
                                        {{ $tracking->kategori_masalah }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button type="button" 
                                            data-tracking-id="{{ $tracking->id }}" 
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
                    {{ $trackings->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail Tracking -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-[#1D3557]">Detail Tracking</h3>
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
                    <p class="text-sm font-medium text-gray-500">RT:</p>
                    <p id="detail-rt" class="text-gray-800 font-medium"></p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Warga:</p>
                    <p id="detail-nama" class="text-gray-800 font-medium"></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">NIK:</p>
                    <p id="detail-nik" class="text-gray-800 font-medium"></p>
                </div>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500">Kader:</p>
                <p id="detail-kader" class="text-gray-800 font-medium"></p>
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
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('detailModal');
    const closeModal = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const modalContent = document.getElementById('modalContent');
    
    // Fungsi untuk menampilkan modal
    function showModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', handleEscapeKey);
    }
    
    // Fungsi untuk menyembunyikan modal
    function hideModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.removeEventListener('keydown', handleEscapeKey);
        // Clear image
        const fotoElement = document.getElementById('detail-foto');
        fotoElement.src = '';
        fotoElement.onerror = null;
        // Reset loading state
        showLoading(false);
    }
    
    // Fungsi untuk menangani tombol ESC
    function handleEscapeKey(e) {
        if (e.key === 'Escape') {
            hideModal();
        }
    }
    
    // Event delegation untuk tombol detail
    document.addEventListener('click', function(e) {
        const detailBtn = e.target.closest('.detail-btn');
        if (detailBtn) {
            e.preventDefault();
            const trackingId = detailBtn.getAttribute('data-tracking-id');
            if (trackingId) {
                showDetail(trackingId);
            } else {
                console.error('Data tracking ID tidak ditemukan');
                alert('Data tracking ID tidak valid');
            }
        }
    });
    
    // Event listeners untuk close modal
    [closeModal, closeModalBtn].forEach(btn => {
        btn.addEventListener('click', hideModal);
    });
    
    // Close modal saat klik background
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });
    
    // Fungsi untuk menampilkan/menyembunyikan loading
    function showLoading(show) {
        if (show) {
            loadingIndicator.classList.remove('hidden');
            modalContent.classList.add('hidden');
        } else {
            loadingIndicator.classList.add('hidden');
            modalContent.classList.remove('hidden');
        }
    }
    
    async function showDetail(trackingId) {
        if (!trackingId) {
            alert('ID tracking tidak valid');
            return;
        }
        
        // Show modal and loading state
        showModal();
        showLoading(true);
        
        try {
            // Perbaikan URL sesuai dengan route yang ada
            const response = await fetch(`{{ route('laporan.harian.show', ':id') }}`.replace(':id', trackingId), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            
            if (!response.ok) {
                let errorMessage = `HTTP error! status: ${response.status}`;
                
                if (response.status === 404) {
                    errorMessage = 'Data tracking tidak ditemukan';
                } else if (response.status === 401) {
                    errorMessage = 'Anda tidak memiliki akses ke data ini';
                } else if (response.status === 500) {
                    errorMessage = 'Terjadi kesalahan server';
                }
                
                throw new Error(errorMessage);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Gagal memuat data');
            }
            
            populateModal(result.data || result);
            showLoading(false);
            
        } catch (error) {
            console.error('Error:', error);
            showLoading(false);
            hideModal();
            
            // Show user-friendly error message
            let userMessage = 'Gagal memuat data tracking';
            if (error.message.includes('404') || error.message.includes('tidak ditemukan')) {
                userMessage = 'Data tracking tidak ditemukan atau sudah dihapus';
            } else if (error.message.includes('401') || error.message.includes('akses')) {
                userMessage = 'Anda tidak memiliki akses ke data ini';
            } else if (error.message.includes('network') || error.message.includes('fetch')) {
                userMessage = 'Koneksi internet bermasalah. Silakan coba lagi';
            }
            
            alert(userMessage + ': ' + error.message);
        }
    }
    
    function populateModal(data) {
        // Format tanggal
        const formattedDate = data.tanggal ? new Date(data.tanggal).toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }) : '-';
        
        // Isi data ke modal dengan null checking
        document.getElementById('detail-tanggal').textContent = formattedDate;
        document.getElementById('detail-nama').textContent = data.nama_warga || '-';
        document.getElementById('detail-nik').textContent = data.warga_nik || '-';
        document.getElementById('detail-rt').textContent = data.rt ? `RT ${data.rt}` : '-';
        document.getElementById('detail-kader').textContent = data.kader || '-';
        document.getElementById('detail-deskripsi').textContent = data.deskripsi || 'Tidak ada deskripsi';
        
        // Set status badge
        const statusBadge = document.getElementById('detail-status-badge');
        statusBadge.textContent = data.kategori_masalah || '-';
        statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium ';
        
        switch(data.kategori_masalah) {
            case 'Aman':
                statusBadge.className += 'bg-green-100 text-green-800';
                break;
            case 'Tidak Aman':
                statusBadge.className += 'bg-red-100 text-red-800';
                break;
            case 'Belum Dicek':
                statusBadge.className += 'bg-yellow-100 text-yellow-800';
                break;
            default:
                statusBadge.className += 'bg-gray-100 text-gray-800';
        }
        
        // Handle photo
        const fotoElement = document.getElementById('detail-foto');
        const noFotoElement = document.getElementById('no-foto');
        
        if (data.bukti_foto) {
            fotoElement.src = data.bukti_foto;
            fotoElement.classList.remove('hidden');
            noFotoElement.style.display = 'none';
            
            fotoElement.onload = function() {
                fotoElement.style.display = 'block';
            };
            
            fotoElement.onerror = function() {
                console.error('Failed to load image:', data.bukti_foto);
                fotoElement.classList.add('hidden');
                noFotoElement.style.display = 'block';
                noFotoElement.querySelector('p').textContent = 'Gagal memuat foto';
            };
        } else {
            fotoElement.classList.add('hidden');
            noFotoElement.style.display = 'block';
            noFotoElement.querySelector('p').textContent = 'Tidak ada foto';
        }
    }
});
</script>
@endpush
@endsection