<template>
    <div class="rounded-lg border border-[#c3c4c7] bg-white shadow-sm">
        <div class="border-b border-[#c3c4c7] bg-[#f6f7f7] px-4 py-3">
            <h3 class="text-sm font-semibold text-slate-800">{{ title }}</h3>
        </div>
        <div class="space-y-3 p-4">
            <a
                href="#"
                class="inline-block rounded border border-[#2271b1] bg-[#2271b1] px-3 py-2 text-sm font-normal text-white hover:bg-[#135e96]"
                @click.prevent
            >
                {{ previewLabel }}
            </a>

            <div class="flex items-baseline justify-between gap-2">
                <span class="text-sm text-slate-600">
                    {{ statusLabel }}:
                    <strong>{{ statusText }}</strong>
                </span>
                <button type="button" class="text-sm text-[#2271b1] hover:underline" @click="$emit('toggle-status-edit')">
                    {{ editLabel }}
                </button>
            </div>

            <div v-if="statusEditOpen" class="pt-1">
                <select
                    :value="status"
                    class="block w-full rounded border border-[#8c8f94] px-2 py-1.5 text-sm focus:border-[#2271b1] focus:ring-1 focus:ring-[#2271b1]"
                    @change="$emit('update:status', $event.target.value)"
                >
                    <option value="draft">{{ draftLabel }}</option>
                    <option value="published">{{ publishedLabel }}</option>
                </select>
            </div>

            <p class="text-sm text-slate-600">{{ visibilityLabel }}</p>

            <p v-if="isEdit && publishedOn" class="text-sm text-slate-600">
                {{ publishedOnLabel }}: {{ publishedOn }}
            </p>

            <a
                v-if="isEdit"
                href="#"
                class="block text-sm text-[#b32d2e] hover:underline"
                @click.prevent="$emit('trash')"
            >
                {{ trashLabel }}
            </a>

            <button
                type="submit"
                :disabled="saving"
                class="w-full rounded border-0 bg-[#2271b1] py-2.5 text-sm font-normal text-white hover:bg-[#135e96] disabled:opacity-50"
            >
                {{ submitText }}
            </button>

            <p v-if="saveError" class="text-sm text-red-600">{{ saveError }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: { type: String, default: 'Publish' },

    status: { type: String, required: true },
    statusEditOpen: { type: Boolean, required: true },
    isEdit: { type: Boolean, required: true },
    saving: { type: Boolean, required: true },
    saveError: { type: String, default: '' },

    statusText: { type: String, required: true },
    publishedOn: { type: String, default: '' },

    previewLabel: { type: String, default: 'Preview' },
    statusLabel: { type: String, default: 'Status' },
    editLabel: { type: String, default: 'Edit' },
    draftLabel: { type: String, default: 'Draft' },
    publishedLabel: { type: String, default: 'Published' },
    visibilityLabel: { type: String, default: 'Visibility: Public' },
    publishedOnLabel: { type: String, default: 'Published on' },
    trashLabel: { type: String, default: 'Move to Trash' },
    submitText: { type: String, required: true },
});

defineEmits(['update:status', 'toggle-status-edit', 'trash']);
</script>

