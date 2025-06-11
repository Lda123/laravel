<!-- resources/views/kader/laporan-bulanan.blade.php -->
@extends('layouts.kader')
@section('title', 'Laporan Bulanan')
@section('content')
<main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-[#1D3557] mb-8">Laporan Bulanan</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto">
        <!-- Download Template Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-center text-gray-800 mb-2">Download Template</h2>
                <p class="text-gray-600 text-center mb-6">Download template laporan bulanan format Word (.docx)</p>
                
                <a href="{{ route('laporan.download-template') }}"
                   download="Tamplate_Laporan_Bulanan_Kader.docx"
                   id="downloadBtn"
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors text-center">
                   Download Template
                </a>
            </div>
        </div>
            
        <!-- Upload Laporan Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-center text-gray-800 mb-2">Upload Laporan</h2>
                <form id="uploadForm" class="mt-4" enctype="multipart/form-data">
                    @csrf
                    <div class="file-upload rounded-lg p-6 text-center cursor-pointer mb-4" id="dropZone">
                        <input type="file" id="fileInput" class="hidden" accept=".docx,.pdf">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Drag & drop file atau klik untuk memilih</p>
                        <p class="text-xs text-gray-500 mt-1">Format: Word (.docx) atau PDF (.pdf)</p>
                        <p id="fileName" class="text-sm font-medium text-blue-600 mt-2 hidden"></p>
                    </div>
                    <button type="button" onclick="validateAndUpload()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
                        Upload Laporan
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// File upload handling
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileName = document.getElementById('fileName');

// Event listeners for drag-drop
dropZone.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', (e) => {
    if(e.target.files.length) {
        const file = e.target.files[0];
        const fileExt = file.name.split('.').pop().toLowerCase();
        
        if(fileExt !== 'docx' && fileExt !== 'pdf') {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Valid',
                text: 'Silakan upload file dalam format .docx atau .pdf',
                confirmButtonColor: '#3b82f6',
            });
            fileInput.value = '';
            return;
        }
        
        fileName.textContent = file.name;
        fileName.classList.remove('hidden');
        dropZone.classList.add('border-green-500', 'bg-green-50');
    }
});

// Drag and drop handlers
['dragover', 'dragenter'].forEach(event => {
    dropZone.addEventListener(event, (e) => {
        e.preventDefault();
        dropZone.classList.add('border-green-500', 'bg-green-50');
    });
});

['dragleave', 'dragend'].forEach(event => {
    dropZone.addEventListener(event, () => {
        dropZone.classList.remove('border-green-500', 'bg-green-50');
    });
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-green-500', 'bg-green-50');
    
    const file = e.dataTransfer.files[0];
    const fileExt = file.name.split('.').pop().toLowerCase();
    
    if(fileExt !== 'docx' && fileExt !== 'pdf') {
        Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Valid',
            text: 'Silakan upload file dalam format .docx atau .pdf',
            confirmButtonColor: '#3b82f6',
        });
        return;
    }
    
    fileInput.files = e.dataTransfer.files;
    fileName.textContent = file.name;
    fileName.classList.remove('hidden');
});

// Upload validation function
function validateAndUpload() {
    if(!fileInput.files.length) {
        Swal.fire({
            icon: 'error',
            title: 'Peringatan',
            text: 'Silakan pilih file terlebih dahulu',
            confirmButtonColor: '#3b82f6',
        });
        return;
    }
    
    const file = fileInput.files[0];
    const fileExt = file.name.split('.').pop().toLowerCase();
    
    if(fileExt !== 'docx' && fileExt !== 'pdf') {
        Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Valid',
            text: 'Hanya file .docx dan .pdf yang diperbolehkan',
            confirmButtonColor: '#3b82f6',
        });
        return;
    }
    
    uploadFile();
}

// Actual upload function
function uploadFile() {
    const formData = new FormData(document.getElementById('uploadForm'));
    formData.append('laporan', fileInput.files[0]);
    
    Swal.fire({
        title: 'Mengupload Laporan',
        html: 'Mohon tunggu...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch("{{ route('laporan.upload') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: 'Upload Berhasil',
            text: 'Laporan bulanan telah berhasil diupload',
            confirmButtonColor: '#10b981',
        });
        fileInput.value = '';
        fileName.textContent = '';
        fileName.classList.add('hidden');
        dropZone.classList.remove('border-green-500', 'bg-green-50');
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Upload Gagal',
            text: 'Terjadi kesalahan saat mengupload file',
            confirmButtonColor: '#ef4444',
        });
    });
}

// Download template success message
document.getElementById('downloadBtn').addEventListener('click', function(e) {
    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: 'Template Berhasil Didownload',
            text: 'Template laporan bulanan (.docx) telah berhasil didownload',
            confirmButtonColor: '#3b82f6',
        });
    }, 1000);
});
</script>
@endpush

@endsection