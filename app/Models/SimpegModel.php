<?php

namespace App\Models;

use CodeIgniter\Model;

class SimpegModel extends Model
{
  protected $db;

  public function __construct()
  {
    $this->db = \Config\Database::connect('simpeg', false);

  }

  public function getRow($table,$where)
  {
    $builder = $this->db->table($table);
    $query = $builder->getWhere($where);

    return $query->getRow();
  }

  public function getArray($table,$where=false)
  {
    $builder = $this->db->table($table);

    if($where){
      $query = $builder->getWhere($where);
    }else{
      $query = $builder->get();
    }

    return $query->getResult();
  }

  public function setquery($query)
  {
    $query = $this->db->query($query);
    return $query;
  }

  public function getPegawai($nip)
  {
    $query = $this->db->query("SELECT * FROM TEMP_PEGAWAI WHERE NIP_BARU='$nip'")->getRow();
    return $query;
  }

  public function anomaliPangkat($satker)
  {
    $query = $this->db->query("select nip, nip_baru, nama, keterangan from vwdata_pegawai where kode_satuan_kerja LIKE '$satker%' and kode_mutasi=1 and kode_pangkat is null")->getResult();
    return $query;
  }

  public function anomaliJabatan($satker)
  {
    $query = $this->db->query("select nip, nip_baru, nama, keterangan from vwdata_pegawai where kode_satuan_kerja LIKE '$satker%' and kode_mutasi=1 and jabatan is null")->getResult();
    return $query;
  }

  public function anomaliPendidikan($satker)
  {
    $query = $this->db->query("select nip, nip_baru, nama, keterangan from vwdata_pegawai where kode_satuan_kerja LIKE '$satker%' and kode_mutasi=1 and jenjang_pendidikan is null")->getResult();
    return $query;
  }

  public function anomaliSatker($satker)
  {
    $query = $this->db->query("select nip, nip_baru, nama, keterangan from vwdata_pegawai where kode_satuan_kerja LIKE '$satker%' and kode_mutasi=1 and (kode_satuan_kerja is null OR kode_satuan_kerja not in (select kode_satuan_kerja from tm_satuan_kerja))")->getResult();
    return $query;
  }

  public function anomaliTgllahir($satker)
  {
    $query = $this->db->query("select nip, nip_baru, nama, keterangan from vwdata_pegawai where kode_satuan_kerja LIKE '$satker%' and kode_mutasi=1 and tanggal_lahir is null")->getResult();
    return $query;
  }

  public function searchPegawai($nip,$satker)
  {
    $query = $this->db->query("SELECT * FROM TEMP_PEGAWAI WHERE NIP_BARU='$nip' AND KODE_SATUAN_KERJA LIKE '$satker%'")->getRow();
    return $query;
  }

  public function getParentSatker()
  {
    $query = $this->db->query("SELECT * FROM TM_SATUAN_KERJA WHERE JENIS_SATKER='PARENT'")->getResult();
    return $query;
  }

