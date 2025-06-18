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
                <select id="kecamatan" name="kecamatan"
                    class="input-focus-effect w-full py-2 px-3 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Kecamatan</option>
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
                <select id="kelurahan" name="kelurahan" {{ !request('kecamatan') ? 'disabled' : '' }}
                    class="input-focus-effect w-full py-2 px-3 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Kelurahan</option>
                    @foreach($kelurahans as $kelurahan)
                    <option value="{{ $kelurahan->id }}" {{ request('kelurahan') == $kelurahan->id ? 'selected' : '' }}>
                        {{ $kelurahan->nama_kelurahan }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- RW Dropdown -->
            <div>
                <label for="rw" class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                <select id="rw" name="rw" {{ !request('kelurahan') ? 'disabled' : '' }}
                    class="input-focus-effect w-full py-2 px-3 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih RW</option>
                    @foreach($rws as $rw)
                    <option value="{{ $rw->id }}" {{ request('rw') == $rw->id ? 'selected' : '' }}>
                        RW {{ str_pad($rw->nomor_rw, 2, '0', STR_PAD_LEFT) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- RT Dropdown -->
            <div>
                <label for="rt" class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                <select id="rt" name="rt" {{ !request('rw') ? 'disabled' : '' }}
                    class="input-focus-effect w-full py-2 px-3 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih RT</option>
                    @foreach($rts as $rt)
                    <option value="{{ $rt->id }}" {{ request('rt') == $rt->id ? 'selected' : '' }}>
                        RT {{ str_pad($rt->nomor_rt, 2, '0', STR_PAD_LEFT) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex justify-center space-x-2">
                <button type="submit" id="btnFilter" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Terapkan Filter
                </button>
                @if(request()->anyFilled(['kecamatan', 'kelurahan', 'rw', 'rt']))
                <a href="{{ route('data-warga') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Terakhir</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($wargas as $warga)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $warga->nama_lengkap ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $warga->nik ?? '-' }}
                        </td>
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
                                $status = $latestTracking ? $latestTracking->kategori_masalah : 'Belum Diperiksa';
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            @if(request()->anyFilled(['kecamatan', 'kelurahan', 'rw', 'rt']))
                                Tidak ada data warga yang sesuai dengan kriteria filter
                            @else
                                Tidak ada data warga yang ditemukan
                            @endif
                        </td>
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
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Function to reset dependent dropdowns
    function resetDependentDropdowns(startFrom) {
        if (startFrom === 'kecamatan') {
            $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
            $('#rw').html('<option value="">Pilih RW</option>').prop('disabled', true);
            $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
        } else if (startFrom === 'kelurahan') {
            $('#rw').html('<option value="">Pilih RW</option>').prop('disabled', true);
            $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
        } else if (startFrom === 'rw') {
            $('#rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
        }
    }

    // Function to toggle dropdown loading state
    function toggleDropdownLoading(dropdownId, isLoading) {
        const $dropdown = $(`#${dropdownId}`);
        if (isLoading) {
            $dropdown.prop('disabled', true);
            $dropdown.addClass('opacity-50');
            $dropdown.after('<div class="loading-spinner absolute right-3 top-3"><i class="fas fa-spinner fa-spin"></i></div>');
        } else {
            $dropdown.removeClass('opacity-50');
            $(`.loading-spinner`).remove();
        }
    }

    // Kecamatan change handler
    $('#kecamatan').change(function() {
        const kecamatan_id = $(this).val();
        resetDependentDropdowns('kecamatan');

        if (kecamatan_id) {
            toggleDropdownLoading('kelurahan', true);

            $.ajax({
                url: "{{ route('data-warga.get-kelurahan') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    kecamatan_id: kecamatan_id
                },
                success: function(data) {
                    $('#kelurahan').html(data.options).prop('disabled', false);
                    toggleDropdownLoading('kelurahan', false);
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#kelurahan').html('<option value="">Gagal memuat data</option>');
                    toggleDropdownLoading('kelurahan', false);
                }
            });
        }
    });

    // Kelurahan change handler
    $('#kelurahan').change(function() {
        const kelurahan_id = $(this).val();
        resetDependentDropdowns('kelurahan');

        if (kelurahan_id) {
            toggleDropdownLoading('rw', true);

            $.ajax({
                url: "{{ route('data-warga.get-rw') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    kelurahan_id: kelurahan_id
                },
                success: function(data) {
                    $('#rw').html(data.options).prop('disabled', false);
                    toggleDropdownLoading('rw', false);
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#rw').html('<option value="">Gagal memuat data</option>');
                    toggleDropdownLoading('rw', false);
                }
            });
        }
    });

    // RW change handler
    $('#rw').change(function() {
        const rw_id = $(this).val();
        resetDependentDropdowns('rw');

        if (rw_id) {
            toggleDropdownLoading('rt', true);

            $.ajax({
                url: "{{ route('data-warga.get-rt') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    rw_id: rw_id
                },
                success: function(data) {
                    $('#rt').html(data.options).prop('disabled', false);
                    toggleDropdownLoading('rt', false);
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#rt').html('<option value="">Gagal memuat data</option>');
                    toggleDropdownLoading('rt', false);
                }
            });
        }
    });
});
</script>
@endpush