# TASKS — Portal Klien KP Ahmad Sandi

**Status progres:** Task 1.1–1.5 (Fase 1) dan Task 2.1–2.2 sudah selesai. Lanjutkan dari **Task 2.3**.

Daftar tugas siap-input ke opencode. Kerjakan **satu task per sesi/prompt** (jangan gabung beberapa task sekaligus) supaya scope tetap terkontrol dan hasilnya bisa direview sebelum lanjut. Setiap task punya prompt yang bisa langsung disalin ke opencode, plus acceptance criteria untuk memastikan hasilnya benar sebelum lanjut ke task berikutnya.

Sebelum mulai, pastikan `PROJECT.md` ada di root folder project — opencode perlu baca itu untuk konteks skema database, stack, dan struktur folder.

---

## FASE 1 — Fondasi & Auth ✅ SELESAI

### Task 1.1 — Setup Project
**Prompt:**
```
Setup project Laravel 12 baru dengan Laravel Breeze (stack Blade) dan Tailwind CSS 4.
Gunakan konfigurasi dari PROJECT.md bagian "Tech Stack & Struktur Folder".
Setelah instalasi selesai, pastikan `php artisan serve` bisa jalan dan halaman
login/register bawaan Breeze bisa diakses.
```
**Acceptance criteria:**
- [ ] `composer install` & `npm install` sukses tanpa error
- [ ] Halaman `/login` dan `/register` bisa diakses di browser
- [ ] Tailwind sudah aktif (styling Breeze tampil normal)

### Task 1.2 — Migration Tabel Inti
**Prompt:**
```
Buat migration untuk tabel berikut sesuai skema di PROJECT.md bagian "ERD / Skema Database":
client_profiles, service_packages, tax_reports, documents, notifications_log.
Tambahkan juga kolom `phone` dan `role` (enum: admin, klien) ke tabel users bawaan.
Gunakan foreign key constraint yang sesuai relasi di dokumen tersebut.
Jangan buat tabel invoices dulu — itu Fase 2.
```
**Acceptance criteria:**
- [ ] `php artisan migrate` sukses tanpa error
- [ ] Semua kolom & tipe data sesuai tabel di PROJECT.md bagian 3
- [ ] Foreign key mengarah ke tabel & kolom yang benar

### Task 1.3 — Model & Relasi Eloquent
**Prompt:**
```
Buat Eloquent model untuk: ClientProfile, ServicePackage, TaxReport, Document,
NotificationLog. Definisikan relasi antar model sesuai bagian "Relasi" di
PROJECT.md (contoh: ClientProfile hasMany TaxReport, TaxReport hasMany Document, dst).
Tambahkan $fillable dan casting enum yang sesuai di tiap model.
```
**Acceptance criteria:**
- [ ] Semua model punya relasi yang bisa dipanggil (`$client->taxReports`, dst) dan diuji lewat `php artisan tinker`
- [ ] Enum status/jenis di-cast dengan benar

### Task 1.4 — Seeder Paket Layanan
**Prompt:**
```
Buat seeder untuk tabel service_packages berisi paket sesuai pricelist di
ahmadsandi.com per jenis klien (dokter, pengacara, notaris, umkm, pt, cv, yayasan).
Jalankan lewat DatabaseSeeder.
```
**Acceptance criteria:**
- [ ] `php artisan db:seed` sukses
- [ ] Data paket muncul di tabel `service_packages`

### Task 1.5 — Role & Middleware
**Prompt:**
```
Buat middleware EnsureRole yang membatasi akses route berdasarkan kolom `role`
di tabel users (admin/klien). Setelah login, redirect klien ke /dashboard dan
admin ke /admin/dashboard. Update RouteServiceProvider atau controller login
Breeze untuk logic redirect ini.
```
**Acceptance criteria:**
- [ ] Login sebagai user role klien ▶ redirect ke `/dashboard`
- [ ] Login sebagai user role admin ▶ redirect ke `/admin/dashboard`
- [ ] User klien tidak bisa akses route `/admin/*` (dapat 403)

