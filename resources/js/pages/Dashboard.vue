<template>
    <div class="space-y-6">
        <!-- Greeting -->
        <section
            class="grid gap-4 md:grid-cols-[minmax(0,2fr)_minmax(0,1.2fr)] items-stretch"
        >
            <div class="rounded-2xl bg-slate-900 px-6 py-5 text-slate-50 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Good day, Admin
                </p>
                <p class="mt-2 text-xl font-semibold">
                    Welcome back to your Armenia travel platform.
                </p>
                <p class="mt-3 text-sm text-slate-300">
                    Monitor bookings, attractions, and partner performance with a single
                    clean dashboard.
                </p>
                <div class="mt-4 inline-flex items-center gap-3 rounded-xl bg-slate-800/80 px-3 py-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-400" />
                    <div class="text-xs">
                        <p class="font-medium text-slate-50">
                            {{ formattedDate }}
                        </p>
                        <p class="text-slate-400">
                            {{ formattedTime }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-xs text-slate-500 flex items-center justify-center"
            >
                <div class="space-y-2 text-center">
                    <p class="font-medium text-slate-700">Analytics Illustration</p>
                    <p>
                        Use this area for a marketing illustration or a snapshot of key
                        KPIs for your tourism platform.
                    </p>
                </div>
            </div>
        </section>

        <!-- Stat cards -->
        <section
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
        >
            <StatCard
                label="Orders"
                value="9,754"
                change="+12.4%"
                trend="up"
                :icon="ShoppingBagIcon"
            />
            <StatCard
                label="Revenue"
                value="$75,210"
                change="+25.08%"
                trend="up"
                :icon="BanknotesIcon"
            />
            <StatCard
                label="Growth"
                value="+25.08%"
                change="+3.2 pts"
                trend="up"
                :icon="ArrowTrendingUpIcon"
            />
            <StatCard
                label="Customers"
                value="2,840"
                change="+6.5%"
                trend="up"
                :icon="UsersIcon"
            />
        </section>

        <!-- Charts row -->
        <section
            class="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]"
        >
            <DonutChart />
            <WeeklyChart />
        </section>

        <!-- Sales + table -->
        <section class="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1.2fr)]">
            <SalesReport />
            <TopProductsTable />
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import {
    ShoppingBagIcon,
    BanknotesIcon,
    ArrowTrendingUpIcon,
    UsersIcon,
} from '@heroicons/vue/24/outline';

import StatCard from '../components/StatCard.vue';
import DonutChart from '../components/DonutChart.vue';
import WeeklyChart from '../components/WeeklyChart.vue';
import SalesReport from '../components/SalesReport.vue';
import TopProductsTable from '../components/TopProductsTable.vue';

const now = ref(new Date());

const tick = () => {
    now.value = new Date();
};

let intervalId;

onMounted(() => {
    intervalId = setInterval(tick, 1000 * 30);
});

onBeforeUnmount(() => {
    clearInterval(intervalId);
});

const formattedDate = computed(() =>
    now.value.toLocaleDateString(undefined, {
        weekday: 'long',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }),
);

const formattedTime = computed(() =>
    now.value.toLocaleTimeString(undefined, {
        hour: '2-digit',
        minute: '2-digit',
    }),
);
</script>

