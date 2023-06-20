<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/updatedata") ?>">Update Data</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/pph22") ?>">Bukti Potong PPh22</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bukti Potong PPh22</h5>
                    
 
                        <!-- // ! Add contractor modal -->
                        <!-- <div class="row">
                            <div class="col-md-4">
                                <i class="ri-filter-2-line"></i>
                                <select class="form-control text-xs">
                                    <option> <i class="ri-filter-2-line"></i>Filter</option>
                                </select>
                            </div>
                            <div class="col-md-4"><br>
                                <button type="button" class="btn btn-outline-dark"  data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="ri-download-line"></i> Download
                                </button>
                            </div>
                        </div> -->
                        <div class="d-flex justify-content-between mb-4">
                            
                            <div class="d-flex left-section col-6">
                                <span class="mx-2 my-auto col-2">Date From</span>
                                <input type="date" class="form-control" id="tgl1" name="tgl1" value="<?php $d1 = $_GET['date1'] ?? false; if($d1 == ""){ echo $tanggal1; } else { echo $_GET['date1']; } ?>">
                                <span class="mx-2 my-auto"> to </span>
                                <input type="date" class="form-control" id="tgl2" name="tgl2" value="<?php $d2 = $_GET['date2'] ?? false; if($d2 == ""){ echo $tanggal2; } else { echo $_GET['date2']; } ?>">
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">
                                    Filter
                                </button>
                                <script>
                                    function getData(){
                                        let tgl1= document.getElementById('tgl1').value
                                        let tgl2= document.getElementById('tgl2').value
                                        window.location.href="<?= site_url() ?>"+`/finance/pph22?date1=${tgl1}&date2=${tgl2}`;
                                    }
                                </script>
                            </div>  
                            <!-- <div class="right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Tambah Data
                                </button>
                            </div> -->
                        </div>

                        <div class="modal fade" id="editForm" tabindex="-1">
                        <form action="<?= site_url("finance/pph22/update") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Bukti Potong PPH</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <input type="hidden" id="id" name="id">
                                            <input type="hidden" id="id_bkt_ptg" name="id_bkt_ptg">
                                            <input type="hidden" id="edbukrs" name="edbukrs">
                                            <input type="hidden" id="edkunnr" name="edkunnr">
                                            <input type="hidden" id="edtgl1" name="edtgl1" value="<?= $_GET['date1'] ?? false; ?>">
                                            <input type="hidden" id="edtgl2" name="edtgl2" value="<?= $_GET['date2'] ?? false; ?>">
                                            <div class="d-flex left-section mb-3">
                                                <label for="edcostumer" class="form-label col-2">Customer</label>
                                                <input type="text" class="form-control" id="edcostumer" name="edcostumer" readonly>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="eddocno" class="form-label col-3">Document Number</label>
                                                <input class="form-control" id="eddocno" name="eddocno" readonly>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edyear" class="form-label col-2">Year</label>
                                                <input class="form-control" id="edyear" name="edyear" readonly>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edglacct" class="form-label col-2">GL Account</label>
                                                <input class="form-control" id="edglacct" name="edglacct" readonly>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edtext" class="form-label col-2">Text</label>
                                                <input class="form-control" id="edtext" name="edtext" readonly>
                                            </div>
                                            <!-- <div class="d-flex left-section mb-3">
                                                <label for="edbkt" class="form-label col-2">Bukti Potong</label>
                                                <select class="form-control" name="edbkt" id="edbkt" required>
                                                    <option value="1">Ya</option>
                                                    <option value="0">Tidak</option>
                                                </select>
                                            </div> -->
                                            <div class="d-flex left-section mb-3">
                                                <label for="ednobkt" class="form-label col-3">Nomor Bukti Potong</label>
                                                <input type="text" class="form-control" id="ednobkt" name="ednobkt" required>
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
                                    <th scope="col">No</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Document Number</th>
                                    <th scope="col">Year</th>
                                    <th scope="col">GL Account</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Text</th>
                                    <th scope="col">Bukti Potong</th>
                                    <th scope="col">Nomor Bukti Potong</th>
                                    <th scope="col">Last Edited By</th>
                                    
                                    <th scope="col">Last Edited At</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $i = 1;
                                foreach ($bkt_ptg as $row) : ?>
                                    <tr>
                                        <td><?= $i++ ?>.</td>
                                        <td><?= $row['cst_nm'] ?></td>
                                        <td><?= $row['BELNR'] ?></td>
                                        <td><?= $row['GJAHR'] ?></td>
                                        <td><?= $row['HKONT'] ?></td>
                                        <td>Rp. <?= number_format($row['DMBTR'],0,',','.') ?></td>
                                        <td><?= $row['BKTXT'] ?></td>
                                        <td>
                                            <?php if($row['BKP'] == '1') {?>
                                                <input class="form-check-input" type="checkbox" id="flexCheckChecked" checked disabled>
                                            <?php }else{?>
                                                <input class="form-check-input" type="checkbox" id="flexCheckChecked" disabled>
                                            <?php }?>
                                        </td>
                                        <td><?= $row['NBKP'] ?></td>
                                        <td><?= $row['EDBY'] ?></td>
                                        <td><?= $row['EDON'].' '.$row['EDAT'] ?></td>
                                        <td class="col">
                                            <a onclick="editForm(<?= $row['id_doc'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if( $row['id_bkt_ptg'] != 0){?>
                                            <a onclick=" return confirm('are you sure')" href="<?= site_url("/finance/pph22/delete/").$row['id_bkt_ptg'].'?date1='.($_GET['date1'] ?? false).'&date2='.($_GET['date2'] ?? false) ?>" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                                <!-- <span class="tipstextL">Delete</span> -->
                                            </a>
                                            <?php }?>
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
        fetch("<?= site_url('/finance/pph22/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#edcostumer').val(res.cst_nm);
                $('#eddocno').val(res.BELNR);
                $('#edyear').val(res.GJAHR);
                $('#edglacct').val(res.HKONT);
                $('#edtext').val(res.BKTXT);
                $('#edbkt').val(res.BKP);
                $('#ednobkt').val(res.NBKP);
                $('#id').val(id);
                $('#id_bkt_ptg').val(res.id_bkt_ptg);
                $('#edbukrs').val(res.BUKRS);
                $('#edkunnr').val(res.KUNNR);
            })
            .then(() => {
                $('#editForm').modal('show');
            });
    }
    

</script>


<?= $this->endSection() ?>