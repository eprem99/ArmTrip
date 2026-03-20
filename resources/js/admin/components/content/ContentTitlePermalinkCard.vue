<template>
    <div class="rounded-lg border border-[#c3c4c7] bg-white p-5 shadow-sm">
        <input
            :value="title"
            type="text"
            class="block w-full border-0 border-b border-transparent bg-transparent p-0 text-2xl font-normal text-slate-800 placeholder-slate-400 focus:border-[#2271b1] focus:shadow-none md:text-3xl"
            :placeholder="titlePlaceholder"
            @input="$emit('update:title', $event.target.value)"
        />
        <p v-if="titleError" class="mt-1 text-xs text-red-600">{{ titleError }}</p>

        <p class="mt-3 text-sm text-slate-600">
            {{ permalinkLabel }}:
            <span class="text-[#2271b1]">{{ permalinkBase }}</span>

            <template v-if="!isHome">
                <span v-if="slugLocked" class="inline-flex items-center gap-1">
                    <span class="font-mono text-xs text-[#2271b1]">{{ slug || slugSample }}</span>
                    <button
                        type="button"
                        class="ml-1 inline-flex items-center justify-center rounded p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                        :title="slugEditLabel"
                        @click="$emit('unlock-slug')"
                    >
                        <PencilSquareIcon class="h-4 w-4" />
                    </button>
                </span>

                <span v-else class="inline-flex items-center gap-2">
                    <input
                        :value="slug"
                        type="text"
                        class="inline-block w-48 border-0 border-b border-dotted border-slate-400 bg-transparent p-0 text-sm text-[#2271b1] focus:border-[#2271b1] focus:outline-none"
                        :placeholder="slugSample"
                        @input="$emit('update:slug', $event.target.value)"
                        @blur="$emit('lock-slug')"
                    />
                    <span v-if="slugChecking" class="text-xs text-slate-400">{{ slugCheckingLabel }}</span>
                    <span v-else-if="slugAvailable === true" class="text-xs text-emerald-600">{{ slugAvailableLabel }}</span>
                    <span v-else-if="slugAvailable === false" class="text-xs text-red-600">{{ slugTakenLabel }}</span>
                </span>
            </template>
        </p>

        <p v-if="slugError" class="mt-1 text-xs text-red-600">{{ slugError }}</p>
    </div>
</template>

<script setup>
import { PencilSquareIcon } from '@heroicons/vue/24/outline';

defineProps({
    title: { type: String, required: true },
    slug: { type: String, required: true },
    isHome: { type: Boolean, required: true },
    permalinkBase: { type: String, required: true },
    slugLocked: { type: Boolean, required: true },
    slugChecking: { type: Boolean, required: true },
    slugAvailable: { type: [Boolean, null], default: null },

    titleError: { type: String, default: '' },
    slugError: { type: String, default: '' },

    titlePlaceholder: { type: String, default: '' },
    permalinkLabel: { type: String, default: 'Permalink' },
    slugSample: { type: String, default: '' },
    slugEditLabel: { type: String, default: 'Edit slug' },
    slugCheckingLabel: { type: String, default: 'Checking…' },
    slugAvailableLabel: { type: String, default: 'Available' },
    slugTakenLabel: { type: String, default: 'Taken' },
});

defineEmits([
    'update:title',
    'update:slug',
    'unlock-slug',
    'lock-slug',
]);
</script>

