<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-bold text-rose-600 dark:text-rose-400">
                ลบบัญชีผู้ใช้งาน
            </h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                เมื่อบัญชีของคุณถูกลบ ข้อมูลทั้งหมดรวมถึงบทความและความคิดเห็นจะถูกลบถาวร กรุณาดาวน์โหลดข้อมูลที่ต้องการเก็บไว้ก่อนดำเนินการ
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion" class="rounded-xl px-4 py-2">
            ขอลบบัญชีของฉัน
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    ยืนยันว่าคุณต้องการลบบัญชีผู้ใช้นี้อย่างถาวร?
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    โปรดกรอกรหัสผ่านปัจจุบันของคุณเพื่อยืนยันว่าต้องการลบบัญชีนี้ทิ้งอย่างถาวร
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="รหัสผ่าน" class="sr-only" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4 dark:bg-slate-950 dark:border-slate-800 dark:text-white"
                        placeholder="กรอกรหัสผ่านเพื่อยืนยัน"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal" class="rounded-xl">
                        ยกเลิก
                    </SecondaryButton>

                    <DangerButton
                        class="rounded-xl"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        {{ form.processing ? 'กำลังลบ...' : 'ยืนยันลบบัญชีถาวร' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
