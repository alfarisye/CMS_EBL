<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Operation - Overburden</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Operation</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("/contractor-ob") ?>">Overburden</a></li>
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
                        <h5 class="card-title">Overburden Data</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->
                        <div class="row mb-3">
                            <div class="col-lg-8 d-flex">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Add New Data
                                </button>
                            </div>
                            

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/contractor-ob/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Add New Data</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 mt-3">
                                                <!-- <label for="username" class="form-label">Production ID</label>
                                                <input type="text" class="form-control" id="prod_id" name="prod_id"> -->
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="username" class="form-label">Production Date</label>
                                                <input type="date" class="form-control" id="date_production" name="date_production" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Contractor</label>
                                                <div class="col-sm-12">
                                                    <select name="Id_contractor" id="Id_contractor" class="form-select" aria-label="Default select example">
                                                        <option selected>Please Select Contractors</option>
                                                        <?php foreach ($contractors as $contractor) : ?>
                                                            <option value="<?= $contractor['id'] ?>"><?= $contractor['contractor_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="uasername" class="form-label">Overburden QTY</label>
                                                <input step="0.00000001" type="number" class ="form-control" id="ob_qty" name="ob_qty" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="uasername" class="form-label">Overburden Distance</label>
                                                <input step="0.00000001" type="number" class ="form-control" id="distanceob_qty" name="distanceob_qty" required>
                                            </div>
                                           
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
                            <table class="table table-hover table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Production Date</th>
                                        <th scope="col">Contractor</th>
                                        <th scope="col">Overburden Qty</th>
                                        <th scope="col">Overburden Distance</th>
                                        
                                        <th colspan="2" scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($overburnden as $ob) : ?>
                                        <tr>
                                            <td><?= date("d-m-Y", strtotime($ob['prd_date'])) ?></td>
                                            <td><?= $ob['contractor_name'] ?></td>
                                            <td><?= $ob['prd_ob_total'] ?></td>
                                            <td><?= $ob['prd_ob_distance'] ?></td>
                                            
                                            <td class="col">
                                                <a href="<?= site_url('contractor-ob/edit/') . urlencode($ob['prd_code']) ?>" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                            <td class="col">
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('contractor-ob/delete/') ?><?= $ob['id'] ?>" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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

        $('#prd_date_text').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            minMonth: new Date().getMonth(),
            locale: {
                format: 'DD/MM/YYYY'
            },

        }, function(start, end, label) {
            $("#prd_date").val(start.format('YYYY-MM-DD'));
        });
        $('#prd_date_text').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
            $("#prd_date").val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('#prd_date_text').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#prd_date").val('');
        });
    });
    const ob_production_day = document.getElementById("prd_ob_day_qty");
    const ob_production_night = document.getElementById("prd_ob_night_qty");
    const cg_production_day = document.getElementById("prd_cg_day_qty");
    const cg_production_night = document.getElementById("prd_cg_night_qty");
    const prd_sr = document.getElementById("prd_sr");

    const updateSRVal = function() {
        let total_ob = ob_production_day.value + ob_production_night.value;
        let total_cg = cg_production_day.value + cg_production_night.value;
        prd_sr.value = (total_ob / total_cg).toFixed(2);
    };

</script>

<?= $this->endSection() ?>