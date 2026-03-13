<template>
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <div class="mb-3 flex items-center justify-between">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                Weekly Activity
            </p>
            <span class="text-[11px] text-slate-500">Visits per day</span>
        </div>
        <div class="flex items-end justify-between gap-2">
            <div
                v-for="day in days"
                :key="day.label"
                class="flex flex-1 flex-col items-center gap-1"
            >
                <div
                    class="flex w-full items-end justify-center rounded-full bg-slate-100"
                    style="height: 80px"
                >
                    <div
                        class="w-2 rounded-full bg-blue-500 transition-all"
                        :style="{ height: `${day.value}%` }"
                    />
                </div>
                <span class="text-[11px] font-medium text-slate-500">
                    {{ day.label }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
const days = [
    { label: 'Mon', value: 35 },
    { label: 'Tue', value: 55 },
    { label: 'Wed', value: 70 },
    { label: 'Thu', value: 62 },
    { label: 'Fri', value: 80 },
    { label: 'Sat', value: 48 },
    { label: 'Sun', value: 40 },
];
</script>

