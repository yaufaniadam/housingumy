# Proyek Antigravity: Sistem Manajemen Tim Housing UMY

## 1. Ringkasan Proyek
Otomatisasi pengelolaan University Resident, Ma’had Ali, Wisma Pascasarjana, dan Professor Guest House melalui satu sistem terpadu.

## 2. Struktur Modul (Blueprint Fase 1)
* **Modul 1: Manajemen Properti**: CRUD Gedung, Kamar, dan Fasilitas.
* **Modul 2: Reservasi**: Alur khusus Unit Kerja (Approval Admin) dan Alur Publik (Mandiri).
* **Modul 3: Keuangan (Filament Resource)**: Input pendapatan/belanja, laporan laba rugi.
* **Modul 4: Dashboard & Pelaporan (Filament Widgets)**: Laporan okupansi dan transaksi harian/bulanan/tahunan.

## 3. Kebijakan Bisnis Utama
* **Tarif**: Satu harga publik untuk mahasiswa, staf, dosen, dan umum (Personal).
* **Internal**: Alur khusus untuk Unit Kerja/Biro dengan tarif internal.
* **Check-in**: Validasi menggunakan QR Code.

## 4. Tech Stack (Updated)
* **Framework**: Laravel 12.
* **Admin Panel**: Filament v4 (TALL Stack - Tailwind, Alpine.js, Laravel, Livewire).
* **Database**: MySQL.
* **Server**: VPS (Ubuntu/Nginx).
