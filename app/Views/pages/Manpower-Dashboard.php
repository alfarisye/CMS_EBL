<?php

function rupiah($angka)
{
    $hasil_rupiah = "Rp." . number_format($angka ?? 0, 2, ',', '.');
    return $hasil_rupiah;
}
?>

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
        <h1>CSR - Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">CSR</li>
                <li class="breadcrumb-item active"><a href="<?= site_url("CSRAct/dashboard") ?>">Dashboard</a></li>
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
        <div class="row d-flex justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Total Tenaga Kerja Oprational</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px; color:#000000;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $operational_kerja['total'] ?? 0 ?> ORANG</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Total Tenaga Kerja Administrasi</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px; color: #000000;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $Administrasi_kerja['total']  ?? 0  ?> ORANG</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Total Tenaga Kerja Pengawas</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $Pengawas_kerja['total'] ?? 0  ?> ORANG</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px"> Total Seluruh Tenaga Kerja</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $total_kerja['total'] ?? 0  ?> ORANG</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row d-flex justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Total Jam Kerja Oprational</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px; color:#000000;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $operational_jam['total'] ?? 0 ?> Jam</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Total Jam Kerja Administrasi</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px; color: #000000;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $Administrasi_jam['total'] ?? 0 ?> Jam</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Total Jam Kerja Pengawas</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $Pengawas_jam['total'] ?? 0 ?> Jam</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px"> Total Seluruh Jam Kerja</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $total_jam['total'] ?? 0 ?> Jam</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row d-flex justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Total Tenaga Kerja Perusahaan Tambang</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px; color:#000000;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $perusahaan_tambangkerja['total'] ?? 0 ?> Orang</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Total Jam Kerja Perusahaan Tambang</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px; color: #000000;"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $perusahaan_tambangjam['total'] ?? 0 ?> Jam</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px">Total Tenaga Kerja Kontraktor</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $kontraktor_kerja['total'] ?? 0 ?> Orang</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px"> Total Jam Kerja Kontraktor</h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-team-fill" style="font-size: 50px;color: #000000"></i>
                            </div>
                            <div class="ps-3 d-flex justify-content-end">
                                <div class="row">
                                    <h3 style="font-size: 16px"><?= $kontraktor_jam['total'] ?? 0 ?> Jam</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pagetitle">
            <h1>Perusahaan Tambang</h1>
        </div>

        <!-- charts -->
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tanaga Kerja Operasional</h5>

                        <!-- Column Chart -->
                        <div id="ccChart"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart"), {
                                    series: [{
                                        name: 'Oprasional',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_perusahaan_opra as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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


            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tanaga Kerja Administrasi</h5>

                        <!-- Column Chart -->
                        <div id="ccChart2"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart2"), {
                                    series: [{
                                        name: 'Administrasi',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_perusahaan_admin as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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
                                    colors: ["#FF8C00"],
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
                                                color: "#FF8C00",
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tanaga Kerja Pengawas</h5>

                        <!-- Column Chart -->
                        <div id="ccChart3"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart3"), {
                                    series: [{
                                        name: 'Pengawas',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_perusahaan_peng as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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
                                    colors: ["#7FFF00"],
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Jam Kerja Oprational</h5>

                        <!-- Column Chart -->
                        <div id="ccChart4"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart4"), {
                                    series: [{
                                        name: 'Oprasional',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_perusahaan_jamopra as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Jam Kerja Administrasi</h5>

                        <!-- Column Chart -->
                        <div id="ccChart5"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart5"), {
                                    series: [{
                                        name: 'Administrasi',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_perusahaan_jamadmin as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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
                                    colors: ["#FF8C00"],
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
                                                color: "#FF8C00",
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Jam Kerja Pengawas</h5>

                        <!-- Column Chart -->
                        <div id="ccChart6"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart6"), {
                                    series: [{
                                        name: 'Pengawas',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_perusahaan_jampeng as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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
                                    colors: ["#7FFF00"],
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

        <div class="pagetitle">
            <h1>Kontraktor</h1>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tenaga Kerja Oprasional</h5>

                        <!-- Column Chart -->
                        <div id="ccChart7"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart7"), {
                                    series: [{
                                        name: 'Oprasional',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_kontrak_opra as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tenaga Kerja Administrasi</h5>

                        <!-- Column Chart -->
                        <div id="ccChart8"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart8"), {
                                    series: [{
                                        name: 'Administrasi',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_kontrak_admin as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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
                                    colors: ["#FF8C00"],
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
                                                color: "#FF8C00",
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tenaga Kerja Pengawas</h5>

                        <!-- Column Chart -->
                        <div id="ccChart9"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart9"), {
                                    series: [{
                                        name: 'Pengawas',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_kontrak_peng as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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
                                    colors: ["#7FFF00"],
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Jam Kerja Oprational</h5>

                        <!-- Column Chart -->
                        <div id="ccChart10"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart10"), {
                                    series: [{
                                        name: 'Oprasional',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_kontrak_jamopra as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Jam Kerja Administrasi</h5>

                        <!-- Column Chart -->
                        <div id="ccChart11"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart11"), {
                                    series: [{
                                        name: 'Administrasi',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_kontrak_jamadmin as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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
                                    colors: ["#FF8C00"],
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
                                                color: "#FF8C00",
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Jam Kerja Pengawas</h5>

                        <!-- Column Chart -->
                        <div id="ccChart12"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#ccChart12"), {
                                    series: [{
                                        name: 'Pengawas',
                                        type: 'line',
                                        data: [
                                            <?php foreach ($crt_kontrak_jampeng as $p) : ?> {
                                                    x: "<?= $bulan[$p['bulan']] ?>",
                                                    y: <?= $p['value'] ?? 0 ?>,
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
                                    colors: ["#7FFF00"],
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
    </section>
</main>


<?= $this->endSection() ?>