  public function getTempPegawai($kode)
  {
    $query = $this->db->query("SELECT NIP,NIP_BARU,NAMA_LENGKAP,AGAMA,TEMPAT_LAHIR,TANGGAL_LAHIR,JENIS_KELAMIN,PENDIDIKAN,JENJANG_PENDIDIKAN,LEVEL_JABATAN,PANGKAT,GOL_RUANG,TMT_CPNS,TMT_PANGKAT,
                              MK_TAHUN,MK_BULAN,TIPE_JABATAN,TAMPIL_JABATAN,TMT_JABATAN,SATKER_1,SATKER_2,SATKER_3,SATKER_4,SATKER_5,HARI_KERJA,USIA_PENSIUN,TMT_PENSIUN,STATUS_PEGAWAI,NO_HP,EMAIL,HARI_KERJA
                              FROM TEMP_PEGAWAI WHERE KODE_SATUAN_KERJA LIKE '$kode%'")->getResult();
    return $query;
  }

  public function query_row($query)
  {
    $query = $this->db->query($query)->getRow();
    return $query;
  }

  public function query_array($query)
  {
    $query = $this->db->query($query)->getResult();
    return $query;
  }

  public function getCount($table,$where=false)
  {
    $builder = $this->db->table($table);

    if($where){
      $query = $builder->getWhere($where);
    }else{
      $query = $builder->get();
    }

    return $query->countAllResults();
  }

  public function getAuth($nip)
  {
    $query = $this->db->query("exec sp_usermanager @nip='".$nip."', @appid='1'")->getRow();
    return $query;
  }

  public function getskp2022x($start,$limit)
  {
    $query = $this->db->query("SELECT * FROM TM_REKON_SKP WHERE PNS_DINILAI_ID IS NOT NULL ORDER BY
                              PNS_DINILAI_ID ASC
                              OFFSET $start ROWS FETCH NEXT $limit ROWS ONLY")->getResult();
    return $query;
  }

  public function getskp2022()
  {
    $query = $this->db->query("SELECT * FROM v_rekon_skp2022")->getResult();
    return $query;
  }

  public function updateskp2022($nip,$tahun,$status)
  {
    $query = $this->db->query("UPDATE TR_SKP SET SEND_BKN='$status' WHERE NIP='$nip' AND TAHUN='$tahun'");
    return $query;
  }

  public function getInfoKP($year,$month)
  {
    $query = $this->db->query("SELECT SATKER2 AS SATKER, COUNT(*) AS JUMLAH FROM TEMP_PEGAWAI_PANGKAT WHERE TMT_KP <> CAST('$month/01/$year' AS DATE)
                              GROUP BY SATKER2")->getResult();
    return $query;
  }

  public function getCountKP($year,$month)
  {
    $query = $this->db->query("SELECT COUNT(*) AS JUMLAH FROM TEMP_PEGAWAI_PANGKAT
    WHERE TMT_KP <> CAST('$month/01/$year' AS DATE)")->getRow();
    return $query;
  }

  public function pejabat($kode)
  {
    // $query = $this->db->query("exec sp_Linq_Pejabat @kode='".$kode."'")->getResult();
    $query = $this->db->query("exec sp_linq_pejabat_all @rp='".$kode."'")->getResult();
    return $query;
  }

  public function pensiun($kode)
  {
    $query = $this->db->query("exec sp_Linq_PegawaiPensiunPerTahun @kode='".$kode."'")->getResult();
    return $query;
  }

  public function pensiunPegawai($kode,$tahun)
  {
    $query = $this->db->query("exec sp_linq_Pegawai_pensiun_PerSatker @kode='".$kode."', @year='".$tahun."'")->getResult();
    return $query;
  }

  public function saveAddr($nip,$alamat1,$alamat2,$kabkota,$provinsi)
  {
    $query = $this->db->query("exec spwINSERT_UPDATE_ALAMAT @NIP='".$nip."', @ALAMAT_1='".$alamat1."', @ALAMAT_2='".$alamat2."', @KAB_KOTA='".$kabkota."', @PROVINSI='".$provinsi."', @NO_SK='NULL', @TEST_TGL_SK='NULL', @TGL_SK='NULL', @TEST_TMT='NULL', @TMT_SK='NULL', @KODE_JABATAN_2='NULL', @KODE_SATUAN_KERJA_2='NULL', @NO_SK_2='NULL', @TEST_TGL_SK_2='NULL', @TGL_SK_2='NULL', @TEST_TMT_SK_2='NULL', @USERID='NULL', @KETERANGAN='NULL', @KODE_PANGKAT='NULL'")->getResult();
    return $query;
  }

  public function addRwJabatan($nip,$satker,$nosk,$tglsk,$tmtsk)
  {
    $userid = session('niplama');
    $pegawai = $this->getPegawai($nip);
    $query = $this->db->query("exec spINSERT_RW_JABATAN @NIP='".$pegawai->NIP."', 
                                                        @NO_URUT=NULL, 
                                                        @KODE_JABATAN='', 
                                                        @KODE_BIDANG_STUDI=NULL, 
                                                        @KODE_SATUAN_KERJA='".$satker."', 
                                                        @NO_SK='".$nosk."', 
                                                        @TEST_TGL_SK=NULL, 
                                                        @TGL_SK='".$tglsk."', 
                                                        @TEST_TMT_SK=NULL, 
                                                        @TMT_SK='".$tmtsk."', 
                                                        @KODE_JABATAN_2=NULL, 
                                                        @KODE_SATUAN_KERJA_2=NULL, 
                                                        @NO_SK_2=NULL, 
                                                        @TEST_TGL_SK_2=NULL, 
                                                        @TGL_SK_2=NULL, 
                                                        @TEST_TMT_SK_2=NULL, 
                                                        @TMT_SK_2=NULL, 
                                                        @KETERANGAN=NULL, 
                                                        @KODE_PANGKAT=NULL,
                                                        @USERID='".$userid."'")->getResult();
    return $query;
  }

  public function get_news()
  {
    $query = $this->db->query("SELECT berita_id,tgl_berita, judul_berita FROM TM_BERITA WHERE status_berita='1' ORDER BY tgl_berita DESC");

    return $query->getResult();
  }

  public function get_map($kode)
  {
    $query = $this->db->query("SELECT SATUAN_KERJA, LAT, LON FROM TM_SATUAN_KERJA WHERE LAT IS NOT NULL AND LAT !='0' AND KODE_SATUAN_KERJA LIKE '$kode%' ORDER BY KODE_SATUAN_KERJA ASC");

    return $query->getResult();
  }

  public function subsatker($kodesatker)
  {
    $query = $this->db->query("SELECT * FROM TM_SATUAN_KERJA
                              WHERE KODE_ATASAN='$kodesatker'")->getResult();

    return $query;
  }

  public function temppegawailat($kodesatker)
  {
    $query = $this->db->query("SELECT * FROM TEMP_PEGAWAI_LATLON
                              WHERE KODE_SATKER='$kodesatker'")->getResult();

    return $query;
  }

  public function updatekoordinat($satker,$lat,$lon)
  {
    $query = $this->db->query("UPDATE TM_SATUAN_KERJA SET LAT='$lat', LON='$lon'
                              WHERE KODE_SATUAN_KERJA='$satker'");

    return $query;
  }

  public function dashboard_jk($kode)
  {
    $query = $this->db->query("SELECT JENIS_KELAMIN, COUNT(NIP) AS JUMLAH FROM TEMP_PEGAWAI WHERE STATUS_PEGAWAI != 'Non' AND KODE_SATUAN_KERJA LIKE '$kode%' GROUP BY JENIS_KELAMIN");

    return $query->getResult();
  }

  public function dashboard_pns($kode)
  {
    $query = $this->db->query("SELECT COUNT(NIP) AS JUMLAH FROM TEMP_PEGAWAI WHERE STATUS_PEGAWAI IN ('PNS','CPNS') AND KODE_SATUAN_KERJA LIKE '$kode%' GROUP BY JENIS_KELAMIN");

    return $query->getRow();
  }

  public function dashboard_pppk($kode)
  {
    $query = $this->db->query("SELECT COUNT(NIP) AS JUMLAH FROM TEMP_PEGAWAI WHERE STATUS_PEGAWAI='PPPK' AND KODE_SATUAN_KERJA LIKE '$kode%' GROUP BY JENIS_KELAMIN");

    return $query->getRow();
  }

  public function report_jk()
  {
    $query = $this->db->query("SELECT JENIS_KELAMIN, COUNT(NIP) AS JUMLAH FROM TEMP_PEGAWAI WHERE STATUS_PEGAWAI != 'Non' GROUP BY JENIS_KELAMIN");

    return $query->getResult();
  }

  public function dashboard_jab($kode)
  {
    $query = $this->db->query("SELECT LEVEL_JABATAN, COUNT(NIP) AS JUMLAH FROM TEMP_PEGAWAI WHERE STATUS_PEGAWAI != 'Non' AND KODE_SATUAN_KERJA LIKE '$kode%' GROUP BY LEVEL_JABATAN ORDER BY LEVEL_JABATAN");

    return $query->getResult();
  }

  public function dashboard_tipejab($kode)
  {
    $query = $this->db->query("SELECT TIPE_JABATAN, COUNT(NIP) AS JUMLAH FROM TEMP_PEGAWAI WHERE STATUS_PEGAWAI != 'Non' AND KODE_SATUAN_KERJA LIKE '$kode%' GROUP BY TIPE_JABATAN ORDER BY TIPE_JABATAN");

    return $query->getResult();
  }

  public function report_jab()
  {
    $query = $this->db->query("SELECT LEVEL_JABATAN, COUNT(NIP) AS JUMLAH FROM TEMP_PEGAWAI WHERE STATUS_PEGAWAI != 'Non' GROUP BY LEVEL_JABATAN ORDER BY LEVEL_JABATAN");

    return $query->getResult();
  }

  public function dashboard_agama($kode)
  {
    $query = $this->db->query("SELECT AGAMA, COUNT(NIP) AS JUMLAH FROM TEMP_PEGAWAI WHERE STATUS_PEGAWAI != 'Non' AND KODE_SATUAN_KERJA LIKE '$kode%' GROUP BY AGAMA ORDER BY AGAMA");

    return $query->getResult();
  }

  public function report_agama()
  {
    $query = $this->db->query("SELECT AGAMA, COUNT(NIP) AS JUMLAH FROM TEMP_PEGAWAI WHERE STATUS_PEGAWAI != 'Non' GROUP BY AGAMA ORDER BY AGAMA");

    return $query->getResult();
  }

  public function getPetabyJabatan($kode)
  {
    $query = $this->db->query("SELECT LEVEL_JABATAN AS JABATAN, COUNT(NIP_BARU) AS JML FROM TEMP_PEGAWAI WHERE KODE_SATKER_2='$kode' GROUP BY LEVEL_JABATAN");

    return $query->getResult();
  }

  public function search_user($nip,$satker)
  {
    if(session('role') == 1){
      $query = $this->db->query("SELECT * FROM TS_USER WHERE NIP='$nip'");
    }else{
      $query = $this->db->query("SELECT * FROM TS_USER WHERE NIP='$nip' AND KODE_UNIT_KERJA LIKE '$satker%'");
    }

    return $query->getRow();
  }

  public function updatepassword($nip,$password)
  {
    $query = $this->db->query("UPDATE TS_USER SET PWD='$password' WHERE NIP='$nip'");
    $query = $this->db->query("UPDATE TEMP_PEGAWAI_SSO SET PWD='$password' WHERE NIP_USER='$nip'");

    return $query;
  }
}
