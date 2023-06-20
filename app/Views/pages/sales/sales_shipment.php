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
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:1000;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:1005;min-width:50vw}
</style>
<main id="main" class="main">
    <div id="sales-shipment" class="d-none">
        <div class="pagetitle">
            <h1>Maintain Shipment</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Sales</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("sales/sales-shipment") ?>">Maintain Shipment</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" style="height:80vh;overflow:scroll;">
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <table class="table table-sm ">
                    <tr>
                        <td colspan="3">
                            <div class="row">
                                <div class="col-3">
                                    <div style="height:100px;" class="d-flex justify-content-center align-items-center ">
                                        <div style="width:100%">
                                            <p class="text-center font-bold text-lg">Maintain Data Shipment</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-9">
                                    <table>
                                        <tr>
                                            <td>
                                                ID
                                            </td>
                                            <td class="py-2">
                                                :
                                            </td>
                                            <td>
                                                <input disabled id type="text" id="id" name="id"  class="form-control p-1  rounded-sm shadow-sm" placeholder="id ..." v-model="vdata['id']" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Category Shipment
                                            </td>
                                            <td class="py-2">
                                                :
                                            </td>
                                            <td>
                                                <input disabled id type="text" id="category" name="category"  class="form-control p-1  rounded-sm shadow-sm" placeholder="category ..." v-model="vdata['category']" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Buyer
                                            </td>
                                            <td class="py-2">
                                                :
                                            </td>
                                            <td>
                                                <input disabled id type="text" id="customer_name" name="customer_name"  class="form-control p-1  rounded-sm shadow-sm" placeholder="customer_name ..." v-model="vdata['customer_name']" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Contract Number
                                            </td>
                                            <td class="py-2">
                                                :
                                            </td>
                                            <td>
                                                <v-select @input="cariuom" :options="sales" label="item_data" v-model="vdata.contract_id" :reduce="e => e.id"></v-select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Laycan Date
                                            </td>
                                            <td class="py-2">
                                                :
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input id type="date" id="laycan_date" name="laycan_date"  class="form-control p-1  rounded-sm shadow-sm" placeholder="laycan_date ..." v-model="vdata['laycan_date']" >
                                                    </div>
                                                    <div class="col-6">
                                                        <input id type="date" id="laycan_date_end" name="laycan_date_end"  class="form-control p-1  rounded-sm shadow-sm" placeholder="laycan_date_end ..." v-model="vdata['laycan_date_end']" >
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                ETA Date
                                            </td>
                                            <td class="py-2">
                                                :
                                            </td>
                                            <td>
                                                <input id type="date" id="ETA_date" name="ETA_date"  class="form-control p-1  rounded-sm shadow-sm" placeholder="ETA_date ..." v-model="vdata['ETA_date']" >
                                            </td>
                                        </tr>
                                    </table>
                                    <div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">TB/BG</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required  type="text" id="TBBG" name="TBBG"  class="form-control p-1  rounded-sm shadow-sm" placeholder="TBBG ..." v-model="vdata['TBBG']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Vessel</td>
                        <td class="px-2">:</td>
                        <td>
                            <input type="text" id="vessel" name="vessel"  class="form-control p-1  rounded-sm shadow-sm" placeholder="vessel ..." v-model="vdata['vessel']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Return Cargo</td>
                        <td class="px-2">:</td>
                        <td>
                            <input type="number" id="gi_qty" name="gi_qty" class="form-control p-1  rounded-sm shadow-sm" placeholder="gi_qty ..." v-model="vdata['gi_qty']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">POL Date</td>
                        <td class="px-2">:</td>
                        <td>
                            <input  type="date" id="bl_date" name="bl_date" class="form-control p-1  rounded-sm shadow-sm" placeholder="bl_date ..." v-model="vdata['bl_date']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">POL Qty</td>
                        <td class="px-2">:</td>
                        <td>
                            <input  type="number" step="any" id="bl_qty" name="bl_qty" class="form-control p-1  rounded-sm shadow-sm" placeholder="bl_qty ..." v-model="vdata['bl_qty']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">POD date</td>
                        <td class="px-2">:</td>
                        <td>
                            <input  type="date" id="discharging_date" name="discharging_date" class="form-control p-1  rounded-sm shadow-sm" placeholder="discharging_date ..." v-model="vdata['discharging_date']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">POD Qty</td>
                        <td class="px-2">:</td>
                        <td>
                            <input  type="number" step="any" id="discharging_qty" name="discharging_qty" class="form-control p-1  rounded-sm shadow-sm" placeholder="discharging_qty ..." v-model="vdata['discharging_qty']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Unit of Measure </td>
                        <td class="px-2">:</td>
                        <td>
                            <input required disabled type="text" id="uom" name="uom"  class="form-control p-1  rounded-sm shadow-sm" placeholder="uom ..." v-model="vdata['uom']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Type</td>
                        <td class="px-2">:</td>
                        <td>
                            <select class='form-control' v-model="vdata.type_supply">
                                <option >Partial Supply</option>
                                <option >Full Supply</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Upload</td>
                        <td class="px-2">:</td>
                        <td>
                            <input  type="file" id="filenya" name="filenya" class="form-control p-1  rounded-sm shadow-sm" placeholder="filenya ..."  >
                        </td>
                    </tr>
                    <!-- <tr>
                        <td class="p-2">Completed</td>
                        <td class="px-2">:</td>
                        <td>
                            <select class='form-control' v-model="vdata.status">
                                <option value="0">OPEN</option>
                                <option value="1">IN Progress</option>
                                <option value="2">FullFilled</option>
                            </select>
                        </td>
                    </tr> -->
                </table>
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
                <h5 class="card-title" >Maintain Data Shipment</h5>
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
                            <select class='form-control' v-model="statusnya">
                                <option value="0">Open</option>
                                <option value="2">Fullfilled</option>
                                <option value="1">In Progress</option>
                                <option value="">All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                            <div class="text-center " >
                                <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Data</button>
                            </div>
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-bordered ">
                        <tr>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                ID &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Contract No &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Laycan Date &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Vessel &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                POL &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                BL Date &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                UoM &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Type &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Status &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Download &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Action 
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                                <td class="text-xs">
                                    {{item.shipment_id}}
                                </td>
                                <td class="text-xs">
                                    {{item.contract_no}}
                                </td>
                                <td class="text-xs">
                                    {{formatTgl(item.laycan_date,'DD-MM-YYYY')}}
                                </td>
                                <td class="text-xs">
                                    {{item.vessel}}
                                </td>
                                <td class="text-xs">
                                    {{item.gi_qty}}
                                </td>
                                <td class="text-xs">
                                    {{formatTgl(item.bl_date,'DD-MM-YYYY')}}
                                </td>
                                <td class="text-xs">
                                    {{item.uom}}
                                </td>
                                <td class="text-xs">
                                    {{item.type_supply}}
                                </td>
                                <td class="text-xs font-semibold" :class="item.status=='0'?'text-orange-500':item.status=='1'?'text-blue-700':'text-green-500'">
                                    {{item.status=='0'?'OPEN':item.status=='1'?'IN PROGRESS':'Fullfilled'}}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning rounded-circle " 
                                    v-if="item.file!='' && !item.file==false"
                                    @click="dowloadpdf(item)"><i class="ri-download-line"></i></button>
                                </td>
                                <td >
                                    <div v-if="disableInput[index]" >
                                        <button type="button"  @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                            <span class="tipstextL">Edit</span>
                                        </button>
                                        <button type="button" @click="deleteDataStatus(item)" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                            <span class="tipstextB">Delete</span>
                                        </button>
                                        <a :href="`<?= site_url() ?>/sales/sales-shipment/pdf?id=${item.id}`" target="__blank" class="my-2">
                                            <button type="button" class="btn btn-sm  btn-info  text-xs tips">&#128438;
                                                <span class="tipstextB">Print</span>
                                            </button>
                                        </a>

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
    new Vue({
        el:"#sales-shipment",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                statusnya:'',
                target:{},
                disableInput:[],
                sortTable:['contract_no','bl_date','vessel','gi_qty','receipt_date'], // disusun berdasarkan urutan td td td
                modals:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                vdata:{},
                idnya:'',
                sales:[],
                customer:[],
                product:[],
                salesall:[],
                dari_tanggal:'',
                sampai_tanggal:'',
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
                data=data.filter(e=>e.status.indexOf(this.statusnya)!=-1)
                this.totalPage = Math.ceil(data.length/this.perPage);
                data=data.slice((this.page-1)*this.perPage,this.page*this.perPage);
                console.log((this.page-1),this.perPage,this.page,this.perPage)
                return data;
            }
        },  
        methods: {
            dowloadpdf(item){
                let data={
                    path:item.file
                }
                let name = item.file.split('/')[2]
                axios.post("<?= site_url() ?>" + `/api/download/pdf`,data,{
                    responseType:'blob'
                }).then(response => {
                    const href = URL.createObjectURL(response.data);
                    const link = document.createElement('a');
                    link.href = href;
                    link.setAttribute('download', `Sales_order-0${item.id}.pdf`); //or any other extension
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            },
            cariuom(e){
                let data=this.salesall.filter(k=>k.id==e)[0]
                this.vdata.contract_id=e;
                this.vdata.contract_no=data.contract_no;
                this.vdata.uom=data.uom;
                this.vdata.category=data.category;
                this.vdata.customer_name=data.customer_name;
                this.vdata.type=data.type;
                this.$forceUpdate();
            },
            getBuyerName(){
                this.vdata.customer_name=this.customer.filter(e=>e.KUNNR==this.vdata.customer_code)[0].NAME1;
                this.$forceUpdate();
            },
            getProductName(){
                this.vdata.product_name=this.product.filter(e=>e.MATNR==this.vdata.product)[0].MAKTX;
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
                let uploadfile= await this.uploadFile();
                this.vdata.file=uploadfile;
                console.log(this.vdata)
                axios.post("<?= site_url() ?>" + `/api/sales-shipment`,this.vdata).then(async (res)=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Insert data berhasil!','bg-green-400');
                    console.log(res)
                    let databaru=res.data[0];
                    if(this.vdata.laycan_date && this.vdata.bl_qty && this.vdata.type_supply=='Partial Supply'){
                        this.vdata=databaru;
                        this.vdata.status='1'
                        this.updateData2(true);
                        this.getData();
                    }else if(this.vdata.laycan_date && this.vdata.bl_qty && this.vdata.type_supply=='Full Supply'){
                        this.vdata=databaru;
                        this.vdata.status='2';
                        this.updateData2(true);
                        let datasales={
                            status:'3'
                        }
                        axios.put("<?= site_url() ?>" + `/api/sales-order/${this.idnya}`,datasales).then(res=>{
                            this.getData();
                        });
                    }
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert(err.response.data.message??'Insert data gagal');
                });  
            },
            async updateData(loading=false){
                let that=this;
                if(!this.validateRequired())return;
                if(!loading){
                    sdb.loadingOn();
                }
                let id=this.vdata.id;
                delete this.vdata.id;
                let uploadfile= await this.uploadFile();
                this.vdata.file=uploadfile;
                this.vdata.updated_by="<?php echo session()->get('username') ?>";
                axios.put("<?= site_url() ?>" + `/api/sales-shipment/${id}`,this.vdata).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    console.log(this.vdata)
                    this.idnya=this.salesall.filter(k=>k.contract_no==this.vdata.contract_no)[0].id
                    if(this.vdata.laycan_date && this.vdata.bl_qty && this.vdata.type_supply=='Partial Supply'){
                        this.vdata.status='1'
                        this.vdata.id=id;
                        this.updateData2(true);
                        this.getData()
                    }else if(this.vdata.laycan_date && this.vdata.bl_qty && this.vdata.type_supply=='Full Supply'){
                        this.vdata.id=id;
                        this.vdata.status='2';
                        this.updateData2(true);
                        let datasales={
                            status:'3'
                        }
                        axios.put("<?= site_url() ?>" + `/api/sales-order/${this.idnya}`,datasales).then(res=>{
                            this.getData()
                        });
                    }
                    sdb.alert('Update data berhasil!','bg-green-400');
                })
            },
            async updateData2(loading=false){
                let that=this;
                if(!this.validateRequired())return;
                if(!loading){
                    sdb.loadingOn();
                }
                let id=this.vdata.id;
                delete this.vdata.id;
                this.vdata.updated_by="<?php echo session()->get('username') ?>";
                axios.put("<?= site_url() ?>" + `/api/sales-shipment/${id}`,this.vdata).then(res=>{
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
            async deleteDataStatus(item){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                let id=item.id;
                delete item.id;
                item.status='2';
                axios.delete("<?= site_url() ?>" + `/api/sales-shipment/${id}`,item).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    this.getData()
                    sdb.alert('Delete data berhasil!','bg-green-400');
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    this.getData()
                    sdb.alert(err.response.data.message??'Delete data gagal');
                });   
            },
            async deleteData(data){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                axios.delete("<?= site_url() ?>" + `/api/sales-shipment/${data.id}`).then(res=>{
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
                // this.vdata.contract_no=item.contract_id
                this.showInsert=false;
                this.modals=true;
                this.$forceUpdate();
            },
            async getData(){
                let data;
                this.datanya=[]
                data = await axios.get("<?= site_url() ?>" + `/api/get/sales-shipment?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.datanya=data.data;
                this.showInsert=false;
                this.customer=[]
                let sales = await axios.get("<?= site_url() ?>" + `/api/get/sales-order?status=1`);
                this.sales=sales.data;
                this.sales.map(function (x){ // taruh setelah produks di initial
                      return x.item_data = x.contract_no + ' | ' + x.id;
                });
                let salesall = await axios.get("<?= site_url() ?>" + `/api/get/sales-order`);
                this.salesall=salesall.data;
                this.$forceUpdate();
                setTimeout(() => {
                    this.sortField();
                }, 1000);
            },
            async uploadFile(){
                let data={}
                let file = document.querySelector("#filenya"); // berikan id pada input file
                if(file.files.length>0){
                    if(!(file.files[0].name.indexOf('.pdf')!=-1)){
                        sdb.alert('Error format file must be *.pdf !');
                        return false;
                    }
                    if(!confirm('Are you sure to upload this file ? '))return;
                    sdb.loadingOn();
                    let fd= new FormData();
                    fd.append('file',file.files[0]);
                    fd.append('<?= csrf_token() ?>','<?= csrf_hash() ?>');
                    return await axios.post("<?= site_url() ?>" + `/api/upload`,fd,this.option).then(async (res)=>{
                        sdb.loadingOff();
                        if(res.data){
                            return res.data.filepath
                        }else{
                            return;
                        }
                    });
                }
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
            formatTgl(tgl,pattern="YYYY-MM-DD") {
                return dateFns.format(
                    new Date(tgl),
                    pattern
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
            document.getElementById('sales-shipment').classList.remove('d-none');
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