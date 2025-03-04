<?php
namespace App\Http\Controllers;

use App\Models\DetailQAT;
use App\Models\QualityAssuranceTesting;
use Illuminate\Http\Request;

class DetailQualityAssuranceTestingController extends Controller
{
    // Menampilkan daftar data
    public function index(Request $request)
    {
        $id = $request->id;
        $details = DetailQAT::where('id_quality_assurance_testing', $request->id)->get();
        return view('detail_quality_assurance_testing.index', compact('details','id'));
    }

    public function create($id_quality_assurance_testing)
    {
        $qualityAssuranceTesting = QualityAssuranceTesting::findOrFail($id_quality_assurance_testing);
        return view('detail_quality_assurance_testing.create', compact('qualityAssuranceTesting'));
    }    

    // Menyimpan data baru ke database
    public function store(Request $request)
    {
        $data = $request->all();
        $detail = DetailQAT::create($data);
        $id = $detail->id_quality_assurance_testing;
        
        return redirect()->route('detail-quality-assurance-testing.index', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    
    }

    // Menampilkan form untuk mengedit data
    public function edit($id)
    {
        $detail = DetailQAT::findOrFail($id);
        return view('detail_quality_assurance_testing.edit', compact('detail'));
    }

    // Memperbarui data
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fitur_fungsi' => 'required|string',
            'steps' => 'required|string',
            'ekspetasi' => 'required|string',
            'berhasil_gagal' => 'required|string',
            'notes' => 'nullable|string',
            'jenis' => 'required|string',
        ]);

        $detail = DetailQAT::findOrFail($id);
        $id_qat = $detail->id_quality_assurance_testing;
        $detail->update($validated);

        return redirect()->route('detail-quality-assurance-testing.index', ['id' => $id_qat])->with('success', 'Data berhasil disimpan!');
    }

    // Menghapus data
    public function destroy($id)
    {
        $detail = DetailQAT::findOrFail($id);
        $id_qat = $detail->id_quality_assurance_testing;
        $detail->delete();

        return redirect()->route('detail-quality-assurance-testing.index', ['id' => $id_qat])->with('success', 'Data berhasil disimpan!');
    }
}