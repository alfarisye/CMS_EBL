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
                    <label for="table">Sub Folder</label>
                    <input type="text" id="subFolder" name="subFolder" class="form-control " placeholder="subFolder" v-model="subFolder" >
                </div>
                <div class="sm-form ">
                    <label for="table">Nama Table</label>
                    <input type="text" id="table" name="table" class="form-control " placeholder="table" v-model="vdata['table']" >
                </div>
                <div class="sm-form ">
                    <label for="idnya">ID </label>
                    <input type="text" id="idnya" name="idnya" class="form-control " placeholder="idnya" v-model="vdata['idnya']" >
                </div>
                <p class="mt-2 text-xs">Auto Increment ?</p>
                <input type="radio" id="one" value="true" v-model="vdata.autoincrement">
                <label for="one">True</label>                
                <input class="ml-2" type="radio" id="two" value="false" v-model="vdata.autoincrement">
                <label for="two">False</label>
                <br>
                <hr class="my-2">
                <p class="text-sm font-bold">List Fields Table : </p>
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
                            <tr>
                                <td class="text-xs">Size</td>
                                <td class="px-2">:</td>
                                <td class="text-xs">
                                    <input type="text" :id="item.field+item.index" :name="item.field+item.index" class="form-control text-xs" placeholder="size" v-model="vdata2[index]['size']" >
                                </td>
                            </tr>
                            <tr>
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
                            </tr>
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
                <!--  -->
                <hr class="my-2">
                <button type="button" @click="copy('migration')" class="btn btn-sm btn-primary float-right ">Copy</button>
                <p class="font-semibold text-lg">Migration {{`${formatTgl(new Date(),"YYYY-MM-DD-HHmmss")}_`+tableCapitalize}}.php</p>
                <textarea type="text" id="migration" name="migration" rows="2" placeholder="migration..." class="form-control md-textarea" v-model="migration" ></textarea>
                <!--  -->
                <hr class="my-2">
                <button type="button" @click="copy('controller')" class="btn btn-sm btn-primary float-right ">Copy</button>
                <p class="font-semibold text-lg">Controller {{tableCapitalize}}.php</p>
                <textarea type="text" id="controller" name="controller" rows="2" placeholder="controller..." class="form-control md-textarea" v-model="controller" ></textarea>
                <!--  -->
                <hr class="my-2">
                <button type="button" @click="copy('controller2')" class="btn btn-sm btn-primary float-right ">Copy</button>
                <p class="font-semibold text-lg">Controller {{tableCapitalize}}.php (Angga)</p>
                <textarea type="text" id="controller2" name="controller2" rows="2" placeholder="controller2..." class="form-control md-textarea" v-model="controller2" ></textarea>
                <!--  -->
                <hr class="my-2">
                <button type="button" @click="copy('route')" class="btn btn-sm btn-primary float-right ">Copy</button>
                <p class="font-semibold text-lg">Route</p>
                <textarea type="text" id="route" name="route" rows="2" placeholder="route..." class="form-control md-textarea" v-model="route" ></textarea>
                <!--  -->
                <hr class="my-2">
                <button type="button" @click="copy('route2')" class="btn btn-sm btn-primary float-right ">Copy</button>
                <p class="font-semibold text-lg">Route (Angga)</p>
                <textarea type="text" id="route2" name="route2" rows="2" placeholder="route2..." class="form-control md-textarea" v-model="route2" ></textarea>
                <hr class="my-2">
                 <!--  -->
                 <hr class="my-2">
                <button type="button" @click="copy('crud')" class="btn btn-sm btn-primary float-right ">Copy</button>
                <p class="font-semibold text-lg">CRUD {{vdata.table.toLowerCase()}}.php</p>
                <textarea type="text" id="crud" name="crud" rows="2" placeholder="crud..." class="form-control md-textarea" v-model="crud" ></textarea>
                <hr class="my-2">
                  <!--  -->
                  <hr class="my-2">
                <button type="button" @click="copy('sqlAlter')" class="btn btn-sm btn-primary float-right ">Copy</button>
                <p class="font-semibold text-lg">sql Alter</p>
                <textarea type="text" id="sqlAlter" name="sqlAlter" rows="2" placeholder="sqlAlter..." class="form-control md-textarea" v-model="sqlAlter" ></textarea>
                <hr class="my-2">
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
                controller:'\<\?php',
                controller2:'\<\?php',
                route:'',
                route2:'',
                migration:'\<\?php',
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
                    table:"T_SAL_MASTER_DMO",
                    autoincrement:'true'
                },
                vdata2:[{}]
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
                this.migration='\<\?php';
                this.model='\<\?php';
                this.controller='\<\?php';
                this.controller2='\<\?php';
                this.route='';
                this.route2='';
                let res = await axios.get(`<?= site_url() ?>generate/generateFolder.php?folder=${this.subFolder==''?'':`${this.subFolder}`}`);
                this.getModel(val,tableCapitalize);
                this.getMigration(val,tableCapitalize);
                this.getController(val,tableCapitalize);
                this.getController2(val,tableCapitalize);
                this.getRoute(val,tableCapitalize);
                this.getRoute2(val,tableCapitalize);
                this.getCRUD(val,tableCapitalize);
                this.getAlter(val,tableCapitalize);
                this.$forceUpdate();
            },
            
            // ====================== MODEL ===============================
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

            // ====================== #MIGRATION ===============================
            async getMigration(data,tableCapitalize){
                let fields=``;
data.forEach(e=>{
     fields+=`"${e.field}" => [
                "type" => "${e.typedata=='FLOAT'?'DECIMAL':e.typedata}",
                ${e.size!=null?`"contraint" =>"${e.size}${e.typedata=='FLOAT'?",2":""}",`:``}
                "null" => true,
            ],
            `;
})
fields=fields.slice(0, -1);
let relationship=``;
this.thisTable.forEach((e,i)=>{
    relationship+=`
        $this->forge->addForeignKey("${this.thisTableId[i]}", "${this.withTable[i]}", "${this.withTableId[i]}");`;
})
this.migration+=`
namespace App\\Database\\Migrations;

use CodeIgniter\\Database\\Migration;

class ${tableCapitalize} extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "${this.vdata.idnya}" => [
                "type" => "INT",
                "constraint" => 10,
                ${this.vdata.autoincrement=='true'?`"auto_increment" => true,`:``}
            ],
            ${fields}
        ]);
        $this->forge->addKey("${this.vdata.idnya}", true);
        ${relationship}
        $this->forge->createTable("${this.vdata.table}");
    }

    public function down()
    {
        $this->forge->dropTable('${this.vdata.table}');
    }
}

`;
                let fd = new FormData();
                fd.append('data',this.migration);
                fd.append('namaFile',`${this.formatTgl(new Date(),"YYYY-MM-DD-HHmmss")}_`+tableCapitalize);
                fd.append('folder',`Database/Migrations/`);
                if("<?= site_url() ?>".indexOf('localhost')!=-1){
                    let res = await axios.post(`<?= site_url() ?>generate/createFile.php`,fd);
                }
                this.$forceUpdate();
            },

             // ====================== #CONTROLLER ===============================
             async getController(data,tableCapitalize){
this.controller+=`
namespace App\\Controllers${this.subFolder==''?'':`\\${this.subFolder}`};

use App\\Controllers\\BaseController;
use App\\Models\\${this.subFolder==''?'':`${this.subFolder}\\`}${tableCapitalize};
use App\\Models\\GLogs;
use CodeIgniter\\I18n\\Time;
use CodeIgniter\\API\\ResponseTrait;
use CodeIgniter\\Files\\File;
use CodeIgniter\\HTTP\\Files\\UploadedFile;

class ${tableCapitalize}s extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function ${this.vdata.table.toLowerCase()}()
    {
        $data['title'] = "${this.vdata.table.split('_').join(' ')}";
        echo view('pages/${this.subFolder==''?'':`${this.subFolder}/`}${this.vdata.table.toLowerCase()}', $data);
    }

    public function get_${this.vdata.table.toLowerCase()}(){
        $db = \\Config\\Database::connect();
        $query = $db->query("select * from ${this.vdata.table} where deleted_at=''");
        return $this->respond($query->getResult(), 200);
    }
    
    public function ${this.vdata.table.toLowerCase()}_insert(){
        $data=$this->request->getJSON();
        $${tableCapitalize} = new ${tableCapitalize}();
        $${tableCapitalize}->save($data);
        $this->GLogs->after_insert('${this.vdata.table}');
        return $this->respond($data, 200);
    }

    public function ${this.vdata.table.toLowerCase()}_update($id)
    {
        $data=$this->request->getJSON();
        $${tableCapitalize} = new ${tableCapitalize}();
        $${tableCapitalize}->find($id);
        $this->GLogs->before_update($id,'${this.vdata.table}','${this.vdata.idnya}');
        $${tableCapitalize}->update($id, $data);
        $this->GLogs->after_update($id,'${this.vdata.table}','${this.vdata.idnya}');
        return $this->respond($${tableCapitalize}, 200);
    }

    public function ${this.vdata.table.toLowerCase()}_delete($id)
    {
        $this->GLogs->before_delete($id,'${this.vdata.table}','${this.vdata.idnya}');
        $db = \\Config\\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("update ${this.vdata.table} set deleted_at='$date' where ${this.vdata.idnya}='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
`;
                let fd = new FormData();
                fd.append('data',this.controller);
                fd.append('namaFile',tableCapitalize+'s');
                fd.append('folder',`Controllers/${this.subFolder==''?'':`${this.subFolder}/`}`);
                if("<?= site_url() ?>".indexOf('localhost')!=-1){
                    let res = await axios.post(`<?= site_url() ?>generate/createFile.php`,fd);
                }
                this.$forceUpdate();
            },

             // ====================== #CONTROLLER2 Angga ===============================
             async getController2(data,tableCapitalize){
                let fields=``;
                let fields2=``;
data.forEach(e=>{
        fields+=`$${e.field} = $this->request->getVar('${e.field}');
        `;
        fields2+=`'${e.field}' => $${e.field},
        `;
})
// fields=fields.slice(0, -1);
fields2=fields2.slice(0, -1);
this.controller2+=`
namespace App\\Controllers${this.subFolder==''?'':`\\${this.subFolder}`};
use App\\Controllers\\BaseController;
use App\\Models\\${this.subFolder==''?'':`${this.subFolder}\\`}${tableCapitalize} as ${tableCapitalize}Model;

class ${tableCapitalize}s extends BaseController
{
    public function index()
    {
        $data['title'] = "${this.vdata.table.split('_').join(' ')}";

        $${tableCapitalize} = new ${tableCapitalize}Model();
        $data['${this.vdata.table.toLowerCase()}'] = $${tableCapitalize}->where("status", 1)->findAll();
        echo view('pages/${this.vdata.table.toLowerCase()}', $data);
    }

    public function add()
    {
        ${fields}
        $${tableCapitalize} = new ${tableCapitalize}Model();
        $${tableCapitalize}->save([
            ${fields2}
        ]);
      
        return redirect()->to("/${this.vdata.table.toLowerCase()}")->with('message', "${this.vdata.table.split('_').join(' ')} berhasil ditambahkan");
    }

    public function delete($id)
    {
        $${tableCapitalize} = new ${tableCapitalize}Model();
        $${tableCapitalize}->update($id, [
            'status' => false
        ]);

        return redirect()->to("/${this.vdata.table.toLowerCase().split('_').join('-')}")->with('message', "${this.vdata.table.split('_').join(' ')} berhasil dihapus");
    }

    public function edit($id)
    {
        $data['title'] = "Edit ${this.vdata.table.split('_').join(' ')}";
        $${tableCapitalize} = new ${tableCapitalize}Model();
        $data['${this.vdata.table.toLowerCase()}'] = $${tableCapitalize}->where("${this.vdata.idnya}", $id)->first();

        echo view('pages/${this.vdata.table.toLowerCase().split('_').join('-')}-edit', $data);
    }

    public function update()
    {
        ${fields}
        $${tableCapitalize} = new ${tableCapitalize}Model();

        $${tableCapitalize}->save([
            ${fields2}
        ]);
       
        return redirect()->back()->with('message', "${this.vdata.table.split('_').join(' ')} berhasil diubah");
    }
}
`;
                // let fd = new FormData();
                // fd.append('data',this.controller2);
                // fd.append('namaFile',tableCapitalize+'s');
                // fd.append('folder',`Controllers/${this.subFolder==''?'':`${this.subFolder}/`}`);
                // if("<?= site_url() ?>".indexOf('localhost')!=-1){
                //     let res = await axios.post(`<?= site_url() ?>generate/createFile.php`,fd);
                // }
                this.$forceUpdate();
            },

             // ====================== #ROUTE ===============================
             async getRoute(data,tableCapitalize){
let tableFirst=this.vdata.table.charAt(0).toUpperCase() + this.vdata.table.slice(1);
this.route+=`
$routes->get('${this.subFolder==''?'':`${this.subFolder}/`}${this.vdata.table.toLowerCase().split('_').join('-')}', '${this.subFolder==''?'':`${this.subFolder}\\`}${tableCapitalize}s::${this.vdata.table.toLowerCase()}', ['filter' => 'auth']);//GET
$routes->get('/api/get/${this.vdata.table.toLowerCase().split('_').join('-')}', '${this.subFolder==''?'':`${this.subFolder}\\`}${tableCapitalize}s::get_${this.vdata.table.toLowerCase()}');//GET
$routes->post('/api/${this.vdata.table.toLowerCase().split('_').join('-')}', '${this.subFolder==''?'':`${this.subFolder}\\`}${tableCapitalize}s::${this.vdata.table.toLowerCase()}_insert');//GET
$routes->put('api/${this.vdata.table.toLowerCase().split('_').join('-')}/(:any)', '${this.subFolder==''?'':`${this.subFolder}\\`}${tableCapitalize}s::${this.vdata.table.toLowerCase()}_update/$1');
$routes->delete('api/${this.vdata.table.toLowerCase().split('_').join('-')}/(:any)', '${this.subFolder==''?'':`${this.subFolder}\\`}${tableCapitalize}s::${this.vdata.table.toLowerCase()}_delete/$1');
`;
                this.$forceUpdate();
            },

              // ====================== #ROUTE2 Angga ===============================
              async getRoute2(data,tableCapitalize){
let tableFirst=this.vdata.table.charAt(0).toUpperCase() + this.vdata.table.slice(1);
this.route2+=`
$routes->group('${this.vdata.table.toLowerCase().split('_').join('-')}', ['filter' => 'auth'], function($routes) {
    $routes->get('/', '${tableCapitalize}s::index');
    $routes->get('dashboard', '${tableCapitalize}s::dashboard');
    $routes->post('add', '${tableCapitalize}s::add');
    $routes->get('delete/(:any)', '${tableCapitalize}s::delete/$1');
    $routes->get('edit/(:any)', '${tableCapitalize}s::edit/$1');
    $routes->post('update', '${tableCapitalize}s::update');
});
`;
                this.$forceUpdate();
            },  

            // =======================================================================
            // ======================= CRUD =============================
            // =======================================================================
        async getCRUD(data,tableCapitalize){
                let fields=``;
                let th=``;
                let td=``;
                let sortField=``;
data.forEach(e=>{
    if(e.field!='created_at' && e.field!='updated_at' && e.field!='deleted_at' && e.field!='created_by' && e.field!='updated_by' && e.field!='deleted_by'){
        fields+=`<tr>
                    <td class="p-2">${e.field.charAt(0).toUpperCase() + e.field.slice(1)}</td>
                    <td class="px-2">:</td>
                    <td>
                        <input required  type="text" id="${e.field}" name="${e.field}" style="width:60%;" class="form-control p-1  rounded-sm shadow-sm" placeholder="${e.field} ..." v-model="vdata['${e.field}']" >
                    </td>
                </tr>
        `;
        th+=`<th class="text-xs" style="background:lightgreen;" scope="col">
                ${e.field.charAt(0).toUpperCase() + e.field.slice(1)} &#8593;&#8595;
            </th>`;
        td+=`<td class="text-xs">{{item.${e.field}}}</td>
        `;
        sortField=`"${e.field}",`;
    }
})
sortField=sortField.slice(0, -1);
this.crud+=`
\<\?= $this->extend('templates/layout') ?>
\<\?= $this->section('main') ?>
<link href="\<\?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:1000;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:1005;min-width:50vw}
</style>
<main id="main" class="main">
    <div id="${tableCapitalize}" class="d-none">
        <div class="pagetitle">
            <h1>${this.vdata.table.split('_').join(' ')}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="\<\?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item active"><a href="\<\?= site_url("${this.vdata.subFolder}/${this.vdata.table.toLowerCase().split('_').join('-')}") ?>">${this.vdata.table.split('_').join(' ')}</a></li>
                </ol>
            </nav>
        </div>
        <div v-if="modals" @click="modals=false" class="modal1"></div>
        <div v-if="modals" class="modal2">
            <div class="rounded-lg shadow p-3 bg-white animate__animated animate__bounceIn">
               <form action="" @submit.prevent='showInsert?insertData():updateData()'>
                <table>
                    ${fields}
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
                <h5 class="card-title" >${this.vdata.table.split('_').join(' ')}</h5>
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
                    <div class="col-sm-3">
                         <form v-if="datanya.length>0" action="\<\?= site_url() ?>/production/report/download?nama_file=${this.vdata.table}" method="post">
                            <textarea :value="JSON.stringify(td)" v-show="false" type="text" id="datanya" name="datanya" rows="2" placeholder="datanya..." class="form-control md-textarea"  ></textarea>
                            <button type="submit" class="btn btn-sm btn-dark text-xs ">Excel <i class="ri-download-line"></i></button>
                        </form>
                    </div>
                </div>
                <div class="table-responsive" >
                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                           ${th}
                            <th style="background:lightgreen;" scope="col">
                                Action 
                            </th>
                        </tr>
                        <tbody v-if="datanya.length>0">
                            <tr v-for="(item, index) in td" :key="index+'form'" >
                               ${td}
                                <td>
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
    import myplugin from '\<\?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    var pages="\<\?= @$_GET['page'] ?>"
    new Vue({
        el:"#${tableCapitalize}",
        data(){
            return{
                perPage:10,
                totalPage:0,
                page:1,
                search:'',
                target:{},
                disableInput:[],
                sortTable:[${sortField}], // disusun berdasarkan urutan td td td
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
            validateRequired(){ // validation manual select semua input required
                let validation=true;
                document.querySelectorAll('[required]').forEach(e=>{e.reportValidity()?'':validation=false});
                return validation;
            },
            async insertData(){
                if(!this.validateRequired())return;
                sdb.loadingOn();
                ${sortField.indexOf('created_by')!=-1?`this.vdata.created_by="\<\?php echo session()->get('username') ?>";`:''}
                let token=await axios.post("\<\?= site_url() ?>" + \`/api/test\`);
                this.vdata[Object.keys(token.data[0])[0]]=Object.values(token.data[0])[0];
                axios.post("\<\?= site_url() ?>" + \`/api/${this.vdata.table.toLowerCase().split('_').join('-')}\`,this.vdata).then(res=>{
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
                let id=this.vdata.${this.vdata.idnya};
                delete this.vdata.${this.vdata.idnya};
                ${sortField.indexOf('updated_by')!=-1?`this.vdata.updated_by="\<\?php echo session()->get('username') ?>";`:''}
                axios.put("\<\?= site_url() ?>" + \`/api/${this.vdata.table.toLowerCase().split('_').join('-')}/\${id}\`,this.vdata).then(res=>{
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
                axios.delete("\<\?= site_url() ?>" + \`/api/${this.vdata.table.toLowerCase().split('_').join('-')}/\${data.${this.vdata.idnya}}\`).then(res=>{
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
                let data = await axios.get("\<\?= site_url() ?>" + \`/api/get/${this.vdata.table.toLowerCase().split('_').join('-')}\`);
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
            document.getElementById('${tableCapitalize}').classList.remove('d-none');
            this.$forceUpdate();
        },
    })
\<\/script>
\<\?= $this->endSection() ?>
`;
                let fd = new FormData();
                fd.append('data',this.crud);
                fd.append('namaFile',this.vdata.table.toLowerCase());
                fd.append('folder',`Views/pages/${this.subFolder==''?'':`${this.subFolder}/`}`);
                if("<?= site_url() ?>".indexOf('localhost')!=-1){
                    let res = await axios.post(`<?= site_url() ?>generate/createFile.php`,fd);
                }
                this.$forceUpdate();
        },
        async getAlter(data,tableCapitalize){
                let sql=`ALTER TABLE ${this.vdata.table} `
                if(data.filter(e=>e.field.indexOf('created_at')==-1)){
                    sql+=`ADD COLUMN created_at VARCHAR(255) NOT NULL,`;
                }
                if(data.filter(e=>e.field.indexOf('updated_at')==-1)){
                    sql+=`ADD COLUMN updated_at VARCHAR(255) NOT NULL,`;
                }
                if(data.filter(e=>e.field.indexOf('deleted_at')==-1)){
                    sql+=`ADD COLUMN deleted_at VARCHAR(255) NOT NULL,`;
                }
                if(data.filter(e=>e.field.indexOf('created_by')==-1)){
                    sql+=`ADD COLUMN created_by VARCHAR(255) NOT NULL,`;
                }
                if(data.filter(e=>e.field.indexOf('updated_by')==-1)){
                    sql+=`ADD COLUMN updated_by VARCHAR(255) NOT NULL,`;
                }
                if(data.filter(e=>e.field.indexOf('deleted_by')==-1)){
                    sql+=`ADD COLUMN deleted_by VARCHAR(255) NOT NULL,`;
                }
                sql=sql.slice(0, -1);
                this.sqlAlter=sql;
                this.$forceUpdate();
        },
    },
})
</script>
<?= $this->endSection() ?>