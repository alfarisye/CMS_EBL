<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Monthly Budget</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Master Data</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("master-data/monthlybudget") ?>">Monthly Budget</a></li>
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
                        <h5 class="card-title">Monthly Budget</h5>
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
                            <form action="<?= site_url("/master-data/monthlybudget/add") ?>" method="POST" novalidate enctype="multipart/form-data" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Monthly Budget </h5>
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
                                                    <input name="years" id="years" type="number" value="<?= date("Y") ?>" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Month</label>
                                                <div class="col-sm-12">
                                                    <input name="month" id="month" class="form-control" type="number" min="0" max="12" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Contracor</label>
                                                <div class="col-sm-12">
                                                    <select class="form-select" name="contractor" id="contractor" ">
                                                        <option value="">pilih Contractor </option>
                                                        <?php foreach ($contractor as $row) : ?>
                                                            <option value="<?= $row['id'] ?>"><?= $row['contractor_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Annual Budget</label>
                                                <div class="col-sm-12">
                                                    <select class="form-select" name="id_ann_bdgt" id="id_ann_bdgt" ">
                                                        <option value="">pilih Annual Budget </option>
                                                        <?php foreach ($vannbdgt as $row) : ?>
                                                            <option value="<?= $row['id_annualbudget'] ?>"><?= $row['desc_annualbdgt'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <table length="100%" >
                                                    <tr>
                                                        <td length="50%">
                                                        <div class="col-md-12 mt-3">
                                                            <label class="form-label">CG Monthly Budget Qty</label>
                                                            <div class="col-sm-12">
                                                                <input name="cg_mbudget" id="cg_mbudget" class="form-control" aria-label="" required></input>
                                                            </div>
                                                            <label class="form-label">MT</label>
                                                        </div>
                                                        </td>
                                                        <td length="50%">
                                                        <div class="col-md-12 mt-3">
                                                            <label class="form-label">CG Daily Budget Qty</label>
                                                            <div class="col-sm-12">
                                                                <input name="cg_dbudget" id="cg_dbudget" class="form-control" aria-label="" required></input>
                                                            </div>
                                                            <label class="form-label">MT</label>
                                                        </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td length="50%">
                                                        <div class="col-md-12 mt-3">
                                                            <label class="form-label">OB Monhtly Budget Qty</label>
                                                            <div class="col-sm-12">
                                                                <input name="ob_mbudget" id="ob_mbudget" class="form-control" aria-label="" required></input>
                                                            </div>
                                                            <label class="form-label">MT</label>
                                                        </div>
                                                        </td>
                                                        <td length="50%">
                                                        <div class="col-md-12 mt-3">
                                                            <label class="form-label">OB Daily Budget Qty</label>
                                                            <div class="col-sm-12">
                                                                <input name="ob_dbudget" id="ob_dbudget" class="form-control" aria-label="" required></input>
                                                            </div>
                                                            <label class="form-label">MT</label>
                                                        </div>
                                                        </td>
                                                    </tr>
                                                    <tr></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Revision</label>
                                                <div class="col-sm-12">
                                                    <input name="revision" id="revision" type="number" class="form-control" aria-label="" required></input>
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
                            <form action="<?= site_url("/master-data/monthlybudget/update") ?>" method="POST" class="needs-validation" novalidate>
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Monthly Budget</h5>
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
                                                    <input name="edyears" id="vyears" type="number" value="<?= date("Y") ?>" class="form-control" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Month</label>
                                                <div class="col-sm-12">
                                                    <input name="edmonth" id="edmonth" class="form-control" type="number" min="0" max="12" aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Contracor</label>
                                                <div class="col-sm-12">
                                                    <select class="form-select" name="edcontractor" id="edcontractor" ">
                                                        <option value="">pilih Contractor </option>
                                                        <?php foreach ($contractor as $row) : ?>
                                                            <option value="<?= $row['id'] ?>"><?= $row['contractor_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Annual Budget</label>
                                                <div class="col-sm-12">
                                                    <select class="form-select" name="edid_ann_bdgt" id="edid_ann_bdgt" ">
                                                        <option value="">pilih Annual Budget </option>
                                                        <?php foreach ($vannbdgt as $row) : ?>
                                                            <option value="<?= $row['id_annualbudget'] ?>"><?= $row['desc_annualbdgt'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <table length="100%" >
                                                    <tr>
                                                        <td length="50%">
                                                        <div class="col-md-12 mt-3">
                                                            <label class="form-label">CG Monthly Budget Qty</label>
                                                            <div class="col-sm-12">
                                                                <input name="edcg_mbudget" id="edcg_mbudget" class="form-control" aria-label="" required></input>
                                                            </div>
                                                            <label class="form-label">MT</label>
                                                        </div>
                                                        </td>
                                                        <td length="50%">
                                                        <div class="col-md-12 mt-3">
                                                            <label class="form-label">CG Daily Budget Qty</label>
                                                            <div class="col-sm-12">
                                                                <input name="edcg_dbudget" id="edcg_dbudget" class="form-control" aria-label="" required></input>
                                                            </div>
                                                            <label class="form-label">MT</label>
                                                        </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td length="50%">
                                                        <div class="col-md-12 mt-3">
                                                            <label class="form-label">OB Monhtly Budget Qty</label>
                                                            <div class="col-sm-12">
                                                                <input name="edob_mbudget" id="edob_mbudget" class="form-control" aria-label="" required></input>
                                                            </div>
                                                            <label class="form-label">MT</label>
                                                        </div>
                                                        </td>
                                                        <td length="50%">
                                                        <div class="col-md-12 mt-3">
                                                            <label class="form-label">OB Daily Budget Qty</label>
                                                            <div class="col-sm-12">
                                                                <input name="edob_dbudget" id="edob_dbudget" class="form-control" aria-label="" required></input>
                                                            </div>
                                                            <label class="form-label">MT</label>
                                                        </div>
                                                        </td>
                                                    </tr>
                                                    <tr></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Revision</label>
                                                <div class="col-sm-12">
                                                    <input name="edrevision" id="edrevision" type="number" class="form-control" aria-label="" required></input>
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
                                        <th scope="col">Month</th>
                                        <th scope="col">Contractor</th>
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
                                        foreach ($MonthlyBudgets as $row) :   ?>
                                        <tr>
                                            <td><?= $no++ ?>.</td>
                                            <td><?= $row['id_monthlybudget'] ?></td>
                                            <td><?= $row['project'] ?></td>
                                            <td><?= $row['year'] ?></td>
                                            <td><?= $row['month'] ?></td>
                                            <td><?= $row['nm_contractor'] ?></td>
                                            <td><?= $row['cg_monthlybudget_qt'] ?></td>
                                            <td><?= $row['ob_monthlybudget_qt'] ?></td>
                                            <td><?= $row['revision'] ?></td>
                                            <td><?= $row['status'] ?></td>
                                            <td class="col">
                                                <a onclick="editForm(<?= $row['id_monthlybudget'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
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
    // fields :
    // 'id_contractor','year','month','project','cg_monthlybudget_qt',
    // 'ob_monthlybudget_qt','cg_dailybudget_qt','ob_dailybudget_qt',
    // 'create_date','last_update','revision','status','id_annualbudget'

    function editForm(id) {
        fetch("<?= site_url('/master-data/monthlybudget/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#edyears').val(res.year);
                $('#edproject').val(res.project);
                $('#edmonth').val(res.month);
                $('#edcontractor').val(res.id_contractor);
                $('#edcg_mbudget').val(res.cg_monthlybudget_qt);
                $('#edob_mbudget').val(res.ob_monthlybudget_qt);
                $('#edcg_dbudget').val(res.cg_dailybudget_qt);
                $('#edob_dbudget').val(res.ob_dailybudget_qt);
                $('#edrevision').val(res.revision);
                $('#edstatus').val(res.status);
                $('#edid_ann_bdgt').val(res.id_annualbudget);
                $('#id').val(id);
            })
            .then(() => {
                $('#editForm').modal('show');
            });
    }
</script>

<?= $this->endSection() ?>