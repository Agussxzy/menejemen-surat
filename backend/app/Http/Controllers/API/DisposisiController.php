<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DisposisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Disposisi::with(['suratMasuk', 'dariUser', 'kepadaUser']);

        // Filter berdasarkan user yang menerima disposisi
        if ($request->filled('kepada_user_id')) {
            $query->where('kepada_user_id', $request->kepada_user_id);
        } else {
            // Jika tidak ada filter, tampilkan disposisi yang dikirim oleh user atau diterima oleh user
            $userId = auth()->id();
            $query->where(function($q) use ($userId) {
                $q->where('dari_user_id', $userId)
                  ->orWhere('kepada_user_id', $userId);
            });
        }

        // Filter berdasarkan surat masuk
        if ($request->filled('surat_masuk_id')) {
            $query->where('surat_masuk_id', $request->surat_masuk_id);
        }

        // Filter berdasarkan status dibaca
        if ($request->filled('dibaca')) {
            $query->where('dibaca', $request->dibaca === 'true' || $request->dibaca == 1);
        }

        $perPage = $request->get('per_page', 10);
        $disposisi = $query->orderBy('tanggal_disposisi', 'desc')->paginate($perPage);

        return response()->json([
            'message' => 'Data disposisi berhasil diambil.',
            'data' => $disposisi
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
            'surat_masuk_id' => 'required|exists:surat_masuk,id',
            'kepada_user_id' => 'required|exists:users,id',
            'catatan' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Pastikan user yang membuat disposisi adalah pimpinan
        $user = auth()->user();
        if ($user->role->name !== 'pimpinan') {
            return response()->json([
                'message' => 'Hanya pimpinan yang dapat membuat disposisi.'
            ], 403);
        }

        // Pastikan surat masuk milik instansi ini
        $suratMasuk = SuratMasuk::findOrFail($request->surat_masuk_id);

        $disposisi = Disposisi::create([
            'surat_masuk_id' => $request->surat_masuk_id,
            'dari_user_id' => auth()->id(),
            'kepada_user_id' => $request->kepada_user_id,
            'catatan' => $request->catatan ?? null,
            'tanggal_disposisi' => now()
        ]);

        return response()->json([
            'message' => 'Disposisi berhasil ditambahkan.',
            'data' => $disposisi->load(['suratMasuk', 'dariUser', 'kepadaUser'])
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
        $disposisi = Disposisi::with(['suratMasuk', 'dariUser', 'kepadaUser'])->findOrFail($id);

        // Tandai disposisi sebagai sudah dibaca jika yang mengakses adalah penerima
        if ($disposisi->kepada_user_id == auth()->id()) {
            $disposisi->update(['dibaca' => true]);
        }

        return response()->json([
            'message' => 'Data disposisi berhasil diambil.',
            'data' => $disposisi
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
        $disposisi = Disposisi::findOrFail($id);

        // Hanya penerima disposisi yang bisa memperbarui status dibaca
        if ($disposisi->kepada_user_id != auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengubah data ini.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'dibaca' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $disposisi->update([
            'dibaca' => $request->dibaca ?? $disposisi->dibaca
        ]);

        return response()->json([
            'message' => 'Status disposisi berhasil diperbarui.',
            'data' => $disposisi->load(['suratMasuk', 'dariUser', 'kepadaUser'])
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
        $disposisi = Disposisi::findOrFail($id);

        // Hanya admin atau yang membuat disposisi yang bisa menghapus
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $disposisi->dari_user_id != auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus data ini.'
            ], 403);
        }

        $disposisi->delete();

        return response()->json([
            'message' => 'Disposisi berhasil dihapus.'
        ]);
    }
}