---

## FASE 2 — Dashboard Klien

Struktur menu sidebar klien (acuan untuk task ini): **Dashboard, Laporan Pajak, Riwayat, Profil**.

### Task 2.1 — Layout & Sidebar Klien ✅ SELESAI
**Prompt:**
```
Buat layout Blade untuk area klien dengan sidebar navy (#0F2438) + topbar,
mengikuti gaya SB Admin (referensi di PROJECT.md bagian UX). Menu sidebar:
Dashboard, Laporan Pajak, Riwayat, Profil. Highlight menu aktif dengan warna
teal (#1B6B63). Pastikan responsif untuk mobile (sidebar collapsible).
```
**Acceptance criteria:**
- [ ] Layout tampil benar di desktop dan mobile
- [ ] Menu aktif ter-highlight sesuai halaman yang dibuka
- [ ] Warna & font sesuai palet di PROJECT.md (Navy/Teal/Gold, Fraunces+Inter)

### Task 2.2 — Halaman Dashboard Klien ✅ SELESAI
**Prompt:**
```
Buat halaman /dashboard yang menampilkan kartu ringkasan laporan pajak aktif
milik klien yang sedang login (query dari tax_reports berdasarkan client_id),
lengkap dengan badge status berwarna (draft=abu, menunggu_dokumen=kuning,
diproses=biru, dilaporkan/selesai=hijau) dan tanggal deadline terdekat.
```
**Acceptance criteria:**
- [ ] Data yang tampil hanya milik klien yang login (bukan klien lain)
- [ ] Badge status warnanya sesuai ketentuan
- [ ] Kartu bisa diklik menuju halaman detail laporan (Task 2.3)

### Task 2.3 — Detail Laporan & Riwayat 👉 MULAI DARI SINI
**Prompt:**
```
Buat halaman /laporan/{id} untuk detail satu laporan pajak (jenis, periode,
status, deadline, daftar dokumen yang sudah diupload). Buat juga halaman
/riwayat yang menampilkan arsip laporan berstatus "selesai", dikelompokkan
per tahun.
```
**Acceptance criteria:**
- [ ] Halaman detail hanya bisa diakses oleh pemilik laporan (cek client_id)
- [ ] Riwayat terkelompok per tahun dengan benar

### Task 2.4 — Halaman Profil
**Prompt:**
```
Buat halaman /profil menampilkan data client_profiles (nama entitas, jenis
klien, NPWP, paket layanan aktif) milik user yang login, dengan form untuk
edit data non-sensitif (nama entitas, tidak termasuk NPWP/paket).
```
**Acceptance criteria:**
- [ ] Data tampil sesuai user login
- [ ] Perubahan tersimpan dengan validasi form yang wajar

---

## FASE 3 — Integrasi Google Drive

### Task 3.1 — GoogleDriveService
**Prompt:**
```
Buat app/Services/GoogleDriveService.php menggunakan Google API PHP Client
untuk autentikasi service account. Buat method createClientFolder(string $namaKlien)
yang membuat folder baru di Drive dan mengembalikan folder ID, serta
uploadFile(string $folderId, UploadedFile $file) yang upload file ke folder
tersebut dan mengembalikan file_id + file_url.
```
**Acceptance criteria:**
- [ ] Folder baru berhasil terbuat di akun Drive saat dites manual
- [ ] File berhasil terupload ke folder yang benar

### Task 3.2 — Hubungkan ke Form Upload Klien
**Prompt:**
```
Update halaman detail laporan (Task 2.3) agar punya form upload dokumen yang
memanggil GoogleDriveService::uploadFile(), lalu simpan metadata (nama_file,
jenis_dokumen, drive_file_id, drive_file_url, uploaded_by) ke tabel documents.
Setelah upload sukses, ubah status tax_report jadi "diproses" jika sebelumnya
"menunggu_dokumen".
```
**Acceptance criteria:**
- [ ] Upload dari form klien tersimpan ke Drive dan tabel documents
- [ ] Status laporan berubah otomatis sesuai kondisi di atas
- [ ] Daftar dokumen di halaman detail laporan menampilkan link ke Drive

