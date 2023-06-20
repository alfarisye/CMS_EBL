<div class="row">
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <h5 class="card-title">Top 5 Spend Amount By Category (in Million)</h5>

                <!-- Pie Chart -->
                <div id="pieChartSpendAmount"></div>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#pieChartSpendAmount"), {
                            series: [
                              <?php
                              foreach($topCategory as $row)
                              {
                                echo $row['TOTAL_NETWR']; echo ',';
                              }
                            ?>],
                            chart: {
                                height: 350,
                                type: 'pie',
                                toolbar: {
                                    show: true
                                }
                            },
                            plotOptions: {
                                pie: {
                                    startAngle: -90,
                                    endAngle: 270,
                                    expandOnClick: true
                                }
                            },
                            dataLabels: {
                                enabled: true
                            },
                            yaxis: {
                              forceNiceScale: true,
                              title: {
                                text: 'Quantity',
                                style: {
                                  color: "#fff",
                                  fontSize: "1px"
                                }
                              },
                              min: 5,
                              labels: {
                                formatter: function(value) {
                                  return value.toLocaleString('id-ID', {
                                    minimumFractionDigits: 0
                                  });
                                }
                              },
                            },
                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'left', 
                                // formatter: function(val, opts) {
                                //     return val + " - Rp." + opts.w.globals.series[opts.seriesIndex]
                                // }
                                formatter: function(value, opts) {
                                  return value +" - Rp." + opts.w.globals.series[opts.seriesIndex].toLocaleString('id-ID', {
                                    minimumFractionDigits: 0
                                  });
                                }
                            },
                            
                            labels: [<?php
                              foreach($topCategory as $row)
                              {
                                echo "'"; echo $row['MATKL']; echo "',";
                              }
                            ?>],
                            }).render();
                    });
                </script>
                <!-- End Pie Chart -->
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <h5 class="card-title">Top 5 Supplier (in Million)</h5>

                <!-- Pie Chart -->
                <div id="pieChartTopSupplier"></div>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#pieChartTopSupplier"), {
                            series: [
                            <?php
                              foreach($topSupplier as $row)
                              {
                                echo $row['TOTAL_NETWR']; echo ',';
                              }
                            ?>],
                            chart: {
                                height: 350,
                                type: 'pie',
                                toolbar: {
                                    show: true
                                }
                            },
                            plotOptions: {
                                pie: {
                                    startAngle: -90,
                                    endAngle: 270,
                                    expandOnClick: true
                                }
                            },
                            dataLabels: {
                              enabled: true,
                              
                              formatter: function (val, opt) {
                                console.log("VAL: ", val);
                                let valueFormatted = val.toFixed(0);
                                console.log("Val formatted: ", valueFormatted);
                                // return opt.w.globals.labels[opt.dataPointIndex] + ": " + val;
                                return valueFormatted + "%";
                              },
                            },
                            yaxis: {
                              forceNiceScale: true,
                              title: {
                                text: 'Quantity',
                                style: {
                                  color: "#fff",
                                  fontSize: "1px"
                                }
                              },
                              min: 5,
                              labels: {
                                formatter: function(value) {
                                  return value.toLocaleString('id-ID', {
                                    minimumFractionDigits: 0
                                  });
                                }
                              },
                            },
                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'left', 
                                // formatter: function(val, opts) {
                                //     return val + " - " + opts.w.globals.series[opts.seriesIndex]
                                // }
                                formatter: function(value, opts) {
                                  return value +"- Rp." + opts.w.globals.series[opts.seriesIndex].toLocaleString('id-ID', {
                                    minimumFractionDigits: 0
                                  });
                                }
                            },
                            labels: [
                            <?php
                              foreach($topSupplier as $row)
                              {
                                echo "'", $row['nm_vendor']; echo "',";
                              }
                            ?>],
                        }).render();
                    });
                </script>
                <!-- End Pie Chart -->

            </div>
        </div>
    </div>

</div>

<div class="col-lg-12">
    <div class="card">
          <div class="card-body">
            <h5 class="card-title">Top 5 Supplier By Amount (In Million)</h5>

            <!-- Column Chart -->
            <div id="bargingChart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#bargingChart"), {
                  series: [{
                    name: 'Amount in (Rp.)',
                    data: [ 
                            <?php
                              foreach($topSupplier as $row)
                              {
                                echo '{x: "'; echo $row['nm_vendor']; echo '" ,';
                                echo     'y:'; echo $row['TOTAL_NETWR']; echo ',},';
                              }
                            ?>
                    ]
                  }],
                  chart: {
                    type: 'bar',
                    height: 350
                  },
                  plotOptions: {
                    bar: {
                      columnWidth: '60%',
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                  },
                  xaxis: {
                    type: 'category'
                  },
                  yaxis: {
                    forceNiceScale: true,
                    title: {
                      text: 'Quantity',
                      style: {
                        color: "#fff",
                        fontSize: "1px"
                      }
                    },
                    min: 5,
                    labels: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 0
                        });
                      }
                    },
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(value) {
                        return value.toLocaleString('id-ID', {
                          minimumFractionDigits: 0
                        });
                      }
                    }
                  }
                }).render();
              });
            </script>
            <!-- End Column Chart -->

        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="card">
          <div class="card-body">
            <h5 class="card-title">Update List Purchase Order</h5>
            <!-- Table users -->
            <table class="table datatable">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">PO</th>
                        <th scope="col">Item (s)</th>
                        <th scope="col">Net Price</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Status</th>
                        <th scope="col">Keterangan PO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>42300001</td>
                        <td>5</td>
                        <td>Rp 9.253.044</td>
                        <td>Hasnur Jaya Internasional</td>
                        <td>Approval</td>
                        <td>Jasa Pelabuhan Sungai Puting</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>42300001</td>
                        <td>5</td>
                        <td>Rp 9.253.044</td>
                        <td>Hasnur Jaya Internasional</td>
                        <td>Approval</td>
                        <td>Jasa Pelabuhan Sungai Puting</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>42300001</td>
                        <td>5</td>
                        <td>Rp 9.253.044</td>
                        <td>Hasnur Jaya Internasional</td>
                        <td>Approval</td>
                        <td>Jasa Pelabuhan Sungai Puting</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>