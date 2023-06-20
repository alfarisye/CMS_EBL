<!-- FLOW NYA BERHUBUNGAN DENGAN T_SAL_PRICE DAN SALES_INV

Setiap sales invoice dibuat maka akan mempengaruhi t_sal_price entah itu di create baru apabila t_sal_price nya belum ada di shipment yang dipilih di inputan
atau di update apabila di t_sal_price sudah ter create dengan shipment yang dipilih 
otomatis data sales di dashboard juga berpengaruh
-->
<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<script src="https://unpkg.com/vue-select@3.0.0"></script>
<link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">
<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:1000;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:1005;min-width:80vw;max-height:75vh;overflow:scroll;}
</style>
<main id="main" class="main">
    <div id="FiSalesInv" class="d-none">
        <div class="pagetitle">
            <h1>SALES INVOICE</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url("/sales/invoice") ?>">Invoice</a></li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("sales/sales-inv") ?>">Sales Invoice</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="offModal" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" >
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <div class="row">
                    <div class="col-6">
                        <table>
                            <tr>
                                <td class="p-2 text-xs">Contract No</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <select class='form-control' @change="getShipment" v-model="vdata.CONTRACT_NO">
                                        <option v-for="(item, index) in tdContract" :key="index+'tdContract'">
                                            {{item.contract_no}}
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Buyer</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <p class="font-semibold text-sm pt-2">
                                        {{customer_name}}
                                    </p>
                                </td>
                            </tr>
                            <tr>    
                                <td colspan="3" style="width: 100%;" class="p-2 text-xs">
                                    <div>
                                        Shipment ID | Final Quantity
                                    </div>
                                    <div v-if="listShipment.length>0">
                                        <div v-for="(item, index) in listShipment" :key="index+'litsShipment'">
                                            <input type="checkbox" :id="`shipment_id${index}`"  :true-value="item.shipment_id" v-model="vdata[`SHIPMENT_ID${index+1}`]"
                                            @change="vdata[`FNL_QTY${index+1}`]=vdata[`SHIPMENT_ID${index+1}`]?item.discharging_qty:0"
                                            >
                                            <label :for="`shipment_id${index}`">{{item.shipment_id}} | 
                                            <span v-if="vdata[`SHIPMENT_ID${index+1}`]" class="ml-3">
                                                Qty : 
                                                <input v-if="vdata[`SHIPMENT_ID${index+1}`]" required @change="getFinalAmount" type="text" :id="`FNL_QTY${index+1}`" :name="`FNL_QTY${index+1}`" style="width:80px;" class="form-control p-1 text-xs rounded-sm d-inline ml-3" placeholder="Final Quantity..." v-model="vdata[`FNL_QTY${index+1}`]" ></label>
                                            </span>    
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Final Price</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input required @change="getFinalAmount" type="number" id="FNL_PRICE" name="FNL_PRICE" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="Final Price..." v-model="vdata['FNL_PRICE']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Final Amount</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input required  type="text" id="FNL_AMNT" name="FNL_AMNT" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="Final Amount..." v-model="vdata['FNL_AMNT']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Baseline Date</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input required  type="date" id="ZFBDT" name="ZFBDT" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="Baseline Date..." v-model="vdata['ZFBDT']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Payment Term</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <v-select :options="FI_ZTERM" label="ZTERM" v-model="vdata.ZTERM" :reduce="e => e.ZTERM"></v-select>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Reference Doc. No.</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input  required type="text" id="XBLNR" name="XBLNR" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="Reference Doc. No...." v-model="vdata['XBLNR']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Attachment</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <div class="my-2" v-if="vdata.ATTCH">
                                        <button type="button" class="btn btn-sm btn-warning " 
                                        @click="dowloadpdf(vdata)">Download <i class="ri-download-line"></i></button>
                                    </div>
                                    <input  type="file" id="filenya" name="filenya" class="form-control p-1  rounded-sm shadow-sm" placeholder="filenya ..."  >
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-6">
                        <table>
                            <tr>
                                <td class="p-2 text-xs">Sales Discounts</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input  type="number" id="SAL_DISC" name="SAL_DISC" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="Sales Discounts..." v-model="vdata['SAL_DISC']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Order</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <v-select :options="FI_AUFK" label="item_data" v-model="vdata.AUFNR" :reduce="e => e.AUFNR"></v-select>
                                </td>
                            </tr>
                             <tr>
                                <td class="p-2 text-xs">Invoice Date</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input   type="date" id="BLDAT" name="BLDAT" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="Invoice Date..." v-model="vdata['BLDAT']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Posting Date</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input   type="date" id="BUDAT" name="BUDAT" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="Posting Date..." v-model="vdata['BUDAT']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Currency</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <select class='form-control' @change="getUKURS($event)" v-model="vdata.TCURR">
                                        <option v-for="(item, index) in FI_CUR_EXC" :key="index+'FI_CUR_EXC'">{{item.TCURR}}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Exchange Rate</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input   type="text" id="UKURS" name="UKURS" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="Exchange Rate..." v-model="vdata['UKURS']" >
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-3">
                                        <a :href="`<?= site_url() ?>/sales/parameter-coa?shipment_id=${vdata.SHIPMENT_ID}`" v-if="vdata.SHIPMENT_ID">Parameter COA</a>
                                </td>
                            </tr>
                            <tr style="height:100px;">
                                <td class="p-2 text-xs">Text</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                <div class="sm-form">
                                    <textarea type="text" id="SGTXT" name="SGTXT" rows="2"  placeholder="SGTXT..." class="form-control md-textarea" v-model="vdata.SGTXT"
                                    ></textarea>
                                </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">PPN</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <select class='form-control' @change="getPPH22" v-model="vdata.PPN">
                                        <option v-for="(item, index) in ZFIT_CMSPPN" :key="index+'ZFIT_CMSPPN'"  :value="item.TEXT1">{{item.TEXT1}}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">PPN Amount</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input   type="text" id="PPN_AMNT" name="PPN_AMNT" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="PPN Amount..." v-model="vdata['PPN_AMNT']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">PPh 22</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <input   type="text" id="PPH_22" name="PPH_22" style="width:100%;" class="form-control p-1  rounded-sm" placeholder="Pph 22..." v-model="vdata['PPH_22']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">WBS Element</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <v-select :options="T_PRPS" label="item_data" v-model="vdata.PROJK" :reduce="e => e.POSID"></v-select>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 text-xs">Profit Center</td>
                                <td class="px-2">:</td>
                                <td style="width:100%;">
                                    <v-select :options="FI_CEPC" label="item_data" v-model="vdata.PRCTR" :reduce="e => e.PRCTR"></v-select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr class="my-4">
                <div class="text-right">
                    <button type="button" @click="modals=false"  class="btn btn-sm btn-warning ml-2 ">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success ml-2 " v-if="showInsert!='none'">{{showInsert?'Save Data':'Update Data'}}</button>
                </div>
               </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" >SALES INVOICE</h5>
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
                            <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Invoice</button>
                        </div>
                    </div>
                    <div class="col-sm-3">
                         <!-- <form v-if="datanya.length>0" action="<?= site_url() ?>/production/report/download?nama_file=FI_SALES_INV" method="post">
                            <textarea :value="JSON.stringify(td)" v-show="false" type="text" id="datanya" name="datanya" rows="2" placeholder="datanya..." class="form-control md-textarea"  ></textarea>
                            <button type="submit" class="btn btn-sm btn-dark text-xs ">Excel <i class="ri-download-line"></i></button>
                        </form> -->
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                           <!-- <th class="text-xs" style="background:lightgreen;" scope="col">
                                Shipment ID &#8593;&#8595;
                            </th> -->
                            <th class="text-xs" style="background:lightgreen;" scope="col">
                                Contract No &#8593;&#8595;
                            </th>
                            <!-- <th class="text-xs" style="background:lightgreen;" scope="col">
                                Buyer &#8593;&#8595;
                            </th> -->
                            <th class="text-xs" style="background:lightgreen;" scope="col">
                                Reference &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Doc. No. SAP &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Doc. No. Reversal SAP &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Created by &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Created On &#8593;&#8595;
                            </th>
                            <th class="text-xs" style="background:lightgreen;" scope="col">
                                Action 
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                                <!-- <td class="text-xs">{{item.SHIPMENT_ID}}</td> -->
                                <td class="text-xs">{{item.CONTRACT_NO}}</td>
                                <!-- <td class="text-xs">{{item.KUNNR}}</td> -->
                                <td class="text-xs">{{item.XBLNR}}</td>
                                <td class="text-xs">{{item.STATUS_SAP=='E'?item.MESSAGE_SAP:item.BELNR}}</td>
                                <td class="text-xs">{{item.STBLG}}</td>
                                <td class="text-xs">{{item.USNAM}}</td>
                                <td class="text-xs">{{formatTgl(item.CPUDT,'DD-MM-YYYY')}}</td>
                                <td >
                                    <div v-if="disableInput[index]">
                                        <button type="button"  @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                            <span class="tipstextB">Edit</span>
                                        </button>
                                        <button type="button"  @click="deleteData(item)" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                            <span class="tipstextB">Delete</span>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark text-xs ml-1" @click="disableInput[index]=!disableInput[index];$forceUpdate()">&#9779;</button>
                                    </div>
                                    <div v-else>
                                        <button v-if="item.BELNR=='' || item.BELNR==null" type="button" class="btn btn-sm btn-dark text-xs" @click="disableInput[index]=!disableInput[index];showInsert=false;$forceUpdate()">Edit &#9779;</button>
                                        <button v-else type="button" class="btn btn-sm btn-warning text-xs" @click="showUpdate(item);showInsert='none'" ><i
                                                    class="my-1 py-1 ri-search-line text-sm"
                                                    style="font-size : 12px;"></i> </button>
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
</main><!-- End #main -->
<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    var pages="<?= @$_GET['page'] ?>"
    new Vue({
        el:"#FiSalesInv",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                sortTable:["CPUDT"], // disusun berdasarkan urutan td td td
                modals:false,
                showInsert:false,
                listShipment:[],
                customer_name:'',
                t_sal_shipment:<?= json_encode($shipment) ?>,
                T_SAL_CONTRACT_ORDER:<?= json_encode($T_SAL_CONTRACT_ORDER) ?>,
                T_MDCUSTOMER:<?= json_encode($T_MDCUSTOMER) ?>,
                FI_ZTERM:<?= json_encode($FI_ZTERM) ?>,
                ZFIT_CMSPPN:<?= json_encode($ZFIT_CMSPPN) ?>,
                T_PRPS:<?= json_encode($T_PRPS) ?>,
                FI_CEPC:<?= json_encode($FI_CEPC) ?>,
                FI_AUFK:<?= json_encode($FI_AUFK) ?>,
                FI_CUR_EXC:<?= json_encode($FI_CUR_EXC) ?>,
                // CUSTOM
                datanya:[],
                vdata:{},
                vdata2:{},
                pages,
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
            },
            tdShipment(){
                let that=this;
                let data=this.t_sal_shipment;
                let uniqArrayOfObject = [...new Map(data.map((item) => [item["shipment_id"], item])).values()];
                return uniqArrayOfObject;
            },
            tdContract(){
                let data=this.T_SAL_CONTRACT_ORDER;
                data = [...new Map(data.map((item) => [item["contract_no"], item])).values()];
                return data;
            }
        },  
        components:{
            vSelect:VueSelect.VueSelect, // taruh di components
        },
        methods: {
            offModal(){
                this.modals=false
                this.listShipment=[]
                this.vdata={}
                this.$forceUpdate();
            },
            getShipment(){
                let data=this.t_sal_shipment.filter(e=>e.contract_no==this.vdata.CONTRACT_NO);
                if(data.length>0){
                    let contract=this.T_SAL_CONTRACT_ORDER.filter(e=>e.id==data[0]['contract_id']);
                    this.vdata.SHIPMENT_TYPE=contract[0]['type']
                    this.vdata.KUNNR=contract[0]['customer_code']
                    this.customer_name=contract[0]['customer_name']
                }
                data = [...new Map(data.map((item) => [item["shipment_id"], item])).values()];
                this.listShipment=data;
               this.$forceUpdate();
            },
            getUKURS(e){
                console.log(e.target.value)
            },
            getFinalAmount(){
                let fnl_qty=0;
                for(let i=0;i<10;i++){
                    fnl_qty=fnl_qty+parseFloat(this.vdata[`FNL_QTY${i+1}`]??0)
                }
                if(this.vdata.FNL_PRICE){
                    this.vdata.FNL_AMNT=parseFloat(this.vdata.FNL_PRICE)*fnl_qty
                    this.vdata.FNL_AMNT=this.vdata.FNL_AMNT.toFixed(2);
                }
                this.$forceUpdate();
            },
            getPPH22(){
                let tax=this.ZFIT_CMSPPN.filter(e=>e.TEXT1==this.vdata.PPN)[0].Tax_Rate
                if(this.vdata.FNL_AMNT){
                    this.vdata.PPN_AMNT=((parseInt(tax)/100)*this.vdata.FNL_AMNT).toFixed(2);
                    this.vdata.PPH_22=(0.015*parseInt(this.vdata.FNL_AMNT)).toFixed(2)
                    this.$forceUpdate();
                }else{
                    sdb.alert('Error Data PPN Amount atau Final Amount kosong');
                }
                console.log('getpph2')
                this.$forceUpdate();
            },
            findContract(id){
                if(id){
                    return this.T_SAL_CONTRACT_ORDER.filter(e=>e.id==id)
                }else{
                    return [{
                        category:'',
                        type:'',
                        quantity:0
                    }]
                }
            },
            validateRequired(){ // validation manual select semua input required
                let validation=true;
                document.querySelectorAll('[required]').forEach(e=>{e.reportValidity()?'':validation=false});
                return validation;
            },
            async insertData(){
                if(!this.validateRequired())return;
                sdb.loadingOn();
                let token=await axios.post("<?= site_url() ?>" + `/api/test`);
                this.vdata[Object.keys(token.data[0])[0]]=Object.values(token.data[0])[0];
                let uploadfile= await this.uploadFile();
                this.vdata.created_by="<?php echo session()->get('username') ?>";
                this.vdata.USNAM="<?php echo session()->get('username') ?>";
                this.vdata.ATTCH=uploadfile;
                axios.post("<?= site_url() ?>" + `/api/fi-sales-inv`,this.vdata).then(res=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert('Insert data berhasil!','bg-green-400');
                    this.vdata={}
                    this.getData();
                }).catch(err=>{
                    sdb.loadingOff();
                    this.modals=false;
                    sdb.alert(err.response.data.message??'Insert data gagal');
                });  
                // ===================
               this.updateTSALPRICE();
            },
            async updateData(){
                if(!this.validateRequired())return;
                sdb.loadingOn();
                let id=this.vdata.id;
                delete this.vdata.id;
                // this.getFinalAmount();
                let uploadfile= await this.uploadFile();
                this.vdata.updated_by="<?php echo session()->get('username') ?>";
                this.vdata.ATTCH=uploadfile;
                axios.put("<?= site_url() ?>" + `/api/fi-sales-inv/${id}`,this.vdata).then(res=>{
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
               this.updateTSALPRICE();
            },
            async deleteData(data){
                if(!confirm('Are you sure ? this data will be deleted.'))return;
                sdb.loadingOn();
                axios.delete("<?= site_url() ?>" + `/api/fi-sales-inv/${data.id}`).then(res=>{
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
            updateTSALPRICE(){
                this.listShipment.forEach((e,i)=>{
                    if(this.vdata[`FNL_QTY${i+1}`]){
                        let data={}
                        data.shipment_id=this.vdata[`SHIPMENT_ID${i+1}`];
                        data.final_quantity=this.vdata[`FNL_QTY${i+1}`];
                        data.final_price=this.vdata.FNL_PRICE;
                        data.curr=this.vdata.TCURR
                        data.date_final=this.vdata.BLDAT
                        data.amount=parseFloat(this.vdata.FNL_PRICE)*parseFloat(this.vdata[`FNL_QTY${i+1}`]);
                        data.shipment_type=this.findContract(this.t_sal_shipment.filter(e=>e.shipment_id==data.shipment_id)[0].contract_id??false).length>0?this.findContract(this.t_sal_shipment.filter(e=>e.shipment_id==data.shipment_id)[0].contract_id??false)[0].category:'';
                        data.type=this.findContract(this.t_sal_shipment.filter(e=>e.shipment_id==data.shipment_id)[0].contract_id??false).length>0?this.findContract(this.t_sal_shipment.filter(e=>e.shipment_id==data.shipment_id)[0].contract_id??false)[0].type:'';
                        data.contract_price=this.findContract(this.t_sal_shipment.filter(e=>e.shipment_id==data.shipment_id)[0].contract_id??false).length>0?this.findContract(this.t_sal_shipment.filter(e=>e.shipment_id==data.shipment_id)[0].contract_id??false)[0].contract_price:'';
                        data.quantity_contract=this.findContract(this.t_sal_shipment.filter(e=>e.shipment_id==data.shipment_id)[0].contract_id??false).length>0?this.findContract(this.t_sal_shipment.filter(e=>e.shipment_id==data.shipment_id)[0].contract_id??false)[0].quantity:'';
                        axios.post("<?= site_url() ?>" + `/api/add-sales-price?data=${JSON.stringify(data)}`,data).then(res=>{});
                    }
                })
            },
            showUpdate(item){
                this.vdata=JSON.parse(JSON.stringify(item));
                this.showInsert=false;
                this.modals=true;
                let contract=this.T_SAL_CONTRACT_ORDER.filter(e=>e.contract_no==item['CONTRACT_NO']);
                if(contract.length>0){
                    this.customer_name=contract[0]['customer_name']
                }
                this.getShipment();
                this.$forceUpdate();
            },
            async uploadFile(){
                let data={}
                let file = document.querySelector("#filenya"); // berikan id pada input file
                if(file.files.length>0){
                    if(!(file.files[0].name.indexOf('.pdf')!=-1)){
                        sdb.alert('Error format file must be *.pdf !');
                        return false;
                    }
                    if(!confirm('Are you sure to upload this file ? '))return false;
                    sdb.loadingOn();
                    let fd= new FormData();
                    fd.append('file',file.files[0]);
                    fd.append('<?= csrf_token() ?>','<?= csrf_hash() ?>');
                    return await axios.post("<?= site_url() ?>" + `/api/upload`,fd,this.option).then(async (res)=>{
                        sdb.loadingOff();
                        if(res.data){
                            return res.data.filepath
                        }else{
                            return false;
                        }
                    });
                }
            },
            dowloadpdf(item){
                let data={
                    path:item.ATTCH
                }
                let name = item.ATTCH.split('/')[2]
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
            async getData(){
                this.datanya=[]
                let data = await axios.get("<?= site_url() ?>" + `/api/get/fi-sales-inv`);
                this.datanya=data.data;
                this.showInsert=false;
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
            formatTgl(tgl,pattern="YYYY-MM-DD") {
                return dateFns.format(
                    new Date(tgl),
                    pattern
                );
            },
        },
        mounted() {
            this.getData();
            this.FI_AUFK.map(function (x){ // taruh setelah produks di initial
                  return x.item_data = x.AUFNR + ' ( ' + x.KTEXT + ' ) ';
            });
            this.FI_CEPC.map(function (x){ // taruh setelah produks di initial
                  return x.item_data = x.PRCTR + ' ( ' + x.LTEXT + ' ) ';
            });
            this.T_PRPS.map(function (x){ // taruh setelah produks di initial
                  return x.item_data = x.POST1 + ' ( ' + x.POSID + ' ) ';
            });
            document.getElementById('FiSalesInv').classList.remove('d-none');
            this.$forceUpdate();
        },
    })
</script>
<?= $this->endSection() ?>
