<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    COGS Margin
                </div>
                <div id="COGSchart"></div>
                <script>
                    var options = {
                        chart: {
                            height: 350,
                            type: "line",
                            stacked: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
                        series: [

                            {
                                name: 'MTD',
                                type: 'column',
                                data: [<?php
                                        foreach ($COGS_margin as $row) {
                                            echo (substr($row['PER_SALES'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                            {
                                name: "YTD",
                                type: 'line',
                                data: [<?php
                                        foreach ($COGS_margin_YTD as $row) {
                                            echo (substr($row['Hasil'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                        ],
                        stroke: {
                            width: [4, 4, 4]
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "20%"
                            }
                        },
                        xaxis: {
                            categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
                        },
                        yaxis: [{
                                seriesName: 'MTD',
                                axisTicks: {
                                    show: true
                                },
                                axisBorder: {
                                    show: true,
                                },
                                label: {
                                  show: false,  
                                }
                            },
                            {
                                seriesName: 'MTD',
                                show: false
                            }
                        ],
                        tooltip: {
                            shared: false,
                            intersect: true,
                            x: {
                                show: false
                            }
                        },
                        legend: {
                            horizontalAlign: "left",
                            offsetX: 40
                        }
                    };

                    var COGSchart = new ApexCharts(document.querySelector("#COGSchart"), options);
                    COGSchart.render();
                </script>
                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                    <thead>
                        <tr>
                            <th>Name/Bulan</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Agust</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nov</th>
                            <th>Des</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MTD</td>
                            <?php foreach ($COGS_margin as $i) : ?>
                                <th><?= (Substr($i['PER_SALES'], 0, 5) ?? 0) . "%" ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>YTD</td>
                            <?php foreach ($COGS_margin_YTD as $i) : ?>
                                <th><?= (Substr($i['Hasil'], 0, 5) ?? 0) . "%" ?></th>
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
                    Gross Profit Margin
                </div>
                <div id="GPMchart"></div>
                <script>
                    var options2 = {
                        chart: {
                            height: 350,
                            type: "line",
                            stacked: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
                        series: [

                            {
                                name: 'MTD',
                                type: 'column',
                                data: [<?php
                                        foreach ($Gross_Profit_Margin as $row) {
                                            echo (substr($row['PER_SALES'], 0, 5) ??'0');
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                            {
                                name: "YTD",
                                type: 'line',
                                data: [<?php
                                        foreach ($Gross_Profit_MarginYTD as $row) {
                                            echo (substr($row['Hasil'], 0, 5) ??'0');
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                        ],
                        stroke: {
                            width: [4, 4, 4]
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "20%"
                            }
                        },
                        xaxis: {
                            categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
                        },
                        yaxis: [{
                                seriesName: 'MTD',
                                axisTicks: {
                                    show: true
                                },
                                axisBorder: {
                                    show: true,
                                },
                                
                            },
                            {
                                seriesName: 'MTD',
                                show: false
                            },  
                        ],
                        tooltip: {
                            shared: false,
                            intersect: true,
                            x: {
                                show: false
                            }
                        },
                        legend: {
                            horizontalAlign: "left",
                            offsetX: 40
                        }
                    };

                    var GPMchart = new ApexCharts(document.querySelector("#GPMchart"), options2);
                    GPMchart.render();
                </script>
                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                    <thead>
                        <tr>
                            <th>Name/Bulan</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Agust</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nov</th>
                            <th>Des</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MTD</td>
                            <?php foreach ($Gross_Profit_Margin as $i) : ?>
                                <th><?= (Substr($i['PER_SALES'], 0, 5) ?? 0) . "%" ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>YTD</td>
                            <?php foreach ($Gross_Profit_MarginYTD as $i) : ?>
                                <th><?= (Substr($i['Hasil'], 0, 5) ?? 0) . "%" ?></th>
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
                    Opr Profit Margin
                </div>
                <div id="OPMchart"></div>
                <script>
                    var options3 = {
                        chart: {
                            height: 350,
                            type: "line",
                            stacked: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
                        series: [

                            {
                                name: 'MTD',
                                type: 'column',
                                data: [<?php
                                        foreach ($Opr_Profit_Margin as $row) {
                                            echo (substr($row['PER_SALES'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                            {
                                name: "YTD",
                                type: 'line',
                                data: [<?php
                                        foreach ($Opr_Profit_MarginYTD as $row) {
                                            echo (substr($row['Hasil'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                        ],
                        stroke: {
                            width: [4, 4, 4]
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "20%"
                            }
                        },
                        xaxis: {
                            categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
                        },
                        yaxis: [{
                                seriesName: 'MTD',
                                axisTicks: {
                                    show: true
                                },
                                axisBorder: {
                                    show: true,
                                },
                               
                            },
                            {
                                seriesName: 'MTD',
                                show: false
                            }, 
                        ],
                        tooltip: {
                            shared: false,
                            intersect: true,
                            x: {
                                show: false
                            }
                        },
                        legend: {
                            horizontalAlign: "left",
                            offsetX: 40
                        }
                    };

                    var OPMchart = new ApexCharts(document.querySelector("#OPMchart"), options3);
                    OPMchart.render();
                </script>

                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                    <thead>
                        <tr>
                            <th>Name/Bulan</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Agust</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nov</th>
                            <th>Des</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MTD</td>
                            <?php foreach ($Opr_Profit_Margin as $i) : ?>
                                <th><?= (Substr($i['PER_SALES'], 0, 5) ?? 0) . "%" ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>YTD</td>
                            <?php foreach ($Opr_Profit_MarginYTD as $i) : ?>
                                <th><?= (Substr($i['Hasil'], 0, 5) ?? 0) . "%" ?></th>
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
                    EBT Margin
                </div>
                <div id="EBTchart"></div>
                <script>
                    var options4 = {
                        chart: {
                            height: 350,
                            type: "line",
                            stacked: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
                        series: [

                            {
                                name: 'MTD',
                                type: 'column',
                                data: [<?php
                                        foreach ($EBT_Margin as $row) {
                                            echo (substr($row['PER_SALES'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                            {
                                name: "YTD",
                                type: 'line',
                                data: [<?php
                                        foreach ($EBT_MarginYTD as $row) {
                                            echo (substr($row['Hasil'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                        ],
                        stroke: {
                            width: [4, 4, 4]
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "20%"
                            }
                        },
                        xaxis: {
                            categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
                        },
                        yaxis: [{
                                seriesName: 'MTD',
                                axisTicks: {
                                    show: true
                                },
                                axisBorder: {
                                    show: true,
                                },
                            },
                            {
                                seriesName: 'MTD',
                                show: false
                            }, 
                        ],
                        tooltip: {
                            shared: false,
                            intersect: true,
                            x: {
                                show: false
                            }
                        },
                        legend: {
                            horizontalAlign: "left",
                            offsetX: 40
                        }
                    };

                    var EBTchart = new ApexCharts(document.querySelector("#EBTchart"), options4);
                    EBTchart.render();
                </script>

                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                    <thead>
                        <tr>
                            <th>Name/Bulan</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Agust</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nov</th>
                            <th>Des</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MTD</td>
                            <?php foreach ($EBT_Margin as $i) : ?>
                                <th><?= (Substr($i['PER_SALES'], 0, 5) ?? 0) . "%" ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>YTD</td>
                            <?php foreach ($EBT_MarginYTD as $i) : ?>
                                <th><?= (Substr($i['Hasil'], 0, 5) ?? 0) . "%" ?></th>
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
                    EAT Margin
                </div>
                <div id="EATchart"></div>
                <script>
                    var options5 = {
                        chart: {
                            height: 350,
                            type: "line",
                            stacked: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
                        series: [

                            {
                                name: 'MTD',
                                type: 'column',
                                data: [<?php
                                        foreach ($eatMargin as $row) {
                                            echo (substr($row['per_saless'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                            {
                                name: "YTD",
                                type: 'line',
                                data: [<?php
                                        foreach ($eatMarginYTD as $row) {
                                            echo (substr($row['Hasil'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                        ],
                        stroke: {
                            width: [4, 4, 4]
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "20%"
                            }
                        },
                        xaxis: {
                            categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
                        },
                        yaxis: [{
                                seriesName: 'MTD',
                                axisTicks: {
                                    show: true
                                },
                                axisBorder: {
                                    show: true,
                                },
                                
                            },
                            {
                                seriesName: 'MTD',
                                show: false
                            },
                        ],
                        tooltip: {
                            shared: false,
                            intersect: true,
                            x: {
                                show: false
                            }
                        },
                        legend: {
                            horizontalAlign: "left",
                            offsetX: 40
                        }
                    };

                    var EATchart = new ApexCharts(document.querySelector("#EATchart"), options5);
                    EATchart.render();
                </script>

                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                    <thead>
                        <tr>
                            <th>Name/Bulan</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Agust</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nov</th>
                            <th>Des</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MTD</td>
                            <?php foreach ($eatMargin as $i) : ?>
                                <th><?= (Substr($i['per_saless'], 0, 5) ?? 0) . "%" ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>YTD</td>
                            <?php foreach ($eatMarginYTD as $i) : ?>
                                <th><?= (Substr($i['Hasil'], 0, 5) ?? 0) . "%" ?></th>
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
                    EBITDA Margin
                </div>
                <div id="EBITDAchart"></div>
                <script>
                    var options6 = {
                        chart: {
                            height: 350,
                            type: "line",
                            stacked: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
                        series: [

                            {
                                name: 'MTD',
                                type: 'column',
                                data: [<?php
                                        foreach ($ebitdaMargin as $row) {
                                            echo (substr($row['per_saless'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                            {
                                name: "YTD",
                                type: 'line',
                                data: [<?php
                                        foreach ($ebitdaMarginYTD as $row) {
                                            echo (substr($row['Hasil'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                        ],
                        stroke: {
                            width: [4, 4, 4]
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "20%"
                            }
                        },
                        xaxis: {
                            categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
                        },
                        yaxis: [{
                                seriesName: 'MTD',
                                axisTicks: {
                                    show: true
                                },
                                axisBorder: {
                                    show: true,
                                },
                               
                            },
                            {
                                seriesName: 'MTD',
                                show: false
                            }, 
                        ],
                        tooltip: {
                            shared: false,
                            intersect: true,
                            x: {
                                show: false
                            }
                        },
                        legend: {
                            horizontalAlign: "left",
                            offsetX: 40
                        }
                    };

                    var EBITDAchart = new ApexCharts(document.querySelector("#EBITDAchart"), options6);
                    EBITDAchart.render();
                </script>

                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                    <thead>
                        <tr>
                            <th>Name/Bulan</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Agust</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nov</th>
                            <th>Des</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MTD</td>
                            <?php foreach ($ebitdaMargin as $i) : ?>
                                <th><?= (Substr($i['per_saless'], 0, 5) ?? 0) . "%" ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>YTD</td>
                            <?php foreach ($ebitdaMarginYTD as $i) : ?>
                                <th><?= (Substr($i['Hasil'], 0, 5) ?? 0) . "%" ?></th>
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
                    EBDA Margin
                </div>
                <div id="EBDAchart"></div>
                <script>
                    var options7 = {
                        chart: {
                            height: 350,
                            type: "line",
                            stacked: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
                        series: [

                            {
                                name: 'MTD',
                                type: 'column',
                                data: [<?php
                                        foreach ($ebdaMargin as $row) {
                                            echo (substr($row['per_saless'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                            {
                                name: "YTD",
                                type: 'line',
                                data: [<?php
                                        foreach ($ebdaMarginYTD as $row) {
                                            echo (substr($row['Hasil'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                        ],
                        stroke: {
                            width: [4, 4, 4]
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "20%"
                            }
                        },
                        xaxis: {
                            categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
                        },
                        yaxis: [{
                                seriesName: 'MTD',
                                axisTicks: {
                                    show: true
                                },
                                axisBorder: {
                                    show: true,
                                },
                                
                            },
                            {
                                seriesName: 'MTD',
                                show: false
                            }, 
                        ],
                        tooltip: {
                            shared: false,
                            intersect: true,
                            x: {
                                show: false
                            }
                        },
                        legend: {
                            horizontalAlign: "left",
                            offsetX: 40
                        }
                    };

                    var EBDAchart = new ApexCharts(document.querySelector("#EBDAchart"), options7);
                    EBDAchart.render();
                </script>

                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                    <thead>
                        <tr>
                            <th>Name/Bulan</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Agust</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nov</th>
                            <th>Des</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MTD</td>
                            <?php foreach ($ebdaMargin as $i) : ?>
                                <th><?= (Substr($i['per_saless'], 0, 5) ?? 0) . "%" ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>YTD</td>
                            <?php foreach ($ebdaMarginYTD as $i) : ?>
                                <th><?= (Substr($i['Hasil'], 0, 5) ?? 0) . "%" ?></th>
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
                    Net Profit Margin
                </div>
                <div id="NETchart"></div>
                <script>
                    var options8 = {
                        chart: {
                            height: 350,
                            type: "line",
                            stacked: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#00FFFF', '#C5EDAC', '#66C7F4'],
                        series: [

                            {
                                name: 'MTD',
                                type: 'column',
                                data: [<?php
                                        foreach ($eatMargin as $row) {
                                            echo (substr($row['per_saless'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                            {
                                name: "YTD",
                                type: 'line',
                                data: [<?php
                                        foreach ($eatMarginYTD as $row) {
                                            echo (substr($row['Hasil'], 0, 5));
                                            echo ',';
                                        }
                                        ?> //contoh data 21.1, 23, 33.1, 34, 44.1, 44.9, 56.5, 58.5
                                ]
                            },
                        ],
                        stroke: {
                            width: [4, 4, 4]
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: "20%"
                            }
                        },
                        xaxis: {
                            categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'Desember']
                        },
                        yaxis: [{
                                seriesName: 'MTD',
                                axisTicks: {
                                    show: true
                                },
                                axisBorder: {
                                    show: true,
                                },
                                
                            },
                            {
                                seriesName: 'MTD',
                                show: false
                            },
                        ],
                        tooltip: {
                            shared: false,
                            intersect: true,
                            x: {
                                show: false
                            }
                        },
                        legend: {
                            horizontalAlign: "left",
                            offsetX: 40
                        }
                    };

                    var NETchart = new ApexCharts(document.querySelector("#NETchart"), options8);
                    NETchart.render();
                </script>

                <table style="font-size: 0.8rem;" class="table-bordered table table-sm table-fixed">
                    <thead>
                        <tr>
                            <th>Name/Bulan</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>June</th>
                            <th>July</th>
                            <th>Agust</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nov</th>
                            <th>Des</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MTD</td>
                            <?php foreach ($eatMargin as $i) : ?>
                                <th><?= (Substr($i['per_saless'], 0, 5) ?? 0) . "%" ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>YTD</td>
                            <?php foreach ($eatMarginYTD as $i) : ?>
                                <th><?= (Substr($i['Hasil'], 0, 5) ?? 0) . "%" ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>