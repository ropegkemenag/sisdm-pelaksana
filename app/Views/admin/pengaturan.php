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

<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-pills nav-custom-outline nav-primary mb-3" role="tablist">
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link active" data-bs-toggle="tab" href="#nav-pengaturan" role="tab">Pengaturan</a>
            </li>
        </ul>
        <?php if (session()->has('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="tab-content text-muted">
            <div class="tab-pane active" id="nav-pengaturan" role="tabpanel">

                <div class="card border card-border-warning">
                    <div class="card-body">

                        <form action="<?= base_url('pengaturan/save') ?>" method="post">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= isset($seting['id']) ? $seting['id'] : '' ?>">
                            <div class="row mb-3">
                                <div class="col-lg-2">
                                    <label for="nameInput" class="form-label">Satuan Kerja</label>
                                </div>
                                <div class="col-lg-8">
                                <select id="unit" class="form-select" name="kode_satker">
                                    <option value="">Pilih Satuan Kerja</option>
                                    <?php foreach ($unit as $row) { ?>
                                        <option value="<?= $row->KODE_SATUAN_KERJA ?>" 
                                            <?= (old('kode_satker', $seting['kode_satker'] ?? '') == $row->KODE_SATUAN_KERJA) ? 'selected' : '' ?>>
                                            <?= $row->SATUAN_KERJA ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-2">
                                    <label for="websiteUrl" class="form-label">NIP Kepala</label>
                                </div>
                                <div class="col-lg-7">
                                    <!-- <input type="text" name="nip_kepala" class="form-control" id="websiteUrl" placeholder="Masukan NIP Kepala"> -->
                                    <input type="text" name="nip_kepala" class="form-control <?= session('errors.nip_kepala') ? 'is-invalid' : '' ?>" 
                                            value="<?= old('nip_kepala', isset($seting['nip_kepala']) ? $seting['nip_kepala'] : '') ?>"
                                            id="nip_kepala"
                                            placeholder="Masukan NIP Kepala">
                                    <div class="invalid-feedback">
                                        <?= session('errors.nip_kepala') ?>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <button class="btn btn-outline-primary" id="cariNip"> Cari NIP</button>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-2">
                                    <label for="websiteUrl" class="form-label">Nama Kepala</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" name="nama_kepala" class="form-control <?= session('errors.nama_kepala') ? 'is-invalid' : '' ?>" 
                                            value="<?= old('nama_kepala', isset($seting['nama_kepala']) ? $seting['nama_kepala'] : '') ?>"
                                            id="nama_kepala" 
                                            placeholder="Nama Kepala" readonly>
                                    <div id="hasilCari"></div>
                                    <div class="invalid-feedback">
                                        <?= session('errors.nama_kepala') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-2">
                                    <label for="websiteUrl" class="form-label">Jabatan Kepala</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" name="jabatan_kepala" class="form-control <?= session('errors.jabatan_kepala') ? 'is-invalid' : '' ?>" 
                                            value="<?= old('jabatan_kepala', isset($seting['jabatan_kepala']) ? $seting['jabatan_kepala'] : '') ?>"
                                            id="jabatan_kepala" 
                                            placeholder="Jabatan Kepala">
                                    <div id="hasilCari"></div>
                                    <div class="invalid-feedback">
                                        <?= session('errors.jabatan_kepala') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-2">
                                    <label for="websiteUrl" class="form-label">Lokasi Tanda Tangan</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" name="lokasi_ttd" class="form-control <?= session('errors.lokasi_ttd') ? 'is-invalid' : '' ?>" 
                                            value="<?= old('lokasi_ttd', isset($seting['lokasi_ttd']) ? $seting['lokasi_ttd'] : '') ?>"
                                            id="lokasi_ttd" 
                                            placeholder="Lokasi Tanda Tangan">
                                    <div id="hasilCari"></div>
                                    <div class="invalid-feedback">
                                        <?= session('errors.lokasi_ttd') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-2">
                                    <label for="websiteUrl" class="form-label">NIP Pengelola</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" name="nip_pengelola" class="form-control <?= session('errors.nip_pengelola') ? 'is-invalid' : '' ?>" 
                                            value="<?= old('nip_pengelola', isset($seting['nip_pengelola']) ? $seting['nip_pengelola'] : '') ?>"
                                            id="websiteUrl" 
                                            placeholder="NIP Pengelola">
                                    <div class="invalid-feedback">
                                        <?= session('errors.nip_pengelola') ?>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="text-end"> -->
                            <button type="submit" class="btn btn-primary">Update</button>
                            <!-- </div> -->
                        </form>
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
    $(document).ready(function () {
        $("#cariNip").click(function (e) { 
            e.preventDefault();
            
            let nip = $("#nip_kepala").val();
    
            axios.get('pengaturan/carinip',{
                params : {nip_kepala : nip}
            }).then((result) => {
                let data = result.data
                // console.log('jabatan',data.data.TAMPIL_JABATAN);
                // console.log('di',data.data.SATKER_3);
                // console.log('di',data.data.SATKER_4);
                // console.log(data);
                
                if (data.status) {
                    $('input[name="nama_kepala"]').val(data.data.NAMA_LENGKAP);
                    let jabatan_kepala = `${data.data.TAMPIL_JABATAN} ${data.data.SATKER_3} - ${data.data.SATKER_4}`;
                    $('input[name="jabatan_kepala"]').val(jabatan_kepala);
                }
            }).catch((err) => {
                console.error(err.message);
                alert(err.message);
            });
        });
    });
</script>
<?= $this->endSection() ?>