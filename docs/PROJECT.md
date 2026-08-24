# Portal Klien KP Ahmad Sandi — Dokumen Proyek

Dokumen ini adalah rujukan utama untuk pengembangan Portal Klien KP Ahmad Sandi (konsultan pajak, ahmadsandi.com). Gunakan sebagai context saat vibecoding dengan opencode — kerjakan satu fase dari Roadmap per sesi, jangan keluar dari scope yang tercantum di sini.

---

## 1. PRD (Product Requirement Document)

### Latar Belakang
ahmadsandi.com melayani klien lintas profesi (dokter, pengacara, notaris) dan badan usaha (UMKM, PT, CV, Yayasan). Proses konsultasi dimulai dari WhatsApp, progres pekerjaan dipantau klien lewat akses Google Drive yang dibagikan manual. Belum ada sistem terpusat untuk melacak status laporan pajak per klien, deadline, atau riwayat dokumen.

### Tujuan Produk
- **Transparansi** — klien bisa lihat status laporan pajaknya sendiri kapan saja, tanpa harus tanya via WA.
- **Kepatuhan** — reminder deadline otomatis mengurangi risiko klien telat lapor / kena denda.
- **Efisiensi Admin** — Ahmad Sandi punya satu dashboard untuk kelola semua klien & dokumen, bukan tersebar di chat dan folder.

### Target Pengguna
| Peran | Kebutuhan Utama |
|---|---|
| **Klien** (Dokter, Pengacara, Notaris, UMKM, PT, CV, Yayasan) | Upload dokumen pajak, lihat status laporan, dapat pengingat deadline, riwayat laporan per periode. |
| **Admin** (Ahmad Sandi & tim) | Kelola data klien, update status tiap laporan, review dokumen masuk, kirim notifikasi, lihat ringkasan deadline mendatang. |

### Ruang Lingkup Fitur

**Fase 1 — MVP**
- Autentikasi klien & admin (role-based)
- Profil klien + jenis klien + paket layanan
- Upload dokumen tersinkron ke Google Drive (metadata di database)
- Tracking status laporan pajak per periode (draft → menunggu dokumen → diproses → dilaporkan → selesai)
- Reminder deadline otomatis via WhatsApp (H-7 dan H-1)
- Dashboard admin: daftar klien, filter status, deadline mendatang

**Fase 2**
- Invoice & tracking pembayaran per periode

**Fase 3**
- E-signature untuk NDA / dokumen kesepakatan
- Laporan/analitik untuk admin (jumlah klien aktif, tren keterlambatan, dsb)

**Fase 4 — Advance (Alur Bisnis Penuh)**
- Multi-entity: satu akun klien bisa punya beberapa entitas/profil (misal dokter praktik pribadi + PT)
- Kalender pajak per jenis klien + auto-generate laporan tiap awal periode (tanpa input manual admin)
- Revisi dokumen: admin kirim catatan kurang/salah, status balik ke klien dengan alasan jelas
- Invoice otomatis saat status laporan jadi "dilaporkan"
- Pembayaran online (payment gateway) langsung dari portal
- Role staff (selain admin/klien) untuk skalabilitas tim
- Export PDF ringkasan laporan tahunan
- Audit log aktivitas (siapa ubah apa, kapan)

### Di Luar Lingkup (Saat Ini)
- Perhitungan pajak otomatis / e-filing langsung ke DJP — tetap dikerjakan manual oleh konsultan
- Mobile app native — cukup web responsif dulu

### Metrik Keberhasilan
- Nol keterlambatan lapor akibat lupa deadline setelah reminder aktif
- Klien tidak lagi menanyakan status via WA untuk hal yang sudah terlihat di dashboard
- Waktu admin untuk cari dokumen klien berkurang (tidak lagi scroll chat/Drive manual)

---

## 2. UX & Alur Pengguna

Referensi gaya: dashboard sidebar gelap + topbar + layout card (mirip proyek logbook perawat, SB Admin style), warna navy/teal untuk kesan tepercaya dan formal.

### Peta Layar — Sisi Klien
- Login
- Dashboard ringkasan (status laporan berjalan + deadline terdekat)
- Detail laporan pajak per periode
- Upload dokumen
- Riwayat laporan (arsip per tahun)
- Profil & paket layanan

