<?php
    $contractor_type = array(
        "crush_coal" => "Crush Coal",
        "timesheet" => "Timesheet",
        "hauling" => "Hauling",
    );
?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Contractor Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Master Data</li>
                <li class="breadcrumb-item active">Contractors</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Contractors</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- // ! Add contractor modal -->
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                            <i class="bi bi-plus me-1"></i> Add new contractor
                        </button>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/master-data/contractor/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Contractor</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="username" class="form-label">Contractor name</label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Dates</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <input type="date" name="start_date" id="start_date" value="<?= $today ?>" hidden>
                                                <input type="date" name="end_date" id="end_date" value="<?= $today ?>" hidden>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="contractor_type">Contractor Type</label>
                                                <select class="form-control" name="contractor_type" id="contractor_type" required>
                                                    <option value="timesheet">Timesheet</option>
                                                    <option value="crush_coal">Crush Coal</option>
                                                    <option value="hauling">Hauling</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="status" class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="status" name="status" checked>
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
                        <!-- // ! edit contractor modal -->
                        <div class="modal fade" id="editForm" tabindex="-1">
                            <form action="<?= site_url("/master-data/contractor/update") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Contractor</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- id User -->
                                            <input type="hidden" id="id" name="id">
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="name" class="form-label">Contractor</label>
                                                <input type="text" class="form-control" id="editName" name="name" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Dates</label>
                                                <div class="row mb-3">
                                                    <div class="col">
                                                        <input class="form-control" type="date" name="start_date" id="editStartDate">
                                                    </div>
                                                    <div class="col">
                                                        <input class="form-control" type="date" name="end_date" id="editEndDate">
                                                    </div>
                                                </div>
                                                <!-- <input type="text" class="form-control" id="editDates" name="dates" required> -->
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="contractor_type">Contractor Type</label>
                                                <select class="form-control" name="contractor_type" id="editcontractor_type" required>
                                                    <option value="timesheet">Timesheet</option>
                                                    <option value="crush_coal">Crush Coal</option>
                                                    <option value="hauling">Hauling</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative">
                                                <label for="status" class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="editStatus" name="status">
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

                        <!-- Table users -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Contractor Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contractors as $contractor) : ?>
                                    <tr>
                                        <td><?= $contractor['contractor_name'] ?></td>
                                        <td><?= $contractor['start_date'] ?></td>
                                        <td><?= $contractor['end_date'] ?></td>
                                        <td><?= $contractor['contractor_type'] != '' ? $contractor_type[$contractor['contractor_type']] : '' ?></td>
                                        <td><?= $contractor['status'] ? 'Active' : 'Inactive' ?></td>
                                        <td class="row">
                                            <a onclick="editForm(<?= $contractor['id'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('Are you sure?')" href="<?= site_url('/master-data/contractor/delete/') ?><?= $contractor['id'] ?>" class="btn btn-danger btn-sm col">
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

    function editForm(id) {
        fetch("<?= site_url('/master-data/contractor/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#editName').val(res.contractor_name);
                $('#editStartDate').val(res.start_date.split(' ')[0]);
                $('#editEndDate').val(res.end_date.split(' ')[0]);
                $('#editStatus').prop('checked', res.status);
                $('#id').val(id);
                $('#editcontractor_type').val(res.contractor_type);
            })
            .then(() => {
                $('#editForm').modal('show');
            })
    }
</script>