<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SimpegModel;
use App\Models\SatkerModel;
use App\Models\UsulModel;
use \Hermawan\DataTables\DataTable;

class Pertama extends BaseController
{
    public function index()
    {
        $smodel = new SatkerModel;
        $umodel = new UsulModel();
        $data['unit'] = $smodel->where('KODE_ATASAN',session('kelola'))->findAll();
        $data['total_proses'] = $umodel->where('jenis',1)->countAll();

        return view('pertama/index', $data);
    }

    public function getdata()
    {
      $db = \Config\Database::connect('simpeg', false);
      $builder = $db->table('TEMP_PEGAWAI')->select('NIP, NIP_BARU, NAMA_LENGKAP, TAMPIL_JABATAN, SATKER_2, SATKER_3, SATKER_4, STATUS_PEGAWAI')->like('KODE_SATUAN_KERJA', kodekepala(session('kelola')), 'after');

      return DataTable::of($builder)
      ->add('action', function($row){
          return '<a href="javascript:;" type="button" class="btn btn-primary btn-sm" onclick="add(\''.$row->NIP_BARU.'\')">Add</a>';
      })->filter(function ($builder, $request) {

            $builder->where('STATUS_PEGAWAI', 'PNS');

        if ($request->unit)
            $builder->like('KODE_SATUAN_KERJA', kodekepala($request->unit), 'after');

      })
      ->toJson(true);
    }

    public function getdataproses()
    {
      $db = \Config\Database::connect('default', false);
      $builder = $db->table('tr_usul a')->select('a.nip as nip,b.NAMA_LENGKAP as nama_lengkap,b.TAMPIL_JABATAN as tampil_jabatan,b.SATKER_2 as satker')->join('TEMP_PEGAWAI b', 'a.nip = b.NIP_BARU','left');

      $jabatan_baru = $db->table('tm_jabatan')->get()->getResultArray();

      return DataTable::of($builder)
      ->add('action', function($row){
          return '<a href="javascript:;" type="button" class="btn btn-primary btn-sm" onclick="proses(this)" data-nip="' . $row->nip . '">Proses</a>';
      })->add('jabatan_baru', function($row) use ($jabatan_baru){
        $options = '';
        foreach ($jabatan_baru as $value) {
            $options .= '<option value="'.$value['id'].'">'.$value['jabatan'].'</option>';
        }
        return '<select class="form-select jabatan-baru">'.$options.'</select>';
      })
      ->add('no_sk', function(){
        return '<input type="text" class="form-conrtol" id="no_sk" name="no_sk" value="">';
      })
      ->add('tgl_sk', function(){
        return '<input type="date" class="form-conrtol" id="tgl_sk" name="tgl_sk" value="">';
      })
      ->add('tmt', function(){
        return '<input type="date" class="form-conrtol" id="tmt" name="tmt" value="">';
      })
      
      
      ->toJson(true);
    }

    public function add($nip,$type) {
        $model = new UsulModel;
        $simpeg = new SimpegModel;
        $cek = $model->where(['nip'=>$nip,'status'=>1])->find();

        if($cek){
            return $this->response->setJSON(['status'=>'error','message'=>'Pegawai sedang dalam proses']);
        }else{
            $pegawai = $simpeg->getPegawai($nip);

            $param = [
                'jenis' => $type,
                'nip' => $nip,
                'kode_satker' => session('kelola')
            ];

            $insert = $model->insert($param);
            return $this->response->setJSON(['status'=>'success']);
        }
    }

    public function proses()
    {
        $nip = $this->request->getPost('nip');
        $jabatanBaru = $this->request->getPost('jabatan_baru');
        $noSk = $this->request->getPost('no_sk');
        $tglSk = $this->request->getPost('tgl_sk');
        $tmt = $this->request->getPost('tmt');

        if (!$noSk || !$tglSk || !$tmt) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua input harus diisi!',
            ]);
        }

        // Update data di database
        $model = new UsulModel();
        $data = [
            'id_jabatan_baru' => $jabatanBaru,
            'no_sk' => $noSk,
            'tgl_sk' => $tglSk,
            'tmt_sk' => $tmt,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $update = $model->where('nip', $nip)->set($data)->update();

        if ($update) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil diproses!',
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memproses data!',
            ]);
        }
    }
}