### Peta Layar — Sisi Admin
- Login
- Dashboard admin (semua klien + filter status + deadline mendatang)
- Detail klien (profil, dokumen, riwayat)
- Update status laporan
- Kelola paket layanan
- Log notifikasi terkirim

### Alur Utama — Klien Upload Dokumen
```
Login klien
 └─▶ Dashboard: lihat kartu "SPT Masa - Agustus 2026" status: Menunggu Dokumen
      └─▶ Klik kartu ▶ Detail laporan
           └─▶ Tombol "Upload Dokumen" ▶ pilih jenis (bukti potong/invoice)
                └─▶ File terupload ke Drive folder klien, metadata tersimpan
                     └─▶ Status berubah otomatis: "Diproses"
                          └─▶ Notifikasi ke admin (dashboard + opsional WA)
```

### Alur Utama — Admin Update Status
```
Login admin
 └─▶ Dashboard: tabel semua laporan, filter by status/deadline
      └─▶ Klik baris klien ▶ Detail laporan
           └─▶ Review dokumen yang sudah diupload (link ke Drive)
                └─▶ Ubah status ▶ "Dilaporkan" / "Selesai"
                     └─▶ Sistem kirim notifikasi WA otomatis ke klien
```

### Alur Bisnis End-to-End (Versi Advance)
```
1. Prospek → lihat ahmadsandi.com → hubungi via WA
2. Admin diskusi kebutuhan → sepakat paket layanan
3. Admin buat akun klien di portal
     └─▶ sistem otomatis: buat folder Drive + kirim WA berisi link login
4. Klien login pertama kali
     └─▶ wajib: ganti password, lengkapi profil, TANDA TANGAN NDA (e-sign)
     └─▶ tanpa NDA tertanda, dashboard laporan terkunci
5. Sistem otomatis buat entri laporan pajak sesuai kalender pajak
     (misal: SPT Masa tiap tanggal 1, status = menunggu_dokumen, deadline = tgl 20)
6. H-7 & H-1 sebelum deadline → reminder WA otomatis ke klien
7. Klien upload dokumen → status → diproses → admin dapat notifikasi
8. Admin review dokumen:
     a) Kurang/salah → kirim catatan revisi → status balik ke menunggu_dokumen
     b) Lengkap → admin hitung & lapor manual ke DJP → status → dilaporkan
9. Status "dilaporkan" → sistem otomatis buat invoice
     └─▶ notifikasi WA ke klien: "Laporan selesai, invoice terbit"
10. Klien bayar via portal (payment gateway) → admin verifikasi otomatis
11. Setelah dibayar & periode ditutup → status → selesai → masuk ke Riwayat
12. Siklus ulang ke langkah 5 untuk periode berikutnya (otomatis, tanpa admin input manual)
13. Tahunan: evaluasi paket → renewal/upgrade paket layanan
```
Poin kunci: langkah 5 (auto-generate laporan berkala) dan 9 (auto-invoice) yang membuat sistem terasa "hidup" — tanpa ini, admin tetap kerja manual tiap bulan persis seperti sebelum ada portal.

### Prinsip Desain
- **Status selalu terlihat** — badge warna konsisten di semua layar (draft=abu, menunggu=kuning, diproses=biru, selesai=hijau)
- **Bahasa dari sisi pengguna** — klien lihat "Laporan Anda", bukan istilah sistem seperti "record" atau "entity"
- **Aksi jelas satu tujuan** — tombol "Upload Dokumen" hanya upload, tidak merangkap ubah status
- **Mobile-responsif** — klien kemungkinan besar akses dari HP, terutama untuk upload foto bukti potong

### Palet & Tipografi Rekomendasi
- **Warna**: Navy `#0F2438` (sidebar/header), Teal `#1B6B63` (aksen aktif/sukses), Gold `#C9A15A` (highlight/badge), Paper `#F7F5F0` (background)
- **Tipografi**: Fraunces (judul), Inter (body/UI), JetBrains Mono (data/kode)
- **Komponen**: card berbayang tipis, radius 8–10px, badge status berbentuk pill, sidebar gelap + topbar terang

---

## 3. ERD / Skema Database

### Tabel Inti

**users**
- `id` PK
- `name`, `email`, `phone`, `role` (enum: admin, staff, klien), `password`
- `password_changed_at` (nullable — cek apakah masih password default)

