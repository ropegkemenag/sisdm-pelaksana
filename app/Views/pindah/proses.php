<table id="datatablesproses" class="display table table-bordered dt-responsive fonttab" style="width: 100%;">
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
</table>
<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {

    var tableproses = $('#datatablesproses').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '<?= site_url('pindah/getdataproses')?>'
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
            {data: 'tmt'},
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

function proses(button)
{
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

function generate (button) {
  const nip = $(button).data('nip');
  console.log("Menjalankan generate dengan NIP:", nip);
  axios.post('pertama/generate',{
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

function view (button) { 
  const url = $(button).data('url');
  window.open(url, '_blank');
}
</script>
<?= $this->endSection() ?>