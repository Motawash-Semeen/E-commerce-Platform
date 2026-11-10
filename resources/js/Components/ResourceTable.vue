<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';

/**
 * The card every admin/owner list uses: filters bar, table, empty state and pagination.
 *   <ResourceTable :paginator="shops" empty-title="No shops">
 *     <template #filters>...</template>
 *     <template #head><th class="table-th">Name</th>...</template>
 *     <template #row="{ item }"><td class="table-td">{{ item.name }}</td>...</template>
 *   </ResourceTable>
 */
defineProps({
    paginator: { type: Object, required: true },
    emptyTitle: { type: String, required: true },
    emptyMessage: { type: String, default: '' },
});
</script>

<template>
    <div class="card overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-gray-100 bg-gray-50/70 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                <slot name="filters" />
            </div>
            <p class="shrink-0 text-sm text-gray-500">
                <span class="font-semibold text-gray-900">{{ paginator.total }}</span>
                {{ paginator.total === 1 ? 'result' : 'results' }}
            </p>
        </div>

        <div v-if="paginator.data.length" class="overflow-x-auto">
            <table class="stack-table min-w-full">
                <thead class="border-b border-gray-100">
                    <tr><slot name="head" /></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="(item, i) in paginator.data"
                        :key="item.id"
                        class="group animate-fade-up transition-colors hover:bg-indigo-50/40"
                        :style="{ animationDelay: `${i * 40}ms` }"
                    >
                        <slot name="row" :item="item" />
                    </tr>
                </tbody>
            </table>
        </div>

        <EmptyState v-else :title="emptyTitle" :message="emptyMessage">
            <slot name="empty" />
        </EmptyState>

        <div v-if="paginator.last_page > 1" class="border-t border-gray-100 px-4 py-3">
            <Pagination :paginator="paginator" />
        </div>
    </div>
</template>
