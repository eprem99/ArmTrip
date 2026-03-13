<template>
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <div class="mb-4 flex items-center justify-between gap-2">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    Top Selling Products
                </p>
                <p class="mt-1 text-sm font-medium text-slate-900">
                    Products driving the most revenue
                </p>
            </div>
            <button
                type="button"
                class="hidden rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-slate-800 sm:inline-flex"
            >
                View all
            </button>
        </div>

        <div class="-mx-4 -mb-3 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-2 font-medium">Product</th>
                        <th class="px-4 py-2 font-medium">Price</th>
                        <th class="px-4 py-2 font-medium">Quantity</th>
                        <th class="px-4 py-2 font-medium">Amount</th>
                        <th class="px-4 py-2 font-medium">Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <tr
                        v-for="product in products"
                        :key="product.id"
                        class="hover:bg-slate-50"
                    >
                        <td class="flex items-center gap-3 px-4 py-3">
                            <div
                                class="h-9 w-9 flex-shrink-0 overflow-hidden rounded-lg bg-slate-100"
                            >
                                <div
                                    class="flex h-full w-full items-center justify-center text-[10px] font-medium text-slate-400"
                                >
                                    IMG
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-900">
                                    {{ product.name }}
                                </p>
                                <p class="text-[11px] text-slate-500">
                                    {{ product.category }}
                                </p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-700">
                            {{ product.price }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-700">
                            {{ product.quantity }}
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-900">
                            {{ product.amount }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium"
                                :class="product.stockPillClass"
                            >
                                {{ product.stock }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
const products = [
    {
        id: 1,
        name: 'Modern Fabric Sofa Set',
        category: 'Living Room',
        price: '$499',
        quantity: 34,
        amount: '$16,966',
        stock: 'Low Stock',
        stockPillClass:
            'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-100',
    },
    {
        id: 2,
        name: 'Scandinavian Wooden Chair',
        category: 'Dining',
        price: '$129',
        quantity: 87,
        amount: '$11,223',
        stock: 'In Stock',
        stockPillClass:
            'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-100',
    },
    {
        id: 3,
        name: 'Minimalist Desk Lamp',
        category: 'Lighting',
        price: '$79',
        quantity: 120,
        amount: '$9,480',
        stock: 'In Stock',
        stockPillClass:
            'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-100',
    },
    {
        id: 4,
        name: 'Premium Cotton Bedding Set',
        category: 'Bedroom',
        price: '$159',
        quantity: 52,
        amount: '$8,268',
        stock: 'Low Stock',
        stockPillClass:
            'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-100',
    },
];
</script>

