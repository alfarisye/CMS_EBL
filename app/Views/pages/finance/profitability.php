<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<style>
    td:nth-child(n + 2) {
        text-align: right;
    }

    th:nth-child(n + 2) {
        text-align: right;
    }
</style>
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


    $start_profit = $_GET['start_profit'] ?? $selectedParamsProfit['start_date_profit'];
    $end_profit = $_GET['end_profit'] ?? $selectedParamsProfit['end_date_profit'];
    $selected_date_profit = '';
    if ($start_profit && $end_profit) {
        $start_date_profit = new DateTime($start_profit);
        $end_date_profit = new DateTime($end_profit);
        $fsdate_profit = $start_date_profit->format('d/m/Y');
        $fedate_profit = $end_date_profit->format('d/m/Y');
        $selected_date_profit = "$fsdate_profit - $fedate_profit";
    }

?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Profitability</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url("finance") ?>">Finance</a></li>
                <li class="breadcrumb-item active"><a href="<?= site_url("finance/profit") ?>">Profitability</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-sm-6 text-end">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card">
                            <select onchange="fetchData()" class="form-control" name="" id="yearFilter">
                                <?php foreach ($years as $year) : ?>
                                    <option value="<?= $year['FISC'] ?>" <?= $year['FISC'] == $selectedParams['year'] ? 'selected' : null ?>><?= $year['FISC'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <h4 class="text-center">Profitability Ratio</h4>
            </div>
            <div class="col-lg-12" id="profitratio">
                <div class="card">
                    <div class="card-body">
                        <?= view('pages/finance/profitratio') ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-12" id="targetProfitLoss">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Profit and Loss</h5>
                        <div class="row">
                            <div class="col">
                                <p class="card-subtitle text-center">(<?= $selectedParams['year'] ?> - In Rupiah)</p>
                                <div class="row mx-3">
                                    <div class="col-sm-4">
                                        <div class="card">
                                            <input type="text" name="dateRangeProfit" id="dateRangeCashflowProfit" value="<?= $selected_date_profit ?>"
                                                class="form-control" placeholder="Input date range here">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Account</th>
                                                <th>Budget (RP)</th>
                                                <th>Budget (%)</th>
                                                <th>Actual (RP)</th>
                                                <th>Actual (%)</th>
                                                <th>Variance (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Revenue</td>
                                                <td>Rp. <?= number_format($revenue_budget_mtd['BUDGET'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format($revenue_budget_mtd['BUDGET'] / $revenue_budget_mtd['BUDGET'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($revenue_actual_mtd['ACTUAL'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format($revenue_actual_mtd['ACTUAL'] / $revenue_actual_mtd['ACTUAL'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $revenue_variance_mtd != 0 ? number_format($revenue_variance_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                            </tr>
                                            <tr>
                                                <td>COGS</td>
                                                <td>Rp. <?= number_format($cogs_budget_mtd['BUDGET'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format($cogs_budget_mtd['BUDGET'] / $revenue_budget_mtd['BUDGET'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($cogs_actual_mtd['ACTUAL'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format($cogs_actual_mtd['ACTUAL'] / $revenue_actual_mtd['ACTUAL'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $cogs_variance_mtd != 0 ? number_format($cogs_variance_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                            </tr>
                                            <tr style="font-weight: bold; background-color:aquamarine">
                                                <td>GP</td>
                                                <td>Rp. <?= number_format($gp_budget_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format($gp_budget_mtd / $revenue_budget_mtd['BUDGET'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($gp_actual_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format($gp_actual_mtd / $revenue_actual_mtd['ACTUAL'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $gp_variance_mtd != 0 ? number_format($gp_variance_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                            </tr>
                                            <tr>
                                                <td>GAE</td>
                                                <td>Rp. <?= number_format($gae_budget_mtd['BUDGET'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format($gae_budget_mtd['BUDGET'] / $revenue_budget_mtd['BUDGET'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($gae_actual_mtd['ACTUAL'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format($gae_actual_mtd['ACTUAL'] / $revenue_actual_mtd['ACTUAL'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $gae_variance_mtd != 0 ? number_format($gae_variance_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                            </tr>
                                            <tr style="font-weight: bold;background-color:aquamarine">
                                                <td>OP</td>
                                                <td>Rp. <?= number_format($op_budget_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format($op_budget_mtd / $revenue_budget_mtd['BUDGET'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($op_actual_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format($op_actual_mtd / $revenue_actual_mtd['ACTUAL'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $op_variance_mtd != 0 ? number_format($op_variance_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                            </tr>
                                            <tr>
                                                <td>Other I/E</td>
                                                <td>Rp. <?= number_format($oie_budget_mtd['BUDGET'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format(($oie_budget_mtd['BUDGET'] / $revenue_budget_mtd['BUDGET']) * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($oie_actual_mtd['ACTUAL'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format(($oie_actual_mtd['ACTUAL'] / $revenue_actual_mtd['ACTUAL']) * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $oie_variance_mtd != 0 ? number_format($oie_variance_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                            </tr>
                                            <tr style="font-weight: bold;background-color:aquamarine">
                                                <td>EBT</td>
                                                <td>Rp. <?= number_format($ebt_budget_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format($ebt_budget_mtd / $revenue_budget_mtd['BUDGET'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($ebt_actual_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format($ebt_actual_mtd / $revenue_actual_mtd['ACTUAL'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $ebt_variance_mtd != 0 ? number_format($ebt_variance_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                            </tr>
                                            <tr>
                                                <td>Tax</td>
                                                <td>Rp. <?= number_format($tax_budget_mtd['BUDGET'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format(($tax_budget_mtd['BUDGET'] / $revenue_budget_mtd['BUDGET']) * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($tax_actual_mtd['ACTUAL'] ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format(($tax_actual_mtd['ACTUAL'] / $revenue_actual_mtd['ACTUAL']) * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $tax_variance_mtd != 0 ? number_format($tax_variance_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                            </tr>
                                            <tr style="font-weight: bold;background-color:aquamarine">
                                                <td>EAT</td>
                                                <td>Rp. <?= number_format($eat_budget_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format($eat_budget_mtd / $revenue_budget_mtd['BUDGET'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($eat_actual_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format($eat_actual_mtd / $revenue_actual_mtd['ACTUAL'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $eat_variance_mtd != 0 ? number_format($eat_variance_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                            </tr>
                                            <tr>
                                                <td>EBITDA</td>
                                                <td>Rp. <?= number_format($ebitda_budget_fix_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_budget_mtd['BUDGET'] != 0 ? number_format($ebitda_budget_fix_mtd / $revenue_budget_mtd['BUDGET'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td>Rp. <?= number_format($ebitda_actual_fix_mtd ?? 0, 2, ',', '.') ?></td>
                                                <td><?= $revenue_actual_mtd['ACTUAL'] != 0 ? number_format($ebitda_actual_fix_mtd / $revenue_actual_mtd['ACTUAL'] * 100, 2, ',', '.') . "%" : "0%" ?></td>
                                                <td><?= $ebitda_variance_fix_mtd != 0 ? number_format($ebitda_variance_fix_mtd * 100, 2, ',', '.') . "%" : "0%" ?></td>
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

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Profit Per Shipment Current Year (<?= $selectedParams['year'] ?>)</h5>
                    <br>
                    <div class="row mx-3">
                        <div class="col-sm-3">
                            <div class="card">
                                <input type="text" name="dateRange" id="dateRangeCashflow" value="<?= $selected_date ?>"
                                    class="form-control" placeholder="Input date range here">
                            </div>
                        </div>
                    </div>

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
                                    <i class="bi bi-plus me-1"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    <i class="ri-download-line"></i> Download
                                </button>
                            </div>
                        </div> -->

                    <!-- Table users -->
                    <div class="table-reponsive">

                        <table class="table-bordered table table-sm" >
                            <thead>
                                <tr style="font-weight: bold;background-color:dodgerblue" class="text-light text-center">
                                    <th rowspan="2">TYPE</th>
                                    <th rowspan="2">Point Of Sale</th>
                                    <th rowspan="2">UoM</th>
                                    <th colspan="2">Price</th>
                                    <th colspan="2">Cost</th>
                                    <th colspan="2">Earning</th>
                                </tr>
                                <tr style="font-weight: bold;background-color:dodgerblue;text-align : right" class="text-light">
                                    <th><span>RKAP</span></th>
                                    <th><span>Actual</span></th>  
                                    <th><span>RKAP</span></th>  
                                    <th><span>Actual</span></th>  
                                    <th><span>RKAP</span></th>  
                                    <th><span>Actual</span></th>      
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="3">LOCAl</td>
                                    <td>FOBB</td>
                                    <td>Rp./mt</td>
                                    <td><?= number_format($local_fobb_rkap_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fobb_actual_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fobb_rkap_cost['cost'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fobb_actual_cost3['total'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fobb_rkap_earning ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fobb_actual_earning ?? 0, 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td style = "text-align : right">CIF</td>
                                    <td>Rp./mt</td>
                                    <td><?= number_format($local_cif_rkap_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_cif_actual_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_cif_rkap_cost['cost'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_cif_actual_cost3['total'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_cif_rkap_earning ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_cif_actual_earning ?? 0, 2, ',', '.') ?></td>
                                </tr>

                                <tr>
                                    <td style = "text-align : right">Franco Pabrik</td>
                                    <td>Rp./mt</td>
                                    <td><?= number_format($local_fb_rkap_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fb_actual_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fb_rkap_cost['cost'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fb_actual_cost3['total'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fb_rkap_earning ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($local_fb_actual_earning ?? 0, 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td rowspan="4">EXPORT</td>
                                    <td>FOBB</td>
                                    <td>Rp./mt</td>
                                    <td><?= number_format($export_fobb_rkap_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fobb_actual_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fobb_rkap_cost['cost'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fobb_actual_cost3['total'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fobb_rkap_earning ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fobb_actual_earning ?? 0, 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td style = "text-align : right">CIF</td>
                                    <td>Rp./mt</td>
                                    <td><?= number_format($export_cif_rkap_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_cif_actual_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_cif_rkap_cost['cost'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_cif_actual_cost3['total'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_cif_rkap_earning ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_cif_actual_earning ?? 0, 2, ',', '.') ?></td>
                                </tr>
                                </tr>
                                <tr>
                                    <td style = "text-align : right">FAS</td>
                                    <td>Rp./mt</td>
                                    <td><?= number_format($export_fas_rkap_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fas_actual_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fas_rkap_cost['cost'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fas_actual_cost3['total'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fas_rkap_earning ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_fas_actual_earning ?? 0, 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td style = "text-align : right">MV</td>
                                    <td>Rp./mt</td>
                                    <td><?= number_format($export_mv_rkap_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_mv_actual_price['price'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_mv_rkap_cost['cost'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_mv_actual_cost3['total'] ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_mv_rkap_earning ?? 0, 2, ',', '.') ?></td>
                                    <td><?= number_format($export_mv_actual_earning ?? 0, 2, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div class="col-lg-12">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Cost Volume Profit Analysis (<?= $selectedParams['year'] ?>)</h5>
                        <div class="row">
                            <div class="col-sm-2 py-3">
                                <select onchange="fetchData()" class="form-control" name="" id="startPeriode2">
                                    <option value="1" <?= 1 == $selectedParams['startPeriode2'] ? 'selected' : null ?>>Januari</option>
                                </select>
                            </div>
                            <div class="col-sm-1 py-3">To</div>
                            <div class="col-sm-2 py-3">
                                <select onchange="fetchData()" class="form-control" name="" id="endPeriode2">
                                    <option value="1" <?= 1 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>Januari</option>
                                    <option value="2" <?= 2 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>Febuari</option>
                                    <option value="3" <?= 3 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>Maret</option>
                                    <option value="4" <?= 4 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>April</option>
                                    <option value="5" <?= 5 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>Mei</option>
                                    <option value="6" <?= 6 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>Juni</option>
                                    <option value="7" <?= 7 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>July</option>
                                    <option value="8" <?= 8 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>Agustus</option>
                                    <option value="9" <?= 9 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>September</option>
                                    <option value="10" <?= 10 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>Oktober</option>
                                    <option value="11" <?= 11 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>November</option>
                                    <option value="12" <?= 12 == $selectedParams['endPeriode2'] ? 'selected' : null ?>>Desember</option>
                                </select>
                            </div>
                        </div>
                    </div>
                        <!-- Table users -->
                        <div class="table-reponsive">

                            <table class="table-bordered table table-sm">
                                <thead>
                                    <tr style="font-weight: bold;background-color:dodgerblue" class="text-light">
                                        <th scope="col">Remark</th>
                                        <th scope="col">UoM</th>
                                        <th scope="col">A</th>
                                        <th scope="col">B</th>
                                        <th scope="col">C</th>
                                        <th scope="col">D</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Price</td>
                                        <td>Rp/mt</td>
                                        <td><?= number_format($CVP_a_price['rata_price'] ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($CVP_b_price['rata_price'] ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($CVP_c_price['rata_price'] ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($CVP_d_price['rata_price'] ?? 0, 2, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td>Fixed Cost</td>
                                        <td>Rp jt</td>
                                        <td><?= number_format($CVP_a_FC['balance'] ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($CVP_b_FC ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($CVP_c_FC ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($CVP_d_FC ?? 0, 2, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td>Variabel Cost</td>
                                        <td>Rp/mt</td>
                                        <td><?= number_format($CVP_a_VC ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($CVP_b_VC ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($CVP_c_VC ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($CVP_d_VC ?? 0, 2, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td>BE Qty</td>
                                        <td>mt</td>
                                        <td><?= number_format($a_be_qty ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($b_be_qty ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($c_be_qty ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($d_be_qty ?? 0, 2, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <h5>A : Non Loan; B: +Loan; C:B+Profit; D:C&Qty</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

</main><!-- End #main -->

<script>
    //pershipment
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

        // profit and loss
        $('#dateRangeCashflowProfit').daterangepicker({
            showDropdowns: true,
            autoUpdateInput: false,
            minMonth: new Date().getMonth(),
            locale: {
                format: 'DD/MM/YYYY'
            },
            startDate: moment('<?= $start_profit ?>'),
            endDate: moment('<?= $end_profit ?>'),
        });
        $('#dateRangeCashflowProfit').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            let url = updateURLParameter(window.location.href, 'start_profit', picker.startDate.format('YYYY-MM-DD'));
            url = updateURLParameter(url, 'end_profit', picker.endDate.format('YYYY-MM-DD'));
            window.location.href = url;
        });

        $('#dateRangeCashflowProfit').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
    const fetchData = function() {
        const year = document.getElementById("yearFilter");
        const startPeriode2 = document.getElementById("startPeriode2");
        const endPeriode2 = document.getElementById("endPeriode2");
        if (year != '') {
            let url = updateURLParameter(window.location.href, 'year', year.value);
            url = updateURLParameter(url, 'startPeriode2', startPeriode2.value);
            url = updateURLParameter(url, 'endPeriode2', endPeriode2.value);
            window.location.href = url;
        }
    }
</script>

<?= $this->endSection() ?>