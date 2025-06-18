@extends('layouts.warga')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Info Cards -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg p-6 border border-blue-100 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                        <i class="fas fa-user fa-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Nama Warga</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $warga ? $warga->nama_lengkap : 'Belum ditentukan' }}
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
                            {{ $warga && $warga->rt ? 'RT ' . $warga->rt->nomor_rt : 'Belum ditentukan' }}
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
                            {{ $laporans->total() }} Laporan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Laporan -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-[#1D3557] mb-4 flex items-center">
            <i class="fas fa-history text-blue-500 mr-2"></i>
            Riwayat Pelaporan Anda
        </h2>
        
        @if($laporans->isEmpty())
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-gray-400 mb-3">
                    <i class="fas fa-file-alt fa-3x"></i>
                </div>
                <p class="text-gray-600">Belum ada data laporan yang dibuat</p>
                <a href="{{ route('warga.laporan.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> Buat Laporan Baru
                </a>
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
                                    {{ $laporan->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $laporan->jenis_laporan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $laporan->alamat_detail }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $statusClass = [
                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                            'Terverifikasi' => 'bg-blue-100 text-blue-800',
                                            'Selesai' => 'bg-green-100 text-green-800'
                                        ];
                                        $statusClass = $statusClass[$laporan->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="{{$statusClass}} px-2 py-1 rounded-full text-xs font-medium">
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
                    <p class="text-sm font-medium text-gray-500">Status:</p>
                    <span id="detail-status-badge" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Jenis Laporan:</p>
                    <p id="detail-jenis" class="text-gray-800 font-medium"></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">RT/RW:</p>
                    <p id="detail-rt-rw" class="text-gray-800 font-medium"></p>
                </div>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500">Lokasi:</p>
                <p id="detail-lokasi" class="text-gray-800 font-medium"></p>
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
            
            <div id="tindakan-container" class="hidden">
                <p class="text-sm font-medium text-gray-500 mb-2">Tindakan:</p>
                <p id="detail-tindakan" class="bg-gray-50 p-3 rounded text-gray-800"></p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('detailModal');
    const closeModal = document.getElementById('closeModal');
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
            const laporanId = detailBtn.getAttribute('data-laporan-id');
            if (laporanId) {
                showDetail(laporanId);
            } else {
                console.error('Data laporan ID tidak ditemukan');
                alert('Data laporan ID tidak valid');
            }
        }
    });
    
    // Event listeners untuk close modal
    closeModal.addEventListener('click', hideModal);
    
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
    
    async function showDetail(laporanId) {
        if (!laporanId) {
            alert('ID laporan tidak valid');
            return;
        }
        
        // Show modal and loading state
        showModal();
        showLoading(true);
        
        try {
            const response = await fetch(`{{ route('warga.riwayat-laporan.show', ':id') }}`.replace(':id', laporanId), {
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
                    errorMessage = 'Data laporan tidak ditemukan';
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
            let userMessage = 'Gagal memuat data laporan';
            if (error.message.includes('404') || error.message.includes('tidak ditemukan')) {
                userMessage = 'Data laporan tidak ditemukan atau sudah dihapus';
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
        const formattedDate = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : '-';
        
        // Isi data ke modal dengan null checking
        document.getElementById('detail-tanggal').textContent = formattedDate;
        document.getElementById('detail-jenis').textContent = data.jenis_laporan || '-';
        document.getElementById('detail-lokasi').textContent = data.alamat_detail || '-';
        document.getElementById('detail-deskripsi').textContent = data.deskripsi || 'Tidak ada deskripsi';
        
        // Set RT/RW
        const rtRw = (data.rt ? 'RT ' + data.rt : '') + 
                     (data.rw ? (data.rt ? '/RW ' : 'RW ') + data.rw : '');
        document.getElementById('detail-rt-rw').textContent = rtRw || '-';
        
        // Set status badge
        const statusBadge = document.getElementById('detail-status-badge');
        statusBadge.textContent = data.status || '-';
        statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium ';
        
        switch(data.status) {
            case 'Pending':
                statusBadge.className += 'bg-yellow-100 text-yellow-800';
                break;
            case 'Terverifikasi':
                statusBadge.className += 'bg-blue-100 text-blue-800';
                break;
            case 'Selesai':
                statusBadge.className += 'bg-green-100 text-green-800';
                break;
            default:
                statusBadge.className += 'bg-gray-100 text-gray-800';
        }
        
        // Handle photo
        const fotoElement = document.getElementById('detail-foto');
        const noFotoElement = document.getElementById('no-foto');
        
        if (data.foto_pelaporan) {
            fotoElement.src = data.foto_pelaporan;
            fotoElement.classList.remove('hidden');
            noFotoElement.style.display = 'none';
            
            fotoElement.onload = function() {
                fotoElement.style.display = 'block';
            };
            
            fotoElement.onerror = function() {
                console.error('Failed to load image:', data.foto_pelaporan);
                fotoElement.classList.add('hidden');
                noFotoElement.style.display = 'block';
                noFotoElement.querySelector('p').textContent = 'Gagal memuat foto';
            };
        } else {
            fotoElement.classList.add('hidden');
            noFotoElement.style.display = 'block';
            noFotoElement.querySelector('p').textContent = 'Tidak ada foto';
        }
        
        // Handle tindakan jika ada
        const tindakanContainer = document.getElementById('tindakan-container');
        const tindakanElement = document.getElementById('detail-tindakan');
        
        if (data.tindakan) {
            tindakanContainer.classList.remove('hidden');
            tindakanElement.textContent = data.tindakan;
        } else {
            tindakanContainer.classList.add('hidden');
        }
    }
});
</script>
@endpush
@endsection