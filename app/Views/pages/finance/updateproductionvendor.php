<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/updatedata") ?>">Update Data</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/updateproductionvendor") ?>">Update Production Vendor</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update Production Vendor</h5>
                    

                        <!-- // ! Add contractor modal -->

                        <div class="row">
                            <div class="col-md-4">
                                <i class="ri-filter-2-line"></i>
                                <select class="form-control text-xs">
                                    <option> <i class="ri-filter-2-line"></i> By Vendor Code</option>
                                    <option value="3000009">3000009</option>
                                    <option value="3000010">3000010</option>
                                </select>
                            </div>
                            <div class="col-md-4 offset-md-4"><br>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Add new data
                                </button>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="ri-upload-line"></i>
                                </button>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="ri-download-line"></i>
                                </button>
                            </div>
                        </div>
                        <br>

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="#" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Production Data</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="contractor_type">Vendor Code</label>
                                                <select class="form-control" name="contractor_type" id="contractor_type" required>
                                                    <option value="3000009">3000009</option>
                                                    <option value="3000010">3000010</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="contractor_type">Vendor Description</label>
                                                <select class="form-control" name="contractor_type" id="contractor_type" required>
                                                    <option value="GM">Garuda Mulia</option>
                                                    <option value="SE">Sayap Elang</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary" type="submit">Save Data</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- // ! edit contractor modal -->
                        <div class="modal fade" id="editForm" tabindex="-1">
                        <form action="#" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Production Data</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="contractor_type">Vendor Code</label>
                                                <select class="form-control" name="contractor_type" id="contractor_type" required>
                                                    <option value="3000009">3000009</option>
                                                    <option value="3000010">3000010</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="contractor_type">Vendor Description</label>
                                                <select class="form-control" name="contractor_type" id="contractor_type" required>
                                                    <option value="GM">Garuda Mulia</option>
                                                    <option value="SE">Sayap Elang</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary" type="submit">Save Data</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Table users -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Vendor Code</th>
                                    <th scope="col">Vendor Description</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Edited By</th>
                                    <th scope="col">Edited At</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach ($prd_lifnr as $row) : ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= $row['LIFNR'] ?></td>
                                        <td><?= $row['VNDRD'] ?></td>
                                        <td><?= $row['CRTDB'] ?></td>
                                        <td><?= $row['CRTDA'] ?></td>
                                        <td><?= $row['EDTBY'] ?></td>
                                        <td><?= $row['EDTAT'] ?></td>
                                        <td class="col">
                                            <a onclick="editForm(<?= $row['id'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('are you sure')" href="<?= site_url("/finance/updateproductiondata/delete/").$row['id'] ?>" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                                <!-- <span class="tipstextL">Delete</span> -->
                                            </a>
                                        </td> 
                                    </tr>
                                <?php endforeach ?>
                                <!-- <tr>
                                    <td>001</td>
                                    <td>3000009</td>
                                    <td>Garuda Mulia</td>
                                    <td>LATHS</td>
                                    <td>18/05/1999</td>
                                    <td>SANI</td>
                                    <td>18/05/1998</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered"> 
                                            <i class="ri-edit-2-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger">           
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>                           
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

                <!-- </div>
            </div>
        </div>
    </section> -->

</main>

<?= $this->endSection() ?>