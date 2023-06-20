<?= $this->extend('templates/layout') ?>

<?= $this->section('main') ?>
<link href="<?= base_url("assets/css/all.css") ?>" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="">
<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
<script src="https://unpkg.com/vue-select@3.0.0"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.27.2/axios.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
<style>
    table td{position:relative}
    table td textarea{position:absolute;top:0;left:0;margin:0;height:100%;width:100%;border:none;padding:10px;box-sizing:border-box;text-align:start}
    .border-hover{border:1px solid transparent}
    .border-hover:hover{border:1px solid #d0d0d0}
    .modal1{position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:100;background:#000;opacity:.5}
    .modal2{position:fixed;top:50%;left:50%;transform:translateX(-50%) translateY(-50%);z-index:105;min-width:50vw}
</style>
<main id="main" class="main">
    <div id="masterPetaEbl" class="d-none">
        <div class="pagetitle">
            <h1>PETA - BUKAAN LAHAN</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= site_url("/peta") ?>">Peta</a></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <!-- MODAL -->
                    <!-- <button type="button" @click="test" class="btn btn-sm btn-dark  ">test</button> -->
                    <div v-if="modal" @click="modal=!modal" style="position:fixed;z-index:10000000;width:100vw;height:100vh;top:0;left:0;background:black;opacity: 0.5;">
                    </div>
                    <div v-if="modal" style="position: fixed;top: 50%;left: 50%;transform: translateX(-50%) translateY(-50%);z-index:10000005;" class="animate__animated animate__fadeIn">
                        <div style="width:60vw;" class="p-4 bg-white shadow-sm rounded-lg">
                            <p class="font-semibold">Data Periode {{formatIndo(pilih[0].periode)}}</p>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <tr v-for="(item, index) in pilih" :key="index+'pilih'">
                                        <td>
                                            {{item.nama_type}} Blok {{item.nama_blok}} ({{item.deskripsi_blok}})
                                        </td>
                                        <td class="px-2">:</td>
                                        <td>
                                            {{item.data_produksi}}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL -->
                    <div class="pr-2">
                        <div class="row mt-2">
                            <div class="col-sm-3">
                                <p class="font-semibold text-xs">Periode :</p>
                                <select class='form-control text-xs' v-model="typePeriode">
                                    <option value="Bulan">Monthly</option>
                                    <option>Range</option>
                                </select>
                            </div>
                            <div class="col-sm-7" v-if="typePeriode=='Bulan'">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="font-semibold text-xs">Years :</p>
                                        <div class="sm-form ">
                                            <select class='form-control' v-model="tahun"  @change="page=1;">
                                                <option v-for="(item, index) in listTahun" :key="index+'listTahun'">{{item}}</option>
                                            </select>
                                            <!-- <input type="text" id="tahun" name="tahun" class="form-control p-1 rounded-sm shadow-sm" placeholder="tahun" v-model="tahun" @change="page=1;"> -->
                                            <!-- <input type="date" id="periode" name="periode" class="form-control p-1 rounded-sm shadow-sm" placeholder="periode" v-model="periode" @change="page=1;getForm()"> -->
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="font-semibold text-xs">Months :</p>
                                        <div class="sm-form ">
                                            <select class='form-control text-xs' v-model="bulan" >
                                                <option value="01">Januari</option>
                                                <option value="02">Februari</option>
                                                <option value="03">Maret</option>
                                                <option value="04">April</option>
                                                <option value="05">Mei</option>
                                                <option value="06">Juni</option>
                                                <option value="07">Juli</option>
                                                <option value="08">Agustus</option>
                                                <option value="09">September</option>
                                                <option value="10">Oktober</option>
                                                <option value="11">November</option>
                                                <option value="12">Desember</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7" v-else>
                                <p class="font-semibold text-xs">Range Tanggal :</p>
                                <div class="sm-form ">
                                    <input type="date" style="width:40%;" id="dari_tanggal" name="dari_tanggal" class="form-control p-1 rounded-sm text-xs ml-2 d-inline" placeholder="dari_tanggal" v-model="dari_tanggal" >
                                    <span class="mx-2 text-xs">
                                        S/D
                                    </span>
                                    <input type="date" style="width:40%;" id="sampai_tanggal" name="sampai_tanggal" class="form-control p-1 rounded-sm text-xs ml-2 d-inline" placeholder="sampai_tanggal" v-model="sampai_tanggal" >
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <p class="font-semibold text-xs">Proses :</p>
                                <button type="button" class="btn btn-sm btn-dark" @click="getForm" id="buttonCari">Cari</button>
                            </div>
                        </div>
                        <div v-if="ready">
                            <!-- MAP -->
                            <div class="row justify-content-center">
                                <div class="order-2 sm:order-1 shadow-sm rounded-lg pt-2 col-12 position-relative">
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div class="row">
                                                
                                                <div class="col-3">
                                                    <p class="font-semibold text-xs">Peta : </p>
                                                    <select class='form-control text-xs' style="width:150px;" v-model="typePeta">
                                                        <option>Standard</option>
                                                        <option>Earth</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <br>
                                            <div id="mapid" style="height:100vh;width:100%;background:white;">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <p class="font-semibold my-2">List Geojson</p>
                                            <div class="sm-form ">
                                                <input type="text" id="search" name="search" class="form-control p-1 text-xs rounded-lg shadow-sm" placeholder="search" v-model="search">
                                            </div>
                                            <div v-for="(item, index) in td" :key="index+'listGeojson'" class="my-2 shadow-sm text-xs p-1 py-2 rounded-lg" v-if="item.geojson">
                                                <button type="button" @click="getLayer(item,index)" :class="showLayers[index]?'btn-dark':'btn-dark-outline'" class="btn btn-sm text-xs float-right p-1 ml-2">
                                                &#8660;
                                                </button>
                                                <span class="text-xs">
                                                    {{index+1}}. {{item.nama_geojson}} - Bulan({{item.periode.split('-')[1]}})
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- MAP -->
                        </div>
                        <div v-else>
                            <div style="height:80vh;" class="d-flex justify-content-center align-items-center shadow-sm-lg rounded-lg">
                                <div style="width:100%">
                                    <p class="text-center text-xl">Loading ...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- DATA PER BLOK -->
                    <div v-if="td.length>0" class="row p-3 justify-content-center">
                        <div class="table-responsive col-sm-10 mt-2 shadow-sm rounded p-3">
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <td></td>
                                    <td v-for="(item, index) in listBlok" :key="index+'listBlok'">
                                        {{item}}
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in [...new Map(listDataBlok.map((item) => [item['nama_type'], item])).values()]" :key="index+'listdatablok'"
                                v-if="item.geojson"
                                >
                                    <td class="font-semibold">{{item.nama_type}}</td>
                                    <td v-for="(item2, index2) in listBlok" :key="index+'listBlok2'">
                                        {{item[item2].reduce((e,n)=>{return parseFloat(e)+parseFloat(n.data_produksi)},0)}} <span class="font-semibold text-lg">Ha</span> 
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                     <!-- DATA KHUSUS FASILITAS PENUNJANG -->
                     <div v-if="td.length>0" class="row mt-2 p-3 justify-content-center">
                         <div class="col-sm-11 p-3 shadow-sm rounded">
                             <select class='form-control' v-model="keyOther" style="width:50%;">
                                 <option v-for="(item, index) in listType" :key="index+'litsType'" :value="item.id">{{item.nama_type}}</option>
                             </select>
                             <hr>
                             <p class="font-semibold text-xl">Fasilitas Penunjang</p>
                             <div class="row">
                                 <div class="col-3 p-2" v-for="(item, index) in listOther" :key="index+'listOther'">
                                     <div class="p-2 shadow-sm rounded-lg">
                                         <p class="text-center text-sm font-semibold p-2">{{item.nama_form}} <br> ({{formatIndo(item.periode)}})</p>
                                         <div class="pl-5 pb-5 pt-2 pr-5 text-center font-semibold text-lg">
                                             {{item.data_produksi}} <span class="text-xl">Ha</span>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

</main><!-- End #main -->

<script type="module">
    var maps;
    new Vue({
        el: "#masterPetaEbl",
        data() { // init data
            return {
                modal: false,
                ready: false,
                loading: true,
                modalGeojson:false,
                center:[ -2.9637506121817,115.21088566035503],
                layers: {},
                pilih:{},
                typePeta:'Standard',
                search:'',
                typePeriode:"Bulan",
                tahun:"",
                bulan:"",
                dari_tanggal:'',
                sampai_tanggal:'',
                listType:[],
                listBlok:[],
                listTahun:[],
                listDataBlok:[],
                keyOther:'4',
                markers:[],
                listGeojson:[],
                listLayers:[],
                countPertama:0,
                showLayers:[]
            }
        },
        computed:{
            td(){
                let that=this;
                let data=this.listGeojson;
                data=data.filter(e=>e.nama_geojson.toLowerCase().indexOf(that.search.toLowerCase())!=-1)
                return data;
            },
            listOther(){
                let that=this;
                let data=this.datanya;
                data=data.filter(e=>e.bl_type_id==this.keyOther)
                return data;
            }
        },
        mounted() {
            document.getElementById('masterPetaEbl').classList.remove('d-none');
            this.tahun=parseInt(this.tahuns(new Date()))
            this.bulan=this.bulans(new Date())
            this.dari_tanggal=this.format(new Date())
            this.sampai_tanggal=this.format(new Date())
            var min = this.tahun - 9
            var years = []
            for (var i = this.tahun; i >= min; i--) {
                years.push(i)
            }
            this.listTahun=years
           setTimeout(() => {
               document.getElementById('buttonCari').click();
           }, 1000);
        },
        watch: { // event listener
            typePeta(){
                this.refreshMap();
            }
        },
        methods: {
            async test(){
                alert('dancing');
            },
            async getForm(){
                let that=this;
                this.ready = false;
                if(this.typePeriode=='Bulan'){
                    if(this.bulan && this.tahun){
                        this.periode=`${this.tahun}-${this.bulan}-${this.hari(new Date())}`;
                        var date = new Date(this.periode);
                        this.dari_tanggal = this.format(new Date(date.getFullYear(), date.getMonth(), 1));
                        this.sampai_tanggal = this.format(new Date(date.getFullYear(), date.getMonth() + 1, 0));
                    }
                }
                this.getData();
                this.$forceUpdate();
            },
            async getData(){
                let that=this;
                let data;
                this.datanya=[];
                data = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/master?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                let listType = await axios.get("<?= site_url() ?>" + `/api/bukaan-lahan/type`);
                this.listType=listType.data;
                this.datanya=data.data;
                let geojson= [...new Map(data.data.map((item) => [item["periode"], item])).values()];
                // let geojson= data.data;
                this.filterListDataBlok(geojson);
                geojson=geojson.map(e=>{
                    return {
                        periode:e.periode,
                        id_geojson:e.id_geojson,
                        bl_type_id:e.bl_type_id,
                        nama_geojson:e.nama_type,
                        geojson:e.geojson?e.geojson:null
                    }
                })
                console.log('geojson',geojson)
                this.getListGeojson(geojson)
                this.$forceUpdate();
            },
            filterListDataBlok(geojson){
                let that=this;
                // Other data
                
                let listBlok = this.datanya.map(e=>e.blok_group)
                listBlok= [...new Set(listBlok)];
                this.listBlok=listBlok;
                let obj=[];
                geojson.forEach(e=>{
                    let hasil={};
                    listBlok.forEach(k=>{
                        hasil[k]=that.datanya.filter(x=>x.bl_type_id==e.bl_type_id && x.blok_group==k)
                    })
                    obj.push({
                        bl_type_id:e.bl_type_id,
                        nama_type:e.nama_type,
                        geojson:e.geojson?e.geojson:null,
                        ...hasil
                    })
                })
                this.listDataBlok=obj;
                console.log('obj',obj)
            },
            cek(){
                alert('Router belum ada');
            },
            async getListGeojson(data){
                // let res=await axios.get('http://localhost/peta/api/get.php');
                this.listGeojson=data;
                data.forEach((e,i)=>{
                    this.showLayers[i]=true;
                })
                this.$forceUpdate();
                this.loadmap()
            },
            async getLayer(item,index){
                let keys=Object.keys(this.layers['peta'+index]['_layers']);
                let lat =this.layers['peta'+index]['_layers'][keys[0]]['feature']['geometry']['coordinates'][0][0][0];
                let long =this.layers['peta'+index]['_layers'][keys[0]]['feature']['geometry']['coordinates'][0][0][1];
                if(this.showLayers[index]){
                    this.showLayers[index]=false;
                }else{
                    this.showLayers[index]=true;
                }
                this.center=[long,lat];
                maps.panTo(this.center)
                this.refreshMap();
            },
            loadmap() {
                let that = this;
                // Set loading to ready
                this.ready = true;
                setTimeout(() => {
                    var center = this.center;
                    maps = new L.Map('mapid');
                    maps.setView(center, 18);
                    if(that.typePeta=='Standard'){
                        L.tileLayer(
                           'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', 
                           // '', 
                           { maxZoom: 19,
                           // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                       }).addTo(maps);
                    }else{
                        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
                        }).addTo(maps);
                    }
                    // Load data geo.json
                    that.loadGeoJson();
                    that.$forceUpdate();
                }, 600);
            },
            loadGeoJson(){
                let that=this;
                this.listGeojson=this.listGeojson.filter(e=>e.geojson)
                this.listGeojson.forEach((e,i)=>{
                    let data={}
                    data.path=e.geojson;
                    axios.post("<?= site_url() ?>" + `/api/download/geojson`,data).then(res => {
                        if(this.showLayers[i]){
                            // taruh object layers leaflet yang sudah di initialize ke global local object layers['peta']
                            that.layers['peta'+i] = L.geoJSON(res.data).addTo(maps);
                            // cek semua keys dari properties _layers
                            let layers=Object.keys(that.layers['peta'+i]['_layers']);
                            let customData=that.datanya.filter(k=>k.id_geojson==e.id_geojson && k.bl_type_id==e.bl_type_id);
                            that.layers['peta'+i]['custom-data']=customData
                            console.log('before')
                            if(that.countPertama==0){
                                let keys=Object.keys(this.layers['peta'+0]['_layers']);
                                let lat =typeof that.layers['peta'+0]['_layers'][keys[0]]['feature']['geometry']['coordinates'][0][0][0]=='object'?that.layers['peta'+0]['_layers'][keys[0]]['feature']['geometry']['coordinates'][0][0][0][0]:that.layers['peta'+0]['_layers'][keys[0]]['feature']['geometry']['coordinates'][0][0][0];
                                let long =typeof that.layers['peta'+0]['_layers'][keys[0]]['feature']['geometry']['coordinates'][0][0][0]=='object'?that.layers['peta'+0]['_layers'][keys[0]]['feature']['geometry']['coordinates'][0][0][0][1]:that.layers['peta'+0]['_layers'][keys[0]]['feature']['geometry']['coordinates'][0][0][1];
                                that.center=[long,lat];
                                that.countPertama++;
                            }
                            // Edit tampilan dari layers
                            that.editLayerView(layers,that.layers['peta'+i]);
                            // Menghilangkan Nama Kebun yang terdouble
                            that.loading = false;
                            // that.loadmarker(1);
                            that.$forceUpdate();
                        }
                    })
                })
            },
            editLayerView(layers,obj){
                let that=this;
                var opacity=1.0;
                // Loop setiap layers lalu ditambahkan properti seperti event click dan css
                maps.panTo(this.center)
                layers.forEach(e=>{
                    let layer = obj['_layers'][e];
                    layer['_path'].classList.add('anim');
                    layer['_path'].setAttribute("fill", layer.feature.properties.fill ? layer.feature.properties.fill : '#000');
                    layer['_path'].addEventListener('click', function() {
                        that.pilih=obj['custom-data']
                        // that.pilih=layer.feature.properties;
                        that.modal=true;
                        that.$forceUpdate();
                    })
                    // ==================================
                    layer['_path'].addEventListener('mouseover', function() {
                        layer['_path'].setAttribute("fill", '#fff');
                        layer['_path'].style.opacity=1.0;
                    })
                    layer['_path'].addEventListener('mouseout', function() {
                        layer['_path'].setAttribute("fill", layer.feature.properties.fill ? layer.feature.properties.fill : '#000');
                        layer['_path'].style.opacity=opacity;
                    })
                    // ====================
                    layer['_path'].setAttribute("stroke-opacity", opacity);
                    layer['_path'].style.opacity=opacity;
                    layer['_path'].setAttribute("stroke-width", layer.feature.properties['stroke-width']);
                    layer['_path'].setAttribute("stroke", layer.feature.properties.stroke);
                    layer['_path'].setAttribute("fill-opacity", layer.feature.properties['fill-opacity'] ? layer.feature.properties['fill-opacity'] : '0.5');
                    let keys=Object.keys(layer.feature.properties);
                    let kecuali=['fill','FID','path']
                    let txt = `<table class="table-sm table-bordered">`;
                    // FILTER CUMA PROPERTIES KEYS YANG DIGUANAKN DARI LAYER.FEATURE.PROPERTIES
                    keys.forEach(e=>{
                        if(!kecuali.includes(e)){
                            if(layer.feature.properties[e]!=null){
                                txt = txt + `
                                <tr>
                                <td>${e}</td>    
                                <td>:</td>    
                                <td>${layer.feature.properties[e]}</td>    
                                </tr>
                                `;
                            }
                        }
                    })
                        layer.bindTooltip(
                            txt + `</table>`, {
                                permanent: false,
                                sticky: true
                            }
                        );
                })
            },
            loadmarker(id){
                let that=this;
                var greenIcon = L.icon({
                    iconUrl: `https://a.tiles.mapbox.com/v4/marker/pin-m+${Math.floor(Math.random()*16777215).toString(16)}@2x.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXFhYTA2bTMyeW44ZG0ybXBkMHkifQ.gUGbDOPUN1v1fTs5SeOR4A`,
                    iconSize:     [20, 60], // size of the icon
                    iconAnchor:   [12, 34], // point of the icon which will correspond to marker's location
                    popupAnchor:  [-3, -26] // point from which the popup should open relative to the iconAnchor
                });
                var hover=true;
                // DISINI BISA MENGAMBIL POSISI MARKER DARI DATABASE LALU DI LOOP DAN DITARUH lat,lng PADA MARKER DYNAMIC
                if(!hover){
                    that.markers['marker'+`e.id`]=L.marker([-2.992837, 114.968395] // lat, lng
                    ,{icon:greenIcon}).bindPopup(`
                    <div class="text-xs">
                        <div class="font-bold uppercase">title</div>
                        <div class="text-justify">deskripsi</div>
                    </div>
                    `, {maxWidth: 500}).addTo(maps)
                    .addEventListener('click',function(){
                            alert('icon di klik');
                        })
                }else{
                    that.markers['marker'+`e.id`]=L.marker([-2.992837, 114.968395],{icon:greenIcon}).bindTooltip(`
                    <div class="text-xs">
                        <div class="font-bold uppercase">Icon Hover</div>
                        <div class="text-justify">Diskripsi dan custom tampilan hover</div>
                    </div>
                    `,{ permanent: false, sticky: true }).addTo(maps)
                    .addEventListener('click',function(){
                        alert('icon diklik');
                    })
                }
            },
            refreshMap(data) {
                this.ready = false;
                this.$forceUpdate();
                setTimeout(() => {
                    this.loadmap();
                }, 300);
            },
            format(tgl) {
                return dateFns.format(
                    new Date(tgl),
                    "YYYY-MM-DD"
                );
            },
            formatIndo(tgl){
                return dateFns.format(
                    new Date(tgl),
                    "DD MMMM YYYY"
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
        components: {
            vSelect:VueSelect.VueSelect
        },
    })
</script>

<?= $this->endSection() ?>