### Task 3.3 — Auto-Create Folder Saat Klien Baru
**Prompt:**
```
Saat admin membuat client_profile baru (dari Task 4.3), panggil
GoogleDriveService::createClientFolder() otomatis dan simpan folder ID ke
kolom drive_folder_id.
```
**Acceptance criteria:**
- [ ] Folder Drive baru otomatis terbuat saat klien baru ditambahkan

---

## FASE 4 — Dashboard Admin

Struktur menu sidebar admin (acuan untuk task ini): **Dashboard, Klien, Laporan Pajak, Notifikasi, Paket Layanan**.

### Task 4.1 — Layout & Sidebar Admin
**Prompt:**
```
Buat layout Blade terpisah untuk area admin (/admin/*) dengan sidebar berisi
menu: Dashboard, Klien, Laporan Pajak, Notifikasi, Paket Layanan. Gaya visual
sama dengan layout klien (navy/teal) tapi tambahkan indikator role "Admin" di
topbar agar mudah dibedakan.
```
**Acceptance criteria:**
- [ ] Layout admin terpisah dari layout klien, tidak tercampur
- [ ] Semua item menu mengarah ke route yang benar

### Task 4.2 — Dashboard Admin
**Prompt:**
```
Buat halaman /admin/dashboard menampilkan: jumlah klien aktif, jumlah laporan
per status, dan daftar laporan dengan deadline dalam 7 hari ke depan
(urutkan dari yang paling dekat).
```
**Acceptance criteria:**
- [ ] Angka ringkasan sesuai data aktual di database
- [ ] Daftar deadline terurut benar dan hanya menampilkan 7 hari ke depan

### Task 4.3 — Manajemen Klien
**Prompt:**
```
Buat halaman /admin/klien (daftar semua klien dengan filter jenis_klien dan
status laporan aktif) dan /admin/klien/{id} (detail klien: profil, semua
dokumen, riwayat laporan). Tambahkan form untuk menambah klien baru
(otomatis buat user + client_profile, trigger Task 3.3).
```
**Acceptance criteria:**
- [ ] Filter di daftar klien berfungsi
- [ ] Detail klien menampilkan semua data terkait dengan benar
- [ ] Tambah klien baru otomatis membuat folder Drive

### Task 4.4 — Update Status Laporan
**Prompt:**
```
Di halaman detail klien atau detail laporan (admin), tambahkan aksi untuk
mengubah status tax_report (dropdown sesuai enum status). Setiap perubahan
status memicu pemanggilan WhatsappNotifierService::send() ke klien terkait
(implementasi service-nya ada di Task 5.1 — untuk sekarang cukup panggil
method-nya, boleh dummy/stub dulu jika Task 5.1 belum jalan).
```
**Acceptance criteria:**
- [ ] Perubahan status tersimpan dan langsung terlihat di dashboard klien
- [ ] Pemanggilan notifikasi terjadi (dicek lewat log jika service masih stub)

### Task 4.5 — Kelola Paket Layanan
**Prompt:**
```
Buat halaman /admin/paket-layanan dengan CRUD sederhana untuk tabel
service_packages (nama_paket, jenis_klien, harga, fitur).
```
**Acceptance criteria:**
- [ ] Admin bisa tambah/edit/hapus paket tanpa error
- [ ] Paket yang sudah dipakai klien tidak bisa dihapus (validasi)

---

## FASE 5 — Notifikasi WhatsApp

### Task 5.1 — WhatsappNotifierService
**Prompt:**
```
Buat app/Services/WhatsappNotifierService.php yang mengirim pesan lewat API
Fonnte (atau Wablas, sesuaikan dengan akun yang tersedia). Method send(string
$phone, string $message, string $tipe, int $clientId) mengirim pesan dan
mencatat hasilnya ke tabel notifications_log (channel=whatsapp).
```
**Acceptance criteria:**
- [ ] Pesan test berhasil terkirim ke nomor WA percobaan
- [ ] Log tersimpan di notifications_log dengan status sesuai response API

