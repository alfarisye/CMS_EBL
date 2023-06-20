<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">
 
    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/updatedata") ?>">Update Data</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/budgetfinance") ?>">Input Budget Finance</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Input Budget Finance</h5>
                        <div class="d-flex justify-content-between mb-4">
                            
                            <div class="d-flex left-section col-5">
                                <span class="mx-2 my-auto">GL Account</span>
                                <select class="form-control text-xs" name="idglaccount" id="idglaccount">
                                    <option <?php if(($_GET['idglaccount'] ?? false) == ''){echo("selected");}?> value=""> -- All GL --</option>
                                <?php foreach ($filterGLAccount as $row) : ?>
                                    <option <?php if(($_GET['idglaccount'] ?? false) == $row['SAKNR']){echo("selected");}?> value="<?php echo  $row['SAKNR'];?>"><?php echo $row['SAKNR'];echo ' '; echo $row['TXT50'];?></option>                                  
                                <?php endforeach ?>
                                </select>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">
                                    Filter
                                </button>
                                <script>
                                    function getData(){
                                        let id_cat= document.getElementById('idglaccount').value
                                        window.location.href="<?= site_url() ?>"+`/finance/budgetfinance?idglaccount=${id_cat}`;
                                    }
                                </script>
                            </div>  
                            <div class="right-section">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> Tambah Data
                                </button>
                                <a href="<?= site_url("/finance/budgetfinance/upload/") ?>" class="btn btn-primary mb-2">
                                    <span class="bi bi-plus me-1">Upload Data</span>
                                </a>
                            </div>
                        </div>
                        <br>

                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("finance/budgetfinance/add") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Add New Data Budget Finance</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="d-flex left-section mb-3">
                                                <label for="glaccount" class="form-label col-2">GL Account</label>
                                                <select class="form-control" name="glaccount" id="glaccount" required>
                                                    <option value="">--Pilih GL Account--</option>
                                                <?php foreach ($filterGLAccount as $row) : ?>
                                                    <option <?php if(($_GET['idglaccount'] ?? false) == $row['TXT50']){echo("selected");}?> value="<?php echo  $row['SAKNR'];?>"><?php echo $row['SAKNR'];echo ' '; echo $row['TXT50'];?></option>                                  
                                                <?php endforeach ?>   
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="wbselement" class="form-label col-2">WBS Element</label>
                                                <select class="form-control" name="wbselement" id="wbselement">
                                                    <option value="">--Pilih WBS Element--</option>
                                                <?php foreach ($filterWBS as $row) : ?>
                                                    <option value="<?php echo  $row['POSID'];?>"><?php echo $row['POST1'];echo ' ';echo $row['POSID'];?></option>                                  
                                                <?php endforeach?>          
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="year" class="form-label col-2">Year</label>
                                                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
                                                <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
                                                <input type="number" class="form-control" id="year" name="year" required>    
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="month" class="form-label col-2">Month</label>
                                                <select class="form-control" name="month" id="month" required>
                                                    <option value=""> -- pilih bulan --</option>
                                                    <option value="1">(1) Januari</option>
                                                    <option value="2">(2) Februari</option>
                                                    <option value="3">(3) Maret</option>
                                                    <option value="4">(4) April</option>
                                                    <option value="5">(5) Mei</option>
                                                    <option value="6">(6) Juni</option>
                                                    <option value="7">(7) Juli</option>
                                                    <option value="8">(8) Agustus</option>
                                                    <option value="9">(9) September</option>
                                                    <option value="10">(10) Oktober</option>
                                                    <option value="11">(11) November</option>
                                                    <option value="12">(12) Desember</option>    
                                                </select>   
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="amount" class="form-label col-2">Amount</label>
                                                <input type="number" class="form-control" id="amount" name="amount" required>       
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
                        <form action="<?= site_url("finance/budgetfinance/update") ?>" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Data Budget Finance</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <input type="hidden" id="edid" name="edid">
                                            <div class="d-flex left-section mb-3">
                                                <label for="edglaccount" class="form-label col-2">GL Account</label>
                                                <select class="form-control" name="edglaccount" id="edglaccount" required>
                                                    <option value="">--Pilih GL Account--</option>
                                                <?php foreach ($filterGLAccount as $row) : ?>
                                                    <option <?php if(($_GET['idglaccount'] ?? false) == $row['TXT50']){echo("selected");}?> value="<?php echo  $row['SAKNR'];?>"><?php echo $row['SAKNR'];echo ' '; echo $row['TXT50'];?></option>                                  
                                                <?php endforeach ?>   
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edwbselement" class="form-label col-2">WBS Element</label>
                                                <select class="form-control" name="edwbselement" id="edwbselement" required>
                                                    <option value="">--Pilih WBS Element--</option>
                                                <?php foreach ($filterWBS as $row) : ?>
                                                    <option value="<?php echo  $row['POSID'];?>"><?php echo $row['POST1'];echo ' ';echo $row['POSID'];?></option>                                  
                                                <?php endforeach?>          
                                                </select>
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edyear" class="form-label col-2">Year</label>
                                                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
                                                <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
                                                <input type="number" class="form-control" id="edyear" name="edyear" required>    
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="edmonth" class="form-label col-2">Month</label>
                                                <select class="form-control" name="edmonth" id="edmonth" required>
                                                    <option value=""> -- pilih bulan --</option>
                                                    <option value="1">(1) Januari</option>
                                                    <option value="2">(2) Februari</option>
                                                    <option value="3">(3) Maret</option>
                                                    <option value="4">(4) April</option>
                                                    <option value="5">(5) Mei</option>
                                                    <option value="6">(6) Juni</option>
                                                    <option value="7">(7) Juli</option>
                                                    <option value="8">(8) Agustus</option>
                                                    <option value="9">(9) September</option>
                                                    <option value="10">(10) Oktober</option>
                                                    <option value="11">(11) November</option>
                                                    <option value="12">(12) Desember</option>    
                                                </select>   
                                            </div>
                                            <div class="d-flex left-section mb-3">
                                                <label for="amount" class="form-label col-2">Amount</label>
                                                <input type="number" class="form-control" id="edamount" name="edamount" required>       
                                            </div>
                                        </div>
                                    
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary" type="submit">Update Data</button>
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
                                    <th scope="col">GL Account</th>
                                    <th scope="col">GL Acc. Desc</th>
                                    <th scope="col">WBS Element</th>
                                    <th scope="col">WBS Elm. Desc</th>
                                    <th scope="col">Year</th>
                                    <th scope="col">Month</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Created On</th>
                                    <th scope="col">Edited By</th>
                                    <th scope="col">Edited On</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                foreach ($md_budg as $row) : ?>
                                    <tr>
                                        <td><?= $row['BUDGID'] ?></td>
                                        <td><?= $row['SAKNR'] ?></td>
                                        <td><?= $row['TXT50'] ?></td>
                                        <td><?= $row['PSPNR'] ?></td>
                                        <td><?= $row['POSID'] ?></td>
                                        <td><?= $row['GJAHR'] ?></td>
                                        <td><?php 
                                            if($row['MONAT'] == 1){echo '(1) Januari';}
                                            elseif($row['MONAT'] == 2){echo '(2) Februari';}
                                            elseif($row['MONAT'] == 3){echo '(3) Maret';}
                                            elseif($row['MONAT'] == 4){echo '(4) April';}
                                            elseif($row['MONAT'] == 5){echo '(5) Mei';}
                                            elseif($row['MONAT'] == 6){echo '(6) Juni';}
                                            elseif($row['MONAT'] == 7){echo '(7) Juli';}
                                            elseif($row['MONAT'] == 8){echo '(8) Agustus';}
                                            elseif($row['MONAT'] == 9){echo '(9) September';}
                                            elseif($row['MONAT'] == 10){echo '(10) Oktober';}
                                            elseif($row['MONAT'] == 11){echo '(11) November';}
                                            else{echo '(12) Desember';}?>
                                        </td>
                                        <td><?= $row['DMBTR'] ?></td>
                                        <td><?= $row['CRTBY'] ?></td>
                                        <td><?= $row['CRTON'] ?></td>
                                        <td><?= $row['EDTBY'] ?></td>
                                        <td><?= $row['EDTON'] ?></td>
                                        <td class="col">
                                            <a onclick="editForm(<?= $row['ID'] ?>)" href="#" class="btn btn-primary btn-sm col">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('are you sure')" href="<?= site_url("/finance/budgetfinance/delete/").$row['ID'] ?>" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
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
        fetch("<?= site_url('/finance/budgetfinance/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#edglaccount').val(res.SAKNR);
                $('#edwbselement').val(res.PSPNR);
                $('#edyear').val(res.GJAHR);
                $('#edmonth').val(res.MONAT);
                $('#edamount').val(res.DMBTR);
                $('#edid').val(id);
            })
            .then(() => {
                $('#editForm').modal('show');
            });
    }
    $("#year").datepicker({
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years"
    });
    $("#edyear").datepicker({
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years"
    });
</script>

<?= $this->endSection() ?>