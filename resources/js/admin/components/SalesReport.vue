<template>
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    Sales Report
                </p>
                <p class="mt-1 text-sm font-medium text-slate-900">
                    Overview of your revenue performance
                </p>
            </div>
            <div class="inline-flex rounded-full bg-slate-100 p-1 text-xs font-medium text-slate-600">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    type="button"
                    class="rounded-full px-3 py-1 transition"
                    :class="
                        activeTab === tab.key
                            ? 'bg-white text-slate-900 shadow-sm'
                            : 'text-slate-500'
                    "
                    @click="activeTab = tab.key"
                >
                    {{ tab.label }}
                </button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div
                v-for="metric in activeMetrics"
                :key="metric.label"
                class="rounded-xl bg-slate-50 px-3 py-3 text-xs ring-1 ring-slate-100"
            >
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">
                    {{ metric.label }}
                </p>
                <p class="mt-1 text-lg font-semibold text-slate-900">
                    {{ metric.value }}
                </p>
                <p class="mt-1 text-[11px]" :class="metric.trendClass">
                    {{ metric.delta }} vs last period
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const tabs = [
    { key: 'today', label: 'Today' },
    { key: 'monthly', label: 'Monthly' },
    { key: 'annual', label: 'Annual' },
];

const dataByTab = {
    today: [
        { label: 'Revenue', value: '$4,320', delta: '+18.4%', trendClass: 'text-emerald-600' },
        { label: 'Orders', value: '142', delta: '+9.2%', trendClass: 'text-emerald-600' },
        { label: 'Growth Rate', value: '+6.1%', delta: '+1.2 pts', trendClass: 'text-emerald-600' },
    ],
    monthly: [
        { label: 'Revenue', value: '$75,210', delta: '+25.1%', trendClass: 'text-emerald-600' },
        { label: 'Orders', value: '9,754', delta: '+12.7%', trendClass: 'text-emerald-600' },
        { label: 'Growth Rate', value: '+25.08%', delta: '+3.4 pts', trendClass: 'text-emerald-600' },
    ],
    annual: [
        { label: 'Revenue', value: '$842,310', delta: '+18.7%', trendClass: 'text-emerald-600' },
        { label: 'Orders', value: '106,942', delta: '+11.3%', trendClass: 'text-emerald-600' },
        { label: 'Growth Rate', value: '+19.3%', delta: '+2.1 pts', trendClass: 'text-emerald-600' },
    ],
};

const activeTab = ref('monthly');

const activeMetrics = computed(() => dataByTab[activeTab.value]);
</script>
