{{-- Chart.js, loaded only on screens that draw a chart. --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" integrity="sha384-bs/nf9FbdNouRbMiFcrcZfLXYPKiPaGVGplVbv7dLGECccEXDW+S3zjqSKR5ZEaD" crossorigin="anonymous"></script>
<script>
window.g4tLineChart = function (canvasId, labels, series) {
    var canvas = document.getElementById(canvasId);
    if (!canvas || !window.Chart) { return; }
    var green = '#3d7a1e', grey = '#a9b4ac';
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: series.map(function (s, i) {
                return {
                    label: s.label, data: s.data, borderColor: i === 0 ? green : grey, borderWidth: i === 0 ? 2.5 : 1.5,
                    borderDash: i === 0 ? [] : [5, 4], backgroundColor: i === 0 ? 'rgba(61,122,30,.09)' : 'transparent',
                    fill: i === 0, tension: .3, pointRadius: labels.length > 45 ? 0 : 2.5, pointHoverRadius: 4
                };
            })
        },
        options: {
            responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, color: '#5f6c64' } } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#8a968e', maxTicksLimit: 10, autoSkip: true } },
                y: { beginAtZero: true, grid: { color: '#eef2ef' }, ticks: { color: '#8a968e', precision: 0 } }
            }
        }
    });
};
</script>
