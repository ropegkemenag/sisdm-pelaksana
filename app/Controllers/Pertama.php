<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SimpegModel;
use App\Models\SatkerModel;
use App\Models\UsulModel;
use \Hermawan\DataTables\DataTable;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Services\UploadService;
use CURLFile;

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
                                                a.link_sk as link_sk,
                                                a.status as status,
                                                a.id_jabatan_baru as id_jabatan_baru,
                                                b.NAMA_LENGKAP as nama_lengkap,
                                                b.TAMPIL_JABATAN as tampil_jabatan,
                                                b.SATKER_2 as satker')
                                                ->join('TEMP_PEGAWAI b', 'a.nip = b.NIP_BARU','left');

      $jabatan_baru = $db->table('tm_jabatan')->get()->getResultArray();

    //   status 1 = prosess
    //  status 2 = generate sk
    // status 3 = tte

      return DataTable::of($builder)
      ->add('action', function($row){
          $btnProses = '<a href="javascript:;" type="button" class="btn btn-primary btn-sm" onclick="proses(this)" data-nip="' . $row->nip . '">Proses</a>';
          $btnGenerate = '<a href="javascript:;" type="button" class="btn btn-warning btn-sm" onclick="generate(this)" data-nip="' . $row->nip . '">Generate</a>'; 
          $btnView = '<a href="javascript:;" type="button" class="btn btn-secondary btn-sm" onclick="view(this)" data-url="'.$row->link_sk.'" data-nip="' . $row->nip . '">View</a>'; 
        if ($row->status == 2) {
            return $btnView;
        }else if ($row->status == 1) {
            return $btnGenerate;
        }else{
            return $btnProses;
        }

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
            'status' => 1,
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
        // 1️⃣ Generate File Word
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

        if (!file_exists($filePath)) {
            return [
                'status' => false,
                'message' => 'Gagal menyimpan file Word'
            ];
        }

        // 2️⃣ Konversi ke PDF via API
        $convRes = $this->convertDoctoPDF($filePath);
        log_message('error', 'Response from API: ' . print_r($convRes, true));
        if (!$convRes['status']) {
            return [
                'status' => false,
                'message' => 'Gagal konversi PDF: ' . $convRes['message']
            ];
        }

        // 3️⃣ Simpan hasil PDF ke lokal sebelum upload
        $pdfPath = WRITEPATH . 'uploads/' . 'document_' . $nip . '.pdf';
        file_put_contents($pdfPath, $convRes['response']);
        log_message('error', 'simpan hasil pdf Response: ' . print_r($pdfPath, true));
        if (!file_exists($pdfPath)) {
            return [
                'status' => false,
                'message' => 'Gagal menyimpan file PDF'
            ];
        }

        // 4️⃣ Upload ke MinIO
        $upload = new UploadService();
        $name   = $nip; 
        $location = 'sk/draft';
        $uploadRes = $upload->upload($pdfPath, $name, $location);
        log_message('error', 'Upload Response: ' . print_r($uploadRes, true));
        if (!$uploadRes['status']) {
            return [
                'status' => false,
                'message' => 'Gagal upload PDF ke MinIO'
            ];
        }

        // 5️⃣ Simpan URL ke Database
        $model = new UsulModel();
        $data = [
            'link_sk' => $uploadRes['url'],
            'status' => 2,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        log_message('error', 'Updating database with: ' . print_r($data, true));
        $model->where('nip', $nip)->set($data)->update();

        // 6️⃣ Hapus file lokal setelah sukses upload
        unlink($filePath);
        unlink($pdfPath);

        return [
            'status' => true,
            'message' => 'Berhasil generate dan upload',
            'pdf_url' => $uploadRes['url']
        ];

        // if ($convRes['status']) {
        //     // Hapus file lokal setelah sukses upload
        //     unlink($filePath);
        
        //     // Simpan URL MinIO ke database
        //     $model = new UsulModel();
        //     $data = [
        //         'link_sk' => $result['url'],
        //         'status' => 1,
        //         'updated_at' => date('Y-m-d H:i:s'),
        //     ];
        //     $model->where('nip', $nip)->set($data)->update();
        //     return [
        //         'status' => true,
        //         'message' => 'Berhasil generate dan upload',
        //         'response' => $convRes['response']
        //     ];

        // } else {
        //     return [
        //         'status' => false,
        //         'message' => 'Gagal generate dan upload'. $convRes['message']
        //     ];
        // }

    }

    function convertDoctoPDF($file)
    {
        $url        = 'https://ropegdev.kemenag.go.id/convert-doc2pdf/upload/file';
        $userapi    = 'RopegAdmin';
        $pwdapi     = 'B1R0kePeG4w4ai4n$1Khl4sB3r4MaL';

        if (!file_exists($file)) {
            return ['status' => false, 'message' => 'File not found'];
        }

        $ch = curl_init();
        // $postData = [
        //     'file' => new CURLFile($file),
        // ];

        // $postData = [
        //     'file' => new CURLFile($file, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', basename($file)),
        // ];

        $postData = file_get_contents($file);
        // $postData = [
        //     'file' => base64_encode($fileData),
        // ];

        

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_USERPWD, "$userapi:$pwdapi"); // Authentication
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);

        // execute the
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        log_message('error', 'HTTP Code: ' . $httpCode);
        log_message('error', 'CURL Error: ' . $error);
        log_message('error', 'API Response: ' . $response);

        if ($httpCode == 200) {
            return ['status' => true, 'message' => 'Conversion successful', 'response' => $response];
        } else {
            return ['status' => false, 'message' => 'Conversion failed: ' . $error, 'response' => $response];
        }
    }
}
