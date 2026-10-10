<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const getAvatarUrl = (path) => {
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
};

const avatarPreview = ref(getAvatarUrl(user.value?.avatar));

watch(
    () => user.value?.avatar,
    (newAvatar) => {
        avatarPreview.value = getAvatarUrl(newAvatar);
    }
);

const form = useForm({
    _method: 'post',
    name: user.value?.name || '',
    email: user.value?.email || '',
    avatar: null,
});

const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.avatar = file;
        avatarPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('profile.update'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset('avatar');
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">ข้อมูลโปรไฟล์</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">อัปเดตข้อมูลบัญชีและรูปภาพประจำตัวของคุณ</p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold text-xl shadow-sm flex-shrink-0">
                    <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover" />
                    <!-- แก้ไขจุด user.value ให้เป็น user เพื่อให้ Vue Unwraps อัตโนมัติ -->
                    <span v-else>{{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
                </div>
                <div>
                    <input
                        type="file"
                        id="avatar"
                        accept="image/*"
                        class="hidden"
                        @change="handleAvatarChange"
                    />
                    <label
                        for="avatar"
                        class="cursor-pointer inline-flex items-center px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                    >
                        เปลี่ยนรูปโปรไฟล์
                    </label>
                    <p class="text-[11px] text-slate-400 mt-1">PNG, JPG, WEBP ขนาดไม่เกิน 2MB</p>
                    <InputError class="mt-1" :message="form.errors.avatar" />
                </div>
            </div>

            <div>
                <InputLabel for="name" value="ชื่อผู้ใช้งาน" class="dark:text-slate-200" />
                <TextInput id="name" type="text" class="mt-1 block w-full dark:bg-slate-950 dark:border-slate-800 dark:text-white" v-model="form.name" required autofocus />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="อีเมล" class="dark:text-slate-200" />
                <TextInput id="email" type="email" class="mt-1 block w-full dark:bg-slate-950 dark:border-slate-800 dark:text-white" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 rounded-xl px-5 py-2.5">
                    {{ form.processing ? 'กำลังบันทึก...' : 'บันทึกข้อมูล' }}
                </PrimaryButton>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">บันทึกเรียบร้อยแล้ว</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
