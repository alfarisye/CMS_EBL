<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">
    <?php
    $months = array(
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    );
    $years = array(
        2020,
        2021,
        2022,
        2023
    );
    ?>

    <!-- page title -->
    <div class="pagetitle">
        <h1>Edit Adjustments</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("operation/timesheet/") ?>">Operation </a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("operation/adjustment_wb") ?>">Adjustment Data</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <!-- end page title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">Edit Adjustments</div>
                    <div class="card-body table-responsive">

                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end of notification -->

                        <!-- distance modal -->
                        <form action="<?= site_url("operation/adjustment_wb/update") ?>" method="POST" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <div class="modal-body">
                                <!-- Custom Styled Validation with Tooltips -->

                                <div class="col-md-12 mt-3">
                                    <label for="remarks" class="form-label">Contractor</label>
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <select name="contractor" id="contractor" class="form-select" aria-label="Default select example" disabled>
                                                <option selected>Please Select Contractors</option>
                                                <?php foreach ($id_contractors as $contractor) : ?>
                                                    <option value="<?= $contractor['id'] ?>" <?= $contractor['id'] == $dataku['id_contractor'] ? 'selected' : null ?>><?= $contractor['contractor_name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="remarks" class="form-label">Transaksi</label>
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <select name="transaksi" id="transaksi" class="form-select" aria-label="Default select example" disabled>
                                                <option value="#" disabled>Select Transaksi</option>
                                                <option value="Overburden" <?= $dataku['transaksi'] == 'Overburden' ? 'selected' : '' ?>>Overburden</option>
                                                <option value="Coal Getting" <?= $dataku['transaksi'] == 'Coal Getting' ? 'selected' : '' ?>>Coal Getting</option>
                                                <option value="CrushCoal" <?= $dataku['transaksi'] == 'CrushCoal' ? 'selected' : '' ?>>CrushCoal</option>
                                                <option value="Hauling to Port" <?= $dataku['transaksi'] == 'Hauling to Port' ? 'selected' : '' ?>>Hauling to Port</option>
                                                <option value="Distance OB" <?= $dataku['transaksi'] == 'Distance OB' ? 'selected' : '' ?>>Distance OB</option>
                                                <option value="Distance CG" <?= $dataku['transaksi'] == 'Distance CG' ? 'selected' : '' ?>>Distance CG</option>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="remarks" class="form-label">Bulan</label>
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <select name="bulan" id="bulan" class="form-select" aria-label="Default select example" disabled>
                                                <?php
                                                foreach ($months as $monthNum => $monthName) {
                                                    $selected = ($monthNum == $dataku['month']) ? 'selected' : '';
                                                    echo "<option value='$monthNum' $selected>$monthName</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="remarks" class="form-label">Tahun</label>
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <select name="tahun" id="tahun" class="form-select" aria-label="Default select example" disabled>
                                                <?php
                                                foreach ($years as $yearData) {
                                                    $selected = ($yearData == $dataku['year']) ? 'selected' : '';
                                                    echo "<option value='$yearData' $selected>$yearData</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="remarks" class="form-label">Value</label>
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <input value="<?= $dataku['qty'] ?>" type="text" class="form-control" id="qty" name="qty">
                                            <div class="input-group-append">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <input style="display:none" value="<?= $dataku['id'] ?>" type="text" class="form-control" id="id" name="id">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="<?= site_url("operation/adjustment_wb") ?> " class="btn btn-danger">Cancel</a>
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
<?= $this->endSection() ?>