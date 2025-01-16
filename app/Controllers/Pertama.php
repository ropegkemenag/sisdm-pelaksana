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
        $data['unit'] = $smodel->where('KODE_ATASAN',session('kelola'))->findAll();
        return view('pertama/index', $data);
    }

    public function getdata()
    {
      $db = \Config\Database::connect('simpeg', false);
      $builder = $db->table('TEMP_PEGAWAI')->select('NIP, NIP_BARU, NAMA_LENGKAP, TAMPIL_JABATAN, SATKER_2, SATKER_3, SATKER_4, STATUS_PEGAWAI')->like('KODE_SATUAN_KERJA', kodekepala(session('kelola')), 'after');

      return DataTable::of($builder)
      ->add('action', function($row){
          return '<a href="javascript:;" type="button" class="btn btn-primary btn-sm" onclick="add('.$row->NIP_BARU.')">Add</a>';
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
      $builder = $db->table('tr_usul a');
      $builder->select('a.nip,b.NAMA_LENGKAP,b.TAMPIL_JABATAN,b.SATKER_2');
      $builder->join('TEMP_PEGAWAI b', 'a.nip = b.NIP_BARU');
      $query = $builder->get();
      
    //   $builder = $db->table('tr_usul')->select('NIP, NIP_BARU, NAMA_LENGKAP, TAMPIL_JABATAN, SATKER_2, SATKER_3, SATKER_4, STATUS_PEGAWAI')->like('KODE_SATUAN_KERJA', kodekepala(session('kelola')), 'after');

      return DataTable::of($builder)
      ->add('action', function($row){
          return '<a href="javascript:;" type="button" class="btn btn-primary btn-sm" onclick="add('.$row->nip.')">Add</a>';
      })->filter(function ($builder, $request) {

            $builder->where('status', 1);

        if ($request->unit)
            $builder->like('kode_satker', kodekepala(session('kelola')), 'after');

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
}
