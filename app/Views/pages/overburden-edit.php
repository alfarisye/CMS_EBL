<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Overburden edit</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Operation</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("/contractor-ob") ?>">Overburden</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("#") ?>">Edit Overburden</a></li>
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
                        <h5 class="card-title">Data Overburden edit</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end notification -->
                        <form id="overburnden" action="<?= site_url("/contractor-ob/update") ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="col-md-12 mt-3">
                                <label for="username" class="form-label">Production Date</label>
                                <input type="date" class="form-control" id="date_production" name="date_production" value="<?= $overburnden['prd_date']  ?>" >
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="form-label">Contractor</label>
                                <div class="col-sm-12">
                                    <select name="Id_contractor" id="Id_contractor" class="form-select" aria-label="Default select example">
                                        <option selected>Please Select Contractors</option>
                                        <?php foreach ($contractors as $contractor) : ?>
                                            <option value="<?= $contractor['id'] ?>"<?= $contractor['id'] == $overburnden['id_contractor'] ? 'selected' : null ?>><?= $contractor['contractor_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label for="uasername" class="form-label">Overburden QTY</label>
                                <input step="0.00000001" type="number" class="form-control" id="ob_qty" name="ob_qty" value="<?= $overburnden['prd_ob_total'] ?? 0 ?>"  required>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label for="uasername" class="form-label">Overburden Distance</label>
                                <input step="0.00000001" type="number" class="form-control" id="distanceob_qty" name="distanceob_qty" value="<?= $overburnden['prd_ob_distance'] ?? 0?>" required>
                            </div>
                    </div>
                    <div class="col-sm-4">
                        <input style="display:none" name="id" type="text" class="form-control" value="<?= $overburnden['id'] ?>" >
                    </div>
                    <input  style="display:none" name="prdCode" type="text" class="form-control" value="<?= $overburnden['prd_code'] ?>" >
                </div>
                <div class="mt-5 text-center">
                    <button id="saveButton" type="submit" class="btn btn-primary">Save</button>
                    <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                    <script>
                        function getData() {
                            window.location.href = "<?= site_url() ?>" + `/contractor-ob`;
                        }
                    </script>
                </div>
                </form>
            </div>
        </div>

</main><!-- End #main -->

<?= $this->endSection() ?>