# 🛒 Microservices Order Service & E-Commerce System

## 📌 Deskripsi Proyek

**Microservices Order Service & E-Commerce System** merupakan sistem terdistribusi berbasis arsitektur *Microservices* yang dibangun menggunakan Laravel dan MySQL. Sistem ini dirancang untuk mengelola proses pemesanan dalam aplikasi e-commerce, mulai dari pengambilan data keranjang, validasi produk dan stok, pembuatan pesanan, hingga simulasi pembayaran.

Order Service berkomunikasi dengan Product Service untuk memperoleh informasi produk dan memvalidasi stok, serta Cart Service untuk mengambil data keranjang pengguna sebelum proses checkout dilakukan.

Proyek ini dibuat untuk memenuhi tugas Ujian Tengah Semester (UTS) mata kuliah Microservices.

## 🏗️ Teknologi yang Digunakan

- **Bahasa Pemrograman:** PHP 8.2
- **Framework:** Laravel 11
- **Database:** MySQL
- **Containerization:** Docker dan Docker Compose
- **Komunikasi Antarservice:** REST API
- **HTTP Client:** Laravel HTTP Client
- **API Testing:** Insomnia
- **Version Control:** Git dan GitHub

## ✨ Fitur Utama

- **Checkout Pesanan:** Membuat pesanan berdasarkan data keranjang pengguna.
- **Validasi Produk:** Memeriksa informasi produk melalui Product Service.
- **Validasi Stok:** Memastikan ketersediaan stok sebelum pesanan dibuat.
- **Manajemen Pesanan:** Mengambil daftar pesanan dan detail pesanan berdasarkan ID.
- **Riwayat Pesanan Pengguna:** Menampilkan pesanan berdasarkan ID pengguna.
- **Filter Status Pesanan:** Memfilter pesanan berdasarkan status tertentu.
- **Simulasi Pembayaran:** Mengubah status pesanan dari `PENDING` menjadi `PAID`.
- **Validasi Input:** Memeriksa data masukan dan parameter request.
- **Penanganan Error:** Memberikan respons yang sesuai ketika terjadi kesalahan bisnis atau kegagalan komunikasi antarlayanan.
- **Health Check:** Memeriksa kondisi layanan melalui endpoint kesehatan jika telah dikonfigurasi.
- **Docker Compose:** Menjalankan layanan dan dependensinya dalam container.

## 🏛️ Arsitektur Microservices

Sistem menggunakan pendekatan Microservices, yaitu membagi aplikasi menjadi beberapa layanan yang memiliki tanggung jawab masing-masing.

### Komponen Sistem

1. **Order Service**
   Bertanggung jawab mengelola proses checkout, membuat pesanan, menyimpan data pesanan dan item pesanan, menampilkan riwayat pesanan, serta menangani simulasi pembayaran.

2. **Product Service**
   Menyediakan informasi produk dan data stok yang dibutuhkan Order Service untuk memvalidasi pesanan.

3. **Cart Service**
   Menyediakan data keranjang belanja pengguna yang digunakan sebagai sumber item dalam proses checkout.

4. **MySQL Database**
   Menyimpan data pesanan dan item pesanan yang dikelola oleh Order Service.

### Alur Proses Checkout

1. Pengguna mengirim request checkout ke Order Service.
2. Order Service meminta data keranjang kepada Cart Service.
3. Order Service memeriksa informasi produk dan stok melalui Product Service.
4. Jika data valid dan stok mencukupi, Order Service memproses pembuatan pesanan.
5. Data pesanan dan item pesanan disimpan ke database MySQL.
6. Order Service mengembalikan respons berupa informasi pesanan kepada pengguna.

Jika keranjang kosong, produk tidak ditemukan, stok tidak mencukupi, atau layanan lain tidak dapat dihubungi, Order Service menangani kondisi tersebut sesuai aturan bisnis dan implementasi API.

## 📋 Persyaratan Sistem

Pastikan perangkat sudah memiliki:

- Docker Desktop
- Git
- Visual Studio Code atau editor kode lainnya
- Insomnia untuk pengujian API

## 🚀 Cara Menjalankan Proyek

