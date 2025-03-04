<?php

namespace App\Http\Controllers;

use App\Models\DetailPengujiQAT;
use App\Models\QualityAssuranceTesting;
use Illuminate\Http\Request;

class DetailTTDQualityAssuranceTestingController extends Controller
{
    // Menampilkan daftar data
    public function index(Request $request)
    {
        $id = $request->id;
        $details = DetailPengujiQAT::where('id_quality_assurance_testing', $request->id)->get();

        return view('detail-ttd-qat.index', compact('details', 'id'));
    }

    // Menampilkan form untuk menambah data
    public function create($id_quality_assurance_testing)
    {
        $qualityAssuranceTesting = QualityAssuranceTesting::findOrFail($id_quality_assurance_testing);
        return view('detail-ttd-qat.create', compact('qualityAssuranceTesting'));
    }

    // Menyimpan data baru ke database
    public function store(Request $request)
    {
        $data = $request->all();
        $detail = DetailPengujiQAT::create($data);
        $id = $detail->id_quality_assurance_testing;

        return redirect()->route('detail-ttd-qat.index', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    }

    // Menampilkan form untuk mengedit data
    public function edit($id)
    {
        $detail = DetailPengujiQAT::findOrFail($id);
        return view('detail-ttd-qat.edit', compact('detail'));
    }

    // Memperbarui data
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $detail = DetailPengujiQAT::findOrFail($id);
        $id_qat = $detail->id_quality_assurance_testing;
        $detail->update($data);

        return redirect()->route('detail-ttd-qat.index', ['id' => $id_qat])->with('success', 'Data berhasil disimpan!');
    }

    // Menghapus data
    public function destroy($id)
    {
        $detail = DetailPengujiQAT::findOrFail($id);
        $id_qat = $detail->id_quality_assurance_testing;
        $detail->delete();

        return redirect()->route('detail-ttd-qat.index', ['id' => $id_qat])->with('success', 'Data berhasil dihapus!');
    }
}
