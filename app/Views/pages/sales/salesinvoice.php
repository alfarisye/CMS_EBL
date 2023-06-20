<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Update Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("sales/invoice") ?>">Invoices</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("sales/salesinvoice") ?>">Sales Invoice</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sales Invoice</h5>
                    

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <input type="date" onchange="fetchAccountReceivable()" name="" id="asOfDateReceivable" class="form-control" value="<?= $_GET['as_date_rec'] ?? date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <input type="date" onchange="fetchAccountReceivable()" name="" id="asOfDateReceivable" class="form-control" value="<?= $_GET['as_date_rec'] ?? date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>
                        <!-- // ! Add contractor modal -->

                        <div class="row">
                            <div class="col-md-4 offset-md-4"><br>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="bi bi-plus me-1"></i> New Invoice
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
                                                    <label for="dates" class="form-label">Shipment ID</label>
                                                </div>
                                                <div class="col-8">  
                                                    <select class="form-select" aria-label="Default select example">
                                                        <option selected>Open this select menu</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                    </select>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Contract No.</label>
                                                </div>
                                                <div class="col-8">  
                                                    <select class="form-select" aria-label="Default select example">
                                                        <option selected>Open this select menu</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                    </select>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Buyer</label>
                                                </div>
                                                <div class="col-8">  
                                                    <select class="form-select" aria-label="Default select example">
                                                        <option selected>Open this select menu</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                    </select>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Invoice Date</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Posting Date</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Baseline Date</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Payt Term</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Attachment</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input class="form-control" type="file" id="formFile"> </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Actual Price</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Actual Qty</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Actual SPC</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Sales</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Potongan Penjualan</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">PPN Keluaran</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Outst Bkt Ptg PPh 22</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">WBS Element</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Profit Center</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Order</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="dates" class="form-label">Text</label>
                                                </div>
                                                <div class="col-8">   
                                                    <input type="text" class="form-control" id="dates" name="dates" required>
                                                </div>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
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
                        <div class="modal fade" id="editForm" tabindex="-1">
                        <form action="#" method="POST" class="needs-validation" novalidate>
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">New Invoice</h5>
                                            <h5 class="modal-title">RKAP</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Custom Styled Validation with Tooltips -->
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Shipment ID</label>
                                                <label for="dates" class="form-label">Year</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Contract No.</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <label for="contractor_type">Month</label>
                                                <select class="form-control" name="contractor_type" id="contractor_type" required>
                                                <option value="1">Januari</option>
                                                <option value="2">Februari</option>
                                                <option value="3">Maret</option>
                                                <option value="4">April</option>
                                                <option value="5">Mei</option>
                                                <option value="6">Juni</option>
                                                <option value="7">Juli</option>
                                                <option value="8">Agustus</option>
                                                <option value="9">September</option>
                                                <option value="10">Oktober</option>
                                                <option value="11">November</option>
                                                <option value="12">Desember</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="contractor_type">Shipment</label>
                                                <select class="form-control" name="contractor_type" id="contractor_type" required>
                                                    <option value="FC">FC</option>
                                                    <option value="VC">VC</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="username" class="form-label">Price(RP/MT)</label>
                                                <input type="number" class="form-control" id="name" name="name" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Buyer</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Invoice Date</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Posting Date</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Baseline Date</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Pay Term</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Currency</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Exchange Rate</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Attachment</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12 position-relative mb-3">
                                                <label for="dates" class="form-label">Actual Price</label>
                                                <input type="text" class="form-control" id="dates" name="dates" required>
                                                <label for="dates" class="form-label">Price(MT)</label>
                                                <input type="number" class="form-control" id="dates" name="dates" required>
                                                <div class="valid-tooltip">
                                                    Looks good!
                                                </div>
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
                                    <th scope="col">Shipment ID</th>
                                    <th scope="col">Contract No</th>
                                    <th scope="col">Buyer</th>
                                    <th scope="col">Doc. No. SAP</th>
                                    <th scope="col">Doc. No. Reversal SAP</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Created On</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>12345</td>
                                    <td>54321</td>
                                    <td>bagus</td>
                                    <td>09876</td>
                                    <td>67890</td>
                                    <td>bagus</td>
                                    <td>hallo</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered"> 
                                            <i class="ri-edit-2-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger">           
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>  
                                        <button type="button" class="btn btn-outline-dark">                                          
                                            <i class="ri-search-line"></i>      
                                        </button>                    
                                    </td>
                                </tr>
                                <tr>
                                    <td>12345</td>
                                    <td>54321</td>
                                    <td>bagus</td>
                                    <td>09876</td>
                                    <td>67890</td>
                                    <td>bagus</td>
                                    <td>hallo</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered"> 
                                            <i class="ri-edit-2-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger">           
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>  
                                        <button type="button" class="btn btn-outline-dark">                                          
                                            <i class="ri-search-line"></i>      
                                        </button>                    
                                    </td>
                                </tr>
                                <tr>
                                    <td>12345</td>
                                    <td>54321</td>
                                    <td>bagus</td>
                                    <td>09876</td>
                                    <td>67890</td>
                                    <td>bagus</td>
                                    <td>hallo</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered"> 
                                            <i class="ri-edit-2-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger">           
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>  
                                        <button type="button" class="btn btn-outline-dark">                                          
                                            <i class="ri-search-line"></i>      
                                        </button>                    
                                    </td>
                                </tr>
                                <tr>
                                    <td>12345</td>
                                    <td>54321</td>
                                    <td>bagus</td>
                                    <td>09876</td>
                                    <td>67890</td>
                                    <td>bagus</td>
                                    <td>hallo</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered"> 
                                            <i class="ri-edit-2-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger">           
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>  
                                        <button type="button" class="btn btn-outline-dark">                                          
                                            <i class="ri-search-line"></i>      
                                        </button>                    
                                    </td>
                                </tr>
                                <tr>
                                    <td>12345</td>
                                    <td>54321</td>
                                    <td>bagus</td>
                                    <td>09876</td>
                                    <td>67890</td>
                                    <td>bagus</td>
                                    <td>hallo</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered"> 
                                            <i class="ri-edit-2-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger">           
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>  
                                        <button type="button" class="btn btn-outline-dark">                                          
                                            <i class="ri-search-line"></i>      
                                        </button>                    
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </section>


                </div>
            </div>
        </div>
    </section>

</main>

<?= $this->endSection() ?>