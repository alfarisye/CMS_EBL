<main id="main" class="main">

    <div class="pagetitle">
        <h1>User Release PO</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url("/") ?>">Home</a></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item active">Release PO</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        
    <section class="section">
        <div class="row" id="app">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">User Release PO</h5>
                        <!-- notification -->
                        <?php if (session()->getFlashdata('message')) : ?>
                            <div class="alert alert-warning" role="alert">
                                <p><?= session()->getFlashdata('message') ?></p>
                            </div>
                        <?php endif ?>

                        <!-- button with modal -->
                        <button type="button" class="btn btn-primary mb-3" @click="modal1=true">
                            <i class="bi bi-plus me-1"></i> Add new User Release PO
                        </button>
                        <!-- MODAL -->
                        <div v-if="modal1" @click="modal1=false" style="position:fixed;width:100vw;height:100vh;left:0;top:0;z-index:10000;background:black;opacity:0.5;"></div>
                        <div v-if="modal1" style="position: fixed;top: 50%;left: 50%;transform: translateX(-50%) translateY(-50%);z-index:10005;min-width:70vw;">
                            <div class="rounded-lg shadow p-4 bg-white animate__animated animate__bounceIn">
                                <h2 class="font-semibold">Add Release Code</h2>
                                <hr>
                                <form action="<?= site_url("/admin/user-release-po/update") ?>" method="POST" class="needs-validation" novalidate>
                                    <?= csrf_field() ?>
                                    <div class="row">
                                        <div class="col-8">
                                            <p>Nama</p>
                                            <select class='form-control' name="id_user" v-model="vdata.id_user">
                                                <option v-for="(item, index) in user_list" :value="item.id" :key="index+'users'">{{item.fullname}} | {{item.description}}</option>
                                            </select>
                                            <br>
                                           
                                        </div>
                                        <div class="col-12">
                                           <p>Release Code</p>
                                            <div class="row">
                                                <div class="col-sm-4" v-for="(item, index) in uniq_release" :key="index+'release_code'">
                                                    <input type="checkbox" v-model="vdata.release_code" name="release_code[]" :value="item.FRGCO" :id="item.FRGCO"> <label :for="item.FRGCO">{{item.FRGCT}}  ({{item.FRGCO}})</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 mt-3 mb-3">
                                <input type="text" id="search" name="search" class="form-control p-1 text-xs rounded-sm shadow-sm" placeholder="search" v-model="search" @input="page=1">
                            </div>
                        </div>
                            <div class="table-responsive">
                                <table class="table tabl-sm table-bordered">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Release Code PO</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr v-for="(item, index) in filtered">
                                        <td>{{item.fullname}}</td>
                                        <td>{{item.description}}</td>
                                        <td>{{item.relcode_po}}</td>
                                        <td>
                                            <button @click="modal1=true;pilih(item.id)" class="btn btn-primary btn-sm" >
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <!-- <button @click="modal1=true;pilih(item.id)" class="btn btn-danger btn-sm" >
                                                <i class="bi bi-trash"></i>
                                            </button> -->
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <button class="btn btn-sm  rounded-circle m-1" :class="page==1?'btn-dark':'btn-dark-outline'" @click="page=1"><</button>
                            <button class="btn btn-sm  rounded-circle m-1" v-for="(item, index) in totalPage" :key="index+'totalPage'" v-show="item<page+3 && item>page-3" :class="page==index+1?'btn-dark':'btn-dark-outline'" @click="page=index+1">{{index+1}}</button>
                            <button class="btn btn-sm  rounded-circle m-1" :class="page==totalPage?'btn-dark':'btn-dark-outline'" @click="page=totalPage">></button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const { createApp } = Vue
        createApp({
        data() {
            return {
                search: '',
                page: 1,
                perPage: 10,
                totalPage: 0,
                modal1:false,
                vdata:{release_code:[]},
                users:<?= json_encode($users) ?>,
                list_users:<?= json_encode($list_users) ?>,
                release_code:<?= json_encode($release_code) ?>,
            }
        },
        methods: {
            pilih(id){
                this.vdata.release_code=this.list_users.filter(e=>e.id==id)[0].relcode_po.split(',')
                this.vdata.id_user=id;
            }
        },
        computed:{
            filtered() {
                let data = this.users.filter(e => Object.keys(this.users[0]).map(k=>e[k]).join(' ').toLowerCase().indexOf(this.search.toLowerCase()) != -1)
                this.totalPage = Math.ceil(data.length / this.perPage);
                return data.slice((this.page - 1) * this.perPage, this.page * this.perPage);
            },
            user_list(){
                let data = [...new Map(this.list_users.map((item) => [item["id"], item])).values()];
                return data
            },
            uniq_release(){
                let data = [...new Map(this.release_code.map((item) => [item["FRGCO"], item])).values()];
                return data;
            }
        }
        }).mount('#app')
    </script>
</main><!-- End #main -->