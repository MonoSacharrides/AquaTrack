<template>
    <AdminLayout title="Edit Record">
        <div class="w-full bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold border-b pb-4 text-gray-800">Edit Record</h1>
            <form @submit.prevent="submit">
                <div class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reading</label>
                        <input v-model="form.reading" type="number" step="0.01"
                            class="mt-1 block w-full border rounded-lg px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Consumption</label>
                        <input v-model="form.consumption" type="number" step="0.01"
                            class="mt-1 block w-full border rounded-lg px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                        <input v-model="form.amount" type="number" step="0.01"
                            class="mt-1 block w-full border rounded-lg px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select v-model="form.status" class="mt-1 block w-full border rounded-lg px-3 py-2">
                            <option value="Paid">Paid</option>
                            <option value="Pending">Pending</option>
                            <option value="Overdue">Overdue</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Save
                    </button>
                    <Link :href="route('admin.records.index')"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    record: Object,
});

const form = reactive({
    reading: props.record.reading,
    consumption: props.record.consumption,
    amount: props.record.amount,
    status: props.record.status || 'Pending',
});

const submit = () => {
    router.put(route('admin.records.update', props.record.id), form, {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('admin.records.index'));
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};
</script>