### Task 5.2 — Scheduled Command Reminder Deadline
**Prompt:**
```
Buat Artisan command SendDeadlineReminders yang mengecek tax_reports dengan
deadline_tanggal jatuh H-7 dan H-1 dari hari ini (status belum "selesai"),
lalu kirim WA lewat WhatsappNotifierService ke klien terkait. Daftarkan
command ini di scheduler agar jalan setiap hari (app/Console/Kernel.php atau
routes/console.php sesuai versi Laravel).
```
**Acceptance criteria:**
- [ ] Command bisa dijalankan manual (`php artisan reminder:send`) dan hasilnya sesuai
- [ ] Scheduler terdaftar dengan benar (`php artisan schedule:list` menampilkan command ini)
- [ ] Tidak mengirim reminder dobel untuk laporan yang sama di hari yang sama

### Task 5.3 — Halaman Log Notifikasi (Admin)
**Prompt:**
```
Buat halaman /admin/notifikasi menampilkan riwayat notifications_log
(klien, tipe, channel, waktu kirim, status) dengan filter tanggal dan status.
```
**Acceptance criteria:**
- [ ] Data log tampil terurut dari yang terbaru
- [ ] Filter berfungsi sesuai kolom yang tersedia

---

## FASE 6 — Invoice & E-signature (Dasar)

### Task 6.1 — Migration & Model Invoice, NDA
**Prompt:**
```
Buat migration untuk tabel invoices (client_id, tax_report_id, periode, nominal,
status_bayar, payment_method, payment_gateway_ref, paid_at) dan nda_agreements
(client_id, document_url atau signature_data, signed_at) sesuai PROJECT.md
bagian ERD. Buat model Invoice dan NdaAgreement dengan relasi ke ClientProfile.
Tambahkan juga kolom nda_signed_at ke tabel client_profiles.
```
**Acceptance criteria:**
- [ ] Migration jalan tanpa error
- [ ] Relasi Eloquent bisa dites lewat tinker

### Task 6.2 — CRUD Invoice Manual (Admin)
**Prompt:**
```
Buat halaman /admin/invoice untuk admin membuat invoice manual per klien
(pilih client, tax_report terkait, nominal, periode) dan melihat daftar semua
invoice dengan filter status_bayar.
```
**Acceptance criteria:**
- [ ] Admin bisa buat invoice baru dan invoice tersimpan dengan benar
- [ ] Filter status_bayar berfungsi

### Task 6.3 — Halaman Invoice Klien
**Prompt:**
```
Buat halaman /invoice untuk klien melihat riwayat invoice miliknya beserta
status pembayaran (belum_bayar/menunggu_verifikasi/lunas).
```
**Acceptance criteria:**
- [ ] Klien hanya melihat invoice miliknya sendiri

### Task 6.4 — ESignatureService & Alur NDA
**Prompt:**
```
Buat app/Services/ESignatureService.php yang integrasi dengan Privy atau
tekenaja (sesuaikan akun yang tersedia) untuk method requestSignature(client_id).
Buat halaman /nda tempat klien melihat isi NDA dan menandatangani. Setelah
signed_at terisi di nda_agreements, update juga client_profiles.nda_signed_at.
```
**Acceptance criteria:**
- [ ] Klien bisa menandatangani NDA lewat provider e-signature
- [ ] Setelah tanda tangan, nda_signed_at di client_profiles terisi

---

## FASE 7 — Multi-Entity & Onboarding

### Task 7.1 — Ubah Relasi ke Multi-Entity
**Prompt:**
```
Ubah relasi User↔ClientProfile dari 1—1 menjadi 1—N (satu user bisa punya
beberapa client_profiles). Sesuaikan semua query yang sebelumnya asumsi
1 user = 1 client_profile (dashboard, laporan, dokumen) agar berdasarkan
entitas yang sedang aktif (simpan client_profile_id aktif di session).
```
**Acceptance criteria:**
- [ ] User dengan lebih dari satu client_profile tidak error di halaman manapun
- [ ] Data yang tampil selalu sesuai entitas aktif di session

