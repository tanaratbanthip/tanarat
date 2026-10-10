<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                เปลี่ยนรหัสผ่าน
            </h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                ตั้งรหัสผ่านที่มีความยาวและปลอดภัยเพื่อปกป้องบัญชีของคุณ
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" value="รหัสผ่านปัจจุบัน" class="dark:text-slate-200" />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full dark:bg-slate-950 dark:border-slate-800 dark:text-white"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div>
                <InputLabel for="password" value="รหัสผ่านใหม่" class="dark:text-slate-200" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full dark:bg-slate-950 dark:border-slate-800 dark:text-white"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="ยืนยันรหัสผ่านใหม่" class="dark:text-slate-200" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full dark:bg-slate-950 dark:border-slate-800 dark:text-white"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 rounded-xl px-5 py-2.5">
                    {{ form.processing ? 'กำลังบันทึก...' : 'อัปเดตรหัสผ่าน' }}
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                        เปลี่ยนรหัสผ่านเรียบร้อยแล้ว
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
