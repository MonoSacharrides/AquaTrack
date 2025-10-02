<template>
    <AdminLayout title="Staff">
        <div class="w-full bg-white shadow-md rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center border-b pb-4">
                    <h1 class="text-2xl font-bold text-gray-800">Staff Members</h1>
                    <div class="flex space-x-4">
                        <button
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition flex items-center gap-2">
                            <v-icon name="bi-plus" class="w-5 h-5" /> Add New Staff
                        </button>
                        <button
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition flex items-center gap-2">
                            <v-icon name="bi-download" class="w-4 h-4" /> Export
                        </button>
                    </div>
                </div>

                <div class="mt-6">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="staff in staffMembers" :key="staff.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ staff.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ staff.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ staff.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ staff.role }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span :class="statusClasses(staff.status)">
                                        {{ staff.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex space-x-4">
                                    <button class="text-blue-600 hover:text-blue-800" title="View">
                                            <v-icon name="bi-eye-fill" class="w-5 h-5" />
                                        </button>
                                        <button class="text-yellow-400 hover:text-yellow-600" title="Edit">
                                            <v-icon name="ri-edit-box-fill" class="w-5 h-5" />
                                        </button>
                                        <button class="text-red-600 hover:text-red-800" title="Delete">
                                            <v-icon name="fa-trash" class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

const staffMembers = [
    {
        id: 1,
        name: 'Admin User',
        email: 'admin@aquatrack.com',
        role: 'Administrator',
        status: 'Active'
    },
    {
        id: 2,
        name: 'Support Staff',
        email: 'support@aquatrack.com',
        role: 'Support',
        status: 'Active'
    }
];

const statusClasses = (status) => {
    return {
        'px-3 py-1 rounded-full text-xs font-semibold': true,
        'bg-green-100 text-green-800': status === 'Active',
        'bg-red-100 text-red-800': status !== 'Active'
    };
};
</script>

<style scoped>
table {
    width: 100%;
    table-layout: auto;
}

.w-full {
    width: 100%;
}

.p-6 {
    padding: 1.5rem;
}

button {
    transition: all 0.3s ease;
}

tr:hover {
    background-color: #f9fafb;
}
</style>
