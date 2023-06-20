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
                    <div class="card-header">
                        <nav class="d-flex justify-content-end" style="--bs-breadcrumb-divider: '>';">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item <?= $timesheets['status'] == 'draft' ? 'active text-primary' : '' ?> ">Draft</li>
                                <li class="breadcrumb-item <?= $timesheets['status'] == 'submitted' ? 'active text-primary' : '' ?>">Verify</li>
                                <li class="breadcrumb-item <?= $timesheets['status'] == 'verified' ? 'active text-primary' : '' ?>">Approve</li>
                                <li class="breadcrumb-item <?= $timesheets['status'] == 'approved' ? 'active text-primary' : '' ?>">Posted</li>
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
                        <form id="timesheetForm" action="<?= site_url("operation/timesheet/update") ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="col">
                                <div class="row mb-3">
                                    <label for="createdAt" class="col-sm-2 col-form-label">Created at</label>
                                    <div class="col-sm-4">
                                        <input name="createdAt" type="text" class="form-control" value="<?= $timesheets['create_date'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdCode" class="col-sm-2 col-form-label">Timesheet Code</label>
                                    <div class="col-sm-4">
                                        <input name="prdCode" type="text" class="form-control" value="<?= $timesheets['prd_code'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdDate" class="col-sm-2 col-form-label">Timesheet Date</label>
                                    <div class="col-sm-4">
                                        <input id="prdDate" name="prdDate" type="date" class="form-control" value="<?= $timesheets['prd_date'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="contractors" class="col-sm-2 col-form-label">Contractor</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <input name="contractors" id="contractorsInput" type="text" class="form-control" value="<?= $timesheets['id_contractor'] ?>" hidden>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <select name="contractors" id="contractors" class="form-select" required>
                                            <?php foreach ($contractors as $contractor) : ?>
                                                <option value="<?= $contractor['id'] ?>" <?= $contractor['id'] == $timesheets['id_contractor'] ? 'selected' : null ?>><?= $contractor['contractor_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="obPrdDay" class="col-sm-2 col-form-label">OB Production Day</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="obPrdDayLog" type="number" class="form-control" value="<?= $timesheet_draft['prd_ob_day_qty'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="obPrdDay" id="prd_ob_day_qty" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_ob_day_qty'] ?>" oninput="updateSRVal()" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="obPrdNight" class="col-sm-2 col-form-label">OB Production Night</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="obPrdNightLog" type="number" class="form-control" value="<?= $timesheet_draft['prd_ob_night_qty'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="obPrdNight" id="prd_ob_night_qty" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_ob_night_qty'] ?>" oninput="updateSRVal()" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="cgPrdDay" class="col-sm-2 col-form-label">CG Production Day</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="cgPrdDayLog" type="number" class="form-control" value="<?= $timesheet_draft['prd_cg_day_qty'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="cgPrdDay" id="prd_cg_day_qty" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_cg_day_qty'] ?>" oninput="updateSRVal()" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="cgPrdNight" class="col-sm-2 col-form-label">CG Production Night</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="cgPrdNightLog" type="number" class="form-control" value="<?= $timesheet_draft['prd_cg_night_qty'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="cgPrdNight" id="prd_cg_night_qty" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_cg_night_qty'] ?>" oninput="updateSRVal()" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prd_ob_distance" class="col-sm-2 col-form-label">OB Distance</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="prdObDistance" type="number" class="form-control" value="<?= $timesheet_draft['prd_ob_distance'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="prd_ob_distance" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_ob_distance'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prd_cg_distance" class="col-sm-2 col-form-label">CG Distance</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="prdCgDistance" type="number" class="form-control" value="<?= $timesheet_draft['prd_cg_distance'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="prd_cg_distance" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_cg_distance'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdSr" class="col-sm-2 col-form-label">SR</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="prdSrLog" type="number" class="form-control" value="<?= $timesheet_draft['prd_sr'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="prdSr" id="prd_sr" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_sr'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdRain" class="col-sm-2 col-form-label">Rain</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="prdRainLog" type="number" class="form-control" value="<?= $timesheet_draft['prd_rain'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="prdRain" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_rain'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdSlip" class="col-sm-2 col-form-label">Slip</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="prdSlipLog" type="number" class="form-control" value="<?= $timesheet_draft['prd_slip'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="prdSlip" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_slip'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdPercent" class="col-sm-2 col-form-label">%</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="prdPercentLog" type="number" class="form-control" value="<?= $timesheet_draft['prd_%'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="prdPercent" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_%'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdRainfall" class="col-sm-2 col-form-label">Rain Fall</label>
                                    <?php if ($timesheets['status'] != 'draft') : ?>
                                        <div class="col-sm-4">
                                            <input name="prdRainfallLog" type="number" class="form-control" value="<?= $timesheet_draft['prd_rainfall'] ?? 0 ?>" readonly>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-sm-4">
                                        <input name="prdRainfall" type="number" step="0.0000000001" class="form-control" value="<?= $timesheets['prd_rainfall'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdRemark" class="col-sm-2 col-form-label">Remark</label>
                                    <div class="col-sm-4">
                                        <input name="prdRemark" id="prdRemark" type="text" class="form-control" value="<?= $timesheets['noted'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <input id="status" name="status" type="text" class="form-control" value="<?= $timesheets['status'] ?>" hidden>
                                        <input name="id" type="text" class="form-control" value="<?= $timesheets['id'] ?>" hidden>
                                        <input name="prdRevision" type="text" class="form-control" value="<?= $timesheets['prd_revision'] ?>" hidden>
                                    </div>
                                </div>

                            </div>
                            <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>
                                <button id="submitButton" type="submit" class="btn btn-success">Submit</button>
                                <button id="submitApproveButton" type="submit" class="btn btn-success">Submit</button>
                                <button id="reviseButton" type="submit" class="btn btn-danger">Revise</button>
                                <button id="approveButton" type="submit" class="btn btn-success">Approve</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <p class="">Timesheet Logs</p>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($timesheet_logs as $id => $ts) : ?>
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
                                                            <th>CG Day</th>
                                                            <th>CG Night</th>
                                                            <th>CG Total</th>
                                                            <th>OB Day</th>
                                                            <th>OB Night</th>
                                                            <th>OB Total</th>
                                                            <th>SR</th>
                                                            <th>Rain</th>
                                                            <th>Slip</th>
                                                            <th>%</th>
                                                            <th>Rainfall</th>
                                                            <th>Revision</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><?= $ts['prd_cg_day_qty'] ?></td>
                                                            <td><?= $ts['prd_cg_night_qty'] ?></td>
                                                            <td><?= $ts['prd_cg_total'] ?></td>
                                                            <td><?= $ts['prd_ob_day_qty'] ?></td>
                                                            <td><?= $ts['prd_ob_night_qty'] ?></td>
                                                            <td><?= $ts['prd_ob_total'] ?></td>
                                                            <td><?= $ts['prd_sr'] ?></td>
                                                            <td><?= $ts['prd_rain'] ?></td>
                                                            <td><?= $ts['prd_slip'] ?></td>
                                                            <td><?= $ts['prd_%'] ?></td>
                                                            <td><?= $ts['prd_rainfall'] ?></td>
                                                            <td><?= $ts['changes'] ?></td>
                                                        </tr>
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
        if (status == 'submitted') {
            // buttons
            $('#saveButton').show();
            $('#submitButton').hide();
            $('#submitApproveButton').show();
            $('#reviseButton').hide();
            $('#approveButton').hide();
            // input
            $("#prdDate").attr('readonly', true);
            $("#contractors").attr('disabled', true);
            $("#prdRemark").attr('readonly', true);

        } else if (status == 'verified') {
            // buttons
            $('#saveButton').hide();
            $('#submitButton').hide();
            $('#submitApproveButton').hide();
            $('#reviseButton').show();
            $('#approveButton').show();

            // input
            $("input").attr('readonly', true);
            $("#contractors").attr('disabled', true);

        } else if (status == 'draft') {
            $('#saveButton').show();
            $('#submitButton').show();
            $('#submitApproveButton').hide();
            $('#reviseButton').hide();
            $('#approveButton').hide();
        } else {
            $('#saveButton').hide();
            $('#submitButton').hide();
            $('#submitApproveButton').hide();
            $('#reviseButton').hide();
            $('#approveButton').hide();
            // input
            $('input').attr('readonly', true);
            $("#contractors").attr('disabled', true);

        }

        // save button
        $("#saveButton").click(function() {
            return true;
        });

        // submit untuk verify
        $("#submitButton").click(function() {
            $("#status").val("submitted");
            return true;
        });

        // submit untuk approve
        $("#submitApproveButton").click(function() {
            $("#status").val("verified");
            return true;
        });

        //approval
        $("#approveButton").click(function() {
            if (confirm("Are you sure to approve this timesheet?")) {
                $("#status").val("approved");
                return true;
            } else {
                return false;
            }
        });

        // revise
        $("#reviseButton").click(function() {
            $("#status").val("submitted");
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

    const ob_production_day = document.getElementById("prd_ob_day_qty");
    const ob_production_night = document.getElementById("prd_ob_night_qty");
    const cg_production_day = document.getElementById("prd_cg_day_qty");
    const cg_production_night = document.getElementById("prd_cg_night_qty");
    const prd_sr = document.getElementById("prd_sr");

    const updateSRVal = function() {
        let total_ob = parseFloat(ob_production_day.value) + parseFloat(ob_production_night.value);
        let total_cg = parseFloat(cg_production_day.value) + parseFloat(cg_production_night.value);
        prd_sr.value = (total_ob / total_cg).toFixed(2);
    };
</script>

<?= $this->endSection() ?>