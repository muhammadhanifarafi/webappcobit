@extends('layouts.master')

@section('title')
  Dashboard Monitoring Project <b>{{ $totalProject }}</b>
@endsection

@section('breadcrumb')
    @parent
    <!-- <li class="active">Dashboard Monitoring Project</li> -->
@endsection

@section('content')
<style>
    .wizard-steps {
        padding: 1%;
        display: flex;
        flex-direction: row; /* Ini akan membuat elemen-elemen di dalamnya ditampilkan secara vertikal */
    }
</style>
<div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box" style="background-color:#10a4d0;">
            <div class="inner">
                <div class="wizard-steps" style="display: flex; justify-content: space-between;" style="display: flex; justify-content: space-between;">
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(1,1)" style="text-align:center;color:white;text-decoration:none;">
                            <!-- <h3>{{ $trx_permintaan_pengembangan }} -</h3> -->
                            <h3>{{ $trx_permintaan_pengembangan }} 
                              <!--  -->
                            </h3>
                         </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(1,2)" style="text-align:center;color:green;text-decoration:none;">
                            <!-- <h3>{{ $is_approve_permintaan_pengembangan }} -</h3> -->
                            <h3>{{ $is_approve_permintaan_pengembangan }} 
                              <!--  -->
                            </h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(1,3)" style="text-align:center;color:red;text-decoration:none;">
                            <!-- <h3>{{ $is_not_approve_permintaan_pengembangan }}</h3> -->
                            <h3>{{ $is_not_approve_permintaan_pengembangan }}</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="inner">
              <!-- <h3>{{ $trx_permintaan_pengembangan }}</h3> -->
              <p stlye="color:white;">Permintaan Pengembangan <b>{{ $trx_permintaan_pengembangan + $is_approve_permintaan_pengembangan + $is_not_approve_permintaan_pengembangan}}</b></p>
              <!-- <b>0%</b> -->
            </div>
            <!-- <div class="box-footer" style="background-color:white;">
                <div class="row"> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">Progress</span>
                      </div>
                  </div> -->
                  <!-- <div class="col-sm-4 border-right" style="color:black"> 
                      <div class="description-block">
                        <h5 class="description-header"></h5>
                        <span class="description-text">Task</span>
                      </div>
                  </div>
                </div>
            </div> -->
            <!-- <div class="icon">
              <i class="fa fa-shopping-cart"></i>
            </div> -->
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box" style="background-color : #9da832;">
              <div class="inner">
                  <div class="wizard-steps" style="display: flex; justify-content: space-between;">
                      <div class="col">
                          <a href="javascript:void(0)" onclick="showDetail(2,1)" style="text-align:center;color:white;text-decoration:none;">
                              <h3>{{ $trx_persetujuan_pengembangan }}</h3>
                          </a>
                      </div>
                      <div class="col">
                          <a href="javascript:void(0)" onclick="showDetail(2,2)" style="text-align:center;color:green;text-decoration:none;">
                            <h3>{{ $is_approve_persetujuan_pengembangan }}</h3>
                          </a>
                      </div>
                      <div class="col">
                          <a href="javascript:void(0)" onclick="showDetail(2,3)" style="text-align:center;color:red;text-decoration:none;">
                            <h3>{{ $is_not_approve_persetujuan_pengembangan }}</h3>
                          </a>
                      </div>
                  </div>
              </div>
              <div class="inner">
                <!-- <h3>{{ $trx_persetujuan_pengembangan }}</h3> -->
                <p stlye="color:white;">Persetujuan Pengembangan <b>{{ $trx_persetujuan_pengembangan + $is_approve_persetujuan_pengembangan + $is_not_approve_persetujuan_pengembangan}}</b></p>
                <!-- <b>0%</b> -->
              </div>
              <!-- <div class="box-footer" style="background-color:white;">
                  <div class="row"> -->
                    <!-- <div class="col-sm-4 border-right">
                        <div class="description-block">
                          <h5 class="description-header">3,200</h5>
                          <span class="description-text">Progress</span>
                        </div>
                    </div> -->
                    <!-- <div class="col-sm-4 border-right" style="color:black"> 
                        <div class="description-block">
                          <h5 class="description-header"></h5>
                          <span class="description-text">Task</span>
                        </div>
                    </div>
                  </div>
              </div> -->
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
            </div>
          </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow" style="color: black;" >
            <div class="inner">
                <div class="wizard-steps" style="display: flex; justify-content: space-between;">
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(3,1)" style="text-align:center;color:green;text-decoration:none;">
                            <h3>{{ $total_hijau_pk = $trx_perencanaan_proyek->total_hijau ?? 0}}</h3>
                         </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(3,2)" style="text-align:center;color:yellow;text-decoration:none;">
                            <h3>{{ $total_kuning_pk = $trx_perencanaan_proyek->total_kuning ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(3,3)" style="text-align:center;color:red;text-decoration:none;">
                            <h3>{{ $total_merah_pk = $trx_perencanaan_proyek->total_merah ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(3,4)" style="text-align:center;color:white;text-decoration:none;">
                            <!-- <h3 style="color:blue;">{{ $total_hijau_pk + $total_kuning_pk + $total_merah_pk}}</h3> -->
                            <h3 style="color:blue;">0</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="inner">
              <!-- <h3></h3> -->
              <p>Perencanaan Proyek <b>{{ $trx_perencanaan_proyek_history; }}</b></p>
              <!-- <b>5%</b> -->
            </div>
            <!-- <div class="box-footer" style="background-color:white;">
                <div class="row"> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">Progress</span>
                      </div>
                  </div> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header"></h5>
                        <span class="description-text">Task</span>
                      </div>
                  </div>
                </div>
            </div> -->
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box" style="background-color: grey; color: black;" >
            <div class="inner">
                <div class="wizard-steps" style="display: flex; justify-content: space-between;">
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(4,1)" style="text-align:center;color:green;text-decoration:none;">
                            <h3>{{ $total_hijau_pk = $trx_perencanaan_kebutuhan->total_hijau ?? 0}}</h3>
                         </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(4,2)" style="text-align:center;color:yellow;text-decoration:none;">
                            <h3>{{ $total_kuning_pk = $trx_perencanaan_kebutuhan->total_kuning ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(4,3)" style="text-align:center;color:red;text-decoration:none;">
                            <h3>{{ $total_merah_pk = $trx_perencanaan_kebutuhan->total_merah ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(4,4)" style="text-align:center;color:white;text-decoration:none;">
                            <!-- <h3 style="color:blue;">{{ $total_hijau_pk + $total_kuning_pk + $total_merah_pk}}</h3> -->
                            <h3 style="color:blue;">0</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="inner">
              <!-- <h3></h3> -->
              <p>Perencanaan Kebutuhan <b>{{ $trx_perencanaan_kebutuhan_history; }}</b></p>
              <!-- <b>5%</b> -->
            </div>
            <!-- <div class="box-footer" style="background-color:white;">
                <div class="row"> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">Progress</span>
                      </div>
                  </div> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header"></h5>
                        <span class="description-text">Task</span>
                      </div>
                  </div>
                </div>
            </div> -->
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box" style="background-color:#10a4d0;color:black;">
            <div class="inner">
                <div class="wizard-steps" style="display: flex; justify-content: space-between;">
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(5,1)" style="text-align:center;color:green;text-decoration:none;">
                            <h3>{{ $total_hijau_ad = $trx_analisis_desain->total_hijau ?? 0}}</h3>
                         </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(5,2)" style="text-align:center;color:yellow;text-decoration:none;">
                            <h3>{{ $total_hijau_ad = $trx_analisis_desain->total_kuning ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(5,3)" style="text-align:center;color:red;text-decoration:none;">
                            <h3>{{ $total_merah_pk = $trx_analisis_desain->total_merah ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(5,4)" style="text-align:center;color:white;text-decoration:none;">
                            <!-- <h3 style="color:blue;">{{ $total_hijau_ad + $total_hijau_ad + $total_merah_pk}}</h3> -->
                            <h3 style="color:blue;">0</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="inner">
              <h3></h3>
              <p>Analisis Desain <b>{{ $trx_analisis_desain_history; }}</b></p>
              <!-- <b>25%</b> -->
            </div>
            <!-- <div class="box-footer" style="background-color:white;">
                <div class="row"> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">Progress</span>
                      </div>
                  </div> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header">{{ $trx_analisis_desain_history; }}</h5>
                        <span class="description-text">Task</span>
                      </div>
                  </div>
                </div>
            </div> -->
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box" style="background-color: #9da832; color: black;">
            <div class="inner">
                <div class="wizard-steps" style="display: flex; justify-content: space-between;">
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(6,1)" style="text-align:center;color:green;text-decoration:none;">
                            <h3>{{ $trx_pengembangan_aplikasi->total_hijau ?? 0}}</h3>
                         </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(6,2)" style="text-align:center;color:yellow;text-decoration:none;">
                            <h3>{{ $trx_pengembangan_aplikasi->total_kuning ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(6,3)" style="text-align:center;color:red;text-decoration:none;">
                            <h3>{{ $trx_pengembangan_aplikasi->total_merah ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(6,4)" style="text-align:center;color:white;text-decoration:none;">
                            <h3 style="color:blue;">0</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="inner">
              <h3></h3>
              <p>Pengembangan Aplikasi <b>{{ $trx_pengembangan_aplikasi_history; }}</b></p>
              <!-- <b>25%</b> -->
            </div>
            <!-- <div class="box-footer" style="background-color:white;">
                <div class="row"> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">Progress</span>
                      </div>
                  </div> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header"></h5>
                        <span class="description-text">Task</span>
                      </div>
                  </div>
                </div>
            </div> -->
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow" style="color: black;">
            <div class="inner">
                <div class="wizard-steps" style="display: flex; justify-content: space-between;">
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(7,1)" style="text-align:center;color:green;text-decoration:none;">
                            <h3>{{ $trx_user_acceptance_testing->total_hijau ?? 0}}</h3>
                         </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(7,2)" style="text-align:center;color:yellow;text-decoration:none;">
                            <h3>{{ $trx_user_acceptance_testing->total_kuning ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(7,3)" style="text-align:center;color:red;text-decoration:none;">
                            <h3>{{ $trx_user_acceptance_testing->total_merah ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(7,4)" style="text-align:center;color:white;text-decoration:none;">
                            <h3 style="color:blue;">0</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="inner">
              <h3></h3>
              <p style="color:black;">User Acceptance Testing <b>{{ $trx_user_acceptance_testing_history; }}</b></p>
              <!-- <b>85%</b> -->
            </div>
            <!-- <div class="box-footer" style="background-color:white;">
                <div class="row"> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">Progress</span>
                      </div>
                  </div> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block" style="color:black;">
                        <h5 class="description-header"></h5>
                        <span class="description-text">Task</span>
                      </div>
                  </div>
                </div>
            </div> -->
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box" style="background-color: grey; color: black;">
            <div class="inner">
                <div class="wizard-steps" style="display: flex; justify-content: space-between;">
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(8,1)" style="text-align:center;color:green;text-decoration:none;">
                            <h3>{{ $trx_quality_assurance_testing->total_hijau ?? 0}}</h3>
                         </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(8,2)" style="text-align:center;color:yellow;text-decoration:none;">
                            <h3>{{ $trx_quality_assurance_testing->total_kuning ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(8,3)" style="text-align:center;color:red;text-decoration:none;">
                            <h3>{{ $trx_quality_assurance_testing->total_merah ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(8,4)" style="text-align:center;color:white;text-decoration:none;">
                            <h3 style="color:blue;">0</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="inner">
              <h3></h3>
              <p>Quality Assurance Testing <b>{{ $trx_quality_assurance_testing_history; }}</b></p>
              <!-- <b>95%</b> -->
            </div>
            <!-- <div class="box-footer" style="background-color:white;">
                <div class="row"> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">Progress</span>
                      </div>
                  </div> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header"></h5>
                        <span class="description-text">Task</span>
                      </div>
                  </div>
                </div>
            </div> -->
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box" style="background-color: #999966; color: black;">
            <div class="inner">
                <div class="wizard-steps" style="display: flex; justify-content: space-between;">
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(9,1)" style="text-align:center;color:green;text-decoration:none;">
                            <h3>{{ $trx_serah_terima_aplikasi->total_hijau ?? 0}}</h3>
                         </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(9,2)" style="text-align:center;color:yellow;text-decoration:none;">
                            <h3>{{ $trx_serah_terima_aplikasi->total_kuning ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(9,3)" style="text-align:center;color:red;text-decoration:none;">
                            <h3>{{ $trx_serah_terima_aplikasi->total_merah ?? 0}}</h3>
                        </a>
                    </div>
                    <div class="col">
                        <a href="javascript:void(0)" onclick="showDetail(9,4)" style="text-align:center;color:white;text-decoration:none;">
                            <h3 style="color:blue;">0</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="inner">
              <h3></h3>
              <p>Berita Acara Serah Terima <b>{{ $trx_serah_terima_aplikasi_history; }}</b></p>
              <!-- <b>100%</b> -->
            </div>
            <!-- <div class="box-footer" style="background-color:white;">
                <div class="row"> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">Progress</span>
                      </div>
                  </div> -->
                  <!-- <div class="col-sm-4 border-right">
                      <div class="description-block">
                        <h5 class="description-header"></h5>
                        <span class="description-text">Task</span>
                      </div>
                  </div>
                </div>
            </div> -->
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
          </div>
        </div>
      </div>
      @endsection
@push('scripts')
<!-- ChartJS -->
<script src="{{ asset('AdminLTE-2/bower_components/chart.js/Chart.js') }}"></script>
<script>
  $(function() {
      // Get context with jQuery - using jQuery's .get() method.
      var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
      // This will get the first returned node in the jQuery collection.
      var salesChart = new Chart(salesChartCanvas);

      var salesChartData = {
          labels: {{ json_encode($data_tanggal) }},
          datasets: [
              {
                  label: 'Pendapatan',
                  fillColor           : 'rgba(60,141,188,0.9)',
                  strokeColor         : 'rgba(60,141,188,0.8)',
                  pointColor          : '#3b8bba',
                  pointStrokeColor    : 'rgba(60,141,188,1)',
                  pointHighlightFill  : '#fff',
                  pointHighlightStroke: 'rgba(60,141,188,1)',
                  data: {{ json_encode($data_pendapatan) }}
              }
          ]
      };

      var salesChartOptions = {
          pointDot : false,
          responsive : true
      };

      salesChart.Line(salesChartData, salesChartOptions);
  });

  function showDetail(id,id2) {
    
    var idDetail = id; 

    if(idDetail == 1){
      url_page = "permintaan_pengembangan.index";
    }else if(idDetail == 2){
      url_page = "persetujuan_pengembangan.index";
    }else if(idDetail == 3){
      url_page = "perencanaan_proyek.index";
    }else if(idDetail == 4){
      url_page = "perencanaan_kebutuhan.index";
    }else if(idDetail == 5){
      url_page = "analisis_desain.index";
    }else if(idDetail == 6){
      url_page = "user_acceptance_testing.index";
    }else if(idDetail == 7){
      url_page = "quality_assurance_testing.index";
    }else if(idDetail == 8){
      url_page = "serah_terima_aplikasi.index";
    }
    if(idDetail == 3 || idDetail == 4 || idDetail == 5 || idDetail == 6 || idDetail == 7 || idDetail == 8 || idDetail == 9){
      $('#detailModal2').modal('show');
    }else{
      $('#detailModal').modal('show');
    }
    $.ajax({
      url: '/dashboard/getDetail2/' + idDetail + '/' + id2,
      type: 'GET',
      success: function(response) {
        $('#detailTable tbody').empty();
        // Inisialisasi nomor urut
        let no = 1;

        // Looping dan masukkan data ke dalam tabel
        response.forEach(function(item) {
                // Tentukan kelas warna berdasarkan nilai progress
                let progressClass = '';
                let progressText = '';

                if (item.progress === 100) {
                    progressClass = 'btn btn-success'; // Warna hijau untuk 100%
                    progressText = 'Done';
                } else if (item.progress === 0) {
                    progressClass = 'btn btn-danger'; // Warna merah untuk 0%
                    progressText = 'Belum Dimulai';
                } else {
                    progressClass = 'btn btn-warning'; // Warna kuning untuk 1-99%
                    progressText = item.progress + '%'; // Tampilkan nilai progress
                }

                if(idDetail == 3 || idDetail == 4 || idDetail == 5 || idDetail == 6 || idDetail == 7 || idDetail == 8 || idDetail == 9){
                  $('#detailTable tbody').append(
                    `<tr>
                          <td>${no++}</td>
                          <td>${item.nomor_proyek}</td>
                          <td>${item.latar_belakang}</td>
                          <td>${item.pic}</td>
                          <td><a href="/dashboard/detailtaskdashboard/${idDetail}" class="${progressClass}" target="_blank">Detail <i class="fa fa-arrow-right" /></a></td>
                    </tr>`
                  );
                }else{
                  $('#detailTable tbody').append(
                    `<tr>
                          <td>${no++}</td>
                          <td>${item.nomor_dokumen}</td>
                          <td>${item.latar_belakang}</td>
                          <td>${item.pic}</td>
                          <td><a href="/dashboard/detailtaskdashboard/${idDetail}" class="${progressClass}" target="_blank">Detail <i class="fa fa-arrow-right" /></a></td>
                    </tr>`
                  );
                }
        });
      }
    });
  }
</script>
<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel"><b>Detail Data Project Berjalan</h5></b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="detailTable">
          <thead>
            <tr>
              <th>No. </th>
              <th>Nomor Dokumen</th>
              <th>Nama Project</th>
              <th>PIC</th>
              <th>Detail</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data akan dimasukkan secara dinamis oleh JavaScript -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="detailModal2" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel"><b>Detail Data Project Berjalan</h5></b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="detailTable">
          <thead>
            <tr>
              <th>No. </th>
              <th>Nomor Project</th>
              <th>Nama Project</th>
              <th>PIC</th>
              <th>Detail</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data akan dimasukkan secara dinamis oleh JavaScript -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endpush