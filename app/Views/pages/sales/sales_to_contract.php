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
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:10005;min-width:50vw}
</style>
<main id="main" class="main">
    <div id="sales-order" class="d-none">
        <div class="pagetitle">
            <h1>Contract</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Sales</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("sales/sales-order") ?>">Contract</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn" style="height:80vh;overflow:scroll;">
                <p class="text-sm font-semibold">Maintain Sales Order</p>
                <p class="text-lg font-semibold">{{vdata['id']}}</p>
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <table class="table table-sm ">
                    <tr>
                        <td class="p-2">Contract No</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required  type="text" id="contract_no" name="contract_no"  class="form-control p-1  rounded-sm shadow-sm" placeholder="contract_no ..." v-model="vdata['contract_no']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Date</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required disabled type="date" id="date" name="date" class="form-control p-1  rounded-sm shadow-sm" placeholder="date ..." v-model="vdata['date']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Contract Date</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required type="date" id="contract_date" name="contract_date" class="form-control p-1  rounded-sm shadow-sm" placeholder="contract_date ..." v-model="vdata['contract_date']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Category</td>
                        <td class="px-2">:</td>
                        <td>
                            <select disabled class='form-control' v-model="vdata.category">
                                <option>FOBB</option>
                                <option>MV</option>
                                <option>CIF</option>
                                <option value="Franko Pabrik">Franco Pabrik</option>
                            </select>
                            <!-- <input required :disabled="!showInsert" type="text" id="category" name="category"  class="form-control p-1  rounded-sm shadow-sm" placeholder="category ..." v-model="vdata['category']" > -->
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Type</td>
                        <td class="px-2">:</td>
                        <td>
                            <select disabled class='form-control' v-model="vdata.type">
                                <option>Local</option>
                                <option>Export</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Buyer</td>
                        <td class="px-2">:</td>
                        <td>
                            <v-select disabled :options="customer" @input="getBuyerName" label="item_data" v-model="vdata.customer_code" :reduce="e => e.KUNNR"></v-select>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Buyer Name</td>
                        <td class="px-2">:</td>
                        <td>
                            <input disabled required disabled type="text" id="customer_name" name="customer_name"  class="form-control p-1  rounded-sm shadow-sm" placeholder="customer_name ..." v-model="vdata['customer_name']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Address</td>
                        <td class="px-2">:</td>
                        <td style="height:100px;">
                            <textarea disabled type="text" id="address" name="address" rows="2" placeholder="address..." class="form-control md-textarea" v-model="vdata.address"
                            ></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Product</td>
                        <td class="px-2">:</td>
                        <td>
                            <v-select disabled :options="product" @input="getProductName" label="item_data" v-model="vdata.product" :reduce="e => e.MATNR"></v-select>
                            <!-- <input required :disabled="!showInsert" type="text" id="product" name="product"  class="form-control p-1  rounded-sm shadow-sm" placeholder="product ..." v-model="vdata['product']" > -->
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Product Name</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required disabled type="text" id="product_name" name="product_name"  class="form-control p-1  rounded-sm shadow-sm" placeholder="product_name ..." v-model="vdata['product_name']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Quantity</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required disabled  type="text" id="quantity" name="quantity"  class="form-control p-1  rounded-sm shadow-sm" placeholder="quantity ..." v-model="vdata['quantity']" >
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
                        <td class="p-2">Contract Price</td>
                        <td class="px-2">:</td>
                        <td>
                            <input required disabled type="text" id="contract_price" name="contract_price"  class="form-control p-1  rounded-sm shadow-sm" placeholder="contract_price ..." v-model="vdata['contract_price']" >
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Currency</td>
                        <td class="px-2">:</td>
                        <td>
                            <select disabled class='form-control' v-model="vdata.currency">
                                <option>IDR</option>
                                <option>USD</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Delivery Condition</td>
                        <td class="px-2">:</td>
                        <td style="height:100px;">
                            <textarea disabled type="text" id="delivery_condition" name="delivery_condition" rows="2" placeholder="delivery_condition..." class="form-control md-textarea" v-model="vdata.delivery_condition"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Price Condition</td>
                        <td class="px-2">:</td>
                        <td style="height:100px;">
                            <textarea disabled type="text" id="price_condition" name="price_condition" rows="2" placeholder="price_condition..." class="form-control md-textarea" v-model="vdata.price_condition"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Quality Parameter</td>
                        <td class="px-2">:</td>
                        <td style="height:100px;">
                            <textarea disabled type="text" id="quality_parameter" name="quality_parameter" rows="2" placeholder="quality_parameter..." class="form-control md-textarea" v-model="vdata.quality_parameter"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Parameter</td>
                        <td class="px-2">:</td>
                        <td style="height:100px;">
                            <textarea disabled type="text" id="parameter" name="parameter" rows="2" placeholder="parameter..." class="form-control md-textarea" v-model="vdata.parameter"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">Terms of Payment</td>
                        <td class="px-2">:</td>
                        <td style="height:100px;">
                            <textarea disabled type="text" id="top" name="top" rows="2" placeholder="top..." class="form-control md-textarea" v-model="vdata.top"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2">WBS Element</td>
                        <td class="px-2">:</td>
                        <td>
                            <input   type="text" id="wbs_element" name="wbs_element"  class="form-control p-1  rounded-sm shadow-sm" placeholder="wbs_element ..." v-model="vdata['wbs_element']" >
                        </td>
                    </tr>
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
                <h5 class="card-title" >Contract</h5>
                <div class="row">
                    <div class="col-6 text-xs">
                        <table>
                            <tr>
                                <td>
                                    <input type="date" id="dari_tanggal" @change="getData()" name="dari_tanggal" class="form-control p-1 rounded-sm shadow-sm" placeholder="dari_tanggal" v-model="dari_tanggal" >
                                </td>
                                <td class="px-3">S/D</td>
                                <td>
                                    <input type="date" id="sampai_tanggal" @change="getData()" name="sampai_tanggal" class="form-control p-1 rounded-sm shadow-sm" placeholder="sampai_tanggal" v-model="sampai_tanggal" >
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
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
                            <!-- <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Sales Order</button> -->
                        </div>
                    </div>
                    <div class="col-sm-3">
                         <!-- <form v-if="datanya.length>0" action="<?= site_url() ?>/production/report/download?nama_file=production report" method="post">
                                <textarea :value="JSON.stringify(td)" v-show="false" type="text" id="datanya" name="datanya" rows="2" placeholder="datanya..." class="form-control md-textarea"  ></textarea>
                                <button type="submit" class="btn btn-sm btn-dark text-xs ">Excel <i class="ri-download-line"></i></button>
                            </form> -->
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-bordered ">
                        <tr>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Sales Order No &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Contract No &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Customer &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Sales Date &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col">
                                Contract Date &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Status &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Quantity &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                UoM &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Created &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" class="text-xs" scope="col ">
                                Last Update &#8593;&#8595;
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
                                    {{formatTgl(item.date,'DD-MM-YYYY')}}
                                </td>
                                <td class="text-xs">
                                    {{formatTgl(item.contract_date,'DD-MM-YYYY')}}
                                </td>
                                <td class="text-xs font-semibold" :class="item.status=='0'?'text-orange-500':item.status=='1'?'text-green-500':item.status=='2'?'text-red-500':item.status=='3'?'text-green-500':item.status=='4'?'text-blue-500':item.status=='5'?'text-blue-600':item.status=='6'?'text-blue-500':item.status=='7'?'text-blue-500':item.status=='8'?'text-blue-500':'text-black'">
                                    {{item.status=='0'?'Draft':item.status=='1'?'Full Approved':item.status=='2'?'Delete':item.status=='3'?'Complete':item.status=='4'?'Partial Approved 1':item.status=='5'?'Partial Approved 2':item.status=='6'?'Partial Approved 3':item.status=='7'?'Partial Approved 4':item.status=='8'?'Partial Approved 5':'Undefined'}}
                                </td>
                                
                                <td class="text-xs">
                                    {{item.quantity}}
                                </td>
                                
                                <td class="text-xs">
                                    {{item.uom}}
                                </td>
                                <td class="text-xs">
                                    {{formatTgl(item.created_at,'DD-MM-YYYY HH:mm:ss')}}
                                </td>
                                <td class="text-xs">
                                    {{formatTgl(item.updated_at,'DD-MM-YYYY HH:mm:ss')}}
                                </td>
                                <td >
                                    <div v-if="disableInput[index]" >
                                        <!-- <button type="button" @click="mintaApproval(item)" class="btn btn-sm  btn-primary  text-xs tips"><i class="ri-chat-check-line"></i>
                                            <span class="tipstextL">Permintaan Approval</span>
                                        </button> -->
                                        <button type="button"  @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                            <span class="tipstextL">Edit</span>
                                        </button>
                                        <!-- <button type="button" @click="deleteDataStatus(item)" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                            <span class="tipstextL">Delete</span>
                                        </button> -->
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
        el:"#sales-order",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                sortTable:['contract_no','customer_name','date','status','quantity','uom','created_at','updated_at'], // disusun berdasarkan urutan td td td
                modals:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                vdata:{},
                customer:[],
                product:[],
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
                this.totalPage = Math.ceil(data.length/this.perPage);
                data=data.slice((this.page-1)*this.perPage,this.page*this.perPage);
                return data;
            }
        },  
        methods: {
            async mintaApproval(item){
                let cek = await axios.get("<?= site_url() ?>"+'/api/get/t_sal_approval_step')
                if(cek.data){
                    let listUser=cek.data;
                    // let listUser=[
                    //     {
                    //         id:1,
                    //         level:1,
                    //         user_name:'malit',
                    //         email:'mrrudyska@gmail.com'
                    //     },
                    //     {
                    //         id:2,
                    //         level:2,
                    //         user_name:'malit',
                    //         email:'mrrudyska@gmail.com'
                    //     },
                    //     {
                    //         id:3,
                    //         level:3,
                    //         user_name:'malit',
                    //         email:'mrrudyska@gmail.com'
                    //     },
                    //     {
                    //         id:4,
                    //         level:4,
                    //         user_name:'malit',
                    //         email:'mrrudyska@gmail.com'
                    //     },
                    //     {
                    //         id:5,
                    //         level:5,
                    //         user_name:'malit',
                    //         email:'mrrudyska@gmail.com'
                    //     },
                    // ]
                    let user=listUser.filter(e=>e.level==1)[0];
                    if(confirm('Request Approve ?')){
                        let txt=`<h3>Dengan Hormat</h3>
                        <p>Berikut kami lampirkan draft kontrak untuk Customer <b>${item.customer_name}</b></p>
                        <p>Mohon bantuannya untuk dapat mereview serta persetujuan apabila kontrak tersebut sudah sesuai dengan membuka link : </p>
                        <p>
                            <a href="<?= site_url() ?>/sales/contract-order-approval"> <?= site_url() ?>/sales/contract-order-approval </a>
                        </p>
                        <br>
                        <br>
                        <p>Terima Kasih</p>
                        <br>
                        <br>
                        <p>Coal Monitoring System</p>
                        <p>PT. Energi Batubara Lestari</p>
                        <hr>`;

                        // let res = await this.sendEmail(user.email,'taufikakbarmalikitkj@gmail.com',`Approval Contract ${item.contract_no}`,txt,item.pdf);
                        
                        this.vdata=item;
                        this.vdata.status='4';
                        this.updateData();
                     }

                }
            },
            async sendEmail(to,cc,subject,message,attach=null){
                let txt=message;
                let fd=new FormData();
                fd.append('to',to);
                fd.append('cc',cc);
                fd.append('subject',subject);
                fd.append('message',txt);
                if(attach){
                    fd.append('attach',attach);
                }
                sdb.loadingOn();
                return await axios.post("<?= site_url() ?>"+`/api/send/mail`,fd).then(res=>{
                    if(res.data){
                        if(JSON.parse(res.data.split('}')[0]+'}').status=='true'){
                            sdb.loadingOff();
                            sdb.alert('Email Berhasil dikirim!','bg-green-400');
                            return true;
                        }else{
                            sdb.loadingOff();
                            sdb.alert('Email Gagal dikirim!','bg-red-400');
                            return false;
                        }
                    }
                });  
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
                axios.post("<?= site_url() ?>" + `/api/sales-order`,this.vdata).then(async (res)=>{
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
                axios.put("<?= site_url() ?>" + `/api/sales-order/${id}`,this.vdata).then(res=>{
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
                axios.put("<?= site_url() ?>" + `/api/sales-order/${id}`,item).then(res=>{
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
                console.log("<?= site_url() ?>" + `/api/sales-order/${data.id}`)
                axios.delete("<?= site_url() ?>" + `/api/sales-order/${data.id}`).then(res=>{
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
                data = await axios.get("<?= site_url() ?>" + `/api/get/sales-order?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}&blank=true`);
                this.datanya=data.data;
                this.showInsert=false;
                this.customer=[]
                let customer = await axios.get("<?= site_url() ?>" + `/api/get/customer`);
                this.customer=customer.data;
                let product = await axios.get("<?= site_url() ?>" + `/api/get/product-material`);
                this.product=product.data;
                this.customer.map(function (x){ // taruh setelah produks di initial
                      return x.item_data = x.KUNNR + ' ' + x.NAME1 ;
                });
                this.product.map(function (x){ // taruh setelah produks di initial
                      return x.item_data = x.MATNR + ' ' + x.MAKTX ;
                });
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
            document.getElementById('sales-order').classList.remove('d-none');
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