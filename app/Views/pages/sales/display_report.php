<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->


<style>
    table td {
        position: relative
    }

    table td textarea {
        position: absolute;
        top: 0;
        left: 0;
        margin: 0;
        height: 100%;
        width: 100%;
        border: none;
        padding: 10px;
        box-sizing: border-box;
        text-align: start
    }

    .border-hover {
        border: 1px solid transparent
    }

    .border-hover:hover {
        border: 1px solid #d0d0d0
    }

    .modal1 {
        position: fixed;
        width: 100vw;
        height: 100vh;
        left: 0;
        top: 0;
        z-index: 100;
        background: #000;
        opacity: .5
    }

    .modal2 {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translateX(-50%) translateY(-50%);
        z-index: 105;
        min-width: 50vw
    }
</style>

<main id="main" class="main">
    <div id="salesDisplayReport" class="d-none">
        <div class="pagetitle">
            <h1>Sales - Display Reports</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Sales</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("/sales/display-report") ?>">Display Report</a></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <!-- MODAL -->
            <div v-if="modal" @click="modal=false" class="modal1"></div>
            <div v-if="modal" class="modal2">
                <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn">
                    <div class="sm-form">
                        <textarea type="text" v-model="target['vmodel'][target.key]" id="alamat" name="alamat" rows="5" placeholder="alamat..." class="form-control md-textarea">
                    </textarea>
                    </div>
                </div>
            </div>
            <!-- MODAL -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sales Dashboard Monitoring</h5>
                    <div class="row py-2">
                        <div class="col-sm-5">
                            <p>Tanggal</p>
                            <div class="row">
                                <div class="col-sm-5">
                                    <input type="date" id="dari_tanggal" name="dari_tanggal" class="form-control p-1 rounded-sm shadow-sm" placeholder="dari_tanggal" v-model="dari_tanggal" >
                                </div>
                                <div class="col-sm-2">
                                    S/D 
                                </div>
                                <div class="col-sm-5">
                                    <input type="date" id="sampai_tanggal" name="sampai_tanggal" class="form-control p-1 rounded-sm shadow-sm" placeholder="sampai_tanggal" v-model="sampai_tanggal" >
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-sm-3">
                            <p class="font-semibold">Months :</p>
                            <div class="sm-form ">
                                <select class='form-control' v-model="bulan" @change="getForm()">
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <p class="font-semibold">Years :</p>
                            <div class="sm-form ">
                                <select class='form-control' v-model="tahun" @change="page=1;getForm()">
                                    <option v-for="(item, index) in listTahun" :key="index+'listTahun'">{{item}}</option>
                                </select>
                            </div>
                        </div> -->
                        <div class="col-sm-3">
                            <br>
                            <br>
                            <button type="button" class="btn btn-sm btn-dark text-xs p-1 " @click="getData()"> Filter</button>
                        </div>
                    </div>
                    <div v-if="reports">
                        <div class="row mt-4">
                            <div class="col-sm-4">
                                <div class="ml-2">
                                    <div class='p-7 shadow-sm rounded-lg row'>
                                        <div class="col-sm-4">
                                            <img src="<?= base_url() ?>/assets/img/barge.PNG" alt="" style="height:80px;width:80px;">
                                        </div>
                                        <div class="col-sm-8 text-right">
                                            <p class="my-1 font-bold ">Total Shipment (Contract)</p>
                                            <p class="my-1 font-semibold text-green-600" v-if="dataPeriode['c_total_shipment']">{{convertToRupiah(dataPeriode['c_total_shipment']).replace('..','.')}} <span>MT</span></p>
                                            <p class="my-1 font-semibold text-green-600" v-show="dataPeriode['bl_qty']">{{dataPeriode['percent_shipment']}} <span>%</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="ml-2">
                                    <div class='p-7 shadow-sm rounded-lg row'>
                                        <div class="col-sm-4">
                                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR9Xfk55gLn4WJOgO7Ym9w9IFaeKLYfM8cMkQ&usqp=CAU" alt="" style="height:80px;width:80px;">
                                        </div>
                                        <div class="col-sm-8 text-right">
                                            <p class="my-1 font-bold ">Average Price/ MT (Contract)</p>
                                            <p class="my-1 font-semibold text-green-600" v-if="dataPeriode['a_price']">
                                                {{convertToRupiah(dataPeriode['a_price']).replace('..','.')}} <span>IDR</span></p>
                                            <p class="my-1 font-semibold text-green-600" v-if="dataPeriode['b_price']">
                                                {{convertToRupiah(dataPeriode['b_price']).replace('..','.')}} <span>IDR</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="ml-2">
                                    <div class='p-7 shadow-sm rounded-lg row'>
                                        <div class="col-sm-4">
                                            <img src="https://assets.webiconspng.com/uploads/2016/12/Bar-Chart-Simple-Icon.png" alt="" style="height:80px;width:80px;">
                                        </div>
                                        <div class="col-sm-8 text-right">
                                            <p class="my-1 font-bold ">Sales (Contract)</p>
                                            <p class="my-1 font-semibold text-green-600" v-if="dataPeriode['a_amount']">
                                                {{convertToRupiah(dataPeriode['a_amount']).replace('..','.')}} <span>IDR</span></p>
                                            <p class="my-1 font-semibold text-green-600" v-if="dataPeriode['b_amount']">
                                                {{convertToRupiah(dataPeriode['b_amount']).replace('..','.')}} <span>IDR</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->
                        <hr class="my-3">
                        <p class="mt-10 font-semibold text-lg text-blue-800">Local</p>
                        <div class="row">
                            <div class="col-sm-4">
                                <p class="font-bold text-blue-800 text-center">FOB Barge</p>
                                <!-- <p class="text-center text-lg font-semibold " 
                                style="position:absolute;margin:auto;left:40%;top:45%;">{{((chart.fobActualLocal/chart.fobRkapLocal) *100).toFixed(2)}} %</p> -->
                                <!-- <canvas v-if="reports" id="fobchart" width="600" height="450"></canvas> -->
                                <div v-if="reports" id="fobchart"></div>
                                <p class="my-1 text-xs font-semibold text-center mt-3" v-if="chart.fobRkapLocal">RKAP Qty : {{convertToRupiah(chart.fobRkapLocal).replace('..','.')}}</p>
                                <p class="my-1 text-xs font-semibold text-center" v-if="chart.fobActualLocal">Actual Qty : {{convertToRupiah(chart.fobActualLocal).replace('..','.')}}</p>
                            </div>
                            <div class="col-sm-4">
                                <p class="font-bold text-blue-800 text-center">CIF</p>
                                <!-- <p class="text-center text-lg font-semibold " 
                                style="position:absolute;margin:auto;left:40%;top:45%;">{{((chart.cifActualLocal/chart.cifRkapLocal) *100).toFixed(2)}} %</p>
                                <canvas v-if="reports" id="cifchart" width="600" height="450"></canvas> -->
                                <div v-if="reports" id="cifchart"></div>
                                <p class="my-1 text-xs font-semibold text-center mt-3" v-if="chart.cifRkapLocal">RKAP Qty : {{convertToRupiah(chart.cifRkapLocal).replace('..','.')}}</p>
                                <p class="my-1 text-xs font-semibold text-center" v-if="chart.cifActualLocal">Actual Qty : {{convertToRupiah(chart.cifActualLocal).replace('..','.')}}</p>
                            </div>
                            <div class="col-sm-4">
                                <p class="font-bold text-blue-800 text-center">Franco Pabrik</p>
                                <!-- <p class="text-center text-lg font-semibold " 
                                style="position:absolute;margin:auto;left:40%;top:45%;">{{((chart.frankoActualLocal/chart.frankoRkapLocal) *100).toFixed(2)}} %</p>
                                <canvas v-if="reports" id="frankochart" width="600" height="450"></canvas> -->
                                <div v-if="reports" id="frankochart"></div>
                                <p class="my-1 text-xs font-semibold text-center mt-3" v-if="chart.frankoRkapLocal">RKAP Qty : {{convertToRupiah(chart.frankoRkapLocal).replace('..','.')}}</p>
                                <p class="my-1 text-xs font-semibold text-center" v-if="chart.frankoActualLocal">Actual Qty : {{convertToRupiah(chart.frankoActualLocal).replace('..','.')}}</p>
                            </div>
                        </div>
                        <!--  -->
                        <hr class="my-3">
                        <p class="mt-5 font-semibold text-lg text-blue-800">Export</p>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="font-bold text-blue-800 text-center">FOB Barge</p>
                                <!-- <p class="text-center text-lg font-semibold " 
                                style="position:absolute;margin:auto;left:40%;top:45%;">{{((chart.fobActualExport/chart.fobRkapExport) *100).toFixed(2)}} %</p>
                                <canvas v-if="reports" id="fobchartex" width="600" height="450"></canvas> -->
                                <div v-if="reports" id="fobchartex"></div>
                                <p class="my-1 text-xs font-semibold text-center mt-3" v-if="chart.fobRkapExport">RKAP Qty : {{convertToRupiah(chart.fobRkapExport).replace('..','.')}}</p>
                                <p class="my-1 text-xs font-semibold text-center" v-if="chart.fobActualExport">Actual Qty : {{convertToRupiah(chart.fobActualExport).replace('..','.')}}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="font-bold text-blue-800 text-center">CIF</p>
                                <!-- <p class="text-center text-lg font-semibold " 
                                style="position:absolute;margin:auto;left:40%;top:45%;">{{((chart.cifActualExport/chart.cifRkapExport) *100).toFixed(2)}} %</p>
                                <canvas v-if="reports" id="cifchartex" width="600" height="450"></canvas> -->
                                <div v-if="reports" id="cifchartex"></div>
                                <p class="my-1 text-xs font-semibold text-center mt-3" v-if="chart.cifRkapExport">RKAP Qty : {{convertToRupiah(chart.cifRkapExport).replace('..','.')}}</p>
                                <p class="my-1 text-xs font-semibold text-center" v-if="chart.cifActualExport">Actual Qty : {{convertToRupiah(chart.cifActualExport).replace('..','.')}}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="font-bold text-blue-800 text-center">FAS</p>
                                <!-- <p class="text-center text-lg font-semibold " 
                                style="position:absolute;margin:auto;left:40%;top:45%;">{{((chart.cifActualExport/chart.cifRkapExport) *100).toFixed(2)}} %</p>
                                <canvas v-if="reports" id="cifchartex" width="600" height="450"></canvas> -->
                                <div v-if="reports" id="faschartex"></div>
                                <p class="my-1 text-xs font-semibold text-center mt-3" v-if="chart.fasRkapExport">RKAP Qty : {{convertToRupiah(chart.fasRkapExport).replace('..','.')}}</p>
                                <p class="my-1 text-xs font-semibold text-center" v-if="chart.fasActualExport">Actual Qty : {{convertToRupiah(chart.fasActualExport).replace('..','.')}}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="font-bold text-blue-800 text-center">MV</p>
                                <!-- <p class="text-center text-lg font-semibold " 
                                style="position:absolute;margin:auto;left:40%;top:45%;">{{((chart.frankoActualExport/chart.frankoRkapExport) *100).toFixed(2)}} %</p>
                                <canvas v-if="reports" id="frankochartex" width="600" height="450"></canvas> -->
                                <div v-if="reports" id="frankochartex"></div>
                                <p class="my-1 text-xs font-semibold text-center mt-3" v-if="chart.frankoRkapExport">RKAP Qty : {{convertToRupiah(chart.frankoRkapExport).replace('..','.')}}</p>
                                <p class="my-1 text-xs font-semibold text-center" v-if="chart.frankoActualExport">Actual Qty : {{convertToRupiah(chart.frankoActualExport).replace('..','.')}}</p>
                            </div>
                        </div>
                        <!-- =========== -->
                        <div class="row">
                            <!-- <div class="col-sm-6 table-responsive p-3">
                                <p class="font-bold text-blue-800">Shipping Terms</p>
                                <table class="table table-sm table-bordered text-xs mt-4">
                                    <tr class="bg-blue-900">
                                        <th class="text-white">Vessel</th>
                                        <th class="text-white">Loading Rate/Days</th>
                                        <th class="text-white">UoM</th>
                                    </tr>
                                    <tr>
                                        <td>Supramax</td>
                                        <td>550000</td>
                                        <td>MT</td>
                                    </tr>
                                    <tr>
                                        <td>Panamax</td>
                                        <td>7000</td>
                                        <td>MT</td>
                                    </tr>
                                </table>
                            </div> -->
                            <!-- <tr v-for="(item, index) in dataPeriode.shipment" :key="index+'shipment'">
                                <td>{{item.vessel}}</td>
                                <td>{{item.bl_qty}}</td>
                                <td>{{item.uom}}</td>
                            </tr> -->
                            <!--  -->
                            <!-- <div class="col-sm-6 table-responsive p-3">
                                <p class="font-bold text-blue-800">Top 5 Buyers</p>
                                <table class="table table-sm table-bordered text-xs mt-4">
                                    <tr class="bg-blue-900">
                                        <th class="text-white">Buyer</th>
                                        <th class="text-white">Quantity</th>
                                        <th class="text-white">Price/MT (Rp)</th>
                                    </tr>
                                    <tr>
                                        <td>PT. INDONESIA TSINGHAN STAINLESS</td>
                                        <td>60.000</td>
                                        <td>577.826</td>
                                    </tr>
                                </table>
                            </div> -->
                            <!-- <tr v-for="(item, index) in dataPeriode.contract" :key="index+'contract order'">
                                <td>{{item.customer_name}}</td>
                                <td>{{item.quantity}}</td>
                                <td>{{item.contact_price}} {{item.currency}}</td>
                            </tr> -->
                            <!--  -->
                            <!-- <div class="col-sm-6 table-responsive p-3">
                                <p class="font-bold text-blue-800">Summary (Actual)</p>
                                <table class="table table-sm table-bordered text-xs mt-4">
                                    <tr class="bg-blue-900">
                                        <th class="text-white">Sales</th>
                                        <th class="text-white">Price (RP/MT)</th>
                                        <th class="text-white">QTY(MT)</th>
                                    </tr>
                                    <tr>
                                        <td>FOBB</td>
                                        <td>716.000</td>
                                        <td>50.000</td>
                                    </tr>
                                    <tr>
                                        <td>CIF</td>
                                        <td>933.333</td>
                                        <td>55.000</td>
                                    </tr>
                                    <tr>
                                        <td>MV</td>
                                        <td>1.000.000</td>
                                        <td>45.000</td>
                                    </tr>
                                </table>
                            </div> -->
                            <!--  -->
                            <!-- <div class="col-sm-6 table-responsive p-3">
                                <p class="font-bold text-blue-800">Summary (RKAP)</p>
                                <table class="table table-sm table-bordered text-xs mt-4">
                                    <tr class="bg-blue-900">
                                        <th class="text-white">Sales</th>
                                        <th class="text-white">Price (RP/MT)</th>
                                        <th class="text-white">QTY(MT)</th>
                                    </tr>
                                    <tr>
                                        <td>FOBB</td>
                                        <td>61.000</td>
                                        <td>15.000</td>
                                    </tr>
                                    <tr>
                                        <td>CIF</td>
                                        <td>652.500</td>
                                        <td>55.000</td>
                                    </tr>
                                    <tr>
                                        <td>MV</td>
                                        <td>577.826</td>
                                        <td>60.000</td>
                                    </tr>
                                </table>
                            </div> -->
                            <!--  -->
                            <!-- <div class="col-sm-12 table-responsive p-3">
                                <p class="font-bold text-blue-800">Price (Contract vs Actual)</p>
                                <table class="table table-sm table-bordered text-xs mt-4">
                                    <tr class="bg-blue-900">
                                        <th class="text-white">Buyer</th>
                                        <th class="text-white">Contract Price (Rp/MT)</th>
                                        <th class="text-white">Actual Price (Rp/MT)</th>
                                    </tr>
                                    <tr>
                                        <td>TONASA</td>
                                        <td>1.000.000</td>
                                        <td>979.292</td>
                                    </tr>
                                    <tr>
                                        <td>SBI</td>
                                        <td>62.000</td>
                                        <td>615.931</td>
                                    </tr>
                                </table>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

