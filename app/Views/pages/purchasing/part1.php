<div id="purchasingDashboard" class="d-none">
    <div class="row py-2">
        <!-- <div class="col-sm-5">
            <p>Tanggal</p>
            <div class="row">
                <div class="col-sm-5">
                    <input type="date" id="dari_tanggal" name="dari_tanggal" class="form-control p-1 rounded-sm shadow-sm" placeholder="dari_tanggal" v-model="dari_tanggal" >
                </div>
                <div class="col-sm-2">
                    S/D 
                </div>
                <div class="col-sm-5">
                    <input type="date" id="sampai_tanggal" name="sampai_tanggal" class="form-control p-1 rounded-sm shadow-sm" placeholder="sampai_tanggal" v-model="sampai_tanggal" >
                </div>
            </div>
        </div> -->
        <div class="col-sm-3">
            <p class="font-semibold">Months :</p>
            <div class="sm-form ">
                <select class='form-control' v-model="bulan" @change="getForm()">
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
        <div class="col-sm-3">
            <p class="font-semibold">Years :</p>
            <div class="sm-form ">
                <select class='form-control' v-model="tahun" @change="page=1;getForm()">
                    <option v-for="(item, index) in listTahun" :key="index+'listTahun'">{{item}}</option>
                </select>
            </div>
        </div>
        <div class="col-sm-3">
            <br>
            <br>
            <button type="button" class="btn btn-sm btn-dark text-xs p-2" @click="getData()"> Filter</button>
        </div>
    </div>
    <div v-if="reports">
        <div class="row mt-4">
            <div class="col-sm-3">
                <div class="p-2 bg-white shadow-sm rounded-lg">
                    <p class="font-bold text-sm">Cost of Purchase Order <br> (In Million)</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);margin-top:2px;" class="float-left">
                        <path d="M21 4H2v2h2.3l3.521 9.683A2.004 2.004 0 0 0 9.7 17H18v-2H9.7l-.728-2H18c.4 0 .762-.238.919-.606l3-7A.998.998 0 0 0 21 4z"></path>
                        <circle cx="10.5" cy="19.5" r="1.5"></circle>
                        <circle cx="16.5" cy="19.5" r="1.5"></circle>
                    </svg>
                    <span class="ml-2 text-sm font-semibold" v-if="res.purchasing_order">Rp {{convertToRupiah((res.purchasing_order[0].total/1000000).toFixed(2)).replace('..','.')}}</span>
                    <br>
                    <br>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="p-2 bg-white shadow-sm rounded-lg">
                    <p class="font-bold text-sm">Cost to Be Invoice <br> (In Million)</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" style="fill: rgba(255, 0, 0, 1);margin-top:2px;" class="float-left ">
                        <path d="M20 3H5C3.346 3 2 4.346 2 6v12c0 1.654 1.346 3 3 3h15c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zM5 19c-.552 0-1-.449-1-1V6c0-.551.448-1 1-1h15v3h-6c-1.103 0-2 .897-2 2v4c0 1.103.897 2 2 2h6.001v3H5zm15-9v4h-6v-4h6z"></path>
                    </svg>
                    <span class="ml-2 text-sm font-semibold" v-if="res.cost_invoice">Rp {{convertToRupiah((res.cost_invoice[0].total/1000000).toFixed(2)).replace('..','.')}}</span>
                    <br>
                    <br>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="p-2 bg-white shadow-sm rounded-lg">
                    <p class="font-bold text-sm">Cost Saving <br> (In Million)</p>
                    <button type="button" class="btn btn-sm btn-success rounded-circle text-xs p-1">IDR</button>
                    <span class="ml-2 text-sm font-semibold" v-if="res.purchasing_order">Rp {{convertToRupiah(((res.purchasing_order[0].total - res.pr_estimate[0].total)/1000000).toFixed(2)).replace('..','.')}}</span>
                    <br>
                    <br>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="p-2 bg-white shadow-sm rounded-lg">
                    <p class="font-bold text-sm">AVG Performa PO</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" style="fill: rgba(0, 150, 150, 1);margin-top:2px;" class="float-left">
                        <path d="M3 3v17a1 1 0 0 0 1 1h17v-2H5V3H3z"></path>
                        <path d="M15.293 14.707a.999.999 0 0 0 1.414 0l5-5-1.414-1.414L16 12.586l-2.293-2.293a.999.999 0 0 0-1.414 0l-5 5 1.414 1.414L13 12.414l2.293 2.293z"></path>
                    </svg>
                    <span class="ml-2 text-sm font-semibold" v-if="res.average_po">Rp {{convertToRupiah(parseInt(res.average_po[0].total))}}</span>
                    <br>
                    <br>
                </div>
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
            <div class="col-sm-7">
                <div id="yearchart"></div>
            </div>
            <div class="col-sm-5">
                <div class="row">
                    <div class="col-sm-6">
                        <div id="pr_doc_chart"></div>
                        <p class="text-xs font-semibold text-center">PR Doc Progress</p>
                    </div>
                    <div class="col-sm-6">
                        <div id="pr_item_chart"></div>
                        <p class="text-xs font-semibold text-center">PR Item Progress</p>
                    </div>
                    <div class="col-sm-6">
                        <div id="po_doc_chart"></div>
                        <p class="text-xs font-semibold text-center">PO Doc Progress</p>
                    </div>
                    <div class="col-sm-6">
                        <div id="po_item_chart"></div>
                        <p class="text-xs font-semibold text-center">PO Item Progress</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
            <div class="col-sm-3 p-3">
                <div class="p-2 bg-yellow-400 font-semibold text-sm rounded-lg shadow-sm">
                    <p class="font-bold">Total PR Created</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);margin-top:2px;" class="float-left">
                        <path d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zM4 19V5h7v14H4zm9 0V5h7l.001 14H13z"></path>
                        <path d="M15 7h3v2h-3zm0 4h3v2h-3z"></path>
                    </svg>
                    <span class="text-2xl font-semibold pl-3" v-if="res.pr_created">{{res.pr_created[0].total}}</span>
                    <span class="text-2xl font-semibold pl-3" v-else>0</span>
                </div>
            </div>

            <div class="col-sm-3 p-3">
                <div class="p-2 bg-orange-400 font-semibold text-sm rounded-lg shadow-sm">
                    <p class="font-bold">Total PR Item Created</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);margin-top:2px;" class="float-left">
                        <path d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zM4 19V5h7v14H4zm9 0V5h7l.001 14H13z"></path>
                        <path d="M15 7h3v2h-3zm0 4h3v2h-3z"></path>
                    </svg>
                    <span class="text-2xl font-semibold pl-3" v-if="res.pr_item_created">{{res.pr_item_created[0].total}}</span>
                    <span class="text-2xl font-semibold pl-3" v-else>0</span>
                </div>
            </div>
            <div class="col-sm-3 p-3">
                <div class="p-2 bg-blue-400 font-semibold text-sm rounded-lg shadow-sm">
                    <p class="font-bold">Total PO Created</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);margin-top:2px;" class="float-left">
                        <path d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zM4 19V5h7v14H4zm9 0V5h7l.001 14H13z"></path>
                        <path d="M15 7h3v2h-3zm0 4h3v2h-3z"></path>
                    </svg>
                    <span class="text-2xl font-semibold pl-3" v-if="res.po_created">{{res.po_created[0].total}}</span>
                    <span class="text-2xl font-semibold pl-3" v-else>0</span>
                </div>
            </div>
            <div class="col-sm-3 p-3">
                <div class="p-2 bg-blue-600 font-semibold text-sm rounded-lg shadow-sm">
                    <p class="font-bold">Total PO Item Created</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);margin-top:2px;" class="float-left">
                        <path d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zM4 19V5h7v14H4zm9 0V5h7l.001 14H13z"></path>
                        <path d="M15 7h3v2h-3zm0 4h3v2h-3z"></path>
                    </svg>
                    <span class="text-2xl font-semibold pl-3" v-if="res.po_item_created">{{res.po_item_created[0].total}}</span>
                    <span class="text-2xl font-semibold pl-3" v-else>0</span>
                </div>
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
            <div class="col-sm-8">
                <p class="text-center font-semibold">Average Procurement Cycle Time (Days)</p>
                <div class="row">
                    <div class="col-sm-2">
                        <br>
                        <button type="button" class="btn btn-sm btn-dark rounded-circle m-0 px-3 py-2 text-xs ">o</button>
                        <br>
                        <br>
                        <br>
                        <p class=" font-semibold text-xs">PR Created</p>
                    </div>
                    <div class="col-sm-3">
                        <button type="button" class="btn btn-sm btn-style6 text-lg " v-if="res.average">{{Math.ceil(res.average.po_created[0].total)}}</button>
                    </div>
                    <div class="col-sm-2">
                        <br>
                        <button type="button" class="btn btn-sm btn-dark rounded-circle m-0 px-3 py-2 text-xs ">o</button>
                        <br>
                        <br>
                        <br>
                        <p class=" font-semibold text-xs">Released</p>
                    </div>
                    <div class="col-sm-3">
                        <button type="button" class="btn btn-sm btn-style6 text-lg " v-if="res.average">{{Math.ceil(res.average.pr_created[0].total)}}</button>
                    </div>
                    <div class="col-sm-2">
                        <br>
                        <button type="button" class="btn btn-sm btn-dark rounded-circle m-0 px-3 py-2 text-xs ">o</button>
                        <br>
                        <br>
                        <br>
                        <p class=" font-semibold text-xs">PO Created</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <p class="font-bold text-center text-lg p-3">Average Cycle Days</p>
                <p class="text-center text-4xl" v-if="res.average">{{(Math.ceil(res.average.po_created[0].total) + Math.ceil(res.average.pr_created[0].total))/2}}</p>
            </div>
            <div class="col-sm-12">
                <hr class="my-3">
            </div>
           
        </div>
        <!--  -->
        <!-- =========== -->
    </div>
