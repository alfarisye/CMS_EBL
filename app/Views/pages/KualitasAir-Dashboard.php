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


<?= $this->extend('templates/layout') ?>



<?= $this->section('main') ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Kualitas Air Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">K3LH</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("kualitasair/monitoring") ?>">Monitoring</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Dasboard </h5>
            <div class="row">
                <div class="col-sm-2">
                    <a href="<?= site_url('k3lh/monitoring') ?>">
                        <button type="button" class="btn btn-primary mb-2">SHE Accident Monitoring</button>
                    </a>
                </div>
                <div class="col-sm-2">
                    <a href="<?= site_url('kualitasair/monitoring') ?>">
                        <button type="button" class="btn btn-primary mb-2">Kualitas Air Monitoring</button>
                    </a>
                </div>
                <div class="col-sm-2">
                    <a href="<?= site_url('Manpower/monitoring') ?>">
                        <button type="button" class="btn btn-primary mb-2">Manpower Monitoring</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>

    <section class="section">

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Debit Air</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-activity text-warning" style="font-size: 3rem;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end ">
                                <div class="row">
                                    <h3 class="text-warning font-size: 16px"><?= (substr(($debit_air['rata'] ?? 0), 0, 6))?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">pH</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-activity text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3 class="text-primary font-size: 16px"><?= (substr(($Ph['rata'] ?? 0), 0, 4))?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">TSS</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-activity text-success" style="font-size: 3rem;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <h3 class="text-success font-size: 16px"><?= (substr(($Tss['rata'] ?? 0), 0, 6)) ?></h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- charts -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Debit Air</h5>

                        <!-- Column Chart -->
                        <div id="ccChart"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart"), {
                                    series: [{
                                        name: 'Debit Air',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($debit_chart as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['rata'] ?? 0 ?>,
                                                },
                                            <?php endforeach; ?>
                                        ]
                                    }, ],
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

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ph</h5>

                        <!-- Column Chart -->
                        <div id="ccChart1"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart1"), {
                                    series: [{
                                        name: 'Ph',
                                        type: 'bar',
                                        data: [
                                            <?php foreach ($Ph_chart as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['rata'] ?? 0 ?>,
                                                },
                                            <?php endforeach; ?>
                                        ]
                                    }, {
                                        name: 'Ph >= 6',
                                        type: 'line',
                                        data: [
                                            <?php for ($i = 1; $i <= 12; $i++) { ?> {
                                                    x: "<?= $bulan[$i] ?>",
                                                    y: "6",
                                                },
                                            <?php } ?>
                                        ]
                                    }, {
                                        name: 'Ph <= 9',
                                        type: 'line',
                                        data: [
                                            <?php for ($i = 1; $i <= 12; $i++) { ?> {
                                                    x: "<?= $bulan[$i] ?>",
                                                    y: "9",
                                                },
                                            <?php } ?>
                                        ]
                                    },],
                                    chart: {

                                        height: 350,
                                        stacked: false,
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
                                        min: 2,
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

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tss</h5>

                        <!-- Column Chart -->
                        <div id="ccChart2"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart2"), {
                                    series: [{
                                        name: 'Tss',
                                        type: 'bar',
                                        data: [
                                            <?php foreach ($tss_chart as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['rata'] ?? 0 ?>,
                                                },
                                            <?php endforeach; ?>
                                        ]
                                    },{
                                        name: 'TSS >= 0',
                                        type: 'line',
                                        data: [
                                            <?php for ($i = 1; $i <= 12; $i++) { ?> {
                                                    x: "<?= $bulan[$i] ?>",
                                                    y: "0",
                                                },
                                            <?php } ?>
                                        ]
                                    }, {
                                        name: 'TSS <= 400',
                                        type: 'line',
                                        data: [
                                            <?php for ($i = 1; $i <= 12; $i++) { ?> {
                                                    x: "<?= $bulan[$i] ?>",
                                                    y: "400",
                                                },
                                            <?php } ?>
                                        ]
                                    }, ],
                                    chart: {

                                        height: 350,
                                        stacked: false,
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



</main>


<?= $this->endSection() ?>