<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Royalty Invoices</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("sales/invoice") ?>">Invoices</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("sales/royaltyinvoice") ?>">Royalty Invoice</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Royalty Invoice</h5>
                        <div class="d-flex justify-content-between mb-4">
                            
                            <div class="d-flex left-section col-5">
                                <span class="mx-2 my-auto">Date From</span>
                                <input type="date" name="datfrom" id="datfrom" class="form-control" value="<?= $_GET['datfrom'] ?? false ?>"/>
                                <span class="mx-2 my-auto">to</span>
                                <input type="date" name="datto" id="datto" class="form-control" value="<?= $_GET['datto'] ?? false ?>"/>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getData()">
                                    Filter
                                </button>
                                <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="getDataReset()">
                                    Reset
                                </button>
                                <script>
                                    function getData(){
                                        let datfrom= document.getElementById('datfrom').value
                                        let datto= document.getElementById('datto').value
                                        window.location.href="<?= site_url() ?>"+`/sales/royaltyinvoice?datfrom=${datfrom}&datto=${datto}`;
                                    }
                                    function getDataReset(){
                                        window.location.href="<?= site_url() ?>"+`/sales/royaltyinvoice/`;
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
                            <form action="<?= site_url("sales/royaltyinvoice/add") ?>" method="POST" class="needs-validation" novalidate
                                enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Invoice</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="contractno" class="form-label">Contract No.</label>
                                                </div>
                                                <div class="col-8">  
                                                    <select class="form-select" id="contractno" name="contractno" aria-label="Default select example" required>
                                                        <option selected value="">-- select Contract No. --</option>
                                                        <?php foreach ($opt_cont as $row) { ?>
                                                            <option value="<?= $row['contract_no'] ?>"> <?= $row['contract_no'] ?> </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="invdate" class="form-label">Invoice Date</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="date" class="form-control" id="invdate" name="invdate" required>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="posdate" class="form-label">Posting Date</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="date" class="form-control" id="posdate" name="posdate" required>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="reference" class="form-label">Reference</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="reference" name="reference">
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="royalty" class="form-label">Royalty</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="royalty" name="royalty" required>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="bankaccount" class="form-label">Cash/Bank Account</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="bankaccount" name="bankaccount" aria-label="Default select example" required>
                                                        <option selected value="">-- select Cash/Bank Account --</option>
                                                        <?php foreach ($opt_bank as $row) { ?>
                                                            <option value="<?= $row['SAKNR'] ?>"> <?= $row['TXT50'] ?> </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="currency" class="form-label">Currency</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="currency" name="currency" aria-label="Default select example" required>
                                                        <option selected value="">-- select Currency --</option>
                                                        <?php foreach ($opt_curr as $row) { ?>
                                                            <option value="<?= $row['FCURR'] ?>"> <?= $row['FCURR'] ?> </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="exchangerate" class="form-label">Exchange Rate</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="exchangerate" name="exchangerate">
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="formFile" class="form-label">Attachment</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input class="form-control" accept="application/pdf" type="file" id="formFile" name="formFile"> 
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="text" class="form-label">Text</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="text" name="text" required>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="wbselement" class="form-label">WBS Element</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="wbselement" name="wbselement" aria-label="Default select example" required>
                                                        <option selected value="">-- select WBS Element --</option>
                                                        <?php foreach ($opt_wbs as $row) { ?>
                                                            <option value="<?= $row['POSID'] ?>"> <?= $row['POSID'] ?> (<?= $row['POST1'] ?>) </option>
                                                        <?php } ?>
                                                    </select>  
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="costcenter" class="form-label">Cost Center</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="costcenter" name="costcenter" aria-label="Default select example">
                                                        <option selected value="">-- select Cost Center --</option>
                                                        <?php foreach ($opt_cost as $row) { ?>
                                                            <option value="<?= $row['KOSTL'] ?>"> <?= $row['LTEXT'] ?> </option>
                                                        <?php } ?>
                                                    </select>    
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="order" class="form-label">Order</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="order" name="order" aria-label="Default select example" required>
                                                        <option selected value="">-- select Order --</option>
                                                        <?php foreach ($opt_ord as $row) { ?>
                                                            <option value="<?= $row['AUFNR'] ?>"> <?= $row['KTEXT'] ?> </option>
                                                        <?php } ?>
                                                    </select>  
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>                                            
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
                        <div class="modal fade" id="editform" tabindex="-1">
                            <form action="<?= site_url("sales/royaltyinvoice/update") ?>" method="POST" class="needs-validation" 
                                novalidate enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Invoice</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <input type="number" id="id" name="id" hidden/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edcontractno" class="form-label">Contract No.</label>
                                                </div>
                                                <div class="col-8">  
                                                    <select class="form-select" id="edcontractno" name="edcontractno" aria-label="Default select example" required>
                                                        <option selected value="">-- select Contract No. --</option>
                                                        <?php foreach ($opt_cont as $row) { ?>
                                                            <option value="<?= $row['contract_no'] ?>"> <?= $row['contract_no'] ?> </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edinvdate" class="form-label">Invoice Date</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="date" class="form-control" id="edinvdate" name="edinvdate" required>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edposdate" class="form-label">Posting Date</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="date" class="form-control" id="edposdate" name="edposdate" required>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edreference" class="form-label">Reference</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="edreference" name="edreference">
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edroyalty" class="form-label">Royalty</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="edroyalty" name="edroyalty" required>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edbankaccount" class="form-label">Cash/Bank Account</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="edbankaccount" name="edbankaccount" aria-label="Default select example" required>
                                                        <option selected value="">-- select Cash/Bank Account --</option>
                                                        <?php foreach ($opt_bank as $row) { ?>
                                                            <option value="<?= $row['SAKNR'] ?>"> <?= $row['TXT50'] ?> </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edcurrency" class="form-label">Currency</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="edcurrency" name="edcurrency" aria-label="Default select example" required>
                                                        <option selected value="">-- select Currency --</option>
                                                        <?php foreach ($opt_curr as $row) { ?>
                                                            <option value="<?= $row['FCURR'] ?>"> <?= $row['FCURR'] ?> </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edexchangerate" class="form-label">Exchange Rate</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="edexchangerate" name="edexchangerate">
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edformFile" class="form-label">Attachment</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input class="form-control" type="file" id="edformFile" name="edformFile"> 
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edtext" class="form-label">Text</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="edtext" name="edtext" required>
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edwbselement" class="form-label">WBS Element</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="edwbselement" name="edwbselement" aria-label="Default select example" required>
                                                        <option selected value="">-- select WBS Element --</option>
                                                        <?php foreach ($opt_wbs as $row) { ?>
                                                            <option value="<?= $row['POSID'] ?>"> <?= $row['POSID'] ?> </option>
                                                        <?php } ?>
                                                    </select>  
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edcostcenter" class="form-label">Cost Center</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="edcostcenter" name="edcostcenter" aria-label="Default select example">
                                                        <option selected value="">-- select Cost Center --</option>
                                                        <?php foreach ($opt_cost as $row) { ?>
                                                            <option value="<?= $row['KOSTL'] ?>"> <?= $row['LTEXT'] ?> </option>
                                                        <?php } ?>
                                                    </select>    
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="edorder" class="form-label">Order</label>
                                                </div>
                                                <div class="col-8">   
                                                    <select class="form-select" id="edorder" name="edorder" aria-label="Default select example" required>
                                                        <option selected value="">-- select Order --</option>
                                                        <?php foreach ($opt_ord as $row) { ?>
                                                            <option value="<?= $row['AUFNR'] ?>"> <?= $row['KTEXT'] ?> </option>
                                                        <?php } ?>
                                                    </select>  
                                                </div>
                                                <!-- <div class="valid-tooltip">
                                                    Looks good!
                                                </div> -->
                                            </div><br/>                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary" type="submit">Save Data</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal fade" id="showform" tabindex="-1">
                            <form action="#" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Invoice</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <input type="number" id="id" name="id" hidden/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="scontractno" class="form-label">Contract No.</label>
                                                </div>
                                                <div class="col-8">  
                                                    <input type="text" class="form-control" id="scontractno" name="scontractno" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="sinvdate" class="form-label">Invoice Date</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="date" class="form-control" id="sinvdate" name="sinvdate" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="sposdate" class="form-label">Posting Date</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="date" class="form-control" id="sposdate" name="sposdate" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="sreference" class="form-label">Reference</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="sreference" name="sreference" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="sroyalty" class="form-label">Royalty</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="sroyalty" name="sroyalty" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="sbankaccount" class="form-label">Cash/Bank Account</label>
                                                </div>
                                                <div class="col-8">
                                                    <input type="text" class="form-control" id="sbankaccount" name="sbankaccount" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="scurrency" class="form-label">Currency</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="scurrency" name="scurrency" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="sexchangerate" class="form-label">Exchange Rate</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="sexchangerate" name="sexchangerate" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="sformFile" class="form-label">Attachment</label>
                                                </div>
                                                <div class="col-8">   
                                                    <!-- <input class="form-control" accept="application/pdf" type="file" id="sformFile" name="sformFile" readonly>  -->
                                                    <a href ="javascript:download_pdf()" class="btn btn-sm btn-warning rounded"><i class="ri-download-line"></i></a>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="stext" class="form-label">Text</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="stext" name="stext" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="swbselement" class="form-label">WBS Element</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="swbselement" name="swbselement" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="scostcenter" class="form-label">Cost Center</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="scostcenter" name="scostcenter" readonly>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="sorder" class="form-label">Order</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="sorder" name="sorder" readonly>
                                                </div>
                                            </div><br/>                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <!-- <button class="btn btn-primary" type="submit">Save Data</button> -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Table users -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Shipment ID</th>
                                    <th scope="col">Contract No</th>
                                    <th scope="col">Vendor</th>
                                    <th scope="col">Reference</th>
                                    <th scope="col">Doc. No. SAP</th>
                                    <th scope="col">Doc. No. Reversal SAP</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Created On</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tdata as $row) { ?>
                                <tr>
                                    <td><?= $row['SHIPMENT_ID'] ?></td>
                                    <td><?= $row['CONTRCT_NO'] ?></td>
                                    <td></td>
                                    <td><?= $row['XBLNR'] ?></td>
                                    <td>
                                        <?php
                                            if ($row['STATUS_SAP'] == "E")
                                                echo $row['MESSAGE_SAP'];
                                            else
                                                echo $row['BELNR'];         
                                        ?>
                                    </td>
                                    <td><?= $row['STBLG'] ?></td>
                                    <td><?= $row['CPUDT'] ?></td>
                                    <td><?= $row['USNAM'] ?></td>
                                    <td>
                                        <?php if (!$row['BELNR']) { ?>
                                            <button type="button" onclick="editForm(<?= $row['id'] ?>)" class="btn btn-outline-dark" data-bs-toggle="modal"> 
                                                <i class="ri-edit-2-fill"></i>
                                            </button>
                                            <a class="btn btn-outline-danger" onclick="return confirm('are you sure')" href="<?= site_url("/sales/royaltyinvoice/delete/").$row['id'] ?>" >           
                                                <i class="ri-delete-bin-6-line"></i>
                                            </a>  
                                        <?php } ?>
                                        <button type="button" onclick="showForm(<?= $row['id'] ?>)" class="btn btn-outline-dark">                                          
                                            <i class="ri-search-line"></i>      
                                        </button>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
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
        fetch("<?= site_url('/sales/royaltyinvoice/get/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#edcontractno').val(res.CONTRCT_NO);
                $('#edinvdate').val(res.BLDAT);
                $('#edposdate').val(res.BUDAT);
                $('#edreference').val(res.XBLNR);
                $('#edroyalty').val(res.ROYLT);
                $('#edbankaccount').val(res.CNB_Acc);
                $('#edcurrency').val(res.ECURR);
                $('#edexchangerate').val(res.KURSF);
                // $('#edformFile').val(res.ATTCH);
                $('#edformFile').attr('href', res.ATTCH);
                $('#edtext').val(res.SGTXT);
                $('#edwbselement').val(res.PROJK);
                $('#edcostcenter').val(res.KOSTL);
                $('#edorder').val(res.AUFNR);
                $('#id').val(id);
            })
            .then(() => {
                $('#editform').modal('show');
            });
    }

    function showForm(id) {
        fetch("<?= site_url('/sales/royaltyinvoice/get/') ?>" + id)
            .then(res => res.json())
            .then(res => { 
                console.log(res);
                $('#scontractno').val(res.CONTRCT_NO);
                $('#sinvdate').val(res.BLDAT);
                $('#sposdate').val(res.BUDAT);
                $('#sreference').val(res.XBLNR);
                $('#sroyalty').val(res.ROYLT);
                $('#sbankaccount').val(res.CNB_Acc+' ('+res.BANKTEXT+')');
                $('#scurrency').val(res.ECURR);
                $('#sexchangerate').val(res.KURSF);
                $('#sformFile').val(res.ATTCH);
                $('#stext').val(res.SGTXT);
                $('#swbselement').val(res.PROJK);
                $('#scostcenter').val(res.KOSTL+' ('+res.COSTTEXT+')');
                $('#sorder').val(res.AUFNR+' ('+res.ORDTEXT+')');
                $('#id').val(id);
                var scrt_var = id;  
                download_pdf = function() {
                location.href = "<?= site_url('/sales/royaltyinvoice/download/') ?>"+scrt_var;
                }
            })
            .then(() => {
                $('#showform').modal('show');
            });
    }
</script>

<?= $this->endSection() ?>