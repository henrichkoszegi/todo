<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps(['categories', 'users']);

const form = useForm({
    name: '',
    description: '',
    category_id: '',
    shares: [],
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Add Todo" />

        <template #header>
            <div class="flex items-center justify-between gap-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Todo</h2>
                <Link :href="route('todos.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Cancel</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">Add Todo</h2>
                        </header>

                        <form @submit.prevent="form.post(route('todos.store'))" class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="Name *" />

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Description" />

                                <textarea
                                    id="description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.description"
                                ></textarea>

                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <InputLabel for="category_id" value="Category *" />

                                <select
                                    id="category_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.category_id"
                                    required
                                >
                                    <option disabled value="">Please select one</option>
                                    <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                                </select>

                                <InputError class="mt-2" :message="form.errors.category_id" />
                            </div>

                            <div>
                                <InputLabel for="shares" value="Share With" />

                                <select
                                    id="shares"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.shares"
                                    multiple
                                >
                                    <option v-for="user in users" :value="user.id">{{ user.name }}</option>
                                </select>

                                <InputError class="mt-2" :message="form.errors.shares" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    Save
                                </PrimaryButton>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
