// Graphs
const ctx = document.getElementById('myChart')

const grp = document.getElementById('myGraph')

const myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [

        ],
        datasets: [{
            data: [

            ],
            lineTension: 0,
            backgroundColor: 'transparent',
            borderColor: '#007bff',
            borderWidth: 4,
            pointBackgroundColor: '#007bff'
        }],
    },
    options: {
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: true,
                text: "Water used in Litres (l)"
            },
            tooltip: {
                boxPadding: 3
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 30,
                min: 0
            }
        }
    }
})


const myGraph = new Chart(grp, {
    type: 'line',
    data: {
        labels: [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ],
        datasets: [{
            data: [
                15339,
                21345,
                18483,
                24003,
                17489,
                4092,
                8000,
            ],
            lineTension: 0,
            backgroundColor: 'transparent',
            borderColor: '#143a1a',
            borderWidth: 4,
            pointBackgroundColor: '#143a1a'
        }]
    },
    options: {
        plugins: {
            legend: {
                display: false,
            },title: {
                display: true,
                text: "Feed used in Kilograms (kg)"
            },
            tooltip: {
                boxPadding: 3
            },
            scales: {
                y: {
                    beginAtZero: false,
                }
            }
        }
    }
})