**client_profiles**
- `id` PK
- `user_id` FK → users *(tidak unique — satu user bisa punya beberapa entitas/profil)*
- `nama_entitas`, `jenis_klien` (enum), `npwp`
- `package_id` FK → service_packages
- `drive_folder_id`
- `nda_signed_at` (nullable — null berarti dashboard laporan masih terkunci)

**service_packages**
- `id` PK
- `nama_paket`, `jenis_klien` (enum), `harga`, `fitur` (json)

**tax_calendars** *(baru — Fase 4)*
- `id` PK
- `jenis_klien` (enum), `jenis_laporan` (enum)
- `hari_mulai_periode`, `hari_deadline` (int, tanggal dalam bulan)
- `frekuensi` (enum: bulanan, tahunan)

**tax_reports**
- `id` PK
- `client_id` FK → client_profiles
- `jenis_laporan` (enum), `periode`, `status` (enum), `deadline_tanggal`
- `catatan_revisi` (nullable — diisi admin saat dokumen kurang/salah)

**documents**
- `id` PK
- `tax_report_id` FK → tax_reports
- `jenis_dokumen` (enum), `nama_file`, `drive_file_id`, `drive_file_url`
- `uploaded_by` FK → users

**notifications_log**
- `id` PK
- `client_id` FK → client_profiles
- `tipe` (enum), `channel` (enum), `sent_at`, `status` (enum)

**invoices**
- `id` PK
- `client_id` FK → client_profiles
- `tax_report_id` FK → tax_reports *(baru — invoice terikat ke laporan pemicunya)*
- `periode`, `nominal`, `status_bayar` (enum)
- `payment_method`, `payment_gateway_ref`, `paid_at` (baru — Fase 4)

**nda_agreements** *(baru — Fase 4)*
- `id` PK
- `client_id` FK → client_profiles
- `signature_data` atau `document_url` (bukti tanda tangan)
- `signed_at`

**activity_logs** *(baru — Fase 4)*
- `id` PK
- `user_id` FK → users
- `action` (string, contoh: "update_status_laporan")
- `subject_type`, `subject_id` (tabel & id yang diubah)
- `created_at`

### Relasi
- `users` 1—N `client_profiles` (satu user bisa punya beberapa entitas — multi-entity)
- `service_packages` 1—N `client_profiles`
- `tax_calendars` — tabel referensi, dipakai job auto-generate `tax_reports` (bukan FK langsung)
- `client_profiles` 1—N `tax_reports`
- `tax_reports` 1—N `documents`
- `client_profiles` 1—N `notifications_log`
- `client_profiles` 1—N `invoices`, `tax_reports` 1—1 `invoices` (per laporan yang sudah dilaporkan)
- `client_profiles` 1—1 `nda_agreements` (per entitas, wajib sebelum akses penuh)
- `users` 1—N `activity_logs`

### Enum Penting
| Kolom | Nilai |
|---|---|
| `users.role` | admin, staff, klien |
| `tax_reports.status` | draft, menunggu_dokumen, diproses, dilaporkan, selesai |
| `tax_reports.jenis_laporan` | spt_masa, spt_tahunan_pribadi, spt_tahunan_badan |
| `client_profiles.jenis_klien` | dokter, pengacara, notaris, umkm, pt, cv, yayasan |
| `documents.jenis_dokumen` | bukti_potong, invoice, npwp, laporan_keuangan, lainnya |
| `notifications_log.channel` | whatsapp, email |
| `notifications_log.tipe` | reminder_deadline, update_status, revisi_dokumen, invoice_terbit |
| `invoices.status_bayar` | belum_bayar, menunggu_verifikasi, lunas |
| `tax_calendars.frekuensi` | bulanan, tahunan |

---

## 4. Tech Stack & Struktur Folder