### 1. Clone Repository

Buka terminal atau Git Bash, kemudian jalankan:

```bash
git clone https://github.com/NabilaAsmi11/mi-product.git
```

Masuk ke folder repository:

```bash
cd mi-product
```

Pastikan folder tersebut berisi file `docker-compose.yml` dan konfigurasi proyek yang diperlukan.

### 2. Periksa Konfigurasi Docker

Pastikan Docker Desktop sudah aktif dan file `docker-compose.yml` telah mengatur service yang dibutuhkan, termasuk Order Service, database, serta Product Service dan Cart Service apabila dijalankan dalam konfigurasi yang sama.

Periksa juga konfigurasi environment seperti:

- `APP_KEY`
- `APP_ENV`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- URL Product Service
- URL Cart Service

Nama variabel dapat berbeda sesuai implementasi proyek.

**Penting:** `DB_HOST` di dalam container biasanya menggunakan nama service database pada Docker Compose, bukan `127.0.0.1`.

### 3. Jalankan Docker Compose

Jalankan perintah berikut untuk membangun image dan menjalankan container:

```bash
docker compose up -d --build
```

Perintah ini akan membangun image jika diperlukan dan menjalankan service yang didefinisikan dalam `docker-compose.yml`.

### 4. Periksa Status Container

```bash
docker compose ps
```

Pastikan container yang diperlukan berjalan dengan baik.

Untuk melihat log seluruh service:

```bash
docker compose logs -f
```

Untuk melihat log Order Service saja, jika nama servicenya `order-service`:

```bash
docker compose logs -f order-service
```

### 5. Persiapkan Database

Jika migrasi database belum dijalankan, gunakan:

```bash
docker compose exec order-service php artisan migrate
```

Perintah tersebut berlaku jika nama service di Docker Compose adalah `order-service` dan konfigurasi database sudah benar.

Jika proyek menggunakan seeder, jalankan sesuai kebutuhan:

```bash
docker compose exec order-service php artisan db:seed
```

Jangan menjalankan migrasi atau seeder secara berulang tanpa memeriksa dampaknya terhadap data yang sudah ada.

### 6. Periksa Endpoint API

Jika Order Service dikonfigurasi menggunakan port `3003`, base URL API adalah:

```text
http://localhost:3003/api
```

Port tersebut harus disesuaikan dengan konfigurasi `ports` pada `docker-compose.yml`.

### 7. Menghentikan Layanan

Untuk menghentikan container:

```bash
docker compose down
```

Untuk menghentikan container sekaligus menghapus volume yang digunakan, diperlukan perintah tambahan. Hindari menghapus volume jika masih ada data penting di dalam database.

## 🔌 Endpoint API

