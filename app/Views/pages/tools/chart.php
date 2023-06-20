<?= $this->extend('templates/layout') ?>
<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<main id="main" class="main">
    <div id="generatorci4" class="d-none">
        <h5 class="card-title" >Tools</h5>
        <hr class="my-2">
        <div class="row justify-content-center">
            <div class="col-sm-4">
                <div class="sm-form ">
                    <label for="table">Source Table</label>
                    <input type="text" id="table" name="table" class="form-control " placeholder="table" v-model="vdata['table']" >
                </div>
                <div class="sm-form ">
                    <label for="title">Chart Title</label>
                    <input type="text" id="title" name="title" class="form-control " placeholder="title" v-model="vdata['title']" >
                </div>
                <div class="sm-form ">
                    <label for="type">Chart type</label>
                    <select class='form-control' v-model="vdata['chart']">
                        <option>Bar</option>
                        <option>Line</option>
                        <option>Radar</option>
                        <option>Polar Area</option>
                        <option>Pie</option>
                        <option>Doughnut</option>
                    </select>
                </div>
                <div class="sm-form" >
                    <label for="field1">Field Perhitungan</label>
                    <input type="text" id="field1" name="field1" class="form-control p-2 rounded-lg shadow" placeholder="field1" v-model="vdata['field1']" >
                </div>
                <hr class="my-2">
                <p class="text-sm font-bold">List Fields Perhitungan : </p>
                <div v-for="(item, index) in vdata2" :key="index+'vdata2'">
                    <div class="shadow rounded-sm p-3 my-2">
                        <button type="button" @click="vdata2.splice(index,1)" class="btn btn-sm btn-danger float-right ">x</button>
                        <table>
                            <tr>
                                <td class="text-xs">Field</td>
                                <td class="px-2">:</td>
                                <td class="text-xs">
                                    <input type="text" :id="item.field" :name="item.field" class="form-control text-xs" placeholder="field" v-model="vdata2[index]['field']" >
                                </td>
                            </tr>
                            <!-- <tr>
                                <td class="text-xs">Type</td>
                                <td class="px-2">:</td>
                                <td class="text-xs">
                                    <select class='form-control' v-model="vdata2[index]['typedata']">
                                        <option>INT</option>
                                        <option>CHAR</option>
                                        <option>VARCHAR</option>
                                        <option>TEXT</option>
                                        <option>LONGTEXT</option>
                                        <option>DATE</option>
                                        <option>DATETIME</option>
                                        <option>FLOAT</option>
                                        <option>DECIMAL</option>
                                    </select>
                                </td>
                            </tr> -->
                        </table>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary btn-block my-3" @click="vdata2.push({})">+ field</button>
                <button type="button" @click="generateCode1(vdata2)" class="btn btn-sm btn-dark btn-block">Generate Code</button>
            </div>
            <div class="col-sm-8">
                <div class="float-right">
                    <button type="button" @click="thisTable.pop()" class="btn btn-sm btn-danger ml-2">-</button>
                    <button type="button" @click="tambahRelasi" class="btn btn-sm btn-primary ml-2 ">+</button>
                </div>
                <p class="mt-2 text-sm font-bold">Relationship ?</p>
                <div class="row" v-for="(item, index) in thisTable.length" :key="index+'thisTable'">
                    <div class="col-sm-12 mt-2">
                        <p class="text-xs font-semibold">Relationship No {{index+1}}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-xs font-semibold m-0 p-0">From Table : </p>
                        <input type="text" id="thisTable" name="thisTable" class="form-control text-xs" :placeholder="`Nama Table ${index+1}`" v-model="thisTable[index]" >
                        <input type="text" id="thisTableId" name="thisTableId" class="mt-2 form-control text-xs" :placeholder="`Nama Field ID ${index+1}`" v-model="thisTableId[index]" >
                    </div>
                    <div class="col-sm-6">
                        <p class="text-xs font-semibold m-0 p-0">With Table : </p>
                        <input type="text" id="withTable" name="withTable" class="form-control text-xs" :placeholder="`Nama Relasi Table ${index+1}`" v-model="withTable[index]" >
                        <input type="text" id="withTableId" name="withTableId" class="mt-2 form-control text-xs" :placeholder="`Nama Relasi ID ${index+1}`" v-model="withTableId[index]" >
                    </div>
                </div>
                <button type="button"  class="btn btn-sm btn-dark btn-block my-3" @click="generateCode1(false)">
                    Get From Table n Generate Code
                </button>
                <hr class="my-2">
                <button type="button" @click="copy('model')" class="btn btn-sm btn-primary float-right ">Copy</button>
                <p class="font-semibold text-lg">Model {{tableCapitalize}}.php</p>
                <textarea type="text" id="model" name="model" rows="2" placeholder="model..." class="form-control md-textarea" v-model="model" ></textarea>
                
            </div>
        </div>
    </div>
