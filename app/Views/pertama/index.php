<?= $this->extend('template') ?>

<?= $this->section('style') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman -->
<div class="row mb-3">
  <div class="col-lg-12">
    <div class="card card-light">
      <div class="card-body">
        <h4 class="mb-0 card-text">Penyesuaian Nomenklatur</h4>
        <p class="mb-0">Kelola data pegawai, proses mutasi, tanda tangan, dan status selesai.</p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
  <ul class="nav nav-pills nav-custom-outline nav-primary mb-3" role="tablist">
      <li class="nav-item waves-effect waves-light">
          <a class="nav-link active" data-bs-toggle="tab" href="#nav-pegawai" role="tab">Data Pegawai</a>
      </li>
      <li class="nav-item waves-effect waves-light">
          <a class="nav-link" data-bs-toggle="tab" href="#nav-proses" role="tab">Proses <span class="badge bg-danger"><?= $total_proses; ?></span></a>
      </li>
      <li class="nav-item waves-effect waves-light">
          <a class="nav-link" data-bs-toggle="tab" href="#nav-tt" role="tab">Tanda Tangan <span class="badge bg-danger">0</span></a>
      </li>
      <li class="nav-item waves-effect waves-light">
          <a class="nav-link" data-bs-toggle="tab" href="#nav-selesai" role="tab">Selesai <span class="badge bg-success">0</span></a>
      </li>
  </ul>
  <div class="tab-content text-muted">
    <div class="tab-pane active" id="nav-pegawai" role="tabpanel">
    <div class="card border card-border-warning">
      <div class="card-body">
        <form action="javascript:void(0);" class="row g-3">
            <div class="col-md-4">
                <label for="unit" class="form-label">Unit Kerja</label>
                <select id="unit" class="form-select">
                  <option value="">Semua Unit</option>
                  <?php foreach ($unit as $row) {
                    echo '<option value="'.$row->KODE_SATUAN_KERJA.'">'.$row->SATUAN_KERJA.'</option>';
                  }  ?>
                </select>
            </div>
        </form>
      </div>
    </div>

    <div class="card border card-border-warning">
      <div class="card-body">

        <table id="datatables" class="display table table-bordered dt-responsive fonttab" style="width:100%">
          <thead>
            <tr>
              <th>NIP BARU</th>
              <th>NAMA</th>
              <th>JABATAN</th>
              <th>UNIT KERJA</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th>NIP BARU</th>
              <th>NAMA</th>
              <th>JABATAN</th>
              <th>UNIT KERJA</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    </div>
    <div class="tab-pane" id="nav-proses" role="tabpanel">
      <div class="card border card-border-warning">
        <div class="card-body">
        <table id="datatablesproses" class="display table table-bordered dt-responsive fonttab" style="width: 100%;">
          <thead>
            <tr>
              <th>NAMA/NIP</th>
              <th>JABATAN</th>
              <th>JABATAN BARU</th>
              <th>TANGGAL SK</th>
              <th>KELAS JABATAN</th>
              <th>NO SK</th>
              <th>UNIT KERJA</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th>NAMA/NIP</th>
              <th>JABATAN</th>
              <th>JABATAN BARU</th>
              <th>TANGGAL SK</th>
              <th>KELAS JABATAN</th>
              <th>NO SK</th>
              <th>UNIT KERJA</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
        </div>
      </div>
    </div>
    <div class="tab-pane" id="nav-tt" role="tabpanel">
      <div class="card border card-border-warning">
        <div class="card-body">
          Tanda Tangan
        </div>
      </div>
    </div>
    <div class="tab-pane" id="nav-selesai" role="tabpanel">
      <div class="card border card-border-warning">
        <div class="card-body">
          Selesai
        </div>
      </div>
    </div>
  </div>
    
  </div><!--end col-->
</div>

