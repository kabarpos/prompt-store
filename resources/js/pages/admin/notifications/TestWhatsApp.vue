<template>
    <AppLayout title="Test Pengiriman WhatsApp">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Test Pengiriman WhatsApp
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Status Konfigurasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status Konfigurasi</h3>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <div class="w-40 text-gray-600">Status Webhook:</div>
                                <div :class="settings.is_active ? 'text-green-600' : 'text-red-600'">
                                    {{ settings.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-40 text-gray-600">API Key:</div>
                                <div :class="settings.api_key_set ? 'text-green-600' : 'text-red-600'">
                                    {{ settings.api_key_set ? 'Terkonfigurasi' : 'Belum dikonfigurasi' }}
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-40 text-gray-600">Webhook URL:</div>
                                <div :class="settings.webhook_url ? 'text-green-600' : 'text-red-600'">
                                    {{ settings.webhook_url || 'Belum dikonfigurasi' }}
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-40 text-gray-600">Nomor Pengirim:</div>
                                <div :class="settings.sender_phone ? 'text-green-600' : 'text-red-600'">
                                    {{ settings.sender_phone || 'Belum dikonfigurasi' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Test -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Kirim Pesan Test</h3>
                        <form @submit.prevent="sendTestMessage" class="space-y-4">
                            <div>
                                <InputLabel for="phone" value="Nomor WhatsApp" />
                                <TextInput
                                    id="phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.phone"
                                    placeholder="628xxxxxxxxxx"
                                />
                                <InputError :message="form.errors.phone" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="message" value="Pesan" />
                                <TextArea
                                    id="message"
                                    class="mt-1 block w-full"
                                    v-model="form.message"
                                    placeholder="Masukkan pesan yang akan dikirim"
                                />
                                <InputError :message="form.errors.message" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton 
                                    type="submit"
                                    variant="primary"
                                    :loading="form.processing"
                                    :disabled="form.processing || !settings.is_active"
                                >
                                    {{ form.processing ? 'Mengirim...' : 'Kirim Pesan Test' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Riwayat Pengiriman -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Pengiriman (24 Jam Terakhir)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Error</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="log in logs" :key="log.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(log.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ log.phone }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ truncateMessage(log.message) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                                'bg-green-100 text-green-800': log.status === 'success',
                                                'bg-red-100 text-red-800': log.status === 'failed'
                                            }">
                                                {{ log.status === 'success' ? 'Berhasil' : 'Gagal' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-red-500">
                                            {{ log.error || '-' }}
                                        </td>
                                    </tr>
                                    <tr v-if="logs.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Belum ada riwayat pengiriman
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/InputLabel.vue'
import TextInput from '@/components/ui/TextInput.vue'
import TextArea from '@/components/ui/TextArea.vue'
import InputError from '@/components/ui/InputError.vue'
import { Button as PrimaryButton } from '@/components/ui/button'
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

export default defineComponent({
    components: {
        AppLayout,
        InputLabel,
        TextInput,
        TextArea,
        InputError,
        PrimaryButton,
        Head
    },

    props: {
        settings: {
            type: Object,
            required: true
        },
        logs: {
            type: Array,
            required: true
        }
    },

    setup(props) {
        const form = useForm({
            phone: '',
            message: ''
        })

        const breadcrumbs = [
            { name: 'Dashboard', href: route('admin.dashboard') },
            { name: 'Pengaturan', href: route('admin.settings.index') },
            { name: 'Test WhatsApp', href: route('admin.settings.test-whatsapp'), current: true }
        ]

        return { form, breadcrumbs }
    },

    methods: {
        async sendTestMessage() {
            if (!this.settings.is_active) {
                this.$notify({
                    title: 'Error',
                    text: 'WhatsApp webhook tidak aktif. Silakan aktifkan terlebih dahulu.',
                    type: 'error'
                })
                return
            }

            try {
                await this.form.post(route('admin.settings.test-whatsapp.send'))
                
                this.$notify({
                    title: 'Berhasil',
                    text: 'Pesan test berhasil dikirim',
                    type: 'success'
                })
                
                this.form.reset()
                this.$inertia.reload()
            } catch (error) {
                this.$notify({
                    title: 'Error',
                    text: error.message || 'Gagal mengirim pesan test',
                    type: 'error'
                })
            }
        },

        formatDate(date) {
            return new Date(date).toLocaleString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        },

        truncateMessage(message, length = 50) {
            if (message.length <= length) return message
            return message.substring(0, length) + '...'
        }
    }
})
</script> 