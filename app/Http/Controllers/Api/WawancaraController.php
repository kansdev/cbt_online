<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

use App\Models\Wawancara;

class WawancaraController extends Controller
{
    public function login_pewawancara(Request $request) {
        $nip = $request->nip;
        $pewawancara = Wawancara::where('nip', $nip)->first();

        if($pewawancara) {
            return response()->json([
                'success' => true,
                'data' => $pewawancara
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pewawancara tidak ditemukan'
            ], 404);
        }
    }

    public function simpan(Request $request) {

        DB::beginTransaction();
        
        try {
            $wawancara = Wawancara::create([
                'pewawancara_id' => $request->pewawancara_id,
                'user_id' => $request->user_id,
                'nomor_pendaftaran' => $request->nomor_pendaftaran,
                'catatan' => $request->catatan,
                'kesimpulan' => $request->kesimpulan
            ]);

            foreach($request->jawaban as $kode => $skor) {
                WawancaraDetail::create([
                    'wawancara_id' => $wawancara->id,
                    'kode_pertanyaan' => $kode,
                    'skor' => $skor
                ]);
            }

            foreach($request->deskripsi as $kategori => $deskripsi) {
                WawancaraDeskripsi::create([
                    'wawancara_id' => $wawancara->id,
                    'kategori' => $kategori,
                    'deskripsi' => $deskripsi
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Wawancara berhasil disimpan'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan wawancara'
            ], 500);
        }
    }

    public function show($id) {
        $wawancara = Wawancara::with(['details', 'deskripsi', 'pewawancara'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $wawancara
        ]);
    }
}
