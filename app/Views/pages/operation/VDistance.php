<main id="main" class="main">

    <!-- page title -->
    <div class="pagetitle">
        <h1>Distance</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("contractor-distance") ?>">Operation</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("contractor-distance") ?>">Input Distance</a></li>
            </ol>
        </nav>
    </div>
    <!-- end page title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">Distance Coal Getting</div>
                    <div class="card-body table-responsive">

                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end of notification -->

                        <!-- add new data button -->
                        <div class="row justify-content-between">
                            <div class="col-4">
                                <br>
                                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addnewdistance">
                                    <i class="bi bi-plus me-1"></i> Add New Data
                                </button>
                            </div>
                        </div>
                        <!-- end of add new data button -->

                        <!-- distance table -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Production Date</th>
                                    <th scope="col">Contractor</th>
                                    <th scope="col">Distance CG</th>
                                    <th scope="col">Remarks</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($DataDistance as $data) : ?>
                                    <tr>
                                        <td><?= date("d-m-Y", strtotime($data['prd_date'])) ?></td>
                                        <td><?= $data['contractor_name'] ?></td>
                                        <td><?= $data['prd_cg_distance'] ?></td>
                                        <td><?= $data['noted'] ?></td>
                                        <td class="row">
                                            <a href="<?= site_url('contractor-distance/edit/') ?><?= $data['prd_code'] ?>" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('Are you sure?')" href="<?= site_url('contractor-distance/delete/') ?><?= $data['id'] ?>" class="btn btn-danger btn-sm col">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- end of distance table -->

                        <!-- distance modal -->
                        <div class="modal fade" id="addnewdistance" tabindex="-1">
                            <form action="<?= site_url("contractor-distance/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Input Data Distance</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative">
                                                <label for="prd_date" class="form-label">Production Date</label>
                                                <input type="date" class="form-control" id="prd_date" name="prd_date" required>
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
                                                <label class="form-label">Distance CG</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <input step="0.00000001" type="number" class="form-control" id="prd_cg_distance" name="prd_cg_distance" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">m</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Remarks</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="noted" name="noted">
                                                        <div class="input-group-append">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- hidden fields below -->
                                            <div class="col-md-12 mt-3">
                                                <div class="col-sm-12">
                                                    <input value=0 type="text" class="form-control" id="zerovalue" name="zerovalue" hidden>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary" type="submit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- end of distance modal -->

                    </div>
                </div>
            </div>
        </div>
    </section>

</main> <!-- end #main -->

<script>
</script>