<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Sales - Cost Mining - Edit Cost Mining</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
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
                        <h5 class="card-title">Edit Cost Mining</h5>
                        <!-- notification -->
                            <?php if (session()->getFlashdata('message')) : ?>
                                <div class="alert alert-warning" role="alert">
                                    <p><?= session()->getFlashdata('message') ?></p>
                                </div>
                            <?php endif ?>
                        <!-- end notification -->
                        <form id="editcostmining" action="<?= site_url("/sales/costmining/update") ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="col-md-12 position-relative mb-3">
                                <label for="year" class="form-label">Year</label>
                                <input type="text" class="form-control" id="year" name="year"  value="<?= $cost['year'] ?>" required>
                            </div>
                            <div class="col-md-12 position-relative mb-3">
                                <label for="month" class="form-label">Month</label>
                                <select class="form-control" name="month" id="month">
                                    <option value="1" <?= 1 == $cost['month'] ? 'selected' : null ?>>Januari</option>
                                    <option value="2" <?= 2 == $cost['month'] ? 'selected' : null ?>>Februari</option>
                                    <option value="3" <?= 3 == $cost['month'] ? 'selected' : null ?>>Maret</option>
                                    <option value="4" <?= 4 == $cost['month'] ? 'selected' : null ?>>April</option>
                                    <option value="5" <?= 5 == $cost['month'] ? 'selected' : null ?>>Mei</option>
                                    <option value="6" <?= 6 == $cost['month'] ? 'selected' : null ?>>Juni</option>
                                    <option value="7" <?= 7 == $cost['month'] ? 'selected' : null ?>>July</option>
                                    <option value="8" <?= 8 == $cost['month'] ? 'selected' : null ?>>Agustus</option>
                                    <option value="9" <?= 9 == $cost['month'] ? 'selected' : null ?>>September</option>
                                    <option value="10" <?= 10 == $cost['month'] ? 'selected' : null ?>>Oktober</option>
                                    <option value="11" <?= 11 == $cost['month'] ? 'selected' : null ?>>November</option>
                                    <option value="12" <?= 12 == $cost['month'] ? 'selected' : null ?>>Desember</option>
                                </select>  
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Contractor</label>
                                <div class="col-sm-12">
                                    <select name="id_contractor" id="id_contractor" class="form-select" aria-label="Default select example">
                                        <?php foreach ($contractor as $contractor) : ?>
                                            <option value="<?= $contractor['id'] ?>"<?= $contractor['contractor_name'] == $cost['contractor_name'] ? 'selected' : null ?>><?= $contractor['contractor_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Cost Type</label>
                                <div class="col-sm-12">
                                    <select name="id_costtype" id="id_costtype" class="form-select" aria-label="Default select example">
                                        <?php foreach ($costtype as $costtype) : ?>
                                            <option value="<?= $costtype['id_costtype'] ?>"<?= $costtype['cost_type'] == $cost['cost_type'] ? 'selected' : null ?>><?= $costtype['cost_type']?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 position-relative mb-3">
                                <label for="cost" class="form-label">Cost</label>
                                <input type="text" class="form-control" id="cost" name="cost" value="<?= $cost['cost'] ?>" required>
                            </div>
                            <div class="col-sm-4">
                                <input name="id_costmining" type="text" class="form-control" value="<?= $cost['id_costmining'] ?>" hidden></input> 
                            </div>
                            <div class="mt-5 text-center">
                                <button id="saveButton" type="submit" class="btn btn-primary">Save</button>                               
                                <button id="cancelButton" type="button" class="btn btn-danger" onclick="getData()">Cancel</button>
                                <script>
                                    function getData(){
                                        window.location.href="<?= site_url() ?>"+`/sales/costmining`;
                                    }   
                                </script>               
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
        </div>
    </section>
</main><!-- End #main -->

<?= $this->endSection() ?>