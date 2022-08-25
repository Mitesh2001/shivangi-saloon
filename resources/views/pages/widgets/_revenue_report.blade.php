<div class="card card-custom gutter-b" id="revenue-report">
    <div class="card-header border-0">
        <h3 class="card-title remove-flex align-items-start flex-column">
            <span class="card-label font-weight-bolder text-dark">Revenue Report</span>
        </h3>
        <div class="card-toolbar">
            <form action="{{ route('dashboard') }}" id="revenue_form" method="get">
                <div class="form-group row">
                    <label class="col-form-label text-right">Predefined Ranges</label>
                    <div>
                        <div class='input-group'>
                            <input type='text' id="revenue_date_range" class="form-control" readonly
                                placeholder="Select date range" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                            <input type="hidden" name="revenue_start_range" id="revenue_start_range">
                            <input type="hidden" name="revenue_end_range" id="revenue_end_range">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    var card = new KTCard('revenue-report');


    $(function() {


        @if(isset($revenueStartDate))
        var start = moment("{{ $revenueStartDate }}");
        @else
        var start = moment();
        @endif

        @if(isset($revenueEndDate))
        var end = moment("{{ $revenueEndDate }}");
        @else
        var end = moment();
        @endif

        function cb(start, end) {
            $('#revenue_date_range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                'MMMM D, YYYY'));
            $("#revenue_start_range").val(start.format('YYYY-MM-DD'));
            $("#revenue_end_range").val(end.format('YYYY-MM-DD'));
        }

        $('#revenue_date_range').daterangepicker({
            startDate: start,
            endDate: end,
            locale: {
                format: 'DD/MM/YYYY'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment()
                    .subtract(1, 'month').endOf('month')
                ]
            },
        }, cb);

        cb(start, end);

        $(document).on('change', '#revenue_date_range', function() {
            $("#revenue_form").submit();
        });

    });

    var options = {
        title: {
            text: '{{ $chartData["title"] }}',
            align: 'left',
            offsetX: 14
        },
        series: JSON.parse('{!! json_encode($chartData["sales_data"]) !!}'),
        chart: {
            type: 'bar',
        },
        plotOptions: {
            bar: {
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 1,
            colors: ['#fff']
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return "₹" + val
                }
            }
        },
        xaxis: {
            labels: {
                offsetX: 0,
                formatter: function(val) {
                    return "₹" + val;
                },
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false
            },
        },
    };

    var chart = new ApexCharts(document.querySelector("#chartContainer"), options);
    chart.render();
});
</script>


