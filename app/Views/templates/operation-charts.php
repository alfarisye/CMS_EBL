<?php
$bulan = [
    1 => "Januari",
    2 => "Febuari",
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
    .filter {
        position: absolute;
        right: 1em;
        top: 15px;
    }
</style>
<!-- first row (sum of production qty) -->
<div class="row">
    <!-- overburden -->
    <div class="col-lg-4">
        <div class="card">
            <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" onclick="updateDaySumOB()" href="#">Today</a></li>
                    <li><a class="dropdown-item" onclick="updateMonthSumOB()" href="#">This Month</a></li>
                    <li><a class="dropdown-item" onclick="updateYearSumOB()" href="#">This Year</a></li>
                    <li class="dropdown-item"><input type="text" class="form-control" id="ob-sum-production-date" name="dates" required></li>
                </ul>
            </div>
            <div class="card-body">
                <h3 class="card-title">Over Burden</h3>
                <!-- Donut Chart -->
                <div id="donutChart2"></div>
                <script>
                    <?php
                    $percentage = $ob_sum_production['budget'] > 0 ? round($ob_sum_production['actual'] / $ob_sum_production['budget'] * 100, 2) : 0;
                    $budget = 100 - $percentage; ?>
                    let chart2;
                    const updateMonthSumOB = function() {
                        fetch("<?= site_url('operation/operation-dashboard/sum-ob/this-month') ?>")
                            .then(res => res.json())
                            .then(data => {
                                let actual = parseFloat(data[0]);
                                let budget = parseFloat(data[1]);
                                let adjust = parseFloat(data[2]);
                                let actual_percent = data[1] > 0 ? ((actual + adjust) / budget * 100).toFixed(2) : 0;
                                console.log((actual + budget) / budget);
                                let budget_percent = 100 - actual_percent;
                                $("#act2").text((actual + adjust).toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                $("#bdgt2").text(budget.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                chart2.updateSeries([actual_percent]);
                            });
                    };
                    const updateDaySumOB = function() {
                        fetch("<?= site_url('operation/operation-dashboard/sum-ob/today') ?>")
                            .then(res => res.json())
                            .then(data => {
                                let actual = data[1] > 0 ? (data[0] / data[1] * 100).toFixed(2) : 0;
                                let budget = 100 - actual;
                                $("#act2").text(data[0].toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                $("#bdgt2").text(data[1].toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                chart2.updateSeries([actual]);
                            });
                    };
                    const updateYearSumOB = function() {
                        fetch("<?= site_url('operation/operation-dashboard/sum-ob/this-year') ?>")
                            .then(res => res.json())
                            .then(data => {
                                let actual = parseFloat(data[0]);
                                let budget = parseFloat(data[1]);
                                let adjust = parseFloat(data[2]);
                                let actual_percent = ((actual + adjust) / budget * 100).toFixed(2);
                                let budget_percent = 100 - actual_percent;
                                $("#act2").text((actual + adjust).toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                $("#bdgt2").text(budget.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                chart2.updateSeries([actual_percent]);
                            });
                    };
                    document.addEventListener("DOMContentLoaded", () => {
                        chart2 = new ApexCharts(document.querySelector("#donutChart2"), {
                            series: [<?= number_format($percentage, 2, ',', '.') ?>],
                            chart: {
                                height: 350,
                                type: 'radialBar',
                                toolbar: {
                                    show: true
                                }
                            },
                            fill: {
                                colors: ['#073b4c'],
                            },
                            plotOptions: {
                                radialBar: {
                                    dataLabels: {
                                        show: true,
                                        name: {
                                            show: false,
                                        },
                                        value: {
                                            show: true,
                                            fontWeight: 500,
                                            fontSize: '25px',
                                            offsetY: 0
                                        }
                                    }
                                }
                            },
                        });
                        chart2.render();

                        $('#ob-sum-production-date').daterangepicker({
                            autoUpdateInput: false,
                            locale: {
                                format: 'DD/MM/YYYY'
                            },

                        }, function(start, end, label) {
                            // function
                        });
                        $('#ob-sum-production-date').on('apply.daterangepicker', function(ev, picker) {
                            fetch("<?= site_url('operation/operation-dashboard/sum-ob/by-date') ?>", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({
                                        'start_date': picker.startDate.format('YYYY-MM-DD'),
                                        'end_date': picker.endDate.format('YYYY-MM-DD'),
                                    })
                                }).then(res => res.json())
                                .then((data) => {
                                    console.log(data);
                                    let actual = parseFloat(data['actual']);
                                    let budget = parseFloat(data['budget']);
                                    let actual_percent = (actual / budget * 100).toFixed(2);
                                    let budget_percent = 100 - actual_percent;
                                    $("#act2").text(actual.toLocaleString("id-ID", {
                                        minimumFractionDigits: 2
                                    }));
                                    $("#bdgt2").text(budget.toLocaleString("id-ID", {
                                        minimumFractionDigits: 2
                                    }));
                                    chart2.updateSeries([actual_percent]);
                                });

                            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                        });

                        $('#ob-sum-production-date').on('cancel.daterangepicker', function(ev, picker) {
                            $(this).val('');
                        });

                        updateYearSumOB();
                    });
                </script>
                <!-- End Donut Chart -->
            </div>
            <div class="card-footer">
                <div>
                    <table class="table table-borderless text-justify">
                        <tr style="border-bottom: 1px solid black;">
                            <td>Actual (Qty)</td>
                            <td><span id="act2"><?= $cg_sum_production['actual'] ?></span> BCM</td>
                        </tr>
                        <tr>
                            <td>Target (Qty)</td>
                            <td><span id="bdgt2"><?= $cg_sum_production['budget'] ?></span> BCM</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- cg -->
    <div class="col-lg-4">
        <div class="card">
            <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" onclick="updateDaySumCG()" href="#">Today</a></li>
                    <li><a class="dropdown-item" onclick="updateMonthSumCG()" href="#">This Month</a></li>
                    <li><a class="dropdown-item" onclick="updateYearSumCG()" href="#">This Year</a></li>
                    <li class="dropdown-item"><input type="text" class="form-control" id="cg-sum-production-date" name="dates" required></li>
                </ul>
            </div>
            <div class="card-body">
                <h3 class="card-title">Coal Getting</h3>
                <h5 class="card-subtitle"></h5>
                <!-- Donut Chart -->
                <div id="donutChart"></div>
                <script>
                    <?php
                    $percentage = $cg_sum_production['budget'] > 0 ? round($cg_sum_production['actual'] / $cg_sum_production['budget'] * 100, 2) : 0;
                    ?>
                    let chart;
                    const updateMonthSumCG = function() {
                        fetch("<?= site_url('operation/operation-dashboard/sum-cg/this-month') ?>")
                            .then(res => res.json())
                            .then(data => {
                                console.log(data);
                                let actual = parseFloat(data[0]);
                                let budget = parseFloat(data[1]);
                                // let adjust = parseFloat(data[2]);
                                let adjust = 0;
                                let actual_percent = budget > 0 ? ((actual + adjust) / budget * 100).toFixed(2) : 0;
                                let budget_percent = 100 - actual_percent;
                                $("#act1").text((actual + adjust).toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                $("#bdgt1").text(budget.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                chart.updateSeries([actual_percent]);
                            });
                    };
                    const updateDaySumCG = function() {
                        fetch("<?= site_url('operation/operation-dashboard/sum-cg/today') ?>")
                            .then(res => res.json())
                            .then(data => {
                                let actual = data[1] > 0 ? (data[0] / data[1] * 100).toFixed(2) : 0;
                                let budget = 100 - actual;
                                $("#act1").text(actual.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                $("#bdgt1").text(data[1].toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                chart.updateSeries([actual]);
                            });
                    };
                    const updateYearSumCG = function() {
                        fetch("<?= site_url('operation/operation-dashboard/sum-cg/this-year') ?>")
                            .then(res => res.json())
                            .then(data => {
                                let actual = parseFloat(data[0]);
                                let budget = parseFloat(data[1]);
                                // let adjust = parseFloat(data[2]);
                                let adjust = 0;
                                let actual_percent = ((actual + adjust) / budget * 100).toFixed(2);
                                let budget_percent = 100 - actual_percent;
                                $("#act1").text((actual + adjust).toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                $("#bdgt1").text(budget.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                chart.updateSeries([actual_percent]);
                            });
                    };
                    document.addEventListener("DOMContentLoaded", () => {
                        chart = new ApexCharts(document.querySelector("#donutChart"), {
                            series: [<?= number_format($percentage, 2, ',', '.') ?>],
                            chart: {
                                height: 350,
                                type: 'radialBar',
                                toolbar: {
                                    show: true
                                }
                            },
                            colors: ['#118ab2'],
                            plotOptions: {
                                radialBar: {
                                    dataLabels: {
                                        show: true,
                                        name: {
                                            show: false,
                                        },
                                        value: {
                                            show: true,
                                            fontWeight: 500,
                                            fontSize: '25px',
                                            offsetY: 0
                                        }
                                    }
                                }
                            }
                        });
                        chart.render();

                        $('#cg-sum-production-date').daterangepicker({
                            autoUpdateInput: false,
                            locale: {
                                format: 'DD/MM/YYYY'
                            },

                        }, function(start, end, label) {
                            // function
                        });
                        $('#cg-sum-production-date').on('apply.daterangepicker', function(ev, picker) {
                            fetch("<?= site_url('operation/operation-dashboard/sum-cg/by-date') ?>", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({
                                        'start_date': picker.startDate.format('YYYY-MM-DD'),
                                        'end_date': picker.endDate.format('YYYY-MM-DD'),
                                    })
                                }).then(res => res.json())
                                .then((data) => {
                                    let actual = parseFloat(data['actual']);
                                    let budget = parseFloat(data['budget']);
                                    let actual_percent = (actual / budget * 100).toFixed(2);
                                    let budget_percent = 100 - actual_percent;
                                    $("#act1").text(actual.toLocaleString("id-ID", {
                                        minimumFractionDigits: 2
                                    }));
                                    $("#bdgt1").text(budget.toLocaleString("id-ID", {
                                        minimumFractionDigits: 2
                                    }));
                                    chart.updateSeries([actual_percent]);
                                });

                            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                        });

                        $('#cg-sum-production-date').on('cancel.daterangepicker', function(ev, picker) {
                            $(this).val('');
                        });
                        updateYearSumCG();
                    });
                </script>
                <!-- End Donut Chart -->
            </div>
            <div class="card-footer">
                <div>
                    <table class="table table-borderless text-justify">
                        <tr style="border-bottom: 1px solid black;">
                            <td>Actual (Qty)</td>
                            <td><span id="act1"><?= $cg_sum_production['actual'] ?></span> MT</td>
                        </tr>
                        <tr>
                            <td>Target (Qty)</td>
                            <td><span id="bdgt1"><?= $cg_sum_production['budget'] ?></span> MT</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" onclick="updateDayStripping()" href="#">Today</a></li>
                    <li><a class="dropdown-item" onclick="updateMonthStripping()" href="#">This Month</a></li>
                    <li><a class="dropdown-item" onclick="updateYearStripping()" href="#">This Year</a></li>
                    <li class="dropdown-item"><input type="text" class="form-control" id="stripping-date" name="dates" required></li>
                </ul>
            </div>
            <div class="card-body">
                <h3 class="card-title">Stripping Ratio</h3>
                <h5 class="card-subtitle"></h5>
                <!-- Donut Chart -->
                <div id="donutChart3"></div>
                <script>
                    <?php
                    $actual_stripping_ratio = $stripping_ratio_today['actual_ob'] / max($stripping_ratio_today['actual_cg'], 1);
                    $budget_stripping_ratio = $stripping_ratio_today['budget_ob'] / max($stripping_ratio_today['budget_cg'], 1);
                    $achievement = $budget_stripping_ratio > 0 ? round($actual_stripping_ratio / $budget_stripping_ratio * 100, 2) : 0;
                    ?>
                    let chartStripping;
                    const updateMonthStripping = function() {
                        fetch("<?= site_url('operation/operation-dashboard/strip-ratio/this-month') ?>")
                            .then(res => res.json())
                            .then(data => {
                                let strip_ratio = data.stripping_ratio;
                                let actual_cg = parseFloat(strip_ratio.actual_cg);
                                let budget_cg = parseFloat(strip_ratio.budget_cg);
                                let actual_ob = parseFloat(strip_ratio.actual_ob);
                                let budget_ob = parseFloat(strip_ratio.budget_ob);
                                let ob_adjust = parseFloat(strip_ratio.ob_adjust);
                                let cg_adjust = parseFloat(strip_ratio.cg_adjust);
                                let actual_stripping_ratio = actual_cg > 0 ? ((actual_ob + ob_adjust) / (actual_cg + cg_adjust)).toFixed(2) : 0;
                                let budget_stripping_ratio = budget_cg > 0 ? (budget_ob / budget_cg).toFixed(2) : 0;
                                let achievement = budget_stripping_ratio > 0 ? (actual_stripping_ratio / budget_stripping_ratio * 100).toFixed(2) : 0;
                                $("#actstr").text(actual_stripping_ratio.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                $("#bdgtstr").text(budget_stripping_ratio.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                chartStripping.updateSeries([achievement]);
                            });
                    };
                    const updateDayStripping = function() {
                        fetch("<?= site_url('operation/operation-dashboard/strip-ratio/today') ?>")
                            .then(res => res.json())
                            .then(data => {
                                let strip_ratio = data.stripping_ratio;
                                let actual_cg = parseFloat(strip_ratio.actual_cg);
                                let budget_cg = parseFloat(strip_ratio.budget_cg);
                                let actual_ob = parseFloat(strip_ratio.actual_ob);
                                let budget_ob = parseFloat(strip_ratio.budget_ob);
                                let actual_stripping_ratio = actual_cg > 0 ? ((actual_ob) / (actual_cg)).toFixed(2) : 0;
                                let budget_stripping_ratio = budget_cg > 0 ? (budget_ob / budget_cg).toFixed(2) : 0;
                                let achievement = budget_stripping_ratio > 0 ? (actual_stripping_ratio / budget_stripping_ratio * 100).toFixed(2) : 0;
                                $("#actstr").text(actual_stripping_ratio.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                $("#bdgtstr").text(budget_stripping_ratio.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                chartStripping.updateSeries([achievement]);
                            });
                    };
                    const updateYearStripping = function() {
                        fetch("<?= site_url('operation/operation-dashboard/strip-ratio/this-year') ?>")
                            .then(res => res.json())
                            .then(data => {
                                console.log(data);
                                let strip_ratio = data.stripping_ratio;
                                let actual_cg = parseFloat(strip_ratio.actual_cg);
                                let budget_cg = parseFloat(strip_ratio.budget_cg);
                                let actual_ob = parseFloat(strip_ratio.actual_ob);
                                let budget_ob = parseFloat(strip_ratio.budget_ob);
                                let actual_stripping_ratio = actual_cg > 0 ? ((actual_ob) / (actual_cg)).toFixed(2) : 0;
                                let budget_stripping_ratio = budget_cg > 0 ? (budget_ob / budget_cg).toFixed(2) : 0;
                                let achievement = budget_stripping_ratio > 0 ? (actual_stripping_ratio / budget_stripping_ratio * 100).toFixed(2) : 0;
                                $("#actstr").text(actual_stripping_ratio.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                $("#bdgtstr").text(budget_stripping_ratio.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2
                                }));
                                chartStripping.updateSeries([achievement]);
                            });
                    };
                    document.addEventListener("DOMContentLoaded", () => {
                        chartStripping = new ApexCharts(document.querySelector("#donutChart3"), {
                            series: [<?= number_format($achievement, 2, ",", ".") ?>],
                            chart: {
                                height: 350,
                                type: 'radialBar',
                                toolbar: {
                                    show: true
                                }
                            },
                            colors: ['#06d6a0'],
                            plotOptions: {
                                radialBar: {
                                    dataLabels: {
                                        show: true,
                                        name: {
                                            show: false,
                                        },
                                        value: {
                                            show: true,
                                            fontWeight: 500,
                                            fontSize: '25px',
                                            offsetY: 0
                                        }
                                    }
                                }
                            }
                        });
                        chartStripping.render();

                        $('#stripping-date').daterangepicker({
                            autoUpdateInput: false,
                            locale: {
                                format: 'DD/MM/YYYY'
                            },

                        }, function(start, end, label) {
                            // function
                        });
                        $('#stripping-date').on('apply.daterangepicker', function(ev, picker) {
                            fetch("<?= site_url('operation/operation-dashboard/strip-ratio/by-date') ?>", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({
                                        'start_date': picker.startDate.format('YYYY-MM-DD'),
                                        'end_date': picker.endDate.format('YYYY-MM-DD'),
                                    })
                                }).then(res => res.json())
                                .then((data) => {
                                    let strip_ratio = data;
                                    let actual_cg = parseFloat(strip_ratio.actual_cg);
                                    let budget_cg = parseFloat(strip_ratio.budget_cg);
                                    let actual_ob = parseFloat(strip_ratio.actual_ob);
                                    let budget_ob = parseFloat(strip_ratio.budget_ob);
                                    let actual_stripping_ratio = actual_cg > 0 ? ((actual_ob) / (actual_cg)).toFixed(2) : 0;
                                    let budget_stripping_ratio = budget_cg > 0 ? (budget_ob / budget_cg).toFixed(2) : 0;
                                    let achievement = budget_stripping_ratio > 0 ? (actual_stripping_ratio / budget_stripping_ratio * 100).toFixed(2) : 0;
                                    $("#actstr").text(actual_stripping_ratio.toLocaleString("id-ID", {
                                        minimumFractionDigits: 2
                                    }));
                                    $("#bdgtstr").text(budget_stripping_ratio.toLocaleString("id-ID", {
                                        minimumFractionDigits: 2
                                    }));
                                    chartStripping.updateSeries([achievement]);
                                });

                            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                        });

                        $('#stripping-date').on('cancel.daterangepicker', function(ev, picker) {
                            $(this).val('');
                        });

                        updateYearStripping();
                    });
                </script>
                <!-- End Donut Chart -->
            </div>
            <div class="card-footer">
                <div>
                    <table class="table table-borderless text-justify">
                        <tr style="border-bottom: 1px solid black;">
                            <td>Actual</td>
                            <td><span id="actstr"><?= $cg_sum_production['actual'] ?></span></td>
                        </tr>
                        <tr>
                            <td>Target</td>
                            <td><span id="bdgtstr"><?= $cg_sum_production['budget'] ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Over Burden Line Chart (Over Burden: Budget vs Actual) -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Over Burden: Target vs Actual</h3>
                <!-- Donut Chart -->
                <div id="lineChart2"></div>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#lineChart2"), {
                            series: [{
                                    name: "Target",
                                    data: [
                                        <?php foreach ($ob_budget_vs_actual as $ob) : ?> {
                                                x: "<?= $bulan[$ob['month']] ?>",
                                                y: <?= $ob['budget'] ?>
                                            },
                                        <?php endforeach ?>
                                    ]
                                },
                                {
                                    name: "Actual",
                                    data: [
                                        <?php foreach ($ob_budget_vs_actual as $ob) : ?> {
                                                x: "<?= $bulan[$ob['month']] ?>",
                                                y: <?= $ob['actual'] + $ob['adjust'] ?>
                                            },
                                        <?php endforeach ?>
                                    ]
                                }
                            ],
                            chart: {
                                height: 350,
                                type: 'line',
                                dropShadow: {
                                    enabled: true,
                                    color: '#000',
                                    top: 18,
                                    left: 7,
                                    blur: 10,
                                    opacity: 0.2
                                },
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#ff9f1c', '#003566'],
                            dataLabels: {
                                enabled: true,
                                formatter: function(value) {
                                    return value.toLocaleString('id-ID', {
                                        minimumFractionDigits: 2
                                    }) + " BCM";
                                }
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                    opacity: 0.5
                                },
                            },
                            markers: {
                                size: 1
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
                                        }) + " BCM";
                                    }
                                },
                            },
                            legend: {
                                position: 'bottom',
                            }
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
                <h3 class="card-title">Over Burden: % Achievement</h3>
                <!-- Donut Chart -->
                <div id="lineObPercentage"></div>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#lineObPercentage"), {
                            series: [{
                                name: "Percentage",
                                data: [
                                    <?php foreach ($ob_budget_vs_actual as $ob) : ?> {
                                            x: "<?= $bulan[$ob['month']] ?>",
                                            y: "<?= round(($ob['actual'] + $ob['adjust']) / $ob['budget'] * 100, 2) ?>"
                                        },
                                    <?php endforeach ?>
                                ]
                            }],
                            chart: {
                                height: 350,
                                type: 'line',
                                dropShadow: {
                                    enabled: true,
                                    color: '#000',
                                    top: 18,
                                    left: 7,
                                    blur: 10,
                                    opacity: 0.2
                                },
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#007200', '#545454'],
                            dataLabels: {
                                enabled: true,
                                formatter: function(value) {
                                    return value + "%";
                                }
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                    opacity: 0.5
                                },
                            },
                            markers: {
                                size: 1
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
                                    text: 'Percentage',
                                    style: {
                                        color: "#fff",
                                        fontSize: "1px"
                                    }
                                },
                                labels: {
                                    formatter: function(value) {
                                        return value + "%";
                                    }
                                },
                            },
                            legend: {
                                position: 'bottom',
                            }
                        }).render();
                    });
                </script>
                <!-- End Donut Chart -->
            </div>
        </div>
    </div>
</div>
<!-- Coal Getting Line Chart (Coal Getting: Budget vs actual) -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Coal Getting: Target vs Actual</h3>
                <!-- Donut Chart -->
                <div id="lineChart"></div>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#lineChart"), {
                            series: [{
                                    name: "Target",
                                    data: [
                                        <?php foreach ($cg_budget_vs_actual as $cg) : ?> {
                                                x: "<?= $bulan[$cg['month']] ?>",
                                                y: <?= $cg['budget'] ?>
                                            },
                                        <?php endforeach ?>
                                    ]
                                },
                                {
                                    name: "Actual",
                                    data: [
                                        <?php foreach ($cg_budget_vs_actual as $cg) : ?> {
                                                x: "<?= $bulan[$cg['month']] ?>",
                                                y: <?= $cg['actual'] + $cg['adjust'] ?>
                                            },
                                        <?php endforeach ?>
                                    ]
                                },
                            ],
                            chart: {
                                height: 350,
                                type: 'line',
                                dropShadow: {
                                    enabled: true,
                                    color: '#000',
                                    top: 18,
                                    left: 7,
                                    blur: 10,
                                    opacity: 0.2
                                },
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#ff9f1c', '#003566'],
                            dataLabels: {
                                enabled: true,
                                formatter: function(value) {
                                    return value.toLocaleString('id-ID', {
                                        minimumFractionDigits: 2
                                    }) + " MT";
                                }
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                    opacity: 0.5
                                },
                            },
                            markers: {
                                size: 1
                            },
                            xaxis: {
                                // categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
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
                                labels: {
                                    formatter: function(value) {
                                        return value.toLocaleString('id-ID', {
                                            minimumFractionDigits: 2
                                        }) + " MT";
                                    }
                                },
                                min: 5,
                            },
                            legend: {
                                position: 'bottom',
                            }
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
                <h3 class="card-title">Coal Getting: % Achievement</h3>
                <!-- Donut Chart -->
                <div id="lineCGPercentage"></div>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#lineCGPercentage"), {
                            series: [{
                                name: "Percentage",
                                data: [
                                    <?php foreach ($cg_budget_vs_actual as $cg) : ?> {
                                            x: "<?= $bulan[$cg['month']] ?>",
                                            y: <?= round(handleDivision(($cg['actual'] + $cg['adjust']) , ($cg['budget'] * 100), 2)) ?>
                                        },
                                    <?php endforeach ?>
                                ]
                            }, ],
                            chart: {
                                height: 350,
                                type: 'line',
                                dropShadow: {
                                    enabled: true,
                                    color: '#000',
                                    top: 18,
                                    left: 7,
                                    blur: 10,
                                    opacity: 0.2
                                },
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#007200', '#545454'],
                            dataLabels: {
                                enabled: true,
                                formatter: function(value) {
                                    return value + "%";
                                }
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                    opacity: 0.5
                                },
                            },
                            markers: {
                                size: 1
                            },
                            xaxis: {
                                // categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
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
                                    text: 'Percentage',
                                    style: {
                                        color: "#fff",
                                        fontSize: "1px"
                                    }
                                },
                                labels: {
                                    formatter: function(value) {
                                        return value + "%";
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                            }
                        }).render();
                    });
                </script>
                <!-- End Donut Chart -->
            </div>
        </div>
    </div>
</div>
<!-- Stripping Ratio -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Stripping Ratio: Target vs Actual</h3>
                <!-- Donut Chart -->
                <div id="lineChartStrippingRatio"></div>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#lineChartStrippingRatio"), {
                            series: [{
                                    name: "Target",
                                    data: [
                                        <?php foreach ($stripping_ratio as $budget) : ?> {
                                                x: "<?= $bulan[$budget['month']] ?>",
                                                y: <?= round($budget['budget_ob'] / $budget['budget_cg'], 2) ?>
                                            },
                                        <?php endforeach ?>
                                    ]
                                },
                                {
                                    name: "Actual",
                                    data: [
                                        <?php foreach ($stripping_ratio as $actual) : ?> {
                                                x: "<?= $bulan[$actual['month']] ?>",
                                                y: <?= round($actual['actual_ob'] / max($actual['actual_cg'], 1), 2) ?>
                                            },
                                        <?php endforeach ?>
                                    ]
                                }
                            ],
                            chart: {
                                height: 350,
                                type: 'line',
                                dropShadow: {
                                    enabled: true,
                                    color: '#000',
                                    top: 18,
                                    left: 7,
                                    blur: 10,
                                    opacity: 0.2
                                },
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#ff9f1c', '#003566'],
                            dataLabels: {
                                enabled: true,
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                    opacity: 0.5
                                },
                            },
                            markers: {
                                size: 1
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
                            },
                            legend: {
                                position: 'bottom',
                            }
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
                <h3 class="card-title">Stripping Ratio: % Achievement</h3>
                <!-- Donut Chart -->
                <div id="lineSRPercentage"></div>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#lineSRPercentage"), {
                            series: [{
                                name: "Actual",
                                data: [
                                    <?php foreach ($stripping_ratio as $actual) : ?> {
                                            <?php
                                            $budget_sr = round($actual['budget_ob'] / $actual['budget_cg'], 2);
                                            $actual_sr = round($actual['actual_ob'] / max($actual['actual_cg'], 1), 2);
                                            ?>
                                            x: "<?= $bulan[$actual['month']] ?>",
                                                y: <?= round($actual_sr / $budget_sr * 100, 2) ?>
                                        },
                                    <?php endforeach ?>
                                ]
                            }],
                            chart: {
                                height: 350,
                                type: 'line',
                                dropShadow: {
                                    enabled: true,
                                    color: '#000',
                                    top: 18,
                                    left: 7,
                                    blur: 10,
                                    opacity: 0.2
                                },
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#007200', '#545454'],
                            dataLabels: {
                                enabled: true,
                                formatter: function(value) {
                                    return value + "%";
                                }
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                    opacity: 0.5
                                },
                            },
                            markers: {
                                size: 1
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
                                    text: 'Percentage',
                                    style: {
                                        color: "#fff",
                                        fontSize: "1px"
                                    }
                                },
                                labels: {
                                    formatter: function(value) {
                                        return value + "%";
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                            }
                        }).render();
                    });
                </script>
                <!-- End Donut Chart -->
            </div>
        </div>
    </div>
