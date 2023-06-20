<div id="purchasingDashboard2" class="d-none">
    <div v-if="reports">
        <div class="row mt-4">
            <div class="col-sm-12 p-2">
                <p class="font-bold text-2xl p-3">Cost of PO by Departement</p>
                <div class="shadow-sm rounded-lg p-2 table-responsive">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th class="text-xs">Departement</th>
                            <th class="text-xs" v-for="(item, index) in res.list_wbs" :key="index+'list_wbs'">{{item.POST1}}</th>
                        </tr>
                        <tr>
                            <th class="text-xs">Amount</th>
                            <td class="text-xs hover:bg-blue-400 cursor-pointer" @click="get_list_budget(item)" v-for="(item, index) in res.list_wbs" :key="index+'list_wbs2'">
                                {{convertToRupiah(item.total).replaceAll('..','.')}}
                            </td>
                        </tr>
                    </table>
                </div>
                <hr>
                <div class="shadow-sm rounded-lg p-2 table-responsive" v-if="res.budget">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th class="text-xs">Client</th>
                            <th class="text-xs">Plan</th>
                            <th class="text-xs">BLOK</th>
                            <th class="text-xs">WBS</th>
                            <th class="text-xs">Desc</th>
                            <th class="text-xs">Total</th>
                        </tr>
                        <tr v-for="(item, index) in res.budget" :key="index+'budget'">
                            <td class="text-xs">810</td>
                            <td class="text-xs">{{item.WERKS}}</td>
                            <td class="text-xs">Blok 3 Balimas</td>
                            <td class="text-xs">{{item.POSID}}</td>
                            <td class="text-xs">{{item.POST1}}</td>
                            <td class="text-xs">{{convertToRupiah(item.WLP00).replaceAll('..','.')}}</td>
                        </tr>
                    </table>
                </div>
                <div v-else>
                    <p class="text-lg text-center p-7 mt-4">
                        Detail data budget klik disalah satu amount!
                    </p>
                </div>
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
            <div class="col-sm-12">
                <p class="float-right  font-bold text-red-600">
                    <span class="text-xl">- 1 %</span>
                    <span class="text-xs text-black">Effeciency</span>
                </p>
                <p class="font-bold text-2xl p-3">Estimate Price vs Net Price (In Million)</p>
                <div id="chart1"></div>
            </div>
        </div>
    </div>
</div>


