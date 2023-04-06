<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    entity: {
        type: Object,
        required: true,
    }
});
</script>

<template>
    <div v-if="entity.total > 0" class="flex items-center justify-between gap-6">
        <div class="text-sm text-gray-700">
            Showing <span class="font-semibold">{{ entity.from }}</span> - <span class="font-semibold">{{ entity.to }}</span> of <span class="font-semibold">{{ entity.total }}</span> results
        </div>

        <nav class="flex items-center gap-2">
            <Component
                v-for="paginationLink in entity.links"
                :is="paginationLink.url ? Link : 'span'"
                :href="paginationLink.url ?? ''"
                :class="['inline-flex items-center gap-2 px-4 py-2 text-sm', paginationLink.url ? 'text-gray-900' : 'text-gray-300', 'font-semibold bg-white rounded-full shadow-sm']"
                v-html="paginationLink.label"
            />
        </nav>
    </div>
</template>
