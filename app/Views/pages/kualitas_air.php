<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>K3LH - Kualitas Air</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">K3LH</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("kualitasair/") ?>">Kualitas Air</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Kualitas Air</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->

                        <div class="d-flex justify-content-between mb-4">
                            <div class="d-flex left-section col-5">
                                <input class="form-control" type="date" id="tgl_awal" name="tgl_awal" placeholder="tgl_awal" value="<?= date('Y-m-d'); ?>">
                                <span class="mx-2 my-auto">to</span>
                                <input class="form-control" type="date" id="tgl_akhir" name="tgl_akhir" placeholder="tgl_akhir" value="<?= date('Y-m-d'); ?>">
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">Filter</button>
                                <script>
                                    function getData() {
                                        let tgl_awal = document.getElementById('tgl_awal').value
                                        let tgl_akhir = document.getElementById('tgl_akhir').value
                                        window.location.href = "<?= site_url() ?>" + `/kualitasair?tgl_awal=${tgl_awal}&tgl_akhir=${tgl_akhir}`;
                                    }
                                </script>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="refresh" name="refresh" onclick="getData2()">Refresh</button>
                                <script>
                                    function getData2() {
                                        let tgl_awal = document.getElementById('tgl_awal').value
                                        let tgl_akhir = document.getElementById('tgl_akhir').value
                                        window.location.href = "<?= site_url() ?>" + `/kualitasair`;
                                    }
                                </script>
                            </div>
                            <div class="right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Tambah Data
                                </button>
                            </div>
                        </div>

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("kualitasair/add") ?>" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">New Kualitas Air</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Location</label>
                                                <div class="col-sm-12">
                                                    <input name="location" id="location" class="form-control" aria-label="Default select example" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <!-- <label for="username" class="form-label">Production ID</label>
                                                <input type="text" class="form-control" id="prod_id" name="prod_id"> -->
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="username" class="form-label">Tanggal</label>
                                                <input type="text" class="form-control" id="prd_date_text" name="prd_date_text" required>
                                                <input type="date" class="form-control" id="date" name="date" hidden>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Debit Air</label>
                                                <div class="col-sm-12">
                                                    <input name="debit_air" id="debit_air" class="form-control" aria-label="Default select example" required></input>
                                                </div>
                                            </div>
                                            <?php foreach ($air_para as $form) : ?>
                                                <div class="col-md-12 mt-3">
                                                    <div class="sm-form">
                                                        <label class="form-label" for="<?= $form['nama_parameter'] ?>"><?= $form['nama_parameter'] ?></label>
                                                        <div class="col-sm-12">
                                                            <input type="number" id="<?= $form['id_Parameter'] ?>" value="<?= $form['id_Parameter'] ?>" name="idParameter[]" class="form-control" aria-label="Default select example" hidden></input>
                                                            <input name="valueParameter[]" id="value" class="form-control" aria-label="Default select example"></input>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>


                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary" type="submit">Submit form</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <!-- Table users -->
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Lokasi</th>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Debit air</th>
                                        <?php foreach ($air_para as $form) : ?>
                                            <th><?= $form['nama_parameter'] ?></th>
                                        <?php endforeach; ?>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kuali_air as $woer) : ?>
                                        <tr>
                                            <td><?= $woer['no_data'] ?></td>
                                            <td><?= $woer['location'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($woer['date'])) ?></td>
                                            <td><?= $woer['debit_air'] ?></td>
                                            <?php foreach ($air_para as $form) : ?>
                                                <td><?= array_reduce($para_kuali, function ($acc, $work) use ($woer, $form) {
                                                        return $form['id_Parameter'] == $work['id_Parameter'] && $work['id_KualitasAir'] == $woer['id_KualitasAir'] ? $work['value'] : $acc;
                                                    }, '') ?></td>
                                            <?php endforeach; ?>
                                            <td class="col">
                                                <a href="<?= site_url('kualitasair/edit/') . urlencode($woer['id_KualitasAir']) ?>" class="btn btn-primary btn-sm col">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('kualitasair/delete/') ?><?= $woer['id_KualitasAir'] ?>" class="btn btn-danger btn-sm col">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- End Table with stripped rows -->
                    </div>

                </div>
            </div>
        </div>
        </div>
    </section>

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
<script>
    $(document).ready(function() {
        $('#prd_date_text').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            minMonth: new Date().getMonth(),
            locale: {
                format: 'DD/MM/YYYY'
            },

        }, function(start, end, label) {
            $("#date").val(start.format('YYYY-MM-DD'));
        });
        $('#prd_date_text').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
        });

        $('#prd_date_text').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#date").val('');
        });
    });
</script>


<?= $this->endSection() ?>