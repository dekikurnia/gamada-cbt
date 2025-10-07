# Gamada CBT

**Gamada CBT** adalah proyek **open source** berbasis web yang dirancang untuk memfasilitasi **Computer Based Test (CBT)** atau ujian berbasis komputer dengan arsitektur **modern, modular, dan scalable**.  
Proyek ini ditujukan untuk digunakan oleh sekolah, lembaga pelatihan, atau organisasi pendidikan yang ingin menyelenggarakan ujian secara efisien dan fleksibel — baik **secara lokal (LAN)** maupun **online**.

---

## 🚀 Fitur Utama

- Manajemen pengguna (Admin, Guru, Siswa)
- Pembuatan dan pengelolaan ujian (Exam)
- Soal pilihan ganda, isian singkat, dan esai
- Koreksi otomatis untuk soal objektif
- Rekap nilai dan hasil ujian
- Sistem login aman dengan **Laravel Sanctum**
- Dapat dijalankan **di Docker container** untuk kemudahan deployment
- Dirancang agar ringan — tetap bisa dijalankan di **shared hosting** atau **XAMPP** jika diperlukan

---

## 🧱 Teknologi yang Digunakan

### Backend
- **Laravel 11 (PHP 8.3+)** → framework utama untuk API dan logic server
- **MySQL 8** → sistem manajemen basis data
- **Nginx** → web server yang digunakan dalam container
- **Docker & Docker Compose** → untuk memisahkan environment development

### Frontend
- **Svelte** → framework frontend modern dan ringan untuk tampilan user (CBT Client)
- **Tailwind CSS** → styling cepat dan konsisten
- **Axios** → komunikasi API dengan backend

### DevOps & CI/CD
- **Git & GitHub** → version control dan kolaborasi
- **Jenkins** → integrasi otomatis untuk build & test (planned)
- **Docker Volume** → penyimpanan data persisten

---
