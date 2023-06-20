<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
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
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Adjustments</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("operation/timesheet/") ?>">Operation </a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("operation/adjustment_wb") ?>">Adjustment Data</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">Adjustment Data</div>
                    <div class="card-body table-responsive">

                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <!-- end of notification -->

                        <!-- add new data button -->
                        <!-- <div class="row justify-content-between">
                            <div class="col-4">
                                <br>
                                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addnewdistance">
                                    <i class="bi bi-plus me-1"></i> Add New Adjust
                                </button>
                            </div>
                        </div> -->
                        <!-- end of add new data button -->

                        <!-- distance table -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Contractor</th>
                                    <th scope="col">Transaksi</th>
                                    <th scope="col">Bulan</th>
                                    <th scope="col">Tahun</th>
                                    <th scope="col">Value</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($getAdjust as $data) : ?>
                                    <tr>
                                        <td><?= $data['id'] ? $data['id'] : 'undefined' ?></td>


                                        <?php if ($data['id_contractor'] != NULL || $data['id_contractor'] != '') : ?>
                                            <?php foreach ($id_contractors as $contractor) : ?>
                                                <?php if ($contractor['id'] == $data['id_contractor']) : ?>
                                                    <td><?= $contractor['contractor_name'] ? $contractor['contractor_name'] : '' ?></td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <td>undefined</td>
                                        <?php endif; ?>


                                        <td><?= $data['transaksi'] ? $data['transaksi'] : 'undefined'  ?></td>
                                        <td><?= $months[$data['month']] ? $months[$data['month']] : ''  ?></td>
                                        <td><?= $data['year'] ? $data['year']  : '' ?></td>
                                        <td><?= number_format(($data['qty'] ?? 0) ?: 0, 2, ',', '.'); ?></td>
                                        <!-- <td><?= date("d-m-Y", strtotime($data['month'])) ?></td> -->
                                        <td class="row">
                                            <a href="<?= site_url('operation/adjustment_wb/edit/') ?><?= $data['id'] ?>" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('Are you sure?')" href="<?= site_url('operation/adjustment_wb/delete/') ?><?= $data['id'] ?>" class="btn btn-danger btn-sm col">
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
                            <form action="<?= site_url("operation/adjustment_wb/add") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Adjustments</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative">
                                                <label class="form-label">Contractor</label>
                                                <div class="col-sm-12">
                                                    <div class="col-sm-12">
                                                        <select name="contractor" id="contractor" class="form-select" aria-label="Default select example">
                                                            <option selected disabled>Please Select Contractors</option>
                                                            <?php foreach ($id_contractors as $contractor) : ?>
                                                                <option value="<?= $contractor['id'] ?>"><?= $contractor['contractor_name'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Transaksi</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <select name="transaksi" id="transaksi" class="form-select" aria-label="Default select example">
                                                            <option value="#" selected disabled>Select Transaksi</option>
                                                            <option value="Overburden">Overburden</option>
                                                            <option value="Coal Getting">Coal Getting</option>
                                                            <option value="CrushCoal">CrushCoal</option>
                                                            <option value="Distance OB">Distance OB</option>
                                                            <option value="Distance CG">Distance CG</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-12 mt-3">
                                                <label class="form-label">Bulan</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <input step="0.00000001" type="date" class="form-control" id="bulan" name="bulan" required>
                                                    </div>
                                                </div>
                                            </div> -->
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Bulan</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <select name="bulan" id="bulan" class="form-select" aria-label="Default select example">
                                                            <option selected disabled>Select Month</option>
                                                            <?php
                                                            foreach ($months as $monthNum => $monthName) {
                                                                echo "<option value='$monthNum'>$monthName</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Tahun</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <select name="tahun" id="tahun" class="form-select" aria-label="Default select example">
                                                            <option selected disabled>Select Year</option>
                                                            <?php
                                                            foreach ($years as $yearData) {
                                                                echo "<option value='$yearData'>$yearData</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Value</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="qty" name="qty">
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

</main><!-- End #main -->

<script>

</script>

<?= $this->endSection() ?>