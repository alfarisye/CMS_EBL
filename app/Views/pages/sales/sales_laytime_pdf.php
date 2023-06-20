<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/jstat@1.9.2/dist/jstat.min.js"></script> 
<script src="https://cdn.jsdelivr.net/gh/formulajs/formulajs@2.9.3/dist/formula.min.js"></script>
<style>
    @media print {
        .no-print,
        .no-print * {
            display: none !important;
        }
        .print * {
            display:initial !important;
        }
    }
</style>
<main id="main" class="main">
    <div id="sales-laytime" class="d-none">
        <div class="row justify-content-center">
          <div class="col-md-11">
            <button type="button" class="btn btn-sm btn-dark no-print" @click="printThis()">Klik to Print</button>
              <p class="text-2xl font-semibold">Laytime Calculation</p>
              <div class="row" v-for="(item, index) in datanya" :key="index+'datanya'">
                <div class="col-sm-6 table-responsive">
                    <table class="table table-sm"  style="width:100%;">
                        <tr class="m-0">
                            <td class="text-xs">Agreed Laycan</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.agreed_laycan}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Vessel Name</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.vessel_name}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Loading Port</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.loading_port}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Vessel Arrival Time</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.vessel_arrived_date?formatTgl(item.vessel_arrived_date,'DD-MM-YYYY'):item.vessel_arrived_date}}  {{item.vessel_arrived_time}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">NOR Tendered</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.nor_tendered_date?formatTgl(item.nor_tendered_date,'DD-MM-YYYY'):item.nor_tendered_date}}  {{item.nor_tendered_time}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">NOR Retendered</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.nor_retendered_date?formatTgl(item.nor_retendered_date,'DD-MM-YYYY'):item.nor_retendered_date}}  {{item.nor_retendered_time}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Remarks</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.remarks}}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-6">
                     <table class="table table-sm"  style="width:100%;">
                        <tr class="m-0">
                            <td class="text-xs">Loading Commence</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.loading_commence_date}} {{item.loading_commence_time}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Loading Completed</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.loading_completed_date}} {{item.loading_completed_time}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Cargo Quantity</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.cargo_qty}} {{item.cargo_uom}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Loading Rate</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">{{item.loading_rate_qty}}  {{item.loading_rate_oum}}/Days</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Demurrage / Dispatch</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">Rp.{{item.demmurage}} / Rp.{{item.dispatch}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Laytime Allowed</td>
                            <td class="px-2">:</td>
                            <td class="text-xs">Hours : {{item.laytime_allow_hour}}  Days : {{item.laytime_allow_days}}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-xs">Status</td>
                            <td class="px-2">:</td>
                            <td class="text-xs font-bold" :class="item.status=='Draft'?'text-orange-500':'text-green-500'">{{item.status}}</td>
                        </tr>
                    </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table class="table table-sm table-bordered">
                        <tr>
                           <th class="text-xs" rowspan="2">Weather</th> 
                           <th class="text-xs" rowspan="2">Date</th> 
                           <th class="text-xs" rowspan="2">Day</th> 
                           <th class="text-xs" colspan="2">TIME RECORD</th> 
                           <th class="text-xs" rowspan="2">Remarks</th> 
                           <th class="text-xs" rowspan="2">Crane Rate Usage (%)</th> 
                           <th class="text-xs" rowspan="2">Laytime to Count (hrs)</th> 
                           <th class="text-xs" rowspan="2">Time Not to Count (hrs)</th> 
                           <th class="text-xs" rowspan="2">Total Laytime Used (hrs)</th> 
                           <th class="text-xs" rowspan="2">Total Laytime Used (days)</th> 
                           <th class="text-xs" rowspan="2">Balance Laytime Allowed (days)</th> 
                        </tr>
                        <tr>
                            <th class="text-xs">From</th>
                            <th class="text-xs">To</th>
                        </tr>
                        <tr v-for="(item, index) in datanya2" :key="index+'datanya2'">
                            <td class="text-xs">{{item.wheater}}</td>
                            <td class="text-xs">{{item.date}}</td>
                            <td class="text-xs">{{item.day}}</td>
                            <td class="text-xs">{{item.from}}</td>
                            <td class="text-xs">{{item.to}}</td>
                            <td class="text-xs">{{item.code}}</td>
                            <td class="text-xs">{{item.rate}} %</td>
                            <td class="text-xs">{{item.laytime_to_count}}</td>
                            <td class="text-xs">{{item.laytime_not_count}}</td>
                            <td class="text-xs">{{item.laytime_used_hour}}</td>
                            <td class="text-xs">{{item.laytime_used_days}}</td>
                            <td class="text-xs">{{item.balance_days}}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-12">
                    <p class="font-semibold text-red-600" v-if="datanya2.length>0">Days in Demurrage {{datanya2[datanya2.length-1]['balance_days']?datanya2[datanya2.length-1]['balance_days']:datanya2[datanya2.length-2]['balance_days']}} Days</p>
                    <p class="font-semibold text-red-600" v-if="datanya.length>0">Value of Demurrage {{datanya[0].value_demmurage}}</p>
                </div>
              </div>
          </div>
        </div>
    </div>
</main><!-- End #main -->
<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    var id="<?= @$_GET['id'] ?>"
    new Vue({
        el:"#sales-laytime",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                sortTable:['id','date','customer_name','contract_no','shipment_no','shipped_qty','unloading_qty','variance'], // disusun berdasarkan urutan td td td
                modals:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                datanya2:[],
                vdata:{
                    status:'Draft'
                },
                vdata2:[],
                masterLaytime:[],
                customer:[],
                shipment:[],
                product:[],
                sales_order:[],
                dari_tanggal:'',
                sampai_tanggal:'',
                id,
                option:{
                    headers:{
                        'X-Requested-With': 'XMLHttpRequest',
                        'contentType': "application/json",
                    }
                }
            }
        },
        methods: {
            printThis(){
                window.print();
            },
            formatTgl(tgl,pattern="YYYY-MM-DD") {
                return dateFns.format(
                    new Date(tgl),
                    pattern
                );
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/get/sales-laytime?id=${this.id}`);
                data.data=data.data.map(e=>{
                    return{
                        ...e,
                        shipment_id:e.shipment_no
                    }
                })
                this.datanya=data.data;
                this.showInsert=false;
                let res = await axios.get("<?= site_url() ?>"+`/api/get/sales-laytime-item?id=${this.id}`);
                this.datanya2=res.data;
                res.data.forEach((e,i)=>{
                    e['item']='00'+(i+1)
                    this.vdata2.push(e)
                })
                let res2 = await axios.get("<?= site_url() ?>"+`/api/get/master-laytime`);
                this.masterLaytime=res2.data;
                this.$forceUpdate();
            },
            
        },
        mounted() {
            document.getElementById('sales-laytime').classList.remove('d-none');
            this.getData();
            this.$forceUpdate();
        },
    })
</script>