Berikut daftar endpoint Order Service yang digunakan sebagai acuan pengujian. Pastikan kembali nama route, method, dan parameter sesuai dengan implementasi pada `routes/api.php`.

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/orders` | Mengambil daftar pesanan |
| POST | `/api/orders` | Membuat pesanan melalui checkout |
| GET | `/api/orders/{id}` | Mengambil detail pesanan |
| GET | `/api/orders/user/{userId}` | Mengambil pesanan berdasarkan pengguna |
| PATCH | `/api/orders/{id}/pay` | Melakukan simulasi pembayaran |

### 1. Mengambil Daftar Pesanan

```http
GET /api/orders
Accept: application/json
```

Digunakan untuk mengambil daftar pesanan yang tersimpan pada Order Service.

### 2. Membuat Pesanan

```http
POST /api/orders
Content-Type: application/json
Accept: application/json
```

Contoh request body:

```json
{
  "user_id": 1
}
```

Request ini digunakan untuk memulai proses checkout berdasarkan data keranjang pengguna. Struktur request yang sebenarnya mengikuti validasi pada implementasi aplikasi.

### 3. Mengambil Detail Pesanan

```http
GET /api/orders/1
Accept: application/json
```

Digunakan untuk mengambil detail pesanan berdasarkan ID pesanan.

### 4. Mengambil Riwayat Pesanan Pengguna

```http
GET /api/orders/user/1
Accept: application/json
```

Digunakan untuk menampilkan daftar pesanan milik pengguna dengan ID tertentu.

### 5. Memfilter Pesanan Berdasarkan Status

```http
GET /api/orders/user/1?status=PENDING
Accept: application/json
```

Filter status digunakan untuk menampilkan pesanan sesuai status yang didukung aplikasi, misalnya `PENDING` atau `PAID`.

### 6. Simulasi Pembayaran

```http
PATCH /api/orders/1/pay
Accept: application/json
```

Digunakan untuk melakukan simulasi pembayaran. Jika pesanan masih berstatus `PENDING`, sistem dapat mengubah statusnya menjadi `PAID`.

Jika pesanan tidak ditemukan, sistem mengembalikan error. Jika status pesanan bukan `PENDING`, sistem dapat mengembalikan konflik bisnis dengan HTTP `409`.

## ⚠️ Penanganan Error API

Order Service perlu menangani kesalahan validasi, aturan bisnis, serta kegagalan komunikasi dengan service lain.

| HTTP Status | Keterangan |
|---|---|
| `200 OK` | Request berhasil diproses |
| `201 Created` | Pesanan berhasil dibuat |
| `404 Not Found` | Pesanan atau data terkait tidak ditemukan |
| `409 Conflict` | Terjadi konflik bisnis, misalnya keranjang kosong atau status pesanan tidak sesuai |
| `422 Unprocessable Entity` | Input atau parameter tidak valid |
| `503 Service Unavailable` | Service yang dibutuhkan tidak tersedia |
| `500 Internal Server Error` | Terjadi kesalahan internal server |

Status HTTP aktual bergantung pada implementasi dan aturan penanganan error yang digunakan.

## 🧪 Pengujian API dengan Postman

Pengujian dilakukan untuk memastikan setiap endpoint berfungsi sesuai kebutuhan.

Skenario pengujian yang dapat dilakukan:

1. Mengambil daftar seluruh pesanan.
2. Membuat pesanan dengan input valid.
3. Membuat pesanan dengan input tidak valid.
4. Melakukan checkout ketika keranjang kosong.
5. Melakukan checkout ketika produk tidak ditemukan.
6. Melakukan checkout ketika stok tidak mencukupi.
7. Menguji ketika Product Service tidak dapat dihubungi.
8. Menguji ketika Cart Service tidak dapat dihubungi.
9. Mengambil detail pesanan berdasarkan ID.
10. Mengambil riwayat pesanan berdasarkan ID pengguna.
11. Memfilter pesanan berdasarkan status.
12. Membayar pesanan dengan status `PENDING`.
13. Membayar pesanan yang sudah berstatus `PAID`.
14. Menguji endpoint dengan ID pesanan yang tidak ditemukan.
15. Memeriksa endpoint health check jika tersedia.

Setiap pengujian perlu memeriksa HTTP status, struktur respons JSON, serta perubahan data di database jika ada.

## 📁 Struktur Proyek

Struktur berikut merupakan gambaran umum proyek Laravel. Nama file dan folder dapat berbeda sesuai implementasi.

```text
mi-product/
├── app/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Models/
│   └── Services/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── tests/
├── .env.example
├── artisan
├── composer.json
├── Dockerfile
├── docker-compose.yml
└── README.md
```

## 🔒 Catatan Keamanan

- Jangan mengunggah file `.env` yang berisi kredensial ke GitHub.
- Gunakan `.env.example` untuk mendokumentasikan konfigurasi yang diperlukan.
- Validasi input dari setiap request API.
- Tangani kegagalan HTTP dari Product Service dan Cart Service.
- Jangan menampilkan informasi sensitif dalam pesan error.
- Gunakan autentikasi dan otorisasi apabila diwajibkan oleh spesifikasi sistem.

## 👩‍💻 Author

**Nama:** Nabila Putri Asmi  
**Program Studi:** Sistem Informasi  
**Perguruan Tinggi:** Politeknik Negeri Padang

---

**Microservices Order Service & E-Commerce System**  
Proyek Ujian Tengah Semester (UTS) Mata Kuliah Microservices.
