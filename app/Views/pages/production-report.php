<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->



<main id="main" class="main">
    <div id="productionReportPage" class="d-none">

        <div class="pagetitle">
            <h1>Production - Report</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("production/report") ?>">Report</a></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Production Report Data</h5>
                            <!-- notification -->
                            <div class="p-3">
                                <p class="font-semibold">Type Data : </p>
                                <button type="button" style="border:1px solid black;" class="btn btn-sm mx-2" :class="typedata=='timesheet'?'btn-dark':'btn-dark-outline border-0 border-dark'" @click="typedata='timesheet';getData()">  
                                <i class="ri-edit-line"></i>
                                Timesheet</button>
                                <button type="button" style="border:1px solid black;" class="btn btn-sm mx-2" :class="typedata=='timesheet'?'btn-dark-outline  border-0 border-dark':'btn-dark'" @click="typedata='adjustment';getData()">
                                <i class="ri-edit-line"></i>
                                Adjustment</button>
                            </div>
                            <!-- b-table -->
                            <div >
                                <div class="row py-2" >
                                    <div class="col-sm-5">
                                        <p class="font-semibold">Date</p>
                                        <div v-if="typeTanggal=='selection'">
                                            <select class='form-control' v-model="pilihTanggal">
                                                <option value="today">Today</option>
                                                <option value="week">This Week</option>
                                                <option value="month">This Month</option>
                                                <option value="calendar">Other (Calendar)</option>
                                            </select>
                                        </div>
                                        <div v-else>
                                            <div class="sm-form text-xs">
                                                <input type="date" style="width:100px;" id="dari_tanggal" name="dari_tanggal" class=" text-xs form-control mr-2 p-2 rounded-lg shadow d-inline" placeholder="dari_tanggal" v-model="dari_tanggal" >
                                                S/D
                                                <input type="date" style="width:100px;" id="sampai_tanggal" name="sampai_tanggal" class=" text-xs form-control p-2 rounded-lg shadow d-inline ml-2" placeholder="sampai_tanggal" v-model="sampai_tanggal" >
                                                <button type="button"  class="btn btn-sm btn-dark ml-2 text-xs" @click="getData()">Cari</button>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-dark my-2" @click="typeTanggal='selection';pilihTanggal='today'">Kembali ke Selection</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <p class="font-semibold">View</p>
                                        <select class='form-control' v-model="perPage" @change="page=1">
                                            <option>5</option>
                                            <option>10</option>
                                            <option>50</option>
                                            <option>100</option>
                                            <option value="100000">Semua</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="sm-form mt-10">
                                            <input type="text" 
                                            @change="page=1"
                                            id="search" name="search" class="form-control p-2 text-xs rounded-lg " placeholder="search" v-model="search" >
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <br>
                                        
                                        <div class="text-center p-3" v-if="datanya.length>0">
                                            <a  :href="'<?= site_url("/production/report/download?nama_file=production report") ?>'+`&data=${JSON.stringify(td)}`">
                                                <button type="button"  class="btn btn-sm btn-dark  " ><i class="ri-download-line"></i></button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive" v-if="datanya.length>0">
                                    <!-- Table users -->
                                    <table class="table table-bordered datatables table-striped">
                                        <tr>
                                            <th scope="col">
                                                No &#8593;&#8595;
                                            </th>
                                            <th scope="col">
                                                Id &#8593;&#8595;
                                            </th>
                                            <th scope="col">
                                                Posting Date &#8593;&#8595;
                                            </th>
                                            <th scope="col">
                                                CG {{typedata=='timesheet'?'Total':''}} &#8593;&#8595;
                                            </th>
                                            <th scope="col">
                                                OB {{typedata=='timesheet'?'Total':''}} &#8593;&#8595;
                                            </th>
                                            <th scope="col">
                                                Status &#8593;&#8595;
                                            </th>
                                        </tr>
                                        <tr v-for="(item, index) in td" :key="index">
                                            <td>{{index+1}}</td>
                                            <td>{{item['id']}}</td>
                                            <td>{{formatR(item.prd_date)}}</td>
                                            <td>{{item.cg_total}}</td>
                                            <td>{{item.ob_total}}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-block" :class="item.status=='approved'?'btn-success':'btn-danger'">{{item.status}}</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{Math.round(td.reduce((e,n)=>{return e+parseFloat(n.cg_total)},0))}}</td>
                                            <td>{{Math.round(td.reduce((e,n)=>{return e+parseFloat(n.ob_total)},0))}}</td>
                                            <td></td>
                                        </tr>
                                    </table>
                                    <div class="text-right">
                                        <!-- BULLET  -->
                                        <button type="button" class="btn btn-sm  rounded-circle py-1 px-2 mr-2" 
                                        :class="page==index+1?'btn-dark':'btn-dark-outline'"
                                        v-for="(item, index) in totalPage" :key="index+'totalPage'" @click="page=index+1">{{index+1}}</button>
                                    </div>
                                    <!-- End Table with stripped rows -->
                                </div>
                                <div v-else>
                                    <p class="p-4 text-center">Data tidak ada!</p>
                                </div>  
                                <!-- CHART -->
                                <div v-if="chartOn">
                                    <hr class="my-2">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div id="linechart"></div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div id="columnchart"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

</main><!-- End #main -->

