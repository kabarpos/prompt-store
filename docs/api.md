# Dokumentasi API Prompt Store

## Gambaran Umum

API Prompt Store menyediakan akses programatik ke data dan fungsionalitas aplikasi Prompt Store. API ini menggunakan standar RESTful dan mengembalikan data dalam format JSON.

## Autentikasi

API menggunakan autentikasi berbasis token. Untuk mengakses endpoint yang memerlukan autentikasi, sertakan token dalam header permintaan:

```
Authorization: Bearer {your_api_token}
```

## Endpoint API

### Produk

#### Mendapatkan Daftar Produk

```
GET /api/products
```

Parameter query:

- `page` (opsional): Nomor halaman untuk paginasi
- `per_page` (opsional): Jumlah item per halaman
- `category_id` (opsional): Filter berdasarkan kategori
- `search` (opsional): Kata kunci pencarian

Contoh respons:

```json
{
  "data": [
    {
      "id": 1,
      "name": "Prompt AI Writer",
      "slug": "prompt-ai-writer",
      "price": 99000,
      "description": "Prompt untuk menulis konten dengan AI",
      "featured_image_url": "/storage/products/prompt-ai-writer.jpg",
      "is_digital": true
    },
    {
      "id": 2,
      "name": "Prompt SEO Expert",
      "slug": "prompt-seo-expert",
      "price": 149000,
      "description": "Prompt untuk optimasi SEO",
      "featured_image_url": "/storage/products/prompt-seo-expert.jpg",
      "is_digital": true
    }
  ],
  "links": {
    "first": "http://example.com/api/products?page=1",
    "last": "http://example.com/api/products?page=5",
    "prev": null,
    "next": "http://example.com/api/products?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "path": "http://example.com/api/products",
    "per_page": 15,
    "to": 15,
    "total": 75
  }
}
```

#### Mendapatkan Detail Produk

```
GET /api/products/{id_or_slug}
```

Contoh respons:

```json
{
  "data": {
    "id": 1,
    "name": "Prompt AI Writer",
    "slug": "prompt-ai-writer",
    "category": {
      "id": 3,
      "name": "Writing Tools",
      "slug": "writing-tools"
    },
    "price": 99000,
    "price_formatted": "Rp99.000",
    "description": "Prompt untuk menulis konten dengan AI",
    "product_features": [
      "Menulis artikel blog",
      "Membuat caption sosial media",
      "Membuat email marketing"
    ],
    "product_values": [
      "Menghemat waktu penulisan",
      "Meningkatkan engagement"
    ],
    "featured_image_url": "/storage/products/prompt-ai-writer.jpg",
    "gallery": [
      {
        "id": 1,
        "image_url": "/storage/products/gallery/prompt-ai-writer-1.jpg"
      },
      {
        "id": 2,
        "image_url": "/storage/products/gallery/prompt-ai-writer-2.jpg"
      }
    ],
    "is_digital": true,
    "demo_url": "https://example.com/demo/prompt-ai-writer",
    "created_at": "2023-01-15T08:30:00.000000Z",
    "updated_at": "2023-02-20T10:15:00.000000Z"
  }
}
```

### Kategori

#### Mendapatkan Daftar Kategori

```
GET /api/categories
```

Contoh respons:

```json
{
  "data": [
    {
      "id": 1,
      "name": "Business",
      "slug": "business",
      "product_count": 15
    },
    {
      "id": 2,
      "name": "Marketing",
      "slug": "marketing",
      "product_count": 12
    },
    {
      "id": 3,
      "name": "Writing Tools",
      "slug": "writing-tools",
      "product_count": 8
    }
  ]
}
```

### Pesanan (Memerlukan Autentikasi)

#### Mendapatkan Daftar Pesanan

```
GET /api/orders
```

Contoh respons:

```json
{
  "data": [
    {
      "id": 1,
      "order_number": "ORD202305150001",
      "status": "completed",
      "status_label": "Selesai",
      "total_amount": 99000,
      "created_at": "2023-05-15T14:30:00.000000Z"
    },
    {
      "id": 2,
      "order_number": "ORD202305200002",
      "status": "pending",
      "status_label": "Menunggu Konfirmasi",
      "total_amount": 149000,
      "created_at": "2023-05-20T09:15:00.000000Z"
    }
  ],
  "links": {
    "first": "http://example.com/api/orders?page=1",
    "last": "http://example.com/api/orders?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://example.com/api/orders",
    "per_page": 15,
    "to": 2,
    "total": 2
  }
}
```

#### Mendapatkan Detail Pesanan

```
GET /api/orders/{id}
```

Contoh respons:

```json
{
  "data": {
    "id": 1,
    "order_number": "ORD202305150001",
    "status": "completed",
    "status_label": "Selesai",
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "customer_phone": "08123456789",
    "subtotal": 99000,
    "discount": 0,
    "admin_fee": 0,
    "total_amount": 99000,
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Prompt AI Writer",
        "price": 99000,
        "quantity": 1,
        "subtotal": 99000
      }
    ],
    "payment": {
      "id": 1,
      "payment_method": "bank_transfer",
      "payment_status": "paid",
      "paid_at": "2023-05-15T15:30:00.000000Z"
    },
    "created_at": "2023-05-15T14:30:00.000000Z",
    "updated_at": "2023-05-15T15:30:00.000000Z"
  }
}
```

#### Membuat Pesanan Baru

```
POST /api/orders
```

Parameter permintaan:

```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 1
    }
  ],
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "08123456789",
  "coupon_code": "DISCOUNT10" // opsional
}
```

### Kode Status HTTP

- `200 OK`: Permintaan berhasil
- `201 Created`: Sumber daya berhasil dibuat
- `400 Bad Request`: Permintaan tidak valid
- `401 Unauthorized`: Autentikasi diperlukan
- `403 Forbidden`: Tidak memiliki izin
- `404 Not Found`: Sumber daya tidak ditemukan
- `422 Unprocessable Entity`: Validasi gagal
- `429 Too Many Requests`: Terlalu banyak permintaan
- `500 Internal Server Error`: Kesalahan server

## Batasan Rate Limit

API memiliki batasan 60 permintaan per menit per IP. Jika melebihi batas, akan menerima respons dengan kode status 429.

## Kontak

Jika Anda memiliki pertanyaan atau masalah terkait API, silakan hubungi tim dukungan kami di api-support@example.com. 