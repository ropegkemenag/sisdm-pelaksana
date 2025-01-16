<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SimpegModel;

class Auth extends BaseController
{

  public function index()
  {
    return view('auth');
  }

  public function login()
  {
    if (!session()->get('isLoggedIn')) {
      return redirect()->to($_ENV['SSO_SIGNIN'].'?appid='.$_ENV['SSO_APPID']);
    }else{
      return redirect()->to('dashboard');
    }
  }

  public function callback()
  {
    $request = \Config\Services::request();
    $client = \Config\Services::curlrequest();

    $token = $request->getVar('token') ?? '';
    if($token){
      $verify_url = $_ENV['SSO_VERIFY'];

      $response = $client->request('GET', $verify_url, [
        'headers' => [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '. $token,
        ],
        'verify' => false,
        'debug' => true
      ]);

      $data = json_decode($response->getBody());

      if($data->status == 200){
        $data = $data->pegawai;

        $simpeg = new SimpegModel;
        $checkrole = $simpeg->getRow('UM_USER',['NIP'=>$data->NIP,'APP_ID'=>3]);

        if($checkrole){
          $ses_data = [
            'nip'        => $data->NIP,
            'niplama'    => $data->NIP_LAMA,
            'nama'       => $data->NAMA,
            'pangkat'    => $data->PANGKAT,
            'golongan'   => $data->GOLONGAN,
            'jabatan'    => $data->JABATAN,
            'level'      => $data->KODE_LEVEL_JABATAN,
            'kodesatker2' => $data->KODE_SATKER_2,
            'satker2'     => $data->SATKER_2,
            'kodesatker3' => $data->KODE_SATKER_3,
            'satker3'     => $data->SATKER_3,
            'kodesatker4' => $data->KODE_SATKER_4,
            'satker4'     => $data->SATKER_4,
            'kodesatker5' => $data->KODE_SATKER_5,
            'kelola'     => $checkrole->KODE_SATKER,
            'kepala'     => kodekepala($data->KODE_SATKER_4),
            'role'     => $checkrole->ROLE,
            'photo'     => $data->PHOTO,
            'isLoggedIn' => TRUE
          ];
          session()->set($ses_data);
          return redirect()->to('dashboard');

        }else{
          return redirect()->to($_ENV['SSO_SIGNIN'].'?appid='.$_ENV['SSO_APPID'].'&info=2');
        }

      }else{
        echo $data->msg;
      }
    }else{
      echo 'no Token';
    }
  }

  public function logout()
  {
    $session = session();
    $session->destroy();
    return redirect()->to($_ENV['SSO_SIGNIN'].'?appid='.$_ENV['SSO_APPID']);
  }
}
