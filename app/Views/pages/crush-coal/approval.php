<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Production - Crush Coal</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Operation</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("operation/crush-coal/") ?>">Crush Coals</a></li>
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
                                <li class="breadcrumb-item <?= $coals['status'] == 'draft' ? 'active text-primary' : '' ?> ">Draft</li>
                                <li class="breadcrumb-item <?= $coals['status'] == 'submitted' ? 'active text-primary' : '' ?>">Verify</li>
                                <li class="breadcrumb-item <?= $coals['status'] == 'verified' ? 'active text-primary' : '' ?>">Approve</li>
                                <li class="breadcrumb-item <?= $coals['status'] == 'approved' ? 'active text-primary' : '' ?>">Posted</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Crush Coal Data</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        <form id="timesheetForm" action="<?= site_url("operation/crush-coal/update") ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="col">
                                <div class="row mb-3">
                                    <label for="createdOn" class="col-sm-2 col-form-label">Created at</label>
                                    <div class="col-sm-4">
                                        <input name="createdOn" type="text" class="form-control" value="<?= $coals['created_on'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="createdBy" class="col-sm-2 col-form-label">Created By</label>
                                    <div class="col-sm-4">
                                        <input name="createdBy" type="text" class="form-control" value="<?= $coals['created_by'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdCode" class="col-sm-2 col-form-label">Production Code</label>
                                    <div class="col-sm-4">
                                        <input name="prdCode" type="text" class="form-control" value="<?= $coals['production_code'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="prdDate" class="col-sm-2 col-form-label">Production Date</label>
                                    <div class="col-sm-4">
                                        <input id="prdDate" name="prdDate" type="date" class="form-control" value="<?= $coals['production_date'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="contractors" class="col-sm-2 col-form-label">Contractor</label>
                                    <div class="col-sm-4">
                                        <select name="contractors" id="contractors" class="form-select" required>
                                            <?php foreach ($contractors as $contractor) : ?>
                                                <option value="<?= $contractor['id'] ?>" <?= $contractor['id'] == $coals['id_contractor'] ? 'selected' : null ?>><?= $contractor['contractor_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="rcQty" class="col-sm-2 col-form-label">RC Qty</label>
                                    <div class="col-sm-4">
                                        <input id="rcQty" name="rcQty" type="number" class="form-control" step="0.25" value="<?= $coals['rc_qty'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="crushers" class="col-sm-2 col-form-label">Crusher</label>
                                    <div class="col-sm-4">
                                        <select name="crushers" id="crushers" class="form-select" required>
                                            <?php foreach ($crushers as $crusher) : ?>
                                                <option value="<?= $crusher['id'] ?>" <?= $crusher['id'] == $coals['id_crusher'] ? 'selected' : null ?>><?= $crusher['crusher_description'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="ccQty" class="col-sm-2 col-form-label">CC Qty</label>
                                    <div class="col-sm-4">
                                        <input id="ccQty" name="ccQty" type="number" class="form-control" step="0.25" value="<?= $coals['cc_qty'] ?>" required>
                                    </div>
                                </div>
                                <!-- hidden fields below -->
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <input id="status" name="status" type="text" class="form-control" value="<?= $coals['status'] ?>" hidden>
                                        <input name="id" type="text" class="form-control" value="<?= $coals['id'] ?>" hidden>
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
            $("input").attr('readonly', true);
            $("#contractors").attr('disabled', true);
            $("#crushers").attr('disabled', true);

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
            $("#crushers").attr('disabled', true);

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
            $("#crushers").attr('disabled', true);

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