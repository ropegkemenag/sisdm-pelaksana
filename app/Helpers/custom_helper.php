<?php

if ( ! function_exists('ago'))
{
  function timeago($time)
  {
    $ptime  = strtotime($time);
    $estimate_time = time() - $ptime;

    if( $estimate_time < 1 )
    {
        return 'less than 1 second ago';
    }

    $condition = array(
                12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $estimate_time / $secs;

        if( $d >= 1 )
        {
            $r = round( $d );
            return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
        }
    }
  }
}

if ( ! function_exists('get_option'))
{
	function get_option($name)
	{
		$CI =& get_instance();

		$CI->db->where('option_name',$name);
        $query	= $CI->db->get('options');
        $result = $query->row();

		return $result->option_value;
	}
}

if ( ! function_exists('update_option'))
{
	function update_option($name,$value)
	{
		$CI =& get_instance();
    $CI->cms_model->update('options',array('option_value' => $value), array('option_name' => $name));

		return true;
	}
}

function hari($date)
{
	$day = date('N', strtotime($date));

	$hari = array(
						'1' => 'Senin',
						'2' => 'Selasa',
						'3' => 'rabu',
						'4' => 'Kamis',
						'5' => "Jum'at",
						'6' => 'Sabtu',
						'7' => 'Minggu',
 					);
	return $hari[$day];
}

function bulan($date)
{
	$month = date('n', strtotime($date));

	$bulan = array(
						'1' => 'Januari','2' => 'Februari','3' => 'Maret','4' => 'April',
						'5' => 'Mei','6' => 'Juni','7' => 'Juli','8' => 'Agustus',
						'9' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember'
 					);
	return $bulan[$month];
}

function local_date($tgl){
	$tanggal = substr($tgl,8,2);
	$bulan = bulan($tgl);
	$tahun = substr($tgl,0,4);
	return $tanggal.' '.$bulan.' '.$tahun;
}

function id_date($tgl){
	$tanggal = substr($tgl,8,2);
	$bulan = substr($tgl,5,2);
	$tahun = substr($tgl,0,4);
	return $tanggal.'/'.$bulan.'/'.$tahun;
}

function read_date($tgl){
  $time = strtotime($tgl);
  return date('d/m/Y', $time);
}

function agama($status)
{
	$agama = array(
						'1' => 'Islam',
						'2' => 'Kristen',
						'3' => 'Katolik',
						'4' => 'Hindu',
						'5' => 'Buddha',
						'6' => 'Konghucu'
 					);
	return $agama[$status];
}

function kodekepala($kode)
{
  if($kode == 0){
    $kodebuntut = 14;
  }else if(substr($kode,-12) == '000000000000'){
    $kodebuntut = 12;
  }else if(substr($kode,-10) == '0000000000'){
    $kodebuntut = 10;
  }else if(substr($kode,-8) == '00000000'){
    $kodebuntut = 8;
  }else if(substr($kode,-6) == '000000'){
    $kodebuntut = 6;
  }else if(substr($kode,-4) == '0000'){
    $kodebuntut = 4;
  }else if(substr($kode,-2) == '00'){
    $kodebuntut = 2;
  }else if(substr($kode,-2) != '00'){
    $kodebuntut = 2;
  }

  $kodekepala = substr($kode, 0, (strlen($kode) - $kodebuntut));

  return $kodekepala;
}

function kodekelola($kode)
{
  if($kode == 0){
    $kodebuntut = 14;
  }else if(substr($kode,-12) == '000000000000'){
    $kodebuntut = 12;
  }else if(substr($kode,-10) == '0000000000'){
    $kodebuntut = 10;
  }else if(substr($kode,-8) == '00000000'){
    $kodebuntut = 8;
  }else if(substr($kode,-6) == '000000'){
    $kodebuntut = 6;
  }else if(substr($kode,-4) == '0000'){
    $kodebuntut = 4;
  }else if(substr($kode,-2) == '00'){
    $kodebuntut = 2;
  }else if(substr($kode,-2) != '00'){
    $kodebuntut = 2;
  }
  return $kodebuntut;
}

function kodeparent($kode)
{
  $kodeparent = substr($kode, 0, 4);

  return $kodeparent;
}

function timeDiff($firstTime,$lastTime)
{
  $firstTime=strtotime($firstTime);
  $lastTime=strtotime($lastTime);

  $timeDiff=$lastTime-$firstTime;

  return gmdate("H:i:s", $timeDiff);

}

function to_date($date='')
{
  $time = strtotime($date);
  return date('Y-m-d', $time);
}

function to_time($date='')
{
  $time = strtotime($date);
  return date('H:i:s', $time);
}

function std_to_time($std='')
{
  $time1 = substr($std, 0, 2);
  $time2 = substr($std, -2);

  return $time1.':'.$time2.':00';

}

