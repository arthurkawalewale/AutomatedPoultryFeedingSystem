<!DOCTYPE html>
<html>
<head>
    <title>FusionCharts Example</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="chart-container">Chart will render here.</div>

    <script>
        FusionCharts.ready(function () {
            const chartData = <?php echo json_encode($chartData); ?>

            const chartConfig = {
                type: 'cylinder',
                renderAt: 'chart-container',
                width: '500',
                height: '300',
                dataFormat: 'json',
                dataSource: {
                    chart: {
                        caption: 'Sample Cylinder Chart',
                        subCaption: 'Sales data',
                        xAxisName: 'Month',
                        yAxisName: 'Revenue (in USD)',
                        theme: 'fusion',
                    },
                    data: chartData,
                },
            };

            const chart = new FusionCharts(chartConfig);
            chart.render();
        });
    </script>

</body>
</html>
