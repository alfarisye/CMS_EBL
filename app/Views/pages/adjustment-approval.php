<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Production - Timesheets</h1>
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
                    <div class="card-header">
                        <nav class="d-flex justify-content-end" style="--bs-breadcrumb-divider: '>';">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item <?= $timesheet_adjustment['status'] == 'draft' ? 'active text-primary' : '' ?> ">Draft</li>
                                <li class="breadcrumb-item <?= $timesheet_adjustment['status'] == 'verify' ? 'active text-primary' : '' ?>">Verify</li>
                                <li class="breadcrumb-item <?= $timesheet_adjustment['status'] == 'posted' ? 'active text-primary' : '' ?>">Posted</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Timesheets Data</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        <form id="timesheetForm" action="<?= site_url("operation/timesheet/adjust/update") ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="col">
                                <div class="row mb-3">
                                    <label for="created_at" class="col-sm-2 col-form-label">Created at</label>
                                    <div class="col-sm-4">
                                        <input name="created_at" type="text" class="form-control" value="<?= $timesheet_adjustment['created_at'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="code" class="col-sm-2 col-form-label">Code</label>
                                    <div class="col-sm-4">
                                        <input name="code" type="text" class="form-control" value="<?= $timesheet_adjustment['code'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="start_date" class="col-sm-2 col-form-label">Start Date</label>
                                    <div class="col-sm-4">
                                        <input id="start_date" name="start_date" type="date" class="form-control" value="<?= $timesheet_adjustment['start_date'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="end_date" class="col-sm-2 col-form-label">End Date</label>
                                    <div class="col-sm-4">
                                        <input id="end_date" name="end_date" type="date" class="form-control" value="<?= $timesheet_adjustment['end_date'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Contractor</label>
                                    <div class="col-sm-4">
                                        <select name="id_contractor" id="id_contractor" class="form-select" aria-label="Default select example">
                                            <option selected>Please Select Contractors</option>
                                            <?php foreach ($contractors as $contractor) : ?>
                                                <option value="<?= $contractor['id'] ?>" <?= $contractor['id'] == $timesheet_adjustment['id_contractor'] ? 'selected' : null ?>><?= $contractor['contractor_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="cg_adjustment" class="col-sm-2 col-form-label">CG Adjustment</label>
                                    <div class="col-sm-4">
                                        <input id="cg_adjustment" step="0.01" name="cg_adjustment" type="number" class="form-control" value="<?= $timesheet_adjustment['cg_adjustment'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="ob_adjustment" class="col-sm-2 col-form-label">OB Adjustment</label>
                                    <div class="col-sm-4">
                                        <input id="ob_adjustment" step="0.01" name="ob_adjustment" type="number" class="form-control" value="<?= $timesheet_adjustment['ob_adjustment'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="adj_cg_distance" class="col-sm-2 col-form-label">CG Distance Adjustment</label>
                                    <div class="col-sm-4">
                                        <input id="adj_cg_distance" step="0.01" name="adj_cg_distance" type="number" class="form-control" value="<?= $timesheet_adjustment['adj_cg_distance'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="adj_ob_distance" class="col-sm-2 col-form-label">OB Distance Adjustment</label>
                                    <div class="col-sm-4">
                                        <input id="adj_ob_distance" step="0.01" name="adj_ob_distance" type="number" class="form-control" value="<?= $timesheet_adjustment['adj_ob_distance'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                                    <div class="col-sm-4">
                                        <input id="notes" name="notes" type="text" class="form-control" value="<?= $timesheet_adjustment['notes'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <input id="status" name="status" type="text" class="form-control" value="<?= $timesheet_adjustment['status'] ?>" hidden>
                                        <input name="id" type="text" class="form-control" value="<?= $timesheet_adjustment['id'] ?>" hidden>
                                    </div>
                                </div>

                            </div>
                            <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>
                                <button id="submitButton" type="submit" class="btn btn-success">Submit</button>
                                <button id="reviseButton" type="submit" class="btn btn-danger">Revise</button>
                                <button id="approveButton" type="submit" class="btn btn-success">Approve</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <p class="">Logs</p>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($logs as $id => $ts) : ?>
                                <div class="accordion accordion-flush" id="log-group">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" data-bs-target="#log-<?= $id ?>" type="button" data-bs-toggle="collapse">
                                                Date: <?= $ts['created_at'] ?> / Actor: <?= $ts['actor'] ?> / Status: <?= $ts['status'] ?>
                                            </button>
                                        </h2>
                                        <div id="log-<?= $id ?>" class="accordion-collapse collapse" data-bs-parent="#log-group">
                                            <div class="accordion-body">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                            <th>CG Adjustment</th>
                                                            <th>OB Adjustment</th>
                                                            <th>CG Distance Adjustment</th>
                                                            <th>OB Distance Adjustment</th>
                                                            <th>Notes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><?= $ts['start_date'] ?></td>
                                                            <td><?= $ts['end_date'] ?></td>
                                                            <td><?= $ts['cg_adjustment'] ?></td>
                                                            <td><?= $ts['ob_adjustment'] ?></td>
                                                            <td><?= $ts['adj_cg_distance'] ?></td>
                                                            <td><?= $ts['adj_ob_distance'] ?></td>
                                                            <td><?= $ts['notes'] ?></td>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<script>
    $(document).ready(function() {
        let status = $('#status').val();
        if (status == 'draft') {
            // buttons
            $('#saveButton').show();
            $('#submitButton').show();
            $('#reviseButton').hide();
            $('#approveButton').hide();

        } else if (status == 'verify') {
            $('#saveButton').hide();
            $('#submitButton').hide();
            $('#reviseButton').show();
            $('#approveButton').show();
            $('input').attr('readonly', true);
        } else {
            $('#saveButton').hide();
            $('#submitButton').hide();
            $('#reviseButton').hide();
            $('#approveButton').hide();
            // input
            $('input').attr('readonly', true);
        }

        // save button
        $("#saveButton").click(function() {
            return true;
        });

        // submit untuk verify
        $("#submitButton").click(function() {
            $("#status").val("verify");
            return true;
        });

        //approval
        $("#approveButton").click(function() {
            if (confirm("Are you sure to approve this adjustment?")) {
                $("#status").val("posted");
                return true;
            } else {
                return false;
            }
        });

        // revise
        $("#reviseButton").click(function() {
            $("#status").val("draft");
            return true;
        });


        // stop enter key
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
</script>

<?= $this->endSection() ?>