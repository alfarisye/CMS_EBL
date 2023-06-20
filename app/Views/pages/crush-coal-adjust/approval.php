<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Production - Crush Coal Adjustment</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Crush Coal</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("operation/crush-coal/adjust/") ?>">Adjustment</a></li>
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
                                <li class="breadcrumb-item <?= $coals['status'] == 'posted' ? 'active text-primary' : '' ?>">Posted</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Crush Coal Adjustment</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        <form id="timesheetForm" action="<?= site_url("/crush-coal/adjust/update") ?>" method="POST">
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
                                    <label for="prdCode" class="col-sm-2 col-form-label">Code</label>
                                    <div class="col-sm-4">
                                        <input name="prdCode" type="text" class="form-control" value="<?= $coals['code'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="start_date" class="col-sm-2 col-form-label">Posting Date Start</label>
                                    <div class="col-sm-4">
                                        <input id="start_date" name="start_date" type="date" class="form-control" value="<?= $coals['post_date_start'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="end_date" class="col-sm-2 col-form-label">Posting Date End</label>
                                    <div class="col-sm-4">
                                        <input id="end_date" name="end_date" type="date" class="form-control" value="<?= $coals['post_date_end'] ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Crush Coal Adjustment</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <input step="0.01" type="number" class="form-control" id="cc_adjustment" name="cc_adjustment" value="<?= $coals['cc_adjustment'] ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">MT</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label" for="notes">Notes</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="notes" name="notes" value="<?= $coals['notes'] ?>" required>
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
                                <button id="approveButton" type="submit" class="btn btn-success">Posting</button>
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
        if (status == 'draft') {
            $('#saveButton').show();
            $('#approveButton').show();
        } else {
            $('#saveButton').hide();
            $('#submitButton').hide();
            $('#submitApproveButton').hide();
            $('#reviseButton').hide();
            $('#approveButton').hide();
            // input
            $('input').attr('readonly', true);

        }

        // save button
        $("#saveButton").click(function() {
            return true;
        });

        //approval
        $("#approveButton").click(function() {
            if (confirm("Are you sure to post this?")) {
                $("#status").val("posted");
                return true;
            } else {
                return false;
            }
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