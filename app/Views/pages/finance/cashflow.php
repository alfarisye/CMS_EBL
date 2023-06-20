<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<?php 
    $start = $_GET['start'] ?? $selectedParams['sdate'];
    $end = $_GET['end'] ?? $selectedParams['edate'];
    $selected_date = '';
    if ($start && $end) {
        $start_date = new DateTime($start);
        $end_date = new DateTime($end);
        $fsdate = $start_date->format('d/m/Y');
        $fedate = $end_date->format('d/m/Y');
        $selected_date = "$fsdate - $fedate";
    }
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
        <h1>Cash Flow</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance") ?>">Finance</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/cashflow") ?>">Cash Flow</a></li>
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
                    <div class="col-sm-6">
                        <div class="card">
                            <select onchange="fetchData()" class="form-control" name="" id="monthFilter">
                                <option value="1" <?= 1 == $selectedParams['month'] ? 'selected' : null ?>>
                                    January
                                </option>
                                <option value="2" <?= 2 == $selectedParams['month'] ? 'selected' : null ?>>
                                    February
                                </option>
                                <option value="3" <?= 3 == $selectedParams['month'] ? 'selected' : null ?>>March
                                </option>
                                <option value="4" <?= 4 == $selectedParams['month'] ? 'selected' : null ?>>April
                                </option>
                                <option value="5" <?= 5 == $selectedParams['month'] ? 'selected' : null ?>>May
                                </option>
                                <option value="6" <?= 6 == $selectedParams['month'] ? 'selected' : null ?>>June
                                </option>
                                <option value="7" <?= 7 == $selectedParams['month'] ? 'selected' : null ?>>July
                                </option>
                                <option value="8" <?= 8 == $selectedParams['month'] ? 'selected' : null ?>>August
                                </option>
                                <option value="9" <?= 9 == $selectedParams['month'] ? 'selected' : null ?>>
                                    September
                                </option>
                                <option value="10" <?= 10 == $selectedParams['month'] ? 'selected' : null ?>>
                                    October
                                </option>
                                <option value="11" <?= 11 == $selectedParams['month'] ? 'selected' : null ?>>
                                    November
                                </option>
                                <option value="12" <?= 12 == $selectedParams['month'] ? 'selected' : null ?>>
                                    December
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <input type="text" name="dateRange" id="dateRangeCashflow" value="<?= $selected_date ?>"
                                class="form-control" placeholder="Input date range here">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <h5 class="card-title">Reports</h5>
                <!-- notification -->
                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="alert alert-warning" role="alert">
                        <p><?= session()->getFlashdata('message') ?></p>
                    </div>
                <?php endif ?>
                <div class="row">
                    <div class="card">
                        <div class="card-header text-center">
                            <h5 class="card-title">Cash Flow Report</h5>
                            <p class="card-subtitle">(In Million Rupiah)</p>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 mb-3">
                                <div class="table-responsive">
                                    <table class="table table-sm" id="targetCashflowReport">
                                        <thead>
                                            <tr>
                                                <th>DESCRIPTION</th>
                                                <th><?= $selectedParams['month'] == $todayDate['month'] && $selectedParams['year'] == $todayDate['year'] ? "MTD" : $selectedParams['month'] . "/" . $selectedParams['year'] ?></th>
                                                <th><?= $selectedParams['year'] == $todayDate['year'] ? "YTD" : $selectedParams['year'] ?></th>
                                                <th>RANGE BASED</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Operating</td>
                                                <td><?= number_format($operating_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($operating_ytd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($operating_range ?? 0, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Investing</td>
                                                <td><?= number_format($investing_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($investing_ytd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($investing_range ?? 0, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr style="font-weight: bold;">
                                                <td>Free Cash Flow</td>
                                                <td><?= number_format($free_cash_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($free_cash_ytd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($free_cash_range ?? 0, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Financing</td>
                                                <td><?= number_format($financing_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($financing_ytd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($financing_range ?? 0, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr style="font-weight: bold;">
                                                <td>Net Cash Flow</td>
                                                <td><?= number_format($net_cash_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($net_cash_ytd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($net_cash_range ?? 0, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr style="font-weight: bold;">
                                                <td>Beginning Balance</td>
                                                <td><?= number_format($beginning_balance_mtd['BALANCE'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($beginning_balance_ytd['BALANCE'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($beginning_range ?? 0, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr style="font-weight: bold;">
                                                <td>Ending Balance</td>
                                                <td><?= number_format($ending_balance_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($ending_balance_ytd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= number_format($ending_balance_range ?? 0, 2, ',', '.') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header text-center">
                            <h5 class="card-title">Cash Conversion Cycle</h5>
                            <p class="card-subtitle">(In Days)</p>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 mb-3">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>DESCRIPTION</th>
                                            <th><?= $selectedParams['month'] == $todayDate['month'] && $selectedParams['year'] == $todayDate['year'] ? "MTD" : $selectedParams['month'] . "/" . $selectedParams['year'] ?></th>
                                            <th><?= $selectedParams['year'] == $todayDate['year'] ? "YTD" : $selectedParams['year'] ?></th>
                                            <th>RANGE BASED</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="font-weight: bold;">
                                            <td>Inventory</td>
                                            <td><?= number_format($days_inventory_mtd ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($days_inventory_ytd ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($days_inventory_date ?? 0, 2, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td>Sales</td>
                                            <td><?= number_format($days_sales_mtd ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($days_sales_ytd ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($days_sales_date ?? 0, 2, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td>Payable</td>
                                            <td><?= number_format($days_payable_mtd ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($days_payable_ytd ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($days_payable_date ?? 0, 2, ',', '.') ?></td>
                                        </tr>
                                        <tr style="font-weight: bold;">
                                            <td>Cash Convertion Cycle</td>
                                            <td><?= number_format($cash_convertion_cycle_mtd ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($cash_convertion_cycle_ytd ?? 0, 2, ',', '.') ?></td>
                                            <td><?= number_format($cash_convertion_cycle_date ?? 0, 2, ',', '.') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header text-center">
                            <h5 class="card-title">Cash Cost</h5>
                            <p class="card-subtitle">(In Million Rupiah)</p>
                        </div>
                        <div class="card-body">
                            <div class="col mb-3">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>DESCRIPTION</th>
                                                <th><?= $selectedParams['month'] == $todayDate['month'] && $selectedParams['year'] == $todayDate['year'] ? "MTD" : $selectedParams['month'] . "/" . $selectedParams['year'] ?></th>
                                                <th class="dont-show">MTD/MT</th>
                                                <th><?= $selectedParams['year'] == $todayDate['year'] ? "YTD" : $selectedParams['year'] ?></th>
                                                <th class="dont-show">YTD/MT</th>
                                                <th>RANGE BASED</th>
                                                <th class="dont-show">RANGE BASED/MT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Total Cost</td>
                                                <td><?= number_format($total_cost_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td class="dont-show"><?= number_format(handleDivision($total_cost_mtd, $mtd_mt), 2, ',', '.') ?></td>
                                                <td><?= number_format($total_cost_ytd ?? 0, 2, ',', '.') ?></td>
                                                <td class="dont-show"><?= number_format(handleDivision($total_cost_ytd, $ytd_mt), 2, ',', '.') ?></td>
                                                <td><?= number_format($total_cost_range ?? 0, 2, ',', '.') ?></td>
                                                <td class="dont-show"><?= number_format(handleDivision($total_cost_range, $range_mt), 2, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Cash Cost</td>
                                                <td><?= number_format($cash_cost_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td class="dont-show"><?= number_format(handleDivision($cash_cost_mtd, $mtd_mt), 2, ',', '.') ?></td>
                                                <td><?= number_format($cash_cost_ytd ?? 0, 2, ',', '.') ?></td>
                                                <td class="dont-show"><?= number_format(handleDivision($cash_cost_ytd, $ytd_mt), 2, ',', '.') ?></td>
                                                <td><?= number_format($cash_cost_range ?? 0, 2, ',', '.') ?></td>
                                                <td class="dont-show"><?= number_format(handleDivision($cash_cost_range, $range_mt), 2, ',', '.') ?></td>
                                            </tr>
                                            <tr style="font-weight: bold;">
                                                <td>Variance</td>
                                                <td><?= number_format($variance_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td class="dont-show"><?= number_format(handleDivision($variance_mtd, $mtd_mt), 2, ',', '.') ?></td>
                                                <td><?= number_format($variance_ytd ?? 0, 2, ',', '.') ?></td>
                                                <td class="dont-show"><?= number_format(handleDivision($variance_ytd, $ytd_mt), 2, ',', '.') ?></td>
                                                <td><?= number_format($variance_range ?? 0, 2, ',', '.') ?></td>
                                                <td class="dont-show"><?= number_format(handleDivision($variance_range, $range_mt), 2, ',', '.') ?></td>
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
    </section>

</main><!-- End #main -->
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
            let month = document.getElementById("monthFilter");
            let year = document.getElementById("yearFilter");
            let url = updateURLParameter(window.location.href, 'start', picker.startDate.format('YYYY-MM-DD'));
            url = updateURLParameter(url, 'end', picker.endDate.format('YYYY-MM-DD'));
            url = updateURLParameter(url, 'year', year.value);
            url = updateURLParameter(url, 'month', month.value);
            window.location.href = url;
        });

        $('#dateRangeCashflow').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
    const fetchData = function() {
        const month = document.getElementById("monthFilter");
        const year = document.getElementById("yearFilter");
        if (month != '' && year != '') {
            let url = updateURLParameter(window.location.href, 'month', month.value);
            url = updateURLParameter(url, 'year', year.value);
            url = deleteURLParameter(url, 'start');
            url = deleteURLParameter(url, 'end');
            window.location.href = url;
        }
    }
</script>

<?= $this->endSection() ?>