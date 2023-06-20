<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>CSR - CSR Activity</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">CSR</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("CSRAct/") ?>">CSR Activity</a></li>
                <li class="breadcrumb-item active"><a href="#">Edit Activity</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- //! this will be the filter -->
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">CSR Activity edit</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        <form id="CSRAct" action="<?= site_url("/CSRAct/update") ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="row mb-3">
                                <div class="col-md-11 mt-3">
                                    <label class="form-label">FORM</label>
                                    <div class="col-md-12 position-relative">
                                        <select class="form-select" name="formtyp_act" id="from" required>
                                            <option value="FORM PPM" <?= "FORM PPM" == $Activitycsr['formtyp_act'] ? 'selected' : null ?>>FORM PPM</option>
                                            <option value="NON PPM" <?= "NON PPM" == $Activitycsr['formtyp_act'] ? 'selected' : null ?>>NON PPM</option>
                                            <option value="BOTH" <?= "BOTH" == $Activitycsr['formtyp_act'] ? 'selected' : null ?>>BOTH</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-11 mt-3">
                                        <label for="date" class="col-sm-2 col-form-label">Date</label>
                                        <div class="col-sm-12">
                                            <input type="date" class="form-control" id="date" name="date" value="<?= $Activitycsr['date'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-11 mt-3">
                                            <label class="form-label">Allocation</label>
                                            <div class="col-sm-12">
                                                <select name="allocation" id="allocation" class="form-select" aria-label="Default select example" required>
                                                    <?php foreach ($new_allo as $a) : ?>
                                                        <option value="<?= $a['id_allo'] ?>" <?= $a['allocation'] == $Activitycsr['allocation'] ? 'selected' : null ?>><?= $a['allocation'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-11 mt-3">
                                            <label class="form-label">Location</label>
                                            <div class="col-sm-12">
                                                <input name="location" id="location" class="form-control" aria-label="Default select example" value="<?= $Activitycsr['location'] ?>" required></input>
                                            </div>
                                        </div>
                                        <div class="col-md-11 mt-3">
                                            <label class="form-label">Activity</label>
                                            <div class="col-sm-12">
                                                <input name="activity" id="activity" class="form-control" aria-label="Default select example" value="<?= $Activitycsr['activity'] ?>" required></input>
                                            </div>
                                        </div>
                                        <div class="col-md-11 mt-3">
                                            <label class="form-label">Remark</label>
                                            <div class="col-sm-12">
                                                <input name="Remark" id="Remark" class="form-control" aria-label="Default select example" value="<?= $Activitycsr['Remark'] ?>" required></input>
                                            </div>
                                        </div>
                                        <div class="col-md-10 mt-3">
                                            <label class="form-label">Total Cost</label>
                                            <div class="col-sm-12">
                                                <div class="input-group">
                                                    <input id="rupiah" class="form-control" rows="6" placeholder="Rp." value="<?= $Activitycsr['actual_cost'] ?>" required></input>
                                                    <input id="rupiah2" class="form-control" name="actual_cost" id="actual_cost" rows="6" placeholder="Rp." value="<?= $Activitycsr['actual_cost'] ?>" hidden></input>
                                                    <span class="input-group-text">IDR</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-10 mt-3">
                                            <label class="form-label">Upload File</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/vnd.rar" id="userfile" name="userfile" class="form-control p-1 text-xs rounded-sm shadow-sm" placeholder="userfile"></input>
                                                    <span class="input-group-text">*MAX 5MB</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">

                                                <input name="id" type="text" class="form-control" value="<?= $Activitycsr['id'] ?>" hidden>

                                            </div>
                                        </div>
                                        <div class="mt-5 text-center">
                                            <button id="saveButton" type="submit" class="btn btn-primary">Save</button>
                                            <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                            <script>
                                                function getData() {
                                                    window.location.href = "<?= site_url() ?>" + `/CSRAct`;
                                                }
                                            </script>
                                        </div>
                        </form>
                    </div>
                </div>

</main><!-- End #main -->

<script>
    $(document).ready(function() {
        $('#type').change(function() {
            var id_type = $(this).val();

            var action = 'get_category';

            if (id_type != '') {
                $.ajax({
                    url: "<?php echo site_url('k3lh/action/'); ?>" + id_type,
                    method: "GET",
                    success: function(data) {
                        console.log(data);
                        const obj = JSON.parse(data);
                        $('#Category').empty();
                        $.each(obj, function(key, value) {
                            $('#Category').append('<option value="' + value.Id_category + '">' + value.category + '</option>');
                        });

                    }
                });
            } else {
                $('#category').val('');
            }
        });
    });
</script>

<script type="text/javascript">
    var rupiahnumber = document.getElementById('rupiah2');
    var rupiah = document.getElementById('rupiah');
    rupiah.addEventListener('keyup', function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        console.log(this.value);
        rupiahnumber.value = formatBiasa(this.value);
        rupiah.value = formatRupiah(this.value, 'Rp. ');
    });

    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);


        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');

    }

    function formatBiasa(angka) {
        var number_biasa = angka.split(".").join("").split("Rp").join("");

        return number_biasa;
    }
</script>

<?= $this->endSection() ?>