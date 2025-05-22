<script setup lang="ts">
import { defineComponent as _defineComponent } from "vue";
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
    ShoppingBag, FileText, Download, Clock, ChevronLeft,
    Calendar, AlertCircle, Eye, BookOpen, CheckCircle, Copy
} from 'lucide-vue-next';

interface DigitalAccess {
    id: number;
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
}

interface PromptItem {
    title: string;
    content: string;
}

interface Product {
    id: number;
    name: string;
    description: string;
    featured_image: string;
    product_features: string[];
    has_hidden_content: boolean;
    hidden_content: PromptItem[] | string;
    has_digital_file: boolean;
}

interface Order {
    id: number;
    order_number: string;
}

interface ProductProps {
    digitalAccess: DigitalAccess;
    product: Product;
    order: Order;
}

const props = defineProps<ProductProps>();

// Format date
const formatDate = (dateString: string): string => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
};

// Get badge status
const getAccessStatus = (): { text: string; variant: string } => {
    if (!props.digitalAccess.is_active) {
        return { text: 'Tidak Aktif', variant: 'destructive' };
    }
    
    if (props.digitalAccess.is_expired) {
        return { text: 'Kedaluwarsa', variant: 'destructive' };
    }
    
    if (props.digitalAccess.has_reached_limit) {
        return { text: 'Batas Unduhan Tercapai', variant: 'warning' };
    }
    
    if (props.digitalAccess.days_remaining !== null) {
        return { text: `${props.digitalAccess.days_remaining} Hari Tersisa`, variant: 'default' };
    }
    
    return { text: 'Akses Selamanya', variant: 'success' };
};

// Copy to clipboard
const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text).then(() => {
        alert('Prompt berhasil disalin!');
    }).catch(err => {
        console.error('Gagal menyalin teks: ', err);
    });
};

// Check if hidden_content is array
const isHiddenContentArray = (): boolean => {
    if (!props.product.hidden_content) return false;
    
    // Check if it's already parsed as array
    if (Array.isArray(props.product.hidden_content)) return true;
    
    // Try to parse if it's JSON string
    if (typeof props.product.hidden_content === 'string') {
        try {
            const parsed = JSON.parse(props.product.hidden_content);
            return Array.isArray(parsed);
        } catch (e) {
            return false;
        }
    }
    
    return false;
};

// Get parsed prompts
const getPrompts = (): PromptItem[] => {
    if (!props.product.hidden_content) return [];
    
    if (Array.isArray(props.product.hidden_content)) {
        return props.product.hidden_content;
    }
    
    if (typeof props.product.hidden_content === 'string') {
        try {
            const parsed = JSON.parse(props.product.hidden_content);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            // If can't parse as JSON, treat as single prompt
            return [{ title: 'Prompt', content: props.product.hidden_content }];
        }
    }
    
    return [];
};
</script>

