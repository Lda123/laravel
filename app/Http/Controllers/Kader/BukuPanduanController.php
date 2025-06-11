<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\BukuPanduanKader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BukuPanduanController extends Controller
{
    public function index()
    {
        $bukuPanduan = BukuPanduanKader::orderBy('tahun_terbit', 'desc')->get();
        
        // Debug: Log untuk memeriksa data
        foreach ($bukuPanduan as $buku) {
            Log::info('Buku: ' . $buku->judul);
            Log::info('Cover Image: ' . $buku->cover_image);
            Log::info('File Pdf: ' . $buku->file_pdf);
            Log::info('Cover Exists: ' . ($this->checkCoverExists($buku->cover_image) ? 'Yes' : 'No'));
            Log::info('File Exists: ' . ($this->checkFileExists($buku->file_pdf) ? 'Yes' : 'No'));
        }
        
        return view('kader.buku_panduan', compact('bukuPanduan'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        
        $bukuPanduan = BukuPanduanKader::when($searchTerm, function($query) use ($searchTerm) {
                return $query->where('judul', 'like', "%$searchTerm%")
                        ->orWhere('deskripsi', 'like', "%$searchTerm%")
                        ->orWhere('penulis', 'like', "%$searchTerm%")
                        ->orWhere('kelas', 'like', "%$searchTerm%");
            })
            ->orderBy('tahun_terbit', 'desc')
            ->get();
            
        return view('kader.buku_panduan', compact('bukuPanduan', 'searchTerm'));
    }

    /**
     * Download PDF file
     */
    public function downloadPdf($id)
    {
        try {
            $buku = BukuPanduanKader::findOrFail($id);
            
            if (!$buku->file_pdf) {
                return back()->with('error', 'File PDF tidak tersedia');
            }

            $filePath = 'bukupanduan/' . $buku->file_pdf;
            
            if (!Storage::disk('public')->exists($filePath)) {
                Log::error("File PDF tidak ditemukan: {$filePath}");
                return back()->with('error', 'File PDF tidak ditemukan');
            }

            $fullPath = Storage::disk('public')->path($filePath);
            $fileName = $this->sanitizeFilename($buku->judul) . '.pdf';

            return Response::download($fullPath, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Error downloading PDF: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh file');
        }
    }

    public function streamPdf($id)
    {
        try {
            $buku = BukuPanduanKader::findOrFail($id);
            
            if (!$buku->file_pdf) {
                abort(404, 'File PDF tidak tersedia');
            }

            $filePath = 'bukupanduan/' . $buku->file_pdf;
            
            if (!Storage::disk('public')->exists($filePath)) {
                abort(404, 'File PDF tidak ditemukan');
            }

            $fullPath = Storage::disk('public')->path($filePath);

            return Response::file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $this->sanitizeFilename($buku->judul) . '.pdf"'
            ]);

        } catch (\Exception $e) {
            Log::error('Error streaming PDF: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat membuka file');
        }
    }

    /**
     * Serve cover image
     */
    public function serveCover($filename)
    {
        try {
            $filePath = 'bukupanduan/covers/' . $filename;
            
            if (!Storage::disk('public')->exists($filePath)) {
                $defaultCover = public_path('images/default-book-cover.jpg');
                if (file_exists($defaultCover)) {
                    return Response::file($defaultCover);
                }
                abort(404, 'Cover image tidak ditemukan');
            }

            $fullPath = Storage::disk('public')->path($filePath);
            $mimeType = $this->getMimeType($fullPath);

            return Response::file($fullPath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000',
            ]);

        } catch (\Exception $e) {
            Log::error('Error serving cover: ' . $e->getMessage());
            $defaultCover = public_path('images/default-book-cover.jpg');
            if (file_exists($defaultCover)) {
                return Response::file($defaultCover);
            }
            abort(404, 'Cover image tidak ditemukan');
        }
    }
    /**
     * Get file info (for AJAX requests)
     */
    public function getFileInfo($id)
    {
        try {
            $buku = BukuPanduanKader::findOrFail($id);
            
            $info = [
                'id' => $buku->id,
                'judul' => $buku->judul,
                'penulis' => $buku->penulis,
                'has_pdf' => !empty($buku->file_pdf),
                'has_cover' => !empty($buku->cover_image),
                'pdf_exists' => $this->checkFileExists($buku->file_pdf),
                'cover_exists' => $this->checkCoverExists($buku->cover_image),
                'file_size' => null,
                'cover_url' => $this->getCoverUrl($buku->cover_image),
                'download_url' => route('buku-panduan.download', $buku->id),
                'stream_url' => route('buku-panduan.stream', $buku->id),
            ];

            // Get file size if exists
            if ($info['pdf_exists']) {
                $filePath = 'bukupanduan/' . $buku->file_pdf;
                $info['file_size'] = Storage::disk('public')->size($filePath);
                $info['file_size_formatted'] = $this->formatBytes($info['file_size']);
            }

            return response()->json($info);

        } catch (\Exception $e) {
            Log::error('Error getting file info: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        }
    }

    /**
     * Method untuk debugging
     */
    public function debug()
    {
        $bukuPanduan = BukuPanduanKader::all();
        
        $output = "<style>body{font-family:Arial;margin:20px;} .book{border:1px solid #ddd;padding:15px;margin:10px 0;} .status{padding:5px;margin:5px 0;} .exists{background:#d4edda;color:#155724;} .not-exists{background:#f8d7da;color:#721c24;}</style>";
        
        foreach ($bukuPanduan as $buku) {
            $output .= "<div class='book'>";
            $output .= "<h3>{$buku->judul}</h3>";
            $output .= "<p><strong>ID:</strong> {$buku->id}</p>";
            $output .= "<p><strong>Cover Image:</strong> {$buku->cover_image}</p>";
            $output .= "<p><strong>File Pdf:</strong> {$buku->file_pdf}</p>";
            
            // Check cover
            if ($buku->cover_image) {
                $coverExists = $this->checkCoverExists($buku->cover_image);
                $coverClass = $coverExists ? 'exists' : 'not-exists';
                $coverStatus = $coverExists ? 'EXISTS' : 'NOT FOUND';
                $output .= "<div class='status {$coverClass}'>Cover Status: {$coverStatus}</div>";
                $output .= "<p><strong>Cover URL:</strong> " . $this->getCoverUrl($buku->cover_image) . "</p>";
                
                if ($coverExists) {
                    $output .= "<img src='" . $this->getCoverUrl($buku->cover_image) . "' style='max-width:100px;max-height:150px;' />";
                }
            }
            
            // Check PDF
            if ($buku->file_pdf) {
                $fileExists = $this->checkFileExists($buku->file_pdf);
                $fileClass = $fileExists ? 'exists' : 'not-exists';
                $fileStatus = $fileExists ? 'EXISTS' : 'NOT FOUND';
                $output .= "<div class='status {$fileClass}'>PDF Status: {$fileStatus}</div>";
                
                if ($fileExists) {
                    $filePath = 'bukupanduan/' . $buku->file_pdf;
                    $fileSize = Storage::disk('public')->size($filePath);
                    $output .= "<p><strong>File Size:</strong> " . $this->formatBytes($fileSize) . "</p>";
                    $output .= "<p><strong>Download URL:</strong> <a href='" . route('buku-panduan.download', $buku->id) . "' target='_blank'>Download</a></p>";
                    $output .= "<p><strong>Stream URL:</strong> <a href='" . route('buku-panduan.stream', $buku->id) . "' target='_blank'>View PDF</a></p>";
                }
            }
            
            $output .= "</div><hr>";
        }
        
        return response($output);
    }

    // Helper Methods
    private function checkFileExists($filename)
    {
        if (!$filename) return false;
        return Storage::disk('public')->exists('bukupanduan/' . $filename);
    }

    private function checkCoverExists($filename)
    {
        if (!$filename) return false;
        // Sesuaikan dengan struktur folder yang sebenarnya
        return Storage::disk('public')->exists('bukupanduan/covers/' . $filename);
    }

    private function getCoverUrl($filename)
    {
        if (!$filename || !$this->checkCoverExists($filename)) {
            return asset('images/default-book-cover.jpg');
        }
        return route('buku-panduan.cover', ['filename' => $filename]);
    }

    private function sanitizeFilename($filename)
    {
        // Remove special characters and replace spaces with dashes
        $filename = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $filename);
        $filename = preg_replace('/\s+/', '-', trim($filename));
        return $filename;
    }

    private function getMimeType($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
        ];
        
        return $mimeTypes[$extension] ?? 'image/jpeg';
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}