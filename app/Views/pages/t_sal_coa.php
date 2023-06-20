<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">
<!-- SCRIPT -->
<script src="https://unpkg.com/vue-select@3.0.0"></script>
<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:10000;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:10005;min-width:80vw}
</style>
<main id="main" class="main">
    <div id="t-sal-coa" class="d-none">
        <div class="pagetitle">
            <h1>Sales COA</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Sales</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("sales/parameter-coa") ?>">Sales COA</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" style="height:80vh;overflow:scroll;">
               <p class="text-sm font-semibold">Maintain COA</p>
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-sm ">
                            <tr>
                                <td class="p-2">Date Report</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required type="date" id="date_report" name="date_report" class="form-control p-1  rounded-sm shadow-sm" placeholder="date_report ..." v-model="vdata['date_report']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Shipment ID</td>
                                <td class="px-2">:</td>
                                <td>
                                    <v-select @input="getOther" :options="shipment" label="shipment_id" v-model="vdata.shipment_id" :reduce="e => e.shipment_id"></v-select>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Customer</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required disabled type="text" id="customer_name" name="customer_name" class="form-control p-1  rounded-sm shadow-sm" placeholder="customer_name ..." v-model="vdata['customer_name']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Vessel</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required disabled type="text" id="vessel" name="vessel" class="form-control p-1  rounded-sm shadow-sm" placeholder="vessel ..." v-model="vdata['vessel']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Quantity</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required disabled type="text" id="quantity" name="quantity" class="form-control p-1  rounded-sm shadow-sm" placeholder="quantity ..." v-model="vdata['quantity']" >
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                    <table class="table table-sm ">
                            <tr>
                                <td class="p-2">Source Cargo</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required type="text" id="source_cargo" name="source_cargo" class="form-control p-1  rounded-sm shadow-sm" placeholder="source_cargo ..." v-model="vdata['source_cargo']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Destination</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required type="text" id="destination" name="destination" class="form-control p-1  rounded-sm shadow-sm" placeholder="destination ..." v-model="vdata['destination']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Date of Sampling</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required type="date" id="date_sampling" name="date_sampling" class="form-control p-1  rounded-sm shadow-sm" placeholder="date_sampling ..." v-model="vdata['date_sampling']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Loading Port</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required type="text" id="loading_port" name="loading_port" class="form-control p-1  rounded-sm shadow-sm" placeholder="loading_port ..." v-model="vdata['loading_port']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Standard</td>
                                <td class="px-2">:</td>
                                <td>
                                    <input required type="text" id="standard" name="standard" class="form-control p-1  rounded-sm shadow-sm" placeholder="standard ..." v-model="vdata['standard']" >
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table-sm table-bordered" style="width:100%;">
                        <tr>
                            <td>Activity</td>
                            <td>Contract</td>
                            <td>Preliminary</td>
                            <td>Final</td>
                            <td>Selisih</td>
                        </tr>
                        <tr v-for="(item, index) in masterDMO" :key="index+'masterDMO'">
                            <td>{{item.coa}}</td>
                            <td>
                                <input type="number"  :max="item.max" :min="item.min" :id="`Contract${index+1}`" :name="`Contract${index+1}`" class="form-control " :placeholder="`Contract${index+1}`" v-model="vdata[`Contract${index+1}`]" >
                            </td>
                            <td>
                                 <input type="number"  :max="item.max" :min="item.min"  :id="`preliminary${index+1}`" :name="`preliminary${index+1}`" class="form-control " :placeholder="`preliminary${index+1}`" v-model="vdata[`preliminary${index+1}`]" >
                            </td>
                            <td>
                                 <input type="number"  :max="item.max" :min="item.min"  :id="`final${index+1}`" :name="`final${index+1}`" class="form-control " :placeholder="`final${index+1}`" v-model="vdata[`final${index+1}`]" >
                            </td>
                            <td>
                                 <input type="number"  :max="item.max" :min="item.min"  :id="`selisih${index+1}`" :name="`selisih${index+1}`" class="form-control " :placeholder="`selisih${index+1}`" v-model="vdata[`selisih${index+1}`]" >
                            </td>
                        </tr>
                    </table>
                </div>
                <hr class="my-4">
                <div class="text-right">
                    <button type="button" @click="modals=false"  class="btn btn-sm btn-warning ml-2 ">Cancel</button>
                    <button type="submit"  class="btn btn-sm btn-success ml-2 ">{{showInsert?'Save Data':'Update Data'}}</button>
                </div>
               </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" >Sales COA</h5>
                <div class="row py-2" >
                    <div class="col-sm-3">
                        <select class='form-control' v-model="perPage" @change="page=1">
                            <option>5</option>
                            <option>10</option>
                            <option>50</option>
                            <option>100</option>
                            <option value="100000">Semua</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                            <input type="text" 
                            @change="page=1"
                            id="search" name="search" class="form-control p-2 text-xs rounded-lg " placeholder="search" v-model="search" >
                    </div>
                    <div class="col-sm-3">
                        <div class="text-center " >
                            <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Data</button>
                        </div>
                    </div>
                </div>
                <!-- {{masterDMO}} -->
                <div class="table-responsive" >
                    <table class="table table-bordered ">
                        <tr>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                ID &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Contract &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Customer Name &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Vessel &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Quantity &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Action 
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                                <td class="text-xs">
                                    {{item.id}}
                                </td>
                                <td class="text-xs">
                                    {{item.contract_no}}
                                </td>
                                <td class="text-xs">
                                    {{item.customer_name}}
                                </td>
                                <td class="text-xs">
                                    {{item.vessel}}
                                </td>
                                <td class="text-xs">
                                    {{item.quantity}}
                                </td>
                                <td >
                                    <div v-if="disableInput[index] " >
                                        <button type="button"  @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                            <span class="tipstextL">Edit</span>
                                        </button>
                                        <button type="button" @click="deleteData(item)" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                            <span class="tipstextB">Delete</span>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark text-xs ml-1" @click="disableInput[index]=!disableInput[index];$forceUpdate()">&#9779;</button>
                                    </div>
                                    <div v-else>
                                        <button  type="button" class="btn btn-sm btn-dark text-xs" @click="disableInput[index]=!disableInput[index];showInsert=false;$forceUpdate()">Edit &#9779;</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-right">
                        <button type="button" class="btn btn-sm  rounded-circle py-1 px-2 mr-2" 
                        :class="page==1?'btn-dark':'btn-dark-outline'" @click="page=1"><</button>
                        <button type="button" class="btn btn-sm  rounded-circle py-1 px-2 mr-2" 
                        :class="page==index+1?'btn-dark':'btn-dark-outline'"
                        v-for="(item, index) in totalPage" :key="index+'totalPage'" 
                        v-if="item<page+3 && item>page-3"
                        @click="page=index+1">{{index+1}}</button>
                        <button type="button" class="btn btn-sm  rounded-circle py-1 px-2 mr-2" 
                        :class="page==totalPage?'btn-dark':'btn-dark-outline'" @click="page=totalPage">></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="pageloading" style="height:80vh;" class="d-flex justify-content-center align-items-center text-center animate__animated animate__bounce animate__infinite text-2xl font-times">
            Loading ...
    </div>
