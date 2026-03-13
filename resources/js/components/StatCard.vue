<template>
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    {{ label }}
                </p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">
                    {{ value }}
                </p>
            </div>
            <div
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl"
                :class="iconBgClass"
            >
                <component :is="icon" class="h-5 w-5" :class="iconColorClass" />
            </div>
        </div>
        <div class="mt-3 flex items-center gap-2 text-xs">
            <span
                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 font-medium"
                :class="pillClass"
            >
                <span>{{ change }}</span>
            </span>
            <span class="text-slate-500">Since last month</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, required: true },
    value: { type: String, required: true },
    change: { type: String, required: true },
    trend: {
        type: String,
        default: 'up', // up | down | neutral
    },
    icon: {
        type: [Object, Function],
        required: true,
    },
});

const iconBgClass = computed(() => {
    if (props.trend === 'up') return 'bg-green-50';
    if (props.trend === 'down') return 'bg-red-50';
    return 'bg-slate-50';
});

const iconColorClass = computed(() => {
    if (props.trend === 'up') return 'text-green-600';
    if (props.trend === 'down') return 'text-red-600';
    return 'text-slate-500';
});

const pillClass = computed(() => {
    if (props.trend === 'up')
        return 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-100';
    if (props.trend === 'down')
        return 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-100';
    return 'bg-slate-50 text-slate-700 ring-1 ring-inset ring-slate-100';
});
</script>

