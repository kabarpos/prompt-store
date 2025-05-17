<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'trigger_status',
        'message_template',
        'description',
        'is_active',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Constant untuk nilai tipe template
     */
    const TYPE_CUSTOMER = 'customer';
    const TYPE_ADMIN = 'admin';

    /**
     * Constant untuk nilai trigger status
     */
    const TRIGGER_NEW_ORDER = 'new_order';
    const TRIGGER_PENDING = 'pending';
    const TRIGGER_PROCESSING = 'processing';
    const TRIGGER_REVIEW = 'review';
    const TRIGGER_COMPLETED = 'completed';
    const TRIGGER_CANCELLED = 'cancelled';
    const TRIGGER_PAYMENT_CONFIRMED = 'payment_confirmed';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'whatsapp_templates';

    /**
     * Daftar variabel yang tersedia untuk template
     */
    public static function getAvailableVariables(): array
    {
        return [
            '{order_number}' => 'Nomor pesanan',
            '{order_date}' => 'Tanggal pesanan',
            '{customer_name}' => 'Nama pelanggan',
            '{total_amount}' => 'Total belanja',
            '{payment_method}' => 'Metode pembayaran',
            '{status}' => 'Status pesanan',
            '{items_list}' => 'Daftar item pesanan',
            '{subtotal}' => 'Subtotal belanja',
            '{admin_fee}' => 'Biaya admin',
            '{discount}' => 'Diskon',
        ];
    }

    /**
     * Mengisi variabel template dengan data pesanan aktual
     */
    public function parseTemplate(Order $order): string
    {
        $template = $this->message_template;
        
        // Format list item order
        $itemsList = $order->items->map(function ($item) {
            return "- {$item->quantity}x {$item->product_name}: Rp " . number_format($item->subtotal, 0, ',', '.');
        })->join("\n");
        
        // Format status yang mudah dibaca
        $statusMap = [
            Order::STATUS_PENDING => 'Menunggu Pembayaran',
            Order::STATUS_PROCESSING => 'Sedang Diproses',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
            Order::STATUS_REVIEW => 'Menunggu Review',
        ];
        
        $readableStatus = $statusMap[$order->status] ?? $order->status;
        
        // Nilai untuk variabel yang tersedia
        $replacements = [
            '{order_number}' => $order->order_number,
            '{order_date}' => $order->created_at->format('d/m/Y H:i'),
            '{customer_name}' => $order->customer_name,
            '{total_amount}' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
            '{payment_method}' => $order->payment->paymentMethod->name ?? 'Belum dipilih',
            '{status}' => $readableStatus,
            '{items_list}' => $itemsList,
            '{subtotal}' => 'Rp ' . number_format($order->subtotal, 0, ',', '.'),
            '{admin_fee}' => 'Rp ' . number_format($order->admin_fee, 0, ',', '.'),
            '{discount}' => 'Rp ' . number_format($order->discount, 0, ',', '.'),
        ];
        
        // Ganti semua variabel dalam template
        foreach ($replacements as $var => $value) {
            $template = str_replace($var, $value, $template);
        }
        
        return $template;
    }
    
    /**
     * Mendapatkan template berdasarkan tipe dan trigger status
     */
    public static function getTemplate(string $type, string $triggerStatus)
    {
        return self::where('type', $type)
            ->where('trigger_status', $triggerStatus)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get the logs for the template.
     */
    public function logs()
    {
        // Karena kita sudah tidak menggunakan template_id,
        // kita akan mengambil log berdasarkan message yang cocok dengan template
        return WhatsAppLog::where('message', 'LIKE', '%' . $this->message_template . '%')
            ->orderBy('created_at', 'desc');
    }
}
