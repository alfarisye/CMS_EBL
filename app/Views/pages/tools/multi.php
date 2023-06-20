<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<div id="app">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="sm-form ">
          <input type="text" id="nama" name="nama" class="form-control p-2 rounded-lg shadow" placeholder="nama" v-model="vdata['nama']" >
          <input type="text" id="message" name="message" class="form-control p-2 rounded-lg shadow" placeholder="message" v-model="vdata['message']" >
          <hr>
          <button type="button" @click="simpanHarapan" class="btn btn-sm btn-dark  ">Submit</button>
      </div>
      <div v-for="(item, index) in filterData" :key="index+'for'" class="shadow rounded-lg p-3 mt-2 ">
        <div class="font-semibold text-xs">
          Nama : {{item.nama}}
        </div>
        <div class="font-semibold text-xs">
          Message : {{item.message}}
        </div>
      </div>
      <button class="btn btn-sm  rounded-circle m-1" :class="page==1?'btn-dark':'btn-dark-outline'" @click="page=1"><</button>
      <button class="btn btn-sm  rounded-circle m-1" v-for="(item, index) in totalPage" :key="index+'totalPage'" v-show="item<page+3 && item>page-3" :class="page==index+1?'btn-dark':'btn-dark-outline'" @click="page=index+1">{{index+1}}</button>
      <button class="btn btn-sm  rounded-circle m-1" :class="page==totalPage?'btn-dark':'btn-dark-outline'" @click="page=totalPage">></button>
    </div>
  </div>
</div>
<script>
  const { createApp } = Vue
  createApp({
    data() {
      return {
        message:'',
        page:1,
        perPage:5,
        totalPage:0,
        harapan:[],
        vdata:{}
      }
    },
    computed:{
      filterData(){
        let data = this.harapan.filter(e => Object.keys(this.harapan[0]).map(k=>e[k]).join(' ').toLowerCase())
        this.totalPage = Math.ceil(data.length / this.perPage);
        return data.slice((this.page - 1) * this.perPage, this.page * this.perPage);
      }
    },
    methods: {
      simpanHarapan(){
        this.harapan.push(JSON.parse(JSON.stringify(this.vdata)));
        this.$forceUpdate();
      }
    },
    mounted() {
    },
  }).mount('#app')
</script>
