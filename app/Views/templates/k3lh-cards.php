
<?php $colorCounter = 0; ?>

<?php
$bulan = [
  1 => "Januari",
  2 => "Febuari",
  3 => "Maret",
  4 => "April",
  5 => "Mei",
  6 => "Juni",
  7 => "Juli",
  8 => "Agustus",
  9 => "September",
  10 => "Oktober",
  11 => "November",
  12 => "Desember"
];
?>

<?php foreach ($grouped_data as $g => $d) : ?>
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
      <?php 
        $colors = array('text-warning', 'text-primary', 'text-success', 'text-info');
        $color = $colors[$colorCounter % count($colors)];
        ?>
        <h3 class="card-title <?= $color ?>"><?= $g ?></h3>
        <div class="row">
          <?php foreach ($d as $category => $total) : ?>
            <div class="col mt-5 text-center">
              <h1 class="<?= $color ?>"><?= $total ?></h1>
              <p class="<?= $color ?>"><?= $category ?></p>
            </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>
  </div>
  <?php $colorCounter++ ?>
<?php endforeach ?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Lost Time Injury</h5>

        <!-- Column Chart -->
        <div id="LTI"></div>

        <script>
          document.addEventListener("DOMContentLoaded", () => {
            new ApexCharts(document.querySelector("#LTI"), {
              series: [
                <?php foreach ($ringan as $name => $val) : ?> {
                    name: 'Ringan',
                    data: [
                      <?php for ($i = 1; $i <= 12; $i++) { ?> {
                          x: "<?= $bulan[$i] ?>",
                          y: <?= $val[$i]['total'] ?? 0  ?>,
                        },
                      <?php } ?>
                    ]
                  },
                <?php endforeach; ?>

                <?php foreach ($berat as $name => $val) : ?> {
                    name: 'Berat',
                    data: [
                      <?php for ($i = 1; $i <= 12; $i++) { ?> {
                          x: "<?= $bulan[$i] ?>",
                          y: <?= $val[$i]['total'] ?? 0  ?>,
                        },
                      <?php } ?>
                    ]
                  },
                <?php endforeach; ?>
                
                <?php foreach ($mati as $name => $val) : ?> {
                    name: 'Mati(Fatality)',
                    data: [
                      <?php for ($i = 1; $i <= 12; $i++) { ?> {
                          x: "<?= $bulan[$i] ?>",
                          y: <?= $val[$i]['total'] ?? 0  ?>,
                        },
                      <?php } ?>
                    ]
                  },
                <?php endforeach; ?>

              ],
              chart: {
                type: 'bar',
                height: 350,
                stacked: false,
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
                labels: {
                  formatter: function(value) {
                    return value.toLocaleString('id-ID', {
                      minimumFractionDigits: 1
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
                      minimumFractionDigits: 1
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
        <h5 class="card-title">Potential Lost Time Injury</h5>

        <!-- Column Chart -->
        <div id="PLTI"></div>

        <script>
          document.addEventListener("DOMContentLoaded", () => {
            new ApexCharts(document.querySelector("#PLTI"), {
              series: [{
                  name: 'Near Miss',
                  data: [
                    <?php for ($i = 1; $i <= 12; $i++) { ?> {
                        x: "<?= $bulan[$i] ?>",
                        y: <?= $potnear[2][$i]['total'] ?? 0 ?>,
                      },
                    <?php } ?>
                  ]
                },
                {
                  name: 'First Aid',
                  data: [
                    <?php for ($i = 1; $i <= 12; $i++) { ?> {
                        x: "<?= $bulan[$i] ?>",
                        y: <?= $potfirst[2][$i]['total'] ?? 0 ?>,
                      },
                    <?php } ?>
                  ]
                }, {
                  name: 'Medical Treatment',
                  data: [
                    <?php for ($i = 1; $i <= 12; $i++) { ?> {
                        x: "<?= $bulan[$i] ?>",
                        y: <?= $potmedical[2][$i]['total'] ?? 0 ?>,
                      },
                    <?php } ?>
                  ]
                },
                {
                  name: 'Fire Case',
                  data: [
                    <?php for ($i = 1; $i <= 12; $i++) { ?> {
                        x: "<?= $bulan[$i] ?>",
                        y: <?= $potfire[2][$i]['total'] ?? 0 ?>,
                      },
                    <?php } ?>
                  ]
                },
                {
                  name: 'Property Demage',
                  data: [
                    <?php for ($i = 1; $i <= 12; $i++) { ?> {
                        x: "<?= $bulan[$i] ?>",
                        y: <?= $potdemage[2][$i]['total'] ?? 0 ?>,
                      },
                    <?php } ?>
                  ]
                },
              ],
              chart: {
                type: 'bar',
                height: 350,
                stacked: false,
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
                labels: {
                  formatter: function(value) {
                    return value.toLocaleString('id-ID', {
                      minimumFractionDigits: 1
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
                      minimumFractionDigits: 1
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
        <h5 class="card-title">Non Potential Lost Time Injury</h5>

        <!-- Column Chart -->
        <div id="NPLTI"></div>

        <script>
          document.addEventListener("DOMContentLoaded", () => {
            new ApexCharts(document.querySelector("#NPLTI"), {
              series: [
                <?php foreach ($nonearmiss as $name => $val) : ?> {
                    name: 'Near Miss',
                    data: [
                      <?php for ($i = 1; $i <= 12; $i++) { ?> {
                          x: "<?= $bulan[$i] ?>",
                          y: <?= $val[$i]['total'] ?? 0  ?>,
                        },
                      <?php } ?>
                    ]
                  },
                <?php endforeach; ?>

                <?php foreach ($nofirstaid as $name => $val) : ?> {
                    name: 'First Aid',
                    data: [
                      <?php for ($i = 1; $i <= 12; $i++) { ?> {
                          x: "<?= $bulan[$i] ?>",
                          y: <?= $val[$i]['total'] ?? 0  ?>,
                        },
                      <?php } ?>
                    ]
                  },
                <?php endforeach; ?>

                <?php foreach ($nomedic as $name => $val) : ?> {
                    name: 'Medical Treatment',
                    data: [
                      <?php for ($i = 1; $i <= 12; $i++) { ?> {
                          x: "<?= $bulan[$i] ?>",
                          y: <?= $val[$i]['total'] ?? 0  ?>,
                        },
                      <?php } ?>
                    ]
                  },
                <?php endforeach; ?>

                <?php foreach ($nofire as $name => $val) : ?> {
                    name: 'Fire Case',
                    data: [
                      <?php for ($i = 1; $i <= 12; $i++) { ?> {
                          x: "<?= $bulan[$i] ?>",
                          y: <?= $val[$i]['total'] ?? 0  ?>,
                        },
                      <?php } ?>
                    ]
                  },
                <?php endforeach; ?>

                <?php foreach ($nopropertidemage as $name => $val) : ?> {
                    name: 'Property Demage',
                    data: [
                      <?php for ($i = 1; $i <= 12; $i++) { ?> {
                          x: "<?= $bulan[$i] ?>",
                          y: <?= $val[$i]['total'] ?? 0  ?>,
                        },
                      <?php } ?>
                    ]
                  },
                <?php endforeach; ?>
              ],
              chart: {
                type: 'bar',
                height: 350,
                stacked: false,
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
                width: 1,
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
                labels: {
                  formatter: function(value) {
                    return value.toLocaleString('id-ID', {
                      minimumFractionDigits: 1
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
                      minimumFractionDigits: 1
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
</div>