</main><!-- End #main -->
<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    var pages="<?= @$_GET['page'] ?>"
    var shipment_id="<?= @$_GET['shipment_id'] ?>"
    new Vue({
        el:"#t-sal-coa",
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
                vdata:{},
                masterDMO:[],
                salesOrder:[],
                customer:[],
                shipment:[],
                product:[],
                sales_order:[],
                dari_tanggal:'',
                sampai_tanggal:'',
                contract_id:'',
                pages,
                option:{
                    headers:{
                        'X-Requested-With': 'XMLHttpRequest',
                        'contentType': "application/json",
                    }
                }
            }
        },
        components: {
            vSelect:VueSelect.VueSelect
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
            getBuyerName(){
                this.vdata.customer_name=this.customer.filter(e=>e.KUNNR==this.vdata.customer_code)[0].NAME1;
                this.$forceUpdate();
            },
            getProductName(){
                this.vdata.product_name=this.product.filter(e=>e.MATNR==this.vdata.product)[0].MAKTX;
                this.$forceUpdate();
            },
            getOther(k){
                let shipment = this.shipment.filter(e=>e.shipment_id==k);
                this.vdata.customer_name=shipment[0].customer_name;
                this.vdata.vessel=shipment[0].vessel;
                this.vdata.contract_no=shipment[0].contract_no;
                let sales=this.salesOrder.filter(e=>e.id==shipment[0].contract_id);
                this.vdata.quantity=sales.length>0?sales[0].quantity:'-';
                this.$forceUpdate();
            },
            getPercent(index){
                this.vdata[`percent${index}`]=((this.vdata[`qty${index}`]/this.vdata.contract_qty)*100).toFixed(2)
                this.$forceUpdate();
            },
            getVariance(){
                this.vdata.variance=parseFloat(this.vdata.shipped_qty??0)-parseFloat(this.vdata.rc_qty??0);
                this.$forceUpdate();
            },
            validateRequired(){ // validation manual select semua input required
                let validation=true;
                document.querySelectorAll('[required]').forEach(e=>{e.reportValidity()?'':validation=false});
                return validation;
            },
            async insertData(){
                let that=this;
                if(!this.validateRequired())return;
                sdb.loadingOn();
                this.vdata.status='';
                let token=await axios.post("<?= site_url() ?>" + `/api/test`);
                this.vdata.status='0'
                this.vdata.created_by="<?php echo session()->get('username') ?>";
                let cekid=this.datanya.map(e=>e.id);
                this.vdata[Object.keys(token.data[0])[0]]=Object.values(token.data[0])[0];
                axios.post("<?= site_url() ?>" + `/api/t-sal-coa`,this.vdata).then(async (res)=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Insert data berhasil!','bg-green-400');
                    this.getData();
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert(err.response.data.message??'Insert data gagal');
                });  
            },
            async updateData(){
                let that=this;
                if(!this.validateRequired())return;
                sdb.loadingOn();
                let id=this.vdata.id;
                delete this.vdata.id;
                this.vdata.updated_by="<?php echo session()->get('username') ?>";
                axios.put("<?= site_url() ?>" + `/api/t-sal-coa/${id}`,this.vdata).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    this.getData()
                    sdb.alert('Update data berhasil!','bg-green-400');
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    this.getData()
                    sdb.alert(err.response.data.message??'Update data gagal');
                });   
            },
            async deleteData(data){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                console.log("<?= site_url() ?>" + `/api/t-sal-coa/${data.id}`)
                axios.delete("<?= site_url() ?>" + `/api/t-sal-coa/${data.id}`).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Delete data berhasil!','bg-green-400');
                    this.getData();
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Delete data gagal!');
                });  
            },
            showUpdate(item){
                this.vdata=item;
                this.showInsert=false;
                this.modals=true;
                this.$forceUpdate();
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/get/t-sal-coa?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.datanya=data.data;
                this.showInsert=false;
                let masterDMO = await axios.get("<?= site_url() ?>" + `/api/get/t-sal-master-coa?limit=10`);
                this.masterDMO=masterDMO.data;
                let salesOrder = await axios.get("<?= site_url() ?>" + `/api/get/sales-order`);
                this.salesOrder=salesOrder.data;
                this.salesOrder.map(function (x){ // taruh setelah produks di initial
                      return x.item_data = x.contract_no+ ' | ' + x.id;
                });
                let shipment = await axios.get("<?= site_url() ?>" + `/api/get/t_sal_shipment`);
                this.shipment=shipment.data;
                if(shipment_id){
                    let data=this.datanya.filter(e=>e.shipment_id==shipment_id)
                    if(data.length>0){
                        this.showUpdate(data[0]);
                    }else{
                        alert('Data COA shipment id ini tidak ditemukan!');
                    }
                }
                this.$forceUpdate();
                setTimeout(() => {
                    this.sortField();
                }, 1000);
            },
            sortField(){
                let that=this
                document.querySelectorAll('table tr th').forEach((e,i)=>{
                    e.style.cursor='pointer';
                    e.addEventListener('click',()=>{
                        that.sortTable[1000]=!that.sortTable[1000];
                        if(!that.sortTable[1000]){
                            that.datanya=that.datanya.sort((a,b) => (a[that.sortTable[i]] > b[that.sortTable[i]]) ? 1 : ((b[that.sortTable[i]] > a[that.sortTable[i]]) ? -1 : 0))
                        }else{
                            that.datanya=that.datanya.sort((a,b) => (a[that.sortTable[i]] < b[that.sortTable[i]]) ? 1 : ((b[that.sortTable[i]] < a[that.sortTable[i]]) ? -1 : 0))
                        }
                        that.$forceUpdate();
                    })
                })
            },
            format(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY-MM-DD"
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
        },
        mounted() {
            document.getElementById('t-sal-coa').classList.remove('d-none');
            document.getElementById('pageloading').remove()
            this.bulan = this.bulans(new Date());
            this.tahun = parseInt(this.tahuns(new Date()));
            if (this.bulan && this.tahun) {
                this.periode = `${this.tahun}-${this.bulan}-${this.hari(new Date())}`;
                var date = new Date(this.periode);
                this.dari_tanggal = this.format(new Date(date.getFullYear(), date.getMonth(), 1));
                this.sampai_tanggal = this.format(new Date(date.getFullYear(), date.getMonth() + 1, 0));
                this.$forceUpdate();
            }
            setTimeout(() => {
                this.getData();
            }, 500);
            this.$forceUpdate();
        },
    })
</script>
<?= $this->endSection() ?>