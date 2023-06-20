<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">

<?php
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
$parameter = $_GET['type'] ?? '';
$time = $_GET['time'] ?? 'yearly';
$parameter_ship = $_GET['ship_type'] ?? '';
$cc_parameter = $_GET['cc_type'] ?? '';
if ($parameter == '') {
  $parameter = 'ob';
}
$month = $_GET['month'] ?? date("m");
$year = $_GET['year'] ?? date('Y');

$start_date_profit = $_GET['start_profit'] ?? false;
$end_date_profit = $_GET['end_profit'] ?? false;

$date = $_GET['date'] ?? false;
?>
<style>
  td:nth-child(n + 2) {
    text-align: right;
  }

  /* th:nth-child(n + 2) {
    text-align: right;
  } */
</style>
<script src="<?= base_url("assets/vendor/simple-datatables/simple-datatables.js") ?>"></script>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div>

  <section class="section dashboard">
    <!-- FILTERS -->
    <div class="row">
      <div class="col-sm-12">
        <div class="row">
          <div class="col-sm-3">
            <select onchange="fetchData()" class="form-control" name="" id="typeFilter">
              <option value="yearly" <?= 'yearly' == $time ? 'selected' : null ?>>Yearly</option>
              <option value="monthly" <?= 'monthly' == $time ? 'selected' : null ?>>Monthly</option>
              <option value="daily" <?= 'daily' == $time ? 'selected' : null ?>>Daily</option>
            </select>
          </div>
          <div class="col-sm-3">
            <div class="card">
              <select onchange="fetchData()" class="form-control" name="" id="yearFilter">
                <?php foreach (range(date('Y'), date('Y') - 4) as $y) : ?>
                  <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card">
              <select onchange="fetchData()" class="form-control" name="" id="monthFilter">
                <!-- <option value="all">All</option> -->
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
          <div class="col-sm-3">
            <div class="card">
              <input type="date" value="<?= $date ?? '' ?>" name="literalDateFilter" id="literalDateFilter" min="<?= $year . '-' . sprintf('%02d', $month - 1) . '-' . '26' ?>" max="<?= $year . '-' . sprintf('%02d', $month) . '-' . '25' ?>" class="form-control" onchange="fetchData()">
            </div>
          </div>
        </div>
      </div>

    </div>
    <!-- Cards -->
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <!-- OB Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">

              <div class="card-body">
                <h5 class="card-title">Sum Of Overburden <span>| <?= $time == 'Yearly' ? "YTD" :  ucfirst($time) ?></span></h5>

                <div class="d-flex align-items-left">
                  <div class="">
                    <h6><?= number_format($ob_production['actual'] ?? 0, 2, ',', '.') ?></h6>
                    <span class="text-primary small pt-1 fw-bold"><?= number_format($ob_production['budget'] != 0 ? ($ob_production['actual'] / $ob_production['budget']) * 100 : 0, 2, ',', '.') . '%' ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End OB Card -->

          <!-- CG Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">

              <div class="card-body">
                <h5 class="card-title">Sum Of Coal Getting <span>| <?= $time == 'Yearly' ? "YTD" :  ucfirst($time) ?></span></h5>

                <div class="d-flex align-items-left">
                  <div class="">
                    <h6><?= number_format($cg_production['actual'] ?? 0, 2, ',', '.') ?></h6>
                    <span class="text-success small pt-1 fw-bold"><?= number_format($cg_production['budget'] != 0 ? ($cg_production['actual'] / $cg_production['budget'] * 100) : 0, 2, ',', '.') . '%' ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End CG Card -->

          <!-- SR Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">

              <div class="card-body">
                <h5 class="card-title">Sum Of Stripping Ratio <span>| <?= $time == 'Yearly' ? "YTD" :  ucfirst($time) ?></span></h5>

                <div class="d-flex align-items-left">
                  <div class="">
                    <?php
                    $actual_sr = $stripping_ytd['actual_cg'] != 0 ? $stripping_ytd['actual_ob'] / $stripping_ytd['actual_cg'] : 0;
                    $budget_sr = $stripping_ytd['budget_cg'] != 0 ? $stripping_ytd['budget_ob'] / $stripping_ytd['budget_cg'] : 0;
                    ?>
                    <h6><?= $stripping_ytd['actual_cg'] != 0 ? number_format($actual_sr, 2, ',', '.') : 0 ?></h6>
                    <span class="text-warning small pt-1 fw-bold"><?= number_format($budget_sr && $actual_sr != 0 ? ($actual_sr / $budget_sr) * 100 : 0, 2, ',', '.') . '%' ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End SR Card -->

          <!-- Crush Coal Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">

              <div class="card-body">
                <h5 class="card-title">Sum Of Crush Coal <span>| <?= $time == 'Yearly' ? "YTD" :  ucfirst($time) ?></span></h5>

                <div class="d-flex align-items-left">
                  <div class="">
                    <h6><?= number_format($crush_coal_ytd['total'] ?? 0, 2, ',', '.') ?></h6>
                    <span class="text-primary small pt-1 fw-bold">
                      <?php if ($crush_coal_ytd) : ?>
                        <?= $crush_coal_ytd['budget'] != 0 ? number_format($crush_coal_ytd['total'] * 100 / $crush_coal_ytd['budget'], 2, ',', '.') . '%' : "0,00%" ?>
                      <?php else : ?>
                        0,00%
                      <?php endif; ?>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End Crush Coal Card -->

          <!-- Hauling to Port Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">

              <div class="card-body">
                <h5 class="card-title">Sum Of Hauling to Port <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                <div class="d-flex align-items-left">
                  <div class="">
                    <h6><?= number_format($inquiry_transfer['total'] ?? 0, 2, ',', '.') ?></h6>
                    <span class="text-success small pt-1 fw-bold">
                      <?php if ($inquiry_transfer) : ?>
                        <?= $inquiry_transfer['budget'] != 0 ? number_format($inquiry_transfer['total'] * 100 / $inquiry_transfer['budget'], 2, ',', '.') . '%' : "0,00%" ?>
                      <?php else : ?>
                        0,00%
                      <?php endif; ?>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Barging Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">

              <div class="card-body">
                <h5 class="card-title"> Sum Of Barging <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                <div class="d-flex align-items-left">
                  <div class="">
                    <?php
                    if (isset($barging_ytd['target']) || isset($barging_ytd['total'])) {
                      $target_barging = number_format($barging_ytd['target'] != 0 ? ($barging_ytd['total'] * 100 / $barging_ytd['target']) : 0, 2, ',', '.') . '%';
                      $total_barging = number_format($barging_ytd['total'] ?? 0, 2, ',', '.');
                    } else {
                      $target_barging = '0%';
                      $total_barging = 0;
                    }
                    ?>
                    <h6><?= $total_barging ?></h6>
                    <span class="text-warning small pt-1 fw-bold"><?= $target_barging ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End Barging Card -->

          <!-- OB distance Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">
              <div class="card-body">
                <h5 class="card-title">Overburden Distance <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>
                <div class="d-flex align-items-left">
                  <div class="">
                    <?php
                    if ($sum_actual_ob_distance['total'] != 0) {
                      $temp_ob_distance = $sum_actual_ob_distance['total'];
                    } else {
                      $temp_ob_distance = 0;
                    }
                    ?>
                    <h6><?= number_format($sum_actual_ob_distance['total'] != 0 ? ($temp_ob_distance) : 0, 2, ',', '.') ?></h6>
                    <span class="text-primary small pt-1 fw-bold">
                      <?= $sum_plan_ob_distance['total'] != 0 ? number_format($temp_ob_distance * 100 / $sum_plan_ob_distance['total'], 2, ',', '.') . '%' : "0,00%" ?>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End OB distance Card -->

          <!-- CG distance Card -->
          <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">

              <div class="card-body">
                <h5 class="card-title">Coal Getting Distance <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                <div class="d-flex align-items-left">
                  <div class="">
                    <?php
                    if ($sum_actual_cg_distance['total'] != 0) {
                      $temp_cg_distance = $sum_actual_cg_distance['total'];
                    } else {
                      $temp_cg_distance = 0;
                    }
                    ?>
                    <h6><?= number_format(($temp_cg_distance ?? 0), 2, ',', '.') ?></h6>
                    <span class="text-success small pt-1 fw-bold">
                      <?= $sum_plan_cg_distance['total'] != 0 ? number_format($temp_cg_distance * 100 / $sum_plan_cg_distance['total'], 2, ',', '.') . '%' : "0,00%" ?>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End CG distance Card -->

          <!-- filter -->
          <div class="col-12 my-3">
            <div class="btn-group" role="group" aria-label="Basic example">
              <button onclick="fetchChartType('ob')" type="button" class="btn btn-primary <?= $parameter == 'ob' ? "active" : '' ?>">Overburden</button>
              <button onclick="fetchChartType('cg')" type="button" class="btn btn-primary <?= $parameter == 'cg' ? "active" : '' ?>">Coal Getting</button>
              <button onclick="fetchChartType('sr')" type="button" class="btn btn-primary <?= $parameter == 'sr' ? "active" : '' ?>">Stripping Ratio</button>
              <button onclick="fetchChartType('distance_ob')" type="button" class="btn btn-primary <?= $parameter == 'distance_ob' ? "active" : '' ?>">Distance Overburden</button>
              <button onclick="fetchChartType('distance_cg')" type="button" class="btn btn-primary <?= $parameter == 'distance_cg' ? "active" : '' ?>">Distance Coal Getting</button>
            </div>
          </div>
          <!-- Reports -->
          <?php if ($parameter == 'cg') : ?>
            <div class="col-lg-6">
              <div class="card">

                <div class="card-body">
                  <h5 class="card-title">Sum Coal Getting (MT) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                  <!-- Line Chart -->
                  <div id="reportsChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                            name: "Target",
                            data: [
                              <?php foreach ($cg_lines as $ob) : ?> {
                                  x: "<?= $bulan[$ob['month']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($ob['month'])) : $ob['month']) ?>",
                                  y: <?= $ob['budget'] ?? 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          },
                          {
                            name: "Actual",
                            data: [
                              <?php foreach ($cg_lines as $ob) : ?> {
                                  x: "<?= $bulan[$ob['month']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($ob['month'])) : $ob['month']) ?>",
                                  y: <?= $ob['actual'] ?? 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          },
                        ],
                        chart: {
                          height: 350,
                          type: "area",
                          toolbar: {
                            show: true
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
                          title: {
                            text: 'Quantity',
                            style: {
                              color: "#fff",
                              fontSize: "1px"
                            }
                          },
                          min: 5,
                          labels: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          },
                          forceNiceScale: true,
                        },
                        tooltip: {
                          x: {
                            format: "dd/MM/yy HH:mm",
                          },
                        },
                      }).render();
                    });
                  </script>
                  <!-- End Line Chart -->
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Contractor Performance Coal Getting (%) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                  <!-- Column Chart -->
                  <div id="contractorPerformanceChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#contractorPerformanceChart"), {
                        series: [
                          <?php if ($time == 'yearly' || $time == 'monthly') : ?>
                            <?php foreach ($contractor_cg as $name => $val) : ?> {
                                name: '<?= $name ?>',
                                data: [
                                  <?php if ($time == 'yearly') { ?>
                                    <?php foreach ($val as $v) : ?> {
                                        x: '<?= $v["bulan"] ?>',
                                        y: '<?= handleDivision($v['actual_cg'], $v['budget_cg']) * 100 ?>',
                                      },
                                    <?php endforeach; ?>
                                  <?php } elseif ($time == 'monthly') { ?>
                                    <?php for ($i = 1; $i <= 12; $i++) { ?> {
                                        x: "<?= $bulan[$i] ?>",
                                        y: <?= handleDivision($val[$i]['actual_cg'] ?? 0, $val[$i]['budget_cg'] ?? 0) * 100 ?>,
                                      },
                                    <?php } ?>
                                  <?php } ?>
                                ]
                              },
                            <?php endforeach; ?>
                          <?php else : ?>
                            <?php foreach ($contractor_cg_ob as $name => $val) : ?> {
                                name: '<?= $name ?>',
                                data: [
                                  <?php foreach ($val as $v) : ?> {
                                      x: "<?= date("d-m-Y", strtotime($v['date'])) ?>",
                                      y: <?= handleDivision($v['actual_cg'] ?? 0, $v['budget_cg'] ?? 0) * 100 ?>,
                                    },
                                  <?php endforeach; ?>
                                ]
                              },
                            <?php endforeach; ?>
                          <?php endif; ?>
                        ],
                        chart: {
                          type: 'bar',
                          height: 350
                        },
                        plotOptions: {
                          bar: {
                            columnWidth: '60%',
                          },
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          show: true,
                          width: 2,
                          colors: ['transparent']
                        },
                        xaxis: {
                          type: 'category'
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
                          min: 5,
                          labels: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          },
                          forceNiceScale: true
                        },
                        fill: {
                          opacity: 1
                        },
                        tooltip: {
                          y: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          }
                        }
                      }).render();
                    });
                  </script>
                  <!-- End Column Chart -->

                </div>
              </div>
            </div>
          <?php elseif ($parameter == 'ob') : ?>
            <div class="col-lg-6">
              <div class="card">

                <div class="card-body">
                  <h5 class="card-title">Sum Overburden (BCM) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                  <!-- Line Chart -->
                  <div id="reportsChart2"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart2"), {
                        series: [{
                            name: "Target",
                            data: [
                              <?php foreach ($ob_lines as $ob) : ?> {
                                  x: "<?= $bulan[$ob['month']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($ob['month'])) : $ob['month']) ?>",
                                  y: <?= $ob['budget'] ?? 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          },
                          {
                            name: "Actual",
                            data: [
                              <?php foreach ($ob_lines as $ob) : ?> {
                                  x: "<?= $bulan[$ob['month']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($ob['month'])) : $ob['month']) ?>",
                                  y: <?= $ob['actual'] ?? 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          },
                        ],
                        chart: {
                          height: 350,
                          type: "area",
                          toolbar: {
                            show: true
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
                          min: 5,
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
                      }).render();
                    });
                  </script>
                  <!-- End Line Chart -->
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Contractor Performance Overburden (%) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                  <!-- Column Chart -->
                  <div id="contractorPerformanceChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#contractorPerformanceChart"), {
                        series: [
                          <?php if ($time == 'yearly' || $time == 'monthly') : ?>
                            <?php foreach ($contractor_ob as $name => $val) : ?> {
                                name: '<?= $name ?>',
                                data: [
                                  <?php if ($time == 'yearly') : ?>
                                    <?php foreach ($val as $v) : ?> {
                                        x: "<?= $v['bulan'] ?>",
                                        y: <?= handleDivision($v['actual_ob'] ?? 0, $v['budget_ob'] ?? 0) * 100 ?>,
                                      },
                                    <?php endforeach; ?>
                                  <?php elseif ($time == 'monthly') : ?>
                                    <?php for ($i = 1; $i <= 12; $i++) : ?> {
                                        x: "<?= $bulan[$i] ?>",
                                        y: <?= handleDivision($val[$i]['actual_ob'] ?? 0, $val[$i]['budget_ob'] ?? 0) * 100 ?>,
                                      },
                                    <?php endfor; ?>
                                  <?php endif; ?>
                                ]
                              },
                            <?php endforeach; ?>
                          <?php else : ?>
                            <?php foreach ($contractor_cg_ob as $name => $val) : ?> {
                                name: '<?= $name ?>',
                                data: [
                                  <?php foreach ($val as $v) : ?> {
                                      x: "<?= date("d-m-Y", strtotime($v['date'])) ?>",
                                      y: <?= handleDivision($v['actual_ob'] ?? 0, $v['budget_ob'] ?? 0) * 100 ?>,
                                    },
                                  <?php endforeach; ?>
                                ]
                              },
                            <?php endforeach; ?>
                          <?php endif; ?>
                        ],

                        chart: {
                          type: 'bar',
                          height: 350
                        },
                        plotOptions: {
                          bar: {
                            columnWidth: '60%',
                          },
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          show: true,
                          width: 2,
                          colors: ['transparent']
                        },
                        xaxis: {
                          type: 'category'
                        },
                        yaxis: {
                          title: {
                            text: 'Quantity',
                            style: {
                              color: "#fff",
                              fontSize: "1px"
                            }
                          },
                          min: 5,
                          labels: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          },
                          forceNiceScale: true,
                        },
                        fill: {
                          opacity: 1
                        },
                        tooltip: {
                          y: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          }
                        }
                      }).render();
                    });
                  </script>
                  <!-- End Column Chart -->

                </div>
              </div>
            </div>
          <?php elseif ($parameter == 'sr') : ?>
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <h3 class="card-title">Stripping Ratio <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h3>
                  <!-- Donut Chart -->
                  <div id="lineChartStrippingRatio"></div>
                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#lineChartStrippingRatio"), {
                        series: [{
                            name: "Target",
                            data: [
                              <?php foreach ($stripping_ratio as $budget) : ?> {
                                  x: "<?= $bulan[$budget['month']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($budget['month'])) : $budget['month']) ?>",
                                  y: <?= $budget['budget_cg'] ? round($budget['budget_ob'] / $budget['budget_cg'], 2) : 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          },
                          {
                            name: "Actual",
                            data: [
                              <?php foreach ($stripping_ratio as $actual) : ?> {
                                  x: "<?= $bulan[$actual['month']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($actual['month'])) : $actual['month']) ?>",
                                  y: <?= $actual['actual_cg'] != 0 ? round($actual['actual_ob'] / $actual['actual_cg'], 2) : 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          }
                        ],
                        chart: {
                          height: 350,
                          type: "area",
                          toolbar: {
                            show: true
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
                          title: {
                            text: 'Quantity',
                            style: {
                              color: "#fff",
                              fontSize: "1px"
                            }
                          },
                          min: 5,
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
                      }).render();
                    });
                  </script>
                  <!-- End Donut Chart -->
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Contractor Performance Stripping Ratio (%) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                  <!-- Column Chart -->
                  <div id="contractorPerformanceChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#contractorPerformanceChart"), {
                        series: [
                          <?php if ($time == 'yearly' || $time == 'monthly') : ?>
                            <?php foreach ($contractor_ob as $name => $val) : ?> {
                                name: '<?= $name ?>',
                                data: [
                                  <?php if ($time == 'yearly') { ?>
                                    <?php foreach ($val as $v) : ?> {
                                        <?php
                                        $target = handleDivision($v['budget_ob'] ?? 0, $contractor_cg[$name][$v['bulan']]['budget_cg'] ?? 0) * 100;
                                        $actual = handleDivision($v['actual_ob'] ?? 0, $contractor_cg[$name][$v['bulan']]['actual_cg'] ?? 0) * 100;
                                        ?>
                                        x: "<?= $v['bulan'] ?>",
                                          y: <?= handleDivision($actual, $target) * 100 ?>,
                                      },
                                    <?php endforeach; ?>
                                  <?php } elseif ($time == 'monthly') { ?>
                                    <?php for ($i = 1; $i <= 12; $i++) { ?> {
                                        <?php
                                        $target = handleDivision($val[$i]['budget_ob'] ?? 0, $contractor_cg[$name][$i]['budget_cg'] ?? 0) * 100;
                                        $actual = handleDivision($val[$i]['actual_ob'] ?? 0, $contractor_cg[$name][$i]['actual_cg'] ?? 0) * 100;
                                        ?>
                                        x: "<?= $bulan[$i] ?>",
                                          y: <?= handleDivision($actual, $target) * 100 ?>,
                                      },
                                    <?php } ?>
                                  <?php } ?>
                                ]
                              },
                            <?php endforeach; ?>
                          <?php else : ?>
                            <?php foreach ($contractor_cg_ob as $name => $val) : ?> {
                                name: '<?= $name ?>',
                                data: [
                                  <?php foreach ($val as $v) : ?> {
                                      <?php
                                      $target = handleDivision($v['budget_ob'] ?? 0, $v['budget_cg'] ?? 0) * 100;
                                      $actual = handleDivision($v['actual_ob'] ?? 0, $v['actual_cg'] ?? 0) * 100;
                                      ?>
                                      x: "<?= date("d-m-Y", strtotime($v['date'])) ?>",
                                        y: <?= handleDivision($actual, $target) * 100 ?>,
                                    },
                                  <?php endforeach; ?>
                                ]
                              },
                            <?php endforeach; ?>
                          <?php endif; ?>
                        ],
                        chart: {
                          type: 'bar',
                          height: 350
                        },
                        plotOptions: {
                          bar: {
                            columnWidth: '60%',
                          },
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          show: true,
                          width: 2,
                          colors: ['transparent']
                        },
                        xaxis: {
                          type: 'category'
                        },
                        yaxis: {
                          title: {
                            text: 'Quantity',
                            style: {
                              color: "#fff",
                              fontSize: "1px"
                            }
                          },
                          min: 5,
                          labels: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          },
                          forceNiceScale: true,
                        },
                        fill: {
                          opacity: 1
                        },
                        tooltip: {
                          y: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          }
                        }
                      }).render();
                    });
                  </script>
                  <!-- End Column Chart -->

                </div>
              </div>
            </div>
          <?php elseif ($parameter == 'distance_ob') : ?>
            <div class="col-lg-6">
              <div class="card">

                <div class="card-body">
                  <h5 class="card-title">Sum Overburden Distance (Meter) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                  <!-- Line Chart -->
                  <div id="reportsChart2"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart2"), {
                        series: [{
                            name: "Actual",
                            data: [
                              <?php foreach ($actual_distance as $ob) : ?> {
                                  x: "<?= $bulan[$ob['bulan']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($ob['bulan'])) : $ob['bulan']) ?>",
                                  y: <?= $ob['distance_ob'] != 0 ? $ob['distance_ob'] : 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          },
                          {
                            name: "Target",
                            data: [
                              <?php foreach ($actual_distance as $ob) : ?> {
                                  x: "<?= $bulan[$ob['bulan']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($ob['bulan'])) : $ob['bulan']) ?>",
                                  y: <?= $ob['target_ob'] ?? 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          },
                        ],
                        chart: {
                          height: 350,
                          type: "area",
                          toolbar: {
                            show: true
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
                          title: {
                            text: 'Quantity',
                            style: {
                              color: "#fff",
                              fontSize: "1px"
                            }
                          },
                          min: 5,
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
                      }).render();
                    });
                  </script>
                  <!-- End Line Chart -->
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Contractor Performance Overburden Distance (%) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                  <!-- Column Chart -->
                  <div id="contractorPerformanceChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#contractorPerformanceChart"), {
                        series: [
                          <?php foreach ($contractor_distance as $name => $val) : ?> {
                              name: '<?= $name ?>',
                              data: [
                                <?php foreach ($val as $v) : ?> {
                                    x: "<?= $bulan[$v['bulan']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($v['bulan'])) : $v['bulan']) ?>",
                                    y: "<?= handleDivision($v['distance_ob'] ?? 0,  $v['target_ob']) * 100 ?>",
                                  },
                                <?php endforeach; ?>
                              ]
                            },
                          <?php endforeach; ?>
                        ],
                        chart: {
                          type: 'bar',
                          height: 350
                        },
                        plotOptions: {
                          bar: {
                            columnWidth: '60%',
                          },
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          show: true,
                          width: 2,
                          colors: ['transparent']
                        },
                        xaxis: {
                          type: 'category'
                        },
                        yaxis: {
                          title: {
                            text: 'Quantity',
                            style: {
                              color: "#fff",
                              fontSize: "1px"
                            }
                          },
                          min: 5,
                          labels: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          },
                          forceNiceScale: true,
                        },
                        fill: {
                          opacity: 1
                        },
                        tooltip: {
                          y: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          }
                        }
                      }).render();
                    });
                  </script>
                  <!-- End Column Chart -->

                </div>
              </div>
            </div>
          <?php elseif ($parameter == 'distance_cg') : ?>
            <div class="col-lg-6">
              <div class="card">

                <div class="card-body">
                  <h5 class="card-title">Sum Coal Getting Distance <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                  <!-- Line Chart -->
                  <div id="reportsChart2"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart2"), {
                        series: [{
                            name: "Actual",
                            data: [
                              <?php foreach ($actual_distance as $ob) : ?> {
                                  x: "<?= $bulan[$ob['bulan']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($ob['bulan'])) : $ob['bulan']) ?>",
                                  y: <?= $ob['distance_cg'] != 0 ? $ob['distance_cg'] : 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          },
                          {
                            name: "Target",
                            data: [
                              <?php foreach ($actual_distance as $ob) : ?> {
                                  x: "<?= $bulan[$ob['bulan']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($ob['bulan'])) : $ob['bulan']) ?>",
                                  y: <?= $ob['target_cg'] ?? 0 ?>
                                },
                              <?php endforeach ?>
                            ]
                          }
                        ],
                        chart: {
                          height: 350,
                          type: "area",
                          toolbar: {
                            show: true
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
                          title: {
                            text: 'Quantity',
                            style: {
                              color: "#fff",
                              fontSize: "1px"
                            }
                          },
                          min: 5,
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
                      }).render();
                    });
                  </script>
                  <!-- End Line Chart -->
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Contractor Performance Coal Getting Distance (%) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

                  <!-- Column Chart -->
                  <div id="contractorPerformanceChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#contractorPerformanceChart"), {
                        series: [
                          <?php foreach ($contractor_distance as $name => $val) : ?> {
                              name: '<?= $name ?>',
                              data: [
                                <?php foreach ($val as $v) : ?> {
                                    x: "<?= $bulan[$v['bulan']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($v['bulan'])) : $v['bulan']) ?>",
                                    y: "<?= handleDivision($v['distance_cg'] ?? 0, $v['target_cg']) * 100 ?>",
                                  },
                                <?php endforeach; ?>
                              ]
                            },
                          <?php endforeach; ?>
                        ],
                        chart: {
                          type: 'bar',
                          height: 350
                        },
                        plotOptions: {
                          bar: {
                            columnWidth: '60%',
                          },
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          show: true,
                          width: 2,
                          colors: ['transparent']
                        },
                        xaxis: {
                          type: 'category'
                        },
                        yaxis: {
                          title: {
                            text: 'Quantity',
                            style: {
                              color: "#fff",
                              fontSize: "1px"
                            }
                          },
                          min: 5,
                          labels: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          },
                          forceNiceScale: true,
                        },
                        fill: {
                          opacity: 1
                        },
                        tooltip: {
                          y: {
                            formatter: function(value) {
                              return value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                              });
                            }
                          }
                        }
                      }).render();
                    });
                  </script>
                  <!-- End Column Chart -->

                </div>
              </div>
            </div>
          <?php endif; ?>
          <!-- End Reports -->

          <!-- Recent Sales -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">

              <div class="card-body">
                <div class="row card-title">
                  <div class="col">
                    <h5 class="">Contractor Performance <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>
                  </div>
                  <div class="col">
                    <button class="btn btn-primary float-right" id='download-btn-cp'>download</button>
                  </div>
                </div>
                <table class="table table-bordered" id="contractor-performance">
                  <thead>
                    <tr>
                      <th scope="col">Contractor</th>
                      <th scope="col"><?= $time == 'yearly' ? "Year" : "Time" ?></th>
                      <th scope="col">Target OB (BCM)</th>
                      <th scope="col">Actual OB (BCM)</th>
                      <th scope="col">Ach. OB</th>
                      <th scope="col">Target CG (MT)</th>
                      <th scope="col">Actual CG (MT)</th>
                      <th scope="col">Ach. CG</th>
                      <th scope="col">Target SR</th>
                      <th scope="col">Actual SR</th>
                      <th scope="col">Ach. SR</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($time == 'yearly' || $time == 'monthly') : ?>
                      <?php foreach ($contractor_ob as $name => $val) : ?>
                        <?php if ($time == 'yearly') { ?>
                          <?php foreach ($val as $v) : ?>
                            <tr>
                              <?php
                              $actual_sr = handleDivision($v['actual_ob'] ?? 0, $contractor_cg[$name][$v['bulan']]['actual_cg'] ?? 0);
                              $budget_sr = handleDivision($v['budget_ob'] ?? 0, $contractor_cg[$name][$v['bulan']]['budget_cg'] ?? 0);
                              $achievement_ob = handleDivision($v['actual_ob'] ?? 0, $v['budget_ob'] ?? 0) * 100;
                              $achievement_cg = handleDivision($contractor_cg[$name][$v['bulan']]['actual_cg'] ?? 0, $contractor_cg[$name][$v['bulan']]['budget_cg'] ?? 0) * 100;
                              $ratio = handleDivision($actual_sr, $budget_sr) * 100;
                              ?>
                              <td><?= $name ?></td>
                              <td><?= $v['bulan'] ?></td>
                              <td><?= number_format($v['budget_ob'] ?? 0, 2, ',', '.') ?></td>
                              <td><?= number_format($v['actual_ob'] ?? 0, 2, ',', '.') ?></td>
                              <td><?= number_format($achievement_ob, 2, ',', '.') . " %" ?></td>
                              <td><?= number_format($contractor_cg[$name][$v['bulan']]['budget_cg'] ?? 0, 2, ',', '.')  ?></td>
                              <td><?= number_format($contractor_cg[$name][$v['bulan']]['actual_cg'] ?? 0, 2, ',', '.')  ?></td>
                              <td><?= number_format($achievement_cg, 2, ',', '.') . " %" ?></td>
                              <td><?= number_format($budget_sr, 2, ',', '.') ?></td>
                              <td><?= number_format($actual_sr, 2, ',', '.') ?></td>
                              <td><?= number_format($ratio, 2, ',', '.') . "%" ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php } elseif ($time == 'monthly') { ?>
                          <tr>
                            <td rowspan="13"><?= $name ?></td>
                          </tr>
                          <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <tr>
                              <?php
                              $actual_sr = handleDivision($val[$i]['actual_ob'] ?? 0, $contractor_cg[$name][$i]['actual_cg'] ?? 0);
                              $budget_sr = handleDivision($val[$i]['budget_ob'] ?? 0,  $contractor_cg[$name][$i]['budget_cg']  ?? 0);
                              $achievement_ob = handleDivision($val[$i]['actual_ob'] ?? 0, $val[$i]['budget_ob'] ?? 0) * 100;
                              $achievement_cg = handleDivision($contractor_cg[$name][$i]['actual_cg']  ?? 0,  $contractor_cg[$name][$i]['budget_cg']  ?? 0) * 100;
                              $ratio = handleDivision($actual_sr, $budget_sr) * 100;
                              ?>
                              <td><?= $bulan[$i] ?></td>
                              <td><?= number_format($val[$i]['budget_ob'] ?? 0, 2, ',', '.') ?></td>
                              <td><?= number_format($val[$i]['actual_ob'] ?? 0, 2, ',', '.') ?></td>
                              <td><?= number_format($achievement_ob, 2, ',', '.') . " %" ?></td>
                              <td><?= number_format($contractor_cg[$name][$i]['budget_cg']  ?? 0, 2, ',', '.')  ?></td>
                              <td><?= number_format($contractor_cg[$name][$i]['actual_cg'] ?? 0, 2, ',', '.')  ?></td>
                              <td><?= number_format($achievement_cg, 2, ',', '.') . " %" ?></td>
                              <td><?= number_format($budget_sr, 2, ',', '.') ?></td>
                              <td><?= number_format($actual_sr, 2, ',', '.') ?></td>
                              <td><?= number_format($ratio, 2, ',', '.') . "%" ?></td>
                            </tr>
                          <?php } ?>
                        <?php } ?>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <?php foreach ($contractor_cg_ob as $name => $val) : ?> {
                        <?php foreach ($val as $v) : ?>
                          <tr>
                            <?php
                            $actual_sr = handleDivision($v['actual_ob'] ?? 0, $v['actual_cg'] ?? 0);
                            $budget_sr = handleDivision($v['budget_ob'] ?? 0, $v['budget_cg'] ?? 0);
                            $achievement_ob = handleDivision($v['actual_ob'] ?? 0, $v['budget_ob'] ?? 0) * 100;
                            $achievement_cg = handleDivision($v['actual_cg'] ?? 0, $v['budget_cg'] ?? 0) * 100;
                            $ratio = handleDivision($actual_sr, $budget_sr) * 100;
                            ?>
                            <td><?= $name ?></td>
                            <td><?= date("d M Y", strtotime($v['date'])) ?></td>
                            <td><?= number_format($v['budget_ob'] ?? 0, 2, ',', '.') ?></td>
                            <td><?= number_format($v['actual_ob'] ?? 0, 2, ',', '.') ?></td>
                            <td><?= number_format($achievement_ob, 2, ',', '.') . " %" ?></td>
                            <td><?= number_format($v['budget_cg'] ?? 0, 2, ',', '.')  ?></td>
                            <td><?= number_format($v['actual_cg'] ?? 0, 2, ',', '.')  ?></td>
                            <td><?= number_format($achievement_cg, 2, ',', '.') . " %" ?></td>
                            <td><?= number_format($budget_sr, 2, ',', '.') ?></td>
                            <td><?= number_format($actual_sr, 2, ',', '.') ?></td>
                            <td><?= number_format($ratio, 2, ',', '.') . "%" ?></td>
                          </tr>
                        <?php endforeach; ?>
                        },
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
                <script>
                  // const cperformance = document.getElementById('contractor-performance');
                  // const dt = new simpleDatatables.DataTable(cperformance);
                  // dt.export({type: 'csv', download: true});
                </script>
              </div>
            </div>
          </div>
          <!-- End Recent Sales -->

        </div>
      </div>
      <!-- End Left side columns -->

      <!-- Right side columns -->
      <!-- End Right side columns -->
    </div>
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Sum Crushed Coal (MT) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

            <!-- Column Chart -->
            <div id="ccChart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#ccChart"), {
                  series: [{
                      name: 'Plan',
                      type: 'line',
                      data: [
                        <?php if ($time == 'monthly' || $time == 'yearly') { ?>
                          <?php foreach ($plan_cc as $p) : ?> {
                              x: "<?= $bulan[$p['bulan']] ?? $p['bulan'] ?>",
                              y: <?= $p['budget'] ?? 0 ?>,
                            },
                          <?php endforeach; ?>
                        <?php } else { ?>
                          <?php foreach ($date_period as $d) : ?> {
                              x: "<?= date("d-m-Y", strtotime($d)) ?>",
                              y: <?= $plan_cc['plan'] ?? 0 ?>
                            },
                          <?php endforeach; ?>
                        <?php } ?>
                      ]
                    },
                    <?php foreach ($sum_cc as $name => $val) : ?> {
                        name: '<?= $name ?>',
                        type: 'bar',
                        data: [
                          <?php if ($time == 'monthly' || $time == 'yearly') { ?>
                            <?php foreach ($val as $v) : ?> {
                                x: "<?= $bulan[$v['bulan']] ?? $v['bulan'] ?>",
                                y: <?= $v['total'] ?? 0 ?>,
                              },
                            <?php endforeach; ?>
                          <?php } else { ?>
                            <?php foreach ($date_period as $d) : ?> {
                                x: "<?= $d ?>",
                                y: <?= $val[$d] ?? 0 ?>
                              },
                            <?php endforeach; ?>
                          <?php } ?>
                        ]
                      },
                    <?php endforeach; ?>
                  ],
                  chart: {

                    height: 350,
                    stacked: true,
                  },
                  plotOptions: {
                    bar: {
                      columnWidth: '60%',
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                  },
                  xaxis: {
                    type: 'category'
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
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    },
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    }
                  }
                }).render();
              });
            </script>
            <!-- End Column Chart -->

          </div>
        </div>
      </div>
      <!-- <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Sum Hauling to Port Chart (Ritase)</h5>
            <div id="haulChart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#haulChart"), {
                  series: [{
                      name: 'CV FAB Ritase',
                      data: [{
                          x: "Jan",
                          y: 120
                        },
                        {
                          x: "Feb",
                          y: 300
                        },
                        {
                          x: "Mar",
                          y: 250
                        },
                        {
                          x: "Apr",
                          y: 300
                        },
                        {
                          x: "May",
                          y: 400
                        },
                        {
                          x: "Jun",
                          y: 500
                        },
                      ]
                    },
                    {
                      name: 'PT NSU Ritase',
                      data: [{
                          x: "Jan",
                          y: 120
                        },
                        {
                          x: "Feb",
                          y: 300
                        },
                        {
                          x: "Mar",
                          y: 400
                        },
                        {
                          x: "Apr",
                          y: 300
                        },
                        {
                          x: "May",
                          y: 400
                        },
                        {
                          x: "Jun",
                          y: 500
                        },
                      ]
                    }
                  ],
                  chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                  },
                  plotOptions: {
                    bar: {
                      columnWidth: '60%',
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                  },
                  xaxis: {
                    type: 'category'
                  },
                  yaxis: {
                    title: {
                      text: 'Quantity',
                      style: {
                        color: "#fff",
                        fontSize: "1px"
                      }
                    },
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    },
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    }
                  }
                }).render();
              });
            </script>

          </div>
        </div>
      </div> -->
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Sum Hauling to Port (MT) <span>| <?= $time == 'Yearly' ? "YTD" : ucfirst($time) ?></span></h5>

            <!-- Column Chart -->
            <div id="tonaseChart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#tonaseChart"), {
                  series: [
                    <?php if (isset($hauling_plan)) : ?> {
                        name: 'Plan',
                        type: 'line',
                        data: [
                          <?php foreach ($hauling_plan as $h) : ?> {
                              x: "<?= $bulan[$h['month']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($h['month'])) : $h['month']) ?>",
                              y: <?= round($h['plan'] ?? 0, 2) ?>
                            },
                          <?php endforeach; ?>
                        ],
                      },
                    <?php endif; ?>
                    <?php foreach ($sum_tonase as $name => $val) : ?> {
                        name: '<?= $name ?>',
                        data: [
                          <?php foreach ($val as $v) : ?> {
                              x: "<?= $bulan[$v['bulan']] ?? ($time == 'daily' ? date("d-m-Y", strtotime($v['bulan'])) : $v['bulan']) ?>",
                              y: <?= $v['total'] ?? 0 ?>,
                            },
                          <?php endforeach; ?>
                        ],
                        type: 'bar',
                      },
                    <?php endforeach; ?>
                  ],
                  chart: {
                    height: 350,
                    stacked: true,
                  },
                  plotOptions: {
                    bar: {
                      columnWidth: '60%',
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                  },
                  xaxis: {
                    type: 'category'
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
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    },
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    }
                  }
                }).render();
              });
            </script>
            <!-- End Column Chart -->

          </div>
        </div>
      </div>
    </div>
    <!-- sales -->
    <div class="row">
      <!-- Total Shipment Card -->
      <div class="col-xxl-4 col-md-4">
        <div class="card info-card revenue-card">
          <div class="card-body" style="height: 150px;">
            <h5 class="card-title">Total Shipment <span>| (Contract)</span></h5>

            <div class="d-flex align-items-center">
              <!-- <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <img height="50" src="<?= base_url("assets/img/cruise.png") ?>" alt="">
              </div> -->
              <div class="ps-3">
                <h6><?= number_format($total_shipment['total'] ?? 0, 2, ',', '.') ?> MT</h6>
                <span class="text-success small pt-1 fw-bold"><?= number_format($total_shipment['target'] != 0 ? ($total_shipment['total'] / $total_shipment['target']) * 100 : 0, 2, ',', '.') . '%' ?></span>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Total Shipment Card -->
      <!-- Average Price Card -->
      <div class="col-xxl-4 col-md-4">
        <div class="card info-card revenue-card">
          <div class="card-body" style="height: 150px;">
            <h5 class="card-title">Average Price MT <span>| (Contract)</span></h5>

            <div class="d-flex align-items-center">
              <!-- <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-currency-dollar"></i>
              </div> -->
              <div class="ps-3">
                <h6><?= number_format($average_price['final_price'] ?? 0, 2, ',', '.') ?> IDR</h6>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Average Price Card -->
      <!-- Total Sales Card -->
      <div class="col-xxl-4 col-md-4">
        <div class="card info-card revenue-card">
          <div class="card-body" style="height: 150px;">
            <h5 class="card-title">Sales</h5>

            <div class="d-flex align-items-center">
              <!-- <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-bar-chart"></i>
              </div> -->
              <div class="ps-3">
                <h6><?= number_format($total_sales['total'] / 1000000, 2, ',', '.') ?> M IDR</h6>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Total Sales Card -->
    </div>
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body" style="height: 600px">
            <h5 class="card-title">Local vs Export (%)</h5>

            <!-- Pie Chart -->
            <div id="pieChart" style="margin-top: 5%;"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#pieChart"), {
                  series: [{
                      name: 'Local',
                      data: [
                        <?php for ($x = 1; $x <= 12; $x++) { ?> {
                            x: '<?= $bulan[$x] ?>',
                            y: <?= handleDivision($export_vs_local['Local'][$x] * 100, ($export_vs_local['Local'][$x] + $export_vs_local['Export'][$x])) ?>
                          },
                        <?php } ?>
                      ],
                    },
                    {
                      name: 'Export',
                      data: [
                        <?php for ($x = 1; $x <= 12; $x++) { ?> {
                            x: '<?= $bulan[$x] ?>',
                            y: <?= handleDivision($export_vs_local['Export'][$x] * 100, ($export_vs_local['Local'][$x] + $export_vs_local['Export'][$x])) ?>
                          },
                        <?php } ?>
                      ],
                    }
                  ],

                  chart: {
                    type: 'bar',
                    height: 480,
                    stacked: true
                  },
                  plotOptions: {
                    bar: {
                      columnWidth: '60%',
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                  },
                  xaxis: {
                    type: 'category'
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
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    },
                    forceNiceScale: true
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    }
                  }
                }).render();
              });
            </script>
            <!-- End Pie Chart -->

          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="mx-3 row">
            <div class="col-8 my-3">
              <div class="btn-group" role="group" aria-label="Basic example">
                <button onclick="fetchShipmentList('qty')" type="button" class="btn btn-primary <?= $parameter_ship == 'qty' ? "active" : '' ?>">Quantity</button>
                <button onclick="fetchShipmentList('price')" type="button" class="btn btn-primary <?= $parameter_ship == 'price' ? "active" : '' ?>">Price</button>
                <button onclick="fetchShipmentList('rev')" type="button" class="btn btn-primary <?= $parameter_ship == 'rev' ? "active" : '' ?>">Revenue</button>
              </div>
            </div>
            <div class="col-4 my-3">
              <button class="btn btn-primary float-right" id='download-btn-ts'>download</button>
            </div>
          </div>
          <div class="card-body my-3 table-responsive" style="height: 500px;">
            <table class="table table-sm">
              <thead>
                <tr class="text-center">
                  <th rowspan="2">Period</th>
                  <th colspan="3">Local</th>
                  <th colspan="3">Export</th>
                </tr>
                <tr class="text-center">
                  <th>FOB Barge <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                  <th>CIF <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                  <th>Franco Pabrik <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                  <th>FOB Barge <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                  <th>CIF <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                  <th>MV <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($shipment_list as $id => $sl) : ?>
                  <tr>
                    <td><?= $bulan[$id] ?></td>
                    <td><?= number_format($sl['Local']['FOB BARGE'] ?? 0, 2, ',', '.') ?></td>
                    <td><?= number_format($sl['Local']['CIF'] ?? 0, 2, ',', '.') ?></td>
                    <td><?= number_format($sl['Local']['FRANCO PABRIK'] ?? 0, 2, ',', '.') ?></td>
                    <td><?= number_format($sl['Export']['FOB BARGE'] ?? 0, 2, ',', '.') ?></td>
                    <td><?= number_format($sl['Export']['CIF'] ?? 0, 2, ',', '.') ?></td>
                    <td><?= number_format($sl['Export']['MV'] ?? 0, 2, ',', '.') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div class="dont-show">
              <table class="table table-sm" id="table-sales">
                <thead>
                  <tr class="text-center">
                    <th>Period</th>
                    <th>(Local) FOB Barge <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                    <th>(Local) CIF <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                    <th>(Local) Franco Pabrik <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                    <th>(Export) FOB Barge <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                    <th>(Export) CIF <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                    <th>(Export) MV <span>(<?= $parameter_ship == 'qty' ? "MT" : "IDR" ?>)</span></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($shipment_list as $id => $sl) : ?>
                    <tr>
                      <td><?= $bulan[$id] ?></td>
                      <td><?= number_format($sl['Local']['FOB BARGE'] ?? 0, 2, ',', '.') ?></td>
                      <td><?= number_format($sl['Local']['CIF'] ?? 0, 2, ',', '.') ?></td>
                      <td><?= number_format($sl['Local']['FRANCO PABRIK'] ?? 0, 2, ',', '.') ?></td>
                      <td><?= number_format($sl['Export']['FOB BARGE'] ?? 0, 2, ',', '.') ?></td>
                      <td><?= number_format($sl['Export']['CIF'] ?? 0, 2, ',', '.') ?></td>
                      <td><?= number_format($sl['Export']['MV'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">

          <div class="card-body">
            <h5 class="card-title">Coal Index Time Series (IDR)</h5>

            <!-- Line Chart -->
            <div id="coalIndexTimeChart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#coalIndexTimeChart"), {
                  series: [{
                      name: "ICI 4200 Index",
                      data: [
                        <?php foreach ($coal_index as $ic) : ?> {
                            x: "<?= $bulan[$ic['bulan']] ?>",
                            y: <?= $ic['ic_total'] ?? 0 ?>
                          },
                        <?php endforeach; ?>
                      ]
                    },
                    {
                      name: "EBL",
                      data: [
                        <?php foreach ($coal_index as $ic) : ?> {
                            x: "<?= $bulan[$ic['bulan']] ?>",
                            y: <?= $ic['newc_total'] ?? 0 ?>
                          },
                        <?php endforeach; ?>
                      ]
                    },
                    // {
                    //   name: "MV",
                    //   data: [
                    //     <?php foreach ($coal_index as $ic) : ?> {
                    //         x: "<?= $bulan[$ic['bulan']] ?>",
                    //         y: <?= $ic['mv'] ?? 0 ?>
                    //       },
                    //     <?php endforeach; ?>
                    //   ]
                    // },
                    // {
                    //   name: "Harga Jual",
                    //   data: [
                    //     <?php foreach ($harga_jual as $h) : ?> {
                    //         y: <?= $h['price']; ?>,
                    //         x: "<?= $bulan[$h['bulan']] ?>"
                    //       },
                    //     <?php endforeach; ?>
                    //   ]
                    // }
                  ],
                  chart: {
                    height: 350,
                    type: "area",
                    toolbar: {
                      show: true
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
                    min: 5,
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
                }).render();
              });
            </script>
            <!-- End Line Chart -->
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Domestic Market Obligation(DMO)</h5>

            <!-- Column Chart -->
            <div id="dmoChart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#dmoChart"), {
                  series: [{
                      name: 'Kelistrikan',
                      data: [
                        <?php foreach ($dmo_summary as $ic) : ?> {
                            x: "<?= $bulan[$ic['bulan']] ?>",
                            y: <?= round($ic['listrik'] ?? 0, 2) ?>
                          },
                        <?php endforeach; ?>
                      ],
                      type: 'bar',
                    },
                    {
                      name: 'Non Kelistrikan',
                      data: [
                        <?php foreach ($dmo_summary as $ic) : ?> {
                            x: "<?= $bulan[$ic['bulan']] ?>",
                            y: <?= round($ic['nonlistrik'] ?? 0, 2) ?>
                          },
                        <?php endforeach; ?>
                      ],
                      type: 'bar',
                    },
                    {
                      name: 'Plan',
                      data: [
                        <?php foreach ($dmo_summary as $ic) : ?> {
                            x: "<?= $bulan[$ic['bulan']] ?>",
                            y: <?= round($ic['target'] ?? 0, 2) ?>
                          },
                        <?php endforeach; ?>
                      ],
                      type: 'line'
                    }
                  ],
                  chart: {
                    height: 350,
                    stacked: true,
                  },
                  plotOptions: {
                    bar: {
                      columnWidth: '60%',
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                  },
                  xaxis: {
                    type: 'category'
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
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    },
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    }
                  }
                }).render();
              });
            </script>
            <!-- End Column Chart -->

          </div>
        </div>
      </div>
      <!-- Index Exchange Rate -->
      <!-- <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Index Exchange Rate</h5>
            <div id="harga_jual"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#harga_jual"), {
                  series: [{
                    name: "Harga Jual",
                    data: [
                      <?php foreach ($harga_jual as $h) : ?> {
                          y: <?= $h['price']; ?>,
                          x: "<?= $bulan[$h['bulan']] ?>"
                        },
                      <?php endforeach; ?>
                    ]
                  }],
                  chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                      enabled: false
                    }
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    curve: 'straight'
                  },
                  grid: {
                    row: {
                      colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                      opacity: 0.5
                    },
                  },
                  xaxis: {
                    type: 'category',
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
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    },
                  },
                }).render();
              });
            </script>

          </div>
        </div>
      </div> -->
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Barging (MT)</h5>

            <!-- Column Chart -->
            <div id="bargingChart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#bargingChart"), {
                  series: [{
                      name: 'Barging',
                      data: [
                        <?php foreach ($barging as $b) : ?> {
                            x: "<?= $bulan[$b['bulan']] ?>",
                            y: <?= $b['total'] ?>,
                          },
                        <?php endforeach; ?>
                      ]
                    },
                    {
                      name: 'Target',
                      data: [
                        <?php foreach ($barging as $b) : ?> {
                            x: "<?= $bulan[$b['bulan']] ?>",
                            y: <?= $b['target'] ?>,
                          },
                        <?php endforeach; ?>
                      ]
                    }
                  ],
                  chart: {
                    type: 'line',
                    height: 350
                  },
                  plotOptions: {
                    bar: {
                      columnWidth: '60%',
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                  },
                  xaxis: {
                    type: 'category'
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
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    },
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    }
                  }
                }).render();
              });
            </script>
            <!-- End Column Chart -->

          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">

          <div class="card-body">
            <h5 class="card-title">Production, Hauling, and Sales (MT)</h5>

            <!-- Line Chart -->
            <div id="prdVsSalesChart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#prdVsSalesChart"), {
                  series: [{
                      name: "Production",
                      data: [
                        <?php foreach ($prd_prod as $p) { ?> {
                            x: "<?= $bulan[$p['bulan']] ?>",
                            y: <?= $p['total'] ?? 0 ?>
                          },
                        <?php } ?>
                      ]
                    },
                    {
                      name: "Hauling",
                      data: [
                        <?php foreach ($prd_hauling as $p) { ?> {
                            x: "<?= $bulan[$p['bulan']] ?>",
                            y: <?= $p['total'] ?? 0 ?>
                          },
                        <?php } ?>
                      ]
                    },
                    {
                      name: "Sales",
                      data: [
                        <?php foreach ($prd_sales as $p) { ?> {
                            x: "<?= $bulan[$p['bulan']] ?>",
                            y: <?= $p['total'] ?? 0 ?>
                          },
                        <?php } ?>
                      ]
                    },
                  ],
                  chart: {
                    height: 350,
                    type: "area",
                    // stacked: true,
                    toolbar: {
                      show: true
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
                    categories: <?= json_encode(array_values($bulan)) ?>, //bisa menggunakan ini atau langsung dalam series, sesuai kebutuhan
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
                    min: 5,
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
                }).render();
              });
            </script>
            <!-- End Line Chart -->
          </div>
        </div>
      </div>
      <div class="col-12 my-3">
        <div class="btn-group" role="group" aria-label="Basic example">
          <button onclick="fetchCostContractor('total')" type="button" class="btn btn-primary <?= $cc_parameter == 'total' ? "active" : '' ?>">Total</button>
          <?php foreach ($cost_types as $ct) : ?>
            <button onclick="fetchCostContractor('<?= $ct['id_costtype'] ?>')" type="button" class="btn btn-primary <?= $cc_parameter == $ct['id_costtype'] ? "active" : '' ?>"><?= ucwords($ct['cost_type']) ?></button>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-12">
        <div class="card">

          <div class="card-body">
            <h5 class="card-title">Cost Contractor (IDR)</h5>

            <!-- Line Chart -->
            <div id="costMining"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#costMining"), {
                  series: [
                    <?php foreach ($cost_mining_contractors as $c) : ?> {
                        name: "<?= $c['contractor'] ?>",
                        data: [
                          <?php for ($x = 1; $x <= 12; $x++) { ?> {
                              x: "<?= $bulan[$x] ?>",
                              y: <?= $cost_minings[$c['contractor']][$x] ?? 0 ?>
                            },
                          <?php } ?>
                        ]
                      },
                    <?php endforeach; ?>
                  ],
                  chart: {
                    height: 350,
                    type: "area",
                    toolbar: {
                      show: true
                    },
                  },
                  markers: {
                    size: 4,
                  },
                  colors: ["#006400", "#483D8B", "#191970", "#FF8C00", "#BDB76B", "#A52A2A", "#FF7F50", "#191970"],
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
                    min: 5,
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
                }).render();
              });
            </script>
            <!-- End Line Chart -->
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Inventory stock (MT)</h5>

            <!-- Column Chart -->
            <div id="unrestrictedStock"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#unrestrictedStock"), {
                  series: [{
                    name: 'ICV',
                    data: [
                      <?php foreach ($unrestricted_stock as $y => $b) : ?> {
                          x: "<?= $y ?>",
                          y: <?= $b ?? 0 ?>,
                        },
                      <?php endforeach; ?>
                    ]
                  }],
                  chart: {
                    type: 'bar',
                    height: 350
                  },
                  plotOptions: {
                    bar: {
                      columnWidth: '60%',
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                  },
                  xaxis: {
                    type: 'category'
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
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    },
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    }
                  }
                }).render();
              });
            </script>
            <!-- End Column Chart -->

          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Inventory Closing Value (MT)</h5>

            <!-- Column Chart -->
            <div id="InventoryClosingValue"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#InventoryClosingValue"), {
                  series: [{
                      name: 'ROM Stock',
                      data: [
                        <?php foreach ($inventory_closing_value as $v) : ?> {
                            x: "<?= $bulan[$v['bulan']] ?>",
                            y: <?= $v['rom_stock'] ?? 0 ?>,
                          },
                        <?php endforeach; ?>
                      ]
                    },
                    {
                      name: 'Crusher Stock',
                      data: [
                        <?php foreach ($inventory_closing_value as $v) : ?> {
                            x: "<?= $bulan[$v['bulan']] ?>",
                            y: <?= $v['crusher_stock'] ?? 0 ?>,
                          },
                        <?php endforeach; ?>
                      ]
                    }, {
                      name: 'Port Stock',
                      data: [
                        <?php foreach ($inventory_closing_value as $v) : ?> {
                            x: "<?= $bulan[$v['bulan']] ?>",
                            y: <?= $v['port_stock'] ?? 0 ?>,
                          },
                        <?php endforeach; ?>
                      ]
                    }
                  ],
                  chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                  },
                  plotOptions: {
                    bar: {
                      columnWidth: '60%',
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                  },
                  xaxis: {
                    type: 'category'
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
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    },
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 2
                        });
                      }
                    }
                  }
                }).render();
              });
            </script>
            <!-- End Column Chart -->

          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12" id="fetchBalanceSheet"></div>
      <div class="col-lg-12" id="fetchProfitLoss"></div>
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title text-center">Cash Flow Report</h5>
            <p class="card-subtitle text-center">(In Rupiah)</p>
            <br>
            <div class="table-responsive">
              <table class="table table-sm" id="fetchCashflowReport">
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      $(document).ready(function() {
        let dataTable = new simpleDatatables.DataTable("#contractor-performance", {
          perPage: 13,
        });
        let dataTableSales = new simpleDatatables.DataTable("#table-sales", {
          searchable: false,
        });
        const btnDownloadCP = document.getElementById('download-btn-cp');
        const btnDownloadTS = document.getElementById('download-btn-ts');
        btnDownloadCP.addEventListener('click', function() {
          dataTable.export({
            type: 'csv',
            download: true,
            filename: "contractor-performance-per-" + new Date(),
            // separator: ';'
          })
        });
        btnDownloadTS.addEventListener('click', function() {
          dataTableSales.export({
            type: 'csv',
            download: true,
            filename: "table-sal-per-" + new Date(),
            // separator: ';'
          })
        });
        <?php if (!$month && !$year) : ?>
          $("#fetchBalanceSheet").load('<?= site_url("finance/balance") ?> #targetBalanceSheet', function() {
            document.querySelectorAll("#fetchBalanceSheet table th")[1].style = 'text-align: right';
          });
          $("#fetchProfitLoss").load('<?= site_url("finance/profit") ?> #targetProfitLoss', function() {
            $('#dateRangeCashflowProfit').daterangepicker({
              showDropdowns: true,
              autoUpdateInput: false,
              minMonth: new Date().getMonth(),
              locale: {
                format: 'DD/MM/YYYY'
              },
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
            document.querySelectorAll("#fetchProfitLoss table tr th:nth-child(n+2)").forEach(x => x.style = 'text-align: right');
          });
          $("#fetchCashflowReport").load('<?= site_url("finance/cashflow") ?> #targetCashflowReport', function() {
            document.querySelectorAll("#targetCashflowReport tr td:nth-of-type(4)").forEach(x => x.remove());
            document.querySelectorAll("#targetCashflowReport tr th:nth-of-type(4)").forEach(x => x.remove());
            document.querySelectorAll("#targetCashflowReport tr th:nth-child(n+2)").forEach(x => x.style = 'text-align:right');
          });
        <?php else : ?>
          <?php
          $temp_profit_query = '';
          if ($start_date_profit && $end_date_profit) {
            $temp_profit_query = "&start_profit=$start_date_profit&end_profit=$end_date_profit";
          }
          ?>
          $("#fetchBalanceSheet").load('<?= site_url("finance/balance") . "?month=$month&year=$year" ?> #targetBalanceSheet', function() {
            document.querySelectorAll("#fetchBalanceSheet table th")[1].style = 'text-align: right';
          });
          $("#fetchProfitLoss").load('<?= site_url("finance/profit") . "?month=$month&year=$year" . $temp_profit_query ?> #targetProfitLoss', function() {
            $('#dateRangeCashflowProfit').daterangepicker({
              showDropdowns: true,
              autoUpdateInput: false,
              minMonth: new Date().getMonth(),
              locale: {
                format: 'DD/MM/YYYY'
              },
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
            document.querySelectorAll("#fetchProfitLoss table tr th:nth-child(n+2)").forEach(x => x.style = 'text-align: right');
          });
          $("#fetchCashflowReport").load('<?= site_url("finance/cashflow") . "?month=$month&year=$year" ?> #targetCashflowReport', function() {
            document.querySelectorAll("#targetCashflowReport tr td:nth-of-type(4)").forEach(x => x.remove());
            document.querySelectorAll("#targetCashflowReport tr th:nth-of-type(4)").forEach(x => x.remove());
            document.querySelectorAll("#targetCashflowReport tr th:nth-child(n+2)").forEach(x => x.style = 'text-align:right');
          });
        <?php endif ?>
      });
    </script>
  </section>
</main>
<script>
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
  const fetchData = function() {
    const month = document.getElementById("monthFilter");
    const year = document.getElementById("yearFilter");
    const type = document.getElementById('typeFilter');
    const date = document.getElementById('literalDateFilter');
    if (month.value != '' && year.value != '' && type.value != '') {
      let url = updateURLParameter(window.location.href, 'month', month.value);
      url = updateURLParameter(url, 'year', year.value);
      url = updateURLParameter(url, 'time', type.value);
      url = deleteURLParameter(url, 'date');
      window.location.href = url;
    }
    if (month.value != '' && year.value != '' && type.value != '' && date.value != '') {
      let url = updateURLParameter(window.location.href, 'month', month.value);
      url = updateURLParameter(url, 'year', year.value);
      url = updateURLParameter(url, 'time', type.value);
      url = updateURLParameter(url, 'date', date.value);
      window.location.href = url;
    }
  }

  const fetchChartType = function(type) {
    let url = updateURLParameter(window.location.href, 'type', type);
    window.location.href = url;
  }

  const fetchShipmentList = function(type) {
    let url = updateURLParameter(window.location.href, 'ship_type', type);
    window.location.href = url;
  }

  const fetchCostContractor = function(type) {
    let url = updateURLParameter(window.location.href, 'cc_type', type);
    window.location.href = url;
  }
</script>
<!-- End #main -->
<?= $this->endSection() ?>