<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    var chart1;
    new Vue({
        el: "#purchasingDashboard2",
        data() {
            return {
                modal: false,
                search: '',
                listTahun: [],
                datanya: [],
                keys: [],
                vdata: {},
                res: {},
                tahun: '',
                bulan: '',
                dari_tanggal: '',
                sampai_tanggal: '',
                chart: {},
                periodenya: 'yearly',
                reports: false,
            }
        },
        methods: {
            loadChart() {
                let that = this;
                var options = {
                    series: [{
                        name: 'PO',
                        data: this.res.vsestimate.po_create.map(e=>(parseInt(e.total)/1000000).toFixed(2))
                    }, {
                        name: 'PR',
                        data: this.res.vsestimate.pr_release.map(e=>(parseInt(e.total)/1000000).toFixed(2))
                    }],
                    chart: {
                        type: 'bar',
                        height: 430
                    },
                    colors:['#268fff','#f7ff26'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetX: -6,
                        style: {
                            fontSize: '12px',
                            colors: ['#fff']
                        },
                        formatter: function(val) {
                            return that.convertToRupiah(val).replace('..', '.')
                        }
                    },
                    stroke: {
                        show: true,
                        width: 1,
                        colors: ['#fff']
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: ['Des', 'Nov', 'Okt', "Sep", "Agust",'Jul',"Jun",'Mei','Apr','Mar','Feb', 'Jan'],
                        labels: {
                            formatter: function(val) {
                                return that.convertToRupiah(val).replace('..', '.')
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return that.convertToRupiah(val).replace('..', '.')
                            }
                        }
                    }
                };

                var chart1 = new ApexCharts(document.querySelector("#chart1"), options);
                chart1.render();
            },
            async getData() {
                let that = this;
                this.datanya = []
                this.periodenya = 'monthly'
                let bulan = this.bulans(this.dari_tanggal);
                let tahun = this.tahuns(this.sampai_tanggal);
                let purchasing_order = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=purchase_order&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.purchasing_order = purchasing_order.data;
                let cost_invoice = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=cost_invoice&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);

                console.log(this.res)

                let vsestimate = await axios.get("<?= site_url() ?>" + `/api/purchasing/estimate&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.vsestimate = vsestimate.data;
                let po_create = [];
                let pr_release = [];
                for (let i = 1; i <= 12; i++) {
                    let cek = this.res.vsestimate.po_create.filter(e => e.bulan == i);
                    if (cek.length > 0) {
                        po_create.push({
                            total: parseInt(cek[0].total),
                            bulan: parseInt(cek[0].bulan)
                        })
                    } else {
                        po_create.push({
                            total: 0,
                            bulan: i
                        })
                    }
                    cek = this.res.vsestimate.pr_release.filter(e => e.bulan == i);
                    if (cek.length > 0) {
                        pr_release.push({
                            total: parseInt(cek[0].total),
                            bulan: parseInt(cek[0].bulan)
                        })
                    } else {
                        pr_release.push({
                            total: 0,
                            bulan: i
                        })
                    }
                }
                po_create=po_create.sort(function(a, b) {
                    return parseFloat(b.bulan) - parseFloat(a.bulan);
                })
                pr_release=pr_release.sort(function(a, b) {
                    return parseFloat(b.bulan) - parseFloat(a.bulan);
                })
                this.res.vsestimate.po_create = po_create;
                this.res.vsestimate.pr_release = pr_release;

                chart1.destroy();
                this.$forceUpdate();
                setTimeout(() => {
                    this.loadChart();
                }, 1000);
            },
            async initData() {
                let that = this;
                this.reports = true;
                this.datanya = []
                // TOTAL
                let purchasing_order = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=purchase_order`);
                this.res.purchasing_order = purchasing_order.data;

                let list_wbs = await axios.get("<?= site_url() ?>" + `/api/purchasing/list-wbs`);
                this.res.list_wbs = list_wbs.data;

                let vsestimate = await axios.get("<?= site_url() ?>" + `/api/purchasing/estimate`);
                this.res.vsestimate = vsestimate.data;
                let po_create = [];
                let pr_release = [];
                for (let i = 1; i <= 12; i++) {
                    let cek = this.res.vsestimate.po_create.filter(e => e.bulan == i);
                    if (cek.length > 0) {
                        po_create.push({
                            total: parseInt(cek[0].total),
                            bulan: parseInt(cek[0].bulan)
                        })
                    } else {
                        po_create.push({
                            total: 0,
                            bulan: i
                        })
                    }
                    cek = this.res.vsestimate.pr_release.filter(e => e.bulan == i);
                    if (cek.length > 0) {
                        pr_release.push({
                            total: parseInt(cek[0].total),
                            bulan: parseInt(cek[0].bulan)
                        })
                    } else {
                        pr_release.push({
                            total: 0,
                            bulan: i
                        })
                    }
                }
                po_create=po_create.sort(function(a, b) {
                    return parseFloat(b.bulan) - parseFloat(a.bulan);
                })
                pr_release=pr_release.sort(function(a, b) {
                    return parseFloat(b.bulan) - parseFloat(a.bulan);
                })
                this.res.vsestimate.po_create = po_create;
                this.res.vsestimate.pr_release = pr_release;
                console.log(this.res)
                this.$forceUpdate();
                setTimeout(() => {
                    this.loadChart();
                }, 1000);
            },
            async get_list_budget(item){
                let posid=item.POSID.slice(0, -2)
                let data = await axios.get("<?= site_url() ?>" + `/api/purchasing/list-wbs-budget?wbs=${posid}`);
                this.res.budget = data.data;
                console.log(data.data)
                this.res.budget=this.res.budget.map(e=>{
                    return {
                        ...e,
                        WLP00:e.WLP00=="0.00"?'':e.WLP00
                    }
                })
                this.$forceUpdate();
            },
            async getForm() {
                let that = this;
                if (this.bulan && this.tahun) {
                    this.periode = `${this.tahun}-${this.bulan}-${this.hari(new Date())}`;
                    var date = new Date(this.periode);
                    this.dari_tanggal = this.formatTgl(new Date(date.getFullYear(), date.getMonth(), 1));
                    this.sampai_tanggal = this.formatTgl(new Date(date.getFullYear(), date.getMonth() + 1, 0));
                    this.$forceUpdate();
                }
            },
            formatTgl(tgl, pattern = "YYYY-MM-DD") {
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
            convertToRupiah(angka) {
                var rupiah = '';
                var angkarev = angka.toString().split('').reverse().join('');

                for (var i = 0; i < angkarev.length; i++)
                    if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + '.';

                return rupiah.split('', rupiah.length - 1).reverse().join('');
            }
        },
        mounted() {
            this.bulan = this.bulans(new Date());
            this.tahun = parseInt(this.tahuns(new Date()));
            var min = this.tahun - 9
            var years = []
            for (var i = this.tahun; i >= min; i--) {
                years.push(i)
            }
            this.listTahun = years
            this.getForm();
            this.initData();
            document.getElementById('purchasingDashboard2').classList.remove('d-none');
        },
    })
</script>