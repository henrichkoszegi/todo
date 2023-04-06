<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Todo from '@/Pages/Todos/Partials/Todo.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { reactive, watch } from 'vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const props = defineProps(['categories', 'filters', 'todos']);

const filters = reactive(props.filters ?? {});

watch(filters, (value) => {
    router.get(route('todos.index'), value, {
        replace: true,
        preserveState: true,
    });
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Todos" />

        <template #header>
            <div class="flex items-center justify-between gap-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Todos</h2>
                <Link :href="route('todos.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Todo</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FlashMessage />

                <div class="mb-6 p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="mb-4 text-gray-900 font-semibold uppercase">Filters</div>
                    <div class="flex gap-4">
                        <div class="px-4 py-2 bg-gray-50 border rounded-lg">
                            <InputLabel for="category_id" value="Category" />

                            <select
                                id="category_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                v-model="filters.category_id"
                            >
                                <option :value="null">All</option>
                                <option
                                    v-for="category in categories"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <div class="px-4 py-2 bg-gray-50 border rounded-lg">
                            <InputLabel for="is_completed" value="Completion" />

                            <select
                                id="is_completed"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                v-model="filters.is_completed"
                            >
                                <option :value="null">All</option>
                                <option value="0">Not Completed</option>
                                <option value="1">Completed</option>
                            </select>
                        </div>

                        <div class="px-4 py-2 bg-gray-50 border rounded-lg">
                            <InputLabel for="ownership" value="Ownership" />

                            <select
                                id="ownership"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                v-model="filters.ownership"
                            >
                                <option :value="null">All</option>
                                <option value="own">Own</option>
                                <option value="shared">Shared with me</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div
                    v-if="todos.total > 0"
                    class="mb-6 bg-white shadow-sm rounded-lg divide-y"
                >
                    <Todo
                        v-for="todo in todos.data"
                        :key="todo.id"
                        :todo="todo"
                    />
                </div>
                <div v-else class="p-6 text-center">Nothing to show.</div>

                <Pagination :entity="todos" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
