<div wire:poll.900s class="p-2 bg-white rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">User of community</h2>
    <canvas id="userStatusChart" class="h-[200px] "></canvas>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Accéder aux données du composant Livewire

        const ctx = document.getElementById('userStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'line', // Ou 'bar', 'doughnut'
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Nomber of users',
                    data: @json($counts),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options:{
            legend:{
                display:false
            },
            scales:{
                y:{
                    beginAtZero: true
                }
            }
        }
        });
    });
</script>