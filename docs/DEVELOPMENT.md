# Panduan Pengembangan

Dokumen untuk developer yang mengerjakan Dashboard KonsulPajak: alur kerja harian, konvensi kode, arsitektur, dan deployment. Untuk PRD/skema/roadmap lihat [PROJECT.md](PROJECT.md).

## Menjalankan Proyek

```bash
composer setup   # sekali di awal: install + .env + key + migrate + build
composer dev     # server + queue + vite paralel
```

Queue worker wajib berjalan karena pengiriman WhatsApp dan pekerjaan berat memakai queue (`QUEUE_CONNECTION=database`).

Scheduler (reminder deadline & pembersihan undangan kadaluarsa) hanya aktif jika `schedule:work` atau cron server berjalan:

```bash
php artisan schedule:work   # lokal
# Produksi: * * * * * php /path/artisan schedule:run
```

## Alur Kerja Harian

1. `git pull origin main`
2. `composer install && npm install` (jika lockfile berubah)
3. `php artisan migrate` (jika ada migrasi baru)
4. `php artisan wayfinder:generate` (jika route/controller berubah)
5. Kerjakan di branch fitur, buka PR ke `main`

## Konvensi Kode

### Backend (Laravel)
- Validasi lewat Form Request di `app/Http/Requests`, bukan inline di controller.
- Controller se-tipis mungkin; logika bisnis di `app/Services`.
- Enum untuk nilai tetap (`app/Enums`): status laporan, jenis klien/dokumen/laporan, role, dsb.
- Nama route bahasa Indonesia untuk domain bisnis (`admin/klien`, `laporan/{taxReport}`), Inggris untuk infrastruktur.
- Akses halaman team-scoped: prefix `{current_team}` + middleware `EnsureTeamMembership`; pembatasan peran via middleware `role:klien` / `role:admin`.

### Frontend (Svelte 5 + Inertia)
- Halaman Inertia di `resources/js/pages`, dipetakan otomatis dari nama route.
- Panggil backend lewat fungsi Wayfinder (`resources/js/routes|actions`), jangan hardcode URL.
- State Svelte 5 pakai runes (`$props`, `$state`, `$derived`).
- Komponen UI dari `resources/js/components/ui` (shadcn-svelte); toast via svelte-sonner.

### Gaya
- PHP: Pint (config `pint.json`) — jalankan `composer lint` sebelum commit.
- Static analysis: PHPStan level sesuai `phpstan.neon` — `composer types:check`.
- JS/Svelte: ESLint (`npm run lint`) + Prettier (`npm run format`).
- TypeScript: `npm run types:check`.

## Arsitektur Singkat

```
Request ─▶ Middleware (auth, verified, EnsureTeamMembership, EnsureRole)
        ─▶ Controller ─▶ Form Request (validasi)
                       ─▶ Service (GoogleDriveService, WhatsappNotifierService, RecapService)
                       ─▶ Model/Eloquent
        ─▶ Response Inertia ─▶ Page Svelte (+ props ter-typing di resources/js/types)
```

Integrasi eksternal dibungkus service class agar mudah di-mock saat testing:
- `GoogleDriveService` — upload dokumen & folder per klien ke Google Drive.
- `WhatsappNotifierService` — kirim WA via Fonnte, hasil dicatat ke `notification_logs`.
- `RecapService` — agregasi rekap bulanan/tahunan; render PDF via DomPDF.

Perintah terjadwal (`routes/console.php`):
| Jadwal | Perintah | Fungsi |
|---|---|---|
| Harian 08.00 | `send:deadline-reminders` | Reminder WA H-7 & H-1 |
| Harian | closure | Hapus undangan team kedaluwarsa |

## Testing

Pest 4, file di `tests/Feature` dan `tests/Unit`. Konvensi:
- Nama test deskriptif: `it('memvalidasi upload dokumen', ...)`.
- Gunakan factory (`database/factories`) dan `RefreshDatabase`.
- Feature test meng-cover otorisasi peran (admin vs klien) untuk tiap endpoint.

```bash
php artisan test
```

## Deployment

1. Set `.env` produksi: `APP_ENV=production`, `APP_DEBUG=false`, MySQL, kredensial Drive & Fonnte.
2. `composer install --no-dev --optimize-autoloader`
3. `npm run build` (atau `build:ssr` bila memakai SSR)
4. `php artisan migrate --force`
5. Cache: `php artisan config:cache route:cache view:cache`
6. Pastikan cron scheduler & supervisor queue (`queue:database`) berjalan.
7. Deploy ke subdomain terpisah dari WordPress utama.

## Troubleshooting

| Masalah | Solusi |
|---|---|
| Import `routes/` atau `actions/` error di editor | Jalankan `php artisan wayfinder:generate` |
| WA tidak terkirim | Cek `FONNTE_TOKEN`, pastikan queue worker hidup, lihat tabel `notification_logs` |
| Upload gagal ke Drive | Cek `GOOGLE_DRIVE_CREDENTIALS` & `GOOGLE_DRIVE_PARENT_FOLDER_ID` |
| Reminder tidak jalan | Pastikan scheduler aktif (`schedule:work` / cron) |
