<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps(['todo']);

const markCompletedForm = useForm({});

function handleMarkCompleted(id) {
    if (!confirm('Are you sure?')) return;

    markCompletedForm.patch(route('todos.mark-completed', id));
}
</script>

<template>
    <div class="p-6 flex items-center justify-between gap-6 space-x-2">
        <div class="flex-1 space-y-1">
            <div class="text-lg text-gray-900 font-semibold">
                {{ todo.name }}
            </div>
            <div v-if="todo.description" class="text-xs text-gray-700 font-light italic">
                {{ todo.description }}
            </div>
            <div class="flex items-center gap-2 text-gray-700 font-light">
                Categorized in <span class="font-semibold">{{ todo.category.name }}</span>
            </div>
            <div v-if="todo.shares.length > 0" class="flex items-center gap-2 text-gray-700 font-light">
                Shared with
                <div
                    v-for="share in todo.shares"
                    class="px-2 py-0.5 inline-flex text-xs text-gray-700 font-bold uppercase bg-gray-200 rounded-md"
                >
                    {{ share.name }}
                </div>
            </div>
            <div class="text-gray-700 font-light">
                Created by <span class="font-semibold">{{ todo.user.name }}</span> on <span class="font-semibold">{{ new Date(todo.created_at).toLocaleString() }}</span>
            </div>
        </div>

        <div class="flex-none">
            <div v-if="todo.is_completed" class="text-green-600">
                &check; Completed
            </div>
            <div v-else class="flex items-center gap-2">
                <Link
                    v-if="$page.props.auth.user.id === todo.user_id"
                    :href="route('todos.edit', todo.id)"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Edit
                </Link>

                <PrimaryButton
                    :disabled="markCompletedForm.processing"
                    @click="handleMarkCompleted(todo.id)"
                >
                    Mark Completed
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>
