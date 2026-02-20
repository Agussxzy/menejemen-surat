<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SuratKeluarController extends Controller
{
    /**
     * Generate automatic nomor surat
     */
    private function generateNomorSurat(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the latest number for this month and year
        $latest = SuratKeluar::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('id', 'desc')
                    ->first();
                    
        $number = 1;
        if ($latest) {
            // Extract number from existing format like "001/INSTANSI/II/2023"
            $parts = explode('/', $latest->nomor_surat);
            if (isset($parts[0])) {
                $number = intval($parts[0]) + 1;
            }
        }
        
        // Format number with leading zeros
        $formattedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        
        // Customize the institution code as needed
        return "{$formattedNumber}/INSTANSI/" . romanNumerals($month) . "/" . $year;
    }

    /**
     * Convert numeric month to roman numerals
     */
    private function romanNumerals($num): string
    {
        $n = intval($num);
        $res = '';
     
        $roman = array(
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1
        );
     
        foreach ($roman as $rom => $number) {
            $matches = intval($n / $number);
            $res .= str_repeat($rom, $matches);
            $n = $n % $number;
        }
     
        return $res;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = SuratKeluar::with(['user']);

        // Add filters if provided
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nomor_surat', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('tujuan', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('perihal', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal_surat', [$request->tanggal_mulai, $request->tanggal_selesai]);
        } elseif ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_surat', '>=', $request->tanggal_mulai);
        } elseif ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_surat', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('klasifikasi')) {
            $query->where('klasifikasi', $request->klasifikasi);
        }

        $perPage = $request->get('per_page', 10);
        $suratKeluar = $query->orderBy('tanggal_surat', 'desc')->paginate($perPage);

        return response()->json([
            'message' => 'Data surat keluar berhasil diambil.',
            'data' => $suratKeluar
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tanggal_surat' => 'required|date',
            'tujuan' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'klasifikasi' => 'required|in:rahasia,penting,umum',
            'penandatangan' => 'required|string|max:255',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file upload if present
        $fileName = null;
        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('surat_keluar', $fileName, 'public');
        }

        $suratKeluar = SuratKeluar::create([
            'nomor_surat' => $this->generateNomorSurat(),
            'tanggal_surat' => $request->tanggal_surat,
            'tujuan' => $request->tujuan,
            'perihal' => $request->perihal,
            'klasifikasi' => $request->klasifikasi,
            'tembusan' => $request->tembusan ?? null,
            'penandatangan' => $request->penandatangan,
            'file_surat' => $fileName,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Surat keluar berhasil ditambahkan.',
            'data' => $suratKeluar->load('user')
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $suratKeluar = SuratKeluar::with(['user'])->findOrFail($id);

        return response()->json([
            'message' => 'Data surat keluar berhasil diambil.',
            'data' => $suratKeluar
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $suratKeluar = SuratKeluar::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tanggal_surat' => 'required|date',
            'tujuan' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'klasifikasi' => 'required|in:rahasia,penting,umum',
            'penandatangan' => 'required|string|max:255',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file upload if present
        $fileName = $suratKeluar->file_surat; // Keep existing file
        if ($request->hasFile('file_surat')) {
            // Delete old file if exists
            if ($suratKeluar->file_surat) {
                Storage::disk('public')->delete('surat_keluar/' . $suratKeluar->file_surat);
            }

            $file = $request->file('file_surat');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('surat_keluar', $fileName, 'public');
        }

        $suratKeluar->update([
            'tanggal_surat' => $request->tanggal_surat,
            'tujuan' => $request->tujuan,
            'perihal' => $request->perihal,
            'klasifikasi' => $request->klasifikasi,
            'tembusan' => $request->tembusan ?? null,
            'penandatangan' => $request->penandatangan,
            'file_surat' => $fileName
        ]);

        return response()->json([
            'message' => 'Surat keluar berhasil diperbarui.',
            'data' => $suratKeluar->load('user')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $suratKeluar = SuratKeluar::findOrFail($id);

        // Delete file if exists
        if ($suratKeluar->file_surat) {
            Storage::disk('public')->delete('surat_keluar/' . $suratKeluar->file_surat);
        }

        $suratKeluar->delete();

        return response()->json([
            'message' => 'Surat keluar berhasil dihapus.'
        ]);
    }

    /**
     * Download the specified file.
     *
     * @param int $id
     * @return mixed
     */
    public function downloadFile(int $id)
    {
        $suratKeluar = SuratKeluar::findOrFail($id);

        if (!$suratKeluar->file_surat) {
            return response()->json([
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        $filePath = 'public/surat_keluar/' . $suratKeluar->file_surat;

        if (!Storage::exists($filePath)) {
            return response()->json([
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        return Storage::download($filePath, $suratKeluar->file_surat);
    }
}