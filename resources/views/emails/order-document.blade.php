<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #e9ecef;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .btn {
            display: inline-block;
            background-color: #4361ee;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
        .important-box {
            background-color: #f8f9fa;
            border-left: 4px solid #4361ee;
            padding: 15px;
            margin-bottom: 20px;
        }
        .expiry-info {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin-top: 20px;
            font-size: 14px;
        }
        h1 {
            color: #4361ee;
            margin: 0;
        }
        h2 {
            color: #495057;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $document->title }}</h1>
            <p>Untuk Pesanan #{{ $document->order->order_number }}</p>
        </div>
        
        <div class="content">
            <p>Halo {{ $document->order->user_id ? $document->order->user->name : $document->order->customer_name }},</p>
            
            <p>Berikut adalah informasi penting terkait dengan pesanan Anda.</p>
            
            @if($document->type == \App\Models\OrderDocument::TYPE_CREDENTIAL)
                <div class="important-box">
                    <h2>Informasi Login</h2>
                    {!! nl2br(e($document->content)) !!}
                </div>
                
                <p>Silakan gunakan informasi ini untuk login ke sistem. Untuk keamanan, kami sarankan untuk segera mengubah password setelah login pertama.</p>
            @elseif($document->type == \App\Models\OrderDocument::TYPE_DOMAIN)
                <div class="important-box">
                    <h2>Informasi Domain</h2>
                    {!! nl2br(e($document->content)) !!}
                </div>
                
                @if($document->expires_at)
                    <div class="expiry-info">
                        <strong>Informasi Masa Aktif:</strong> {{ $document->expiry_status }}
                    </div>
                @endif
            @elseif($document->type == \App\Models\OrderDocument::TYPE_UPDATE)
                <div class="important-box">
                    <h2>Pembaruan</h2>
                    {!! nl2br(e($document->content)) !!}
                </div>
            @elseif($document->type == \App\Models\OrderDocument::TYPE_DOWNLOAD)
                <div class="important-box">
                    <h2>Link Unduhan</h2>
                    {!! nl2br(e($document->content)) !!}
                </div>
                
                @if($document->file_path)
                    <p>Kami juga telah melampirkan file yang diperlukan dalam email ini.</p>
                @endif
            @else
                <div class="important-box">
                    <h2>Informasi</h2>
                    {!! nl2br(e($document->content)) !!}
                </div>
            @endif
            
            <p>Jika Anda memiliki pertanyaan, silakan hubungi tim dukungan kami.</p>
            
            <a href="{{ route('orders.documents.index', $document->order_id) }}" class="btn">Lihat Detail di Akun Anda</a>
        </div>
        
        <div class="footer">
            <p>
                <a href="{{ $documentUrl }}" style="color: #4361ee; text-decoration: underline;">Lihat Dokumen</a> | 
                <a href="{{ $allDocumentsUrl }}" style="color: #4361ee; text-decoration: underline;">Semua Dokumen Saya</a>
            </p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.</p>
            <p>Email ini dikirim secara otomatis, mohon jangan membalas email ini.</p>
        </div>
    </div>
</body>
</html> 