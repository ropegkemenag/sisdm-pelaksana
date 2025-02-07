<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Models\SatkerModel;

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

    public function save()
    {
        // dd($_REQUEST);
        $validation = \Config\Services::validation();
        $rules = [
            'nama_kepala'  => 'required|min_length[3]|max_length[100]',
            // 'kode_satker'  => 'required|exact_length[6]|numeric',
            'nip_pengelola' => 'required',
            'nip_kepala'   => 'required',
            'nama_satker'  => 'required|min_length[3]|max_length[100]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        $id = $this->request->getPost('id');
        $data = [
            'nama_kepala'   => $this->request->getPost('nama_kepala'),
            'kode_satker'   => $this->request->getPost('kode_satker'),
            'nip_pengelola' => $this->request->getPost('nip_pengelola'),
            'nip_kepala'    => $this->request->getPost('nip_kepala'),
            'nama_satker'   => $this->request->getPost('nama_satker'),
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