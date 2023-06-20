<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor</title>
    <link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        ::-webkit-scrollbar {
            width: 1px;
            height: 1px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 1px grey; 
            border-radius: 1px;
        }
        
        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: red; 
            border-radius: 1px;
        }

    </style>
</head>

<body>
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
    $months=['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    ?>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="<?= base_url("assets/js/axios.js") ?>"></script>
    <script src="<?= base_url("assets/js/date_fns.js") ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet">
    </link>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide-extension-auto-scroll@0.5.3/dist/js/splide-extension-auto-scroll.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <div id="monitor">
        <div style="height:100vh;width:100vw;position:absolute;z-index:1000;">

        </div>
        <div v-if="datanya" class="p-4">
            <section class="splide  mt-3" aria-label="Splide Basic HTML Example">
                <div class="splide__track">
                    <div class="splide__list">
                        <!-- slide 1 -->
                        <div class="splide__slide p-3">
                            <div>
                                <div class="row">
                                    <div class="col-2">
                                        <a href="<?= site_url('/') ?>" class="float-left ml-3">
                                            <img src="<?= base_url("assets/img/logoHG.webp") ?>" style="height:100px;" alt="" />
                                        </a>
                                    </div>
                                    <div class="col-10">
                                        <div style="position:absolute;top:-36px;">
                                            <p class="font-bold text-6xl p-0 m-0">DAILY PRODUCTION</p>
                                            <p style="color:#227c70;" class="font-semibold text-4xl p-0 m-0 "><?= date('d') ?> <?= $months[(int)date('m')] ?> <?= date('Y') ?></p>
                                        </div>
                                    </div>
                                </div>
                                <img src="<?= base_url("assets/img/top-border-icon.PNG") ?>" style="height:5vw;position:absolute;top:0px;right:0px;" class="float-right" alt="" />
                                <hr>
                                <div style="height:50px;"></div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4.5vh;color:#1c315e;font-weight:bold;">OVER BURDEN (OB)</p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0" v-if="daily">{{!isNaN(daily['daily_OB'])?number_format(daily['daily_OB']):0}}</p>
                                                            <p class="text-1xl font-semibold m-0 p-0" style="color:#227c70;">BCM</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="obDaily"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4.5vh;color:#1c315e;font-weight:bold;">COAL GETTING (CG)</p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0" v-if="daily">{{!isNaN(daily['daily_CG'])?number_format(daily['daily_CG']):0}}</p>
                                                            <p class="text-1xl font-semibold m-0 p-0" style="color:#227c70;">MT</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="cgDaily"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4vh;color:#1c315e;font-weight:bold;">STRIPPING RATIO (SR)</p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0" v-if="daily">{{!isNaN(daily['daily_STRIPPING'])?number_format(daily['daily_STRIPPING']):0}}</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="strippingDaily"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4.5vh;color:#1c315e;font-weight:bold;">BARGING </p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0">{{!isNaN(daily['daily_BARGING'])?number_format(daily['daily_BARGING']):0}}</p>
                                                            <p class="text-1xl font-semibold m-0 p-0" style="color:#227c70;">MT</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="bargingDaily"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-4" >
                                        <div style="height:60px;"></div>
                                        <div id="lineChartDaily"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end Slide 1 -->
                        <!-- slide 2 -->
                        <div class="splide__slide p-3">
                            <div>
                                <div class="row">
                                    <div class="col-2">
                                        <a href="<?= site_url('/') ?>" class="float-left ml-3">
                                            <img src="<?= base_url("assets/img/logoHG.webp") ?>" style="height:100px;" alt="" />
                                        </a>
                                    </div>
                                    <div class="col-10">
                                        <div style="position:absolute;top:-36px;">
                                            <p class="font-bold text-6xl p-0 m-0">MONTHLY PRODUCTION</p>
                                            <p style="color:#227c70;" class="font-semibold text-4xl p-0 m-0 "><?= $months[(int)date('m')] ?> <?= date('Y') ?></p>
                                        </div>
                                    </div>
                                </div>
                                <img src="<?= base_url("assets/img/top-border-icon.PNG") ?>" style="height:5vw;position:absolute;top:0px;right:0px;" class="float-right" alt="" />
                                <hr>
                                <div style="height:50px;"></div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4.5vh;color:#1c315e;font-weight:bold;">OVER BURDEN (OB)</p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0" v-if="monthly">{{monthly['ob_production']['actual']?number_format(monthly['ob_production']['actual']):0,00}}</p>
                                                            <p class="text-1xl font-semibold m-0 p-0" style="color:#227c70;">BCM</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="obmonthly"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4.5vh;color:#1c315e;font-weight:bold;">COAL GETTING (CG)</p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0" v-if="monthly">{{monthly['cg_production']['actual']?number_format(monthly['cg_production']['actual']):0,00}}</p>
                                                            <p class="text-1xl font-semibold m-0 p-0" style="color:#227c70;">MT</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="cgmonthly"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4vh;color:#1c315e;font-weight:bold;">STRIPPING RATIO (SR)</p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0" v-if="monthly">{{monthly['stripping_ytd']['actual_cg'] !=0 ?(monthly['stripping_ytd']['actual_ob'] / monthly['stripping_ytd']['actual_cg']).toFixed(2):0}}</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="strippingmonthly"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4.5vh;color:#1c315e;font-weight:bold;">BARGING </p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0">{{monthly['barging_ytd']['total']?number_format(monthly['barging_ytd']['total']):0}}</p>
                                                            <p class="text-1xl font-semibold m-0 p-0" style="color:#227c70;">MT</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="bargingmonthly"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-4" >
                                        <div style="height:60px;"></div>
                                        <div id="lineChartmonthly"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end Slide 2 -->
                        <!-- slide 3 -->
                        <div class="splide__slide p-3">
                            <div>
                                <div class="row">
                                    <div class="col-2">
                                        <a href="<?= site_url('/') ?>" class="float-left ml-3">
                                            <img src="<?= base_url("assets/img/logoHG.webp") ?>" style="height:100px;" alt="" />
                                        </a>
                                    </div>
                                    <div class="col-10">
                                        <div style="position:absolute;top:-36px;">
                                            <p class="font-bold text-6xl p-0 m-0">YEARLY PRODUCTION</p>
                                            <p style="color:#227c70;" class="font-semibold text-4xl p-0 m-0 "><?= date('Y') ?></p>
                                        </div>
                                    </div>
                                </div>
                                <img src="<?= base_url("assets/img/top-border-icon.PNG") ?>" style="height:5vw;position:absolute;top:0px;right:0px;" class="float-right" alt="" />
                                <hr>
                                <div style="height:50px;"></div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4.5vh;color:#1c315e;font-weight:bold;">OVER BURDEN (OB)</p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0" v-if="yearly">{{yearly['ob_production']['actual']?number_format(yearly['ob_production']['actual']):0,00}}</p>
                                                            <p class="text-1xl font-semibold m-0 p-0" style="color:#227c70;">BCM</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="obyearly"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4.5vh;color:#1c315e;font-weight:bold;">COAL GETTING (CG)</p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0" v-if="yearly">{{yearly['cg_production']['actual']?number_format(yearly['cg_production']['actual']):0,00}}</p>
                                                            <p class="text-1xl font-semibold m-0 p-0" style="color:#227c70;">MT</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="cgyearly"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4vh;color:#1c315e;font-weight:bold;">STRIPPING RATIO (SR)</p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0" v-if="yearly">{{yearly['stripping_ytd']['actual_cg'] !=0 ?(yearly['stripping_ytd']['actual_ob'] / yearly['stripping_ytd']['actual_cg']).toFixed(2):0}}</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="strippingyearly"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 p-3">
                                                <div class="bg-gray-300 p-4 shadow rounded-lg">
                                                    <br>
                                                    <p class="font-bold m-0 p-0" style="font-size:4.5vh;color:#1c315e;font-weight:bold;">BARGING </p>
                                                    <div class="row">
                                                        <div class="col-7 mt-6">
                                                            <br>
                                                            <p style="font-size:4.4vh;" class=" font-bold m-0 p-0">{{yearly['barging_ytd']['total']?number_format(yearly['barging_ytd']['total']):0}}</p>
                                                            <p class="text-1xl font-semibold m-0 p-0" style="color:#227c70;">MT</p>
                                                        </div>
                                                        <div class="col-5">
                                                            <div id="bargingyearly"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-4" >
                                        <div style="height:60px;"></div>
                                        <div id="lineChartyearly"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end Slide 3 -->
                    </div>
                </div>
            </section>
            <div class="row">
                <div class="col-sm-3 " style="background:#1C315E;">
                    <p class="text-center text-4xl  font-semibold text-white">{{jam}}</p>
                </div>
                <div class="col-sm-9" style="background:#227c70;">
                    <p class="text-center text-4xl  font-semibold text-white">COAL MONITORING SYSTEM PT ENERGI BATUBARA LESTARI</p>
                </div>
            </div>
        </div>
        <div v-else>
            <div style="height:100vh;" class="d-flex justify-content-center align-items-center shadow-lg rounded-lg">
                <div style="width:100%">
                    <p class="text-center text-2xl font-semibold animate__animated animate__pulse animate__infinite">
                        Loading ...
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script>
        var optionLingkaran={
            chart: {
                height: 180,
                type: 'radialBar',
                toolbar: {
                    show: false
                }
            },
            fill: {
                colors: ['#1c315e'],
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
                            fontWeight: 150,
                            fontSize: '20px',
                            offsetY: 0
                        }
                    }
                }
            },
        }
        var optionLine={
            chart: {
                type: 'bar',
                height: 600,
                toolbar: {
                    show: false
                }
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
            legend: {
                position: 'top',
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['PT CK', 'PT GMT', 'PT HRS'],
            },
            fill: {
                opacity: 1,

            },
        }
        const {
            createApp
        } = Vue
        createApp({
            data() {
                return {
                    vdata: {},
                    daily:null,
                    monthly:null,
                    yearly:null,
                    jam:'',
                    datanya:false
                }
            },
            methods: {
                async getData(){
                    let daily = await axios.get('https://ebl-cms.hasnurgroup.com/index.php/api/monitor?month=<?= (int)date('m') ?>&year=<?= date('Y') ?>&time=daily&date=<?= date('Y-m-d') ?>')
                    let monthly = await axios.get('https://ebl-cms.hasnurgroup.com/index.php/api/monitor?month=<?= (int)date('m') ?>&year=<?= date('Y') ?>&time=monthly')
                    let yearly = await axios.get('https://ebl-cms.hasnurgroup.com/index.php/api/monitor')
                    this.daily=daily.data
                    this.daily['daily_OB']=this.daily['ob_production']['actual']?parseFloat(this.daily['ob_production']['actual']).toFixed(2):0,00;
                    this.daily['daily_CG']=this.daily['cg_production']['actual']?parseFloat(this.daily['cg_production']['actual']).toFixed(2):0,00;
                    this.daily['daily_STRIPPING']=this.daily['stripping_ytd']['actual_cg'] !=0 ? parseFloat(this.daily['stripping_ytd']['actual_ob'] / this.daily['stripping_ytd']['actual_cg']).toFixed(2):0;
                    this.daily['daily_BARGING']=this.daily['barging_ytd']['total']?parseFloat(this.daily['barging_ytd']['total']).toFixed(2):0;
                    console.log('daily',daily.data)
                    this.monthly=monthly.data
                    console.log('monthly',monthly.data)
                    this.yearly=yearly.data
                    console.log('yearly',yearly.data)
                    this.datanya=true;
                    setTimeout(() => {
                        var splide = new Splide('.splide', {
                            perPage: 1,
                            autoplay: true
                        });
                        splide.mount();
                        splide.on( 'move', () => {
                            console.log( 'Current slide index:', splide.index );
                            if(splide.index==2){
                                setTimeout(() => {
                                    document.querySelector('.splide__arrow--prev').click()
                                    document.querySelector('.splide__arrow--prev').click()
                                }, 8000);
                            }
                        } );
                        this.renderChart();
                    }, 1000);
                    this.$forceUpdate();
                },
                renderChart() {
                    let ob1Val=parseInt(this.daily['ob_production']['budget']) !=0 ? parseInt(this.daily['ob_production']['actual']/this.daily['ob_production']['budget'] * 100) :0;
                    var ob1 = new ApexCharts(document.querySelector("#obDaily"), {
                        series: [ !isNaN(ob1Val)?ob1Val:0 ],
                        ...optionLingkaran
                    });
                    ob1.render();
                    let cg1Val=parseInt(this.daily['cg_production']['budget']) !=0 ? parseInt(this.daily['cg_production']['actual']/this.daily['cg_production']['budget'] * 100) :0
                    var cg1 = new ApexCharts(document.querySelector("#cgDaily"), {
                        series: [ !isNaN(cg1Val)?cg1Val:0 ],
                        ...optionLingkaran
                    });
                    cg1.render();
                    var actual_sr=this.daily['stripping_ytd']['actual_cg']!=0?this.daily['stripping_ytd']['actual_ob']/this.daily['stripping_ytd']['actual_cg']:0;
                    var budget_sr=this.daily['stripping_ytd']['budget_cg']!=0?this.daily['stripping_ytd']['budget_ob']/this.daily['stripping_ytd']['budget_cg']:0;
                    let strippingVal=parseInt(budget_sr)!=0?(actual_sr/budget_sr)*100:0;
                    var stripping1 = new ApexCharts(document.querySelector("#strippingDaily"), {
                        series: [ !isNaN(strippingVal)?strippingVal:0  ],
                        ...optionLingkaran
                    });
                    stripping1.render();
                    let bargingVal=this.daily['barging_ytd']['target']??0 !=0 ? parseInt(this.daily['barging_ytd']['total'])/parseInt(this.daily['barging_ytd']['target']??0) * 100 :0;
                    var barging1 = new ApexCharts(document.querySelector("#bargingDaily"), {
                        series: [ !isNaN(bargingVal)?bargingVal:0  ],
                        ...optionLingkaran
                    });
                    barging1.render();
                    // -=================================
                    var cgCK= this.daily['cg_production_contractor'].filter(e=>e.contractor=='CK')[0]?this.daily['cg_production_contractor'].filter(e=>e.contractor=='CK')[0]['actual']:0
                    var cgGMT=this.daily['cg_production_contractor'].filter(e=>e.contractor=='GMT')[0]?this.daily['cg_production_contractor'].filter(e=>e.contractor=='GMT')[0]['actual']:0
                    var cgHRS=this.daily['cg_production_contractor'].filter(e=>e.contractor=='HRS')[0]?this.daily['cg_production_contractor'].filter(e=>e.contractor=='HRS')[0]['actual']:0
                    var obCK= this.daily['ob_production_contractor'].filter(e=>e.id_contractor=='3')[0]?this.daily['ob_production_contractor'].filter(e=>e.id_contractor=='3')[0]['actual']:0
                    var obGMT=this.daily['ob_production_contractor'].filter(e=>e.id_contractor=='2')[0]?this.daily['ob_production_contractor'].filter(e=>e.id_contractor=='2')[0]['actual']:0
                    var obHRS=this.daily['ob_production_contractor'].filter(e=>e.id_contractor=='1')[0]?this.daily['ob_production_contractor'].filter(e=>e.id_contractor=='1')[0]['actual']:0
                    var options = {
                        series: [{
                            name: 'OB',
                            data: [obCK, obGMT, obHRS],
                            color: '#8cb89f'
                        }, {
                            name: 'CG',
                            data: [cgCK, cgGMT, cgHRS],
                            color: '#1c315e'
                        }, ],
                        ...optionLine
                    };

                    var lineChartDaily1 = new ApexCharts(document.querySelector("#lineChartDaily"), options);
                    lineChartDaily1.render();
                    // MONTHLY
                    let ob2Val=parseInt(this.monthly['ob_production']['budget']) !=0 ? parseInt(this.monthly['ob_production']['actual']/this.monthly['ob_production']['budget'] * 100) :0;
                    var ob2 = new ApexCharts(document.querySelector("#obmonthly"), {
                        series: [ !isNaN(ob2Val)?ob2Val:0  ],
                        ...optionLingkaran
                    });
                    ob2.render();
                    let cg2Val=parseInt(this.monthly['cg_production']['budget']) !=0 ? parseInt(this.monthly['cg_production']['actual']/this.monthly['cg_production']['budget'] * 100) :0
                    var cg2 = new ApexCharts(document.querySelector("#cgmonthly"), {
                        series: [ !isNaN(cg2Val)?cg2Val:0  ],
                        ...optionLingkaran
                    });
                    cg2.render();
                    var actual_sr2=this.monthly['stripping_ytd']['actual_cg']!=0?this.monthly['stripping_ytd']['actual_ob']/this.monthly['stripping_ytd']['actual_cg']:0;
                    var budget_sr2=this.monthly['stripping_ytd']['budget_cg']!=0?this.monthly['stripping_ytd']['budget_ob']/this.monthly['stripping_ytd']['budget_cg']:0;
                    let stripping2Val=parseInt(budget_sr2)!=0?(actual_sr2/budget_sr2)*100:0;
                    var stripping2 = new ApexCharts(document.querySelector("#strippingmonthly"), {
                        series: [ !isNaN(stripping2Val)?stripping2Val.toFixed(2):0  ],
                        ...optionLingkaran
                    });
                    stripping2.render();
                    let barging2Val=this.monthly['barging_ytd']['target']??0 !=0 ? parseInt(this.monthly['barging_ytd']['total'])/parseInt(this.monthly['barging_ytd']['target']??0) * 100 :0;
                    var barging2 = new ApexCharts(document.querySelector("#bargingmonthly"), {
                        series: [ !isNaN(barging2Val)?barging2Val:0  ],
                        ...optionLingkaran
                    });
                    barging2.render();
                    // -=================================
                    var cgCK2= this.monthly['cg_production_contractor'].filter(e=>e.contractor=='CK')[0]?this.monthly['cg_production_contractor'].filter(e=>e.contractor=='CK')[0]['actual']:0
                    var cgGMT2=this.monthly['cg_production_contractor'].filter(e=>e.contractor=='GMT')[0]?this.monthly['cg_production_contractor'].filter(e=>e.contractor=='GMT')[0]['actual']:0
                    var cgHRS2=this.monthly['cg_production_contractor'].filter(e=>e.contractor=='HRS')[0]?this.monthly['cg_production_contractor'].filter(e=>e.contractor=='HRS')[0]['actual']:0
                    var obCK2= this.monthly['ob_production_contractor'].filter(e=>e.id_contractor=='3')[0]?this.monthly['ob_production_contractor'].filter(e=>e.id_contractor=='3')[0]['actual']:0
                    var obGMT2=this.monthly['ob_production_contractor'].filter(e=>e.id_contractor=='2')[0]?this.monthly['ob_production_contractor'].filter(e=>e.id_contractor=='2')[0]['actual']:0
                    var obHRS2=this.monthly['ob_production_contractor'].filter(e=>e.id_contractor=='1')[0]?this.monthly['ob_production_contractor'].filter(e=>e.id_contractor=='1')[0]['actual']:0
                    var options = {
                        series: [{
                            name: 'OB',
                            data: [obCK2, obGMT2, obHRS2],
                            color: '#8cb89f'
                        }, {
                            name: 'CG',
                            data: [cgCK2, cgGMT2, cgHRS2],
                            color: '#1c315e'
                        }, ],
                        ...optionLine
                    };

                    var lineChartmonthly1 = new ApexCharts(document.querySelector("#lineChartmonthly"), options);
                    lineChartmonthly1.render();
                    // YEARLY
                    let ob3Val=parseInt(this.yearly['ob_production']['budget']) !=0 ? parseInt(this.yearly['ob_production']['actual']/this.yearly['ob_production']['budget'] * 100) :0;
                    var ob3 = new ApexCharts(document.querySelector("#obyearly"), {
                        series: [ !isNaN(ob3Val)?ob3Val:0  ],
                        ...optionLingkaran
                    });
                    ob3.render();
                    let cg3Val=parseInt(this.yearly['cg_production']['budget']) !=0 ? parseInt(this.yearly['cg_production']['actual']/this.yearly['cg_production']['budget'] * 100) :0
                    var cg3 = new ApexCharts(document.querySelector("#cgyearly"), {
                        series: [ !isNaN(cg3Val)?cg3Val:0  ],
                        ...optionLingkaran
                    });
                    cg3.render();
                    var actual_sr3=this.yearly['stripping_ytd']['actual_cg']!=0?this.yearly['stripping_ytd']['actual_ob']/this.yearly['stripping_ytd']['actual_cg']:0;
                    var budget_sr3=this.yearly['stripping_ytd']['budget_cg']!=0?this.yearly['stripping_ytd']['budget_ob']/this.yearly['stripping_ytd']['budget_cg']:0;
                    let stripping3Val=parseInt(budget_sr3)!=0?(actual_sr3/budget_sr3)*100:0;
                    var stripping3 = new ApexCharts(document.querySelector("#strippingyearly"), {
                        series: [ !isNaN(stripping3Val)?stripping3Val.toFixed(2):0  ],
                        ...optionLingkaran
                    });
                    stripping3.render();
                    let barging3Val=this.yearly['barging_ytd']['target']??0 !=0 ? parseInt(this.yearly['barging_ytd']['total'])/parseInt(this.yearly['barging_ytd']['target']??0) * 100 :0;
                    var barging3 = new ApexCharts(document.querySelector("#bargingyearly"), {
                        series: [ !isNaN(barging3Val)?barging3Val:0  ],
                        ...optionLingkaran
                    });
                    barging3.render();
                    // -=================================
                    var cgCK3= this.yearly['cg_production_contractor'].filter(e=>e.contractor=='CK')[0]?this.yearly['cg_production_contractor'].filter(e=>e.contractor=='CK')[0]['actual']:0
                    var cgGMT3=this.yearly['cg_production_contractor'].filter(e=>e.contractor=='GMT')[0]?this.yearly['cg_production_contractor'].filter(e=>e.contractor=='GMT')[0]['actual']:0
                    var cgHRS3=this.yearly['cg_production_contractor'].filter(e=>e.contractor=='HRS')[0]?this.yearly['cg_production_contractor'].filter(e=>e.contractor=='HRS')[0]['actual']:0
                    var obCK3= this.yearly['ob_production_contractor'].filter(e=>e.id_contractor=='3')[0]?this.yearly['ob_production_contractor'].filter(e=>e.id_contractor=='3')[0]['actual']:0
                    var obGMT3=this.yearly['ob_production_contractor'].filter(e=>e.id_contractor=='2')[0]?this.yearly['ob_production_contractor'].filter(e=>e.id_contractor=='2')[0]['actual']:0
                    var obHRS3=this.yearly['ob_production_contractor'].filter(e=>e.id_contractor=='1')[0]?this.yearly['ob_production_contractor'].filter(e=>e.id_contractor=='1')[0]['actual']:0
                    var options = {
                        series: [{
                            name: 'OB',
                            data: [obCK3, obGMT3, obHRS3],
                            color: '#8cb89f'
                        }, {
                            name: 'CG',
                            data: [cgCK3, cgGMT3, cgHRS3],
                            color: '#1c315e'
                        }, ],
                        ...optionLine
                    };

                    var lineChartyearly1 = new ApexCharts(document.querySelector("#lineChartyearly"), options);
                    lineChartyearly1.render();
                },
                number_format(x) {
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            },
            mounted() {
                let that=this;
                this.getData();
                setInterval(() => {
                    location.reload();
                }, 3600000);

                function startTime() {
                    var today = new Date();
                    var h = today.getHours();
                    var m = today.getMinutes();
                    var s = today.getSeconds();
                    m = checkTime(m);
                    s = checkTime(s);
                    that.jam =
                    h + ":" + m + ":" + s;
                    var t = setTimeout(startTime, 500);
                }
                function checkTime(i) {
                    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
                    return i;
                }
                startTime();

            },
        }).mount('#monitor')
    </script>
</body>

</html>