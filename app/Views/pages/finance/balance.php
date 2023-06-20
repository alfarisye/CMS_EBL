<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>

<?php
$ap_type = array();
$ap_type['2100110201'] = 'Trade Payable';
$ap_type['2100110201'] = 'Trade Payable';
$ap_type['2200010601'] = 'Bank/Leasing Payable';
$ap_type['2200110001'] = 'Non Trade Payable';
$parameter = $_GET['indicator'] ?? '2100110201';

$bulan = [
    1 => "Januari",
    2 => "Februari",
    3 => "Maret",
    4 => "April",
    5 => "Mei",
    6 => "Juni",
    7 => "Juli",
    8 => "Agustus",
    9 => "September",
    10 => "Oktober",
    11 => "November",
    12 => "Desember"
];
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
        <h1>Balance Sheet Report</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance") ?>">Finance</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/cashflow") ?>">Balance Sheet Report</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <select onchange="fetchData()" class="form-control" name="" id="yearFilter">
                                <?php foreach ($years as $year) : ?>
                                    <option value="<?= $year['FISC'] ?>" <?= $year['FISC'] == $selectedParams['year'] ? 'selected' : null ?>><?= $year['FISC'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" style="display: none;">
                        <div class="card">
                            <select onchange="fetchData()" class="form-control" name="" id="monthFilter">
                                <option value="16">All</option>
                                <option value="1" <?= 1 == $selectedParams['month'] ? 'selected' : null ?>>January</option>
                                <option value="2" <?= 2 == $selectedParams['month'] ? 'selected' : null ?>>February</option>
                                <option value="3" <?= 3 == $selectedParams['month'] ? 'selected' : null ?>>March</option>
                                <option value="4" <?= 4 == $selectedParams['month'] ? 'selected' : null ?>>April</option>
                                <option value="5" <?= 5 == $selectedParams['month'] ? 'selected' : null ?>>May</option>
                                <option value="6" <?= 6 == $selectedParams['month'] ? 'selected' : null ?>>June</option>
                                <option value="7" <?= 7 == $selectedParams['month'] ? 'selected' : null ?>>July</option>
                                <option value="8" <?= 8 == $selectedParams['month'] ? 'selected' : null ?>>August</option>
                                <option value="9" <?= 9 == $selectedParams['month'] ? 'selected' : null ?>>September</option>
                                <option value="10" <?= 10 == $selectedParams['month'] ? 'selected' : null ?>>October</option>
                                <option value="11" <?= 11 == $selectedParams['month'] ? 'selected' : null ?>>November</option>
                                <option value="12" <?= 12 == $selectedParams['month'] ? 'selected' : null ?>>December</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reports</h5>
                        <div class="row">
                            <!-- tables -->
                            <div class="col-md-12" id="targetBalanceSheet">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title text-center">Balance Sheet</h5>
                                        <p class="card-subtitle text-center">(In Million Rupiah)</p>
                                        <br>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>DESCRIPTION</th>
                                                        <!-- <th><?= $selectedParams['month'] == $todayDate['month'] && $selectedParams['year'] == $todayDate['year'] ? "MTD" :  $selectedParams['month'] . "/" . $selectedParams['year'] ?></th> -->
                                                        <th><?= $selectedParams['year'] == $todayDate['year'] ? "YTD" :  $selectedParams['year'] ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Current Asset</td>
                                                        <!-- <td><?= number_format($current_asset_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($current_asset_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Non Current Asset</td>
                                                        <!-- <td><?= number_format($non_current_asset_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($non_current_asset_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td>Total Asset</td>
                                                        <!-- <td><?= number_format($total_asset_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($total_asset_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Current Liabilities</td>
                                                        <!-- <td><?= number_format($current_liabilities_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($current_liabilities_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Non Current Liabilities</td>
                                                        <!-- <td><?= number_format($non_current_liabilities_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($non_current_liabilities_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr style="font-weight: bold;">
                                                        <td>Total Liabilities</td>
                                                        <!-- <td><?= number_format($total_liabilities_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($total_liabilities_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Capital Stock</td>
                                                        <!-- <td><?= number_format($capital_stock_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($capital_stock_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <!-- <tr>
                                                    <td>Additional Paid-in Capital</td>
                                                    <td><?= number_format($additional_capital_stock_mtd ?? 0, 2, ',', '.') ?></td>
                                                    <td><?= number_format($additional_capital_stock_ytd ?? 0, 2, ',', '.') ?></td>
                                                </tr> -->
                                                    <tr>
                                                        <td>Other Comprehensive Income</td>
                                                        <!-- <td><?= number_format($oci_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($oci_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Retained Earning</td>
                                                        <!-- <td><?= number_format($retained_earning_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($retained_earning_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr class="dont-show">
                                                        <td>Stockholder's Equity</td>
                                                        <!-- <td><?= number_format($stockholder_equity_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($stockholder_equity_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr class="dont-show">
                                                        <td>Non Controlling Interest</td>
                                                        <!-- <td><?= number_format($nc_interest_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($nc_interest_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr style="font-weight: bold;">
                                                        <td>Total Equity</td>
                                                        <!-- <td><?= number_format($total_equity_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($total_equity_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                    <tr style="font-weight: bold;">
                                                        <td>Total Liabilities & Equity</td>
                                                        <!-- <td><?= number_format($total_liaequ_mtd ?? 0, 2, ',', '.') ?></td> -->
                                                        <td><?= number_format(($total_liaequ_ytd ?? 0) / 1000000, 2, ',', '.') ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- liquidity ratio -->
                            <div class="col-md-12">
                                <h4 class="card-subtitle">Liquidity Ratio</h4 class="card-subtitle">
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title">
                                                    Current Ratio
                                                </div>
                                                <div id="currentRatioChart"></div>
                                                <script>
                                                    let crChart;
                                                    document.addEventListener("DOMContentLoaded", () => {
                                                        crChart = new ApexCharts(document.querySelector("#currentRatioChart"), {
                                                            series: [{
                                                                name: "Current Ratio",
                                                                data: [
                                                                    <?php foreach (range(1, 12) as $i) : ?> {
                                                                            x: "<?= $bulan[$i] ?>",
                                                                            y: <?= $current_ratio_ytd[$i]['ratio'] ?? 0 ?>
                                                                        },
                                                                    <?php endforeach ?>
                                                                ]
                                                            }, ],
                                                            chart: {
                                                                height: 350,
                                                                type: "area",
                                                                toolbar: {
                                                                    show: false,
                                                                },
                                                            },
                                                            markers: {
                                                                size: 4,
                                                            },
                                                            colors: ["#4154f1", "#2eca6a", "#ff771d"],
                                                            fill: {
                                                                type: "gradient",
                                                                gradient: {
                                                                    shadeIntensity: 1,
                                                                    opacityFrom: 0.3,
                                                                    opacityTo: 0.4,
                                                                    stops: [0, 90, 100],
                                                                },
                                                            },
                                                            dataLabels: {
                                                                enabled: false,
                                                            },
                                                            stroke: {
                                                                curve: "smooth",
                                                                width: 2,
                                                            },
                                                            xaxis: {
                                                                title: {
                                                                    text: 'Month',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                type: 'category',
                                                                tickPlacement: 'between',
                                                            },
                                                            yaxis: {
                                                                forceNiceScale: true,
                                                                title: {
                                                                    text: 'Quantity',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                labels: {
                                                                    formatter: function(value) {
                                                                        return value.toLocaleString('id-ID', {
                                                                            minimumFractionDigits: 2
                                                                        });
                                                                    }
                                                                },
                                                            },
                                                            tooltip: {
                                                                x: {
                                                                    format: "dd/MM/yy HH:mm",
                                                                },
                                                            },
                                                        });
                                                        crChart.render();
                                                    });
                                                </script>
                                            </div>
                                            <div class="card-footer table-responsive">
                                                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= $bulan[$i] ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>YTD</td>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= number_format($current_ratio_ytd[$i]['ratio'] ?? 0, 2, ',', '.') ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title">
                                                    Quick Ratio
                                                </div>
                                                <div id="quickRatioChart"></div>
                                                <script>
                                                    let qrChart;
                                                    document.addEventListener("DOMContentLoaded", () => {
                                                        qrChart = new ApexCharts(document.querySelector("#quickRatioChart"), {
                                                            series: [{
                                                                name: "Quick Ratio",
                                                                data: [
                                                                    <?php foreach (range(1, 12) as $i) : ?> {
                                                                            x: "<?= $bulan[$i] ?>",
                                                                            y: <?= $quick_ratio_ytd[$i]['ratio'] ?? 0 ?>
                                                                        },
                                                                    <?php endforeach ?>
                                                                ]
                                                            }, ],
                                                            chart: {
                                                                height: 350,
                                                                type: "area",
                                                                toolbar: {
                                                                    show: false,
                                                                },
                                                            },
                                                            markers: {
                                                                size: 4,
                                                            },
                                                            colors: ["#4154f1", "#2eca6a", "#ff771d"],
                                                            fill: {
                                                                type: "gradient",
                                                                gradient: {
                                                                    shadeIntensity: 1,
                                                                    opacityFrom: 0.3,
                                                                    opacityTo: 0.4,
                                                                    stops: [0, 90, 100],
                                                                },
                                                            },
                                                            dataLabels: {
                                                                enabled: false,
                                                            },
                                                            stroke: {
                                                                curve: "smooth",
                                                                width: 2,
                                                            },
                                                            xaxis: {
                                                                title: {
                                                                    text: 'Month',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                type: 'category',
                                                                tickPlacement: 'between',
                                                            },
                                                            yaxis: {
                                                                forceNiceScale: true,
                                                                title: {
                                                                    text: 'Quantity',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                labels: {
                                                                    formatter: function(value) {
                                                                        return value.toLocaleString('id-ID', {
                                                                            minimumFractionDigits: 2
                                                                        });
                                                                    }
                                                                },
                                                            },
                                                            tooltip: {
                                                                x: {
                                                                    format: "dd/MM/yy HH:mm",
                                                                },
                                                            },
                                                        });
                                                        qrChart.render();
                                                    });
                                                </script>
                                                <div class="card-footer table-responsive">
                                                    <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <?php foreach (range(1, 12) as $i) : ?>
                                                                    <th><?= $bulan[$i] ?></th>
                                                                <?php endforeach; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>YTD</td>
                                                                <?php foreach (range(1, 12) as $i) : ?>
                                                                    <th><?= number_format($quick_ratio_ytd[$i]['ratio'] ?? 0, 2, ',', '.') ?></th>
                                                                <?php endforeach; ?>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title">
                                                    Cash Ratio
                                                </div>
                                                <div id="cashChart"></div>
                                                <script>
                                                    let cChart;
                                                    document.addEventListener("DOMContentLoaded", () => {
                                                        cChart = new ApexCharts(document.querySelector("#cashChart"), {
                                                            series: [{
                                                                name: "Cash Ratio",
                                                                data: [
                                                                    <?php foreach (range(1, 12) as $i) : ?> {
                                                                            x: "<?= $bulan[$i] ?>",
                                                                            y: <?= $cash_ratio_ytd[$i]['ratio'] ?? 0 ?>
                                                                        },
                                                                    <?php endforeach ?>
                                                                ]
                                                            }, ],
                                                            chart: {
                                                                height: 350,
                                                                type: "area",
                                                                toolbar: {
                                                                    show: false,
                                                                },
                                                            },
                                                            markers: {
                                                                size: 4,
                                                            },
                                                            colors: ["#4154f1", "#2eca6a", "#ff771d"],
                                                            fill: {
                                                                type: "gradient",
                                                                gradient: {
                                                                    shadeIntensity: 1,
                                                                    opacityFrom: 0.3,
                                                                    opacityTo: 0.4,
                                                                    stops: [0, 90, 100],
                                                                },
                                                            },
                                                            dataLabels: {
                                                                enabled: false,
                                                            },
                                                            stroke: {
                                                                curve: "smooth",
                                                                width: 2,
                                                            },
                                                            xaxis: {
                                                                title: {
                                                                    text: 'Month',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                type: 'category',
                                                                tickPlacement: 'between',
                                                            },
                                                            yaxis: {
                                                                forceNiceScale: true,
                                                                title: {
                                                                    text: 'Quantity',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                labels: {
                                                                    formatter: function(value) {
                                                                        return value.toLocaleString('id-ID', {
                                                                            minimumFractionDigits: 2
                                                                        });
                                                                    }
                                                                },
                                                            },
                                                            tooltip: {
                                                                x: {
                                                                    format: "dd/MM/yy HH:mm",
                                                                },
                                                            },
                                                        });
                                                        cChart.render();
                                                    });
                                                </script>
                                                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= $bulan[$i] ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>YTD</td>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= number_format($cash_ratio_ytd[$i]['ratio'] ?? 0, 2, ',', '.') ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- leverage ratio -->
                            <div class="col-md-12">
                                <h4 class="card-subtitle">Leverage Ratio</h4 class="card-subtitle">
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title">
                                                    Debt Service Coverage Ratio
                                                </div>
                                                <div id="debtServiceRatioChart"></div>
                                                <script>
                                                    let debtChart;
                                                    document.addEventListener("DOMContentLoaded", () => {
                                                        debtChart = new ApexCharts(document.querySelector("#debtServiceRatioChart"), {
                                                            series: [{
                                                                name: "Debt Service Coverage Ratio",
                                                                data: [
                                                                    <?php foreach (range(1, 12) as $i) : ?> {
                                                                            x: "<?= $bulan[$i] ?>",
                                                                            y: <?= $debt_service_ytd[$i]['ratio'] ?? 0 ?>
                                                                        },
                                                                    <?php endforeach ?>
                                                                ]
                                                            }, ],
                                                            chart: {
                                                                height: 350,
                                                                type: "area",
                                                                toolbar: {
                                                                    show: false,
                                                                },
                                                            },
                                                            markers: {
                                                                size: 4,
                                                            },
                                                            colors: ["#4154f1", "#2eca6a", "#ff771d"],
                                                            fill: {
                                                                type: "gradient",
                                                                gradient: {
                                                                    shadeIntensity: 1,
                                                                    opacityFrom: 0.3,
                                                                    opacityTo: 0.4,
                                                                    stops: [0, 90, 100],
                                                                },
                                                            },
                                                            dataLabels: {
                                                                enabled: false,
                                                            },
                                                            stroke: {
                                                                curve: "smooth",
                                                                width: 2,
                                                            },
                                                            xaxis: {
                                                                title: {
                                                                    text: 'Month',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                type: 'category',
                                                                tickPlacement: 'between',
                                                            },
                                                            yaxis: {
                                                                forceNiceScale: true,
                                                                title: {
                                                                    text: 'Quantity',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                labels: {
                                                                    formatter: function(value) {
                                                                        return value.toLocaleString('id-ID', {
                                                                            minimumFractionDigits: 2
                                                                        });
                                                                    }
                                                                },
                                                            },
                                                        });
                                                        debtChart.render();
                                                    });
                                                </script>
                                                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= $bulan[$i] ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>YTD</td>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= number_format($debt_service_ytd[$i]['ratio'] ?? 0) ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title">
                                                    Debt to Equity Ratio
                                                </div>
                                                <div id="derChart"></div>
                                                <script>
                                                    let derChart;
                                                    document.addEventListener("DOMContentLoaded", () => {
                                                        derChart = new ApexCharts(document.querySelector("#derChart"), {
                                                            series: [{
                                                                name: "Debt to Equity Ratio",
                                                                data: [
                                                                    <?php foreach (range(1, 12) as $i) : ?> {
                                                                            x: "<?= $bulan[$i] ?>",
                                                                            y: <?= $der_ytd[$i]['ratio'] ?? 0 ?>
                                                                        },
                                                                    <?php endforeach ?>
                                                                ]
                                                            }, ],
                                                            chart: {
                                                                height: 350,
                                                                type: "area",
                                                                toolbar: {
                                                                    show: false,
                                                                },
                                                            },
                                                            markers: {
                                                                size: 4,
                                                            },
                                                            colors: ["#4154f1", "#2eca6a", "#ff771d"],
                                                            fill: {
                                                                type: "gradient",
                                                                gradient: {
                                                                    shadeIntensity: 1,
                                                                    opacityFrom: 0.3,
                                                                    opacityTo: 0.4,
                                                                    stops: [0, 90, 100],
                                                                },
                                                            },
                                                            dataLabels: {
                                                                enabled: false,
                                                            },
                                                            stroke: {
                                                                curve: "smooth",
                                                                width: 2,
                                                            },
                                                            xaxis: {
                                                                title: {
                                                                    text: 'Month',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                type: 'category',
                                                                tickPlacement: 'between',
                                                            },
                                                            yaxis: {
                                                                forceNiceScale: true,
                                                                title: {
                                                                    text: 'Quantity',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                labels: {
                                                                    formatter: function(value) {
                                                                        return value.toLocaleString('id-ID', {
                                                                            minimumFractionDigits: 2
                                                                        });
                                                                    }
                                                                },
                                                            },
                                                            tooltip: {
                                                                x: {
                                                                    format: "dd/MM/yy HH:mm",
                                                                },
                                                            },
                                                        });
                                                        derChart.render();
                                                    });
                                                </script>
                                                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= $bulan[$i] ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>YTD</td>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= number_format($der_ytd[$i]['ratio'] ?? 0)?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title">
                                                    Equity Ratio
                                                </div>
                                                <div id="equChart"></div>
                                                <script>
                                                    let eqChart;
                                                    document.addEventListener("DOMContentLoaded", () => {
                                                        eqChart = new ApexCharts(document.querySelector("#equChart"), {
                                                            series: [{
                                                                name: "Equity Ratio",
                                                                data: [
                                                                    <?php foreach (range(1, 12) as $i) : ?> {
                                                                            x: "<?= $bulan[$i] ?>",
                                                                            y: <?= $equ_ytd[$i]['ratio'] ?? 0 ?>
                                                                        },
                                                                    <?php endforeach ?>
                                                                ]
                                                            }, ],
                                                            chart: {
                                                                height: 350,
                                                                type: "area",
                                                                toolbar: {
                                                                    show: false,
                                                                },
                                                            },
                                                            markers: {
                                                                size: 4,
                                                            },
                                                            colors: ["#4154f1", "#2eca6a", "#ff771d"],
                                                            fill: {
                                                                type: "gradient",
                                                                gradient: {
                                                                    shadeIntensity: 1,
                                                                    opacityFrom: 0.3,
                                                                    opacityTo: 0.4,
                                                                    stops: [0, 90, 100],
                                                                },
                                                            },
                                                            dataLabels: {
                                                                enabled: false,
                                                            },
                                                            stroke: {
                                                                curve: "smooth",
                                                                width: 2,
                                                            },
                                                            xaxis: {
                                                                title: {
                                                                    text: 'Month',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                type: 'category',
                                                                tickPlacement: 'between',
                                                            },
                                                            yaxis: {
                                                                forceNiceScale: true,
                                                                title: {
                                                                    text: 'Quantity',
                                                                    style: {
                                                                        color: "#fff",
                                                                        fontSize: "1px"
                                                                    }
                                                                },
                                                                labels: {
                                                                    formatter: function(value) {
                                                                        return value.toLocaleString('id-ID', {
                                                                            minimumFractionDigits: 2
                                                                        });
                                                                    }
                                                                },
                                                            },
                                                            tooltip: {
                                                                x: {
                                                                    format: "dd/MM/yy HH:mm",
                                                                },
                                                            },
                                                        });
                                                        eqChart.render();
                                                    });
                                                </script>
                                                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= $bulan[$i] ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>YTD</td>
                                                            <?php foreach (range(1, 12) as $i) : ?>
                                                                <th><?= number_format($equ_ytd[$i]['ratio'] ?? 0) ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
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
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <select onchange="fetchData()" class="form-control" name="" id="indicatorFilter">
                                <option value="">Select type</option>
                                <option value="2100110201" <?= $parameter == '2100110201' ? 'selected' : '' ?>>Trade Payable</option>
                                <option value="2200110001" <?= $parameter == '2200110001' ? 'selected' : '' ?>>Non Trade Payable</option>
                                <option value="2200010601" <?= $parameter == '2200010601' ? 'selected' : '' ?>>Bank/Leasing Payable</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Movement Account Payable (In Million)</div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th><?= $ap_type[$type_ap] ?></th>
                                    <?php foreach ($trade_payable as $p) : ?>
                                        <th><?= $p['FISC'] ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Previous</td>
                                    <?php foreach ($trade_payable as $p) : ?>
                                        <td><?= number_format($p['previous'], 2, '.', ',') ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <td>Mov. Inc</td>
                                    <?php foreach ($trade_payable as $p) : ?>
                                        <td><?= number_format($p['increase'], 2, '.', ',') ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <td>Mov. Dec</td>
                                    <?php foreach ($trade_payable as $p) : ?>
                                        <td><?= number_format($p['decrease'], 2, '.', ',') ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr style="font-weight: bold;">
                                    <td>Outstanding</td>
                                    <?php foreach ($trade_payable as $p) : ?>
                                        <td><?= number_format($p['os_today'], 2, '.', ',') ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Movement Account Receivable Trading (In Million)</div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Account Receivable</th>
                                    <?php foreach ($account_receivable as $p) : 
                                        if($p['year']!=date("Y")-3){?>
                                        <th><?= $p['year'];?></th>
                                    <?php }endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Previous</td>
                                    <?php foreach ($account_receivable as $p) : 
                                        if($p['year']!=date("Y")){?>
                                        <td><?= number_format($p['previous'], 2, '.', ',') ?></td>
                                    <?php }endforeach;?>
                                </tr>
                                <tr>
                                    <td>Mov. Inc</td>
                                    <?php foreach ($account_receivable as $p) : 
                                        if($p['year']!=date("Y")-3){?>
                                        <td><?= number_format($p['increase'], 2, '.', ',') ?></td>
                                    <?php }endforeach; ?>
                                </tr>
                                <tr>
                                    <td>Mov. Dec</td>
                                    <?php foreach ($account_receivable as $p) : 
                                        if($p['year']!=date("Y")-3){?>
                                        <td><?= number_format($p['decrease'], 2, '.', ',') ?></td>
                                    <?php }endforeach; ?>
                                </tr>
                                <tr style="font-weight: bold;">
                                    <td>Outstanding</td>
                                    <?php 
                                    $increase = 0 ; $decrease = 0;
                                    foreach ($account_receivable as $p) : 
                                    if($p['year']!=date("Y")){?>
                                        <td><?= number_format($p['previous']+$increase-$decrease, 2, '.', ',') ?></td>
                                    <?php 
                                    }
                                    $increase = $p['increase'];
                                    $decrease = $p['decrease'];
                                    endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <select onchange="fetchAccountPayable()" class="form-control" name="" id="accountPayableFilter">
                                <option value="">Select type</option>
                                <option value="invoice" <?= ($_GET['account_type'] ?? 'invoice') == 'invoice' ? 'selected' : '' ?>>Invoice</option>
                                <option value="net_invoice" <?= ($_GET['account_type'] ?? 'invoice') == 'net_invoice' ? 'selected' : '' ?>>Net Invoice</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <input type="date" onchange="fetchAccountPayable()" name="" id="asOfDate" class="form-control" value="<?= $_GET['as_of_date'] ?? date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <select onchange="fetchAccountReceivable()" class="form-control" name="" id="accountReceivableFilter">
                                <option value="">Select type</option>
                                <option value="invoice" <?= ($_GET['receive_type'] ?? 'invoice') == 'invoice' ? 'selected' : '' ?>>Invoice</option>
                                <option value="net_invoice" <?= ($_GET['receive_type'] ?? 'invoice') == 'net_invoice' ? 'selected' : '' ?>>Net Invoice</option>
                                <option value="bukti_potong" <?= ($_GET['receive_type'] ?? 'invoice') == 'bukti_potong' ? 'selected' : '' ?>>Bukti Potong</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <input type="date" onchange="fetchAccountReceivable()" name="" id="asOfDateReceivable" class="form-control" value="<?= $_GET['as_date_rec'] ?? date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Aging Account Payable</div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Aging Account Payable</th>
                                    <th>Amount</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Current</td>
                                    <td><?= number_format($aging_account['Current'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= number_format(handleDivision($aging_account['Current'],$aging_account['total']) * 100, 2, ',', '.'); ?> %</td>
                                </tr>
                                <tr>
                                    <td>1-30</td>
                                    <td><?= number_format($aging_account['0-30'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= number_format(handleDivision($aging_account['0-30'], $aging_account['total']) * 100, 2, ',', '.'); ?> %</td>
                                </tr>
                                <tr>
                                    <td>31-60</td>
                                    <td><?= number_format($aging_account['31-60'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= number_format(handleDivision($aging_account['31-60'], $aging_account['total']) * 100, 2, ',', '.'); ?> %</td>
                                </tr>
                                <tr>
                                    <td>61-90</td>
                                    <td><?= number_format($aging_account['61-90'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= number_format(handleDivision($aging_account['61-90'], $aging_account['total']) * 100, 2, ',', '.'); ?> %</td>
                                </tr>
                                <tr>
                                    <td>91-180</td>
                                    <td><?= number_format($aging_account['91-180'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= number_format(handleDivision($aging_account['91-180'], $aging_account['total']) * 100, 2, ',', '.'); ?> %</td>
                                </tr>
                                <tr>
                                    <td>> 180</td>
                                    <td><?= number_format($aging_account['>180'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= number_format(handleDivision($aging_account['>180'], $aging_account['total']) * 100, 2, ',', '.'); ?> %</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td><?= number_format($aging_account['total'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= number_format(handleDivision($aging_account['total'], $aging_account['total']) * 100, 2, ',', '.'); ?> %</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Aging Account Receivable</div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Aging Account Receivable</th>
                                    <th>Amount</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Not Yet Due</td>
                                    <td><?= number_format($aging_receivable['Not Yet Due'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= $aging_receivable['total'] != 0 ? number_format($aging_receivable['Not Yet Due'] / $aging_receivable['total'] * 100, 2, ',', '.') : 0; ?> %</td>
                                </tr>
                                <tr>
                                    <td>Current</td>
                                    <td><?= number_format($aging_receivable['Current'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= $aging_receivable['total'] != 0 ? number_format($aging_receivable['Current'] / $aging_receivable['total'] * 100, 2, ',', '.') : 0; ?> %</td>
                                </tr>
                                <tr>
                                    <td>1-30</td>
                                    <td><?= number_format($aging_receivable['0-30'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= $aging_receivable['total'] != 0 ? number_format($aging_receivable['0-30'] / $aging_receivable['total'] * 100, 2, ',', '.') : 0; ?> %</td>
                                </tr>
                                <tr>
                                    <td>31-60</td>
                                    <td><?= number_format($aging_receivable['31-60'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= $aging_receivable['total'] != 0 ? number_format($aging_receivable['31-60'] / $aging_receivable['total'] * 100, 2, ',', '.') : 0; ?> %</td>
                                </tr>
                                <tr>
                                    <td>61-90</td>
                                    <td><?= number_format($aging_receivable['61-90'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= $aging_receivable['total'] != 0 ? number_format($aging_receivable['61-90'] / $aging_receivable['total'] * 100, 2, ',', '.') : 0; ?> %</td>
                                </tr>
                                <tr>
                                    <td>91-180</td>
                                    <td><?= number_format($aging_receivable['91-180'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= $aging_receivable['total'] != 0 ? number_format($aging_receivable['91-180'] / $aging_receivable['total'] * 100, 2, ',', '.') : 0; ?> %</td>
                                </tr>
                                <tr>
                                    <td>> 180</td>
                                    <td><?= number_format($aging_receivable['>180'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= $aging_receivable['total'] != 0 ? number_format($aging_receivable['>180'] / $aging_receivable['total'] * 100, 2, ',', '.') : 0; ?> %</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td><?= number_format($aging_receivable['total'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?= $aging_receivable['total'] != 0 ? number_format($aging_receivable['total'] / $aging_receivable['total'] * 100, 2, ',', '.') : 0; ?> %</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card">
                        <select onchange="coffetchData()" class="form-control" name="" id="cofFilter">
                            <option selected value="">--Select Year--</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?= $year['FISC'] ?>" <?php if($_GET['cof']?? false == $year['FISC']){echo("selected");}?>><?= $year['FISC'] ?></option>
                            <?php break; endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <button type="button" class="btn btn-outline-dark"><i class="ri-download-line"></i>Download</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="row">
                <div class="col-12">
                    <div class="card-header">Cost of Fund ( In Million - Rupiah)</div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">CoF</th>
                                    <th colspan="2" class="text-center">MTD</th>
                                    <th colspan="2" class="text-center">YTD</th>
                                </tr>
                                <tr>
                                    <th style="vertical-align : middle;text-align:right;">Rp jt</th>
                                    <th>Rp/MT</th>
                                    <th>Rp jt</th>
                                    <th>Rp/MT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>KI</td>
                                    <td>Rp <?=number_format($costOfFund[0][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[1][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[2][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[3][0]['result']?? 0, 2, ',', '.')?></td>
                                </tr>
                                <tr>
                                    <td>KMK</td>
                                    <td>Rp <?=number_format($costOfFund[4][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[5][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[6][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[7][0]['result']?? 0, 2, ',', '.')?></td>
                                </tr>
                                <tr>
                                    <td>Leasing</td>
                                    <td>Rp <?=number_format($costOfFund[8][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[9][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[10][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[11][0]['result']?? 0, 2, ',', '.')?></td>
                                </tr>
                                <tr>
                                    <td>SCF</td>
                                    <td>Rp <?=number_format($costOfFund[12][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[13][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[14][0]['result']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[15][0]['result']?? 0, 2, ',', '.')?></td>
                                </tr>
                                <!-- <tr>
                                    <td>UM</td>
                                    <td>Rp <?=number_format($costOfFund[16][0]['RpJtYtd']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[17][0]['RpMtYtd']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[18][0]['RpJtYtd']?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[19][0]['RpMtYtd']?? 0, 2, ',', '.')?></td>
                                </tr> -->

                                <!-- total dibawah jangan dihapus -->
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td>Rp <?=number_format($costOfFund[20]?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[21]?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[22]?? 0, 2, ',', '.')?></td>
                                    <td>Rp <?=number_format($costOfFund[23]?? 0, 2, ',', '.')?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-4">
                    <!-- Pie Chart -->
                    <!-- <div id="pieChartTopSupplier"></div> -->
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            new ApexCharts(document.querySelector("#pieChartTopSupplier"), {
                                series: [60, 4, 0.37, 34.1, 0.1],
                                chart: {
                                    height: 350,
                                    type: 'pie',
                                    toolbar: {
                                        show: true
                                    }
                                },
                                plotOptions: {
                                    pie: {
                                        startAngle: -90,
                                        endAngle: 270,
                                        expandOnClick: true
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    formatter: function(val) {
                                        return val + "%"
                                    },
                                },
                                legend: {
                                    position: 'bottom',
                                    horizontalAlign: 'left',
                                    formatter: function(val, opts) {
                                        return val + " - " + opts.w.globals.series[opts.seriesIndex]
                                    }
                                },
                                labels: ['KI', 'KM', 'Leasing', 'SCF', 'UM'],
                            }).render();
                        });
                    </script>
                    <!-- End Pie Chart -->
                </div>
            </div>
        </div>



        </div>
        </div>
        </div>

    </section>

</main><!-- End #main -->

<script>
    $(document).ready(function() {

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

    function coffetchData(){
        var cof = document.getElementById("cofFilter").value;
        location.href = "<?= site_url("finance/balance")."?cof="?>"+cof+"#cofFilter"
    }

</script>

<?= $this->endSection() ?>