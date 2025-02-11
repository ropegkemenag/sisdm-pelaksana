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
        $data['seting'] = $seting->where('kode_satker',session('kelola'))->first();
        $data['unit'] = $smodel->where('KODE_SATUAN_KERJA',session('kelola'))->findAll();
        return view('admin/pengaturan',$data);
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
        $validation = \Config\Services::validation();
        $rules = [
            'nama_kepala'   => 'required|min_length[3]|max_length[100]',
            'kode_satker'   => 'required',
            'nip_pengelola' => 'required',
            'nip_kepala'    => 'required',
            'jabatan_kepala'=> 'required',
            'lokasi_ttd'    => 'required',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        $id = $this->request->getPost('id');
        $msatker = new SatkerModel();
        $nama_satker = $msatker->where('KODE_SATUAN_KERJA',$this->request->getPost('kode_satker'))->select('SATUAN_KERJA')->first();
        
        $data = [
            'nama_kepala'   => $this->request->getPost('nama_kepala'),
            'kode_satker'   => $this->request->getPost('kode_satker'),
            'nip_pengelola' => $this->request->getPost('nip_pengelola'),
            'nip_kepala'    => $this->request->getPost('nip_kepala'),
            'nama_satker'   => $nama_satker->SATUAN_KERJA ?? NULL,
            'lokasi_ttd'    => $this->request->getPost('lokasi_ttd'),
            'jabatan_kepala'=> $this->request->getPost('jabatan_kepala')
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
}