<template>
    <div class="rounded-lg border border-[#c3c4c7] bg-white p-5 shadow-sm">
        <h3 class="mb-3 text-sm font-semibold text-slate-800">
            {{ titleLabel }}
        </h3>
        <div class="space-y-4">
            <div>
                <label for="seo_title" class="block text-sm font-medium text-slate-700">
                    {{ metaTitleLabel }}
                </label>
                <input
                    id="seo_title"
                    :value="seoTitle"
                    type="text"
                    class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    :placeholder="metaTitlePlaceholder"
                    @input="$emit('update:seoTitle', $event.target.value)"
                />
            </div>
            <div>
                <label for="seo_description" class="block text-sm font-medium text-slate-700">
                    {{ metaDescriptionLabel }}
                </label>
                <textarea
                    id="seo_description"
                    :value="seoDescription"
                    rows="3"
                    class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    :placeholder="metaDescriptionPlaceholder"
                    @input="$emit('update:seoDescription', $event.target.value)"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    seoTitle: { type: String, default: '' },
    seoDescription: { type: String, default: '' },
    titleLabel: { type: String, default: 'SEO' },
    metaTitleLabel: { type: String, default: 'Meta title' },
    metaDescriptionLabel: { type: String, default: 'Meta description' },
    metaTitlePlaceholder: { type: String, default: '' },
    metaDescriptionPlaceholder: { type: String, default: '' },
});

defineEmits(['update:seoTitle', 'update:seoDescription']);
</script>

