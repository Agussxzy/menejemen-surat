<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Generate laporan surat masuk
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateLaporanSuratMasuk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        // Query data surat masuk
        $suratMasuk = SuratMasuk::whereBetween('tanggal_surat', [$tanggalMulai, $tanggalSelesai])
            ->with(['user'])
            ->get();

        // Prepare data for report
        $reportData = [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'total_surat' => $suratMasuk->count(),
            'data' => $suratMasuk->map(function ($item) {
                return [
                    'nomor_surat' => $item->nomor_surat,
                    'tanggal_surat' => $item->tanggal_surat,
                    'pengirim' => $item->pengirim,
                    'perihal' => $item->perihal,
                    'klasifikasi' => $item->klasifikasi,
                    'status' => $item->status,
                    'user_pembuat' => $item->user->name
                ];
            })
        ];

        // Simpan ke tabel laporan
        $laporan = Laporan::create([
            'judul' => "Laporan Surat Masuk {$tanggalMulai} s.d {$tanggalSelesai}",
            'jenis' => 'surat_masuk',
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'data_laporan' => $reportData,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Laporan surat masuk berhasil dibuat.',
            'data' => $laporan
        ]);
    }

    /**
     * Generate laporan surat keluar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateLaporanSuratKeluar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        // Query data surat keluar
        $suratKeluar = SuratKeluar::whereBetween('tanggal_surat', [$tanggalMulai, $tanggalSelesai])
            ->with(['user'])
            ->get();

        // Prepare data for report
        $reportData = [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'total_surat' => $suratKeluar->count(),
            'data' => $suratKeluar->map(function ($item) {
                return [
                    'nomor_surat' => $item->nomor_surat,
                    'tanggal_surat' => $item->tanggal_surat,
                    'tujuan' => $item->tujuan,
                    'perihal' => $item->perihal,
                    'klasifikasi' => $item->klasifikasi,
                    'penandatangan' => $item->penandatangan,
                    'user_pembuat' => $item->user->name
                ];
            })
        ];

        // Simpan ke tabel laporan
        $laporan = Laporan::create([
            'judul' => "Laporan Surat Keluar {$tanggalMulai} s.d {$tanggalSelesai}",
            'jenis' => 'surat_keluar',
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'data_laporan' => $reportData,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Laporan surat keluar berhasil dibuat.',
            'data' => $laporan
        ]);
    }

    /**
     * Display a listing of the reports.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Laporan::with(['user']);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $perPage = $request->get('per_page', 10);
        $laporan = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'message' => 'Data laporan berhasil diambil.',
            'data' => $laporan
        ]);
    }

    /**
     * Display the specified report.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $laporan = Laporan::with(['user'])->findOrFail($id);

        return response()->json([
            'message' => 'Data laporan berhasil diambil.',
            'data' => $laporan
        ]);
    }

    /**
     * Remove the specified report from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $laporan = Laporan::findOrFail($id);

        $laporan->delete();

        return response()->json([
            'message' => 'Laporan berhasil dihapus.'
        ]);
    }
}