<div id="modalview" class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Preview SK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="$('#addform').submit()">Kirim</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
var tableproses;
$(document).ready(function() {

  var table = $('#datatables').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '<?= site_url('pertama/getdata')?>',
            data: function (d) {
                d.unit = $('#unit').val();
            }
        },
        columns: [
            {data: 'NIP_BARU'},
            {data: 'NAMA_LENGKAP'},
            {data: 'TAMPIL_JABATAN'},
            {data: 'SATKER_2'},
            {data: 'action', orderable: false}
        ]
    });

  $(".select2").select2();
  $(".jabatan-baru").select2();
  $('#satker1').on('change', function(event) {
    getsatker($('#satker1').val());
    $('#selectsatker2').css('display','');
  });

  $('#unit').change(function(event) {
        table.ajax.reload();
    });

      tableproses = $('#datatablesproses').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '<?= site_url('pertama/getdataproses')?>'
        },
        columns: [
            {
              data: null,
              render: function(data, type, row) {
              return row.nama_lengkap + '<br><small>' + row.nip + '</small>';
            },orderable: false},
            {data: 'tampil_jabatan'},
            {data: 'jabatan_baru'},
            {data: 'tgl_sk'},
            {data: 'kelas_jabatan'},
            {data: 'no_sk'},
            {data: 'satker'},
            {data: 'action',orderable: false}
        ],
        drawCallback: function(settings) {
            // Inisialisasi Select2 setiap kali tabel di-render ulang
            $('.jabatan-baru').select2({
                placeholder: 'Pilih Jabatan Baru',
                allowClear: true
            });
        }
    });
});

function getsatker($id) {
  axios.get('manajemen/pegawai/getcountsatker/'+$id)
  .then(function (response) {
    $('#jumlahpegawai').html(response.data);
  });
}

function add($nip) {
  const encodedNip = encodeURIComponent($nip);
  axios.get('pertama/add/'+encodedNip+'/1')
  .then(function (response) {
    if(response.data.status == 'success'){
      alert('Pegawai telah ditambahkan');
      window.location.reload();
      // resum();
    }else{
      alert(response.data.message);
    }
  });
}

function proses(button)
{
  const row = $(button).closest('tr');

  const id = $(button).data('id');
  const nip = $(button).data('nip');
  const jabatanBaru = row.find('.jabatan-baru').val();
  const noSk = row.find('#no_sk').val();
  const tglSk = row.find('#tgl_sk').val();
  const tmt = row.find('#tmt').val();
  const kelasjabatan = row.find('#kelas_jabatan').val();
  
  if (!noSk || !tglSk || !kelasjabatan) {
        alert('Semua input harus diisi!');
        return;
  }

  axios.post('pertama/proses', {
      id: id,
      nip: nip,
      jabatan_baru: jabatanBaru,
      no_sk: noSk,
      tgl_sk: tglSk,
      kelas_jabatan: kelasjabatan
  })
  .then(response => {
    console.log(response);
    
      if (response.data.status === 'success') {
          alert(response.data.message);
          console.log('Reloading table...');
          console.log(tableproses);
          // Refresh tabel setelah sukses
          tableproses.ajax.reload();
          if (tableproses) {
                tableproses.ajax.reload(null, false); // Reload tanpa reset pagination
            } else {
              alert('gagal reload table, coba reload halaman')
                console.error('tableproses is undefined!');
            }
      } else {
          alert(response.data.message);
      }
  })

}

function generate (button) {
  const id = $(button).data('id');
  const nip = $(button).data('nip');
  console.log("Menjalankan generate dengan NIP:", nip);

  const originalText = $(button).html();
  $(button).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

  axios.post('pertama/generate',{
    id: id,
    nip: nip,
  })
  .then(response => {
    console.log(response);
      if (response.data.status === true) {
          alert(response.data.message);
          // Refresh tabel setelah sukses
          tableproses.ajax.reload();
          window.location.reload();
        } else {
          window.location.reload();
          alert(response.data.message);
      }
  })
  .finally(() => {
    // Kembalikan tombol ke kondisi semula
    $(button).prop('disabled', false).html(originalText);
    tableproses.ajax.reload();
  });
}

function view (button) { 
  const url = $(button).data('url');
  window.open(url, '_blank');
}

function resum()
{
  alert('Resum');
}
</script>
<?= $this->endSection() ?>
