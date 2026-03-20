<template>
    <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
        <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
            <h3 class="text-sm font-semibold text-slate-800">{{ title }}</h3>
        </div>
        <div class="p-4">
            <div v-if="modelValue" class="space-y-3">
                <div class="rounded border border-[#c3c4c7] bg-slate-50 p-2">
                    <p class="mb-1.5 text-xs font-medium text-slate-500">{{ previewLabel }}</p>
                    <div class="relative min-h-[120px] w-full overflow-hidden rounded bg-white">
                        <img
                            v-show="!imageError"
                            :src="modelValue"
                            :alt="alt"
                            class="max-h-48 w-full object-contain"
                            @load="$emit('update:imageError', false)"
                            @error="$emit('update:imageError', true)"
                        />
                        <div
                            v-show="imageError"
                            class="flex min-h-[120px] items-center justify-center text-xs text-slate-400"
                        >
                            {{ imageLoadErrorLabel }}
                        </div>
                    </div>
                </div>
                <input
                    :value="modelValue"
                    type="text"
                    class="block w-full rounded border border-[#8c8f94] px-2 py-1.5 text-xs focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                    placeholder="https://…"
                    @input="$emit('update:modelValue', $event.target.value)"
                />
                <button type="button" class="text-sm text-[#b32d2e] hover:underline" @click="$emit('remove')">
                    {{ removeLabel }}
                </button>
            </div>

            <template v-else>
                <button
                    type="button"
                    class="w-full py-6 text-sm text-[#2271b1] hover:underline"
                    @click="$emit('pick')"
                >
                    {{ setLabel }}
                </button>
                <button
                    type="button"
                    class="mt-1 w-full text-left text-xs text-slate-500 hover:underline"
                    @click="$emit('update:showUrl', true)"
                >
                    {{ setUrlLabel }}
                </button>
                <div v-if="showUrl" class="mt-2">
                    <input
                        :value="modelValue"
                        type="url"
                        class="block w-full rounded border border-[#8c8f94] px-2 py-1.5 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                        placeholder="https://…"
                        @input="$emit('update:modelValue', $event.target.value)"
                        @blur="$emit('update:showUrl', !!modelValue)"
                    />
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
defineProps({
    modelValue: { type: String, default: '' },
    title: { type: String, default: 'Featured Image' },
    alt: { type: String, default: '' },
    imageError: { type: Boolean, default: false },
    showUrl: { type: Boolean, default: false },

    previewLabel: { type: String, default: 'Preview' },
    imageLoadErrorLabel: { type: String, default: 'Image could not be loaded' },
    removeLabel: { type: String, default: 'Remove image' },
    setLabel: { type: String, default: 'Set featured image' },
    setUrlLabel: { type: String, default: 'Or enter URL' },
});

defineEmits([
    'update:modelValue',
    'update:imageError',
    'update:showUrl',
    'remove',
    'pick',
]);
</script>