</div>
<!-- Contractor Charts -->
<div class="row">
    <?php $id = 1; ?>
    <!-- Over Burden -->
    <?php foreach ($contractor_ob as $ccg) : ?>
        <div class="col-lg-4">
            <div class="card">
                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Filter</h6>
                        </li>
                        <li id="contractor-ob<?= $id ?>-today"><a class="dropdown-item" href="#/">Today</a></li>
                        <li id="contractor-ob<?= $id ?>-month"><a class="dropdown-item" href="#/">This Month</a></li>
                        <li id="contractor-ob<?= $id ?>-year"><a class="dropdown-item" href="#/">This Year</a></li>
                        <li class="dropdown-item"><input type="text" class="form-control" id="ob-contractor-date-<?= $id ?>" name="dates" required></li>
                    </ul>
                </div>
                <div class="card-body">
                    <h3 class="card-title"><?= $ccg['contractor_name'] ?></h3>
                    <h5 class="card-subtitle">Over Burden</h5>
                    <!-- Donut Chart -->
                    <div id="obChart<?= $id ?>"></div>
                    <script>
                        <?php
                        $actual = $ccg['actual'] / $ccg['budget'] * 100;
                        $budget = 100 - $actual;
                        $percentage = round($ccg['actual'] / $ccg['budget'] * 100, 2); ?>
                        let chart_contractor_ob<?= $id ?>;
                        $("#contractor-ob<?= $id ?>-month").click(
                            function() {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-ob/this-month/') . $ccg['id'] ?>")
                                    .then(res => res.json())
                                    .then(data => {
                                        console.log(data);
                                        let actual = parseFloat(data[0]);
                                        let budget = parseFloat(data[1]);
                                        let actual_percent = budget > 0 ? ((actual) / budget * 100).toFixed(2) : 0;
                                        let budget_percent = 100 - actual_percent;
                                        $("#ob-actual-<?= $id ?>").text(actual.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#ob-budget-<?= $id ?>").text(budget.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        chart_contractor_ob<?= $id ?>.updateSeries([actual_percent]);
                                    });
                            }
                        );

                        $("#contractor-ob<?= $id ?>-year").click(
                            function() {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-ob/this-year/') . $ccg['id'] ?>")
                                    .then(res => res.json())
                                    .then(data => {
                                        let actual = parseFloat(data[0]);
                                        let budget = parseFloat(data[1]);
                                        let actual_percent = ((actual) / Math.max(budget, 1) * 100).toFixed(2);
                                        let budget_percent = 100 - actual_percent;
                                        $("#ob-actual-<?= $id ?>").text(actual.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#ob-budget-<?= $id ?>").text(budget.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        chart_contractor_ob<?= $id ?>.updateSeries([actual_percent]);
                                    });
                            }
                        );

                        $("#contractor-ob<?= $id ?>-today").click(
                            function() {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-ob/today/') . $ccg['id'] ?>")
                                    .then(res => res.json())
                                    .then(data => {
                                        let actual = parseFloat(data[0]);
                                        let budget = parseFloat(data[1]);
                                        let actual_percent = ((actual) / Math.max(budget, 1) * 100).toFixed(2);
                                        let budget_percent = 100 - actual_percent;
                                        $("#ob-actual-<?= $id ?>").text(actual.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#ob-budget-<?= $id ?>").text(budget.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        chart_contractor_ob<?= $id ?>.updateSeries([actual_percent]);
                                    });
                            }
                        );
                        document.addEventListener("DOMContentLoaded", () => {
                            chart_contractor_ob<?= $id ?> = new ApexCharts(document.querySelector("#obChart<?= $id ?>"), {
                                series: [<?= $percentage ?>],
                                chart: {
                                    height: 350,
                                    type: 'radialBar',
                                    toolbar: {
                                        show: true
                                    }
                                },
                                fill: {
                                    colors: ['#073b4c'],
                                },
                                plotOptions: {
                                    radialBar: {
                                        dataLabels: {
                                            show: true,
                                            name: {
                                                show: false,
                                            },
                                            value: {
                                                show: true,
                                                fontWeight: 500,
                                                fontSize: '25px',
                                                offsetY: 0
                                            }
                                        }
                                    }
                                }
                            });
                            chart_contractor_ob<?= $id ?>.render();

                            $('#ob-contractor-date-<?= $id ?>').daterangepicker({
                                autoUpdateInput: false,
                                locale: {
                                    format: 'DD/MM/YYYY'
                                },

                            }, function(start, end, label) {
                                // function
                            });
                            $('#ob-contractor-date-<?= $id ?>').on('apply.daterangepicker', function(ev, picker) {
                                fetch("<?= site_url('operation/operation-dashboard/sum-ob/by-date/') . $id ?>", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json"
                                        },
                                        body: JSON.stringify({
                                            'start_date': picker.startDate.format('YYYY-MM-DD'),
                                            'end_date': picker.endDate.format('YYYY-MM-DD'),
                                        })
                                    }).then(res => res.json())
                                    .then((data) => {
                                        let actual = parseFloat(data['actual']);
                                        let budget = parseFloat(data['budget']);
                                        let actual_percent = (actual / Math.max(budget, 1) * 100).toFixed(2);
                                        let budget_percent = 100 - actual_percent;
                                        $("#ob-actual-<?= $id ?>").text(actual.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#ob-budget-<?= $id ?>").text(budget.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        chart_contractor_ob<?= $id ?>.updateSeries([actual_percent]);
                                    });

                                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                            });

                            $('#ob-contractor-date-<?= $id ?>').on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                            });
                        });
                    </script>
                    <!-- End Donut Chart -->
                </div>
                <div class="card-footer">
                    <div>
                        <table class="table table-borderless text-justify">
                            <tr style="border-bottom: 1px solid black;">
                                <td>Actual (Qty)</td>
                                <td><span id="ob-actual-<?= $id ?>"><?= number_format($ccg['actual'], 2, ',', '.') ?></span> BCM</td>
                            </tr>
                            <tr>
                                <td>Budget (Qty)</td>
                                <td><span id="ob-budget-<?= $id ?>"><?= number_format($ccg['budget'], 2, ',', '.') ?></span> BCM</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php $id++ ?>
    <?php endforeach ?>
    <!-- CG -->
    <?php $id = 1; ?>
    <?php foreach ($contractor_cg as $ccg) : ?>
        <div class="col-lg-4">
            <div class="card">
                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Filter</h6>
                        </li>
                        <li id="contractor-cg<?= $id ?>-today"><a class="dropdown-item" href="#/">Today</a></li>
                        <li id="contractor-cg<?= $id ?>-month"><a class="dropdown-item" href="#/">This Month</a></li>
                        <li id="contractor-cg<?= $id ?>-year"><a class="dropdown-item" href="#/">This Year</a></li>
                        <li class="dropdown-item"><input type="text" class="form-control" id="cg-contractor-date-<?= $id ?>" name="dates" required></li>
                    </ul>
                </div>
                <div class="card-body">
                    <h3 class="card-title"><?= $ccg['contractor_name'] ?></h3>
                    <h5 class="card-subtitle">Coal Getting</h5>
                    <!-- Donut Chart -->
                    <div id="cgChart<?= $id ?>"></div>
                    <script>
                        <?php
                        $actual = $ccg['actual'] / $ccg['budget'] * 100;
                        $budget = 100 - $actual;
                        $percentage = round($ccg['actual'] / $ccg['budget'] * 100, 2); ?>
                        let chart_contractor_cg<?= $id ?>;
                        $("#contractor-cg<?= $id ?>-month").click(
                            function() {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-cg/this-month/') . $ccg['project1'] ?>")
                                    .then(res => res.json())
                                    .then(data => {
                                        let actual = parseFloat(data[0]);
                                        let budget = parseFloat(data[1]);
                                        let actual_percent = ((actual) / Math.max(budget, 1) * 100).toFixed(2);
                                        let budget_percent = 100 - actual_percent;
                                        $("#cg-actual-contractor-<?= $id ?>").text(actual.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#cg-budget-contractor-<?= $id ?>").text(budget.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        chart_contractor_cg<?= $id ?>.updateSeries([actual_percent]);
                                    });
                            }
                        );

                        $("#contractor-cg<?= $id ?>-year").click(
                            function() {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-cg/this-year/') . $ccg['project1'] ?>")
                                    .then(res => res.json())
                                    .then(data => {
                                        let actual = parseFloat(data[0]);
                                        let budget = parseFloat(data[1]);
                                        let actual_percent = ((actual) / Math.max(budget, 1) * 100).toFixed(2);
                                        let budget_percent = 100 - actual_percent;
                                        $("#cg-actual-contractor-<?= $id ?>").text(actual.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#cg-budget-contractor-<?= $id ?>").text(budget.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        chart_contractor_cg<?= $id ?>.updateSeries([actual_percent]);
                                    });
                            }
                        );

                        $("#contractor-cg<?= $id ?>-today").click(
                            function() {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-cg/today/') . $ccg['project1'] ?>")
                                    .then(res => res.json())
                                    .then(data => {
                                        console.log(data);
                                        let actual = parseFloat(data[0]);
                                        let budget = parseFloat(data[1]);
                                        let actual_percent = ((actual) / Math.max(budget, 1) * 100).toFixed(2);
                                        let budget_percent = 100 - actual_percent;
                                        $("#cg-actual-contractor-<?= $id ?>").text(actual.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#cg-budget-contractor-<?= $id ?>").text(budget.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        chart_contractor_cg<?= $id ?>.updateSeries([actual_percent]);
                                    });
                            }
                        );

                        document.addEventListener("DOMContentLoaded", () => {
                            chart_contractor_cg<?= $id ?> = new ApexCharts(document.querySelector("#cgChart<?= $id ?>"), {
                                series: [<?= $percentage ?>],
                                chart: {
                                    height: 350,
                                    type: 'radialBar',
                                    toolbar: {
                                        show: true
                                    }
                                },
                                fill: {
                                    colors: ['#118ab2'],
                                },
                                plotOptions: {
                                    radialBar: {
                                        dataLabels: {
                                            show: true,
                                            name: {
                                                show: false,
                                            },
                                            value: {
                                                show: true,
                                                fontWeight: 500,
                                                fontSize: '25px',
                                                offsetY: 0
                                            }
                                        }
                                    }
                                }
                            });
                            chart_contractor_cg<?= $id ?>.render();

                            $('#cg-contractor-date-<?= $id ?>').daterangepicker({
                                autoUpdateInput: false,
                                locale: {
                                    format: 'DD/MM/YYYY'
                                },

                            }, function(start, end, label) {
                                // function
                            });
                            $('#cg-contractor-date-<?= $id ?>').on('apply.daterangepicker', function(ev, picker) {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-cg/by-date/') . $ccg['project1'] ?>", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json"
                                        },
                                        body: JSON.stringify({
                                            'start_date': picker.startDate.format('YYYY-MM-DD'),
                                            'end_date': picker.endDate.format('YYYY-MM-DD'),
                                        })
                                    }).then(res => res.json())
                                    .then((data) => {
                                        let actual = parseFloat(data['actual']);
                                        let budget = parseFloat(data['budget']);
                                        let actual_percent = (actual / Math.max(budget, 1) * 100).toFixed(2);
                                        let budget_percent = 100 - actual_percent;
                                        $("#cg-actual-contractor-<?= $id ?>").text(actual.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#cg-budget-contractor-<?= $id ?>").text(budget.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        chart_contractor_cg<?= $id ?>.updateSeries([actual_percent]);
                                    });

                                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                            });

                            $('#cg-contractor-date-<?= $id ?>').on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                            });
                        });
                    </script>
                    <!-- End Donut Chart -->
                </div>
                <div class="card-footer">
                    <div>
                        <table class="table table-borderless text-justify">
                            <tr style="border-bottom: 1px solid black;">
                                <td>Actual (Qty)</td>
                                <td><span id="cg-actual-contractor-<?= $id ?>"><?= number_format($ccg['actual'], 2, ',', '.') ?></span> MT</td>
                            </tr>
                            <tr>
                                <td>Budget (Qty)</td>
                                <td><span id="cg-budget-contractor-<?= $id ?>"><?= number_format($ccg['budget'], 2, ',', '.') ?></span> MT</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php $id++ ?>
    <?php endforeach ?>
    <!-- stripping ratio -->
    <?php $id = 1; ?>
    <?php foreach ($stripping_contractor as $ccg) : ?>
        <div class="col-lg-4">
            <div class="card">
                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Filter</h6>
                        </li>
                        <li id="contractor-sr<?= $id ?>-today"><a class="dropdown-item" href="#/">Today</a></li>
                        <li id="contractor-sr<?= $id ?>-month"><a class="dropdown-item" href="#/">This Month</a></li>
                        <li id="contractor-sr<?= $id ?>-year"><a class="dropdown-item" href="#/">This Year</a></li>
                        <li class="dropdown-item"><input type="text" class="form-control" id="sr-contractor-date-<?= $id ?>" name="dates" required></li>
                    </ul>
                </div>
                <div class="card-body">
                    <h3 class="card-title"><?= $ccg['contractor_name'] ?></h3>
                    <h5 class="card-subtitle">Stripping Ratio</h5>
                    <!-- Donut Chart -->
                    <div id="srChart<?= $id ?>"></div>
                    <script>
                        <?php
                        $actual_stripping_ratio = $ccg['actual_cg'] > 0 ? $ccg['actual_ob'] / $ccg['actual_cg'] * 100 : 0;
                        $budget_stripping_ratio = $ccg['budget_cg'] > 0 ? $ccg['budget_ob'] / $ccg['budget_cg'] * 100 : 0;
                        $achievement = $budget_stripping_ratio > 0 ? round($actual_stripping_ratio / $budget_stripping_ratio * 100, 2) : 0;
                        ?>
                        let chart_contractor_sr<?= $id ?>;
                        $("#contractor-sr<?= $id ?>-month").click(
                            function() {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-strip/this-month/') . $ccg['id'] ?>")
                                    .then(res => res.json())
                                    .then(data => {
                                        let strip_ratio = data.stripping_ratio;
                                        let actual_cg = parseFloat(strip_ratio.actual_cg);
                                        let budget_cg = parseFloat(strip_ratio.budget_cg);
                                        let actual_ob = parseFloat(strip_ratio.actual_ob);
                                        let budget_ob = parseFloat(strip_ratio.budget_ob);
                                        let actual_stripping_ratio = actual_cg > 0 ? ((actual_ob) / (actual_cg)).toFixed(2) : 0;
                                        let budget_stripping_ratio = budget_cg > 0 ? (budget_ob / budget_cg).toFixed(2) : 0;
                                        let achievement = budget_stripping_ratio > 0 ? (actual_stripping_ratio / budget_stripping_ratio * 100).toFixed(2) : 0;
                                        chart_contractor_sr<?= $id ?>.updateSeries([achievement]);
                                        $("#sr-actual-<?= $id ?>").text(actual_stripping_ratio.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#sr-budget-<?= $id ?>").text(budget_stripping_ratio.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                    });
                            }
                        );

                        $("#contractor-sr<?= $id ?>-year").click(
                            function() {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-strip/this-year/') . $ccg['id'] ?>")
                                    .then(res => res.json())
                                    .then(data => {
                                        let strip_ratio = data.stripping_ratio;
                                        let actual_cg = parseFloat(strip_ratio.actual_cg);
                                        let budget_cg = parseFloat(strip_ratio.budget_cg);
                                        let actual_ob = parseFloat(strip_ratio.actual_ob);
                                        let budget_ob = parseFloat(strip_ratio.budget_ob);
                                        let actual_stripping_ratio = actual_cg > 0 ? ((actual_ob) / (actual_cg)).toFixed(2) : 0;
                                        let budget_stripping_ratio = budget_cg > 0 ? (budget_ob / budget_cg).toFixed(2) : 0;
                                        let achievement = budget_stripping_ratio > 0 ? (actual_stripping_ratio / budget_stripping_ratio * 100).toFixed(2) : 0;
                                        chart_contractor_sr<?= $id ?>.updateSeries([achievement]);
                                        $("#sr-actual-<?= $id ?>").text(actual_stripping_ratio.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#sr-budget-<?= $id ?>").text(budget_stripping_ratio.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                    });
                            }
                        );

                        $("#contractor-sr<?= $id ?>-today").click(
                            function() {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-strip/today/') . $ccg['id'] ?>")
                                    .then(res => res.json())
                                    .then(data => {
                                        console.log(data);
                                        let strip_ratio = data.stripping_ratio;
                                        let actual_cg = parseFloat(strip_ratio.actual_cg);
                                        let budget_cg = parseFloat(strip_ratio.budget_cg);
                                        let actual_ob = parseFloat(strip_ratio.actual_ob);
                                        let budget_ob = parseFloat(strip_ratio.budget_ob);
                                        let actual_stripping_ratio = actual_cg > 0 ? ((actual_ob) / (actual_cg)).toFixed(2) : 0;
                                        let budget_stripping_ratio = budget_cg > 0 ? (budget_ob / budget_cg).toFixed(2) : 0;
                                        let achievement = budget_stripping_ratio > 0 ? (actual_stripping_ratio / budget_stripping_ratio * 100).toFixed(2) : 0;
                                        chart_contractor_sr<?= $id ?>.updateSeries([achievement]);
                                        $("#sr-actual-<?= $id ?>").text(actual_stripping_ratio.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#sr-budget-<?= $id ?>").text(budget_stripping_ratio.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                    });
                            }
                        );

                        document.addEventListener("DOMContentLoaded", () => {
                            chart_contractor_sr<?= $id ?> = new ApexCharts(document.querySelector("#srChart<?= $id ?>"), {
                                series: [<?= $achievement ?>],
                                chart: {
                                    height: 350,
                                    type: 'radialBar',
                                    toolbar: {
                                        show: true
                                    }
                                },
                                fill: {
                                    colors: ['#06d6a0'],
                                },
                                plotOptions: {
                                    radialBar: {
                                        dataLabels: {
                                            show: true,
                                            name: {
                                                show: false,
                                            },
                                            value: {
                                                show: true,
                                                fontWeight: 500,
                                                fontSize: '25px',
                                                offsetY: 0
                                            }
                                        }
                                    }
                                }
                            });
                            chart_contractor_sr<?= $id ?>.render();

                            $('#sr-contractor-date-<?= $id ?>').daterangepicker({
                                autoUpdateInput: false,
                                locale: {
                                    format: 'DD/MM/YYYY'
                                },

                            }, function(start, end, label) {
                                // function
                            });
                            $('#sr-contractor-date-<?= $id ?>').on('apply.daterangepicker', function(ev, picker) {
                                fetch("<?= site_url('operation/operation-dashboard/contractor-strip/by-date/') . $id ?>", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json"
                                        },
                                        body: JSON.stringify({
                                            'start_date': picker.startDate.format('YYYY-MM-DD'),
                                            'end_date': picker.endDate.format('YYYY-MM-DD'),
                                        })
                                    }).then(res => res.json())
                                    .then((data) => {
                                        console.log(data);
                                        let strip_ratio = data;
                                        let actual_cg = parseFloat(strip_ratio.actual_cg);
                                        let budget_cg = parseFloat(strip_ratio.budget_cg);
                                        let actual_ob = parseFloat(strip_ratio.actual_ob);
                                        let budget_ob = parseFloat(strip_ratio.budget_ob);
                                        let actual_stripping_ratio = actual_cg > 0 ? ((actual_ob) / (actual_cg)).toFixed(2) : 0;
                                        let budget_stripping_ratio = budget_cg > 0 ? (budget_ob / budget_cg).toFixed(2) : 0;
                                        let achievement = budget_stripping_ratio > 0 ? (actual_stripping_ratio / budget_stripping_ratio * 100).toFixed(2) : 0;

                                        chart_contractor_sr<?= $id ?>.updateSeries([achievement]);
                                        $("#sr-actual-<?= $id ?>").text(actual_stripping_ratio.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                        $("#sr-budget-<?= $id ?>").text(budget_stripping_ratio.toLocaleString("id-ID", {
                                            minimumFractionDigits: 2
                                        }));
                                    });

                                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                            });

                            $('#sr-contractor-date-<?= $id ?>').on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                            });
                        });
                    </script>
                    <!-- End Donut Chart -->
                </div>
                <div class="card-footer">
                    <div>
                        <table class="table table-borderless text-justify">
                            <tr style="border-bottom: 1px solid black;">
                                <td>Actual (Qty)</td>
                                <td><span id="sr-actual-<?= $id ?>"><?= number_format($actual_stripping_ratio, 2, ',', '.') ?></span></td>
                            </tr>
                            <tr>
                                <td>Budget (Qty)</td>
                                <td><span id="sr-budget-<?= $id ?>"><?= number_format($budget_stripping_ratio, 2, ',', '.') ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php $id++ ?>
    <?php endforeach ?>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">CONTRACTOR PERFORMANCE</h3>
                <table class="table table-hover table-bordered datatable">
                    <thead>
                        <tr>
                            <th scope="col">Tahun</th>
                            <th scope="col">Contractor</th>
                            <th scope="col">Target OB</th>
                            <th scope="col">Actual OB</th>
                            <th scope="col">Ach. OB</th>
                            <th scope="col">Target CG</th>
                            <th scope="col">Actual CG</th>
                            <th scope="col">Ach. CG</th>
                            <th scope="col">Target SR</th>
                            <th scope="col">Actual SR</th>
                            <th scope="col">Ach. SR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contractor_performance as $cp) : ?>
                            <tr>
                                <?php
                                $actual_sr = $cp['cg_total'] > 0 ? $cp['ob_total'] / $cp['cg_total'] : 0;
                                $budget_sr = $cp['cg_annualbudget_qt'] > 0 ? $cp['ob_annualbudget_qt'] / $cp['cg_annualbudget_qt'] : 0;
                                $ratio = round(handleDivision($actual_sr, $budget_sr * 100, 2)) . "%";
                                ?>
                                <td><?= $cp['year'] ?></td>
                                <td><?= $cp['contractor_name'] ?></td>
                                <td><?= number_format($cp['ob_annualbudget_qt'], 2, ',', '.') . " BCM"  ?></td>
                                <td><?= number_format($cp['ob_total'], 2, ',', '.') . " BCM" ?></td>
                                <td><?= round(handleDivision($cp['ob_total'] , $cp['ob_annualbudget_qt'] * 100, 2)) . " %" ?></td>
                                <td><?= number_format($cp['cg_annualbudget_qt'], 2, ',', '.') . " MT"  ?></td>
                                <td><?= number_format($cp['cg_total'], 2, ',', '.') . " MT"  ?></td>
                                <td><?= round(handleDivision($cp['cg_total'], $cp['cg_annualbudget_qt'] * 100, 2)) . " %" ?></td>
                                <td><?= number_format($budget_sr, 2, ',', '.') ?></td>
                                <td><?= number_format($actual_sr, 2, ',', '.') ?></td>
                                <td><?= $ratio ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div style="display: none;">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">CONTRACTOR PERFORMANCE: OVER BURDEN</h3>
                    <!-- Donut Chart -->
                    <div id="overburdenChart"></div>
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            new ApexCharts(document.querySelector("#overburdenChart"), {
                                series: [
                                    <?php if (isset($contractor_line['ob'])) : ?>
                                        <?php foreach ($contractor_line['ob'] as $index => $cg) : ?> {
                                                name: "<?= $index ?>",
                                                data: [
                                                    <?php foreach ($cg as $data) : ?> {
                                                            x: "<?= $bulan[$data['month']] ?>",
                                                            y: <?= $data['budget'] > 0 ? round($data['actual'] / $data['budget'] * 100, 2) : 0  ?>
                                                        },
                                                    <?php endforeach ?>
                                                ],
                                            },
                                        <?php endforeach ?>
                                    <?php endif ?>
                                ],
                                xaxis: {
                                    type: 'category',
                                },
                                yaxis: {
                                    max: 100
                                },
                                chart: {
                                    type: 'bar',
                                    height: 350
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '55%',
                                        endingShape: 'rounded'
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
                                fill: {
                                    opacity: 1
                                },
                            }).render();
                        });
                    </script>
                    <!-- End Donut Chart -->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">CONTRACTOR PERFORMANCE: COAL GETTING</h3>
                    <!-- Donut Chart -->
                    <div id="boxChart"></div>
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            new ApexCharts(document.querySelector("#boxChart"), {
                                series: [
                                    <?php if (isset($contractor_line['cg'])) : ?>
                                        <?php foreach ($contractor_line['cg'] as $index => $cg) : ?> {
                                                name: "<?= $index ?>",
                                                data: [
                                                    <?php foreach ($cg as $data) : ?> {
                                                            x: "<?= $bulan[$data['month']] ?>",
                                                            y: <?= $data['budget'] > 0 ? round($data['actual'] / $data['budget'] * 100, 2) : 0 ?>
                                                        },
                                                    <?php endforeach ?>
                                                ]
                                            },
                                        <?php endforeach ?>
                                    <?php endif ?>
                                ],
                                xaxis: {
                                    type: 'category',
                                },
                                yaxis: {
                                    max: 100
                                },
                                chart: {
                                    type: 'bar',
                                    height: 350
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '55%',
                                        endingShape: 'rounded'
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
                                fill: {
                                    opacity: 1
                                },
                            }).render();
                        });
                    </script>
                    <!-- End Donut Chart -->
                </div>
            </div>
        </div>
    </div>
</div>