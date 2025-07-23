<div wire:poll.900s class="p-2 bg-white rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Posts of community</h2>
    <canvas id="userChart" class="h-[200px] "></canvas>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        const ctx = document.getElementById('userChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Nomber of post',
                    data: @json($counts),
                    backgroundColor: 'rgba(226, 127, 14, 0.88)',
                    borderColor: 'rgba(226, 127, 14, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: false,
                        }
                    },
                    x: {
                        title: {
                            display: false,
                        }
                    }
                }
            }
        });
    });
</script>