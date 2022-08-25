<div class="card card-custom card-stretch gutter-b"> 
    <div class="card-header border-0"> 
        <h3 class="card-title remove-flex align-items-start flex-column">
            <span class="card-label font-weight-bolder text-dark">Revenue Report</span> 
        </h3>
        <div class="card-toolbar"> 
            <form action="{{ route('dashboard') }}" id="revenue_form" method="get">
                <div class="form-group row">
                    <label class="col-form-label text-right">Predefined Ranges</label>
                        <div >
                            <div class='input-group'>
                            <input type='text' id="revenue_date_range" class="form-control" readonly  placeholder="Select date range"/>
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
$(document).ready(function () {
    $(function() {

        @if(isset($_GET['revenue_start_range']))  
            var start = moment("{{ $_GET['revenue_start_range'] }}");
        @else 
            var start = moment(); 
        @endif

        @if(isset($_GET['revenue_end_range'])) 
            var end = moment("{{ $_GET['revenue_end_range'] }}");
        @else 
            var end = moment();
        @endif

        function cb(start, end) {
            $('#revenue_date_range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY')); 
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
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
        }, cb);

        cb(start, end);

        $(document).on('change', '#revenue_date_range', function (){
            $("#revenue_form").submit();
        });

    });
 
    var options = {
        chart: {
            height: 380,
            width: "100%",
            type: "bar",
            animations: {
                initialAnimation: {
                    enabled: false
                }
            },
            zoom: {
                enabled: false
            },
        },
        series: JSON.parse('{!! json_encode($chartData["sales_data"]) !!}'),
        xaxis: {
            type: "datetime", 
            labels: {
                rotate: -15,
                rotateAlways: true,
                datetimeUTC: false,
            },
            @if(isset($_GET['revenue_start_range']))
                min: new Date("{{ $_GET['revenue_start_range'] }}").getTime(), 
            @endif
            @if(isset($_GET['revenue_end_range']))
                max: new Date("{{ $_GET['revenue_end_range'] }}").getTime(),
            @endif  
            tickAmount: 6,
        },
        yaxis: {
            labels: { 
                offsetX: 0,
                formatter: function(val) {
                    return  "₹"+ val;
                },
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false
            }
        },
        dataLabels: { 
            formatter: function(val) {
                return  "₹"+ val;
            },
        },
        legend:{
            position: 'top',
            horizontalAlign: 'right',
            offsetX: -10 
        }, 
        title: {
            text: '{{ $chartData["title"] }}',
            align: 'left',
            offsetX: 14
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy'
            },
            y: {
                formatter: function (val) {
                    return "₹"+ val
                }
            }
        },
    };

    var chart = new ApexCharts(document.querySelector("#chartContainer"), options); 
    chart.render();
}); 
</script>

