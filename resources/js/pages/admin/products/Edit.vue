<template>
    <Head :title="`Edit Produk - ${product.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul dan tombol kembali -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold">Edit Produk: {{ product.name }}</h1>
                <Link :href="route('admin.products.index')" class="cursor-pointer">
                    <Button variant="outline" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali
                    </Button>
                </Link>
            </div>
            
            <div class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-50 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div>
                        <h2 class="text-lg font-medium">Form Edit Produk</h2>
                        <p class="text-slate-600 dark:text-slate-400 mt-1">Perbarui informasi produk Anda</p>
                    </div>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <Label for="name">Nama</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                        autocomplete="name"
                                    />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="product_code">Kode Produk</Label>
                                    <Input
                                        id="product_code"
                                        v-model="form.product_code"
                                        type="text"
                                        class="mt-1 block w-full opacity-60 cursor-not-allowed"
                                        disabled
                                    />
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        Kode produk digenerate otomatis oleh sistem dan tidak dapat diubah
                                    </p>
                                    <InputError :message="form.errors.product_code" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="category">Kategori</Label>
                                    <div class="relative mt-1">
                                        <div 
                                            class="custom-select-container" 
                                            :class="{ 'active': isSelectOpen }"
                                        >
                                            <div 
                                                @click="toggleSelect" 
                                                class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                                            >
                                                <span>{{ selectedCategoryLabel }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isSelectOpen }">
                                                    <path d="m6 9 6 6 6-6"></path>
                                                </svg>
                                            </div>
                                            
                                            <div 
                                                v-if="isSelectOpen" 
                                                class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                                            >
                                                <div 
                                                    v-for="category in props.categories" 
                                                    :key="category.id"
                                                    @click="selectCategory(category.id)"
                                                    class="custom-select-option py-2 px-3 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                                                    :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.category_id === category.id }"
                                                >
                                                    {{ category.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.category_id" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="price">Harga</Label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm"> Rp </span>
                                        </div>
                                        <Input
                                            id="price"
                                            v-model="form.price"
                                            type="number"
                                            class="pl-12 mt-1 block w-full"
                                            placeholder="0"
                                            required
                                        />
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        Masukkan harga tanpa tanda baca, contoh: 100000
                                    </p>
                                    <InputError :message="form.errors.price" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="custom_url">URL Kustom</Label>
                                    <div class="space-y-2">
                                        <Input
                                            id="custom_url"
                                            v-model="form.custom_url"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="Gunakan URL yang sudah ada"
                                        />
                                        <div v-if="props.product.custom_url" class="text-xs text-slate-600 dark:text-slate-400 flex items-center gap-1">
                                            <span>URL saat ini:</span>
                                            <span class="font-medium">{{ props.product.custom_url }}</span>
                                        </div>
                                        <p class="text-xs text-slate-600 dark:text-slate-400">
                                            Opsional: URL kustom untuk produk (tanpa spasi dan karakter khusus). Kosongkan untuk mempertahankan URL saat ini.
                                        </p>
                                    </div>
                                    <InputError :message="form.errors.custom_url" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="demo_url">URL Demo</Label>
                                    <Input
                                        id="demo_url"
                                        v-model="form.demo_url"
                                        type="url"
                                        class="mt-1 block w-full"
                                        placeholder="https://example.com"
                                    />
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        Opsional: Masukkan URL untuk demo produk
                                    </p>
                                    <InputError :message="form.errors.demo_url" class="mt-2" />
                                </div>

                                <div class="flex items-center gap-3 mt-6">
                                    <div class="flex-1">
                                        <Label for="is_active" class="mb-3">Status Produk</Label>
                                        <div class="flex items-center gap-2">
                                            <Switch 
                                                id="is_active" 
                                                v-model="form.is_active"
                                                class="peer data-[state=checked]:bg-green-600 data-[state=unchecked]:bg-gray-200 dark:data-[state=unchecked]:bg-gray-700"
                                            />
                                            <Label for="is_active">{{ form.is_active ? 'Aktif' : 'Tidak Aktif' }}</Label>
                                        </div>
                                        <Badge 
                                            variant="outline" 
                                            :class="form.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-800/20 dark:border-green-300/20 mt-2' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300 border-gray-800/20 dark:border-gray-300/20 mt-2'"
                                        >
                                            {{ form.is_active ? 'Produk akan ditampilkan di website' : 'Produk tidak akan ditampilkan di website' }}
                                        </Badge>
                                    </div>
                                </div>

                                <div>
                                    <Label for="featured_image">Gambar Utama</Label>
                                    <div class="mt-2">
                                        <!-- Tampilkan gambar utama saat ini atau preview -->
                                        <img
                                            v-if="featuredImagePreview"
                                            :src="featuredImagePreview"
                                            :alt="product.name"
                                            class="w-32 h-32 object-cover rounded-md mb-2"
                                        />
                                        <img
                                            v-else
                                            :src="featuredImageUrl"
                                            :alt="product.name"
                                            class="w-32 h-32 object-cover rounded-md mb-2"
                                        />
                                    </div>
                                    <Input
                                        id="featured_image"
                                        type="file"
                                        @input="handleFeaturedImageChange"
                                        accept="image/*"
                                        class="mt-1 block w-full"
                                    />
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        Opsional: Ganti gambar utama produk. Format yang didukung: JPEG, PNG, JPG, GIF. Maksimal 2MB.
                                    </p>
                                    <InputError :message="form.errors.featured_image" class="mt-2" />
                                </div>

                                <div>
                                    <Label>Galeri Gambar</Label>
                                    <div class="grid grid-cols-4 gap-4 mt-2">
                                        <div
                                            v-for="image in product.gallery"
                                            :key="image.id"
                                            class="relative group"
                                        >
                                            <img
                                                :src="'/storage/' + image.image"
                                                :alt="product.name"
                                                class="w-full aspect-square object-cover rounded-md"
                                            />
                                            <button
                                                type="button"
                                                @click="showDeleteGalleryDialog(image)"
                                                class="absolute top-2 right-2 p-1 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                                            >
                                                <X class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                    <Input
                                        id="gallery"
                                        type="file"
                                        @input="handleGalleryImagesChange"
                                        accept="image/*"
                                        multiple
                                        class="mt-4 block w-full"
                                    />
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        Opsional: Tambahkan gambar baru ke galeri produk. Format yang didukung: JPEG, PNG, JPG, GIF. Maksimal 2MB per gambar.
                                    </p>
                                    <InputError :message="form.errors.gallery" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <div class="mb-6">
                                    <Label>Fitur Produk</Label>
                                    <div class="space-y-2 mt-2">
                                        <div v-for="(feature, index) in productFeatures" :key="`feature-${index}`" class="flex items-start gap-2">
                                            <div class="flex-1">
                                                <Input
                                                    v-model="feature.text"
                                                    type="text"
                                                    placeholder="Masukkan fitur produk"
                                                    class="w-full"
                                                />
                                            </div>
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="icon"
                                                @click="removeFeature(index)"
                                                class="h-10 w-10"
                                            >
                                                <X class="w-4 h-4" />
                                            </Button>
                                        </div>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            @click="addFeature"
                                            class="mt-2 w-full"
                                        >
                                            Tambah Fitur
                                        </Button>
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        Opsional: Tambahkan fitur-fitur produk
                                    </p>
                                </div>

                                <div class="mb-6">
                                    <Label>Nilai/Keunggulan Produk</Label>
                                    <div class="space-y-2 mt-2">
                                        <div v-for="(value, index) in productValues" :key="`value-${index}`" class="flex items-start gap-2">
                                            <div class="flex-1">
                                                <Input
                                                    v-model="value.text"
                                                    type="text"
                                                    placeholder="Masukkan nilai/keunggulan produk"
                                                    class="w-full"
                                                />
                                            </div>
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="icon"
                                                @click="removeValue(index)"
                                                class="h-10 w-10"
                                            >
                                                <X class="w-4 h-4" />
                                            </Button>
                                        </div>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            @click="addValue"
                                            class="mt-2 w-full"
                                        >
                                            Tambah Nilai/Keunggulan
                                        </Button>
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                        Opsional: Tambahkan nilai/keunggulan produk
                                    </p>
                                </div>

                                <Label for="description">Deskripsi</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full h-[400px]"
                                    required
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <Button 
                                type="submit" 
                                :disabled="form.processing || loading"
                                class="cursor-pointer"
                            >
                                <Loader2 v-if="form.processing || loading" class="mr-2 h-4 w-4 animate-spin" />
                                Perbarui Produk
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Dialog Konfirmasi Hapus Gambar -->
        <ConfirmationDialog
            :open="showDeleteConfirm"
            @update:open="showDeleteConfirm = $event"
            title="Konfirmasi Penghapusan Gambar"
            dangerMode
            :icon="Trash2"
            :loading="loading"
            confirmLabel="Hapus"
            @confirm="deleteGalleryImage()"
        >
            <p class="mb-2">Apakah Anda yakin ingin menghapus gambar ini dari galeri produk?</p>
        </ConfirmationDialog>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Loader2, X, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import InputError from '@/components/InputError.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { toast } from 'vue-sonner';
import { Teleport } from 'vue';

// Breadcrumbs untuk navigasi yang digunakan di AppLayout
const breadcrumbs = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Produk',
        href: route('admin.products.index'),
    },
    {
        title: 'Edit Produk',
        href: '#',
    }
];