</main><!-- End #main -->
<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    new Vue({
        el:"#generatorci4",
        data(){
            return{
                model:'\<\?php',
                sqlAlter:'',
                crud:'',
                // CUSTOM
                subFolder:'',
                datanya:[],
                datanya2:[],
                thisTable:[],
                thisTableId:[],
                withTable:[],
                withTableId:[],
                tableCapitalize:'',
                vdata:{
                    idnya:'id',
                    table:"im_stock_raw",
                    title:'Data Pengeluaran Tahunan',
                    autoincrement:'true'
                },
                vdata2:[{}]
            }
        },
        computed:{
            sql(){
                let sql=`SELECT ${this.vdata.perhitungan}(tb1.${this.vdata.field1}) as total from ${this.vdata.table} tb1 , 
                ${this.vdata.type=='yearly'?`MONTH(tb1.created_at) as bulan`:this.vdata.type=='monthly'?`DAY(tb1.created_at) as day`:`HOUR(MONTH(tb1.created_at)) as jam`}
                where 
                ${this.vdata.type=='yearly'?`MONTH(tb1.created_at) as bulan`:this.vdata.type=='monthly'?`DAY(tb1.created_at) as day`:`HOUR(MONTH(tb1.created_at)) as jam`}
                tb1.created_at BETWEEN '$dari_tanggal' AND '$sampai_tanggal' GROUP BY bulan
                `;
                return sql;
            }
        },
        mounted() {
            document.getElementById('generatorci4').classList.remove('d-none');
            this.$forceUpdate();
        },
        methods: {
            tambahRelasi(){
                this.thisTable.push(this.vdata.table);
                this.thisTableId.push(this.vdata.idnya);
                this.$forceUpdate()
            },
            copy(id){
                sdb.alert('copy to clipboard '+id,'bg-green-400');
                let textarea=document.getElementById(id);
                textarea.select()
                textarea.setSelectionRange(0, 99999);
                document.execCommand('copy');
                document.body.removeChild(textarea);
            },
            formatTgl(tgl,pattern="YYYY-MM-DD") {
                return dateFns.format(
                    new Date(tgl),
                    pattern
                );
            },
            async generateCode1(val=false){
                if(!val){
                    if(this.vdata.table==''){
                        sdb.alert('Table tidak boleh kosong!');
                        return;
                    }
                    let res = await axios.get("<?= site_url() ?>/api/get/table?table="+this.vdata.table);
                    val = [];
                    res.data.forEach(e=>{
                        val.push(e)
                    })
                    val=val.filter(e=>{
                        if(
                            e['COLUMN_NAME']!='id'&&
                            e['COLUMN_NAME']!='created_at'&&e['COLUMN_NAME']!='updated_at'&&e['COLUMN_NAME']!='deleted_at'){
                            return e
                        }
                    })
                    val=val.map((e,index)=>{
                        return {
                            field:e['COLUMN_NAME'],
                            join:'',
                            pilihan:'',
                            relationship:'',
                            relatoinshipBelong:'',
                            required:'',
                            size:e['CHARACTER_MAXIMUM_LENGTH']?e['CHARACTER_MAXIMUM_LENGTH']:e['NUMERIC_PRECISION'],
                            status:'new',
                            typedata:e['DATA_TYPE'].toUpperCase(),
                            typeinput:'Text',
                            urutan:index+2
                        }
                    })
                    this.vdata2=val;
                }
                let tableCapitalize=this.vdata.table.split('_').join(' ').toLowerCase().replace(/(^\w{1})|(\s+\w{1})/g, letter => letter.toUpperCase()).replaceAll(' ','');
                this.tableCapitalize=tableCapitalize;
                // let res = await axios.get(`<?= site_url() ?>generate/generateFolder.php?folder=${this.subFolder==''?'':`${this.subFolder}`}`);
                this.fields=val;
                console.log(val)
                // this.getModel(val,tableCapitalize);
                // this.getMigration(val,tableCapitalize);
                // this.getController(val,tableCapitalize);
                // this.getController2(val,tableCapitalize);
                // this.getRoute(val,tableCapitalize);
                // this.getRoute2(val,tableCapitalize);
                // this.getCRUD(val,tableCapitalize);
                // this.getAlter(val,tableCapitalize);
                this.$forceUpdate();
            },
            
            // ====================== MODEL ===============================
            async generateChart(){
                let sql=``;
                if(this.vdata.type=='yearly'){
                    sql+=`select `;
                }else if(this.vdata.type=='monthly'){
                    
                }else if(this.vdata.type=='dayly'){

                }
            },
            async getModel(data,tableCapitalize){
                let fields=``;
data.forEach(e=>{
    fields+=`"${e.field}",`;
})
fields=fields.slice(0, -1);
this.model+=`
namespace App\\Models${this.subFolder==''?'':`\\${this.subFolder}`};

use CodeIgniter\\I18n\\Time;
use CodeIgniter\\Model;

class ${tableCapitalize} extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = '${this.vdata.table}';
    protected $primaryKey       = '${this.vdata.idnya}';
    protected $useAutoIncrement = ${this.vdata.autoincrement=='true'?true:false};
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [${fields}];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
`;
                let fd = new FormData();
                fd.append('data',this.model);
                fd.append('namaFile',tableCapitalize);
                fd.append('folder',`Models/${this.subFolder==''?'':`${this.subFolder}/`}`);
                if("<?= site_url() ?>".indexOf('localhost')!=-1){
                    let res = await axios.post(`<?= site_url() ?>generate/createFile.php`,fd);
                }
                this.$forceUpdate();
            },
        }
})
</script>
<?= $this->endSection() ?>