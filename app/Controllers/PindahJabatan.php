<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use \Hermawan\DataTables\DataTable;

class PindahJabatan extends BaseController
{
    public function index()
    {
        return view('pindah/index');
    }

    public function getdata()
    {
      $db = \Config\Database::connect('simpeg', false);
      $builder = $db->table('TEMP_PEGAWAI')->select('NIP, NIP_BARU, NAMA_LENGKAP, TAMPIL_JABATAN, SATKER_2, SATKER_3, SATKER_4, STATUS_PEGAWAI')->like('LEVEL_JABATAN','%pelaksana%')->like('KODE_SATUAN_KERJA', kodekepala(session('kelola')), 'after');

      return DataTable::of($builder)
      ->add('action', function($row){
          return '<a href="javascript:;" type="button" class="btn btn-primary btn-sm" onclick="add(\''.$row->NIP_BARU.'\')">Add</a>';
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
                                                ->where('jenis',2)
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
        // $options = '';
        $options = '<option disabled selected>Pilih Jabatan Baru</option>';
        foreach ($jabatan_baru as $value) {
            $selected = ($value['id'] == $row->id_jabatan_baru) ? 'selected' : '';
            $options .= '<option value="'.$value['id'].'"' . $selected . '>'.$value['jabatan'].'</option>';
        }
        return '<select class="form-select form-control jabatan-baru" id="jabatan-baru">'.$options.'</select>';
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
}