function format_number($dates='')
{
  $date = date('d', strtotime($dates));
  $month = date('M', strtotime($dates));
  $year = date('Y', strtotime($dates));

  return $date.'/'.$month.' '.$year;
}

function rupiah($angka)
{
	$hasil_rupiah = number_format(($angka ?? 0),0,',','.');
	return $hasil_rupiah;
}

function shortdec($number='')
{
  return number_format((float)$number, 2, '.', '');
}

function konsultasi_status($status)
{
  if($status == 0){
    $result = 'Dikirim';
  }else if($status == 1){
    $result = 'Admin Membalas';
  }else if($status == 2){
    $result = 'Anda Membalas';
  }else if($status == 3){
    $result = 'Ditutup';
  }

  return $result;
}

function laporanstatus($status)
{
  if($status == 0){
    $result = '<span class="badge bg-warning">Dikirim</span>';
  }else if($status == 1){
    $result = '<span class="badge bg-success">Diterima</span>';
  }else if($status == 2){
    $result = '<span class="badge bg-danger">Ditolak</span>';
  }else{
    $result = '';
  }

  return $result;
}

function jenis_tubel($jenis)
{
  if($jenis == 6){
    $result = 'Tugas Belajar Pada Jam Kerja';
  }else if($jenis == 7){
    $result = 'Tugas Belajar Diluar Jam Kerja';
  }else{
    $result = '-';
  }

  return $result;
}

function hp($nohp) {
  $nohp = str_replace(" ","",$nohp);
  $nohp = str_replace("(","",$nohp);
  $nohp = str_replace(")","",$nohp);
  $nohp = str_replace(".","",$nohp);

  $hp = null;
  if(!preg_match('/[^+0-9]/',trim($nohp))){
     if(substr(trim($nohp), 0, 3)=='+62'){
         $hp = trim($nohp);
     }
     elseif(substr(trim($nohp), 0, 1)=='0'){
         $hp = '62'.substr(trim($nohp), 1);
     }
  }
  return $hp;
 }

 function setagenda($id)
 {
   $db          = \Config\Database::connect();
   $tahun       = date('Y');
   $getagenda   = $db->query("exec sp_get_agenda @tahun='".$tahun."'")->getRow();
   $urutagenda    = ($getagenda->AGENDA > 0)?$getagenda->AGENDA:1;
   $noagenda    = setzeroagenda($urutagenda);

   // $usul = new \App\Models\UsulModel();
   // $usul->update($id,['AGENDA' => $noagenda,'AGENDA_URUT' => $urutagenda]);

   $update = $db->query("UPDATE TR_USULAN SET AGENDA='$noagenda', AGENDA_URUT='$urutagenda' WHERE ID='$id'");
 }

 function setzeroagenda($id)
 {
   if(strlen($id) == 1){
     return '00000'.$id;
   }else if(strlen($id) == 2){
     return '0000'.$id;
   }else if(strlen($id) == 3){
     return '000'.$id;
   }else if(strlen($id) == 4){
     return '00'.$id;
   }else if(strlen($id) == 5){
     return '0'.$id;
   }else{
     return $id;
   }
 }

 function gender($kode)
 {
   if($kode == 1){
     return 'Laki-Laki';
   }else if($kode == 0){
     return 'Perempuan';
   }else{
     return '-';
   }
 }

 function usul_status($status)
 {
   if($status == 0){
     $result = '<span class="badge bg-secondary">Dikirim</span>';
   }else if($status == 1){
     $result = '<span class="badge bg-success">Diverifikasi</span>';
   }else if($status == 2){
     $result = '<span class="badge bg-danger">TMS</span>';
   }else{
     $result = '';
   }

   return $result;
 }

 function levelstar($tingkat) {
  if($tingkat == 1){
    return '★';
  }else if($tingkat == 2){
    return '★★';
  }else if($tingkat == 3){
    return '★★★';
  }
 }

define('ENCRYPTION_KEY', '4736d52f85bdb63e46bf7d6d41bbd551af36e1bfb7c68164bf81e2400d291319');
function encrypt($string, $salt = null)
{
	if($salt === null) { $salt = hash('sha256', uniqid(mt_rand(), true)); }  // this is an unique salt per entry and directly stored within a password
	return base64_encode(openssl_encrypt($string, 'AES-256-CBC', ENCRYPTION_KEY, 0, str_pad(substr($salt, 0, 16), 16, '0', STR_PAD_LEFT))).':'.$salt;
}
function decrypt($string)
{
  	// if( count(explode(':', $string)) !== 2 ) { return $string; }
	$salt = explode(":",$string)[1]; $string = explode(":",$string)[0]; // read salt from entry
	return openssl_decrypt(base64_decode($string), 'AES-256-CBC', ENCRYPTION_KEY, 0, str_pad(substr($salt, 0, 16), 16, '0', STR_PAD_LEFT));
}
