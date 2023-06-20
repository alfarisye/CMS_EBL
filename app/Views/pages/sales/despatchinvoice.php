<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Despatch Invoice</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("/sales/invoice") ?>">Invoices</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("despatchinvoice") ?>">Despatch
                        Invoice</a></li>
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
                        <h5 class="card-title">Despatch Invoice</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                        <div class="alert alert-warning" role="alert">
                            <p><?= session()->getFlashdata('message') ?></p>
                        </div>
                        <?php endif ?>
                        <div class="row">
                            <div class="col-6 text-xs">
                                <table>
                                    <tr>
                                        <td>
                                            <input type="date" id="dari_tanggal" name="dari_tanggal"
                                                class="form-control p-1 rounded-sm shadow-sm" placeholder="dari_tanggal"
                                                v-model="dari_tanggal" value="<?= $_GET['daritanggal'] ?? false ?>">
                                        </td>
                                        <td class="px-3">-</td>
                                        <td>
                                            <input type="date" id="sampai_tanggal" 
                                                name="sampai_tanggal" class="form-control p-1 rounded-sm shadow-sm"
                                                placeholder="sampai_tanggal" v-model="sampai_tanggal" value="<?= $_GET['sampaitanggal'] ?? false ?>">
                                        </td>
                                        <td>
                                            <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="filter()">
                                                Filter
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" class="ms-2 my-2 btn btn-primary" id="filter" name="filter" onclick="reset()">
                                                Reset
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- add data -->
                        <div class="d-flex right-section" style="margin-top: 20px; ">
                            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                data-bs-target="#verticalycentered">
                                <i class="bi bi-plus me-1"></i> New Invoice
                            </button>
                        </div>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("despatchinvoice/add") ?>" method="POST" novalidate
                                enctype="multipart/form-data" class="needs-validation" novalidate>

                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"> Add New Invoice </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">


                                            <div class="container">
                                                <div class="row">
                                                    <div class="col">
                                                        <label class="form-label">Shipment ID</label>
                                                        <select onchange="getContract()" id="shipmentID" name="shipmentID"
                                                            class="form-select" required>
                                                            <option selected>Select Shipment ID</option>
                                                            <?php
                                                            foreach ($SalesShipBuilder as $row) : ?>
                                                            <option value="<?= $row['shipment_id'] ?>">
                                                                <?= $row['shipment_id'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                            <label class="form-label">Invoice Date</label>
                                                            <input name="invoice_date" type="date" id="invoice_date"
                                                                class="form-control" placeholder="Invoice Date ..."
                                                                aria-label="" required></input>
                                                            <!-- <input hidden name="contractN" id="contractN" class="form-control"
                                                                placeholder="Contract No. ..." aria-label="" required></input>
                                                    
                                                        <label class="form-label">Contract No.</label>
                                                    
                                                            <input name="contractN2" id="contractN2" class="form-control"
                                                                placeholder="Contract No. ..." aria-label="" disabled></input> -->
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <input hidden name="contractN" id="contractN" class="form-control"
                                                                placeholder="Contract No. ..." aria-label="" required></input>
                                                    
                                                        <label class="form-label">Contract No.</label>
                                                    
                                                            <input name="contractN2" id="contractN2" class="form-control"
                                                                placeholder="Contract No. ..." aria-label="" disabled></input>
                                                    </div>
                                                    <div class="col">
                                                         <label class="form-label">Posting Date</label>
                                                        <input name="posting_date" type="date" id="posting_date"
                                                            class="form-control" placeholder="Posting Date ..."
                                                            aria-label="" required></input>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <input hidden name="Buyer" id="Buyer" class="form-control"
                                                                placeholder="Buyer ..." aria-label="" required></input>
                                                    
                                                        <label class="form-label">Buyer</label>
                                                    
                                                            <input name="Buyer2" id="Buyer2" class="form-control"
                                                                placeholder="Buyer ..." aria-label="" disabled></input>
                                                        <!-- <label class="form-label">Posting Date</label>
                                                            <input name="posting_date" type="date" id="posting_date"
                                                                class="form-control" placeholder="Posting Date ..."
                                                                aria-label="" required></input> -->
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Currency</label>
                                                        <select id="currency" name="currency" class="form-select"
                                                            required>
                                                            <option selected>Select Currency</option>
                                                            <?php
                                                            foreach ($Currency as $row) : ?>
                                                            <option value="<?= $row['TCURR'] ?>"><?= $row['TCURR'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <label class="form-label">Baseline Date</label>
                                                        <input name="baseline_date" type="date" id="baseline_date"
                                                            class="form-control" placeholder="Baseline Date ..."
                                                            aria-label="" required></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Exchange Rate</label>
                                                        <input type="number" name="exchange_rate" id="exchange_rate" class="form-control"
                                                            placeholder="Exchange Rate ..." aria-label=""></input>
                                                            
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <label class="form-label">Payt Term</label>
                                                        <select id="payterm" name="payterm" class="form-select"
                                                            required>
                                                            <option selected>Select Payt Term</option>
                                                            <?php
                                                            foreach ($Payterms as $row) : ?>
                                                            <option value="<?= $row['ZTERM'] ?>"><?= $row['ZTERM'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Attachment</label>
                                            
                                                        <input type="file" accept="application/pdf" name="att" id="att"
                                                            class="form-control" placeholder="Attachment ..."
                                                            aria-label=""></input>       
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                    <label class="form-label">Reference</label>
                                                            <input name="reference" id="reference" class="form-control"
                                                                placeholder="Reference ..." aria-label="" required></input>
                                                    
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Text</label>
                                                        <div class="col-sm-12">
                                                            <input name="text" id="text" class="form-control"
                                                                placeholder="Text ..." aria-label="" required></input>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <label class="form-label">Despatch</label>
                                                        <div class="col-sm-12">
                                                            <input type="number" name="despatch" id="despatch" class="form-control"
                                                                placeholder="Despatch ..." aria-label=""></input>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                    <label class="form-label">WBS Element</label>
                                                        <select id="WBS_element" name="WBS_element" class="form-select"
                                                            required>
                                                            <option selected>Select WBS Element</option>
                                                            <?php
                                                            foreach ($PBUKR as $row) : ?>
                                                            <option value="<?= $row['POSID'] ?>"><?= $row['POSID'] ?> - <?= $row['POST1'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col">
                                                        <label class="form-label">Order</label>
                                                        <select id="order" name="order" class="form-select"
                                                            required>
                                                            <option selected>Select Order</option>
                                                            <?php
                                                            foreach ($Order as $row) : ?>
                                                            <option value="<?= $row['AUFNR'] ?>"><?= $row['AUFNR'] ?> - <?= $row['KTEXT'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Profit Center</label>
                                                        <select id="profit_center" name="profit_center" class="form-select"
                                                            required>
                                                            <option selected>Select Profit Center</option>
                                                            <?php
                                                            foreach ($ProfitCenter as $row) : ?>
                                                            <option value="<?= $row['PRCTR'] ?>"><?= $row['LTEXT'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        
                                                    </div>
                                                    <div class="col">
                                                    
                                                    </div>
                                                        <input hidden disabled name="Doc_No" id="Doc_No"
                                                            class="form-control" placeholder="Doc_No ..." aria-label=""
                                                            required></input>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success" type="submit">Save Data</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Edit Data -->
                        <div class="modal fade" id="editForm" tabindex="-1">
                            <form action="<?= site_url("despatchinvoice/update") ?>" method="POST"
                                lass="needs-validation" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"> Edit Despatch Invoice </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input hidden id="ID" name="ID">
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Shipment ID</label>
                                                <select onchange="getContractedit()" id="shipmentIDup" name="shipmentIDup"
                                                    class="form-select" required>
                                                    <option selected>Select Shipment ID</option>
                                                    <?php
                                                    foreach ($SalesShipBuilder as $row) : ?>
                                                    <option value="<?= $row['shipment_id'] ?>">
                                                        <?= $row['shipment_id'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <div class="col-sm-12">
                                                    <input hidden name="contractNup" id="contractNup"
                                                        class="form-control" placeholder="Contract No. ..."
                                                        aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Contract No.</label>
                                                <div class="col-sm-12">
                                                    <input name="contractN2up" id="contractN2up" class="form-control"
                                                        placeholder="Contract No. ..." aria-label="" disabled></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <input hidden name="Buyerup" id="Buyerup" class="form-control"
                                                                placeholder="Buyer ..." aria-label="" required></input>
                                                    
                                                        <label class="form-label">Buyer</label>
                                                    
                                                            <input name="Buyer2up" id="Buyer2up" class="form-control"
                                                                placeholder="Buyer ..." aria-label="" disabled></input>
                                            
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Invoice Date</label>
                                                <div class="col-sm-12">
                                                    <input name="invoice_dateup" type="date" id="invoice_dateup"
                                                        class="form-control" placeholder="Invoice Date ..."
                                                        aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Posting Date</label>
                                                <div class="col-sm-12">
                                                    <input name="posting_dateup" type="date" id="posting_dateup"
                                                        class="form-control" placeholder="Posting Date ..."
                                                        aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Baseline Date</label>
                                                <div class="col-sm-12">
                                                    <input name="baseline_dateup" type="date" id="baseline_dateup"
                                                        class="form-control" placeholder="Baseline Date ..."
                                                        aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Payt Term</label>
                                                <select id="paytermup" name="paytermup" class="form-select"
                                                    required>
                                                    <option selected>Select Payt Term</option>
                                                    <?php
                                                    foreach ($Payterms as $row) : ?>
                                                    <option value="<?= $row['ZTERM'] ?>"><?= $row['ZTERM'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Attachment</label>
                                                <div class="col-sm-12">
                                                    <input type="file" accept="application/pdf" name="attup" id="attup"
                                                        class="form-control" placeholder="Attachment ..." aria-label=""
                                                        ></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Exchange Rate</label>
                                                <div class="col-sm-12">
                                                    <input name="exchange_rateup" id="exchange_rateup"
                                                        class="form-control" placeholder="Exchange Rate ..."
                                                        aria-label=""></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Reference</label>
                                                <div class="col-sm-12">
                                                    <input name="referenceup" id="referenceup" class="form-control"
                                                        placeholder="Reference ..." aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Despatch</label>
                                                <div class="col-sm-12">
                                                    <input name="despatchup" id="despatchup" class="form-control"
                                                        placeholder="Reference ..." aria-label="" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">WBS Element</label>
                                                <select id="WBS_elementup" name="WBS_elementup" class="form-select"
                                                    required>
                                                    <option selected>Select WBS Element</option>
                                                    <?php
                                                    foreach ($PBUKR as $row) : ?>
                                                    <option value="<?= $row['POSID'] ?>"><?= $row['POSID'] ?> - <?= $row['POST1'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Profit Center</label>
                                                <select id="profit_centerup" name="profit_centerup" class="form-select"
                                                    required>
                                                    <option selected>Select Profit Center</option>
                                                    <?php
                                                    foreach ($ProfitCenter as $row) : ?>
                                                    <option value="<?= $row['PRCTR'] ?>"><?= $row['LTEXT'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Order</label>
                                                <select id="orderup" name="orderup" class="form-select"
                                                    required>
                                                    <option selected>Select Order</option>
                                                    <?php
                                                    foreach ($Order as $row) : ?>
                                                    <option value="<?= $row['AUFNR'] ?>"><?= $row['AUFNR'] ?> - <?= $row['KTEXT'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Text</label>
                                                <div class="col-sm-12">
                                                    <textarea name="textup" id="textup" class="form-control"
                                                        placeholder="Text ..." aria-label="" required></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <div class="col-sm-12">
                                                    <input hidden disabled name="Doc_Noup" id="Doc_Noup"
                                                        class="form-control" placeholder="Doc_No ..." aria-label=""
                                                        required></input>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success" type="submit">Update Data</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- View data -->
                        <div class="modal fade" id="viewForm" tabindex="-1">
                            <form action="#" method="#" lass="needs-validation">
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"> View Despatch Invoice </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Shipment ID</label>
                                                <select onchange="getContractview()" id="shipmentIDview"
                                                    name="shipmentIDview" class="form-select" disabled>
                                                    <option selected>Select Shipment ID</option>
                                                    <?php
                                                    foreach ($SalesShipBuilder as $row) : ?>
                                                    <option value="<?= $row['shipment_id'] ?>">
                                                        <?= $row['shipment_id'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <div class="col-sm-12">
                                                    <input hidden name="contractNview" id="contractNview"
                                                        class="form-control" placeholder="Contract No. ..."
                                                        aria-label="" disabled></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Contract No.</label>
                                                <div class="col-sm-12">
                                                    <input name="contractN2view" id="contractN2view"
                                                        class="form-control" placeholder="Contract No. ..."
                                                        aria-label="" disabled></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <!-- <label class="form-label">Buyer</label> -->
                                                <input hidden name="Buyerview" id="Buyerview" class="form-control"
                                                                placeholder="Buyer ..." aria-label="" required></input>
                                                    
                                                        <label class="form-label">Buyer</label>
                                                    
                                                            <input name="Buyer2view" id="Buyer2view" class="form-control"
                                                                placeholder="Buyer ..." aria-label="" disabled></input>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Invoice Date</label>
                                                <div class="col-sm-12">
                                                    <input name="invoice_dateview" type="date" id="invoice_dateview"
                                                        class="form-control" placeholder="Invoice Date ..."
                                                        aria-label="" disabled></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Posting Date</label>
                                                <div class="col-sm-12">
                                                    <input name="posting_dateview" type="date" id="posting_dateview"
                                                        class="form-control" placeholder="Posting Date ..."
                                                        aria-label="" disabled></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Baseline Date</label>
                                                <div class="col-sm-12">
                                                    <input name="baseline_dateview" type="date" id="baseline_dateview"
                                                        class="form-control" placeholder="Baseline Date ..."
                                                        aria-label="" disabled></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Payt Term</label>
                                                <div class="col-sm-12">
                                                        <select id="paytermview" name="paytermview" class="form-select"
                                                            disabled>
                                                            <option selected>Select Payt Term</option>
                                                            <?php
                                                            foreach ($Payterms as $row) : ?>
                                                            <option value="<?= $row['ZTERM'] ?>"><?= $row['ZTERM'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                </div>
                                            </div>
                                            

                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Attachment</label>
                                                <div class="col-sm-12">
                                                     <a href ="javascript:download_pdf()" class="btn btn-sm btn-warning rounded"><i class="ri-download-line"></i></a>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Exchange Rate</label>
                                                <div class="col-sm-12">
                                                    <input name="exchange_rateview" id="exchange_rateview"
                                                        class="form-control" placeholder="Exchange Rate ..."
                                                        aria-label="" disabled></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Reference</label>
                                                <div class="col-sm-12">
                                                    <input name="referenceview" id="referenceview" class="form-control"
                                                        placeholder="Reference ..." aria-label="" disabled></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Despatch</label>
                                                <div class="col-sm-12">
                                                    <input name="despatchview" id="despatchview" class="form-control"
                                                        placeholder="Despatch ..." aria-label="" disabled></input>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">WBS Element</label>
                                                <select id="WBS_elementview" name="WBS_elementview" class="form-select"
                                                    disabled>
                                                    <option selected>Select WBS Element</option>
                                                    <?php
                                                    foreach ($PBUKR as $row) : ?>
                                                    <option value="<?= $row['POSID'] ?>"><?= $row['POSID'] ?> - <?= $row['POST1'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Profit Center</label>
                                                <select id="profit_centerview" name="profit_centerview" class="form-select" disabled>
                                                    <option selected>Select Profit Center</option>
                                                    <?php
                                                    foreach ($ProfitCenter as $row) : ?>
                                                    <option value="<?= $row['PRCTR'] ?>"><?= $row['LTEXT'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Order</label>
                                                <select id="orderview" name="orderview" class="form-select" disabled>
                                                    <option selected>Select Order</option>
                                                    <?php
                                                    foreach ($Order as $row) : ?>
                                                    <option value="<?= $row['AUFNR'] ?>"><?= $row['KTEXT'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Text</label>
                                                <div class="col-sm-12">
                                                    <textarea name="textview" id="textview" class="form-control"
                                                        placeholder="Text ..." aria-label="" disabled></textarea>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-12 mt-3">
                                                <div class="col-sm-12">
                                                    <input dname="Doc_Noview" id="Doc_Noview" class="form-control"
                                                        placeholder="Doc_No ..." aria-label="" disabled ></input>
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="modal-footer">
                                            <!-- <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success" type="submit">viewdate Data</button> -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>





                        <!-- Dashboard data -->

                        <div class="table-responsive">
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr style="background-color: #03fcc2;">
                                        <th scope="col">Shipment ID</th>
                                        <th scope="col">Contract No.</th>
                                        <th scope="col">Buyer</th>
                                        <th scope="col">Reference</th>
                                        <th scope="col">Doc. No. SAP</th>
                                        <th scope="col">Doc. No. Reversal SAP</th>
                                        <th scope="col">Created By</th>
                                        <th scope="col">Created On</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php 
                                    $no = 1;
                                    foreach ($MDMasterBuild as $row) { ?>
                                    <tr>
                                        <!-- <td><?= $no++ ?>.</td> -->

                                        <td><?= $row['SHIPMENT_ID'] ?></td>
                                        <td><?= $row['CONTRCT_NO'] ?></td>
                                        <td><?= $row['KUNNR'] ?></td>
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
                                        <td><?= $row['USNAM'] ?></td>
                                        <td><?php
                                        $newUpdate = date("d-m-Y", strtotime($row['CPUDT']));
                                        echo $newUpdate;
                                        ?>
                                        </td>
                                        <td class="col">
                                        <?php if($row['BELNR']=="" or is_null($row['BELNR'])){?>
                                            <a style="margin-left: 3px; margin-top: 5px;"
                                                onclick="editForm('<?= $row['ID']?>')"
                                                class="btn btn-sm btn-primary rounded-circle text-sm">
                                                <i class="my-1 py-1 ri-pencil-fill text-sm"
                                                    style="font-size : 12px;"></i></a>
                                            <a style="margin-left: 3px; margin-top: 5px;"
                                                onclick="return confirm('Are you sure?')"
                                                href="<?= site_url('despatchinvoice/delete/') ?><?= $row['ID'] ?>"
                                                class="btn btn-sm btn-danger rounded-circle text-sm"> <i
                                                    class="my-1 py-1 ri-delete-bin-line text-sm"
                                                    style="font-size : 12px;"></i></a>
                                        <?php }  ?>
                                            <a style="margin-left: 3px; margin-top: 5px;"
                                                onclick="viewForm('<?= $row['ID']?>')"
                                                class="btn btn-sm btn-warning rounded-circle text-sm"> <i
                                                    class="my-1 py-1 ri-search-line text-sm"
                                                    style="font-size : 12px;"></i></a>
                                        </td>
                                    </tr>
                                    <?php }  ?>
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
    function getContract(x) {
        var x = document.getElementById("shipmentID").value;
        // document.getElementById("contract_no").value = x;
        fetch("<?= site_url('/despatchinvoice/get/')?>" + x)
            .then(res => res.json())
            .then(res => {
                console.log(x);
                console.log(res);
                $('#contractN').val(res.SalesShip[0].contract_no);
                $('#contractN2').val(res.SalesShip[0].contract_no);
                $('#Buyer').val(res.Buyer);
                $('#Buyer2').val(res.Buyer+' - '+res.BuyerDesc);
                $('#despatch').val(res.SalesLay.length > 0 ? res.SalesLay[0].value_demmurage :
                0); //harus True False or True True False OR TRUE TRUE FALSE ++
            });
    }

    function getContractedit(x) {
        var x = document.getElementById("shipmentIDup").value;
        // document.getElementById("contract_no").value = x;
        fetch("<?= site_url('/despatchinvoice/get/')?>" + x)
            .then(res => res.json())
            .then(res => {
                console.log(x);
                console.log(res);
                $('#contractNup').val(res.SalesShip[0].contract_no);
                $('#contractN2up').val(res.SalesShip[0].contract_no);
                $('#Buyerup').val(res.Buyer);
                $('#Buyer2up').val(res.Buyer+' - '+res.BuyerDesc);
                $('#despatch').val(res.SalesLay.length > 0 ? res.SalesLay[0].value_demmurage :
                0); //harus True False or True True False OR TRUE TRUE FALSE ++
            });
    }

    function getContractview(x) {
        var x = document.getElementById("shipmentIDview").value;
        // document.getElementById("contract_no").value = x;
        fetch("<?= site_url('/despatchinvoice/get/')?>" + x)
            .then(res => res.json())
            .then(res => {
                console.log(x);
                console.log(res);
                $('#contractNview').val(res.SalesShip[0].contract_no);
                $('#contractN2view').val(res.SalesShip[0].contract_no);
                $('#Buyerview').val(res.Buyer);
                $('#Buyer2view').val(res.Buyer+' - '+res.BuyerDesc);
                $('#despatch').val(res.SalesLay.length > 0 ? res.SalesLay[0].value_demmurage :
                0); //harus True False or True True False OR TRUE TRUE FALSE ++
            });
    }

    function editForm(id) {
        // alert("hai")
        fetch("<?= site_url('/despatchinvoice/getEdit/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#shipmentIDup').val(res.SHIPMENT_ID);
                $('#contractNup').val(res.CONTRCT_NO);
                $('#contractN2up').val(res.CONTRCT_NO);
                $('#Buyerup').val(res.KUNNR);
                $('#Buyer2up').val(res.KUNNR);
                $('#invoice_dateup').val(res.BLDAT);
                $('#posting_dateup').val(res.BUDAT);
                $('#baseline_dateup').val(res.ZFBDT);
                $('#paytermup').val(res.ZTERM);
                $('#test_attch').text(res.ATTCH);
                $('#test').attr('href', res.ATTCH);
                $('#despatchup').val(res.DSPTCH);
                $('#referenceup').val(res.XBLNR);
                $('#exchange_rateup').val(res.KURSF);
                $('#WBS_elementup').val(res.PROJK);
                $('#profit_centerup').val(res.PRCTR);
                $('#orderup').val(res.AUFNR);
                $('#textup').val(res.SGTXT);
                $('#Doc_Noup').val(res.DOC_NO);
                $('#ID').val(res.ID);
            })
            .then(() => {
                $('#editForm').modal('show');
            });
    }

    function myFunction(id) {
        fetch("<?= site_url('/despatchinvoice/getEdit/') ?>" + id)
    }

    function viewForm(id) {
        fetch("<?= site_url('/despatchinvoice/getEdit/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#shipmentIDview').val(res.SHIPMENT_ID);
                $('#contractNview').val(res.CONTRCT_NO);
                $('#contractN2view').val(res.CONTRCT_NO);
                $('#Buyerview').val(res.KUNNR);
                $('#Buyer2view').val(res.KUNNR);
                $('#invoice_dateview').val(res.BLDAT);
                $('#posting_dateview').val(res.BUDAT);
                $('#baseline_dateview').val(res.ZFBDT);
                $('#paytermview').val(res.ZTERM);
                $('#despatchview').val(res.DSPTCH);
                $('#referenceview').val(res.XBLNR);
                $('#exchange_rateview').val(res.KURSF);
                $('#WBS_elementview').val(res.PROJK);
                $('#profit_centerview').val(res.PRCTR);
                $('#orderview').val(res.AUFNR);
                $('#textview').val(res.SGTXT);
                $('#Doc_Noview').val(res.DOC_NO);
                $('#IDview').val(res.ID);
                var scrt_var = res.ID;  
                download_pdf = function() {
                location.href = "<?= base_url(); ?>/despatchinvoice/download/"+scrt_var;
                }
                // console.log(scrt_var);
            })
            .then(() => {
                $('#viewForm').modal('show');
            });
    }
    function filter(){
        let id_cat= document.getElementById('dari_tanggal').value;
        let id_cat2= document.getElementById('sampai_tanggal').value;
        window.location.href="<?= site_url() ?>"+`/despatchinvoice?daritanggal=${id_cat}&sampaitanggal=${id_cat2}`;
    }
    function reset(){
        window.location.href="<?= site_url() ?>"+`/despatchinvoice`;
    }
</script>

<?= $this->endSection() ?>