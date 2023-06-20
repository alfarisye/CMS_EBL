<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Production - Timesheets</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Operation</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("operation/timesheet/") ?>">Timesheets</a></li>
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
                        <h5 class="card-title">Timesheets Data</h5>
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
                                    <i class="bi bi-plus me-1"></i> Add new Timesheet
                                </button>
                            </div>
                            <div class="col-lg-4 d-flex">
                                <a href="<?= site_url("operation/timesheet/adjust") ?>" class="ms-auto button btn btn-warning text-white">Adjust</a>
                                <a href="<?= site_url("/production/report") ?>" class="ms-auto button btn btn-dark text-white"><i class="ri-line-chart-line"></i> Production Report</a>
                            </div>
                        </div>

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("operation/timesheet/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Timesheet</h5>
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
                                                <input type="text" class="form-control" id="prd_date_text" name="prd_date_text" required>
                                                <input type="date" class="form-control" id="prd_date" name="prd_date" hidden>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Contractor</label>
                                                <div class="col-sm-12">
                                                    <select name="id_contractor" id="id_contractor" class="form-select" aria-label="Default select example">
                                                        <option selected>Please Select Contractors</option>
                                                        <?php foreach ($contractors as $contractor) : ?>
                                                            <option value="<?= $contractor['id'] ?>"><?= $contractor['contractor_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6 position-relative">
                                                    <label class="form-label">OB Production Day</label>
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <input step="0.0000000001" type="number" class="form-control" id="prd_ob_day_qty" name="prd_ob_day_qty" oninput="updateSRVal()" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">MT</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 position-relative">
                                                    <label class="form-label">OB Production Night</label>
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <input step="0.0000000001" type="number" class="form-control" id="prd_ob_night_qty" name="prd_ob_night_qty" oninput="updateSRVal()" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">MT</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6 position-relative">
                                                    <label class="form-label">CG Production Day</label>
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <input step="0.0000000001" type="number" class="form-control" id="prd_cg_day_qty" name="prd_cg_day_qty" oninput="updateSRVal()" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">MT</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 position-relative">
                                                    <label class="form-label">CG Production Night</label>
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <input step="0.0000000001" type="number" class="form-control" id="prd_cg_night_qty" name="prd_cg_night_qty" oninput="updateSRVal()" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">MT</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">OB Distance</label>
                                                <input step="0.0000000001" type="number" class="form-control" id="prd_ob_distance" name="prd_ob_distance" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">CG Distance</label>
                                                <input step="0.0000000001" type="number" class="form-control" id="prd_cg_distance" name="prd_cg_distance" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">SR</label>
                                                <input step="0.0000000001" type="number" class="form-control" id="prd_sr" name="prd_sr" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Rain</label>
                                                <input step="0.0000000001" type="number" class="form-control" id="prd_rain" name="prd_rain" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Slip</label>
                                                <input step="0.0000000001" type="number" class="form-control" id="prd_slip" name="prd_slip" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">%</label>
                                                <input step="0.0000000001" type="number" class="form-control" id="prd_%" name="prd_%" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Rainfall</label>
                                                <input step="0.0000000001" type="number" class="form-control" id="prd_rainfall" name="prd_rainfall" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Remark</label>
                                                <input type="text" class="form-control" id="noted" name="noted" required>
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
                                        <th colspan="2" scope="col" class="text-center">Action</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Code</th>
                                        <th scope="col">Contractor</th>
                                        <th scope="col">OB Plan</th>
                                        <th scope="col">OB DS</th>
                                        <th scope="col">OB NS</th>
                                        <th scope="col">OB Total</th>
                                        <th scope="col">CG Plan</th>
                                        <th scope="col">CG DS</th>
                                        <th scope="col">CG NS</th>
                                        <th scope="col">CG Total</th>
                                        <th scope="col">OB Achv.</th>
                                        <th scope="col">CG Achv.</th>
                                        <th scope="col">OB Distance</th>
                                        <th scope="col">CG Distance</th>
                                        <th scope="col">SR</th>
                                        <th scope="col">Rain</th>
                                        <th scope="col">Slip</th>
                                        <th scope="col">%</th>
                                        <th scope="col">Rain Fall</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($timesheets as $timesheet) : ?>
                                        <tr>
                                            <td class="col">
                                                <a href="<?= site_url('operation/timesheet/edit/') . urlencode($timesheet['prd_code']) ?>" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                            <td class="col">
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('operation/timesheet/delete/') ?><?= $timesheet['id'] ?>" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($timesheet['status'] == 'draft') : ?>
                                                    <span class="badge rounded-pill bg-secondary"><?= $timesheet['status'] ?></span>
                                                <?php elseif ($timesheet['status'] == 'submitted') : ?>
                                                    <span class="badge rounded-pill bg-warning"><?= $timesheet['status'] ?></span>
                                                <?php elseif ($timesheet['status'] == 'approved') : ?>
                                                    <span class="badge rounded-pill bg-success"><?= $timesheet['status'] ?></span>
                                                <?php elseif ($timesheet['status'] == 'verified') : ?>
                                                    <span class="badge rounded-pill bg-primary"><?= $timesheet['status'] ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date("d-m-Y", strtotime($timesheet['prd_date'])) ?></td>
                                            <td><?= $timesheet['prd_code'] ?></td>
                                            <td><?= $timesheet['contractor_name'] ?></td>
                                            <td><?= number_format($timesheet['ob_dailybudget_qt'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_ob_day_qty'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_ob_night_qty'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_ob_total'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['cg_dailybudget_qt'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_cg_day_qty'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_cg_night_qty'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_cg_total'], 2, ',', '.') ?></td>
                                            <td><?= round(handleDivision($timesheet['prd_ob_total'], $timesheet['ob_dailybudget_qt']) * 100, 2) . " %" ?? null ?></td>
                                            <td><?= round(handleDivision($timesheet['prd_cg_total'], $timesheet['cg_dailybudget_qt']) * 100, 2) . " %" ?? null ?></td>
                                            <td><?= number_format($timesheet['prd_ob_distance'] ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_cg_distance'] ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_sr'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_rain'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_slip'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_%'], 2, ',', '.') ?></td>
                                            <td><?= number_format($timesheet['prd_rainfall'], 2, ',', '.') ?></td>
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
        let total_ob = parseFloat(ob_production_day.value) + parseFloat(ob_production_night.value);
        let total_cg = parseFloat(cg_production_day.value) + parseFloat(cg_production_night.value);
        let result = (total_ob / total_cg).toFixed(2);
        prd_sr.value = result
    };

</script>

<?= $this->endSection() ?>