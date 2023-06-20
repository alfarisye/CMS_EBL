<main id="main" class="main">

    <div class="pagetitle">
        <h1>Crush Coal Adjust</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Crush Coal</li>
                <li class="breadcrumb-item active">Adjustment</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Crush Coal Adjustment</h5>
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
                                    <i class="bi bi-plus me-1"></i> Add new Adjustment
                                </button>
                            </div>
                        </div>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/crush-coal/adjust/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Adjustment</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative">
                                                <label for="dates" class="form-label">Posting Date</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <input type="date" name="start_date" id="start_date" hidden>
                                                <input type="date" name="end_date" id="end_date" hidden>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Crush Coal Adjustment</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <input step="0.01" type="number" class="form-control" id="cc_adjustment" name="cc_adjustment" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">MT</span>
                                                        </div>
                                                    </div>
                                                </div>
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

                        <!-- Table users -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Code</th>
                                    <th scope="col">Doc. Date</th>
                                    <th scope="col">Posting Date Period</th>
                                    <th scope="col">CC Adjustment</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($adjustments as $a) : ?>
                                    <tr>
                                        <td><?= $a['code'] ?></td>
                                        <td><?= $a['doc_date'] ?></td>
                                        <td><?= $a['post_date_start'] ?> - <?= $a['post_date_end'] ?></td>
                                        <td><?= $a['cc_adjustment'] ?></td>
                                        <td><?= $a['status'] ?></td>
                                        <td class="row">
                                            <a href="<?= site_url('/crush-coal/adjust/edit/') ?><?= $a['id'] ?>" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('Are you sure?')" href="<?= site_url('/crush-coal/adjust/delete/') ?><?= $a['id'] ?>" class="btn btn-danger btn-sm col">
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

    // $("#editDates").daterangepicker({}, function(start, end, label) {
    //     $('#editStartDate').val(start.format('YYYY-MM-DD'));
    //     $('#editEndDate').val(end.format('YYYY-MM-DD'));
    // });
</script>