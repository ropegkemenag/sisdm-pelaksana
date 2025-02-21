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
        <h4 class="mb-0 card-text">Pindah Jabatan</h4>
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
        <a class="nav-link" data-bs-toggle="tab" href="#nav-proses" role="tab">Proses <span class="badge bg-danger">0</span></a>
      </li>
      <li class="nav-item waves-effect waves-light">
        <a class="nav-link" data-bs-toggle="tab" href="#nav-tt" role="tab">Tanda Tangan <span class="badge bg-danger">0</span></a>
      </li>
      <li class="nav-item waves-effect waves-light">
        <a class="nav-link" data-bs-toggle="tab" href="#nav-selesai" role="tab">Selesai <span class="badge bg-success">12</span></a>
      </li>
    </ul>
    <div class="tab-content text-muted">
      <div class="tab-pane active" id="nav-pegawai" role="tabpanel">

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
            <!-- <table id="datatablesproses" class="display table table-bordered dt-responsive fonttab" style="width: 100%;">
              <thead>
                <tr>
                  <th>NAMA/NIP</th>
                  <th>JABATAN</th>
                  <th>JABATAN BARU</th>
                  <th>TANGGAL SK</th>
                  <th>TMT</th>
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
                  <th>TMT</th>
                  <th>NO SK</th>
                  <th>UNIT KERJA</th>
                  <th></th>
                </tr>
              </tfoot>
            </table> -->
            <?= $this->include('pindah/proses.php') ?>
          </div>
        </div>
      </div>
      <div class="tab-pane" id="nav-tt" role="tabpanel">
        <div class="card border card-border-warning">
          <div class="card-body">
          <?= $this->include('pindah/tandatangan.php') ?>
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


<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {

    var table = $('#datatables').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '<?= site_url('pindah/getdata') ?>',
        data: function(d) {
          d.unit = $('#unit').val();
        }
      },
      columns: [{
          data: 'NIP_BARU'
        },
        {
          data: 'NAMA_LENGKAP'
        },
        {
          data: 'TAMPIL_JABATAN'
        },
        {
          data: 'SATKER_2'
        },
        {
          data: 'action',
          orderable: false
        }
      ]
    });

    $(".select2").select2();
    $(".jabatan-baru").select2();
    $('#satker1').on('change', function(event) {
      getsatker($('#satker1').val());
      $('#selectsatker2').css('display', '');
    });

    $('#unit').change(function(event) {
      table.ajax.reload();
    });

    // var tableproses = $('#datatablesproses').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     ajax: {
    //       url: '<?= site_url('pertama/getdataproses') ?>'
    //     },
    //     columns: [
    //         {
    //           data: null,
    //           render: function(data, type, row) {
    //           return row.nama_lengkap + '<br><small>' + row.nip + '</small>';
    //         },orderable: false},
    //         {data: 'tampil_jabatan'},
    //         {data: 'jabatan_baru'},
    //         {data: 'tgl_sk'},
    //         {data: 'tmt'},
    //         {data: 'no_sk'},
    //         {data: 'satker'},
    //         {data: 'action',orderable: false}
    //     ],
    //     drawCallback: function(settings) {
    //         // Inisialisasi Select2 setiap kali tabel di-render ulang
    //         $('.jabatan-baru').select2({
    //             placeholder: 'Pilih Jabatan Baru',
    //             allowClear: true
    //         });
    //     }
    // });
  });

  function getsatker($id) {
    axios.get('manajemen/pegawai/getcountsatker/' + $id)
      .then(function(response) {
        $('#jumlahpegawai').html(response.data);
      });
  }

  function add($nip) {
    const encodedNip = encodeURIComponent($nip);
    axios.get('pertama/add/' + encodedNip + '/2')
      .then(function(response) {
        if (response.data.status == 'success') {
          alert('Pegawai telah ditambahkan');
          resum();
        } else {
          alert(response.data.message);
        }
      });
  }

  function proses(button) {
    const row = $(button).closest('tr');

    const nip = $(button).data('nip');
    const jabatanBaru = row.find('.jabatan-baru').val();
    const noSk = row.find('#no_sk').val();
    const tglSk = row.find('#tgl_sk').val();
    const tmt = row.find('#tmt').val();

    if (!noSk || !tglSk || !tmt) {
      alert('Semua input harus diisi!');
      return;
    }

    axios.post('pertama/proses', {
        nip: nip,
        jabatan_baru: jabatanBaru,
        no_sk: noSk,
        tgl_sk: tglSk,
        tmt: tmt
      })
      .then(response => {
        if (response.data.status === 'success') {
          alert(response.data.message);
          // Refresh tabel setelah sukses
          tableproses.ajax.reload();
        } else {
          alert(response.data.message);
        }
      })
      .catch(error => {
        console.error(error);
        alert('Terjadi kesalahan saat memproses data.');
      });

  }

  function generate(button) {
    const nip = $(button).data('nip');
    console.log("Menjalankan generate dengan NIP:", nip);
    axios.post('pertama/generate', {
        nip: nip,
      })
      .then(response => {
        console.log(response);

        if (response.data.status === true) {
          alert(response.data.message);
          // Refresh tabel setelah sukses
          tableproses.ajax.reload();
        } else {
          alert(response.data.message);
        }
      })
      .catch(error => {
        console.error(error);
        alert('Terjadi kesalahan saat memproses data.');
      });
  }

  function view(button) {
    const url = $(button).data('url');
    window.open(url, '_blank');
  }

  function resum() {
    alert('Resum');
  }
</script>
<?= $this->endSection() ?>