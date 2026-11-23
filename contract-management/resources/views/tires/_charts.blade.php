<canvas id="tireStatusChart" height="200"></canvas>
<canvas id="weeklyGivesChart" height="200" class="mt-4"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx1 = document.getElementById('tireStatusChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['In Use', 'Available', 'Retired'],
            datasets: [{
                data: [{{ $stats['tires_in_use'] }}, {{ $stats['total_tires'] - $stats['tires_in_use'] }}, 0],
                backgroundColor: ['#ffc107', '#28a745', '#6c757d'],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
