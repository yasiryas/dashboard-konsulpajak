# Dashboard KonsulPajak

Portal konsultasi perpajakan untuk KonsulPajak (KP Ahmad Sandi — ahmadsandi.com). Klien dapat melacak status laporan pajak, mengunggah dokumen ke Google Drive, dan menerima pengingat deadline via WhatsApp. Admin mengelola klien, laporan, invoice, dan rekap dalam satu dashboard.

## Fitur

### Klien
- Dashboard ringkasan status laporan berjalan dan deadline terdekat
- Detail laporan pajak per periode + upload dokumen (tersinkron ke Google Drive)
- Riwayat laporan (arsip per tahun)
- Profil dan paket layanan

### Admin
- Dashboard admin dengan statistik dan daftar deadline mendatang
- Manajemen klien (tambah/detail) dan update status laporan
- Kelola paket layanan (CRUD)
- Invoice: buat, ubah status bayar, hapus
- Log notifikasi WhatsApp
- Rekap bulanan & tahunan + ekspor PDF (DomPDF)
- Monitoring realtime

### Umum
- Autentikasi lengkap via Laravel Fortify (login, registrasi, verifikasi email, reset password, 2FA)
- Role-based access (`admin`, `klien`) via middleware `role:`
- Multi-team dengan undangan anggota (Fortify Teams)
- Reminder deadline otomatis H-7 & H-1 via WhatsApp (Fonnte), dijadwalkan lewat scheduler

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13, PHP 8.3+, Fortify, Wayfinder, Inertia Laravel |
| Frontend | Svelte 5, Inertia v3, Tailwind CSS 4, shadcn-svelte (bits-ui), Vite |
| Database | SQLite (default) / MySQL |
| Storage | Google Drive API (`google/apiclient`) |
| Notifikasi WA | Fonnte API |
| PDF | barryvdh/laravel-dompdf |
| Testing | Pest 4 |
| Kualitas | Pint, PHPStan (Larastan), ESLint, Prettier, svelte-check |

## Persyaratan

- PHP >= 8.3 (ekstensi: sqlite/pdo_mysql, gd, curl, zip)
- Composer
- Node.js >= 20 & npm (atau pnpm)

## Instalasi

```bash
# 1. Instal dependensi + .env + key + migrasi + build aset
composer setup

# 2. Jalankan (server + queue + vite sekaligus)
composer dev
```

Aplikasi tersedia di http://localhost:8000.

Alternatif manual:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

## Variabel Environment

Selain standar Laravel, yang spesifik proyek:

| Variabel | Fungsi |
|---|---|
| `GOOGLE_DRIVE_CREDENTIALS` | Kredensial service account Google (JSON) untuk upload dokumen |
| `GOOGLE_DRIVE_PARENT_FOLDER_ID` | Folder root Drive tempat folder per klien dibuat |
| `FONNTE_TOKEN` | Token API Fonnte untuk pengiriman WhatsApp |

## Perintah Penting

| Perintah | Fungsi |
|---|---|
| `composer dev` | `artisan serve` + `queue:listen` + `vite` secara paralel |
| `composer lint` / `composer lint:check` | Fix / cek style PHP (Pint) |
| `composer types:check` | Analisis statis PHP (PHPStan) |
| `npm run dev` / `npm run build` | Dev server / build aset frontend |
| `npm run lint` / `npm run lint:check` | Fix / cek ESLint |
| `npm run format` / `npm run format:check` | Format / cek Prettier |
| `npm run types:check` | svelte-check (TypeScript + Svelte) |
| `php artisan test` | Menjalankan seluruh suite Pest |
| `composer ci:check` | Gabungan lint + format + types + test (untuk CI) |
| `php artisan send:deadline-reminders` | Kirim reminder WA deadline (dijadwalkan otomatis tiap hari 08.00 via scheduler) |

## Struktur Proyek

```
app/
├─ Actions/Fortify/        # Aksi autentikasi Fortify
├─ Actions/Teams/          # Pembuatan team
├─ Console/Commands/       # SendDeadlineReminders
├─ Enums/                  # StatusLaporan, JenisKlien, UserRole, dst.
├─ Http/
│  ├─ Controllers/Admin/   # Client, Package, Invoice, Recap, Realtime, dst.
│  ├─ Controllers/Teams/   # Team, invitation, member
│  ├─ Middleware/          # EnsureRole, EnsureTeamMembership
│  └─ Requests/            # Form Request validasi
├─ Models/                 # User, Team, ClientProfile, TaxReport, Document, Invoice, ...
├─ Policies/               # TeamPolicy
├─ Rules/                  # Aturan validasi kustom
└─ Services/               # GoogleDriveService, WhatsappNotifierService, RecapService

resources/js/
├─ components/             # Komponen aplikasi + ui/ (shadcn-svelte)
├─ layouts/                # AppLayout, AuthLayout, settings
├─ pages/                  # Halaman Inertia (admin/, auth/, laporan/, settings/, teams/)
├─ types/                  # Tipe TypeScript shared
└─ lib/                    # Utilitas (toast, theme, initials)

resources/views/admin/rekap/  # Template Blade PDF rekapitulasi
routes/                       # web.php, settings.php, console.php
docs/                         # Dokumentasi proyek
```

> Catatan: `resources/js/actions`, `resources/js/routes`, `resources/js/wayfinder` adalah hasil generate Wayfinder dan di-gitignore. Jalankan `php artisan wayfinder:generate` setelah mengubah route/controller.

## Testing

Suite memakai Pest. Contoh:

```bash
php artisan test                        # semua
php artisan test --filter=TeamTest      # satu file
php artisan test tests/Feature/Admin    # direktori
```

Database testing memakai SQLite in-memory (lihat `phpunit.xml`).

## Dokumentasi Lanjutan

- [docs/PROJECT.md](docs/PROJECT.md) — PRD, UX flow, skema database, roadmap fase
- [docs/TASKS.md](docs/TASKS.md) — daftar tugas pengerjaan
- [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md) — panduan pengembangan, konvensi, deployment

## Lisensi

MIT
