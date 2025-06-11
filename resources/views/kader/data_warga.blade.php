@extends('layouts.kader')

@section('title', 'Data Warga')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Data Warga</h1>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Filter Data Warga</h2>
        <form method="GET" action="{{ route('data-warga') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Kecamatan Dropdown -->
            <div>
                <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                <select id="kecamatan" name="kecamatan" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id }}" {{ request('kecamatan') == $kecamatan->id ? 'selected' : '' }}>
                        {{ $kecamatan->nama_kecamatan }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Kelurahan Dropdown -->
            <div>
                <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                <select id="kelurahan" name="kelurahan" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kelurahan</option>
                    @if(request('kecamatan'))
                        @foreach($kelurahans->where('kecamatan_id', request('kecamatan')) as $kelurahan)
                        <option value="{{ $kelurahan->id }}" {{ request('kelurahan') == $kelurahan->id ? 'selected' : '' }}>
                            {{ $kelurahan->nama_kelurahan }}
                        </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- RW Dropdown -->
            <div>
                <label for="rw" class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                <select id="rw" name="rw" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua RW</option>
                    @if(request('kelurahan'))
                        @foreach($rws->where('kelurahan_id', request('kelurahan')) as $rw)
                        <option value="{{ $rw->id }}" {{ request('rw') == $rw->id ? 'selected' : '' }}>
                            RW {{ str_pad($rw->nomor_rw, 2, '0', STR_PAD_LEFT) }}
                        </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- RT Dropdown -->
            <div>
                <label for="rt" class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                <select id="rt" name="rt" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua RT</option>
                    @if(request('rw'))
                        @foreach($rts->where('rw_id', request('rw')) as $rt)
                        <option value="{{ $rt->id }}" {{ request('rt') == $rt->id ? 'selected' : '' }}>
                            RT {{ str_pad($rt->nomor_rt, 2, '0', STR_PAD_LEFT) }}
                        </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Search Box -->
            <div class="md:col-span-3">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama</label>
                <div class="flex">
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari berdasarkan Nama">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="md:col-span-1 flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Terapkan Filter
                </button>
                @if(request()->anyFilled(['kecamatan', 'kelurahan', 'rw', 'rt', 'search']))
                <a href="{{ route('data-warga') }}" class="ml-2 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Warga Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($wargas as $warga)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $warga->nama_lengkap ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($warga->rt && $warga->rt->rw && $warga->rt->rw->kelurahan)
                                RT {{ str_pad($warga->rt->nomor_rt, 2, '0', STR_PAD_LEFT) }}/RW {{ str_pad($warga->rt->rw->nomor_rw, 2, '0', STR_PAD_LEFT) }}, {{ $warga->rt->rw->kelurahan->nama_kelurahan }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $latestTracking = $warga->trackingHarians->first();
                                $status = $latestTracking->kategori_masalah ?? 'Belum Diperiksa';
                                $statusClasses = [
                                    'Aman' => 'bg-green-100 text-green-800',
                                    'Tidak Aman' => 'bg-red-100 text-red-800',
                                    'Belum Diperiksa' => 'bg-yellow-100 text-yellow-800'
                                ];
                                $statusClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button type="button" 
                                    data-warga-id="{{ $warga->id }}" 
                                    class="detail-btn text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data warga yang ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($wargas->hasPages())
        <div class="px-4 py-3 bg-gray-50 sm:px-6">
            {{ $wargas->appends(request()->query())->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>

<!-- Detail Warga Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Detail Warga</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="text-center py-8 hidden">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>
        
        <div id="modalContent" class="space-y-4">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama</p>
                    <p id="detail-nama" class="font-medium">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jenis Kelamin</p>
                    <p id="detail-jk" class="font-medium">-</p>
                </div>
            </div>
            
            <!-- Status Info -->
            <div>
                <p class="text-sm text-gray-500">Status Terakhir</p>
                <span id="detail-status-badge" class="px-3 py-1 rounded-full text-sm font-medium">-</span>
            </div>
            
            <!-- Tracking Info (Conditional) -->
            <div id="tracking-info" class="hidden">
                <hr class="my-4">
                <h4 class="font-medium text-gray-800 mb-2">Pemeriksaan Terakhir</h4>
                
                <div class="grid grid-cols-2 gap-4 mb-2">
                    <div>
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p id="detail-tanggal-periksa" class="font-medium">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Oleh</p>
                        <p id="detail-kader" class="font-medium">-</p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="text-sm text-gray-500">Catatan</p>
                    <p id="detail-deskripsi" class="bg-gray-50 p-3 rounded text-gray-800">-</p>
                </div>
                
                <!-- Photo Evidence -->
                <div>
                    <p class="text-sm text-gray-500 mb-2">Bukti Foto</p>
                    <div class="border rounded-lg overflow-hidden bg-gray-50">
                        <img id="detail-foto" src="" class="w-full h-auto max-h-64 object-contain hidden cursor-pointer" alt="Bukti Foto" onclick="openFullImage()">
                        <div id="no-foto" class="p-4 text-center text-gray-500">
                            <i class="fas fa-image fa-2x mb-2"></i>
                            <p>Tidak ada foto</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button id="closeModalBtn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Full Image Modal -->
<div id="fullImageModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center hidden z-60">
    <div class="relative max-w-screen-lg w-full p-4">
        <button id="closeFullImage" class="absolute top-4 right-4 text-white hover:text-gray-300 text-3xl z-10">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex items-center justify-center h-full">
            <img id="fullImage" src="" class="max-w-full max-h-[80vh] object-contain" alt="Bukti Foto Full">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modal = document.getElementById('detailModal');
    const fullImageModal = document.getElementById('fullImageModal');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const modalContent = document.getElementById('modalContent');
    
    // Show modal
    function showModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    // Hide modal
    function hideModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Show full image
    window.openFullImage = function() {
        const fotoElement = document.getElementById('detail-foto');
        const fullImage = document.getElementById('fullImage');
        
        if (fotoElement.src) {
            fullImage.src = fotoElement.src;
            fullImageModal.classList.remove('hidden');
        }
    }
    
    // Hide full image
    function hideFullImage() {
        fullImageModal.classList.add('hidden');
    }
    
    // Close buttons event listeners
    document.getElementById('closeModal')?.addEventListener('click', hideModal);
    document.getElementById('closeModalBtn')?.addEventListener('click', hideModal);
    document.getElementById('closeFullImage')?.addEventListener('click', hideFullImage);
    
    // Close when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) hideModal();
    });
    fullImageModal.addEventListener('click', function(e) {
        if (e.target === fullImageModal) hideFullImage();
    });
    
    // Escape key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!fullImageModal.classList.contains('hidden')) {
                hideFullImage();
            } else {
                hideModal();
            }
        }
    });
    
    // Show loading state
    function showLoading(show) {
        if (show) {
            loadingIndicator.classList.remove('hidden');
            modalContent.classList.add('hidden');
        } else {
            loadingIndicator.classList.add('hidden');
            modalContent.classList.remove('hidden');
        }
    }
    
    // Detail button click handler
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.detail-btn')) {
            const detailBtn = e.target.closest('.detail-btn');
            const wargaId = detailBtn.getAttribute('data-warga-id');
            
            if (wargaId) {
                try {
                    detailBtn.disabled = true;
                    showModal();
                    showLoading(true);
                    
                    const response = await fetch(`/kader/warga-detail/${wargaId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (!response.ok) throw new Error('Gagal memuat data');
                    
                    const data = await response.json();
                    populateModal(data);
                    showLoading(false);
                    
                } catch (error) {
                    console.error('Error:', error);
                    alert('Gagal memuat detail warga: ' + error.message);
                    hideModal();
                } finally {
                    detailBtn.disabled = false;
                }
            }
        }
    });
    
    // Populate modal with data
    function populateModal(data) {
        // Basic info
        document.getElementById('detail-nama').textContent = data.nama_lengkap || '-';
        document.getElementById('detail-jk').textContent = data.jenis_kelamin === 'L' ? 'Laki-laki' : (data.jenis_kelamin === 'P' ? 'Perempuan' : '-');
        
        // Status badge
        const statusBadge = document.getElementById('detail-status-badge');
        const trackingInfo = document.getElementById('tracking-info');
        
        if (data.latest_tracking) {
            const status = data.latest_tracking.kategori_masalah || 'Belum Diperiksa';
            const statusClasses = {
                'Aman': 'bg-green-100 text-green-800',
                'Tidak Aman': 'bg-red-100 text-red-800',
                'Belum Diperiksa': 'bg-yellow-100 text-yellow-800'
            };
            
            statusBadge.textContent = status;
            statusBadge.className = `px-3 py-1 rounded-full text-sm font-medium ${statusClasses[status] || 'bg-gray-100 text-gray-800'}`;
            
            // Tracking info
            document.getElementById('detail-tanggal-periksa').textContent = 
                data.latest_tracking.tanggal ? new Date(data.latest_tracking.tanggal).toLocaleDateString('id-ID') : '-';
            document.getElementById('detail-kader').textContent = data.latest_tracking.kader || '-';
            document.getElementById('detail-deskripsi').textContent = data.latest_tracking.deskripsi || 'Tidak ada catatan';
            
            // Photo handling
            const fotoElement = document.getElementById('detail-foto');
            const noFotoElement = document.getElementById('no-foto');
            
            if (data.latest_tracking.bukti_foto) {
                fotoElement.src = data.latest_tracking.bukti_foto;
                fotoElement.classList.remove('hidden');
                noFotoElement.classList.add('hidden');
                
                fotoElement.onerror = function() {
                    fotoElement.classList.add('hidden');
                    noFotoElement.classList.remove('hidden');
                };
            } else {
                fotoElement.classList.add('hidden');
                noFotoElement.classList.remove('hidden');
            }
            
            trackingInfo.classList.remove('hidden');
        } else {
            statusBadge.textContent = 'Belum Diperiksa';
            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800';
            trackingInfo.classList.add('hidden');
        }
    }
    
    // Dynamic dropdowns with auto-load
    const kecamatanDropdown = document.getElementById('kecamatan');
    const kelurahanDropdown = document.getElementById('kelurahan');
    const rwDropdown = document.getElementById('rw');
    const rtDropdown = document.getElementById('rt');
    
    // Function to load kelurahan based on kecamatan
    function loadKelurahan(kecamatanId) {
        if (kecamatanId) {
            fetch(`/kader/get-kelurahan?kecamatan_id=${kecamatanId}`)
                .then(response => response.json())
                .then(data => {
                    kelurahanDropdown.innerHTML = '<option value="">Semua Kelurahan</option>';
                    data.forEach(kelurahan => {
                        const option = document.createElement('option');
                        option.value = kelurahan.id;
                        option.textContent = kelurahan.nama_kelurahan;
                        kelurahanDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            kelurahanDropdown.innerHTML = '<option value="">Semua Kelurahan</option>';
        }
    }
    
    // Function to load RW based on kelurahan
    function loadRw(kelurahanId) {
        if (kelurahanId) {
            fetch(`/kader/get-rw?kelurahan_id=${kelurahanId}`)
                .then(response => response.json())
                .then(data => {
                    rwDropdown.innerHTML = '<option value="">Semua RW</option>';
                    data.forEach(rw => {
                        const option = document.createElement('option');
                        option.value = rw.id;
                        option.textContent = `RW ${String(rw.nomor_rw).padStart(2, '0')}`;
                        rwDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            rwDropdown.innerHTML = '<option value="">Semua RW</option>';
        }
    }
    
    // Function to load RT based on RW
    function loadRt(rwId) {
        if (rwId) {
            fetch(`/kader/get-rt?rw_id=${rwId}`)
                .then(response => response.json())
                .then(data => {
                    rtDropdown.innerHTML = '<option value="">Semua RT</option>';
                    data.forEach(rt => {
                        const option = document.createElement('option');
                        option.value = rt.id;
                        option.textContent = `RT ${String(rt.nomor_rt).padStart(2, '0')}`;
                        rtDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            rtDropdown.innerHTML = '<option value="">Semua RT</option>';
        }
    }
    
    // Kecamatan change handler
    if (kecamatanDropdown) {
        kecamatanDropdown.addEventListener('change', function() {
            const kecamatanId = this.value;
            loadKelurahan(kecamatanId);
            rwDropdown.innerHTML = '<option value="">Semua RW</option>';
            rtDropdown.innerHTML = '<option value="">Semua RT</option>';
        });
    }
    
    // Kelurahan change handler
    if (kelurahanDropdown) {
        kelurahanDropdown.addEventListener('change', function() {
            const kelurahanId = this.value;
            loadRw(kelurahanId);
            rtDropdown.innerHTML = '<option value="">Semua RT</option>';
        });
    }
    
    // RW change handler
    if (rwDropdown) {
        rwDropdown.addEventListener('change', function() {
            const rwId = this.value;
            loadRt(rwId);
        });
    }
});
</script>
@endpush