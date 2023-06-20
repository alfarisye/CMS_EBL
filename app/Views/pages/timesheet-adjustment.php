<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Production - Timesheet Adjustments</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("operation/timesheet/") ?>">Timesheet</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("operation/timesheet/adjust") ?>">Timesheet Adjustments</a></li>
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
                        <h5 class="card-title">Timesheet Adjustments Data</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                            <i class="bi bi-plus me-1"></i> Add new Adjustment
                        </button>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("operation/timesheet/adjust/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Timesheet Adjustment</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 mt-3">
                                                <!-- <label for="username" class="form-label">Production ID</label>
                                                <input type="text" class="form-control" id="prod_id" name="prod_id"> -->
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="dates" class="form-label">Posting Date</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <input type="date" name="start_date" id="start_date" hidden>
                                                <input type="date" name="end_date" id="end_date" hidden>
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
                                            <div class="col-md-12 mt-3">
                                                <label for="cg_adjustment">CG Adjustment</label>
                                                <input type="number" step="0.01" class="form-control" id="cg_adjustment" name="cg_adjustment" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="cg_adjustment">OB Adjustment</label>
                                                <input type="number" step="0.01" class="form-control" id="cg_adjustment" name="ob_adjustment" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="adj_cg_distance">CG Distance Adjustment</label>
                                                <input type="number" step="0.01" class="form-control" id="cg_adjustment" name="adj_cg_distance" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="adj_ob_distance">OB Distance Adjustment</label>
                                                <input type="number" step="0.01" class="form-control" id="cg_adjustment" name="adj_ob_distance" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="notes">Notes</label>
                                                <input type="text" class="form-control" id="notes" name="notes" required>
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
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Action</th>
                                        <th>Contractor</th>
                                        <th>Kode</th>
                                        <th>Doc. Date</th>
                                        <th>Posting Date Period</th>
                                        <th>CG Adjustment</th>
                                        <th>OB Adjustment</th>
                                        <th>CG Distance Adjustment</th>
                                        <th>OB Distance Adjustment</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($timesheet_adjustments as $ts) : ?>
                                        <tr>
                                            <td>
                                                <a href="<?= site_url('operation/timesheet/adjust/edit/') . urlencode($ts['code']) ?>" class="btn btn-primary btn-sm col">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('operation/timesheet/adjust/delete/') ?><?= $ts['id'] ?>" class="btn btn-danger btn-sm col">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                            <td><?= $ts['contractor_name'] ?></td>
                                            <th><?= $ts['code'] ?></th>
                                            <td><?= $ts['created_at'] ?></td>
                                            <td><?= $ts['start_date'] . " - " . $ts['end_date'] ?></td>
                                            <td><?= $ts['cg_adjustment'] ?></td>
                                            <td><?= $ts['ob_adjustment'] ?></td>
                                            <td><?= $ts['adj_cg_distance'] ?></td>
                                            <td><?= $ts['adj_ob_distance'] ?></td>
                                            <td><?= $ts['status'] ?></td>
                                        </tr>
                                    <?php endforeach ?>
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
        $('#dates').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'DD/MM/YYYY'
            },

        }, function(start, end, label) {
            $("#start_date").val(start.format('YYYY-MM-DD'));
            $("#end_date").val(end.format('YYYY-MM-DD'));
        });
        $('#dates').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        });

        $('#dates').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#start_date").val('');
            $("#end_date").val('');
        });
    });
</script>

<?= $this->endSection() ?>