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

interface Document {
    id: number;
    title: string;
    type: string;
    file_path: string;
    expires_at: string | null;
    is_read: boolean;
}

interface Product {
    id: number;
    name: string;
    description: string;
    featured_image: string;
    product_code: string;
    order_id: number;
    order_number: string;
    purchased_at: string;
    is_expired: boolean;
    remaining_days: number | null;
    documents: Document[];
    has_documents: boolean;
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
            <div v-if="myProducts && myProducts.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="product in myProducts" :key="`${product.order_id}-${product.id}`" class="overflow-hidden">
                    <div class="aspect-[16/9] overflow-hidden">
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
                    
                    <CardHeader>
                        <CardTitle class="line-clamp-2">{{ product.name }}</CardTitle>
                        <CardDescription>
                            <Badge :variant="product.is_expired ? 'destructive' : 'default'" class="mb-2">
                                <Clock class="h-3 w-3 mr-1" />
                                {{ product.is_expired ? 'Akses Kedaluwarsa' : (product.remaining_days ? `${product.remaining_days} hari tersisa` : 'Akses Selamanya') }}
                            </Badge>
                            
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center mb-1">
                                    <Calendar class="h-3 w-3 mr-1" />
                                    Dibeli pada {{ formatDate(product.purchased_at) }}
                                </div>
                                <div class="flex items-center">
                                    <ShoppingBag class="h-3 w-3 mr-1" />
                                    Order #{{ product.order_number }}
                                </div>
                            </div>
                        </CardDescription>
                    </CardHeader>
                    
                    <CardContent>
                        <p class="text-sm line-clamp-3">{{ product.description }}</p>
                    </CardContent>
                    
                    <CardFooter class="flex flex-col space-y-2">
                        <Link 
                            v-if="!product.is_expired"
                            :href="route('dashboard.my-products.show', [product.order_id, product.id])" 
                            class="w-full"
                        >
                            <Button class="w-full" variant="default">
                                <Eye class="h-4 w-4 mr-2" />
                                Lihat Produk
                            </Button>
                        </Link>
                        
                        <Button v-else disabled class="w-full" variant="outline">
                            <AlertCircle class="h-4 w-4 mr-2" />
                            Akses Kedaluwarsa
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