// Computed property untuk breadcrumb items
const breadcrumbItems = computed(() => {
    return [
        {
            title: 'Dashboard',
            href: route('admin.dashboard'),
        },
        {
            title: 'Produk',
            href: route('admin.products.index'),
        },
        {
            title: 'Edit Produk',
            href: '#',
            current: true
        }
    ];
});

const props = defineProps({
    product: Object,
    categories: {
        type: Array,
        default: () => []
    }
});

// Tambahkan log untuk melihat data produk
console.log("Product data received:", props.product);
console.log("Product features:", props.product.product_features);
console.log("Product values:", props.product.product_values);

// State untuk dialog hapus gambar
const imageToDelete = ref(null);
const showDeleteConfirm = ref(false);
const loading = ref(false);

// Preview gambar utama baru
const featuredImagePreview = ref(null);

// Tambahkan array untuk menangani gambar galeri baru
const newGalleryImages = ref([]);

// State untuk custom select dropdown
const isSelectOpen = ref(false);
const selectRef = ref(null);

// Computed property untuk label kategori terpilih
const selectedCategoryLabel = computed(() => {
    if (!props.categories || !form.category_id) return 'Pilih kategori...';
    const category = props.categories.find(c => c.id === form.category_id);
    return category ? category.name : 'Pilih kategori...';
});

