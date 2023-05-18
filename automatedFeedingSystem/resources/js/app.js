import './bootstrap';

import '../sass/app.scss'

import './mychart.js';

import 'feather-icons';


    window.livewire.on('chartUpdate', (chartId, labels, datasets) => {
    let chart = window[chartId];

    chart.data.datasets.forEach((dataset, key) => {
    dataset.data = datasets[key];
});

    chart.data.labels = labels;

    console.log(chart)
    //alert("I'm here");
    chart.update();
});

