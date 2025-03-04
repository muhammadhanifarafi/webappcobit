<?php
namespace App\Http\Controllers;

use App\Models\DetailPengujian;
use App\Models\UserAcceptanceTesting;
use Illuminate\Http\Request;

class DetailUserAcceptanceTestingController extends Controller
{
    // Menampilkan daftar data
    public function index(Request $request)
    {
        $id = $request->id;
        $details = DetailPengujian::where('id_user_acceptance_testing', $request->id)->get();
        return view('detail-user-acceptance-testing.index', compact('details','id'));
    }

    public function create($id_user_acceptance_testing)
    {
        $userAcceptanceTesting = UserAcceptanceTesting::leftJoin('trx_quality_assurance_testing', 'trx_user_acceptance_testing.id_permintaan_pengembangan', '=', 'trx_quality_assurance_testing.id_permintaan_pengembangan')
        ->where('trx_user_acceptance_testing.id_user_acceptance_testing', $id_user_acceptance_testing)
        ->first(); // Ambil hasil pertama (seperti findOrFail)
        return view('detail-user-acceptance-testing.create', compact('userAcceptanceTesting'));
    }    

    // Menyimpan data baru ke database
    public function store(Request $request)
    {
        $data = $request->all();
        $detail = DetailPengujian::create($data);
        $id = $detail->id_user_acceptance_testing;
        
        return redirect()->route('detail-user-acceptance-testing.index', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    
    }

    // Menampilkan form untuk mengedit data
    public function edit($id)
    {
        $detail = DetailPengujian::leftJoin('trx_user_acceptance_testing', 'trx_user_acceptance_testing.id_user_acceptance_testing', '=', 'detail_user_acceptance_testing.id_user_acceptance_testing')
        ->leftJoin('trx_quality_assurance_testing', 'trx_quality_assurance_testing.id_permintaan_pengembangan', '=', 'trx_user_acceptance_testing.id_permintaan_pengembangan')
        ->where('detail_user_acceptance_testing.id_detail_user_acceptance_testing', $id)
        ->first(); // Ambil hasil pertama (seperti findOrFail)

        return view('detail-user-acceptance-testing.edit', compact('detail'));
    }

    // Memperbarui data
    public function update(Request $request, $id)
    {
        $data = $request->all(); 
        $detail = DetailPengujian::findOrFail($id);
        $id_uat = $detail->id_user_acceptance_testing;
        $detail->update($data);

        return redirect()->route('detail-user-acceptance-testing.index', ['id' => $id_uat])->with('success', 'Data berhasil disimpan!');
    }

    // Menghapus data
    public function destroy($id)
    {
        $detail = DetailPengujian::findOrFail($id);
        $id_uat = $detail->id_user_acceptance_testing;
        $detail->delete();

        return redirect()->route('detail-user-acceptance-testing.index', ['id' => $id_uat])->with('success', 'Data berhasil disimpan!');
    }
}