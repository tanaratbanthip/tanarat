<script setup>
import BlogLayout from '@/Layouts/BlogLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="ตั้งค่าบัญชีและโปรไฟล์" />

    <BlogLayout>
        <div class="max-w-4xl mx-auto py-4 space-y-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">ตั้งค่าบัญชี</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">จัดการข้อมูลโปรไฟล์ ความปลอดภัย และการตั้งค่าบัญชีของคุณ</p>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 rounded-2xl shadow-sm">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 rounded-2xl shadow-sm">
                <UpdatePasswordForm />
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 rounded-2xl shadow-sm">
                <DeleteUserForm />
            </div>
        </div>
    </BlogLayout>
</template>
