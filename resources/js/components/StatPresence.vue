<template>
    <div class="bg-white p-4 rounded-lg shadow">
        <canvas ref="chart"></canvas>
    </div>
</template>

<script>
import { Chart } from 'chart.js';

export default {
    props: ['data', 'labels', 'title'],

    mounted() {
        new Chart(this.$refs.chart, {
            type: 'bar',
            data: {
                labels: this.labels,
                datasets: [{
                    label: this.title,
                    data: this.data,
                    backgroundColor: this.data.map(value => {
                        if (value >= 80) return '#16a34a'; // Vert foncé
                        if (value >= 60) return '#22c55e'; // Vert clair
                        if (value >= 40) return '#f97316'; // Orange
                        return '#ef4444'; // Rouge
                    }),
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
}
</script>