### Stack
| Layer | Pilihan | Catatan |
|---|---|---|
| Backend | Laravel 12, PHP 8.3+ | Auth pakai Laravel Fortify (role admin/klien/staff) |
| Frontend | Inertia + Svelte 5 + Tailwind CSS 4 | Wayfinder untuk route typings, tidak perlu SPA penuh |
| Database | MySQL | Sesuai skema di bagian 3 |
| Storage dokumen | Google Drive API | Akun Drive/Workspace milik Ahmad Sandi, folder otomatis per klien |
| Notifikasi WA | Fonnte atau Wablas API | Untuk reminder deadline H-7/H-1 dan update status |
| PDF (opsional) | DomPDF | Untuk cetak ringkasan laporan jika dibutuhkan |
| E-signature | Privy atau tekenaja | Untuk tanda signature NDA digital (Fase 4) |
| Payment gateway | Midtrans atau Xendit | Untuk pembayaran invoice online (Fase 4) |
| Hosting | VPS / cPanel, subdomain | `portal.ahmadsandi.com`, terpisah dari WordPress compro |
| Timing | Timestamps | created_at, updated_at otomatis di semua model |

### Struktur Folder (inti)
```
app/
├─ Models/
│   ├─ User.php
│   ├─ ClientProfile.php
│   ├─ ServicePackage.php
│   ├─ TaxReport.php
│   ├─ TaxCalendar.php
│   ├─ Document.php
│   ├─ NotificationLog.php
│   ├─ Invoice.php
│   ├─ NdaAgreement.php
│   └─ ActivityLog.php
├─ Http/
│   ├─ Controllers/
│   │   ├─ Client/DashboardController.php
│   │   ├─ Client/DocumentController.php
│   │   ├─ Client/NdaController.php
│   │   ├─ Client/InvoiceController.php
│   │   ├─ Admin/ClientController.php
│   │   ├─ Admin/TaxReportController.php
│   │   ├─ Admin/InvoiceController.php
│   │   └─ Auth/... (dari Breeze)
│   └─ Middleware/EnsureRole.php, EnsureNdaSigned.php
├─ Services/
│   ├─ GoogleDriveService.php
│   ├─ WhatsappNotifierService.php
│   ├─ ESignatureService.php
│   └─ PaymentGatewayService.php
└─ Console/Commands/
    ├─ SendDeadlineReminders.php
    └─ GenerateScheduledTaxReports.php   ← baru, jalan tiap awal periode

resources/views/
├─ client/  (dashboard, laporan, upload)
├─ admin/   (dashboard, detail-klien)
└─ layouts/ (sidebar navy/teal, topbar)

routes/
└─ web.php
```

### Environment / Kredensial yang Perlu Disiapkan
- Kredensial Google Cloud (OAuth client ID/secret) untuk Drive API
- API key Fonnte/Wablas untuk kirim WA
- Subdomain `portal.ahmadsandi.com` sudah diarahkan ke server
- Database MySQL terpisah dari database WordPress

---

## 5. Roadmap Fase Pengerjaan

Kerjakan satu fase per sesi vibecoding agar scope tetap terkontrol.

### Fase 1 — Fondasi & Auth
- Install Laravel + Breeze, setup Tailwind
- Migration: users, client_profiles, service_packages, tax_reports, documents, notifications_log
- Seeder untuk service_packages (sesuai pricelist di web)
- Middleware role admin/klien + redirect dashboard sesuai role

### Fase 2 — Dashboard Klien
- Halaman dashboard: kartu status laporan berjalan
- Halaman detail laporan per periode
- Halaman riwayat/arsip laporan
- Halaman profil klien

### Fase 3 — Integrasi Google Drive
- GoogleDriveService: auth, buat folder otomatis per klien baru
- Upload file dari form ▶ Drive API ▶ simpan metadata di tabel documents
- Tampilkan daftar dokumen per laporan dengan link ke Drive

### Fase 4 — Dashboard Admin
- Tabel semua klien + filter status + filter jenis klien
- Detail klien: profil, dokumen, riwayat laporan
- Update status laporan (dropdown/tombol aksi)
- Ringkasan deadline 7 hari ke depan

### Fase 5 — Notifikasi WhatsApp
- WhatsappNotifierService (integrasi Fonnte/Wablas)
- Scheduled command: cek deadline H-7 dan H-1, kirim WA
- Trigger WA saat status laporan berubah
- Catat semua pengiriman di notifications_log

### Fase 6 — Invoice & E-signature (Dasar)
- Migration & model Invoice, NdaAgreement
- CRUD invoice manual oleh admin
- Halaman klien lihat riwayat invoice
- Integrasi ESignatureService untuk NDA, wajib ditandatangani di login pertama