</main><!-- End #main -->

<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    var chart1;
    var chart2;
    var chart3;
    var chart4;
    var chart5;
    var chart6;
    var chart7;
    new Vue({
        el: "#salesDisplayReport",
        data() {
            return {
                modal: false,
                search: '',
                listTahun: [],
                datanya: [],
                rkap: [],
                actual: [],
                keys: [],
                vdata: {},
                tahun: '',
                bulan: '',
                dari_tanggal: '',
                sampai_tanggal: '',
                chart: {},
                dataPeriode:{},
                formInsert: false,
                reports:false,
                option: {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'contentType': "application/json",
                    }
                }
            }
        },
        computed: {
            td() {
                let that = this;
                let data = this.datanya;
                return data;
            }
        },
        methods: {
            loadChart2(){
                let fobRkapLocal = this.rkap.filter(e => e.type == 'Local' && e.category.indexOf('FOB') != -1);
                fobRkapLocal = fobRkapLocal.length > 0 ? fobRkapLocal.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let fobActualLocal = this.actual.filter(e => e.type == 'Local' && e.category.indexOf('FOB') != -1)
                fobActualLocal = fobActualLocal.length > 0 ? fobActualLocal.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.fobRkapLocal = fobRkapLocal.toFixed(2);
                this.chart.fobActualLocal = fobActualLocal.toFixed(2);
                chart1 = new ApexCharts(document.querySelector("#fobchart"), {
                    series: [((fobActualLocal/fobRkapLocal)*100).toFixed(2)=='NaN'?0:((fobActualLocal/fobRkapLocal)*100).toFixed(2)],
                    chart: {
                        height: 350,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#2A8FF7'],
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
                chart1.render();
                // ==================================
                let cifRkapLocal = this.rkap.filter(e => e.type == 'Local' && e.category.indexOf('CIF') != -1);
                cifRkapLocal = cifRkapLocal.length > 0 ? cifRkapLocal.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let cifActualLocal = this.actual.filter(e => e.type == 'Local' && e.category.indexOf('CIF') != -1)
                cifActualLocal = cifActualLocal.length > 0 ? cifActualLocal.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.cifRkapLocal = cifRkapLocal.toFixed(2);
                this.chart.cifActualLocal = cifActualLocal.toFixed(2);
                chart2 = new ApexCharts(document.querySelector("#cifchart"), {
                    series: [((cifActualLocal/cifRkapLocal)*100).toFixed(2)=='NaN'?0:((cifActualLocal/cifRkapLocal)*100).toFixed(2)],
                    chart: {
                        height: 350,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#3E8E5B'],
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
                // ==================================
                let frankoRkapLocal = this.rkap.filter(e => e.type == 'Local' && e.category.indexOf('Franko') != -1);
                frankoRkapLocal = frankoRkapLocal.length > 0 ? frankoRkapLocal.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let frankoActualLocal = this.actual.filter(e => e.type == 'Local' && e.category.indexOf('Franko') != -1)
                frankoActualLocal = frankoActualLocal.length > 0 ? frankoActualLocal.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.frankoRkapLocal = frankoRkapLocal.toFixed(2);
                this.chart.frankoActualLocal = frankoActualLocal.toFixed(2);
                chart3 = new ApexCharts(document.querySelector("#frankochart"), {
                    series: [((frankoActualLocal/frankoRkapLocal)*100).toFixed(2)=='NaN'?0:((frankoActualLocal/frankoRkapLocal)*100).toFixed(2)],
                    chart: {
                        height: 350,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#F6AF42'],
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
                chart3.render();
                // ==========================================
                let fobRkapExport = this.rkap.filter(e => e.type == 'Export' && e.category.indexOf('FOB') != -1);
                fobRkapExport = fobRkapExport.length > 0 ? fobRkapExport.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let fobActualExport = this.actual.filter(e => e.type == 'Export' && e.category.indexOf('FOB') != -1)
                fobActualExport = fobActualExport.length > 0 ? fobActualExport.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.fobRkapExport = fobRkapExport.toFixed(2);
                this.chart.fobActualExport = fobActualExport.toFixed(2);
                chart4 = new ApexCharts(document.querySelector("#fobchartex"), {
                    series: [((fobActualExport/fobRkapExport)*100).toFixed(2)=='NaN'?0:((fobActualExport/fobRkapExport)*100).toFixed(2)],
                    chart: {
                        height: 250,
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
                chart4.render();
                // ========================================
                let cifRkapExport = this.rkap.filter(e => e.type == 'Export' && e.category.indexOf('CIF') != -1);
                cifRkapExport = cifRkapExport.length > 0 ? cifRkapExport.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let cifActualExport = this.actual.filter(e => e.type == 'Export' && e.category.indexOf('CIF') != -1)
                cifActualExport = cifActualExport.length > 0 ? cifActualExport.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.cifRkapExport = cifRkapExport.toFixed(2);
                this.chart.cifActualExport = cifActualExport.toFixed(2);
                console.log(((cifActualExport/cifRkapExport)*100).toFixed(2)=='NaN'?0:((cifActualExport/cifRkapExport)*100).toFixed(2)=='Infinity')
                chart5 = new ApexCharts(document.querySelector("#cifchartex"), {
                    series: [((cifActualExport/cifRkapExport)*100).toFixed(2)=='NaN'?0:((cifActualExport/cifRkapExport)*100).toFixed(2)!='Infinity'?((cifActualExport/cifRkapExport)*100).toFixed(2):0],
                    chart: {
                        height: 250,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#ED415D'],
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
                chart5.render();
                // ==========================================
                let frankoRkapExport = this.rkap.filter(e => e.type == 'Export' && e.category.indexOf('FOB MV') != -1);
                frankoRkapExport = frankoRkapExport.length > 0 ? frankoRkapExport.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let frankoActualExport = this.actual.filter(e => e.type == 'Export' && e.category.indexOf('FOB MV') != -1)
                frankoActualExport = frankoActualExport.length > 0 ? frankoActualExport.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.frankoRkapExport = frankoRkapExport;
                this.chart.frankoActualExport = frankoActualExport;
                chart6 = new ApexCharts(document.querySelector("#frankochartex"), {
                    series: [((frankoActualExport/frankoRkapExport)*100).toFixed(2)=='NaN'?0:((frankoActualExport/frankoRkapExport)*100).toFixed(2)],
                    chart: {
                        height: 250,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#775DCF'],
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
                chart6.render();
                // ========================================
                console.log('rkap',this.rkap)
                console.log('actual',this.actual)
                let fasRkapExport = this.rkap.filter(e => e.type == 'Export' && e.category.indexOf('FAS') != -1);
                fasRkapExport = fasRkapExport.length > 0 ? fasRkapExport.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let fasActualExport = this.actual.filter(e => e.type == 'Export' && e.category.indexOf('FAS') != -1)
                fasActualExport = fasActualExport.length > 0 ? fasActualExport.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.fasRkapExport = fasRkapExport.toFixed(2);
                this.chart.fasActualExport = fasActualExport.toFixed(2);
                chart7 = new ApexCharts(document.querySelector("#faschartex"), {
                    series: [((fasActualExport/fasRkapExport)*100).toFixed(2)=='NaN'?0:((fasActualExport/fasRkapExport)*100).toFixed(2)],
                    chart: {
                        height: 250,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#ED415D'],
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
                chart7.render();
                // ==========================================
            },
            loadChart() {
                let fobRkapLocal = this.rkap.filter(e => e.type == 'Local' && e.category.indexOf('FOB') != -1);
                fobRkapLocal = fobRkapLocal.length > 0 ? fobRkapLocal.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let fobActualLocal = this.actual.filter(e => e.type == 'Local' && e.category.indexOf('FOB') != -1)
                fobActualLocal = fobActualLocal.length > 0 ? fobActualLocal.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.fobRkapLocal = fobRkapLocal;
                this.chart.fobActualLocal = fobActualLocal;
                // LOCAL
                // =======================================
                chart1=new Chart(document.getElementById("fobchart").getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ["RKAP Qty", "Actual Qty"],
                        datasets: [{
                            label: "Population (millions)",
                            backgroundColor: ["blue", "lightblue"],
                            data: [fobRkapLocal, fobActualLocal]
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'FOB Barge'
                        }
                    }
                });
                let cifRkapLocal = this.rkap.filter(e => e.type == 'Local' && e.category.indexOf('CIF') != -1);
                cifRkapLocal = cifRkapLocal.length > 0 ? cifRkapLocal.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let cifActualLocal = this.actual.filter(e => e.type == 'Local' && e.category.indexOf('CIF') != -1)
                cifActualLocal = cifActualLocal.length > 0 ? cifActualLocal.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.cifRkapLocal = cifRkapLocal;
                this.chart.cifActualLocal = cifActualLocal;
                // =======================================
                chart2=new Chart(document.getElementById("cifchart").getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ["RKAP Qty", "Actual Qty"],
                        datasets: [{
                            label: "Population (millions)",
                            backgroundColor: ["yellow", "orange"],
                            data: [cifRkapLocal, cifActualLocal]
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'CIF'
                        }
                    }
                });
                let frankoRkapLocal = this.rkap.filter(e => e.type == 'Local' && e.category.indexOf('Franko') != -1);
                frankoRkapLocal = frankoRkapLocal.length > 0 ? frankoRkapLocal.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let frankoActualLocal = this.actual.filter(e => e.type == 'Local' && e.category.indexOf('Franko') != -1)
                frankoActualLocal = frankoActualLocal.length > 0 ? frankoActualLocal.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.frankoRkapLocal = frankoRkapLocal;
                this.chart.frankoActualLocal = frankoActualLocal;
                // =======================================
                chart3=new Chart(document.getElementById("frankochart").getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ["RKAP Qty", "Actual Qty"],
                        datasets: [{
                            label: "Population (millions)",
                            backgroundColor: ["#3e95cd", "#8e5ea2"],
                            data: [frankoRkapLocal, frankoActualLocal]
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Franco Pabrik'
                        }
                    }
                });

                // EXPORTS
                let fobRkapExport = this.rkap.filter(e => e.type == 'Export' && e.category.indexOf('FOB') != -1);
                fobRkapExport = fobRkapExport.length > 0 ? fobRkapExport.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let fobActualExport = this.actual.filter(e => e.type == 'Export' && e.category.indexOf('FOB') != -1)
                fobActualExport = fobActualExport.length > 0 ? fobActualExport.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.fobRkapExport = fobRkapExport;
                this.chart.fobActualExport = fobActualExport;
                // =======================================
                chart4=new Chart(document.getElementById("fobchartex").getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ["RKAP Qty", "Actual Qty"],
                        datasets: [{
                            label: "Population (millions)",
                            backgroundColor: ["blue", "lightblue"],
                            data: [fobRkapExport, fobActualExport]
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'FOB Barge'
                        }
                    }
                });
                let cifRkapExport = this.rkap.filter(e => e.type == 'Export' && e.category.indexOf('CIF') != -1);
                cifRkapExport = cifRkapExport.length > 0 ? cifRkapExport.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let cifActualExport = this.actual.filter(e => e.type == 'Export' && e.category.indexOf('CIF') != -1)
                cifActualExport = cifActualExport.length > 0 ? cifActualExport.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.cifRkapExport = cifRkapExport;
                this.chart.cifActualExport = cifActualExport;
                // =======================================
                chart5=new Chart(document.getElementById("cifchartex").getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ["RKAP Qty", "Actual Qty"],
                        datasets: [{
                            label: "Population (millions)",
                            backgroundColor: ["yellow", "orange"],
                            data: [cifRkapExport, cifActualExport]
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'CIF'
                        }
                    }
                });
                let frankoRkapExport = this.rkap.filter(e => e.type == 'Export' && e.category.indexOf('Franko') != -1);
                frankoRkapExport = frankoRkapExport.length > 0 ? frankoRkapExport.reduce((e,n)=>{return e+parseFloat(n.quantity)},0) : 0;
                let frankoActualExport = this.actual.filter(e => e.type == 'Export' && e.category.indexOf('Franko') != -1)
                frankoActualExport = frankoActualExport.length > 0 ? frankoActualExport.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0) : 0;
                this.chart.frankoRkapExport = frankoRkapExport;
                this.chart.frankoActualExport = frankoActualExport;
                // =======================================
                chart6=new Chart(document.getElementById("frankochartex").getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ["RKAP Qty", "Actual Qty"],
                        datasets: [{
                            label: "Population (millions)",
                            backgroundColor: ["#3e95cd", "#8e5ea2"],
                            data: [frankoRkapExport, frankoActualExport]
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Franko Pabrik'
                        }
                    }
                });
                this.$forceUpdate();

            },
            async calculateDataPeriode(){
                this.dataPeriode['bl_qty']=this.dataPeriode.shipment.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0)
                this.dataPeriode['quantity']=this.dataPeriode.target.reduce((e,n)=>{return e+parseFloat(n.quantity)},0)
                this.dataPeriode['percent_shipment']=((this.dataPeriode['bl_qty']/this.dataPeriode['quantity']) *100).toFixed(2)

                let res = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_shipment`);
                this.dataPeriode['c_total_shipment'] = res.data.reduce((e,n)=>{return e+parseFloat(n.bl_qty)},0).toFixed(2);

                if(this.dataPeriode.price['IDR'].length>0){
                    this.periode = `${this.tahun}-${this.bulan}-${this.hari(new Date())}`;
                    var date = new Date(this.periode);
                    this.dari_tanggal = this.format(new Date(date.getFullYear(), date.getMonth(), 1));
                    this.sampai_tanggal = this.format(new Date(date.getFullYear(), date.getMonth() + 1, 0));
                    let cur_exc = await axios.get("<?= site_url() ?>" + `/api/get/fi_cur_exc?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                    cur_exc=cur_exc.data[0];
                    this.dataPeriode['a_price'] = this.dataPeriode.price['IDR'].length>0? parseInt(this.dataPeriode.price['IDR'][0].final_price2):'0..00';
                    this.dataPeriode['b_price'] = this.dataPeriode.price['USD'].length>0? parseInt(this.dataPeriode.price['USD'][0].final_price2*cur_exc):'0.00';
                    this.dataPeriode['a_amount'] = this.dataPeriode.price['IDR'].length>0?parseInt(this.dataPeriode.price['IDR'][0].amount2):'0..00';
                    this.dataPeriode['b_amount'] = this.dataPeriode.price['USD'].length>0?parseInt(this.dataPeriode.price['USD'][0].amount2*cur_exc):'0.00';
                }
                this.$forceUpdate();
            },
            async getData() {
                let that = this;
                this.datanya = []
                let bulan =this.bulans(this.dari_tanggal);
                let tahun =this.tahuns(this.sampai_tanggal);
                let res = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_target?month=${parseInt(bulan)}&year=${tahun}`);
                this.rkap = res.data;
                this.dataPeriode.target=res.data
                let res2 = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_shipment?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.actual = res2.data;
                this.dataPeriode.shipment=res2.data
                let res3 = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_price?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.price = res3.data;
                this.dataPeriode.price=res3.data
                let res4 = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_contract_order?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.dataPeriode.contract=res4.data
                this.$forceUpdate();
                chart1.destroy()
                chart2.destroy()
                chart3.destroy()
                chart4.destroy()
                chart5.destroy()
                chart6.destroy()
                chart7.destroy()
                setTimeout(() => {
                    this.sortField();
                    this.calculateDataPeriode();
                    this.loadChart2();
                }, 1000);
            },
            async initData() {
                let that = this;
                sdb.loadingOn();
                this.reports=true;
                this.datanya = []
                let res = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_target`);
                this.rkap = res.data;
                this.dataPeriode.target=res.data
                let res2 = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_shipment`);
                this.actual = res2.data;
                this.dataPeriode.shipment=res2.data
                let res3 = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_price`);
                this.price = res3.data;
                this.dataPeriode.price=res3.data
                let res4 = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_contract_order`);
                this.dataPeriode.contract=res4.data
                this.$forceUpdate();
                setTimeout(() => {
                    this.sortField();
                    this.calculateDataPeriode();
                    this.loadChart2();
                    sdb.loadingOff();
                }, 1000);
            },
            async getForm() {
                let that = this;
                if (this.bulan && this.tahun) {
                    this.periode = `${this.tahun}-${this.bulan}-${this.hari(new Date())}`;
                    var date = new Date(this.periode);
                    // this.dari_tanggal = this.format(new Date(date.getFullYear(), date.getMonth(), 1));
                    // this.sampai_tanggal = this.format(new Date(date.getFullYear(), date.getMonth() + 1, 0));
                    this.$forceUpdate();
                }
            },
            sortField() {
                const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
                const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
                    v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
                )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
                // do the work...
                document.querySelectorAll('th').forEach(th => {
                    th.style.cursor = "pointer";
                    th.addEventListener('click', (() => {
                        const table = th.closest('table');
                        Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
                            .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                            .forEach(tr => table.appendChild(tr));
                    }))
                });
            },
            format(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY-MM-DD"
                );
            },
            formatIndo(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "DD MMMM YYYY"
                );
            },
            tahuns(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY"
                );
            },
            bulans(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "MM"
                );
            },
            hari(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "DD"
                );
            },
            convertToRupiah(angka){
                return new Intl.NumberFormat('id-ID').format(angka);
            }
        },
        mounted() {
            this.bulan = this.bulans(new Date());
            this.tahun = parseInt(this.tahuns(new Date()));
            var min = this.tahun - 9
            var years = []
            for (var i = this.tahun; i >= min; i--) {
                years.push(i)
            }
            this.listTahun = years
            this.getForm();
            this.initData();
            document.getElementById('salesDisplayReport').classList.remove('d-none');
        },
    })
</script>

<?= $this->endSection() ?>