</div>


<script type="module">
    import myplugin from '<?= base_url("assets/js/myplugin.js") ?>';
    let sdb = new myplugin();
    var chart1;
    var chart2;
    var chart3;
    var chart4;
    var chart5;
    new Vue({
        el: "#purchasingDashboard",
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
                        name: 'PR Release',
                        type: 'column',
                        data: this.res.purchaseChart.pr_release.map(e => e.total.toFixed(2))
                    }, {
                        name: 'PO Create',
                        type: 'column',
                        data: this.res.purchaseChart.po_create.map(e => e.total.toFixed(2))
                    }, 
                    // {
                    //     name: 'Performa',
                    //     type: 'line',
                    //     data: this.res.purchaseChart.performa.map(e => e.total.toFixed(2))
                    // }
                ],
                    colors:['#fcdb23', '#2385fc'],
                    chart: {
                        height: 350,
                        type: 'line',
                        stacked: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: [1, 1, 4]
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return that.convertToRupiah(val).replace('..', '.')
                            }
                        }
                    }

                };

                chart1 = new ApexCharts(document.querySelector("#yearchart"), options);
                chart1.render();
                // =============== pr doc
                chart2 = new ApexCharts(document.querySelector("#pr_doc_chart"), {
                    series: [parseInt(this.res.pr_doc[0].total)],
                    chart: {
                        height: 170,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#f4f430'],
                    },
                    plotOptions: {
                        radialBar: {
                            dataLabels: {
                                show: true,
                                name: {
                                    show: false,
                                },
                                value: {
                                    show: true,
                                    fontWeight: 50,
                                    fontSize: '11px',
                                    offsetY: 0
                                }
                            }
                        }
                    },
                });
                chart2.render();

                // =============== pr item
                chart3 = new ApexCharts(document.querySelector("#pr_item_chart"), {
                    series: [parseInt(this.res.pr_item[0].total)],
                    chart: {
                        height: 170,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#ffe23f'],
                    },
                    plotOptions: {
                        radialBar: {
                            dataLabels: {
                                show: true,
                                name: {
                                    show: false,
                                },
                                value: {
                                    show: true,
                                    fontWeight: 50,
                                    fontSize: '11px',
                                    offsetY: 0
                                }
                            }
                        }
                    },
                });
                chart3.render();
                // =============== pr doc
                chart4 = new ApexCharts(document.querySelector("#po_doc_chart"), {
                    series: [parseInt(this.res.po_doc[0].total)],
                    chart: {
                        height: 170,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#3561ff'],
                    },
                    plotOptions: {
                        radialBar: {
                            dataLabels: {
                                show: true,
                                name: {
                                    show: false,
                                },
                                value: {
                                    show: true,
                                    fontWeight: 50,
                                    fontSize: '11px',
                                    offsetY: 0
                                }
                            }
                        }
                    },
                });
                chart4.render();
                // =============== pr doc
                chart5 = new ApexCharts(document.querySelector("#po_item_chart"), {
                    series: [parseInt(this.res.po_item[0].total)],
                    chart: {
                        height: 170,
                        type: 'radialBar',
                        toolbar: {
                            show: true
                        }
                    },
                    fill: {
                        colors: ['#361eed'],
                    },
                    plotOptions: {
                        radialBar: {
                            dataLabels: {
                                show: true,
                                name: {
                                    show: false,
                                },
                                value: {
                                    show: true,
                                    fontWeight: 50,
                                    fontSize: '11px',
                                    offsetY: 0
                                }
                            }
                        }
                    },
                });
                chart5.render();
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
                this.res.cost_invoice = cost_invoice.data;
                let pr_estimate = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_estimate&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.pr_estimate = pr_estimate.data;
                let average_po = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=average_po&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.average_po = average_po.data;

                let pr_doc = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_doc&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.pr_doc = pr_doc.data;
                let pr_item = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_item&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.pr_item = pr_item.data;

                let po_doc = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=po_doc&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.po_doc = po_doc.data;
                let po_item = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=po_item&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.po_item = po_item.data;

                let pr_created = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_created&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.pr_created = pr_created.data;
                let pr_item_created = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_item_created&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.pr_item_created = pr_item_created.data;

                let po_created = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=po_created&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.po_created = po_created.data;
                let po_item_created = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=po_item_created&dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.po_item_created = po_item_created.data;

                let average = await axios.get("<?= site_url() ?>" + `/api/purchasing/average?dari_tanggal=${this.dari_tanggal}&sampai_tanggal=${this.sampai_tanggal}`);
                this.res.average = average.data;


                console.log(this.res)
                chart1.destroy();
                chart2.destroy();
                chart3.destroy();
                chart4.destroy();
                chart5.destroy();
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
                let cost_invoice = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=cost_invoice`);
                this.res.cost_invoice = cost_invoice.data;
                let pr_estimate = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_estimate`);
                this.res.pr_estimate = pr_estimate.data;
                let average_po = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=average_po`);
                this.res.average_po = average_po.data;

                let pr_doc = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_doc`);
                this.res.pr_doc = pr_doc.data;
                let pr_item = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_item`);
                this.res.pr_item = pr_item.data;

                let po_doc = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=po_doc`);
                this.res.po_doc = po_doc.data;
                let po_item = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=po_item`);
                this.res.po_item = po_item.data;

                let pr_created = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_created`);
                this.res.pr_created = pr_created.data;
                let pr_item_created = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=pr_item_created`);
                this.res.pr_item_created = pr_item_created.data;

                let po_created = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=po_created`);
                this.res.po_created = po_created.data;
                let po_item_created = await axios.get("<?= site_url() ?>" + `/api/purchasing/total?type=po_item_created`);
                this.res.po_item_created = po_item_created.data;

                let average = await axios.get("<?= site_url() ?>" + `/api/purchasing/average`);
                this.res.average = average.data;

                // CHART
                let purchaseChart = await axios.get("<?= site_url() ?>" + `/api/purchasing/chart`);
                this.res.purchaseChart = purchaseChart.data;
                let po_create = [];
                let pr_release = [];
                let performa = [];
                for (let i = 1; i <= 12; i++) {
                    let cek = this.res.purchaseChart.po_create.filter(e => e.bulan == i);
                    if (cek.length > 0) {
                        po_create.push({
                            total: parseInt(cek[0].total),
                            bulan: `${cek[0].bulan}`
                        })
                    } else {
                        po_create.push({
                            total: 0,
                            bulan: `${i}`
                        })
                    }
                    cek = this.res.purchaseChart.pr_release.filter(e => e.bulan == i);
                    if (cek.length > 0) {
                        pr_release.push({
                            total: parseInt(cek[0].total),
                            bulan: `${cek[0].bulan}`
                        })
                    } else {
                        pr_release.push({
                            total: 0,
                            bulan: `${i}`
                        })
                    }
                }
                po_create.forEach((e, i) => {
                    let total = (po_create[i].total / pr_release[i].total) * 100;
                    performa.push({
                        bulan: e.bulan,
                        total: total.toString() == 'NaN' ? 0 : total
                    })
                })
                this.res.purchaseChart.po_create = po_create;
                this.res.purchaseChart.pr_release = pr_release;
                this.res.purchaseChart.performa = performa;
                console.log(this.res)
                this.$forceUpdate();
                setTimeout(() => {
                    this.loadChart();
                }, 1000);
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
            convertToRupiah(angka){
                return new Intl.NumberFormat('id-ID').format(angka);
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
            document.getElementById('purchasingDashboard').classList.remove('d-none');
        },
    })
</script>