### Task 7.2 — Fitur Switch Entitas
**Prompt:**
```
Tambahkan dropdown di topbar klien untuk memilih entitas aktif (jika user
punya lebih dari satu client_profile). Endpoint POST /entitas/{id}/switch
mengubah client_profile_id aktif di session lalu redirect ke dashboard.
```
**Acceptance criteria:**
- [ ] Switch entitas mengubah data yang tampil di seluruh halaman klien
- [ ] User tidak bisa switch ke entitas milik user lain

### Task 7.3 — Wizard Onboarding & Gerbang NDA
**Prompt:**
```
Buat middleware EnsureNdaSigned yang redirect klien ke alur onboarding
(ganti password default → lengkapi profil → tanda tangan NDA di /nda) jika
client_profiles.nda_signed_at masih null. Terapkan middleware ini ke semua
route dashboard/laporan klien.
```
**Acceptance criteria:**
- [ ] Klien baru tidak bisa akses dashboard laporan sebelum NDA ditandatangani
- [ ] Setelah NDA ditandatangani, redirect otomatis berhenti terjadi

---

## FASE 8 — Kalender Pajak & Auto-Generate Laporan

### Task 8.1 — Migration & Seeder Kalender Pajak
**Prompt:**
```
Buat migration tabel tax_calendars (jenis_klien, jenis_laporan,
hari_mulai_periode, hari_deadline, frekuensi) sesuai PROJECT.md. Buat seeder
jadwal untuk tiap jenis_klien (contoh: dokter/pengacara/notaris = spt_masa
bulanan, PT/CV = spt_masa bulanan + spt_tahunan_badan tahunan).
```
**Acceptance criteria:**
- [ ] Data kalender tersimpan sesuai jadwal riil per jenis klien

### Task 8.2 — Command Auto-Generate Laporan
**Prompt:**
```
Buat Artisan command GenerateScheduledTaxReports yang, untuk setiap
client_profile aktif, mengecek tax_calendars sesuai jenis_klien-nya dan
membuat tax_report baru jika sudah waktunya (sesuai hari_mulai_periode),
dengan status awal "menunggu_dokumen" dan deadline_tanggal dari
hari_deadline. Pastikan tidak membuat duplikat untuk periode yang sama.
Daftarkan command ini di scheduler untuk jalan tiap hari.
```
**Acceptance criteria:**
- [ ] Command membuat tax_report baru sesuai jadwal, tanpa duplikat
- [ ] `php artisan schedule:list` menampilkan command ini

### Task 8.3 — Halaman Kelola Kalender Pajak (Admin)
**Prompt:**
```
Buat halaman /admin/kalender-pajak dengan CRUD untuk tabel tax_calendars.
```
**Acceptance criteria:**
- [ ] Admin bisa tambah/edit/hapus jadwal tanpa error

---

## FASE 9 — Revisi Dokumen

### Task 9.1 — Alur Revisi Dokumen
**Prompt:**
```
Tambahkan kolom catatan_revisi ke tax_reports (jika belum ada dari migration
sebelumnya). Di halaman detail laporan admin, tambahkan form "Kirim Revisi"
yang mengisi catatan_revisi dan mengubah status jadi menunggu_dokumen. Trigger
WhatsappNotifierService::send() dengan tipe=revisi_dokumen ke klien.
```
**Acceptance criteria:**
- [ ] Status berubah dan catatan tersimpan saat admin kirim revisi
- [ ] Notifikasi WA terkirim ke klien terkait

### Task 9.2 — Tampilkan Catatan Revisi ke Klien
**Prompt:**
```
Di halaman detail laporan klien, tampilkan catatan_revisi (jika ada) dengan
tampilan mencolok (misal banner kuning) di atas form upload dokumen.
```
**Acceptance criteria:**
- [ ] Catatan revisi terlihat jelas oleh klien sebelum upload ulang

