<script setup lang="ts">
import { defineComponent as _defineComponent } from "vue";
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import {
    ShoppingBag, FileText, Download, Clock, ChevronLeft,
    Calendar, AlertCircle, Eye, BookOpen, CheckCircle,
    Key, RefreshCw, Globe
} from 'lucide-vue-next';

interface Document {
    id: number;
    type: string;
    title: string;
    content: string;
    file_path: string | null;
    expires_at: string | null;
    is_read: boolean;
    read_at: string | null;
    type_label: string;
    type_color: string;
    type_icon: string;
}

interface Product {
    id: number;
    name: string;
    description: string;
    featured_image: string;
    product_code: string;
    product_features: string[];
    product_values: string[];
}

interface Order {
    id: number;
    order_number: string;
    purchased_at: string;
}

interface ProductProps {
    product: Product;
    order: Order;
    is_expired: boolean;
    remaining_days: number | null;
    documents: Document[];
}

const props = defineProps<ProductProps>();

// Format date
const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
};

// Icon component mapping
const iconMapping: { [key: string]: any } = {
    'Key': Key,
    'Globe': Globe,
    'RefreshCw': RefreshCw,
    'Download': Download,
    'File': FileText
};

// Get icon component by name
const getIconComponent = (iconName: string) => {
    return iconMapping[iconName] || FileText;
};
</script>

<template>
    <Head :title="`Produk Digital - ${product.name}`" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <!-- Breadcrumb & Back Button -->
            <div class="flex items-center space-x-2">
                <Link :href="route('dashboard.my-products.index')" class="inline-flex items-center text-sm font-medium text-primary hover:underline">
                    <ChevronLeft class="h-4 w-4 mr-1" />
                    Kembali ke Produk Saya
                </Link>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- Produk Information -->
                <div class="md:col-span-2">
                    <Card>
                        <CardHeader>
                            <div class="flex justify-between items-start">
                                <div>
                                    <CardTitle class="text-2xl font-bold">{{ product.name }}</CardTitle>
                                    <CardDescription>
                                        <div class="flex items-center mt-1">
                                            <ShoppingBag class="h-4 w-4 mr-1" />
                                            <span>Order #{{ order.order_number }}</span>
                                        </div>
                                        <div class="flex items-center mt-1">
                                            <Calendar class="h-4 w-4 mr-1" />
                                            <span>Dibeli pada {{ formatDate(order.purchased_at) }}</span>
                                        </div>
                                    </CardDescription>
                                </div>
                                
                                <Badge :variant="is_expired ? 'destructive' : 'default'" class="ml-4">
                                    <Clock class="h-3 w-3 mr-1" />
                                    {{ is_expired ? 'Akses Kedaluwarsa' : (remaining_days ? `${remaining_days} hari tersisa` : 'Akses Selamanya') }}
                                </Badge>
                            </div>
                        </CardHeader>
                        
                        <CardContent>
                            <div class="md:flex-1">
                                <div class="aspect-[16/9] overflow-hidden rounded-md mb-4">
                                    <img 
                                        v-if="product.featured_image" 
                                        :src="product.featured_image" 
                                        :alt="product.name" 
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <BookOpen class="h-12 w-12 text-gray-400" />
                                    </div>
                                </div>
                                
                                <div>
                                    <h3 class="font-semibold mb-2">Deskripsi Produk</h3>
                                    <p class="text-gray-700 dark:text-gray-300">{{ product.description }}</p>
                                </div>
                                
                                <div v-if="product.product_features && product.product_features.length > 0" class="mt-6">
                                    <h3 class="font-semibold mb-2">Fitur Produk</h3>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li v-for="(feature, index) in product.product_features" :key="index" class="text-gray-700 dark:text-gray-300">
                                            {{ feature }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Access & Downloads -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle>Akses & Unduhan</CardTitle>
                            <CardDescription>
                                Dokumen dan tautan akses produk Anda
                            </CardDescription>
                        </CardHeader>
                        
                        <CardContent>
                            <div v-if="is_expired" class="p-4 bg-red-50 dark:bg-red-900/20 rounded-md mb-4">
                                <div class="flex items-center">
                                    <AlertCircle class="h-5 w-5 text-red-500 mr-2" />
                                    <p class="text-sm font-medium text-red-500">Akses Anda ke produk ini telah kedaluwarsa</p>
                                </div>
                            </div>
                            
                            <div v-else-if="!is_expired && documents.length === 0" class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-md mb-4">
                                <div class="flex items-center">
                                    <AlertCircle class="h-5 w-5 text-yellow-500 mr-2" />
                                    <p class="text-sm font-medium text-yellow-500">Unduhan produk sedang diproses oleh admin</p>
                                </div>
                            </div>
                            
                            <div v-else-if="!is_expired && documents.length > 0" class="space-y-4">
                                <div v-for="document in documents" :key="document.id" class="border rounded-md p-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-start">
                                            <div :class="`text-${document.type_color}-500 bg-${document.type_color}-50 dark:bg-${document.type_color}-900/20 p-2 rounded-md mr-3`">
                                                <component :is="getIconComponent(document.type_icon)" class="h-5 w-5" />
                                            </div>
                                            <div>
                                                <h4 class="font-medium">{{ document.title }}</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ document.content }}</p>
                                                
                                                <div v-if="document.is_read" class="flex items-center mt-2 text-xs text-gray-500">
                                                    <CheckCircle class="h-3 w-3 mr-1" />
                                                    Sudah dibuka
                                                </div>
                                                
                                                <div v-if="document.expires_at" class="flex items-center mt-2 text-xs text-gray-500">
                                                    <Clock class="h-3 w-3 mr-1" />
                                                    Kedaluwarsa: {{ formatDate(document.expires_at) }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <Link 
                                            v-if="document.file_path" 
                                            :href="route('dashboard.documents.download', document.id)"
                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2"
                                        >
                                            <Download class="h-4 w-4 mr-2" />
                                            Unduh
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 