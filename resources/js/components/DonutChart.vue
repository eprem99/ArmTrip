<template>
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    Store Performance
                </p>
                <p class="mt-1 text-sm font-medium text-slate-900">Conversion by channel</p>
            </div>
            <select
                v-model="selectedRange"
                class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/10"
            >
                <option value="7d">Last 7 days</option>
                <option value="30d">Last 30 days</option>
                <option value="90d">Last 90 days</option>
            </select>
        </div>
        <div class="flex items-center gap-6">
            <div class="relative mx-auto h-40 w-40">
                <Doughnut :data="chartData" :options="chartOptions" />
                <div
                    class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center"
                >
                    <p class="text-xs font-medium text-slate-500">Total</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">140</p>
                    <p class="mt-0.5 text-[11px] text-slate-500">Orders</p>
                </div>
            </div>
            <div class="hidden flex-1 space-y-3 text-xs text-slate-700 sm:block">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-blue-500" />
                        <span>Online Store</span>
                    </div>
                    <span class="font-medium text-slate-900">52%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500" />
                        <span>Marketplace</span>
                    </div>
                    <span class="font-medium text-slate-900">30%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-indigo-500" />
                        <span>Offline</span>
                    </div>
                    <span class="font-medium text-slate-900">18%</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend,
} from 'chart.js';
import { Doughnut } from 'vue-chartjs';

ChartJS.register(ArcElement, Tooltip, Legend);

const selectedRange = ref('30d');

const chartData = computed(() => ({
    labels: ['Online Store', 'Marketplace', 'Offline'],
    datasets: [
        {
            data: [73, 42, 25],
            backgroundColor: ['#2563eb', '#22c55e', '#6366f1'],
            borderWidth: 0,
            hoverOffset: 4,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            backgroundColor: '#020617',
            titleFont: { size: 11, weight: '500' },
            bodyFont: { size: 11 },
            padding: 8,
            cornerRadius: 6,
        },
    },
    cutout: '70%',
};
</script>

