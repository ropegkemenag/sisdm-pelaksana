<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsulModel;
use \Hermawan\DataTables\DataTable;

class Usulan extends BaseController
{
    public function index()
    {
        return view('usulan/index');
    }

    public function getdata()
    {
        $model = new UsulModel;
        $model->where('periode','40')
                ->like('kode_satker',session('kepala'),'after')
                ->select('kode,nip,nama,pangkat,jabatan,tingkat,status,file')
                ->orderBy('tingkat', 'desc')
                ->orderBy('pangkat', 'desc');

        return DataTable::of($model)
        ->edit('nip', function($row, $meta){
            return '<a href="javascript:;" onclick="preview(\''.$row->file.'\')">'.$row->nip.'</a><br><b>'.$row->nama.'</b>';
        })
        ->edit('tingkat', function($row, $meta){
            return levelstar($row->tingkat);
        })
        ->edit('status', function($row, $meta){
            return usul_status($row->status);
        })
        ->add('action', function($row){
            return 'xxx';
        })
        ->addNumbering('no')
        ->toJson(true);
    }
}
