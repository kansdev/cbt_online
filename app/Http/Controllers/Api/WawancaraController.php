<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Models\Pewawancara;
use App\Models\Wawancara;
use App\Models\WawancaraDetail;
use App\Models\WawancaraDeskripsi;

class WawancaraController extends Controller
{
    public function login_pewawancara(Request $request) {
        $nip = $request->nip;
        $pewawancara = Pewawancara::where('nip', $nip)->first();

        if($pewawancara) {
            return response()->json([
                'status' => 'success',
                'data' => $pewawancara
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Pewawancara tidak ditemukan'
            ], 404);
        }
    }

    public function simpan(Request $request) {

        DB::beginTransaction();
        
        try {
            $wawancara = Wawancara::create([
                'pewawancara_id' => $request->pewawancara['id'],
                'user_id' => $request->user_id,
                'nomor_pendaftaran' => $request->nomor_pendaftaran,
                'catatan' => $request->catatan,
                'kesimpulan' => $request->kesimpulan
            ]);

            foreach($request->jawaban as $kode => $skor) {
                WawancaraDetail::create([
                    'pewawancara_id' => $wawancara->id,
                    'kode_pertanyaan' => $kode,
                    'skor' => $skor
                ]);
            }

            foreach($request->deskripsi as $kategori => $deskripsi) {
                WawancaraDeskripsi::create([
                    'pewawancara_id' => $wawancara->id,
                    'kategori' => $kategori,
                    'deskripsi' => $deskripsi
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Wawancara berhasil disimpan'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function show($id) {
        $wawancara = Wawancara::with(['details', 'deskripsi', 'pewawancara'])->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $wawancara
        ]);
    }
}
