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

<main id="main" class="main">
    <div id="petaebl" class="d-none">

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
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- MODAL -->
                            <div v-if="modal" @click="modal=!modal" style="position:fixed;z-index:10000000;width:100vw;height:100vh;top:0;left:0;background:black;opacity: 0.5;">
                            </div>
                            <div v-if="modal" style="position: fixed;top: 50%;left: 50%;transform: translateX(-50%) translateY(-50%);z-index:10000005;" class="animate__animated animate__fadeIn">
                                <div style="width:60vw;" class="p-2 bg-white shadow-sm rounded-lg">
                                    MODAL <br>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-striped">
                                            <tr>
                                                <td v-for="(item, index) in Object.keys(pilih)" :key="index+'key'">
                                                    {{item}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td v-for="(item, index) in Object.keys(pilih)" :key="index+'data'">
                                                    {{pilih[item]}}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- MODAL -->
                            <div class="pr-2">
                                <div v-if="ready">
                                    <!-- MAP -->
                                    <div class="row justify-content-center">
                                        <div class="order-2 sm:order-1 shadow-sm rounded-lg p-3 col-11 position-relative">
                                            <div class="row">
                                                <div class="col-sm-10">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <p class="font-semibold text-xs">Peta : </p>
                                                            <select class='form-control text-xs' style="width:150px;" v-model="typePeta">
                                                                <option>Standard</option>
                                                                <option>Earth</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="text-xs font-semibold">Proses : </p>
                                                            <a href="" @click="cek">
                                                                <button type="button" class="btn btn-sm btn-dark  "><span class="typcn typcn-document-add"></span>+ GeoJson</button>
                                                            </a>
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
                                                    <div v-for="(item, index) in td" :key="index+'listGeojson'" class="my-2 shadow-sm text-xs p-1 py-2 rounded-lg">
                                                        <button type="button" @click="getLayer(item,index)" :class="showLayers[index]?'btn-dark':'btn-dark-outline'" class="btn btn-sm text-xs float-right p-1 ml-2">
                                                        &#8660;
                                                        </button>
                                                        <span class="text-xs">
                                                            {{index+1}}. {{item.nama_geojson}}
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
        el: "#petaebl",
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
                markers:[],
                listGeojson:[],
                listLayers:[],
                showLayers:[]
            }
        },
        computed:{
            td(){
                let that=this;
                let data=this.listGeojson;
                data=data.filter(e=>e.nama_geojson.toLowerCase().indexOf(that.search.toLowerCase())!=-1)
                return data;
            }
        },
        mounted() {
            document.getElementById('petaebl').classList.remove('d-none');
            setTimeout(() => {
                // Load peta
                this.getListGeojson();
            }, 2000);
        },
        watch: { // event listener
            typePeta(){
                this.refreshMap();
            }
        },
        methods: {
            cek(){
                alert('Router belum ada');
            },
            async getListGeojson(){
                // let res=await axios.get('http://localhost/peta/api/get.php');
                let res={
                    data:[
                        {
                            nama_geojson:'Bukaan Lahan 2016-2022',
                            geojson:'<?= base_url('assets/json/2016-2022.geojson') ?>',
                        }
                    ]
                }
                this.listGeojson=res.data;
                res.data.forEach((e,i)=>{
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
                    maps.setView(center, 15);
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
                this.listGeojson.forEach((e,i)=>{
                    axios.get(e.geojson).then(res => {
                        if(this.showLayers[i]){
                            // taruh object layers leaflet yang sudah di initialize ke global local object layers['peta']
                            that.layers['peta'+i] = L.geoJSON(res.data).addTo(maps);
                            // cek semua keys dari properties _layers
                            let layers=Object.keys(that.layers['peta'+i]['_layers']);
                            console.log('keys layers',that.layers['peta'+i]['_layers']);
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
                layers.forEach(e=>{
                    let layer = obj['_layers'][e];
                    layer['_path'].classList.add('anim');
                    layer['_path'].setAttribute("fill", layer.feature.properties.fill ? layer.feature.properties.fill : '#000');
                    layer['_path'].addEventListener('click', function() {
                        that.pilih=layer.feature.properties;
                        that.modal=true;
                        that.$forceUpdate();
                    })
                    // ==================================
                    layer['_path'].addEventListener('mouseover', function() {
                        layer['_path'].setAttribute("fill", '#fff');
                        layer['_path'].style.opacity=1.0;
                    })
                    layer['_path'].addEventListener('mouseout', function() {
                        layer['_path'].setAttribute("fill", layer.feature.properties.fill ? layer.feature.properties.fill : '#fff');
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
        },
        components: {
            vSelect:VueSelect.VueSelect
        },
    })
</script>

<?= $this->endSection() ?>