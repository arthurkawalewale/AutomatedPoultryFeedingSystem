import './bootstrap';

import '../sass/app.scss'

import './mychart.js';

import 'fusioncharts';

import 'feather-icons';

window.livewire.on('chartUpdate', (chartId, labels, datasets) => {
    let chart = window[chartId].chart;

    chart.data.datasets.forEach((dataset, key) => {
        dataset.data = datasets[key];
    });

    chart.data.labels = labels;

    chart.update();
});
