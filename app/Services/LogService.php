<?php

namespace App\Services;

use App\Models\LogModel;

class LogService {

    protected $logModel;

    public function __construct()
    {
        $this->logModel = new LogModel();
    }

    public function insert($id_usul,$status,$keterangan,$kode_satker,$nama_satker,$created_by,$created_by_name)
    {
        $data = [
            'id_usul'           => $id_usul,
            'status'            => $status,
            'keterangan'        => $keterangan,
            'kode_satker'       => $kode_satker,
            'nama_satker'       => $nama_satker,
            'created_by'        => $created_by,
            'created_by_name'   => $created_by_name,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s')
        ];

        return $this->logModel->insert($data);
    }
}