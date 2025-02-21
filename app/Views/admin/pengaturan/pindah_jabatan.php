<form action="<?= base_url('pengaturan/save') ?>" method="post">
    <?= csrf_field(); ?>
    <input type="hidden" name="id" value="<?= isset($seting_pindah['id']) ? $seting_pindah['id'] : '' ?>">
    <input type="hidden" name="jenis" value="2">
    <div class="row mb-3">
        <div class="col-lg-2">
            <label for="nameInput" class="form-label">Satuan Kerja</label>
        </div>
        <div class="col-lg-8">
            <select id="unit" class="form-select" name="kode_satker">
                <option value="">Pilih Satuan Kerja</option>
                <?php foreach ($unit as $row) { ?>
                    <option value="<?= $row->KODE_SATUAN_KERJA ?>"
                        <?= (old('kode_satker', $seting_pindah['kode_satker'] ?? '') == $row->KODE_SATUAN_KERJA) ? 'selected' : '' ?>>
                        <?= $row->SATUAN_KERJA ?>
                    </option>
                <?php } ?>
            </select>
            <div class="invalid-feedback">
                <?= session('errors.kode_satker') ?>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-2">
            <label for="websiteUrl" class="form-label">NIP Pejabat</label>
        </div>
        <div class="col-lg-7">
            <!-- <input type="text" name="nip_kepala" class="form-control" id="websiteUrl" placeholder="Masukan NIP Kepala"> -->
            <input type="text" name="nip_pejabat" class="form-control <?= session('errors.nip_pejabat') ? 'is-invalid' : '' ?>"
                value="<?= old('nip_pejabat', isset($seting_pindah['nip_pejabat']) ? $seting_pindah['nip_pejabat'] : '') ?>"
                id="nip_kepala_pindah"
                placeholder="Masukan NIP Kepala">
            <div class="invalid-feedback">
                <?= session('errors.nip_pejabat') ?>
            </div>
        </div>
        <div class="col-lg-2">
            <button class="btn btn-outline-primary" id="cariNipPindah"> Cari NIP</button>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-2">
            <label for="websiteUrl" class="form-label">Nama Pejabat</label>
        </div>
        <div class="col-lg-8">
            <input type="text" name="nama_pejabat" class="form-control <?= session('errors.nama_pejabat') ? 'is-invalid' : '' ?>"
                value="<?= old('nama_pejabat', isset($seting_pindah['nama_pejabat']) ? $seting_pindah['nama_pejabat'] : '') ?>"
                id="nama_pejabat_pindah"
                placeholder="Nama Pejabat">
            <div id="hasilCari"></div>
            <div class="invalid-feedback">
                <?= session('errors.nama_pejabat') ?>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-2">
            <label for="websiteUrl" class="form-label">Jabatan</label>
        </div>
        <div class="col-lg-8">
            <input type="text" name="jabatan" class="form-control <?= session('errors.jabatan') ? 'is-invalid' : '' ?>"
                value="<?= old('jabatan', isset($seting_pindah['jabatan']) ? $seting_pindah['jabatan'] : '') ?>"
                id="jabatan_pindah"
                placeholder="Jabatan Pejabat">
            <div id="hasilCari"></div>
            <div class="invalid-feedback">
                <?= session('errors.jabatan') ?>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-2">
            <label for="websiteUrl" class="form-label">a.n Pejabat</label>
        </div>
        <div class="col-lg-8">
            <input type="text" name="an_pejabat" class="form-control <?= session('errors.an_pejabat') ? 'is-invalid' : '' ?>"
                value="<?= old('an_pejabat', isset($seting_pindah['an_pejabat']) ? $seting_pindah['an_pejabat'] : '') ?>"
                id="an_pejabat"
                placeholder="Atas Nama Pejabat Diatas">
            <div id="hasilCari"></div>
            <div class="invalid-feedback">
                <?= session('errors.an_pejabat') ?>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-2">
            <label for="websiteUrl" class="form-label">Lokasi Ditetapkan</label>
        </div>
        <div class="col-lg-8">
            <input type="text" name="tempat_ditetapkan" class="form-control <?= session('errors.tempat_ditetapkan') ? 'is-invalid' : '' ?>"
                value="<?= old('tempat_ditetapkan', isset($seting_pindah['tempat_ditetapkan']) ? $seting_pindah['tempat_ditetapkan'] : '') ?>"
                id="tempat_ditetapkan"
                placeholder="Tempat ditetapkan">
            <div id="hasilCari"></div>
            <div class="invalid-feedback">
                <?= session('errors.tempat_ditetapkan') ?>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-2">
            <label for="websiteUrl" class="form-label">Kode Surat</label>
        </div>
        <div class="col-lg-8">
            <input type="text" name="kode_surat" class="form-control <?= session('errors.kode_surat') ? 'is-invalid' : '' ?>"
                value="<?= old('kode_surat', isset($seting_pindah['kode_surat']) ? $seting_pindah['kode_surat'] : '') ?>"
                id="kode_surat"
                placeholder="Kode Surat">
            <div id="hasilCari"></div>
            <div class="invalid-feedback">
                <?= session('errors.kode_surat') ?>
            </div>
        </div>
    </div>
    <!-- <div class="text-end"> -->
    <button type="submit" class="btn btn-primary">Update</button>
    <!-- </div> -->
</form>
