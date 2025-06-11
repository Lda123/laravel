@extends('layouts.warga')

@section('title', 'Riwayat Pengecekan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Riwayat Pengecekan Rumah</h1>
        <p class="text-lg text-gray-600">Status terkini dan riwayat pengecekan rumah Anda</p>
    </div>

   <!-- Current Status -->
    <div class="grid grid-cols-1 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Status Terkini</h2>
                @if(isset($latestData) && $latestData)
                    @php
                        $statusClass = match($latestData->status) {
                            'Aman' => 'bg-green-100 text-green-800',
                            'Tidak Aman' => 'bg-red-100 text-red-800',
                            'Belum Dicek' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    @endphp
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                            {{ $latestData->status }}
                        </span>
                        @if($latestData->kategori_masalah)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                {{ $latestData->kategori_masalah }}
                            </span>
                        @endif
                    </div>
                @else
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Belum Ada Data
                    </span>
                @endif
            </div>
            
            @if(isset($latestData) && $latestData)
                <div class="mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 mb-2">
                                <span class="font-medium">Terakhir diperiksa:</span> 
                                {{ \Carbon\Carbon::parse($latestData->tanggal_pantau)->format('l, d F Y') }}
                            </p>
                            @if(isset($latestData->kader) && $latestData->kader)
                                <p class="text-gray-600 mb-2">
                                    <span class="font-medium">Petugas:</span> 
                                    {{ $latestData->kader->nama_lengkap ?? $latestData->kader->nama ?? 'Tidak diketahui' }}
                                </p>
                            @endif
                        </div>
                        <div>
                            @if($latestData->tingkat_risiko)
                                <p class="text-gray-600 mb-2">
                                    <span class="font-medium">Tingkat Risiko:</span>
                                    <span class="ml-1 px-2 py-1 text-xs rounded {{ 
                                        $latestData->tingkat_risiko == 'Tinggi' ? 'bg-red-100 text-red-700' :
                                        ($latestData->tingkat_risiko == 'Sedang' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')
                                    }}">
                                        {{ $latestData->tingkat_risiko }}
                                    </span>
                                </p>
                            @endif
                            @if($latestData->jam_pantau)
                                <p class="text-gray-600">
                                    <span class="font-medium">Waktu:</span> 
                                    {{ \Carbon\Carbon::parse($latestData->jam_pantau)->format('H:i') }} WIB
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Kategori Masalah Detail -->
                @if($latestData->kategori_masalah || $latestData->detail_masalah)
                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                        <h4 class="font-medium text-blue-900 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Kategori Masalah yang Teridentifikasi
                        </h4>
                        @if($latestData->kategori_masalah)
                            <p class="text-blue-800 font-medium">{{ $latestData->kategori_masalah }}</p>
                        @endif
                        @if($latestData->detail_masalah)
                            <p class="text-blue-700 text-sm mt-1">{{ $latestData->detail_masalah }}</p>
                        @endif
                    </div>
                @endif
                
                <!-- Status Information -->
                <div class="bg-gray-100 rounded-lg p-4">
                    @if($latestData->status == 'Tidak Aman')
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-red-800 font-medium mb-1">Perhatian: Status Tidak Aman</p>
                                <p class="text-gray-700 mb-2">
                                    Terdapat indikasi risiko DBD di lingkungan rumah Anda. 
                                    {{ $latestData->keterangan ?? 'Silakan hubungi kader untuk informasi lebih lanjut.' }}
                                </p>
                                
                                <!-- Rekomendasi Tindakan -->
                                @if($latestData->rekomendasi_tindakan)
                                    <div class="mt-3 p-3 bg-red-50 rounded border-l-4 border-red-400">
                                        <p class="text-red-800 font-medium text-sm mb-1">Tindakan yang Disarankan:</p>
                                        <p class="text-red-700 text-sm">{{ $latestData->rekomendasi_tindakan }}</p>
                                    </div>
                                @endif
                                
                                <!-- Tindak Lanjut -->
                                @if($latestData->tindak_lanjut)
                                    <div class="mt-2 text-sm">
                                        <span class="font-medium text-red-800">Tindak Lanjut:</span>
                                        <span class="text-red-700">{{ $latestData->tindak_lanjut }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($latestData->status == 'Aman')
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-green-800 font-medium mb-1">Status Baik</p>
                                <p class="text-gray-700 mb-2">
                                    Lingkungan rumah Anda dalam kondisi aman dari DBD. 
                                    {{ $latestData->keterangan ?? 'Tetap jaga kebersihan lingkungan.' }}
                                </p>
                                
                                <!-- Catatan Pencegahan -->
                                @if($latestData->catatan_pencegahan)
                                    <div class="mt-3 p-3 bg-green-50 rounded border-l-4 border-green-400">
                                        <p class="text-green-800 font-medium text-sm mb-1">Catatan Pencegahan:</p>
                                        <p class="text-green-700 text-sm">{{ $latestData->catatan_pencegahan }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-yellow-800 font-medium mb-1">Belum Dicek</p>
                                <p class="text-gray-700">
                                    {{ $latestData->keterangan ?? 'Data pengecekan masih dalam proses verifikasi oleh kader.' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Summary Statistics (if available) -->
                @if(isset($weeklyStats) && $weeklyStats)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Ringkasan 7 Hari Terakhir</h4>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-green-50 rounded-lg p-3">
                                <div class="text-lg font-bold text-green-600">{{ $weeklyStats->aman_count ?? 0 }}</div>
                                <div class="text-xs text-green-700">Aman</div>
                            </div>
                            <div class="bg-red-50 rounded-lg p-3">
                                <div class="text-lg font-bold text-red-600">{{ $weeklyStats->tidak_aman_count ?? 0 }}</div>
                                <div class="text-xs text-red-700">Tidak Aman</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-3">
                                <div class="text-lg font-bold text-yellow-600">{{ $weeklyStats->belum_dicek_count ?? 0 }}</div>
                                <div class="text-xs text-yellow-700">Belum Dicek</div>
                            </div>
                        </div>
                    </div>
                @endif
                
            @else
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-gray-700 font-medium mb-1">Belum Ada Data Pengecekan</p>
                    <p class="text-sm text-gray-600">Data akan muncul setelah kader melakukan pengecekan terhadap rumah Anda</p>
                    
                    <!-- Informasi Kontak Kader -->
                    @if(isset($contactInfo) && $contactInfo)
                        <div class="mt-4 p-3 bg-white rounded border">
                            <p class="text-sm text-gray-600 mb-2">Hubungi kader untuk pengecekan:</p>
                            <p class="text-sm font-medium text-gray-800">{{ $contactInfo->nama ?? 'Kader Posyandu' }}</p>
                            @if($contactInfo->telepon)
                                <p class="text-sm text-blue-600">{{ $contactInfo->telepon }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <<!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pengecekan</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ isset($stats) ? $stats->total : 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status Aman</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ isset($stats) ? $stats->aman_count : 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status Tidak Aman</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ isset($stats) ? $stats->tidak_aman_count : 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- History Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                Riwayat Pengecekan
            </h2>
            
            <form method="GET" action="{{ route('warga.riwayat') }}" class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <select name="bulan" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ request('bulan') == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($month)->isoFormat('MMMM') }}
                        </option>
                    @endforeach
                </select>
                <select name="tahun" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Tahun</option>
                    @foreach(range(date('Y'), 2020) as $year)
                        <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="Aman" {{ request('status') == 'Aman' ? 'selected' : '' }}>Aman</option>
                    <option value="Tidak Aman" {{ request('status') == 'Tidak Aman' ? 'selected' : '' }}>Tidak Aman</option>
                    <option value="Belum Dicek" {{ request('status') == 'Belum Dicek' ? 'selected' : '' }}>Belum Dicek</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['bulan', 'tahun', 'status']))
                    <a href="{{ route('warga.riwayat') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Reset
                    </a>        
                @endif
            </form>
        </div>

        @if(isset($riwayat) && $riwayat->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <!-- Table header -->
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($riwayat as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                        @if($item->tanggal_pantau)
                            {{ \Carbon\Carbon::parse($item->tanggal_pantau)->format('d M Y') }}
                        @elseif($item->tanggal)
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClass = match($item->status ?? 'Belum Dicek') {
                                'Aman' => 'bg-green-100 text-green-800',
                                'Tidak Aman' => 'bg-red-100 text-red-800',
                                'Belum Dicek' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                            {{ $item->status ?? 'Belum Dicek' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500 max-w-xs truncate">
                            {{ $item->keterangan ?? $item->deskripsi ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                        {{ $item->kader->nama_lengkap ?? $item->kader->nama ?? 'Sistem' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button type="button" 
                                data-id="{{ $item->id }}" 
                                class="detail-btn text-blue-500 hover:text-blue-700 transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 rounded px-2 py-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            Detail
                        </button>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if(isset($riwayat) && method_exists($riwayat, 'links'))
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{ $riwayat->links() }}
        </div>
        @endif
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-gray-400 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-gray-600">Belum ada data riwayat pengecekan</p>
            <p class="text-sm text-gray-500 mt-2">Data akan muncul setelah kader melakukan pengecekan terhadap rumah Anda</p>
        </div>
    @endif
    </div>

<!-- Health Tips -->
<div class="bg-blue-50 rounded-lg p-6 mb-8">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Tips Pencegahan DBD</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-blue-600 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h3 class="font-medium text-gray-800 mb-1">Bersihkan Tempat Air</h3>
            <p class="text-sm text-gray-600">Kuras dan bersihkan bak mandi, tempayan, vas bunga minimal seminggu sekali.</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-blue-600 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <h3 class="font-medium text-gray-800 mb-1">Tutup Wadah Air</h3>
            <p class="text-sm text-gray-600">Tutup rapat tempat penampungan air untuk mencegah nyamuk bertelur.</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-blue-600 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2H4z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16" />
                </svg>
            </div>
            <h3 class="font-medium text-gray-800 mb-1">Buang Barang Bekas</h3>
            <p class="text-sm text-gray-600">Buang atau daur ulang barang bekas yang dapat menampung air hujan.</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-blue-600 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                </svg>
            </div>
            <h3 class="font-medium text-gray-800 mb-1">Gunakan Anti Nyamuk</h3>
            <p class="text-sm text-gray-600">Pasang kasa nyamuk dan gunakan lotion anti nyamuk terutama saat pagi/sore.</p>
        </div>
    </div>
</div>

<!-- Modal Detail Pengecekan -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Detail Pengecekan</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
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
            
            <div>
                <p class="text-sm font-medium text-gray-500">Petugas:</p>
                <p id="detail-petugas" class="text-gray-800 font-medium"></p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500">Keterangan:</p>
                <p id="detail-keterangan" class="bg-gray-50 p-3 rounded text-gray-800"></p>
            </div>
            
            <div id="foto-container">
                <p class="text-sm font-medium text-gray-500 mb-2">Bukti Foto:</p>
                <div class="border rounded-lg overflow-hidden">
                    <img id="detail-foto" src="" class="w-full h-auto max-h-64 object-contain hidden" alt="Bukti Foto">
                    <div id="no-foto" class="p-4 text-center text-gray-500 bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
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
    const loadingIndicator = document.getElementById('loadingIndicator');
    const modalContent = document.getElementById('modalContent');
    
    // Check if elements exist before adding event listeners
    if (!modal || !closeModal || !loadingIndicator || !modalContent) {
        console.error('Required modal elements not found');
        return;
    }
    
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
        if (fotoElement) {
            fotoElement.src = '';
            fotoElement.onerror = null;
            fotoElement.classList.add('hidden');
        }
        
        // Clear all modal content
        clearModalContent();
        
        // Reset loading state
        showLoading(false);
    }
    
    // Fungsi untuk clear modal content
    function clearModalContent() {
        const fields = [
            'detail-tanggal',
            'detail-petugas', 
            'detail-keterangan'
        ];
        
        fields.forEach(fieldId => {
            const element = document.getElementById(fieldId);
            if (element) element.textContent = '';
        });
        
        const statusBadge = document.getElementById('detail-status-badge');
        if (statusBadge) {
            statusBadge.textContent = '';
            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800';
        }
        
        const noFotoElement = document.getElementById('no-foto');
        if (noFotoElement) {
            noFotoElement.style.display = 'none';
        }
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
            const checkId = detailBtn.getAttribute('data-id');
            if (checkId) {
                showDetail(checkId);
            } else {
                console.error('Data ID tidak ditemukan');
                showAlert('error', 'Data ID tidak valid');
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
    
    // Fungsi untuk menampilkan alert yang lebih user-friendly
    function showAlert(type, message) {
        // Coba gunakan SweetAlert jika tersedia, jika tidak gunakan alert biasa
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'error' ? 'Oops...' : 'Informasi',
                text: message,
                confirmButtonColor: type === 'error' ? '#ef4444' : '#3b82f6'
            });
        } else {
            alert(message);
        }
    }
    
    async function showDetail(checkId) {
        if (!checkId) {
            showAlert('error', 'ID pengecekan tidak valid');
            return;
        }
        
        // Show modal and loading state
        showModal();
        showLoading(true);
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch(`/warga/riwayat/${checkId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken || ''
                }
            });
            
            if (!response.ok) {
                let errorMessage;
                
                switch(response.status) {
                    case 404:
                        errorMessage = 'Data pengecekan tidak ditemukan atau sudah dihapus';
                        break;
                    case 401:
                        errorMessage = 'Anda tidak memiliki akses ke data ini';
                        break;
                    case 403:
                        errorMessage = 'Akses ditolak';
                        break;
                    case 500:
                        errorMessage = 'Terjadi kesalahan server';
                        break;
                    default:
                        errorMessage = `Terjadi kesalahan (${response.status})`;
                }
                
                throw new Error(errorMessage);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Gagal memuat data');
            }
            
            populateModal(result.data);
            showLoading(false);
            
        } catch (error) {
            console.error('Error fetching detail:', error);
            showLoading(false);
            hideModal();
            
            // Show user-friendly error message
            let userMessage;
            
            if (error.message.includes('fetch') || error.name === 'TypeError') {
                userMessage = 'Koneksi internet bermasalah. Silakan periksa koneksi Anda dan coba lagi.';
            } else if (error.message.includes('tidak ditemukan')) {
                userMessage = 'Data pengecekan tidak ditemukan atau sudah dihapus.';
            } else if (error.message.includes('akses')) {
                userMessage = 'Anda tidak memiliki akses ke data ini.';
            } else {
                userMessage = error.message || 'Terjadi kesalahan yang tidak diketahui.';
            }
            
            showAlert('error', userMessage);
        }
    }
    
    function populateModal(data) {
        if (!data) {
            console.error('No data provided to populate modal');
            showAlert('error', 'Data tidak valid');
            return;
        }
        
        console.log('Populating modal with data:', data);
        
        // Format tanggal
        let formattedDate = '-';
        if (data.tanggal_pantau) {
            try {
                const date = new Date(data.tanggal_pantau);
                formattedDate = date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            } catch (e) {
                console.error('Error formatting date:', e);
                formattedDate = data.tanggal_pantau;
            }
        }
        
        // Isi data ke modal dengan null checking
        const detailTanggal = document.getElementById('detail-tanggal');
        const detailPetugas = document.getElementById('detail-petugas');
        const detailKeterangan = document.getElementById('detail-keterangan');
        
        if (detailTanggal) detailTanggal.textContent = formattedDate;
        if (detailPetugas) {
            const petugasNama = data.kader?.nama_lengkap || data.kader?.nama || 'Sistem';
            detailPetugas.textContent = petugasNama;
        }
        if (detailKeterangan) {
            detailKeterangan.textContent = data.keterangan || 'Tidak ada keterangan';
        }
        
        // Set status badge
        const statusBadge = document.getElementById('detail-status-badge');
        if (statusBadge) {
            const status = data.status || 'Belum Dicek';
            statusBadge.textContent = status;
            
            // Reset class
            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium ';
            
            // Add status-specific classes
            switch(status) {
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
        }
        
        // Handle photo
        handleModalPhoto(data.bukti_foto);
    }
    
    function handleModalPhoto(photoUrl) {
        const fotoElement = document.getElementById('detail-foto');
        const noFotoElement = document.getElementById('no-foto');
        
        if (!fotoElement || !noFotoElement) {
            console.error('Photo elements not found in modal');
            return;
        }
        
        if (photoUrl) {
            // Reset state
            fotoElement.classList.add('hidden');
            noFotoElement.style.display = 'none';
            
            // Set up image loading
            fotoElement.onload = function() {
                console.log('Image loaded successfully');
                fotoElement.classList.remove('hidden');
                fotoElement.style.display = 'block';
                noFotoElement.style.display = 'none';
            };
            
            fotoElement.onerror = function() {
                console.error('Failed to load image:', photoUrl);
                fotoElement.classList.add('hidden');
                noFotoElement.style.display = 'block';
                
                const noFotoText = noFotoElement.querySelector('p');
                if (noFotoText) {
                    noFotoText.textContent = 'Gagal memuat foto';
                }
            };
            
            // Set the image source (this will trigger onload or onerror)
            fotoElement.src = photoUrl;
            
        } else {
            // No photo available
            fotoElement.classList.add('hidden');
            noFotoElement.style.display = 'block';
            
            const noFotoText = noFotoElement.querySelector('p');
            if (noFotoText) {
                noFotoText.textContent = 'Tidak ada foto';
            }
        }
    }
    
    // Function to refresh status on page (optional)
    function refreshCurrentStatus() {
        console.log('Status refresh functionality can be implemented here');
    }
    
// Optional: Add status refresh button event if exists
    const refreshBtn = document.getElementById('refresh-status-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshCurrentStatus);
    }
});
</script>
@endpush
@endsection