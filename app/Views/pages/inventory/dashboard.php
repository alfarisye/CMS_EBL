<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->
<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:10000;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:10005;min-width:50vw}
</style>

<main id="main" class="main">
    <div id="inventoryDashboard" class="d-none">
        <div class="pagetitle">
            <h1>Inventory - Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Inventory</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("/inventory/dashboard") ?>">Dashboard</a></li>
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
                    <h5 class="card-title">Dashboard </h5>
                    <div class="row py-2">
                        <div class="col-sm-12">
                            <p class="text-sm font-semibold">Periode</p>
                        </div>
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
                            <button type="button" class="btn btn-sm btn-dark text-xs p-2" @click="getData()"> Filter</button>
                        </div>
                    </div>
                    <div v-if="reports">
                        <div class="row mt-4">
                            <div class="col-sm-8">
                                <div class="ml-2">
                                    <div class='p-2 shadow-sm rounded-lg row'>
                                        <br>
                                        <div id="chart1"></div>
                                        <br>
                                        <br>
                                        <br>
                                        <div id="chart2"></div>
                                        <br>
                                        <br>
                                        <br>
                                        <p class="text-lg font-semibold">Last Update Explosive Product</p>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped table-bordered">
                                                <tr style="background:darkblue;">
                                                    <th class="text-white text-xs">Explosive Product</th>
                                                    <th class="text-white text-xs">Expired</th>
                                                </tr>
                                                <tr v-for="(item, index) in res.explosive" :key="index+'res.explosive'">
                                                    <td class="text-xs">{{item.product_explsvmaterial}}</td>
                                                    <td class="text-xs">{{formatIndo(item.expired)}}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <br>
                                        <br>
                                        <br>
                                        <p class="text-lg font-semibold">Last Update Quality Report</p>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped table-bordered">
                                                <tr style="background:lightblue;">
                                                    <th class="text-xs">No</th>
                                                    <th class="text-xs">ID</th>
                                                    <th class="text-xs">Project Location</th>
                                                    <th class="text-xs">Sample Type</th>
                                                    <th class="text-xs">Lab Sample ID</th>
                                                    <th class="text-xs">Costumer ID</th>
                                                    <th class="text-xs">Tanggal Mulai</th>
                                                    <th class="text-xs">Tanggal Akhir</th>
                                                </tr>
                                                <tr v-for="(item, index) in res.quality_report" :key="index+'quality_report'">
                                                    <td class="text-xs">{{index+1}}</td>
                                                    <td class="text-xs">{{item.id}}</td>
                                                    <td class="text-xs">{{item.Project_location}}</td>
                                                    <td class="text-xs">{{item.Sample_type}}</td>
                                                    <td class="text-xs">{{item.Lab_sample_id}}</td>
                                                    <td class="text-xs">{{item.Customer_sample_id}}</td>
                                                    <td class="text-xs">{{formatIndo(item.tanggal_mulai)}}</td>
                                                    <td class="text-xs">{{formatIndo(item.tanggal_akhir)}}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="ml-2">
                                    <div class="sm-form ">
                                        <input type="date" id="todayDate" name="todayDate" @change="getTodayValue(true)" class="form-control p-1 rounded-sm" placeholder="id" v-model="todayDate" >
                                    </div>
                                    <div class='p-2 shadow-sm rounded-lg row'>
                                        <div class="col-sm-12">
                                            <br>
                                            <p class="font-bold text-sm mt-2">Raw Coal Stock QTY.</p>
                                        </div>
                                        <div class="col-sm-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);"><path d="M21.993 7.95a.96.96 0 0 0-.029-.214c-.007-.025-.021-.049-.03-.074-.021-.057-.04-.113-.07-.165-.016-.027-.038-.049-.057-.075-.032-.045-.063-.091-.102-.13-.023-.022-.053-.04-.078-.061-.039-.032-.075-.067-.12-.094-.004-.003-.009-.003-.014-.006l-.008-.006-8.979-4.99a1.002 1.002 0 0 0-.97-.001l-9.021 4.99c-.003.003-.006.007-.011.01l-.01.004c-.035.02-.061.049-.094.073-.036.027-.074.051-.106.082-.03.031-.053.067-.079.102-.027.035-.057.066-.079.104-.026.043-.04.092-.059.139-.014.033-.032.064-.041.1a.975.975 0 0 0-.029.21c-.001.017-.007.032-.007.05V16c0 .363.197.698.515.874l8.978 4.987.001.001.002.001.02.011c.043.024.09.037.135.054.032.013.063.03.097.039a1.013 1.013 0 0 0 .506 0c.033-.009.064-.026.097-.039.045-.017.092-.029.135-.054l.02-.011.002-.001.001-.001 8.978-4.987c.316-.176.513-.511.513-.874V7.998c0-.017-.006-.031-.007-.048zm-10.021 3.922L5.058 8.005 7.82 6.477l6.834 3.905-2.682 1.49zm.048-7.719L18.941 8l-2.244 1.247-6.83-3.903 2.153-1.191zM13 19.301l.002-5.679L16 11.944V15l2-1v-3.175l2-1.119v5.705l-7 3.89z"></path></svg>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="font-semibold text-lg">{{res.todayReceive[0]?convertToRupiah(res.todayReceive[0].total):0}} MT</span>
                                            <!-- <div v-if="periodenya=='yearly'">
                                                <span class="font-semibold text-lg">{{convertToRupiah(res.receive[0].totalAll)}} MT</span>
                                            </div>
                                            <div v-else>
                                                <span class="font-semibold text-lg">{{convertToRupiah(res.receive.reduce((e,n)=>{return e+parseInt(n.total)},0))}} MT</span>
                                            </div> -->
                                        </div>
                                        <!--  -->
                                        <div class="col-sm-12">
                                            <br>
                                            <p class="font-bold text-sm mt-2">Crushed Coal - Crushed Stock QTY.</p>
                                        </div>
                                        <div class="col-sm-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);"><path d="M21.993 7.95a.96.96 0 0 0-.029-.214c-.007-.025-.021-.049-.03-.074-.021-.057-.04-.113-.07-.165-.016-.027-.038-.049-.057-.075-.032-.045-.063-.091-.102-.13-.023-.022-.053-.04-.078-.061-.039-.032-.075-.067-.12-.094-.004-.003-.009-.003-.014-.006l-.008-.006-8.979-4.99a1.002 1.002 0 0 0-.97-.001l-9.021 4.99c-.003.003-.006.007-.011.01l-.01.004c-.035.02-.061.049-.094.073-.036.027-.074.051-.106.082-.03.031-.053.067-.079.102-.027.035-.057.066-.079.104-.026.043-.04.092-.059.139-.014.033-.032.064-.041.1a.975.975 0 0 0-.029.21c-.001.017-.007.032-.007.05V16c0 .363.197.698.515.874l8.978 4.987.001.001.002.001.02.011c.043.024.09.037.135.054.032.013.063.03.097.039a1.013 1.013 0 0 0 .506 0c.033-.009.064-.026.097-.039.045-.017.092-.029.135-.054l.02-.011.002-.001.001-.001 8.978-4.987c.316-.176.513-.511.513-.874V7.998c0-.017-.006-.031-.007-.048zm-10.021 3.922L5.058 8.005 7.82 6.477l6.834 3.905-2.682 1.49zm.048-7.719L18.941 8l-2.244 1.247-6.83-3.903 2.153-1.191zM13 19.301l.002-5.679L16 11.944V15l2-1v-3.175l2-1.119v5.705l-7 3.89z"></path></svg>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="font-semibold text-lg">{{res.todayCrushcoal[0]?convertToRupiah(res.todayCrushcoal[0].total):0}} MT</span>
                                            <!-- <div v-if="periodenya=='yearly'">
                                                <span class="font-semibold text-lg">{{convertToRupiah(res.crushcoal[0].totalAll)}} MT</span>
                                            </div>
                                            <div v-else>
                                                <span class="font-semibold text-lg">{{convertToRupiah(res.crushcoal.reduce((e,n)=>{return e+parseInt(n.total)},0))}} MT</span>
                                            </div> -->
                                        </div>
                                        
                                        <!--  -->
                                         <!--  -->
                                        <div class="col-sm-12">
                                            <br>
                                            <p class="font-bold text-sm mt-2">Crushed Coal - Port Stock QTY.</p>
                                        </div>
                                        <div class="col-sm-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);"><path d="M21.993 7.95a.96.96 0 0 0-.029-.214c-.007-.025-.021-.049-.03-.074-.021-.057-.04-.113-.07-.165-.016-.027-.038-.049-.057-.075-.032-.045-.063-.091-.102-.13-.023-.022-.053-.04-.078-.061-.039-.032-.075-.067-.12-.094-.004-.003-.009-.003-.014-.006l-.008-.006-8.979-4.99a1.002 1.002 0 0 0-.97-.001l-9.021 4.99c-.003.003-.006.007-.011.01l-.01.004c-.035.02-.061.049-.094.073-.036.027-.074.051-.106.082-.03.031-.053.067-.079.102-.027.035-.057.066-.079.104-.026.043-.04.092-.059.139-.014.033-.032.064-.041.1a.975.975 0 0 0-.029.21c-.001.017-.007.032-.007.05V16c0 .363.197.698.515.874l8.978 4.987.001.001.002.001.02.011c.043.024.09.037.135.054.032.013.063.03.097.039a1.013 1.013 0 0 0 .506 0c.033-.009.064-.026.097-.039.045-.017.092-.029.135-.054l.02-.011.002-.001.001-.001 8.978-4.987c.316-.176.513-.511.513-.874V7.998c0-.017-.006-.031-.007-.048zm-10.021 3.922L5.058 8.005 7.82 6.477l6.834 3.905-2.682 1.49zm.048-7.719L18.941 8l-2.244 1.247-6.83-3.903 2.153-1.191zM13 19.301l.002-5.679L16 11.944V15l2-1v-3.175l2-1.119v5.705l-7 3.89z"></path></svg>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="font-semibold text-lg">{{res.todayPort[0]?convertToRupiah(res.todayPort[0].total):0}} MT</span>
                                            <!-- <div v-if="periodenya=='yearly'">
                                                <span class="font-semibold text-lg">{{convertToRupiah(res.port[0].totalAll)}} MT</span>
                                            </div>
                                            <div v-else>
                                                <span class="font-semibold text-lg">{{convertToRupiah(res.port.reduce((e,n)=>{return e+parseInt(n.total)},0))}} MT</span>
                                            </div> -->
                                        </div>
                                        <div class="col-sm-12">
                                            <br>
                                            <p class="font-bold text-sm mt-2">RC Stock QTY.</p>
                                        </div>
                                        <div class="col-sm-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);"><path d="M21.993 7.95a.96.96 0 0 0-.029-.214c-.007-.025-.021-.049-.03-.074-.021-.057-.04-.113-.07-.165-.016-.027-.038-.049-.057-.075-.032-.045-.063-.091-.102-.13-.023-.022-.053-.04-.078-.061-.039-.032-.075-.067-.12-.094-.004-.003-.009-.003-.014-.006l-.008-.006-8.979-4.99a1.002 1.002 0 0 0-.97-.001l-9.021 4.99c-.003.003-.006.007-.011.01l-.01.004c-.035.02-.061.049-.094.073-.036.027-.074.051-.106.082-.03.031-.053.067-.079.102-.027.035-.057.066-.079.104-.026.043-.04.092-.059.139-.014.033-.032.064-.041.1a.975.975 0 0 0-.029.21c-.001.017-.007.032-.007.05V16c0 .363.197.698.515.874l8.978 4.987.001.001.002.001.02.011c.043.024.09.037.135.054.032.013.063.03.097.039a1.013 1.013 0 0 0 .506 0c.033-.009.064-.026.097-.039.045-.017.092-.029.135-.054l.02-.011.002-.001.001-.001 8.978-4.987c.316-.176.513-.511.513-.874V7.998c0-.017-.006-.031-.007-.048zm-10.021 3.922L5.058 8.005 7.82 6.477l6.834 3.905-2.682 1.49zm.048-7.719L18.941 8l-2.244 1.247-6.83-3.903 2.153-1.191zM13 19.301l.002-5.679L16 11.944V15l2-1v-3.175l2-1.119v5.705l-7 3.89z"></path></svg>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="font-semibold text-lg">{{res.todayRC[0]?convertToRupiah(res.todayRC[0].rc_qty):0}} MT</span>
                                            <!-- <div v-if="periodenya=='yearly'">
                                                <span class="font-semibold text-lg">{{convertToRupiah(res.port[0].totalAll)}} MT</span>
                                            </div>
                                            <div v-else>
                                                <span class="font-semibold text-lg">{{convertToRupiah(res.port.reduce((e,n)=>{return e+parseInt(n.total)},0))}} MT</span>
                                            </div> -->
                                        </div>
                                        <!-- <div class="col-sm-12">
                                            <br>
                                              <p class="font-bold text-sm mt-2">Total Crushed Coal - Port Stock QTY. (YTD)</p>
                                        </div>
                                        <div class="col-sm-4">
                                            <i class="ri-user-3-fill"></i> 
                                        </div>
                                        <div class="col-sm-8">
                                            <span>2.000.000.00</span>
                                        </div> -->
                                        <div class="col-sm-12">
                                            <br>
                                            <p class="font-bold text-sm mt-2">Storage By Location </p>
                                            <div id="chart3"></div>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->
                        <hr class="my-3">
                        <!-- =========== -->
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
    new Vue({
        el: "#inventoryDashboard",
        data() {
            return {
                modal: false,
                search: '',
                listTahun: [],
                datanya: [],
                keys: [],
                vdata: {},
                res:{},
                tahun: '',
                bulan: '',
                dari_tanggal: '',
                sampai_tanggal: '',
                chart: {},
                dataPeriode:{},
                todayDate:'',
                periodenya:'yearly',
                formInsert: false,
                reports:false,
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
                let that=this;
                if(this.periodenya=='yearly'){
                    let tgl=[];
                    let rawCoal1=[];
                    let crushCoal1=[];
                    for(let i=1;i<=12;i++){
                        tgl.push(i)
                        let raw=this.res.receive.filter(e=>e.bulan==i);
                        if(raw.length>0){
                            rawCoal1.push(raw[0].total);
                        }else{
                            rawCoal1.push(0)
                        }
                        let crush=this.res.crushcoal.filter(e=>e.bulan==i);
                        if(crush.length>0){
                            crushCoal1.push(crush[0].total);
                        }else{
                            crushCoal1.push(0)
                        }
                    }
                    var options = {
                    series: [
                    {
                        name: "Raw Coal",
                        data: rawCoal1
                    },
                    {
                        name: "Crushed Coal",
                        data: crushCoal1
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
                    },
                    colors: ['#77B6EA', '#545454'],
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    title: {
                        text: 'Stock By Category',
                        align: 'middle'
                    },
                    grid: {
                    row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul','Aug','Sep','Oct','Nov','Des'],
                        title: {
                            text: 'Bulan'
                        }
                    },
                    yaxis: {
                        labels: {
                                formatter: function(val) {
                                    return that.convertToRupiah(val).replace('..','.')
                                }
                            }
                        }
                    };

                    chart1 = new ApexCharts(document.querySelector("#chart1"), options);
                    chart1.render();
                    this.$forceUpdate();
                }else{
                    let tgl=[];
                    let rawCoal=[];
                    let crushCoal=[];
                    for(let i=1;i<=parseInt(this.sampai_tanggal.split('-')[2]);i++){
                        tgl.push(i)
                        let raw=this.res.receive.filter(e=>e.day==i);
                        if(raw.length>0){
                            rawCoal.push(raw[0].total);
                        }else{
                            rawCoal.push(0)
                        }
                        let crush=this.res.crushcoal.filter(e=>e.day==i);
                        if(crush.length>0){
                            crushCoal.push(crush[0].total);
                        }else{
                            crushCoal.push(0)
                        }
                    }
                    console.log('crush',this.res.crushcoal)
                    var options = {
                    series: [
                    {
                        name: "Raw Coal",
                        data: rawCoal
                    },
                    {
                        name: "Crushed Coal",
                        data: crushCoal
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
                    },
                    colors: ['#77B6EA', '#545454'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    title: {
                        text: 'Stock By Category',
                        align: 'middle'
                    },
                    grid: {
                    row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: tgl,
                        title: {
                            text: 'Tanggal'
                        }
                    },
                    };

                    chart1 = new ApexCharts(document.querySelector("#chart1"), options);
                    chart1.render();
                    this.$forceUpdate();
                }

                // ==================== CHART 2
                let series=[];
                this.res.explosive2.forEach(e=>{
                    series.push({
                        name:e.product_explsvmaterial,
                        data: [e.qty]
                    })
                })
                var options = {
                series: series,
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
                xaxis: {
                    categories: series.map(e=>e.name),
                },
                fill: {
                 opacity: 1
                },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                            return "" + val + ""
                            }
                        }
                    }
                };

                chart2 = new ApexCharts(document.querySelector("#chart2"), options);
                chart2.render();
                

            },
            renderChart3(){
                // ========== CHART 3
                let raw1,crush,port;
                // if(this.periodenya=='yearly'){
                    raw1=this.res.todayReceive.reduce((e,n)=>{return e+parseInt(n.total)},0);
                    crush=this.res.todayCrushcoal.reduce((e,n)=>{return e+parseInt(n.total)},0);
                    port=this.res.todayPort.reduce((e,n)=>{return e+parseInt(n.total)},0);
                // }else{
                //     raw1=parseInt(this.res.receive[24].total);
                //     crush=parseInt(this.res.crushcoal[24].total);
                //     port=parseInt(this.res.port[24].total);
                // }
                var options = {
                        series: [raw1,crush,port],
                        chart: {
                        type: 'donut',
                    },
                    labels: ['Raw', 'Crushed','Port'],
                // var options = {
                //         series: [44, 55, 41],
                //         chart: {
                //         type: 'donut',
                //     },
                    responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                        width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                    }]
                };

                chart3 = new ApexCharts(document.querySelector("#chart3"), options);
                chart3.render();
            },
            async calculateDataPeriode(){
               
                this.$forceUpdate();
            },
            async getData() {
                let that = this;
                this.datanya = []
                this.periodenya='monthly'
                let bulan =this.bulans(this.dari_tanggal);
                let tahun =this.tahuns(this.sampai_tanggal);
                let receive = await axios.get("<?= site_url() ?>" + `/api/get/inquery-receive?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.receive=receive.data;
                let port = await axios.get("<?= site_url() ?>" + `/api/get/inquery-port?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.port=port.data;
                let crushcoal = await axios.get("<?= site_url() ?>" + `/api/get/t-crushcoal?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.crushcoal=crushcoal.data;
                let quality_report = await axios.get("<?= site_url() ?>" + `/api/quality-report?sort=desc`);
                this.res.quality_report=quality_report.data;
                let explosive = await axios.get("<?= site_url() ?>" + `/api/get/stock-explosive-material?order=created_at&sort=desc&limit=2`);
                this.res.explosive=explosive.data;
                let explosive2 = await axios.get("<?= site_url() ?>" + `/api/get/stock-explosive-material?order=qty&sort=desc&limit=2`);
                this.res.explosive2=explosive2.data;
                this.$forceUpdate();
                chart1.destroy()
                chart2.destroy()
                setTimeout(() => {
                    this.sortField();
                    this.calculateDataPeriode();
                    this.loadChart2();
                }, 1000);
            },
           async getTodayValue(update=false){
                if(update){
                    chart3.destroy()
                }
                let todayReceive = await axios.get("<?= site_url() ?>" + `/api/get/inquery-receive?dari_tanggal=${this.format(this.todayDate)}&sampai_tanggal=${this.format(this.todayDate)}`);
                this.res.todayReceive=todayReceive.data;
                let todayPort = await axios.get("<?= site_url() ?>" + `/api/get/inquery-port?dari_tanggal=${this.format(this.todayDate)}&sampai_tanggal=${this.format(this.todayDate)}`);
                this.res.todayPort=todayPort.data;
                let todayCrushcoal = await axios.get("<?= site_url() ?>" + `/api/get/t-crushcoal?dari_tanggal=${this.format(this.todayDate)}&sampai_tanggal=${this.format(this.todayDate)}`);
                this.res.todayCrushcoal=todayCrushcoal.data;
                let todayRC = await axios.get("<?= site_url() ?>" + `/api/get/sales-rc?dari_tanggal=${this.format(this.todayDate)}&sampai_tanggal=${this.format(this.todayDate)}`);
                this.res.todayRC=todayRC.data;
                this.$forceUpdate();
                setTimeout(() => {
                    this.renderChart3();
                }, 1000);
            },
            async initData() {
                let that = this;
                this.reports=true;
                this.datanya = []
                let receive = await axios.get("<?= site_url() ?>" + `/api/get/inquery-receive`);
                this.res.receive=receive.data;
                let port = await axios.get("<?= site_url() ?>" + `/api/get/inquery-port`);
                this.res.port=port.data;
                let crushcoal = await axios.get("<?= site_url() ?>" + `/api/get/t-crushcoal`);
                this.res.crushcoal=crushcoal.data;
                let quality_report = await axios.get("<?= site_url() ?>" + `/api/quality-report?sort=desc`);
                this.res.quality_report=quality_report.data;
                let explosive = await axios.get("<?= site_url() ?>" + `/api/get/stock-explosive-material?order=created_at&sort=desc&limit=2`);
                this.res.explosive=explosive.data;
                let explosive2 = await axios.get("<?= site_url() ?>" + `/api/get/stock-explosive-material?order=qty&sort=desc&limit=2`);
                this.res.explosive2=explosive2.data;
                this.$forceUpdate();
                setTimeout(() => {
                    this.sortField();
                    this.calculateDataPeriode();
                    this.loadChart2();
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
        async mounted() {
            this.bulan = this.bulans(new Date());
            this.tahun = parseInt(this.tahuns(new Date()));
            let receive = await axios.get("<?= site_url() ?>" + `/api/get/t-crushcoal-latest`);
            this.todayDate=this.format(receive.data[0].posting_date)
            this.getTodayValue();
            var min = this.tahun - 9
            var years = []
            for (var i = this.tahun; i >= min; i--) {
                years.push(i)
            }
            this.listTahun = years
            this.getForm();
            this.initData();
            document.getElementById('inventoryDashboard').classList.remove('d-none');
        },
    })
</script>

<?= $this->endSection() ?>