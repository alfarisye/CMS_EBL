<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<!-- SCRIPT -->


<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.3/xlsx.full.min.js"></script>

<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:100000;background:#000;opacity:0.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:100005;min-width:50vw;min-height:50vh;max-height:95vh;}
</style>

<main id="main" class="main">
    <div id="qualityReportPage" class="d-none">
        <div class="mb-15">
            <div class="row">
                <div class="col-12">
                    <img src="<?= base_url("assets/img/ebl.png") ?>"  style="width:120px;height:auto;" class="float-left">
                    <h1 class="pt-8 ml-2">Upload Quality</h1>
                </div>
            </div>
            <div class="pagetitle">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url("quality-report") ?>">Quality Report</a></li>
                        <li class="breadcrumb-item active"><a href="<?= site_url("quality-report/upload") ?>">Upload</a></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row py-2" >
                    <div class="col-sm-12">
                        <p class="font-semibold">Upload File (Xls): </p>
                        <form action="" @submit.prevent="uploadData">
                            <div class="row">
                                <div class="col-sm-4">
                                    <input required type="file" 
                                    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                    id="file1" name="file1" class="form-control p-1 text-xs rounded-sm shadow-sm" placeholder="file1" >
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" @click="cekDataExcel"  class="btn btn-sm btn-style2 " >Proses Upload</button>
                                </div>
                                <div class="offset-sm-4 col-sm-1">
                                    <button type="submit"  class="btn btn-sm btn-style2 " >Post</button>
                                </div>
                                <div class="col-sm-1">
                                    <a href="<?= site_url("/quality-report")  ?>">
                                        <button type="button" @click="hapusDataExcel" class="btn btn-sm btn-style2 " >Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-12">
                        <hr>
                        <div class="table-responsive" v-if="tableReview && uploadFile">
                            <table class="table table-bordered ">
                                <tr>
                                    <th scope="col" class="text-xs">
                                        No &#8593;&#8595;
                                    </th>
                                    <th scope="col" class="text-xs" v-for="(item, index) in keys" :key="index+'keys'">
                                        {{item}}
                                    </th>
                                </tr>
                                <tbody v-if="dataReview.length>0">
                                    <tr v-for="(item, index) in dataReview" :key="index+'dataReview'" >
                                        <td>{{index+1}}</td>
                                        <td class="text-xs" v-for="(item2, index2) in keys" :key="index+'keys'">
                                            {{item[item2]}}                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 " v-if="!tableReview">
                            <p class="font-semibold">Total Upload Success : {{totalSuccess}}</p>
                            <p class="font-semibold">Total Upload Error : {{totalError}}</p>
                        </div>
                        <div class="table-responsive" v-if="!tableReview">
                            <table class="table table-bordered ">
                                <tr>
                                    <th scope="col" class="text-xs">
                                        No &#8593;&#8595;
                                    </th>
                                    <th scope="col" class="text-xs">
                                        Kode Sample &#8593;&#8595;
                                    </th>
                                    <th scope="col" class="text-xs">
                                        Tanggal Mulai &#8593;&#8595;
                                    </th>
                                    <th scope="col ">
                                        Tanggal Akhir &#8593;&#8595;
                                    </th>
                                    <th scope="col" class="text-xs">
                                        Kategori Sample &#8593;&#8595;
                                    </th>
                                    <th scope="col" class="text-xs">
                                        Status Progress &#8593;&#8595;
                                    </th>
                                    <th scope="col" class="text-xs">
                                        TM (%) arb
                                    </th>
                                    <th scope="col" class="text-xs">
                                        M (%) adb
                                    </th>
                                    <th scope="col" class="text-xs">
                                        Ash (%) adb
                                    </th>
                                    <th scope="col" class="text-xs">
                                        VM (%) adb
                                    </th>
                                    <th scope="col" class="text-xs">
                                        FC (%) adb
                                    </th>
                                    <th scope="col" class="text-xs">
                                        TS (%) adb
                                    </th>
                                    <th scope="col" class="text-xs">
                                        CV (%) adb
                                    </th>
                                    <th scope="col" class="text-xs">
                                        CV (%) arb
                                    </th>
                                    <th scope="col" class="text-xs">
                                        CV (%) daf
                                    </th>
                                    <th scope="col" class="text-xs">
                                        CV (%) db
                                    </th>
                                    <th scope="col" class="text-xs">
                                        Status Upload
                                    </th>
                                    <th scope="col" class="text-xs">
                                        Message Upload
                                    </th>
                                    
                                </tr>
                                <tbody v-if="datanya.length>0">
                                    <tr v-for="(item, index) in td" :key="index" :class="item.status_upload==false?'bg-red-400':'bg-green-400'">
                                        <td>{{index+1}}</td>
                                        <td class="text-xs" >
                                            {{item.sample_id}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.tgl_mulai}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.tgl_akhir}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.kategori}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.status_progress}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.TM_arb}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.M_adb}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.Ash_adb}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.VM_adb}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.FC_adb}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.TS_adb}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.CV_adb}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.CV_arb}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.CV_daf}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.CV_db}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.status_upload}}
                                        </td>
                                        <td class="text-xs" >
                                            {{item.message}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
    new Vue({
        el:"#qualityReportPage",
        data(){
            return{
                perPage:10000,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                tableReview:true,
                uploadFile:false,
                sortTable:[null,'id','sample_id','tgl_awal','tgl_akhir',"kategori","TM_arb","M_adb","Ash_adb","VM_adb","FC_adb","TS_adb","CV_adb","CV_arb","CV_daf","CV_db"], // disusun berdasarkan urutan td td td
                modal:false,
                showInsert:false,
                // CUSTOM
                tgl_awal:'',
                tgl_akhir:'',
                keys:[],
                aksi:'insert',
                datanya:[],
                dataReview:[],
                totalError:0,
                totalSuccess:0,
                vdata:{},
                option:{
                    headers:{
                        'X-Requested-With': 'XMLHttpRequest',
                        'contentType': "application/json",
                    }
                }
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
            cekDataExcel(){
                var files = document.getElementById('file1').files;
                this.uploadFile=true;
                this.$forceUpdate();
                if(files.length==0){
                    alert("Please choose any file...");
                    return;
                }
                var filename = files[0].name;
                var extension = filename.substring(filename.lastIndexOf(".")).toUpperCase();
                if (extension == '.XLS' || extension == '.XLSX') {
                    this.excelFileToJSON(files[0]);
                }else{
                    alert("Please select a valid excel file.");
                }
            },
            excelFileToJSON(file){
                let that=this;
                try {
                var reader = new FileReader();
                reader.readAsBinaryString(file);
                reader.onload = function(e) {
            
                    var data = e.target.result;
                    var workbook = XLSX.read(data, {
                        type : 'binary',
                        cellDates: true
                    });
                    var result = {};
                    console.log('workbook',workbook)
                    workbook.SheetNames.forEach(function(sheetName) {
                        var roa = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                        if (roa.length > 0) {
                            result[sheetName] = roa;
                        }
                    });
                    //displaying the json result
                    console.log(result)
                    let dataJson=JSON.parse(JSON.stringify(result, null, 4));
                    result=result[Object.keys(result)[0]]
                    let keys=[]
                    result.forEach(e=>{
                        Object.keys(e).forEach(k=>{
                           keys.push(k) 
                        })
                    })
                    let unique = [...new Set(keys)];
                    // console.log(unique)
                    that.keys=unique;
                    that.dataReview=result
                    that.$forceUpdate();
                    }
                }catch(e){
                    console.error(e);
                }
            },
            hapusDataExcel(){
                let file = document.querySelector("#file1"); // berikan id pada input file
                delete file.files[0]
            },
            async uploadData(){
                let file = document.querySelector("#file1"); // berikan id pada input file
                if(!(file.files[0].name.indexOf('.xls')!=-1 || file.files[0].name.indexOf('.xlsx')!=-1)){
                    sdb.alert('Error wrong format file!');
                    return false;
                }
                if(!confirm('Are you sure to upload this file ? '))return;
                sdb.loadingOn();
                this.tableReview=false;
                let fd = new FormData();
                fd.append('file',file.files[0]);
                await axios.post("<?= site_url() ?>" + `/api/upload/quality-report`,fd).then(res=>{
                    sdb.loadingOff();
                    sdb.alert('Data Berhasil di upload !','bg-green-400');
                    this.getData(res.data);
                    // this.$forceUpdate();
                });
            },
            showField(vmodel,e){
                this.modal=true;
                this.target.vmodel=vmodel;
                this.target.key=e.target.name;
                this.$forceUpdate();
            },
            async submit(){
                if(this.aksi=='update'){
                    this.updateData(this.vdata);
                }else{
                    this.deleteData(this.vdata);
                }
            },
            async getData(data){
                this.datanya=data;
                this.totalError=data.filter(e=>e.status_upload==false).length
                this.totalSuccess=data.filter(e=>e.status_upload==true).length
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
            format(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY-MM-DD"
                );
            },
           
        },
        mounted() {
            // this.getData();
            this.tgl_awal=this.format(new Date());
            this.tgl_akhir=this.format(new Date());
            setTimeout(() => {
                // this.getData();
            }, 1000);
            document.getElementById('qualityReportPage').classList.remove('d-none');
            document.getElementById('pageloading').remove()
        },
    })
</script>

<?= $this->endSection() ?>