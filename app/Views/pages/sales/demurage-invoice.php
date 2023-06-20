<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Demurage Invoice</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("sales/invoice") ?>">Invoice</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("demurage-invoice") ?>">Demurage Invoice</a></li>
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
                        <h5 class="card-title">Demurage Invoice</h5>
                        <!-- notification -->
                        <?php


                        if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>
                        <div class="row">
                            <div class="col-6 text-xs">
                                <form name="frmSearch" method="GET" action="<?= site_url("demurage-invoice/") ?>">
                                    <table>
                                        <tr>
                                            <td>
                                                <input type="date" id="from_date" name="from_date" class="form-control p-1 rounded-sm shadow-sm" placeholder="dari_tanggal">
                                            </td>
                                            <td class="px-3">S/D</td>
                                            <td>
                                                <input onchange="getDate()" type="date" id="to_date" name="to_date" class="form-control p-1 rounded-sm shadow-sm" placeholder="sampai_tanggal">
                                            </td>
                                            <!-- <td>
                                            <button  style="margin-left:10px;" type="submit" class="btn btn-info rounded-circle text-sm">
                                                <i class="ri-search-eye-line"></i>
                                            </button>
                                            </td> -->
                                        </tr>
                                    </table>

                                </form>
                            </div>
                        </div>

                        <!-- add data -->
                        <div class="d-flex right-section" style="margin-top: 20px; ">
                            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                <i class="bi bi-plus me-1"></i> New Invoice
                            </button>
                        </div>
                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                            <form action="<?= site_url("demurage-invoice/add") ?>" method="POST" novalidate enctype="multipart/form-data" class="needs-validation" novalidate>

                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"> Add New Invoice </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">


                                            <div class="container">
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Shipment ID</label>
                                                        <select onchange="getContract()" id="shipmentID" name="shipmentID" class="form-select" required>
                                                            <option value="" selected disabled>-- Select Shipment ID --</option>
                                                            <?php
                                                            foreach ($SalesShipBuilder as $row) : ?>
                                                                <option value="<?= $row['shipment_id'] ?>">
                                                                    <?= $row['shipment_id'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Invoice Date</label>
                                                        <input name="invoice_date" type="date" id="invoice_date" class="form-control" placeholder="Invoice Date ..." aria-label="" required></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <input hidden name="contractN" id="contractN" class="form-control" placeholder="Contract No. ..." aria-label="" required></input>
                                                        <label class="form-label">Contract No.</label>
                                                        <input name="contractN2" id="contractN2" class="form-control" placeholder="Contract No. ..." aria-label="" disabled></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Posting Date</label>
                                                        <input name="posting_date" type="date" id="posting_date" class="form-control" placeholder="Posting Date ..." aria-label="" required></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Vendor</label>
                                                        <select id="vendor" name="vendor" class="form-select" required>
                                                            <option value="" selected disabled>-- Select Vendor --</option>
                                                            <?php
                                                            foreach ($Vendor as $row) : ?>
                                                                <option value="<?= $row['LIFNR'] ?>"><?= $row['LIFNR'] ?> - <?= $row['NAME1'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Currency</label>
                                                        <select id="currency" name="currency" class="form-select" required>
                                                            <option value="" selected disabled>-- Select Currency --</option>
                                                            <?php
                                                            foreach ($Currency as $row) : ?>
                                                                <option value="<?= $row['TCURR'] ?> "><?= $row['TCURR'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Baseline Date</label>
                                                        <input name="baseline_date" type="date" id="baseline_date" class="form-control" placeholder="Baseline Date ..." aria-label="" required></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Exchange Rate</label>
                                                        <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" placeholder="Exchange Rate ..." aria-label=""></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Payt Term</label>
                                                        <select id="payterm" name="payterm" class="form-select" required>
                                                            <option value="" selected disabled>-- Select Payt Term --</option>
                                                            <?php
                                                            foreach ($Payterms as $row) : ?>
                                                                <option value="<?= $row['ZTERM'] ?>"><?= $row['ZTERM'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Attachment</label>

                                                        <input type="file" accept="application/pdf" name="att" id="att" class="form-control" placeholder="Attachment ..." aria-label=""></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Reference</label>
                                                        <input name="reference" id="reference" class="form-control" placeholder="Reference ..." aria-label="" required></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Text</label>
                                                        <div class="col-sm-12">
                                                            <textarea name="text" id="text" class="form-control" placeholder="Text ..." aria-label="" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Demurage</label>
                                                        <input name="demurage" id="demurage" class="form-control" placeholder="Demurage ..." aria-label=""></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">WBS Element</label>
                                                        <select id="WBS_element" name="WBS_element" class="form-select" required>
                                                            <option value="" selected disabled>-- Select WBS Element --</option>
                                                            <?php
                                                            foreach ($PBUKR as $row) : ?>
                                                                <option value="<?= $row['POSID'] ?>"><?= $row['POSID'] ?> - <?= $row['POST1'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Order</label>
                                                        <select id="order" name="order" class="form-select" required>
                                                            <option value="" selected disabled>-- Select Order --</option>
                                                            <?php
                                                            foreach ($Order as $row) : ?>
                                                                <option value="<?= $row['AUFNR'] ?>"><?= $row['AUFNR'] ?> - <?= $row['KTEXT'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Profit Center</label>
                                                        <select id="profit_center" name="profit_center" class="form-select" required>
                                                            <option value="" selected disabled>-- Select Profit Center --</option>
                                                            <?php
                                                            foreach ($ProfitCenter as $row) : ?>
                                                                <option value="<?= $row['PRCTR'] ?>"><?= $row['PRCTR'] ?> - <?= $row['LTEXT'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <input hidden disabled name="Doc_No" id="Doc_No" class="form-control" placeholder="Doc_No ..." aria-label="" required></input>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success" type="submit">Save Data</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Edit Data -->
                        <div class="modal fade" id="editForm" tabindex="-1">
                            <form action="<?= site_url("demurage-invoice/update") ?>" method="POST" lass="needs-validation" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"> Edit Demurage Invoice </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="container">
                                                <input hidden id="ID" name="ID">
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Shipment ID</label>
                                                        <select onchange="getContractup()" id="shipmentIDup" name="shipmentIDup" class="form-select" required>
                                                            <option value="" selected disabled disabled>Select Shipment ID</option>
                                                            <?php
                                                            foreach ($SalesShipBuilder as $row) : ?>
                                                                <option value="<?= $row['shipment_id'] ?>">
                                                                    <?= $row['shipment_id'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Invoice Date</label>
                                                        <input name="invoice_dateup" type="date" id="invoice_dateup" class="form-control" placeholder="Invoice Date ..." aria-label="" required></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <input hidden name="contractNup" id="contractNup" class="form-control" placeholder="Contract No. ..." aria-label="" required></input>
                                                        <label class="form-label">Contract No.</label>
                                                        <input name="contractN2up" id="contractN2up" class="form-control" placeholder="Contract No. ..." aria-label="" disabled></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Posting Date</label>
                                                        <input name="posting_dateup" type="date" id="posting_dateup" class="form-control" placeholder="Posting Date ..." aria-label="" required></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Vendor</label>
                                                        <select id="vendorup" name="vendorup" class="form-select" required>
                                                            <option value="" selected disabled>Select Vendor</option>
                                                            <?php
                                                            foreach ($Vendor as $row) : ?>
                                                                <option value="<?= $row['LIFNR'] ?>"><?= $row['LIFNR'] ?> - <?= $row['NAME1'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Currency</label>
                                                        <select id="currencyup" name="currencyup" class="form-select" required>
                                                            <option value="" selected disabled>Select Currency</option>
                                                            <?php
                                                            foreach ($Currency as $row) : ?>
                                                                <option value="<?= $row['TCURR'] ?>"><?= $row['TCURR'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Baseline Date</label>
                                                        <input name="baseline_dateup" type="date" id="baseline_dateup" class="form-control" placeholder="Baseline Date ..." aria-label="" required></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Exchange Rate</label>
                                                        <input type="number" name="exchange_rateup" id="exchange_rateup" class="form-control" placeholder="Exchange Rate ..." aria-label=""></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Payt Term</label>
                                                        <select id="paytermup" name="paytermup" class="form-select" required>
                                                            <option value="" selected disabled>Select Payt Term</option>
                                                            <?php
                                                            foreach ($Payterms as $row) : ?>
                                                                <option value="<?= $row['ZTERM'] ?>"><?= $row['ZTERM'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Attachment</label>

                                                        <input type="file" accept="application/pdf" name="attup" id="attup" class="form-control" placeholder="Attachment ..." aria-label=""></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Reference</label>
                                                        <input name="referenceup" id="referenceup" class="form-control" placeholder="Reference ..." aria-label="" required></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Text</label>
                                                        <div class="col-sm-12">
                                                            <textarea name="textup" id="textup" class="form-control" placeholder="Text ..." aria-label="" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Demurage</label>
                                                        <input name="demurageup" id="demurageup" class="form-control" placeholder="Demurage ..." aria-label=""></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">WBS Element</label>
                                                        <select id="WBS_elementup" name="WBS_elementup" class="form-select" required>
                                                            <option value="" selected disabled>Select WBS Element</option>
                                                            <?php
                                                            foreach ($PBUKR as $row) : ?>
                                                                <option value="<?= $row['POSID'] ?>"><?= $row['POSID'] ?> - <?= $row['POST1'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Order</label>
                                                        <select id="orderup" name="orderup" class="form-select" required>
                                                            <option value="" selected disabled>Select Order</option>
                                                            <?php
                                                            foreach ($Order as $row) : ?>
                                                                <option value="<?= $row['AUFNR'] ?>"><?= $row['AUFNR'] ?> - <?= $row['KTEXT'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Profit Center</label>
                                                        <select id="profit_centerup" name="profit_centerup" class="form-select" required>
                                                            <option value="" selected disabled>Select Profit Center</option>
                                                            <?php
                                                            foreach ($ProfitCenter as $row) : ?>
                                                                <option value="<?= $row['PRCTR'] ?>"><?= $row['PRCTR'] ?> - <?= $row['LTEXT'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <input hidden disabled name="Doc_Noup" id="Doc_Noup" class="form-control" placeholder="Doc_No ..." aria-label="" required></input>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
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
                                            <h5 class="modal-title"> View Demurage Invoice </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="container">
                                                <input hidden id="IDview" name="IDview">
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Shipment ID</label>
                                                        <select id="shipmentIDview" name="shipmentIDview" class="form-select" disabled>
                                                            <option value="" selected disabled>Select Shipment ID</option>
                                                            <?php
                                                            foreach ($SalesShipBuilder as $row) : ?>
                                                                <option value="<?= $row['shipment_id'] ?>">
                                                                    <?= $row['shipment_id'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Invoice Date</label>
                                                        <input name="invoice_dateview" type="date" id="invoice_dateview" class="form-control" placeholder="Invoice Date ..." aria-label="" disabled></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <input hidden name="contractNview" id="contractNview" class="form-control" placeholder="Contract No. ..." aria-label="" disabled></input>
                                                        <label class="form-label">Contract No.</label>
                                                        <input name="contractN2view" id="contractN2view" class="form-control" placeholder="Contract No. ..." aria-label="" disabled></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Posting Date</label>
                                                        <input name="posting_dateview" type="date" id="posting_dateview" class="form-control" placeholder="Posting Date ..." aria-label="" disabled></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Vendor</label>
                                                        <select id="vendorview" name="vendorview" class="form-select" disabled>
                                                            <option selected>Select Vendor</option>
                                                            <?php
                                                            foreach ($Vendor as $row) : ?>
                                                                <option value="<?= $row['LIFNR'] ?>"><?= $row['LIFNR'] ?> - <?= $row['NAME1'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Currency</label>
                                                        <select id="currencyview" name="currencyview" class="form-select" disabled>
                                                            <option selected>Select Currency</option>
                                                            <?php
                                                            foreach ($Currency as $row) : ?>
                                                                <option value="<?= $row['TCURR'] ?>"><?= $row['TCURR'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Baseline Date</label>
                                                        <input name="baseline_dateview" type="date" id="baseline_dateview" class="form-control" placeholder="Baseline Date ..." aria-label="" disabled></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Exchange Rate</label>
                                                        <input type="number" name="exchange_rateview" id="exchange_rateview" class="form-control" placeholder="Exchange Rate ..." aria-label="" disabled></input>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Payt Term</label>
                                                        <select id="paytermview" name="paytermview" class="form-select" disabled>
                                                            <option selected>Select Payt Term</option>
                                                            <?php
                                                            foreach ($Payterms as $row) : ?>
                                                                <option value="<?= $row['ZTERM'] ?>"><?= $row['ZTERM'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>

                                                    <div class="col">
                                                        <!-- <input  id="IDview" name="ID"></input> -->
                                                        <label class="form-label">Attachment</label><br>
                                                        <a href="javascript:download_pdf()" class="btn btn-sm btn-warning rounded">Download <i class="ri-download-line"></i></a>
                                                    </div>

                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Reference</label>
                                                        <input name="referenceview" id="referenceview" class="form-control" placeholder="Reference ..." aria-label="" disabled></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Text</label>
                                                        <div class="col-sm-12">
                                                            <textarea name="textview" id="textview" class="form-control" placeholder="Text ..." aria-label="" disabled></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Demurage</label>
                                                        <input name="demurageview" id="demurageview" class="form-control" placeholder="Demurage ..." aria-label="" disabled></input>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">WBS Element</label>
                                                        <select id="WBS_elementview" name="WBS_elementview" class="form-select" disabled>
                                                            <option selected>Select WBS Element</option>
                                                            <?php
                                                            foreach ($PBUKR as $row) : ?>
                                                                <option value="<?= $row['POSID'] ?>"><?= $row['POSID'] ?> - <?= $row['POST1'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-bottom:10px;">
                                                    <div class="col">
                                                        <label class="form-label">Order</label>
                                                        <select id="orderview" name="orderview" class="form-select" disabled>
                                                            <option selected>Select Order</option>
                                                            <?php
                                                            foreach ($Order as $row) : ?>
                                                                <option value="<?= $row['AUFNR'] ?>"><?= $row['AUFNR'] ?> - <?= $row['KTEXT'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Profit Center</label>
                                                        <select id="profit_centerview" name="profit_centerview" class="form-select" disabled>
                                                            <option selected>Select Profit Center</option>
                                                            <?php
                                                            foreach ($ProfitCenter as $row) : ?>
                                                                <option value="<?= $row['PRCTR'] ?>"><?= $row['PRCTR'] ?> - <?= $row['LTEXT'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                    <input hidden disabled name="Doc_Noview" id="Doc_Noview" class="form-control" placeholder="Doc_No ..." aria-label="" disabled></input>
                                                </div>
                                            </div>
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
                                        <th scope="col">Vendor</th>
                                        <th scope="col">Reference</th>
                                        <th scope="col">Doc. No. SAP</th>
                                        <th scope="col">Doc. No. Reversal SAP</th>
                                        <th scope="col">Created By</th>
                                        <th scope="col">Created On</th>
                                        <!-- <th scope="col">Download</th> -->
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $no = 1;

                                    foreach ($MDMasterBuild as $row) : ?>
                                        <tr>
                                            <!-- <td><?= $no++ ?>.</td> -->

                                            <td><?= $row['SHIPMENT_ID'] ?></td>
                                            <td><?= $row['CONTRCT_NO'] ?></td>
                                            <td><?= $row['LIFNR'] ?></td>
                                            <td><?= $row['XBLNR'] ?></td>
                                            <!-- <td><?//= $row['BELNR'] ?></td> -->
                                            <td>
                                                <?php
                                                    if ($row['STATUS_SAP'] == "E")
                                                        echo $row['MESSAGE_SAP'];
                                                    else
                                                        echo $row['BELNR'];         
                                                ?>
                                            </td>
                                            <td><?= $row['STBLG'] ?></td>
                                            <td><?= $row['CREATED_BY'] ?></td>
                                            <td><?php
                                                $newUpdate = date("d-m-Y", strtotime($row['created_on']));
                                                echo $newUpdate;
                                                ?>
                                            </td>
                                            <!-- <td><a class="btn btn-sm btn-warning rounded"
                                                href="<?= base_url(); ?>/demurage-invoice/download/<?= $row['ID'] ?>"><i
                                                    class="ri-download-line"></i></a></td> -->
                                            <td class="col">
                                                <?php
                                                if ($row['BELNR'] == NULL) { ?>
                                                    <a style="margin-left: 3px; margin-top: 5px;" onclick="editForm('<?= $row['ID'] ?>')" class="btn btn-sm btn-primary rounded-circle text-sm">
                                                        <i class="my-1 py-1 ri-pencil-fill text-sm" style="font-size : 12px;"></i></a>
                                                    <a style="margin-left: 3px; margin-top: 5px;" onclick="return confirm('Are you sure?')" href="<?= site_url('demurage-invoice/delete/') ?><?= $row['ID'] ?>" class="btn btn-sm btn-danger rounded-circle text-sm"> <i class="my-1 py-1 ri-delete-bin-line text-sm" style="font-size : 12px;"></i></a>
                                                <?php } ?>

                                                <a style="margin-left: 3px; margin-top: 5px;" onclick="viewForm('<?= $row['ID'] ?>')" class="btn btn-sm btn-warning rounded-circle text-sm"> <i class="my-1 py-1 ri-search-line text-sm" style="font-size : 12px;"></i></a>
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
    function getDate() {
        var from = document.getElementById("from_date").value;
        var to = document.getElementById("to_date").value;
        location.href = `<?= site_url("/demurage-invoice"); ?>?from_date=${from}&to_date=${to}`;
        //template literals
    }

    function getContract(x) {
        var x = document.getElementById("shipmentID").value;
        //document.getElementById("contract_no").value = x;
        // console.log(x);
        fetch("<?= site_url('/demurage-invoice/get/') ?>" + x)
            .then(res => res.json())
            .then(res => {
                //console.log(x);   
                $('#contractN').val(res.SalesShip[0].contract_no);
                $('#contractN2').val(res.SalesShip[0].contract_no);
                $('#demurage').val(res.SalesLay.length > 0 ? res.SalesLay[0].value_demmurage : 0); //harus True False or True True False OR TRUE TRUE FALSE ++
            });
        // fetch("<?= site_url('demurage-invoice/get/') ?>" + id)
        // .then(response => response.json())
        // .catch(err => console.log(err))
    }

    function getContractup(x) {
        var x = document.getElementById("shipmentIDup").value;
        //document.getElementById("contract_no").value = x;
        // console.log(x);
        fetch("<?= site_url('/demurage-invoice/get/') ?>" + x)
            .then(res => res.json())
            .then(res => {
                //console.log(x);   
                $('#contractNup').val(res.SalesShip[0].contract_no);
                $('#contractN2up').val(res.SalesShip[0].contract_no);
                $('#demurageup').val(res.SalesLay.length > 0 ? res.SalesLay[0].value_demmurage : 0); //harus True False or True True False OR TRUE TRUE FALSE ++
            });
        // fetch("<?= site_url('demurage-invoice/get/') ?>" + id)
        // .then(response => response.json())
        // .catch(err => console.log(err))
    }

    function editForm(id) {
        // alert("hai")
        fetch("<?= site_url('/demurage-invoice/getEdit/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#shipmentIDup').val(res.SHIPMENT_ID);
                $('#contractNup').val(res.CONTRCT_NO);
                $('#contractN2up').val(res.CONTRCT_NO);
                $('#vendorup').val(res.LIFNR);
                $('#invoice_dateup').val(res.BLDAT);
                $('#posting_dateup').val(res.BUDAT);
                $('#baseline_dateup').val(res.ZFBDT);
                $('#paytermup').val(res.ZTERM);
                $('#test_attch').text(res.ATTCH);
                $('#test').attr('href', res.ATTCH);
                // $('#actualPriceup').val(res.ACT_PRICE+'.00');
                // $('#actualqualityup').val(res.ACT_QTY+'.00');
                $('#referenceup').val(res.XBLNR);
                $('#exchange_rateup').val(res.KURSF);
                $('#currencyup').val(res.ECURR);
                $('#demurageup').val(res.DEMURAGE);
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

    function viewForm(id) {
        fetch("<?= site_url('/demurage-invoice/getEdit/') ?>" + id)
            .then(res => res.json())
            .then(res => {
                console.log(res);
                $('#shipmentIDview').val(res.SHIPMENT_ID);
                $('#contractNview').val(res.CONTRCT_NO);
                $('#contractN2view').val(res.CONTRCT_NO);
                $('#vendorview').val(res.LIFNR);
                $('#invoice_dateview').val(res.BLDAT);
                $('#posting_dateview').val(res.BUDAT);
                $('#baseline_dateview').val(res.ZFBDT);
                $('#paytermview').val(res.ZTERM);
                // $('#test_attch').text(res.ATTCH);
                // $('#test_attch2').text(res.ATTCH);
                // $('#test2').attr('href', res.ATTCH);

                $('#referenceview').val(res.XBLNR);
                $('#currencyview').val(res.ECURR);
                $('#exchange_rateview').val(res.KURSF);
                $('#demurageview').val(res.DEMURAGE);
                $('#WBS_elementview').val(res.PROJK);
                $('#profit_centerview').val(res.PRCTR);
                $('#orderview').val(res.AUFNR);
                $('#textview').val(res.SGTXT);
                $('#Doc_Noview').val(res.DOC_NO);
                $('#IDview').val(res.ID);
                var scrt_var = res.ID;

                var pdf = res.ATTCH;
                if (pdf == null) {
                    download_pdf = function() {
                        alert("File Not Available, Please upload the file");
                    }
                } else {
                    download_pdf = function() {
                        location.href = "<?= site_url("/demurage-invoice/download/"); ?>" + scrt_var;
                    }
                }
            })
            .then(() => {
                $('#viewForm').modal('show');
            });
    }
</script>

<?= $this->endSection() ?>