<template>
    <Head :title="`Produk Digital - ${product.name}`" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <!-- Breadcrumb & Back Button -->
            <div class="flex items-center space-x-2 mb-2">
                <Link href="/digital-products" class="inline-flex items-center text-sm font-medium text-primary hover:underline">
                    <ChevronLeft class="h-4 w-4 mr-1" />
                    Kembali ke Produk Digital
                </Link>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- Produk Information -->
                <div class="md:col-span-2">
                    <Card>
                        <CardHeader>
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <CardTitle class="text-xl md:text-2xl font-bold">{{ product.name }}</CardTitle>
                                    <CardDescription>
                                        <div class="flex items-center mt-1">
                                            <ShoppingBag class="h-4 w-4 mr-1 flex-shrink-0" />
                                            <span>Order #{{ order.order_number }}</span>
                                        </div>
                                        <div class="flex items-center mt-1">
                                            <Calendar class="h-4 w-4 mr-1 flex-shrink-0" />
                                            <span>Dibeli pada {{ formatDate(digitalAccess.purchased_at) }}</span>
                                        </div>
                                    </CardDescription>
                                </div>
                                
                                <Badge :variant="getAccessStatus().variant" class="ml-4 flex-shrink-0">
                                    <Clock class="h-3 w-3 mr-1" />
                                    {{ getAccessStatus().text }}
                                </Badge>
                            </div>
                        </CardHeader>
                        
                        <CardContent>
                            <!-- Hidden Content Section -->
                            <div v-if="product.has_hidden_content && product.hidden_content && digitalAccess.is_accessible" class="p-4 border border-primary/20 bg-primary/5 rounded-md">
                                <div class="flex items-center mb-3">
                                    <Eye class="h-5 w-5 text-primary mr-2" />
                                    <h3 class="font-semibold">Koleksi Prompt</h3>
                                </div>
                                
                                <!-- Tampilkan Prompts sebagai Repeater -->
                                <div v-if="isHiddenContentArray()" class="space-y-6">
                                    <div v-for="(prompt, index) in getPrompts()" :key="`prompt-${index}`" class="border border-primary/10 p-4 rounded-md bg-white dark:bg-gray-800/50">
                                        <h4 class="font-medium text-lg mb-2">{{ prompt.title }}</h4>
                                        <div class="prose prose-sm dark:prose-invert max-w-none" v-html="prompt.content"></div>
                                        
                                        <div class="flex justify-end mt-3">
                                            <Button 
                                                variant="outline" 
                                                size="sm"
                                                @click="copyToClipboard(prompt.content)"
                                            >
                                                <Copy class="h-3.5 w-3.5 mr-1" />
                                                Salin Prompt
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Fallback untuk format lama -->
                                <div v-else class="prose prose-sm dark:prose-invert max-w-none" v-html="product.hidden_content"></div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle>Akses Cepat</CardTitle>
                            <CardDescription>
                                Tindakan untuk produk digital ini
                            </CardDescription>
                        </CardHeader>
                        
                        <CardContent>
                            <div v-if="!digitalAccess.is_accessible" class="p-4 bg-red-50 dark:bg-red-900/20 rounded-md mb-4">
                                <div class="flex items-center">
                                    <AlertCircle class="h-5 w-5 text-red-500 mr-2" />
                                    <p class="text-sm font-medium text-red-500">{{ getAccessStatus().text }}</p>
                                </div>
                            </div>
                            
                            <!-- Detail Akses Card -->
                            <div class="border rounded-md p-4 mb-4">
                                <h3 class="font-medium mb-2">Detail Akses</h3>
                                
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Kode Akses:</span>
                                        <span class="font-mono">{{ digitalAccess.access_code }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Status:</span>
                                        <Badge :variant="getAccessStatus().variant">{{ getAccessStatus().text }}</Badge>
                                    </div>
                                    
                                    <div v-if="digitalAccess.max_downloads" class="flex justify-between">
                                        <span class="text-gray-500">Unduhan:</span>
                                        <span>{{ digitalAccess.download_count }} / {{ digitalAccess.max_downloads }}</span>
                                    </div>
                                    
                                    <div v-if="digitalAccess.expires_at" class="flex justify-between">
                                        <span class="text-gray-500">Kedaluwarsa:</span>
                                        <span>{{ formatDate(digitalAccess.expires_at) }}</span>
                                    </div>
                                    
                                    <div v-if="digitalAccess.last_accessed" class="flex justify-between">
                                        <span class="text-gray-500">Terakhir Diakses:</span>
                                        <span>{{ formatDate(digitalAccess.last_accessed) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col space-y-3">
                                <Link 
                                    v-if="digitalAccess.is_accessible && product.has_digital_file"
                                    :href="route('digital-products.download', digitalAccess.id)"
                                    class="w-full"
                                >
                                    <Button class="w-full" variant="default">
                                        <Download class="h-4 w-4 mr-2" />
                                        Unduh Produk
                                    </Button>
                                </Link>

                                <Link 
                                    :href="route('digital-products.index')"
                                    class="w-full"
                                >
                                    <Button class="w-full" variant="outline">
                                        <ChevronLeft class="h-4 w-4 mr-2" />
                                        Semua Produk Digital 
                                    </Button>
                                </Link>
                                
                                <Link 
                                    :href="route('orders.show', order.id)"
                                    class="w-full"
                                >
                                    <Button class="w-full" variant="outline">
                                        <ShoppingBag class="h-4 w-4 mr-2" />
                                        Lihat Order
                                    </Button>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 