---

## FASE 10 — Invoice Otomatis & Payment Gateway

### Task 10.1 — Auto-Generate Invoice
**Prompt:**
```
Buat Eloquent event listener (observer) pada TaxReport: ketika status berubah
menjadi "dilaporkan", otomatis buat record Invoice baru (nominal dari
service_packages.harga milik client tersebut, status_bayar=belum_bayar).
Trigger notifikasi WA tipe=invoice_terbit ke klien.
```
**Acceptance criteria:**
- [ ] Invoice otomatis terbuat tiap kali status laporan jadi "dilaporkan"
- [ ] Tidak ada invoice dobel untuk laporan yang sama

### Task 10.2 — PaymentGatewayService
**Prompt:**
```
Buat app/Services/PaymentGatewayService.php integrasi dengan Midtrans atau
Xendit. Method createTransaction(Invoice $invoice) membuat transaksi dan
mengembalikan URL pembayaran. Buat endpoint POST /invoice/{id}/bayar yang
memanggil method ini dan redirect klien ke halaman pembayaran.
```
**Acceptance criteria:**
- [ ] Klien bisa mulai transaksi dan diarahkan ke halaman pembayaran gateway

### Task 10.3 — Webhook Konfirmasi Pembayaran
**Prompt:**
```
Buat endpoint POST /webhook/payment yang menerima callback dari payment
gateway, verifikasi signature-nya, lalu update status_bayar invoice terkait
jadi "lunas" dan isi payment_method + paid_at. Trigger notifikasi WA ke
klien bahwa pembayaran diterima.
```
**Acceptance criteria:**
- [ ] Webhook memvalidasi signature dan menolak request yang tidak sah
- [ ] Status invoice terupdate otomatis setelah pembayaran sukses (dites pakai sandbox gateway)

---

## FASE 11 — Staff Role, Export PDF, Audit Log

### Task 11.1 — Role Staff
**Prompt:**
```
Tambahkan role "staff" ke enum users.role. Sesuaikan EnsureRole middleware
agar staff punya akses ke halaman admin tapi tanpa akses ke pengaturan
sensitif (misal kelola paket layanan dan kalender pajak tetap admin-only).
```
**Acceptance criteria:**
- [ ] User staff bisa akses dashboard admin & kelola laporan/klien
- [ ] User staff tidak bisa akses halaman admin-only tertentu

### Task 11.2 — Export PDF Ringkasan Laporan
**Prompt:**
```
Buat endpoint GET /laporan/{id}/export-pdf yang menghasilkan PDF ringkasan
laporan pajak (data laporan + daftar dokumen) memakai DomPDF, dengan layout
sederhana dan header berisi nama entitas klien.
```
**Acceptance criteria:**
- [ ] PDF berhasil diunduh dan datanya sesuai laporan yang dipilih

### Task 11.3 — Audit Log
**Prompt:**
```
Buat migration & model ActivityLog (user_id, action, subject_type, subject_id,
created_at). Buat trait atau observer yang otomatis mencatat log setiap kali
status tax_report, dokumen, atau invoice berubah. Buat halaman
/admin/activity-log untuk melihat riwayatnya dengan filter tanggal/user.
```
**Acceptance criteria:**
- [ ] Perubahan status/dokumen/invoice tercatat otomatis di activity_logs
- [ ] Halaman log menampilkan data terurut dari yang terbaru, filter berfungsi

---

## Tips Menjalankan Task di opencode
- Salin satu blok **Prompt** saja per sesi, jangan gabung beberapa task
- Setelah opencode selesai, cek satu-satu **Acceptance criteria** sebelum lanjut ke task berikutnya
- Kalau opencode mulai melebar dari task (menambah fitur yang tidak diminta), hentikan dan minta ia fokus ulang ke prompt yang diberikan
- Commit ke git setelah tiap task lulus acceptance criteria, supaya mudah rollback kalau task berikutnya bermasalah
