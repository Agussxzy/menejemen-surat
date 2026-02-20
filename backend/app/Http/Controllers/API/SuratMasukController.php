<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SuratMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = SuratMasuk::with(['user']);

        // Add filters if provided
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nomor_surat', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('pengirim', 'LIKE', "%{$searchTerm}%")
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 10);
        $suratMasuk = $query->orderBy('tanggal_surat', 'desc')->paginate($perPage);

        return response()->json([
            'message' => 'Data surat masuk berhasil diambil.',
            'data' => $suratMasuk
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
            'nomor_surat' => 'required|string|max:100|unique:surat_masuk,nomor_surat',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'klasifikasi' => 'required|in:rahasia,penting,umum',
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
            $file->storeAs('surat_masuk', $fileName, 'public');
        }

        $suratMasuk = SuratMasuk::create([
            'nomor_surat' => $request->nomor_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_diterima' => $request->tanggal_diterima,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'klasifikasi' => $request->klasifikasi,
            'file_surat' => $fileName,
            'catatan' => $request->catatan ?? null,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Surat masuk berhasil ditambahkan.',
            'data' => $suratMasuk->load('user')
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
        $suratMasuk = SuratMasuk::with(['user', 'disposisi'])->findOrFail($id);

        return response()->json([
            'message' => 'Data surat masuk berhasil diambil.',
            'data' => $suratMasuk
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
        $suratMasuk = SuratMasuk::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nomor_surat' => [
                'required',
                'string',
                'max:100',
                Rule::unique('surat_masuk')->ignore($id)
            ],
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'klasifikasi' => 'required|in:rahasia,penting,umum',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file upload if present
        $fileName = $suratMasuk->file_surat; // Keep existing file
        if ($request->hasFile('file_surat')) {
            // Delete old file if exists
            if ($suratMasuk->file_surat) {
                Storage::disk('public')->delete('surat_masuk/' . $suratMasuk->file_surat);
            }

            $file = $request->file('file_surat');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('surat_masuk', $fileName, 'public');
        }

        $suratMasuk->update([
            'nomor_surat' => $request->nomor_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_diterima' => $request->tanggal_diterima,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'klasifikasi' => $request->klasifikasi,
            'status' => $request->status ?? $suratMasuk->status,
            'file_surat' => $fileName,
            'catatan' => $request->catatan ?? null
        ]);

        return response()->json([
            'message' => 'Surat masuk berhasil diperbarui.',
            'data' => $suratMasuk->load('user')
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
        $suratMasuk = SuratMasuk::findOrFail($id);

        // Delete file if exists
        if ($suratMasuk->file_surat) {
            Storage::disk('public')->delete('surat_masuk/' . $suratMasuk->file_surat);
        }

        $suratMasuk->delete();

        return response()->json([
            'message' => 'Surat masuk berhasil dihapus.'
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
        $suratMasuk = SuratMasuk::findOrFail($id);

        if (!$suratMasuk->file_surat) {
            return response()->json([
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        $filePath = 'public/surat_masuk/' . $suratMasuk->file_surat;

        if (!Storage::exists($filePath)) {
            return response()->json([
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        return Storage::download($filePath, $suratMasuk->file_surat);
    }
}