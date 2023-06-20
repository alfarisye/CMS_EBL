<main id="main" class="main">

    <div class="pagetitle">
        <h1>Crush Coal Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Operation</li>
                <li class="breadcrumb-item active">Crush Coal</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Crush Coals</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- // ! Add crush coal modal -->
                        <div class="row justify-content-between">
                            <div class="col-4">
                                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Add new Crush Coal
                                </button>
                            </div>
                            <div class="col-4">
                                <a href="<?= site_url("/crush-coal/adjust") ?>" class="btn btn-warning text-white float-right">Adjust</a>
                            </div>
                        </div>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("operation/crush-coal/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Crush Coal</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative">
                                                <label for="production_date" class="form-label">Production Date</label>
                                                <input type="date" class="form-control" id="production_date" name="production_date" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
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
                                                <label class="form-label">Raw Coal Qty</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <input step="0.01" type="number" class="form-control" id="rc_qty" name="rc_qty" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">MT</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Crusher</label>
                                                <div class="col-sm-12">
                                                    <select name="id_crusher" id="id_crusher" class="form-select" aria-label="Default select example">
                                                        <option selected>Please Select Crushers</option>
                                                        <?php foreach ($crushers as $c) : ?>
                                                            <option value="<?= $c['id'] ?>"><?= $c['crusher_description'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Crush Coal Qty</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <input step="0.01" type="number" class="form-control" id="cc_qty" name="cc_qty" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">MT</span>
                                                        </div>
                                                    </div>
                                                </div>
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
                        <!-- // ! edit crush coal modal -->
                        <div class="modal fade" id="editForm" tabindex="-1">
                            test
                        </div>

                        <!-- Table users -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Contractor</th>
                                    <th scope="col">RC Qty</th>
                                    <th scope="col">Crusher</th>
                                    <th scope="col">CC Qty</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($crush_coals as $cc) : ?>
                                    <tr>
                                        <td><?= date("d-m-Y", strtotime($cc['production_date'])) ?></td>
                                        <td><?= $cc['production_code'] ?></td>
                                        <td><?= $cc['contractor_name'] ?></td>
                                        <td><?= $cc['rc_qty'] ?></td>
                                        <td><?= $cc['crusher_description'] ?></td>
                                        <td><?= $cc['cc_qty'] ?></td>
                                        <td><?= $cc['status'] ?></td>
                                        <td class="row">
                                            <a href="<?= site_url('operation/crush-coal/edit/') ?><?= $cc['id'] ?>" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('Are you sure?')" href="<?= site_url('operation/crush-coal/delete/') ?><?= $cc['id'] ?>" class="btn btn-danger btn-sm col">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->
<script>
    $("#dates").daterangepicker({}, function(start, end, label) {
        $('#start_date').val(start.format('YYYY-MM-DD'));
        $('#end_date').val(end.format('YYYY-MM-DD'));
    });

    // $("#editDates").daterangepicker({}, function(start, end, label) {
    //     $('#editStartDate').val(start.format('YYYY-MM-DD'));
    //     $('#editEndDate').val(end.format('YYYY-MM-DD'));
    // });
</script>