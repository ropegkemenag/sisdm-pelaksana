<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SimpegModel;
use App\Models\SatkerModel;
use App\Models\UsulModel;
use App\Services\LogService;
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
      $builder = $db->table('tr_usul a')->select('a.id as id,a.nip as nip, 
                                                a.no_sk as nomer_sk, 
                                                a.tgl_sk as tgl_SK, 
                                                a.tmt_sk as tmt_SK, 
                                                a.kelas_jabatan as kelas_jabatan, 
                                                a.link_sk as link_sk,
                                                a.status as status,
                                                a.id_jabatan_baru as id_jabatan_baru,
                                                b.NAMA_LENGKAP as nama_lengkap,
                                                b.JENJANG_PENDIDIKAN as pendidikan,
                                                b.TAMPIL_JABATAN as tampil_jabatan,
                                                b.SATKER_2 as satker')
                                                ->where('jenis',1)
                                                ->join('TEMP_PEGAWAI b', 'a.nip = b.NIP_BARU','left');

      $jabatan_baru = $db->table('tm_jabatan')->get()->getResultArray();

    //   status 1 = usul
    //  status 2 = proses
    // status 3 = genereate 
    // status 4 = tte 

      return DataTable::of($builder)
      ->add('action', function($row){
          $btnProses = '<a href="javascript:;" type="button" class="btn btn-primary btn-sm" onclick="proses(this)" data-id="' . $row->id . '" data-nip="' . $row->nip . '">Proses</a>';
          $btnGenerate = '<a href="javascript:;" type="button" class="btn btn-warning btn-sm" onclick="generate(this)" data-id="' . $row->id . '" data-nip="' . $row->nip . '">Generate</a>'; 
          $btnView = '<a href="javascript:;" type="button" class="btn btn-secondary btn-sm" onclick="view(this)" data-url="'.$row->link_sk.'" data-nip="' . $row->nip . '">View</a>'; 
        if ($row->status == 3) {
            return $btnView;
        }else if ($row->status == 2) {
            return $btnGenerate;
        }else{
            return $btnProses;
        }

      })->add('jabatan_baru', function($row) use ($jabatan_baru){
        // $options = '';
        $options = '<option disabled selected>Pilih Jabatan Baru</option>';
        foreach ($jabatan_baru as $value) {
            $selected = ($value['id'] == $row->id_jabatan_baru) ? 'selected' : '';
            $options .= '<option value="'.$value['id'].'"' . $selected . '>'.$value['jabatan'].'</option>';
        }
        return '<select class="form-select form-control jabatan-baru jabbaru" id="jabatan-baru-'.$row->id.'" onchange="updateKelasJabatan(this, '.$row->id.')">'.$options.'</select>';
      })
      ->add('no_sk', function($row){
        return '<input type="text" class="form-control" id="no_sk" name="no_sk" value="'.$row->nomer_sk.'">';
      })
      ->add('tgl_sk', function($row){
        return '<input type="date" class="form-control" id="tgl_sk" name="tgl_sk" value="'.$row->tgl_SK.'">';
      })
    //   ->add('tmt', function($row){
    //     return '<input type="date" class="form-control" id="tmt" name="tmt" value="'.$row->tmt_SK.'">';
    //   })
      ->add('kelas_jabatan', function($row){
        return '<input type="text" class="form-control kelas_jabatan" id="kelas_jabatan_'.$row->id.'" name="kelas_jabatan" value="'.$row->kelas_jabatan.'" readonly>';
      })
      
      
      ->toJson(true);
    }

    public function getJabatan($id){
        $db = \Config\Database::connect('default', false);
        $jabatan_baru = $db->table('tm_jabatan')->where('id',$id)->get()->getRow();
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Berhasil ambil data',
            'data'    => $jabatan_baru
        ]);
        
    }

    public function add($nip,$type) {
        //   status 1 = usul
        //  status 2 = proses
        // status 3 = genereate 
        // status 4 = tte 
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
                'kode_satker' => session('kelola'),
                'status' => 1
            ];

            $insert = $model->insert($param);
            $idUsul = $model->insertID();
            // insert log
            $log = new LogService();
            // $id_usul,$status,$keterangan,$kode_satker,$nama_satker,$created_by,$created_by_name
            $log->insert($idUsul,1,'usul',session('kodesatker4'),session('satker4'),session('nip'),session('nama'));

            return $this->response->setJSON(['status'=>'success']);
        }
    }

    public function proses()
    {
        //   status 1 = usul
        //  status 2 = proses
        // status 3 = genereate 
        // status 4 = tte 

        $id = $this->request->getVar('id');
        $nip = $this->request->getVar('nip');
        $jabatanBaru = $this->request->getVar('jabatan_baru');
        $noSk = $this->request->getVar('no_sk');
        $tglSk = $this->request->getVar('tgl_sk');
        $kelasJabatan = $this->request->getVar('kelas_jabatan');
        // $tmt = $this->request->getVar('tmt');

        if (!$noSk || !$tglSk || !$kelasJabatan) {
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
            'kelas_jabatan' => $kelasJabatan,
            // 'tmt_sk' => $tmt,
            'status' => 2,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $update = $model->where('id', $id)->set($data)->update();

        $log = new LogService();
        // $id_usul,$status,$keterangan,$kode_satker,$nama_satker,$created_by,$created_by_name
        $log->insert($id,2,'proses lengkapi data',session('kodesatker4'),session('satker4'),session('nip'),session('nama'));

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
        $id = $this->request->getVar('id');
        $nip = $this->request->getVar('nip');
        $db = \Config\Database::connect('default', false);
        $builder = $db->table('tr_usul a')->select('a.id as id,a.nip as nip, 
                                                  a.no_sk as nomer_sk, 
                                                  a.tgl_sk as tgl_SK, 
                                                  a.tmt_sk as tmt_SK, 
                                                  a.id_jabatan_baru as id_jabatan_baru,
                                                  a.jenis as jenis,
                                                  a.kelas_jabatan as kelas_jabatan,
                                                  b.NAMA_LENGKAP as nama_lengkap,
                                                  b.TAMPIL_JABATAN as tampil_jabatan,
                                                  b.SATKER_2 as satker,
                                                  b.JENJANG_PENDIDIKAN,
                                                  b.PANGKAT,
                                                  b.GOL_RUANG,
                                                  b.TAMPIL_JABATAN as jabatan_lama,
                                                  c.jabatan as jabatan_baru')
                                                  ->where('a.id',$id)
                                                  ->join('TEMP_PEGAWAI b', 'a.nip = b.NIP_BARU','left')
                                                  ->join('tm_jabatan c', 'a.id_jabatan_baru = c.id','left');
                                                  $result = $builder->get()->getRowArray();
        $mpengaturan = new PengaturanModel();
        $pengaturan = $mpengaturan->where('kode_satker',session('kelola'))->where('jenis',1)->get()->getRow();
        
        // 1️⃣ Generate File Word
        $template = new TemplateProcessor('document/template_sk_jabatan_pelaksana.docx');

        $tgl_end_sk = date("m/Y", strtotime($result['tgl_SK']));
        $kodesurat  = $pengaturan->kode_surat;
        $no_depan   = $result['nomer_sk'];
        $final_nosk = $no_depan.'/'.$kodesurat.'/'.$tgl_end_sk;

        $formattedDate = date('d F Y', strtotime($result['tgl_SK']));
        $indonesianMonths = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];
        $format_tgl_sk = str_replace(array_keys($indonesianMonths), $indonesianMonths, $formattedDate);

        // $format_tgl_sk = strftime("%d %B %Y", strtotime($result['tgl_SK']));
        $kls_jab = formatKelasJabatan((int) $result['kelas_jabatan']);
        
        $template->setValue('nomorsk', $final_nosk);
        $template->setValue('nama', $result['nama_lengkap']);
        $template->setValue('nip', $result['nip']);
        $template->setValue('pangkat', $result['PANGKAT']);
        $template->setValue('gol_ruang', $result['GOL_RUANG']);
        $template->setValue('pendidikan', $result['JENJANG_PENDIDIKAN']);
        // $template->setValue('jabatan_lama', $result['jabatan_lama']);
        $template->setValue('jabatan_baru', $result['jabatan_baru']);
        $template->setValue('kelas_jabatan', $result['kelas_jabatan']);
        $template->setValue('lokasi', $pengaturan->tempat_ditetapkan);
        $template->setValue('tglsk', $format_tgl_sk);
        // $template->setValue('tmtsk', $result['tmt_SK']);
        $template->setValue('kepala', $pengaturan->jabatan);
        $template->setValue('namakepala', $pengaturan->nama_pejabat);
        $template->setValue('nipkepala',  $pengaturan->nip_pejabat);
        $template->setValue('anpejabat',  $pengaturan->an_pejabat);
        $template->setValue('satker',  $pengaturan->nama_satker);

        $filePath = WRITEPATH . 'uploads/' . 'document_' . $nip . '.docx';
        $template->saveAs($filePath);

        if (!file_exists($filePath)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan file Word'
            ]);
        }

        // 2️⃣ Konversi ke PDF via API
        $convRes = $this->convertDoctoPDF($filePath);
        log_message('error', 'Response from API: ' . print_r($convRes, true));
        if (!$convRes['status']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal konversi PDF: ' . $convRes['message']
            ]);
        }

        // 3️⃣ Simpan hasil PDF ke lokal sebelum upload
        $pdfPath = WRITEPATH . 'uploads/' . 'document_' . $nip . '.pdf';
        $cek = file_put_contents($pdfPath, $convRes['response']);
        log_message('error', 'simpan hasil pdf Response: ' . print_r($pdfPath, true));
        log_message('error', 'pdf response: ' . print_r($cek, true));
        if (!file_exists($pdfPath)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan file PDF'
            ]);
        }

        // 4️⃣ Upload ke MinIO
        $upload = new UploadService();
        $name   = $nip; 
        $location = 'sk/draft';
        $uploadRes = $upload->upload($pdfPath, $name, $location);
        log_message('error', 'Upload Response: ' . print_r($uploadRes, true));
        if (!$uploadRes['status']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal upload PDF ke MinIO'
            ]);
        }

        // 5️⃣ Simpan URL ke Database
        $model = new UsulModel();
        $data = [
            'link_sk' => $uploadRes['url'],
            'status' => 3,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        log_message('error', 'Updating database with: ' . print_r($data, true));
        $model->where('id', $id)->set($data)->update();

        // 6️⃣ Hapus file lokal setelah sukses upload
        unlink($filePath);
        unlink($pdfPath);

        // insert log
        $log = new LogService();
        // $id_usul,$status,$keterangan,$kode_satker,$nama_satker,$created_by,$created_by_name
        $log->insert($id,3,'generate draft',session('kodesatker4'),session('satker4'),session('nip'),session('nama'));

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Berhasil generate dan upload',
            'pdf_url' => $uploadRes
        ]);

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
            log_message('error', 'File tidak ditemukan: ' . $file);
            return ['status' => false, 'message' => 'File not found'];
        }

        $ch = curl_init();
        $postData = file_get_contents($file);
        // $postData = [
        //     'file' => new CURLFile($file),
        // ];

        // $postData = [
        //     'file' => new CURLFile($file, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', basename($file)),
        // ];
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
        log_message('error', 'Header Response: ' . CURLINFO_CONTENT_TYPE);
        $receivedContentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        log_message('error', 'Received Content-Type: ' . $receivedContentType);

        if ($httpCode == 200 && !empty($response)) {
            return ['status' => true, 'message' => 'Conversion successful', 'response' => $response];
        } else {
            return ['status' => false, 'message' => 'Conversion failed: ' . $error, 'response' => $response];
        }
    }
}
