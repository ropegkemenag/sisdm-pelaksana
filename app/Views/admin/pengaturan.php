<?= $this->extend('template') ?>

<?= $this->section('style') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                            <input type="text" name="id" value="<?= isset($seting['id']) ? $seting['id'] : '' ?>">
                            <div class="row mb-3">
                                <div class="col-lg-2">
                                    <label for="nameInput" class="form-label">Satuan Kerja</label>
                                </div>
                                <div class="col-lg-8">
                                    <select id="unit" class="form-select" name="nama_satker">
                                        <option value="">Pilih Satuan Kerja</option>
                                        <?php foreach ($unit as $row) {
                                            echo '<option value="' . $row->KODE_SATUAN_KERJA . '">' . $row->SATUAN_KERJA . '</option>';
                                        }  ?>
                                    </select>
                                    <!-- <input type="text" class="form-control" id="nameInput" placeholder="Enter your name"> -->
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
                                            placeholder="Masukan NIP Kepala">
                                    <div class="invalid-feedback">
                                        <?= session('errors.nip_kepala') ?>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <button class="btn btn-outline-primary"> Cari NIP</button>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-2">
                                    <label for="websiteUrl" class="form-label">Nama Kepala</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" name="nama_kepala" class="form-control <?= session('errors.nama_kepala') ? 'is-invalid' : '' ?>" 
                                            value="<?= old('nama_kepala', isset($seting['nama_kepala']) ? $seting['nama_kepala'] : '') ?>"
                                            id="websiteUrl" 
                                            placeholder="Nama Kepala">
                                    <div class="invalid-feedback">
                                        <?= session('errors.nama_kepala') ?>
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
</script>
<?= $this->endSection() ?>