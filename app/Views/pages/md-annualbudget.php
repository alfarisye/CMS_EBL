<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Annual Budget</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Master Data</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("master-data/annualbudget") ?>">Annual Budget</a></li>
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
                        <h5 class="card-title">Annual Budget</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        
                        <div class="d-flex right-section">
                            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                <i class="bi bi-plus me-1"></i> Tambah Data
                            </button>
                        </div>
                            
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("/master-data/annualbudget/add") ?>" method="POST" novalidate enctype="multipart/form-data" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Annual Budget </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="col-md-12 mt-3">
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Project</label>
                                                <div class="col-sm-12">
                                                    <input name="project" id="project" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Year</label>
                                                <div class="col-sm-12">
                                                    <input name="years" id="years" type="number" class="form-control" value="<?= date("Y") ?>" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">CG Annual Budget Qty</label>
                                                <div class="col-sm-12">
                                                    <input name="cg_budget" id="cg_budget" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">OB Annual Budget Qty</label>
                                                <div class="col-sm-12">
                                                    <input name="ob_budget" id="ob_budget" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Revision</label>
                                                <div class="col-sm-12">
                                                    <input name="revision" id="revision" value="0" type="number" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status" id="status">
                                                    <option value="active">active</option>
                                                    <option value="non active">non active</option>
                                                </select>
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

                        <div class="modal fade" id="editForm" tabindex="-1">
                            <form action="<?= site_url("/master-data/annualbudget/update") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Annual Budget</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- id User -->
                                            <input type="hidden" id="id" name="id">
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Project</label>
                                                <div class="col-sm-12">
                                                    <input name="edproject" id="edproject" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Year</label>
                                                <div class="col-sm-12">
                                                    <input name="edyears" id="edyears" type="number" class="form-control" value="<?= date("Y") ?>" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">CG Annual Budget Qty</label>
                                                <div class="col-sm-12">
                                                    <input name="edcg_budget" id="edcg_budget" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">OB Annual Budget Qty</label>
                                                <div class="col-sm-12">
                                                    <input name="edob_budget" id="edob_budget" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Revision</label>
                                                <div class="col-sm-12">
                                                    <input name="edrevision" id="edrevision" value="0" type="number" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="edstatus" id="edstatus">
                                                    <option value="active">active</option>
                                                    <option value="non active">non active</option>
                                                </select>
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

                        <div class="table-responsive">
                            <!-- Table users -->
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Id</th>
                                        <th scope="col">Project</th>
                                        <th scope="col">Year</th>
                                        <th scope="col">CG Budget Qty</th>
                                        <th scope="col">OB Budget Qty</th>
                                        <th scope="col">Revision</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    foreach ($AnnualBudgets as $row) : ?>
                                        <tr>
                                            <td><?= $no++ ?>.</td>
                                            <td><?= $row['id_annualbudget'] ?></td>
                                            <td><?= $row['project'] ?></td>
                                            <td><?= $row['year'] ?></td>
                                            <td><?= $row['cg_annualbudget_qt'] ?></td>
                                            <td><?= $row['ob_annualbudget_qt'] ?></td>
                                            <td><?= $row['revision'] ?></td>
                                            <td><?= $row['status'] ?></td>
                                            <td class="col">
                                                <a onclick="editForm(<?= $row['id_annualbudget'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <!-- <a href="<?= site_url('/annualbudget/edit') . urlencode($row['id_annualbudget']) ?>" class="btn btn-primary btn-sm col">
                                                    <i class="bi bi-pencil"></i>
                                                </a> -->
                                                <!-- <a onclick="return confirm('Are you sure?')" href="<?= site_url('tambah-parameter/delete/') ?><?= $row['id_annualbudget'] ?>" class="btn btn-danger btn-sm col">
                                                    <i class="bi bi-trash"></i>
                                                </a> -->
                                            </td> 
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</main><!-- End #main -->

<script>
    function editForm(id) {
        fetch("<?= site_url('/master-data/annualbudget/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#edyears').val(res.year);
                $('#edproject').val(res.project);
                $('#edcg_budget').val(res.cg_annualbudget_qt);
                $('#edob_budget').val(res.ob_annualbudget_qt);
                $('#edrevision').val(res.revision);
                $('#edstatus').val(res.status);
                $('#id').val(id);
            })
            .then(() => {
                $('#editForm').modal('show');
            });
    }
</script>

<?= $this->endSection() ?>