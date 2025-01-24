<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SimpegModel;
use App\Models\SatkerModel;
use App\Models\UsulModel;
use \Hermawan\DataTables\DataTable;
use PhpOffice\PhpWord\TemplateProcessor;

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
      $builder = $db->table('tr_usul a')->select('a.nip as nip, 
                                                a.no_sk as nomer_sk, 
                                                a.tgl_sk as tgl_SK, 
                                                a.tmt_sk as tmt_SK, 
                                                a.id_jabatan_baru as id_jabatan_baru,
                                                b.NAMA_LENGKAP as nama_lengkap,
                                                b.TAMPIL_JABATAN as tampil_jabatan,
                                                b.SATKER_2 as satker')
                                                ->join('TEMP_PEGAWAI b', 'a.nip = b.NIP_BARU','left');

      $jabatan_baru = $db->table('tm_jabatan')->get()->getResultArray();

      return DataTable::of($builder)
      ->add('action', function($row){
          return '
                    <a href="javascript:;" type="button" class="btn btn-primary btn-sm" onclick="proses(this)" data-nip="' . $row->nip . '">Proses</a>
                    <a href="javascript:;" type="button" class="btn btn-warning btn-sm" onclick="generate(this)" data-nip="' . $row->nip . '">Generate</a>
                    
                ';
      })->add('jabatan_baru', function($row) use ($jabatan_baru){
        $options = '';
        foreach ($jabatan_baru as $value) {
            $selected = ($value['id'] == $row->id_jabatan_baru) ? 'selected' : '';
            $options .= '<option value="'.$value['id'].'"' . $selected . '>'.$value['jabatan'].'</option>';
        }
        return '<select class="form-select form-control jabatan-baru">'.$options.'</select>';
      })
      ->add('no_sk', function($row){
        return '<input type="text" class="form-control" id="no_sk" name="no_sk" value="'.$row->nomer_sk.'">';
      })
      ->add('tgl_sk', function($row){
        return '<input type="date" class="form-control" id="tgl_sk" name="tgl_sk" value="'.$row->tgl_SK.'">';
      })
      ->add('tmt', function($row){
        return '<input type="date" class="form-control" id="tmt" name="tmt" value="'.$row->tmt_SK.'">';
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
        $nip = $this->request->getVar('nip');
        $jabatanBaru = $this->request->getVar('jabatan_baru');
        $noSk = $this->request->getVar('no_sk');
        $tglSk = $this->request->getVar('tgl_sk');
        $tmt = $this->request->getVar('tmt');

        if (!$noSk || !$tglSk || !$tmt) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ada yang kosong, harus diisi!',
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

    public function generateDoc()
    {
        $nip = $this->request->getVar('nip');
        $db = \Config\Database::connect('default', false);
        $builder = $db->table('tr_usul a')->select('a.nip as nip, 
                                                  a.no_sk as nomer_sk, 
                                                  a.tgl_sk as tgl_SK, 
                                                  a.tmt_sk as tmt_SK, 
                                                  a.id_jabatan_baru as id_jabatan_baru,
                                                  b.NAMA_LENGKAP as nama_lengkap,
                                                  b.TAMPIL_JABATAN as tampil_jabatan,
                                                  b.SATKER_2 as satker,
                                                  b.JENJANG_PENDIDIKAN,
                                                  b.PANGKAT,
                                                  b.GOL_RUANG,
                                                  b.TAMPIL_JABATAN as jabatan_lama,
                                                  c.jabatan as jabatan_baru')
                                                  ->where('a.nip',$nip)
                                                  ->join('TEMP_PEGAWAI b', 'a.nip = b.NIP_BARU','left')
                                                  ->join('tm_jabatan c', 'a.id_jabatan_baru = c.id','left');
                                                  $result = $builder->get()->getRowArray();

        $template = new TemplateProcessor('document/template_sk_jabatan_pelaksana.docx');

        $template->setValue('nomorsk', $result['nomer_sk']);
        $template->setValue('nama', $result['nama_lengkap']);
        $template->setValue('nip', $result['nip']);
        $template->setValue('pangkat', $result['PANGKAT']);
        $template->setValue('gol_ruang', $result['GOL_RUANG']);
        $template->setValue('pendidikan', $result['JENJANG_PENDIDIKAN']);
        $template->setValue('jabatan_lama', $result['jabatan_lama']);
        $template->setValue('jabatan_baru', $result['jabatan_baru']);
        $template->setValue('lokasi', 'Jakarta');
        $template->setValue('tmtsk', $result['tmt_SK']);
        $template->setValue('kepala', 'KEPALA');
        $template->setValue('namakepala', 'namakepala');
        $template->setValue('nipkepala', '1234567890');

        $filePath = WRITEPATH . 'uploads/' . 'document_' . $nip . '.docx';
        $template->saveAs($filePath);

        $model = new UsulModel();
        $data = [
            'link_sk' => $filePath,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $update = $model->where('nip', $nip)->set($data)->update();

        return $filePath;

    }
}
