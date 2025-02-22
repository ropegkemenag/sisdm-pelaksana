<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Models\SatkerModel;
use App\Models\SimpegModel;

class Pengaturan extends BaseController
{
    public function index()
    {
        $smodel = new SatkerModel();
        $seting = new PengaturanModel();
        $data['seting_pertama'] = $seting->where('kode_satker',session('kelola'))->where('jenis',1)->first();
        $data['seting_pindah'] = $seting->where('kode_satker',session('kelola'))->where('jenis',2)->first();
        $data['unit'] = $smodel->where('KODE_SATUAN_KERJA',session('kelola'))->findAll();
        return view('admin/pengaturan/pengaturan',$data);
    }

    public function cariNip()
    {
        $simpeg = new SimpegModel;
        $nip = $this->request->getGet('nip_kepala');
        $ceknip = $simpeg->getPegawai($nip);

        if(!$ceknip){
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan !',
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Data ditemukan !',
            'data' => $ceknip
        ]);
    }

    public function save()
    {
        // dd($_REQUEST);
        if (!session('nip')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $validation = \Config\Services::validation();
        $rules = [
            'nama_pejabat'      => 'required|min_length[3]|max_length[100]',
            'kode_satker'       => 'required',
            'nip_pejabat'       => 'required',
            'jabatan'           => 'required',
            'tempat_ditetapkan' => 'required',
            'kode_surat' => 'required',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        $id = $this->request->getPost('id');
        $msatker = new SatkerModel();
        $nama_satker = $msatker->where('KODE_SATUAN_KERJA',$this->request->getPost('kode_satker'))->select('SATUAN_KERJA')->first();
        
        $data = [
            'nip_pejabat'       => $this->request->getPost('nip_pejabat'),
            'nama_pejabat'      => $this->request->getPost('nama_pejabat'),
            'an_pejabat'        => $this->request->getPost('an_pejabat'),
            'jabatan'           => $this->request->getPost('jabatan'),
            'tempat_ditetapkan' => $this->request->getPost('tempat_ditetapkan'),
            'kode_surat'        => $this->request->getPost('kode_surat'),
            'keputusan_atas'    => $this->request->getPost('keputusan_atas') ?? NULL,
            'keputusan_bawah'   => $this->request->getPost('keputusan_bawah') ?? NULL,
            'org_tata_kerja'    => $this->request->getPost('org_tata_kerja') ?? NULL,
            'kode_satker'       => $this->request->getPost('kode_satker'),
            'nip_pengelola'     => session('nip'),
            'nama_satker'       => $nama_satker->SATUAN_KERJA ?? NULL,
            'jenis'             => $this->request->getPost('jenis'),
        ];
        $model = new PengaturanModel();
        if ($id) {
            $model->update($id, $data);
            $message = 'Data berhasil diperbarui!';
        } else {
            $model->insert($data);
            $message = 'Data berhasil disimpan!';
        }
        
        return redirect()->to('/pengaturan')->with('success', $message);
        
    }

    public function petunjuk()
    {
        return view('petunjuk');
    }
}