// Toggle dropdown
const toggleSelect = () => {
    isSelectOpen.value = !isSelectOpen.value;
};

// Pilih kategori
const selectCategory = (categoryId) => {
    form.category_id = categoryId;
    isSelectOpen.value = false;
};

// Handle click outside
const handleClickOutside = (event) => {
    if (selectRef.value && !selectRef.value.contains(event.target)) {
        isSelectOpen.value = false;
    }
};

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    selectRef.value = document.querySelector('.custom-select-container');
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Inisialisasi fitur dan nilai produk
const initProductFeatures = () => {
    console.log("Product features from DB:", props.product.product_features);
    let features = [];
    
    // Kasus 1: Array biasa
    if (props.product.product_features && Array.isArray(props.product.product_features)) {
        features = props.product.product_features.map(feature => ({ text: feature }));
    } 
    // Kasus 2: String JSON
    else if (typeof props.product.product_features === 'string' && props.product.product_features) {
        try {
            const parsed = JSON.parse(props.product.product_features);
            if (Array.isArray(parsed)) {
                features = parsed.map(feature => ({ text: feature }));
            }
        } catch (e) {
            console.error("Error parsing features:", e);
        }
    }
    
    // Jika tidak ada data, tambahkan field kosong
    if (features.length === 0) {
        features.push({ text: '' });
    }
    
    return features;
};

