<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>

<?php
$start = $_GET['start'] ?? $selectedParams['start_date'];
$end = $_GET['end'] ?? $selectedParams['end_date'];
$selected_date = '';
if ($start && $end) {
    $start_date = new DateTime($start);
    $end_date = new DateTime($end);
    $fsdate = $start_date->format('d/m/Y');
    $fedate = $end_date->format('d/m/Y');
    $selected_date = "$fsdate - $fedate";
}
$ap_type = array();
$ap_type['2100110201'] = 'Trade Payable';
$ap_type['2100110201'] = 'Trade Payable';
$ap_type['2200010601'] = 'Bank/Leasing Payable';
$ap_type['2200110001'] = 'Non Trade Payable';
?>

<style>
    td:nth-child(n + 2) {
        text-align: right;
    }

    th:nth-child(n + 2) {
        text-align: right;
    }
</style>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Sales & Production</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance/salesandproduction") ?>">Finance</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/salesandproduction") ?>">Sales & Production</a></li>
            </ol>
        </nav>
    </div>
    <!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Price & Quantity</div>
                    <br>
                    <div class="row mx-3">
                        <div class="col-sm-4">
                            <div class="card">
                                <input type="text" name="dateRange" id="dateRangeCashflow" value="<?= $selected_date ?>" class="form-control" placeholder="Input date range here">
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr class="text-light text-center" style="font-weight: bold; background-color:dodgerblue">
                                    <th rowspan="2">Type</th>
                                    <th rowspan="2">Point of Sales</th>
                                    <th colspan="2">Price (Rp/MT)</th>
                                    <th colspan="2">Quantity (MT)</th>
                                    <th colspan="2">Revenue (Rp Jt)</th>
                                </tr>
                                <tr class="text-light" style="font-weight: bold; background-color:dodgerblue; text-align: right;">
                                    <th><span>RKAP</span></th>
                                    <th><span>Actual</span></th>
                                    <th><span>RKAP</span></th>
                                    <th><span>Actual</span></th>
                                    <th><span>RKAP</span></th>
                                    <th><span>Actual</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="font-weight: bold;background-color:aquamarine">
                                    <th>Local</th>
                                    <th>FOBB</th>
                                    <th><?= number_format($fobb_price_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($fobb_price_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($fobb_quantity_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($fobb_quantity_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($fobb_revenue_rkap ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($fobb_revenue_actual ?? 0, 2, ',', '.') ?></th>
                                </tr>
                                <tr>
                                    <th>Local</th>
                                    <th>CIF</th>
                                    <th><?= number_format($cif_price_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($cif_price_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($cif_quantity_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($cif_quantity_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($cif_revenue_rkap ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($cif_revenue_actual ?? 0, 2, ',', '.') ?></th>
                                </tr>
                                <tr style="font-weight: bold;background-color:aquamarine">
                                    <th>Local</th>
                                    <th>Franco Pabrik</th>
                                    <th><?= number_format($pabrik_price_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($pabrik_price_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($pabrik_quantity_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($pabrik_quantity_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($pabrik_revenue_rkap ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($pabrik_revenue_actual ?? 0, 2, ',', '.') ?></th>
                                </tr>
                                <tr>
                                    <th>Export</th>
                                    <th>FOBB</th>
                                    <th><?= number_format($export_fobb_price_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fobb_price_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fobb_quantity_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fobb_quantity_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fobb_revenue_rkap ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fobb_revenue_actual ?? 0, 2, ',', '.') ?></th>
                                </tr>
                                <tr style="font-weight: bold;background-color:aquamarine">
                                    <th>Export</th>
                                    <th>CIF</th>
                                    <th><?= number_format($export_cif_price_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_cif_price_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_cif_quantity_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_cif_quantity_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_cif_revenue_rkap ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_cif_revenue_actual ?? 0, 2, ',', '.') ?></th>
                                </tr>
                                <tr>
                                    <th>Export</th>
                                    <th>FAS</th>
                                    <th><?= number_format($export_fas_price_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fas_price_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fas_quantity_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fas_quantity_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fas_revenue_rkap ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_fas_revenue_actual ?? 0, 2, ',', '.') ?></th>
                                </tr>
                                <tr style="font-weight: bold;background-color:aquamarine">
                                    <th>Export</th>
                                    <th>MV</th>
                                    <th><?= number_format($export_mv_price_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_mv_price_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_mv_quantity_rkap['RKAP'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_mv_quantity_actual['ACTUAL'] ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_mv_revenue_rkap ?? 0, 2, ',', '.') ?></th>
                                    <th><?= number_format($export_mv_revenue_actual ?? 0, 2, ',', '.') ?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Sales Cost</div>
                        <div class="card-body table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Buyer</th>
                                        <th>Contract No.</th>
                                        <th>Shipment ID</th>
                                        <th>No Invoice</th>
                                        <th>Contract Price</th>
                                        <th>Actual Price</th>
                                        <th>Variance Price</th>
                                        <th>Dispatch (Price Variance)</th>
                                        <th>Demurage (Price Variance)</th>
                                        <th>Total Net Price</th>
                                        <th>Parameters</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($sales as $row) : ?>
                                        <tr>
                                            <td><?= $row['KUNNR'] ?> - <?= $row['BUYER'] ?></td>
                                            <td><?= $row['CONTRACT_NO'] ?></td>
                                            <td>
                                                <?php
                                                for ($i = 1; $i < 11; $i++) { ?>
                                                    <p><?= $row["SHIPMENT_ID$i"] ?? '' ?></p>
                                                <?php } ?>
                                            </td>
                                            <td><?= $row['XBLNR'] ?></td>
                                            <td><?= $row['CONTRACT_PRICE'] ?></td>
                                            <td>
                                                <?php
                                                for ($i = 1; $i < 11; $i++) { ?>
                                                    <p><?= $row["FNL_QTY$i"] ?? '' ?></p>
                                                <?php } ?>
                                            </td>
                                            <td><?php
                                                for ($i = 1; $i < 11; $i++) { ?>
                                                    <p><?= $row["VAR_PRICE$i"] ?? '' ?></p>
                                                <?php } ?>
                                            </td>
                                            <td><?= $row['DESPATCH'] ?></td>
                                            <td><?= $row['DEMURAGE'] ?></td>
                                            <td>
                                                <?php
                                                for ($i = 1; $i < 11; $i++) { ?>
                                                    <p><?= $row["TOTAL_NET_PRICE$i"] ?? '' ?></p>
                                                <?php } ?>
                                            </td>
                                            <td><a href="<?= site_url("finance/salesCOA?id=" . $row['id']) ?>" class="bi bi-search"></a></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        </div>
        </div>

        </div>
        </div>
    </section>
</main>

<script>
    $(document).ready(function() {
        $('#dateRangeCashflow').daterangepicker({
            showDropdowns: true,
            autoUpdateInput: false,
            minMonth: new Date().getMonth(),
            locale: {
                format: 'DD/MM/YYYY'
            },
            startDate: moment('<?= $start ?>'),
            endDate: moment('<?= $end ?>'),
        });
        $('#dateRangeCashflow').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            let url = updateURLParameter(window.location.href, 'start', picker.startDate.format('YYYY-MM-DD'));
            url = updateURLParameter(url, 'end', picker.endDate.format('YYYY-MM-DD'));
            window.location.href = url;
        });

        $('#dateRangeCashflow').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

    function updateURLParameter(url, param, paramVal) {
        var newAdditionalURL = "";
        var tempArray = url.split("?");
        var baseURL = tempArray[0];
        var additionalURL = tempArray[1];
        var temp = "";
        if (additionalURL) {
            tempArray = additionalURL.split("&");
            for (var i = 0; i < tempArray.length; i++) {
                if (tempArray[i].split('=')[0] != param) {
                    newAdditionalURL += temp + tempArray[i];
                    temp = "&";
                }
            }
        }

        var rows_txt = temp + "" + param + "=" + paramVal;
        return baseURL + "?" + newAdditionalURL + rows_txt;
    }

    const fetchAccountPayable = function() {
        const type = document.getElementById("accountPayableFilter");
        const date = document.getElementById('asOfDate');
        if (type.value == '' || date.value == '') {
            return alert('Silahkan pilih tipe/tanggal terlebih dahulu');
        }
        let url = updateURLParameter(window.location.href, 'account_type', type.value);
        url = updateURLParameter(url, 'as_of_date', date.value);
        window.location.href = url;
    }

    const fetchAccountReceivable = function() {
        const type = document.getElementById("accountReceivableFilter");
        const date = document.getElementById('asOfDateReceivable');
        if (type.value == '' || date.value == '') {
            return alert('Silahkan pilih tipe/tanggal terlebih dahulu');
        }
        let url = updateURLParameter(window.location.href, 'receive_type', type.value);
        url = updateURLParameter(url, 'as_date_rec', date.value);
        window.location.href = url;
    }

    const fetchData = function() {
        const month = document.getElementById("monthFilter");
        const year = document.getElementById("yearFilter");
        const type = document.getElementById("indicatorFilter");
        if (type.value == '') {
            alert('Silahkan pilih tipe terlebih dahulu');
            return;
        }
        if (month.value != '' && year.value != '' && type.value != '') {
            let url = updateURLParameter(window.location.href, 'month', month.value);
            url = updateURLParameter(url, 'year', year.value);
            url = updateURLParameter(url, 'indicator', type.value);
            window.location.href = url;
        }
    }
</script>

<?= $this->endSection() ?>