<script type="module">
    new Vue({
        el:"#productionReportPage",
        data(){
            return{
                chartOn:false,
                typedata:'timesheet',
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                pilihTanggal:'today',
                tanggal:'',
                typeTanggal:'selection',
                dari_tanggal:'',
                sampai_tanggal:'',
                datanya:[],
            }
        },
        watch:{
            pilihTanggal(){
                if(this.pilihTanggal=='today'){
                    this.dari_tanggal=this.format(new Date());
                    this.sampai_tanggal=this.format(new Date());
                }
                if(this.pilihTanggal=='week'){
                    const today = new Date();
                    this.dari_tanggal = this.format(new Date(today.setDate(today.getDate())));
                    this.sampai_tanggal = this.format(new Date(today.setDate(today.getDate() + 6)));
                }
                if(this.pilihTanggal=='month'){
                    var date = new Date();
                    this.dari_tanggal = this.format(new Date(date.getFullYear(), date.getMonth(), 1));
                    this.sampai_tanggal = this.format(new Date(date.getFullYear(), date.getMonth() + 1, 0));
                }
                if(this.pilihTanggal=='calendar'){
                    this.typeTanggal="range";
                }
                this.$forceUpdate();
                this.getData();
            }
        },  
        computed:{
            td(){
                let that=this;
                let data=this.datanya;
                let keys=Object.keys(this.datanya[0]);
                data=data.filter(e=>{
                    let txt='';
                    keys.forEach(k=>{
                        txt+=e[k];
                    })
                    if(txt.toLowerCase().indexOf(that.search.toLowerCase())!=-1){
                        return e
                    }
                })
                this.totalPage = Math.ceil(data.length/this.perPage);
                data=data.slice((this.page-1)*this.perPage,this.page*this.perPage);
                return data;
            }
        },  
        methods: {
            async getData(){
                let data;
                this.datanya=[]
                if(this.typedata=='timesheet'){
                    data = await axios.get("<?= site_url() ?>" + `/api/timesheet/get?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                }else{
                    data = await axios.get("<?= site_url() ?>" + `/api/timesheet-adjustment/get?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                }
                this.datanya=data.data;
                console.log('datanya',this.datanya)
                this.$forceUpdate();
                setTimeout(() => {
                    this.sortField();
                    this.loadChart();
                }, 1000);
            },
            sortField(){
                const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
                const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
                    v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
                    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
                // do the work...
                document.querySelectorAll('th').forEach(th => {
                    th.style.cursor="pointer";
                    th.addEventListener('click', (() => {
                    const table = th.closest('table');
                    Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
                        .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                        .forEach(tr => table.appendChild(tr) );
                }))});
            },
            format(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY-MM-DD"
                );
            },
            formatR(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "DD-MM-YYYY"
                );
            },
            formatHari(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "DD-MM"
                );
            },
            loadChart(){
                this.loadLineChart();
                this.loadColumChart();
            },
            loadLineChart(){
                var optionsLine = {
                chart: {
                    type: 'line'
                },
                curve: 'smooth',
                series: [
                    {
                        name: 'CG',
                        data: []
                    },
                    {
                        name: 'OB',
                        data: []
                    }
                ],
                xaxis: {
                    categories: []
                }
                }
                this.datanya.forEach(e=>{
                    optionsLine.series[0]['data'].push(parseFloat(e.cg_total));
                    optionsLine.series[1]['data'].push(parseFloat(e.ob_total));
                    optionsLine.xaxis.categories.push(this.formatHari(e.prd_date));
                })
                console.log(optionsLine)
                
                this.chartOn=false;
                setTimeout(() => {
                    this.chartOn=true;
                    setTimeout(() => {
                        var chart = new ApexCharts(document.querySelector("#linechart"), optionsLine);
                        chart.render();
                    }, 500);
                }, 500);
            },
            loadColumChart(){
                var optionsColumn = {
                    chart: {
                        type: 'bar'
                    },
                    series: [
                        {
                            name: "CG",
                            data: [{
                            x: 'CG',
                            y: 10
                            }]
                        },
                        {
                            name: "OB",
                            data: [{
                            x: 'OB',
                            y: 10
                            }]
                        },
                    ]
                }
                optionsColumn.series[0].data[0].y=this.datanya.reduce((e,n)=>{return e+parseFloat(n.cg_total)},0)
                optionsColumn.series[1].data[0].y=this.datanya.reduce((e,n)=>{return e+parseFloat(n.ob_total)},0)
                // this.datanya.forEach(e=>{
                //     optionsColumn.series[0]['data'].push(parseFloat(e.cg_total));
                //     optionsColumn.series[1]['data'].push(parseFloat(e.ob_total));
                //     optionsColumn.xaxis.categories.push(this.formatHari(e.prd_date));
                // })
                // console.log(optionsColumn)
                
                this.chartOn=false;
                setTimeout(() => {
                    this.chartOn=true;
                    setTimeout(() => {
                        var chart = new ApexCharts(document.querySelector("#columnchart"), optionsColumn);
                        chart.render();
                    }, 500);
                }, 500);
            }
        },
        mounted() {
            this.getData();
            console.log(window)
            this.dari_tanggal=this.format(new Date());
            this.sampai_tanggal=this.format(new Date());
            document.getElementById('productionReportPage').classList.remove('d-none');
        },
    })
</script>

<?= $this->endSection() ?>