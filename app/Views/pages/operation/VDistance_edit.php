<main id="main" class="main">

    <!-- page title -->
    <div class="pagetitle">
        <h1>Edit Distance</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("contractor-distance") ?>">Operation</a></li>
                <li class="breadcrumb-item active"><a href=#>Edit Distance</a></li>
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

                        <!-- distance modal -->
                        <form action="<?= site_url("contractor-distance/update") ?>" method="POST" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <div class="modal-body">
                                <!-- Custom Styled Validation with Tooltips -->
                                <div class="col-md-12 position-relative">
                                    <label for="prd_date" class="form-label">Production Date</label>
                                    <input value="<?= $distance['prd_date'] ?>" type="date" class="form-control" id="prd_date" name="prd_date" required>
                                    <div class="valid-tooltip">
                                        Looks good!
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="id_contractor" class="form-label">Contractor</label>
                                    <div class="col-sm-12">
                                        <select name="id_contractor" id="id_contractor" class="form-select" aria-label="Default select example">
                                            <?php foreach ($contractors as $contractor) : ?>
                                                <option value="<?= $contractor['id'] ?>" <?= $contractor['id'] == $distance['id_contractor'] ? 'selected' : null ?>><?= $contractor['contractor_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="distancecg_qty" class="form-label">Distance CG</label>
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <input value="<?= $distance['prd_cg_distance'] ?>" step="0.00000001" type="number" class="form-control" id="distancecg_qty" name="distancecg_qty" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">m</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <input value="<?= $distance['noted'] ?>" type="text" class="form-control" id="remarks" name="remarks">
                                            <div class="input-group-append">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- hidden fields below -->
                                <div class="col-md-12 mt-3">
                                    <div class="col-sm-12">
                                        <input style="display:none" value="<?= $distance['id'] ?>" type="text" class="form-control" id="id" name="id">
                                    </div>
                                    <div>
                                    <input style="display:none" name="prd_code" type="text" class="form-control" value="<?= $distance['prd_code'] ?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="<?= site_url("contractor-distance") ?> " class="btn btn-danger">Cancel</a>
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </form>
                        <!-- end of distance modal -->
                    </div>
                </div>
            </div>
        </div>
    </section>

</main> <!-- end #main -->

<script>
</script>