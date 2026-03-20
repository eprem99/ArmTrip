<template>
    <button
        type="button"
        class="inline-flex w-fit self-start items-center gap-2 rounded border border-[#c3c4c7] bg-white px-3 py-2 text-sm text-slate-700 shadow-sm hover:border-[#2271b1] hover:text-[#2271b1]"
        @click="$emit('click')"
    >
        <PhotoIcon class="h-5 w-5" />
        <slot />
    </button>
</template>

<script setup>
import { PhotoIcon } from '@heroicons/vue/24/outline';

defineEmits(['click']);
</script>