const initProductValues = () => {
    console.log("Product values from DB:", props.product.product_values);
    let values = [];
    
    // Kasus 1: Array biasa
    if (props.product.product_values && Array.isArray(props.product.product_values)) {
        values = props.product.product_values.map(value => ({ text: value }));
    } 
    // Kasus 2: String JSON
    else if (typeof props.product.product_values === 'string' && props.product.product_values) {
        try {
            const parsed = JSON.parse(props.product.product_values);
            if (Array.isArray(parsed)) {
                values = parsed.map(value => ({ text: value }));
            }
        } catch (e) {
            console.error("Error parsing values:", e);
        }
    }
    
    // Jika tidak ada data, tambahkan field kosong
    if (values.length === 0) {
        values.push({ text: '' });
    }
    
    return values;
};

const productFeatures = ref(initProductFeatures());
const productValues = ref(initProductValues());

const form = useForm({
    name: props.product.name,
    category_id: props.product.category_id,
    price: props.product.price,
    description: props.product.description,
    featured_image: null,
    gallery: props.product.gallery || [],
    custom_url: props.product.custom_url || '',
    demo_url: props.product.demo_url || '',
    product_code: props.product.product_code,
    is_active: props.product.is_active,
    _method: 'PUT' // Metode HTTP yang benar untuk update
});

// Fungsi untuk preview gambar utama
const handleFeaturedImageChange = (event) => {
    const file = event.target.files[0];
    form.featured_image = file;
    
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            featuredImagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        featuredImagePreview.value = null;
    }
};

// Fungsi untuk menangani penambahan gambar galeri baru
const handleGalleryImagesChange = (event) => {
    if (event.target.files && event.target.files.length > 0) {
        newGalleryImages.value = Array.from(event.target.files);
    }
};

// URLs untuk gambar yang ada
const featuredImageUrl = computed(() => {
    return `/storage/${props.product.featured_image}`;
});

// Fungsi untuk mengelola fitur produk
const addFeature = () => {
    productFeatures.value.push({ text: '' });
};

const removeFeature = (index) => {
    productFeatures.value.splice(index, 1);
    // Pastikan selalu ada minimal satu field
    if (productFeatures.value.length === 0) {
        productFeatures.value.push({ text: '' });
    }
};

// Fungsi untuk mengelola nilai produk
const addValue = () => {
    productValues.value.push({ text: '' });
};

const removeValue = (index) => {
    productValues.value.splice(index, 1);
    // Pastikan selalu ada minimal satu field
    if (productValues.value.length === 0) {
        productValues.value.push({ text: '' });
    }
};

