<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/updatedata") ?>">Update Data</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/updateproductiondata") ?>">Update Production Data</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update Production Data</h5>
                    

                        <!-- // ! Add contractor modal -->

                        <!-- <div class="row"> -->
                            <!-- <div class="col-md-4"> -->
                                <!-- Filter by Activity -->
                            <!-- </div> -->
                            <!-- <div class="col-md-4"> -->
                                <!-- <i class="ri-filter-2-line"></i> -->
                                <!-- <div class="d-flex justify-content-between mb-4">
                                    <span class="mx-2 my-auto">Activity</span>
                                    <select class="form-control text-xs" name="idactivity" id="idactivity">
                                        <option value=""> -- Filter By Activity --</option>
                                        <?php foreach ($t_actitvy as $actv) : ?>
                                            <option value="<?= $actv['id'] ?>"><?= $actv['ACTVY'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <script>
                                        function filterFunction(){
                                            let val = document.getElementById('idactivity').value;
                                            if (val != '') {
                                                window.open("<?= site_url("/finance/updateproductiondata/getActivity/")?>"+val);  
                                            }else{
                                                window.open("<?= site_url("/finance/updateproductiondata/")?>");  
                                            }
                                        }
                                    </script>

                                    <a type="button" class="ms-2 my-2 btn btn-primary" onclick="filterFunction()" id="filter" name="filter" >Filter</a>
                                </div>
                            </div>
                            <div class="row">&nbsp;</div>
                            <div class="d-flex right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Tambah Data
                                </button> -->
                                <!-- <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="ri-upload-line"></i>
                                </button>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="ri-download-line"></i>
                                </button> -->
                            <!-- </div>
                        </div>
                        <br> -->
                        
                        <div class="d-flex justify-content-between mb-4">
                            
                            <div class="d-flex left-section col-5">
                                <span class="mx-2 my-auto">Activity</span>
                                <select class="form-control text-xs" name="idactivity" id="idactivity">
                                    <option value=""> -- All Activity --</option>
                                    <?php foreach ($t_actitvy as $actv) : ?>
                                        <option value="<?= $actv['id'] ?>"><?= $actv['ACTVY'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">
                                    Filter
                                </button>
                                <script>
                                    function getData(){
                                        let id_activity= document.getElementById('idactivity').value
                                        window.location.href="<?= site_url() ?>"+`/finance/updateproductiondata?idactv=${id_activity}`;
                                    }
                                </script>
                            </div>  
                            <div class="right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Tambah Data
                                </button>
                            </div>
                        </div>


                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("finance/updateproductiondata/add") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Production Data</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="activity">Activity</label>
                                                <select class="form-control" name="activity" id="activity" required>
                                                    <option value="">-- Pilih Activity --</option>
                                                    <?php foreach ($t_actitvy as $row) : ?>
                                                        <option value="<?= $row['id'] ?>"><?= $row['ACTVY'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="vendor">Vendor</label>
                                                <select class="form-control" name="vendor" id="vendor" required>
                                                    <option value="">-- Pilih Vendor --</option>
                                                    <?php foreach ($vendor as $row) : ?>
                                                        <option value="<?= $row['LIFNR'] ?>"><?= $row['NAME1'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="quantity" class="form-label">Quantity</label>
                                                <input type="number" class="form-control" id="quantity" name="quantity" required>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="tarif" class="form-label">Tarif</label>
                                                <input type="number" class="form-control" id="tarif" name="tarif" required>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
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
                        <form action="<?= site_url("finance/updateproductiondata/update") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Production Data</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <input type="hidden" id="id" name="id">
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="edactivity">Activity</label>
                                                <select class="form-control" name="edactivity" id="edactivity" required>
                                                    <option value="">Pilih Activity</option>
                                                    <?php foreach ($t_actitvy as $row) : ?>
                                                        <option value="<?= $row['id'] ?>"><?= $row['ACTVY'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="vendor">Vendor</label>
                                                <select class="form-control" name="edvendor" id="edvendor" required>
                                                    <option value="">Pilih Vendor</option>
                                                    <?php foreach ($vendor as $row) : ?>
                                                        <option value="<?= $row['LIFNR'] ?>"><?= $row['NAME1'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="edquantity" class="form-label">Quantity</label>
                                                <input type="number" class="form-control" id="edquantity" name="edquantity" required>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="tarif" class="form-label">Tarif</label>
                                                <input type="number" class="form-control" id="edtarif" name="edtarif" required>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
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
                                    <th scope="col">Activity User</th>
                                    <th scope="col">Vendor User</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Tarif</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Edited By</th>
                                    <th scope="col">Edited At</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                foreach ($PrdDat as $row) : ?>
                                    <tr>
                                        <td><?= $row['act_desc'] ?></td>
                                        <td><?= $row['vendor_name'] ?></td>
                                        <td><?= $row['QTY'] ?></td>
                                        <td><?= $row['TRF'] ?></td>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

 

</main>
<script>
    // fields :
    // ACTVY, CRTDA, CRTDB, EDTAT, EDTBY, LIFNR, QTY, TRF, id,

    function editForm(id) {
        fetch("<?= site_url('/finance/updateproductiondata/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#edactivity').val(res.ACTVY);
                $('#edvendor').val(res.LIFNR);
                $('#edquantity').val(res.QTY);
                $('#edtarif').val(res.TRF);
                $('#id').val(id);
            })
            .then(() => {
                $('#editForm').modal('show');
            });
    }
    

</script>

<?= $this->endSection() ?>