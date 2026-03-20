<template>
    <div class="rounded-lg border border-[#c3c4c7] bg-white p-5 shadow-sm">
        <label :for="id" class="block text-sm font-medium text-slate-700">{{ label }}</label>
        <textarea
            :id="id"
            :value="modelValue"
            rows="3"
            class="mt-2 block w-full rounded border border-[#8c8f94] px-3 py-2 text-base focus:border-[#2271b1] focus:shadow-sm focus:ring-1 focus:ring-[#2271b1]"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </div>
</template>

<script setup>
defineProps({
    modelValue: { type: String, default: '' },
    label: { type: String, default: '' },
    id: { type: String, default: 'excerpt' },
});

defineEmits(['update:modelValue']);
</script>

