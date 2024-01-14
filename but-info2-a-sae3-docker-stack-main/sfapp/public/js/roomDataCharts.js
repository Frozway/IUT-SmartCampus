function createCharts(labels, temperatureData, co2Data, humidityData, dayDate) {
    var temperatureChart = new Chart(document.getElementById('temperatureChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: dayDate ? 'Température (°C)' + ' au ' + dayDate : 'Température (°C)',
                data: temperatureData,
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    var co2Chart = new Chart(document.getElementById('co2Chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: dayDate ? 'CO2 (ppm)' + ' au ' + dayDate : 'CO2 (ppm)',
                data: co2Data,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    var humidityChart = new Chart(document.getElementById('humidityChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: dayDate ? 'Humidité (%)' + ' au ' + dayDate : 'Humidité (%)',
                data: humidityData,
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
