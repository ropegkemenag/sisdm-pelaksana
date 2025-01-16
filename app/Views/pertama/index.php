<?= $this->extend('template') ?>

<?= $this->section('style') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
  <div class="col-lg-12">
  <ul class="nav nav-pills nav-custom-outline nav-primary mb-3" role="tablist">
      <li class="nav-item waves-effect waves-light">
          <a class="nav-link active" data-bs-toggle="tab" href="#nav-pegawai" role="tab">Data Pegawai</a>
      </li>
      <li class="nav-item waves-effect waves-light">
          <a class="nav-link" data-bs-toggle="tab" href="#nav-proses" role="tab">Proses <span class="badge bg-danger">35</span></a>
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
        <table id="datatablesproses" class="display table table-bordered dt-responsive fonttab" style="width:100%">
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
  $('#satker1').on('change', function(event) {
    getsatker($('#satker1').val());
    $('#selectsatker2').css('display','');
  });

  $('#unit').change(function(event) {
        table.ajax.reload();
    });

    var tableproses = $('#datatablesproses').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '<?= site_url('pertama/getdataproses')?>',
            data: function (d) {
                d.unit = $('#unit').val();
            }
        },
        columns: [
            {data: 'nip'},
            {data: 'NAMA_LENGKAP'},
            {data: 'TAMPIL_JABATAN'},
            {data: 'SATKER_2'},
            {data: 'action', orderable: false}
        ]
    });
});

function getsatker($id) {
  axios.get('manajemen/pegawai/getcountsatker/'+$id)
  .then(function (response) {
    $('#jumlahpegawai').html(response.data);
  });
}

function add($nip) {
  axios.get('pertama/add/'+$nip+'/1')
  .then(function (response) {
    if(response.data.status == 'success'){
      alert('Pegawai telah ditambahkan');
      resum();
    }else{
      alert(response.data.message);
    }
  });
}

function resum()
{
  alert('Resum');
}
</script>
<?= $this->endSection() ?>
