<div class="p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Status users</h2>
    <canvas id="tuChart"></canvas>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Accéder aux données du composant Livewire
        const activeCount = @js($activeUsersCount);
        const blockedCount = @js($blockedUsersCount);

        const ctx = document.getElementById('tuChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut', // Ou 'bar', 'doughnut'
            data: {
                labels: ['Actifs', 'Bloqués'],
                datasets: [{
                    label: 'Nomber fo user',
                    data: [activeCount, blockedCount],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)', // Vert pour Actifs
                        'rgba(255, 99, 132, 0.6)'  // Rouge pour Bloqués
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'Répartition des statuts utilisateurs'
                    }
                }
            }
        });
    });
</script>