const submit = () => {
    loading.value = true;
    
    // Debug log
    console.log('Form is_active sebelum dikirim:', form.is_active, typeof form.is_active);
    console.log('Form custom_url sebelum dikirim:', form.custom_url);
    
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("name", form.name);
    formData.append("description", form.description);
    formData.append("price", form.price);
    
    // Hanya kirim custom_url jika memiliki nilai (tidak kosong)
    // Ini akan membantu controller untuk mengetahui bahwa field memang dikirim
    if (form.custom_url !== null && form.custom_url !== undefined && form.custom_url.trim() !== '') {
        formData.append("custom_url", form.custom_url.trim());
    } else {
        // Jika kosong, kirim empty string agar controller tahu field ini dikirim tapi kosong
        // Sehingga controller bisa mempertahankan nilai lama
        formData.append("custom_url", '');
    }
    
    // Kirim demo_url jika ada
    if (form.demo_url) {
        formData.append("demo_url", form.demo_url);
    }
    
    // Pastikan is_active selalu disertakan dan konversi ke string "1" atau "0"
    // FormData hanya menerima string, jadi kita perlu mengkonversi Boolean
    const isActiveValue = form.is_active ? "1" : "0";
    formData.append("is_active", isActiveValue);
    
    if (form.category_id) {
        formData.append("category_id", form.category_id);
    }
    
    if (form.featured_image && typeof form.featured_image !== 'string') {
        formData.append("featured_image", form.featured_image);
    }
    
    if (newGalleryImages.value.length > 0) {
        newGalleryImages.value.forEach((image) => {
            formData.append("gallery[]", image);
        });
    }
    
    // Tambahkan gallery_ids yang akan dipertahankan
    form.gallery.forEach((item) => {
        formData.append("gallery_ids[]", item.id);
    });
    
    // Menambahkan product_features dan product_values sebagai JSON
    if (productFeatures.value.length > 0) {
        const features = productFeatures.value.filter(f => f.text.trim() !== '').map(f => f.text);
        formData.append("product_features", JSON.stringify(features));
    }
    
    if (productValues.value.length > 0) {
        const values = productValues.value.filter(v => v.text.trim() !== '').map(v => v.text);
        formData.append("product_values", JSON.stringify(values));
    }
    
    // Debug log untuk memeriksa isi formData
    console.log('Kirim nilai is_active:', isActiveValue);
    
    router.post(route("admin.products.update", props.product.id), formData, {
        onSuccess: () => {
            loading.value = false;
            toast.success("Produk berhasil diperbarui");
            newGalleryImages.value = [];
            showDeleteConfirm.value = false;
            imageToDelete.value = null;
        },
        onError: (errors) => {
            loading.value = false;
            toast.error("Gagal memperbarui produk");
            console.error(errors);
        },
        forceFormData: true,
    });
};

const showDeleteGalleryDialog = (image) => {
    imageToDelete.value = image;
    showDeleteConfirm.value = true;
};

const deleteGalleryImage = () => {
    if (!imageToDelete.value) return;
    
    loading.value = true;
    
    router.delete(route('admin.products.gallery.delete', imageToDelete.value.id), {
        preserveScroll: true,
        only: ['product'], // Hanya refresh data product
        onSuccess: () => {
            toast.success('Berhasil', {
                description: 'Gambar berhasil dihapus dari galeri',
            });
            showDeleteConfirm.value = false;
        },
        onError: (errors) => {
            toast.error('Gagal', {
                description: `Terjadi kesalahan saat menghapus gambar: ${errors.message || 'Unknown error'}`,
            });
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};
</script>

<style>
/* Reset previous select styling */
.select-wrapper, select {
  display: none;
}

/* Custom select styling */
.custom-select-container {
  position: relative;
  width: 100%;
  border-radius: 0.375rem;
}

.custom-select-dropdown {
  position: absolute;
  top: calc(100%);
  left: 0;
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  animation: slideDown 0.15s ease-out;
  z-index: 50;
}

.custom-select-option:first-child {
  border-top-left-radius: 0.375rem;
  border-top-right-radius: 0.375rem;
}

.custom-select-option:last-child {
  border-bottom-left-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Perbaikan outline saat fokus */
.custom-select-trigger {
  outline: none !important;
  -webkit-appearance: none;
  -webkit-tap-highlight-color: transparent !important;
}

.custom-select-trigger:focus,
.custom-select-trigger:focus-visible,
.custom-select-trigger:active,
.custom-select-trigger:hover,
.custom-select-trigger:-moz-focusring {
  outline: none !important;
  box-shadow: none !important;
  border-color: #0ea5e9 !important;
}

/* Fix untuk Firefox */
.custom-select-trigger:-moz-focusring {
  outline: none !important;
}

/* Fix untuk Safari dan Chrome */
.custom-select-trigger::-webkit-focus-inner {
  border: 0;
}

/* Fix tambahan untuk Chrome */
*:focus {
  outline-color: transparent !important;
}
</style> 