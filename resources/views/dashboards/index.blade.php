@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumbs', 'Dashboard')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.css" rel="stylesheet">
    <style>
        .traffic-chart {
            min-height: 335px;
        }
    </style>
@endsection

@section('content')
        <!-- Widgets  -->
            <div class="row">
                <div class="col-lg-4 col-md-12">
                    <a href="{{route('articles.index', ['status' => 'publish'])}}">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-1">
                                        <i class="fa fa-file-o"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-text"><span class="count">{{$data["publish"]}}</span></div>
                                            <div class="stat-heading">Article Publish</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-12">
                    <a href="{{route('articles.index', ['status'=>'draft'])}}">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-2">
                                        <i class="fa fa-clipboard"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-text"><span class="count">{{$data["draft"]}}</span></div>
                                            <div class="stat-heading">Article Draft</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-4">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib">
                                        <!-- GANTI DATA DUMMY DENGAN DATA REAL -->
                                        <div class="stat-text"><span class="count">{{$data["visitors"]}}</span></div>
                                        <div class="stat-heading">Visitor</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /Widgets -->

        <!--  Traffic  -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="box-title">Traffic </h4>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-body">
                                    <div id="traffic-chart" class="traffic-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body"></div>
                    </div>
                </div>
            </div>
        <!--  /Traffic -->
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            "use strict";
            
            // Traffic Chart using chartist dengan DATA REAL
            if ($('#traffic-chart').length) {
                // Data dari controller (REAL DATA)
                var chartLabels = @json($chartData['labels']);
                var chartData = @json($chartData['data']);
                
                var chart = new Chartist.Line('#traffic-chart', {
                    labels: chartLabels,
                    series: [chartData]
                }, {
                    low: 0,
                    showArea: true,
                    showLine: false,
                    showPoint: false,
                    fullWidth: true,
                    axisX: {
                        showGrid: true
                    },
                    axisY: {
                        onlyInteger: true
                    }
                });

                chart.on('draw', function(data) {
                    if(data.type === 'line' || data.type === 'area') {
                        data.element.animate({
                            d: {
                                begin: 2000 * data.index,
                                dur: 2000,
                                from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                                to: data.path.clone().stringify(),
                                easing: Chartist.Svg.Easing.easeOutQuint
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection