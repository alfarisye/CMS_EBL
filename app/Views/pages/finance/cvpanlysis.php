<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/updatedata") ?>">Update Data</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/cvpanlysis") ?>">Additional for CVP Analysis Report</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Additional for CVP Analysis Report</h5>
                    

                        <!-- // ! Add contractor modal -->

                        <!-- <div class="row">
                            <div class="col-md-4">
                                <i class="ri-filter-2-line"></i>
                                <select class="form-control text-xs">
                                    <option> <i class="ri-filter-2-line"></i> By Category</option>
                                    <option value="A">Category A</option>
                                    <option value="B">Category B</option>
                                    <option value="C">Category C</option>
                                    <option value="D">Category D</option>
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
                        </div> -->
                        <div class="d-flex justify-content-between mb-4">
                            
                            <div class="d-flex left-section col-5">
                                <span class="mx-2 my-auto">Category</span>
                                <select class="form-control text-xs" name="category" id="category">
                                    <option <?php if(($_GET['idcat']?? false) == ''){echo("selected");}?> value=""> -- All Category --</option>
                                    
                                    <?php foreach ($qcat as $row): ?>
                                        <option <?php if(($_GET['idcat']?? false) == $row['id']){echo("selected");}?> value="<?= $row['id'] ?>"><?= $row['category'] ?></option>
                                    <?php endforeach ?>
                                    
                                </select>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">
                                    Filter
                                </button>
                                <script>
                                    function getData(){
                                        let id_cat= document.getElementById('category').value
                                        window.location.href="<?= site_url() ?>"+`/finance/cvpanlysis?idcat=${id_cat}`;
                                    }
                                </script>
                            </div>  
                            <div class="right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <br>

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("finance/cvpanlysis/add") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Additional for CVP Analysis Report</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="d-flex left-section mb-3">
                                                <label for="category" class="form-label col-2">Category</label>
                                                <select class="form-control" name="category" id="category" required>
                                                <option value=""> -- pilih category --</option>
                                                <!-- <option value="1"> C (B + Profit) </option>
                                                <option value="2"> D (C & Qty)</option> -->
                                                <?php foreach ($qcat as $row): ?>
                                                    <option value="<?= $row['id'] ?>"><?= $row['category'] ?></option>
                                                <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="remark" class="form-label col-2">Remarks</label>
                                                <select class="form-control" name="remark" id="remark" required>
                                                    <option value="">-- pilih remark --</option>
                                                    <!-- <option value="FC">FC</option>
                                                    <option value="BE">BE Qty</option> -->
                                                    <?php foreach ($qremark as $row): ?>
                                                        <option value="<?= $row['value'] ?>"><?= $row['remark'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="year" class="form-label col-2">Year</label>
                                                <input type="number" class="form-control" id="year" name="year" required>
                                                
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="period" class="form-label col-2">Period</label>
                                                <input type="number" class="form-control" id="period" name="period" required>
                                                
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="amount" class="form-label col-2">Amount</label>
                                                <input class="form-control" id="amount" name="amount" required>
                                                
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="comments" class="form-label">Comments</label>
                                                <input type="text" class="form-control" id="comments" name="comments" required>
                                                
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

                        <div class="modal fade" id="editForm" tabindex="-1">
                            <form action="<?= site_url("finance/cvpanlysis/update") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Additional for CVP Analysis Report</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <input type="hidden" id="id" name="id">
                                            <div class="d-flex left-section mb-3">
                                                <label for="edcategory" class="form-label col-2">Category</label>
                                                <select class="form-control" name="edcategory" id="edcategory" required>
                                                <option value=""> -- pilih category --</option>
                                                <!-- <option value="1"> C (B + Profit) </option>
                                                <option value="2"> D (C & Qty)</option> -->
                                                <?php foreach ($qcat as $row): ?>
                                                    <option value="<?= $row['id'] ?>"><?= $row['category'] ?></option>
                                                <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edremark" class="form-label col-2">Remarks</label>
                                                <select class="form-control" name="edremark" id="edremark" required>
                                                    <option value="">-- pilih remark --</option>
                                                    <!-- <option value="FC">FC</option>
                                                    <option value="VC">VC</option> -->
                                                    <?php foreach ($qremark as $row): ?>
                                                        <option value="<?= $row['value'] ?>"><?= $row['remark'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edyear" class="form-label col-2">Year</label>
                                                <input type="number" class="form-control" id="edyear" name="edyear" required>
                                                
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edperiod" class="form-label col-2">Period</label>
                                                <input type="number" class="form-control" id="edperiod" name="edperiod" required>
                                                
                                            </div>    
                                            <div class="d-flex left-section mb-3">
                                                <label for="edamount" class="form-label col-2">Amount</label>
                                                <input class="form-control" id="edamount" name="edamount" required>
                                                
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="edcomments" class="form-label">Comments</label>
                                                <input type="text" class="form-control" id="edcomments" name="edcomments" required>
                                                
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
                        <form action="<?= site_url("finance/cvpanlysis/update") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Additional for CVP Analysis Report</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="edcategory">Category</label>
                                                <select class="form-control" name="edcategory" id="edcategory" required>
                                                    <option value=""> -- pilih category --</option>
                                                    <option value="1"> C (B + Profit) </option>
                                                    <option value="2"> D (C & Qty)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="edremark">Remarks</label>
                                                <select class="form-control" name="edremark" id="edremark" required>
                                                    <option value="">-- pilih remark --</option>
                                                    <option value="FC">FC</option>
                                                    <option value="VC">VC</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="edyear" class="form-label">Year</label>
                                                <input type="text" class="form-control" id="edyear" name="edyear" required>
                                                
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="edamount" class="form-label">Amount</label>
                                                <input type="text" class="form-control" id="edamount" name="edamount" required>
                                                
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="edcomments" class="form-label">Comments</label>
                                                <input type="text" class="form-control" id="edcomments" name="edcomments" required>
                                                
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
                                    <th scope="col">Category</th>
                                    <th scope="col">Remark</th>
                                    <th scope="col">Year</th>
                                    <th scope="col">Period</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Comments</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Edited By</th>
                                    <th scope="col">Edited At</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                foreach ($add_cvp as $row) : ?>
                                    <tr>
                                        <td><?= $row['CTGRY'] ?></td>
                                        <td><?= $row['RKMRK'] ?></td>
                                        <td><?= $row['GJAHR'] ?></td>
                                        <td><?= $row['MONAT'] ?></td>
                                        <td><?= $row['DMBTR'] ?></td>
                                        <td><?= $row['COMMT'] ?></td>
                                        <td><?= $row['CRTDB'] ?></td>
                                        <td><?= $row['CRTDA'] ?></td>
                                        <td><?= $row['EDTBY'] ?></td>
                                        <td><?= $row['EDTAT'] ?></td>
                                        <td class="col">
                                            <a onclick="editForm(<?= $row['id'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('are you sure')" href="<?= site_url("/finance/cvpanlysis/delete/").$row['id'] ?>" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                                <!-- <span class="tipstextL">Delete</span> -->
                                            </a>
                                        </td> 
                                    </tr>
                                <?php endforeach ?>
                               
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
<script>
    // RKMRK, GJAHR, DMBTR, COMMT
    function editForm(id) {
        fetch("<?= site_url('/finance/cvpanlysis/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#edcategory').val(res.id_cat);
                $('#edremark').val(res.RKMRK);
                $('#edyear').val(res.GJAHR);
                $('#edperiod').val(res.MONAT);
                $('#edamount').val(res.DMBTR);
                $('#edcomments').val(res.COMMT);
                $('#id').val(id);
            })
            .then(() => {
                $('#editForm').modal('show');
            });
    }
</script>

<?= $this->endSection() ?>