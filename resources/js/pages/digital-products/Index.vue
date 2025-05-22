<script setup lang="ts">
import { defineComponent as _defineComponent } from "vue";
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
    ShoppingBag, FileText, Download, Clock, ChevronRight,
    Calendar, AlertCircle, Eye, BookOpen
} from 'lucide-vue-next';

interface Product {
    id: number;
    order_id: number;
    order_number: string;
    product_id: number;
    product_name: string;
    product_image: string;
    product_description: string;
    access_code: string;
    is_expired: boolean;
    is_active: boolean;
    has_reached_limit: boolean;
    is_accessible: boolean;
    download_count: number;
    max_downloads: number | null;
    days_remaining: number | null;
    expires_at: string | null;
    purchased_at: string;
    last_accessed: string | null;
    has_digital_file: boolean;
}

interface ProductsProps {
    myProducts: Product[];
}

const props = defineProps<ProductsProps>();

// Format date
const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
};

// Get badge status
const getAccessStatus = (product: Product): { text: string; variant: string } => {
    if (!product.is_active) {
        return { text: 'Tidak Aktif', variant: 'destructive' };
    }
    
    if (product.is_expired) {
        return { text: 'Kedaluwarsa', variant: 'destructive' };
    }
    
    if (product.has_reached_limit) {
        return { text: 'Batas Unduhan Tercapai', variant: 'warning' };
    }
    
    if (product.days_remaining !== null) {
        return { text: `${product.days_remaining} Hari Tersisa`, variant: 'default' };
    }
    
    return { text: 'Akses Selamanya', variant: 'success' };
};
</script>

<template>
    <Head title="Produk Digital Saya" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold">Produk Digital Saya</h1>
                <p class="text-gray-500 dark:text-gray-400">
                    Akses semua produk digital yang telah Anda beli
                </p>
            </div>

            <!-- Produk Digital List -->
            <div v-if="myProducts && myProducts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <Card v-for="product in myProducts" :key="product.id" class="overflow-hidden flex flex-col">
                    <div class="h-48 overflow-hidden">
                        <img 
                            v-if="product.product_image" 
                            :src="product.product_image" 
                            :alt="product.product_name" 
                            class="w-full h-full object-cover object-center"
                        />
                        <div v-else class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <BookOpen class="h-12 w-12 text-gray-400" />
                        </div>
                    </div>
                    
                    <CardHeader class="p-4 pb-2">
                        <CardTitle class="line-clamp-2 text-lg">{{ product.product_name }}</CardTitle>
                        <CardDescription>
                            <Badge 
                                :variant="getAccessStatus(product).variant" 
                                class="mb-2"
                            >
                                <Clock class="h-3 w-3 mr-1" />
                                {{ getAccessStatus(product).text }}
                            </Badge>
                            
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center mb-1">
                                    <Calendar class="h-3 w-3 mr-1 flex-shrink-0" />
                                    <span class="truncate">{{ formatDate(product.purchased_at) }}</span>
                                </div>
                                <div class="flex items-center">
                                    <ShoppingBag class="h-3 w-3 mr-1 flex-shrink-0" />
                                    <span class="truncate">Order #{{ product.order_number }}</span>
                                </div>
                                <div v-if="product.max_downloads" class="flex items-center mt-1">
                                    <Download class="h-3 w-3 mr-1 flex-shrink-0" />
                                    <span class="truncate">Unduhan: {{ product.download_count }} / {{ product.max_downloads }}</span>
                                </div>
                            </div>
                        </CardDescription>
                    </CardHeader>
                    
                    <CardContent class="p-4 pt-0 pb-2 flex-grow">
                        <p class="text-sm line-clamp-2">{{ product.product_description }}</p>
                    </CardContent>
                    
                    <CardFooter class="p-4 pt-2 flex flex-col space-y-2">
                        <Link 
                            v-if="product.is_accessible"
                            :href="`/digital-products/${product.id}`" 
                            class="w-full"
                        >
                            <Button class="w-full" variant="default">
                                <Eye class="h-4 w-4 mr-2" />
                                Lihat Produk
                            </Button>
                        </Link>
                        
                        <Link 
                            v-if="product.is_accessible && product.has_digital_file"
                            :href="route('digital-products.download', product.id)" 
                            class="w-full"
                        >
                            <Button class="w-full" variant="outline">
                                <Download class="h-4 w-4 mr-2" />
                                Unduh Langsung
                            </Button>
                        </Link>
                        
                        <Button v-else-if="!product.is_accessible" disabled class="w-full" variant="outline">
                            <AlertCircle class="h-4 w-4 mr-2" />
                            {{ getAccessStatus(product).text }}
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <div v-else class="flex flex-col items-center justify-center py-8">
                <ShoppingBag class="h-16 w-16 text-gray-400 mb-4" />
                <h2 class="text-lg font-medium mb-2">Belum Ada Produk Digital</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-4 text-center">
                    Anda belum memiliki produk digital. Silakan beli produk digital untuk mengaksesnya di sini.
                </p>
                <Link href="/products">
                    <Button>
                        <ShoppingBag class="h-4 w-4 mr-2" />
                        Lihat Produk
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template> 