### Fase 7 — Multi-Entity & Onboarding
- Ubah relasi users↔client_profiles jadi 1—N, tambah fitur "switch entitas" di dashboard klien
- Wizard onboarding: ganti password default, lengkapi profil, tanda tangan NDA
- Middleware EnsureNdaSigned yang mengunci akses dashboard laporan sebelum NDA ditandatangani

### Fase 8 — Kalender Pajak & Auto-Generate Laporan
- Migration & model TaxCalendar, seeder jadwal per jenis_klien
- Command GenerateScheduledTaxReports: buat tax_report baru otomatis sesuai kalender, jadwalkan tiap hari via scheduler
- Halaman admin untuk kelola kalender pajak

### Fase 9 — Revisi Dokumen
- Tambah kolom catatan_revisi di tax_reports
- Fitur admin: kirim catatan revisi, status balik ke menunggu_dokumen otomatis
- Notifikasi WA ke klien saat ada revisi, tampilkan catatan di halaman detail laporan klien

### Fase 10 — Invoice Otomatis & Payment Gateway
- Event listener: status tax_report berubah jadi "dilaporkan" → auto-generate invoice
- PaymentGatewayService (Midtrans/Xendit): buat transaksi, terima webhook konfirmasi bayar
- Update status_bayar otomatis dari webhook, kirim notifikasi WA saat lunas

### Fase 11 — Staff Role, Export PDF, Audit Log
- Tambah role staff + middleware terkait
- Export PDF ringkasan laporan tahunan (DomPDF)
- ActivityLog: catat setiap perubahan status/dokumen/invoice, tampilkan di halaman admin

---

## 6. API / Endpoint Spec

### Auth
| Method | Path | Deskripsi |
|---|---|---|
| POST | `/login` | Login klien/admin |
| POST | `/logout` | Logout |

### Klien
| Method | Path | Deskripsi |
|---|---|---|
| GET | `/dashboard` | Ringkasan status laporan berjalan |
| GET | `/laporan/{id}` | Detail satu laporan pajak |
| POST | `/laporan/{id}/dokumen` | Upload dokumen (ke Drive) |
| GET | `/riwayat` | Arsip laporan per tahun |
| POST | `/entitas/{id}/switch` | Ganti entitas aktif (multi-entity) |
| GET/POST | `/nda` | Lihat & tanda tangani NDA (wajib sebelum akses penuh) |
| GET | `/invoice` | Riwayat invoice & status pembayaran |
| POST | `/invoice/{id}/bayar` | Mulai transaksi pembayaran via payment gateway |
| GET | `/laporan/{id}/export-pdf` | Unduh ringkasan laporan sebagai PDF |

### Admin
| Method | Path | Deskripsi |
|---|---|---|
| GET | `/admin/klien` | Daftar semua klien + filter |
| GET | `/admin/klien/{id}` | Detail klien + dokumen + riwayat |
| PUT | `/admin/laporan/{id}/status` | Update status laporan → trigger notifikasi |
| POST | `/admin/laporan/{id}/revisi` | Kirim catatan revisi → status balik ke menunggu_dokumen |
| POST | `/admin/klien` | Tambah klien baru → auto buat folder Drive |
| GET | `/admin/deadline` | Daftar deadline 7 hari ke depan |
| GET/POST | `/admin/kalender-pajak` | Kelola jadwal wajib lapor per jenis klien |
| GET | `/admin/invoice` | Kelola semua invoice & status pembayaran |
| GET | `/admin/activity-log` | Riwayat aktivitas semua perubahan data |

### Webhook
| Method | Path | Deskripsi |
|---|---|---|
| POST | `/webhook/payment` | Terima konfirmasi pembayaran dari Midtrans/Xendit → update status_bayar |

### Integrasi Eksternal (internal service, bukan client-facing)
| Fungsi | Deskripsi |
|---|---|
| `GoogleDriveService::createClientFolder()` | Buat folder Drive baru per klien |
| `GoogleDriveService::uploadFile()` | Upload file ke folder klien, return file_id + url |
| `WhatsappNotifierService::send()` | Kirim pesan WA (reminder/status update/revisi/invoice) |
| `ESignatureService::requestSignature()` | Kirim dokumen NDA untuk ditandatangani klien |
| `PaymentGatewayService::createTransaction()` | Buat transaksi pembayaran invoice |

> Catatan: semua endpoint memakai autentikasi session Laravel standar + middleware role.
