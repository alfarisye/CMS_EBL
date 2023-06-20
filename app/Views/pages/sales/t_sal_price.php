
<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:1000;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:1005;min-width:70vw}
</style>
<main id="main" class="main">
    <div id="TSalPrice" class="d-none">
        <div class="pagetitle">
            <h1>SALES PRICE</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("undefined/t-sal-price") ?>">SALES PRICE</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn">
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <div class="row">
                    <div class="col-6">
                    <table >
                        <tr>
                            <td class="p-2">Shipment_id</td>
                            <td class="px-2">:</td>
                            <td >
                                <select class='form-control' @change="getContract" v-model="vdata.shipment_id">
                                    <option v-for="(item, index) in T_SAL_SHIPMENT" :key="index+'SAL_SHIPMENT'" :value="item.shipment_id">{{item.shipment_id}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2">Contract_no</td>
                            <td class="px-2">:</td>
                            <td >
                                <input style="width:100%;" disabled  type="text" id="contract_no" name="contract_no" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="contract_no ..." v-model="vdata['contract_no']" >
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2">Shipment_type</td>
                            <td class="px-2">:</td>
                            <td >
                                <input style="width:100%;" disabled  type="text" id="shipment_type" name="shipment_type" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="shipment_type ..." v-model="vdata['shipment_type']" >
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2">Type</td>
                            <td class="px-2">:</td>
                            <td >
                                <input style="width:100%;" required  type="text" id="type" name="type" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="type ..." v-model="vdata['type']" >
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2">Contract_price</td>
                            <td class="px-2">:</td>
                            <td >
                                <input style="width:100%;" required  type="text" id="contract_price" name="contract_price" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="contract_price ..." v-model="vdata['contract_price']" >
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2">Quantity_contract</td>
                            <td class="px-2">:</td>
                            <td >
                                <input style="width:100%;" required  type="text" id="quantity_contract" name="quantity_contract" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="quantity_contract ..." v-model="vdata['quantity_contract']" >
                            </td>
                        </tr>
                    </table>
                    </div>
                    <div class="col-6">
                        <table>
                            <tr>
                                <td class="p-2">Date_final</td>
                                <td class="px-2">:</td>
                                <td >
                                    <input style="width:100%;" required  type="date" id="date_final" name="date_final" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="date_final ..." v-model="vdata['date_final']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Final_price</td>
                                <td class="px-2">:</td>
                                <td >
                                    <input style="width:100%;" required  type="text" id="final_price" name="final_price" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="final_price ..." v-model="vdata['final_price']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Final_quantity</td>
                                <td class="px-2">:</td>
                                <td >
                                    <input style="width:100%;" required  type="text" id="final_quantity" name="final_quantity" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="final_quantity ..." v-model="vdata['final_quantity']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Amount</td>
                                <td class="px-2">:</td>
                                <td >
                                    <input style="width:100%;" required  type="text" id="amount" name="amount" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="amount ..." v-model="vdata['amount']" >
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">Curr</td>
                                <td class="px-2">:</td>
                                <td >
                                    <select class='form-control' v-model="vdata.curr">
                                        <option v-for="(item, index) in FI_CUR_EXC" :key="index+'FI_CURR'" :value="item.TCURR">{{item.TCURR}}</option>
                                    </select>
                                    <!-- <input style="width:100%;" required  type="text" id="curr" name="curr" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="curr ..." v-model="vdata['curr']" > -->
                                </td>
                            </tr>
                        </table>
                    </div>
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
                <h5 class="card-title" >SALES PRICE</h5>
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
                            <input style="width:100%;" type="text" 
                            @change="page=1"
                            id="search" name="search" class="form-control p-2 text-xs rounded-lg " placeholder="search" v-model="search" >
                    </div>
                    <div class="col-sm-3">
                        <div class="text-center " >
                            <button type="button"  class="btn btn-sm btn-primary text-xs ml-3 "  @click="modals=!modals;showInsert=true;vdata={}">+ New Data</button>
                        </div>
                    </div>
                    <div class="col-sm-3">
                         <form v-if="datanya.length>0" action="<?= site_url() ?>/production/report/download?nama_file=T_SAL_PRICE" method="post">
                            <textarea :value="JSON.stringify(td)" v-show="false" type="text" id="datanya" name="datanya" rows="2" placeholder="datanya..." class="form-control md-textarea"  ></textarea>
                            <button type="submit" class="btn btn-sm btn-dark text-xs ">Excel <i class="ri-download-line"></i></button>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                           <th class="text-xs" style="background:lightgreen;" scope="col">
                                Shipment_id &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Contract_no &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Shipment_type &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Type &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Contract_price &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Quantity_contract &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Date_final &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Final_price &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Final_quantity &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Amount &#8593;&#8595;
                            </th><th class="text-xs" style="background:lightgreen;" scope="col">
                                Curr &#8593;&#8595;
                            </th>
                            <th style="background:lightgreen;" scope="col">
                                Action 
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                               <td class="text-xs">{{item.shipment_id}}</td>
                                <td class="text-xs">{{item.contract_no}}</td>
                                <td class="text-xs">{{item.shipment_type}}</td>
                                <td class="text-xs">{{item.type}}</td>
                                <td class="text-xs">{{item.contract_price}}</td>
                                <td class="text-xs">{{item.quantity_contract}}</td>
                                <td class="text-xs">{{item.date_final}}</td>
                                <td class="text-xs">{{item.final_price}}</td>
                                <td class="text-xs">{{item.final_quantity}}</td>
                                <td class="text-xs">{{item.amount}}</td>
                                <td class="text-xs">{{item.curr}}</td>
                                <td >
                                    <div v-if="disableInput[index]">
                                        <button type="button" @click="showUpdate(item)" class="btn btn-sm  btn-success text-xs tips">&#9999;
                                            <span class="tipstextB">Edit</span>
                                        </button>
                                        <button type="button" @click="deleteData(item)" class="btn btn-sm  btn-danger  text-xs tips">&#10005;
                                            <span class="tipstextB">Delete</span>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark text-xs ml-1" @click="disableInput[index]=!disableInput[index];$forceUpdate()">&#9779;</button>
                                    </div>
                                    <div v-else>
                                        <button type="button" class="btn btn-sm btn-dark text-xs" @click="disableInput[index]=!disableInput[index];showInsert=false;$forceUpdate()">Edit &#9779;</button>
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
        el:"#TSalPrice",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                T_SAL_SHIPMENT:<?= json_encode($T_SAL_SHIPMENT) ?>,
                FI_CUR_EXC:<?= json_encode($FI_CUR_EXC) ?>,
                disableInput:[],
                sortTable:["curr"], // disusun berdasarkan urutan td td td
                modals:false,
                showInsert:false,
                // CUSTOM
                datanya:[],
                vdata:{},
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
            }
        },  
        methods: {
            getContract(){
                this.vdata.contract_no=this.T_SAL_SHIPMENT.filter(e=>e.shipment_id==this.vdata.shipment_id)[0].contract_no
                this.vdata.shipment_type=this.T_SAL_SHIPMENT.filter(e=>e.shipment_id==this.vdata.shipment_id)[0].type
                this.$forceUpdate();
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
                axios.post("<?= site_url() ?>" + `/api/t-sal-price`,this.vdata).then(res=>{
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
            },
            async updateData(){
                if(!this.validateRequired())return;
                sdb.loadingOn();
                let id=this.vdata.id;
                delete this.vdata.id;
                
                axios.put("<?= site_url() ?>" + `/api/t-sal-price/${id}`,this.vdata).then(res=>{
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
                axios.delete("<?= site_url() ?>" + `/api/t-sal-price/${data.id}`).then(res=>{
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
                this.datanya=[]
                let data = await axios.get("<?= site_url() ?>" + `/api/get/t-sal-price`);
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
            document.getElementById('TSalPrice').classList.remove('d-none');
            this.$forceUpdate();
        },
    })
</script>
<?= $this->endSection() ?>
