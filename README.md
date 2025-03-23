# Sistem Manajemen Perumahan - Backend

Repositori ini berisi aplikasi backend untuk Sistem Manajemen Perumahan, yang dibangun menggunakan Laravel. Backend menyediakan API RESTful untuk mengelola data penghuni, rumah, pembayaran, dan pengeluaran.

## Teknologi yang Digunakan

- PHP 8.2.16
- Laravel 10
- MySQL
- Composer

## Persyaratan Sistem

- PHP 8.2.16 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Composer
- Web server (Apache/Nginx)

## Struktur Database

Database aplikasi terdiri dari tabel-tabel berikut:

1. **residents** - Menyimpan informasi penghuni
    - id (PK)
    - name
    - id_card_photo
    - resident_status
    - phone_number
    - is_married
    - timestamps

2. **houses** - Menyimpan informasi rumah
    - id (PK)
    - house_number
    - occupancy_status
    - timestamps

3. **house_residents** - Relasi antara rumah dan penghuni
    - id (PK)
    - house_id (FK)
    - resident_id (FK)
    - start_date
    - end_date
    - is_current
    - timestamps

4. **payments** - Pencatatan pembayaran iuran
    - id (PK)
    - house_resident_id (FK)
    - payment_type
    - amount
    - payment_date
    - payment_period
    - period_start
    - period_end
    - is_paid
    - timestamps

5. **expenses** - Pencatatan pengeluaran
    - id (PK)
    - description
    - amount
    - expense_date
    - expense_type
    - timestamps

## API Endpoints

### Penghuni (Residents)
- `GET /api/residents` - Mendapatkan semua penghuni
- `GET /api/residents/{id}` - Mendapatkan detail penghuni
- `POST /api/residents` - Membuat penghuni baru
- `PUT /api/residents/{id}` - Mengupdate data penghuni
- `DELETE /api/residents/{id}` - Menghapus penghuni

### Rumah (Houses)
- `GET /api/houses` - Mendapatkan semua rumah
- `GET /api/houses/{id}` - Mendapatkan detail rumah
- `POST /api/houses` - Membuat rumah baru
- `PUT /api/houses/{id}` - Mengupdate data rumah
- `DELETE /api/houses/{id}` - Menghapus rumah
- `GET /api/houses/{house}/history` - Mendapatkan history penghuni rumah
- `GET /api/houses/{house}/payment-history` - Mendapatkan history pembayaran rumah
- `POST /api/houses/{house}/add-resident` - Menambahkan penghuni ke rumah
- `POST /api/houses/{house}/remove-resident` - Menghapus penghuni dari rumah

### Pembayaran (Payments)
- `GET /api/payments` - Mendapatkan semua pembayaran
- `GET /api/payments/{id}` - Mendapatkan detail pembayaran
- `POST /api/payments` - Membuat pembayaran baru
- `PUT /api/payments/{id}` - Mengupdate data pembayaran
- `DELETE /api/payments/{id}` - Menghapus pembayaran
- `GET /api/payments/summary` - Mendapatkan ringkasan pembayaran
- `GET /api/payments/monthly-detail` - Mendapatkan detail pembayaran bulanan

### Pengeluaran (Expenses)
- `GET /api/expenses` - Mendapatkan semua pengeluaran
- `GET /api/expenses/{id}` - Mendapatkan detail pengeluaran
- `POST /api/expenses` - Membuat pengeluaran baru
- `PUT /api/expenses/{id}` - Mengupdate data pengeluaran
- `DELETE /api/expenses/{id}` - Menghapus pengeluaran

### Statistik
- `GET /api/statistics/dashboard` - Mendapatkan data statistik untuk dashboard

## Cara Instalasi

1. Clone repositori
```bash
git clone https://github.com/arifafandi/rt-management-system-backend.git
cd rt-management-system-backend
```

2. Install dependensi
```bash
composer install
```

3. Konfigurasi file .env
```bash
cp .env.example .env
php artisan key:generate
```

4. **Persiapan Database**

### Menggunakan phpMyAdmin
- Buka phpMyAdmin di browser Anda
- Login dengan kredensial MySQL Anda
- Klik tab "Databases"
- Masukkan "rt_management_system" pada kolom "Create database"
- Pilih "utf8mb4_unicode_ci" sebagai collation
- Klik tombol "Create"

### Menggunakan Command Line
```bash
# Login ke MySQL
mysql -u root -p

# Setelah login, buat database baru
CREATE DATABASE rt_management_system;

# Keluar dari MySQL
exit;
```

5. Edit file .env untuk konfigurasi database
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rt_management_system
DB_USERNAME=root
DB_PASSWORD=password_anda
```
> **Penting**: Ganti `password_anda` dengan password MySQL yang sebenarnya di komputer Anda. Jika MySQL tidak menggunakan password, biarkan `DB_PASSWORD=` kosong.

6. Konfigurasi storage untuk upload foto KTP
```bash
php artisan storage:link
```

7. Jalankan migrasi dan seeder
```bash
php artisan migrate
php artisan db:seed
```

8. Jalankan server
```bash
php artisan serve
```

## Penggunaan

Backend akan berjalan di http://localhost:8000 dan API endpoints tersedia di http://localhost:8000/api

## Struktur Proyek

```
app/
├── Http/
│   ├── Controllers/      # API controllers
├── Models/               # Eloquent models
database/
├── migrations/           # Database migrations
└── seeders/              # Database seeders
routes/
└── api.php               # API routes
storage/
└── app/
    └── public/
        └── id_cards/     # Storage for ID card photos
```

## Terkait

Lihat juga repositori frontend untuk aplikasi ini:
- [Frontend React](https://github.com/arifafandi/rt-management-system-frontend)

Untuk informasi lebih lanjut tentang proyek secara keseluruhan:
- [Repositori Utama](https://github.com/arifafandi/rt-management-system)
