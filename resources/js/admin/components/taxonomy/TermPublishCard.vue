<template>
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
            <h3 class="text-sm font-semibold text-slate-800">{{ title }}</h3>
        </div>
        <div class="space-y-4 p-4">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <span class="text-sm text-slate-600">
                    {{ statusLabel }}:
                    <strong class="text-slate-900">{{ statusText }}</strong>
                </span>
                <button
                    type="button"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800"
                    @click="$emit('toggle-status-edit')"
                >
                    {{ editLabel }}
                </button>
            </div>

            <div v-if="statusEditOpen">
                <select
                    :value="status"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    @change="$emit('update:status', $event.target.value)"
                >
                    <option value="draft">{{ draftLabel }}</option>
                    <option value="published">{{ publishedLabel }}</option>
                </select>
            </div>

            <p class="text-xs leading-relaxed text-slate-500">
                {{ visibilityHint }}
            </p>

            <p
                v-if="isEdit && updatedAt"
                class="text-xs text-slate-500"
            >
                {{ updatedAtLabel }}: {{ updatedAt }}
            </p>

            <button
                type="submit"
                :disabled="saving"
                class="w-full rounded-xl bg-blue-600 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 disabled:opacity-50"
            >
                {{ submitText }}
            </button>

            <p
                v-if="saveError"
                class="text-sm text-red-600"
            >
                {{ saveError }}
            </p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: { type: String, required: true },
    status: { type: String, required: true },
    statusEditOpen: { type: Boolean, required: true },
    isEdit: { type: Boolean, required: true },
    saving: { type: Boolean, required: true },
    saveError: { type: String, default: '' },
    statusText: { type: String, required: true },
    updatedAt: { type: String, default: '' },
    statusLabel: { type: String, required: true },
    editLabel: { type: String, required: true },
    draftLabel: { type: String, required: true },
    publishedLabel: { type: String, required: true },
    visibilityHint: { type: String, required: true },
    updatedAtLabel: { type: String, required: true },
    submitText: { type: String, required: true },
});

defineEmits(['update:status', 'toggle-status-edit']);
</script>
