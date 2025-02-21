<?= $this->extend('template') ?>

<?= $this->section('style') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    input[readonly] {
        background-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman -->
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card card-light">
            <div class="card-body">
                <h4 class="mb-0 card-text">Pengaturan</h4>
                <p class="mb-0">Kelola pengaturan untuk SK Pengangkatan Pertama, CPNS, Pindah Jabatan.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-pills nav-custom-outline nav-primary mb-3" role="tablist">
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link active" data-bs-toggle="tab" href="#navpertama" role="tab">Penyesuian Nomenklatur</a>
            </li>
            <!-- <li class="nav-item waves-effect waves-light">
                <a class="nav-link" data-bs-toggle="tab" href="#navpindahjabatan" role="tab">Pindah Jabatan</a>
            </li> -->
            <!-- <li class="nav-item waves-effect waves-light">
                <a class="nav-link" data-bs-toggle="tab" href="#navskpns" role="tab">SK PNS</a>
            </li> -->
            <!-- <li class="nav-item waves-effect waves-light">
                <a class="nav-link" data-bs-toggle="tab" href="#navpenyelesaian" role="tab">Penyelesaian</a>
            </li> -->
        </ul>
        <?php if (session()->has('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="tab-content text-muted">
            <div class="tab-pane active" id="navpertama" role="tabpanel">
                <div class="card border card-border-warning">
                    <div class="card-body">
                        <?= $this->include('admin/pengaturan/pengakatan_pertama.php') ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="navpindahjabatan" role="tabpanel">
                <div class="card border card-border-warning">
                    <div class="card-body">
                        <?= $this->include('admin/pengaturan/pindah_jabatan.php') ?>
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
        $("#cariNip").click(function(e) {
            e.preventDefault();
            alert('oke pertama');
            let nip = $("#nip_kepala_pertama").val();

            cariPegawai(nip,'pertama');
        });

        $("#cariNipPindah").click(function(e) {
            e.preventDefault();
            alert('oke pindah');
            let nip = $("#nip_kepala_pindah").val();
            cariPegawai(nip,'pindah');
        });

        function cariPegawai(nip,target) {
            axios.get('pengaturan/carinip', {
                params: {
                    nip_kepala: nip
                }
            }).then((result) => {
                let data = result.data
                // console.log('jabatan',data.data.TAMPIL_JABATAN);
                // console.log('di',data.data.SATKER_3);
                // console.log('di',data.data.SATKER_4);
                // console.log(data);

                if (data.status) {
                    $(`#nama_pejabat_${target}`).val(data.data.NAMA_LENGKAP);
                    let jabatan_kepala = `${data.data.TAMPIL_JABATAN} ${data.data.SATKER_3} - ${data.data.SATKER_4}`;
                    $(`#jabatan_${target}`).val(jabatan_kepala);
                }
            }).catch((err) => {
                console.error(err.message);
                alert(err.message);
            });
        }
    });
</script>
<?= $this->endSection() ?>