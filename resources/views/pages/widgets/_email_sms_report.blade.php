<div class="card card-custom gutter-b mt-6 mb-6" id="revenue-report">
    <div class="card-header border-0">
        <h3 class="card-title remove-flex align-items-start flex-column">
            <span class="card-label font-weight-bolder text-dark">SMS Report</span>
        </h3>
        <div class="card-toolbar"></div>
    </div>
    <div class="card-body detail-parent"> 
        <div class="row mb-lg-6">
            <div class="col-md-6 mb-sm-5 pb-md-5">
                <div id="branch_report" class="shadow-sm" ></div>
            </div>
            <div class="col-md-6 mb-sm-5 pb-md-5">
                <div id="users_report" class="shadow-sm" ></div>
            </div> 
        </div>
        <div class="row">
            <div class="col-md-6 mb-sm-5 pb-md-5">
                <div id="sms_report" class="shadow-sm" ></div>
            </div>
            <div class="col-md-6 mb-sm-5 pb-md-5">
                <div id="email_report" class="shadow-sm" ></div>
            </div> 
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
 
    var options = {
        series: [<?php echo $salon_statistics['total_branches']; ?>, <?php echo $salon_statistics['remaining_branches']; ?>],
        title: {
            text: "Branch Balance",
            align: 'left',
            margin: 10,
            offsetX: 10,
            offsetY: 10,
            floating: false,
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
            },
        }, 
        chart: {
            width: 500,
            type: 'pie', 
        },
        legend: {
            position: 'bottom',
        },
        labels: ['Used Branches', 'Remaining Branches'], 
        colors:['#F44336', '#008ffb'],
        responsive: [{
            breakpoint: 500,
            options: {
                chart: {
                    width: 300
                },
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#branch_report"), options);
    chart.render();
 
    var options = {
        series: [<?php echo $salon_statistics['total_users']; ?>, <?php echo $salon_statistics['remaining_users']; ?>],
        title: {
            text: "Users Balance",
            align: 'left',
            margin: 10,
            offsetX: 10,
            offsetY: 10,
            floating: false,
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
            },
        },
        chart: {
            width: 500,
            type: 'pie',
        },
        legend: {
            position: 'bottom'
        },
        labels: ['Used users', 'Remaining users'], 
        responsive: [{
            breakpoint: 500,
            options: {
                chart: {
                    width: 300
                },
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#users_report"), options);
    chart.render();
 

    var options = {
        series: [getRemainingPercentage(<?php echo $salon_statistics['total_sms']; ?>,
            <?php echo $salon_statistics['remaining_sms']; ?>)],
        chart: {
            height: 350,
            type: 'radialBar',
            offsetY: -10
        },
        title: {
            text: "SMS Balance",
            align: 'left',
            margin: 10,
            offsetX: 10,
            offsetY: 10,
            floating: false,
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
            },
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                    name: {
                        fontSize: '16px',
                        color: "#111",
                        offsetY: 120
                    },
                    value: {
                        offsetY: 76,
                        fontSize: '22px',
                        color: "#111",
                        formatter: function(val) {
                            return val + "%";
                        }
                    }
                },
            }
        },
        fill: {
            type: 'gradient',
            colors: ['#ffa800'],
            gradient: {
                shade: 'light',
                shadeIntensity: 0.15,
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 65, 91]
            },
        },
        stroke: {
            dashArray: 3
        },
        labels: [
            ['{{"Used Balance: ". $salon_statistics["total_sms"] }}', '{{ "Remaining Balance: ". $salon_statistics["remaining_sms"] }}']
        ],
    };

    var sms_chart = new ApexCharts(document.querySelector("#sms_report"), options);
    sms_chart.render();


    var email_reports = {
        series: [getRemainingPercentage(<?php echo $salon_statistics['total_emails']; ?>,
            <?php echo $salon_statistics['remaining_emails']; ?>)],
        chart: {
            height: 350,
            type: 'radialBar',
            offsetY: -10
        },
        title: {
            text: "Email Balnce",
            align: 'left',
            margin: 10,
            offsetX: 10,
            offsetY: 10,
            floating: false,
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
            },
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                    name: {
                        fontSize: '16px',
                        color: "#111",
                        offsetY: 120
                    },
                    value: {
                        offsetY: 76,
                        fontSize: '22px',
                        color: "#111",
                        formatter: function(val) {
                            return val + "%";
                        }
                    }
                },
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                shadeIntensity: 0.15,
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 65, 91]
            },
        },
        stroke: {
            dashArray: 3
        },
        labels: [
            ['{{"Used Balance: ". $salon_statistics["total_emails"] }}',
                '{{ "Remaining Balance: ". $salon_statistics["remaining_emails"] }}'
            ]
        ],
    };

    var email_report = new ApexCharts(document.querySelector("#email_report"), email_reports);
    email_report.render();
})

function getRemainingPercentage(used_balance, total_balance) {
    let final = Math.round(((total_balance - used_balance) / total_balance) * 100);
    return final;
}
</script>