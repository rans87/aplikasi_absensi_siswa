# 🚀 QUICK START GUIDE - PresenceX

## 📌 Akun Default

### Admin
```
URL: http://localhost:8000/login
Tab: Admin
Email: admin@presencex.com
Password: admin123
```

## 🎯 Cara Menggunakan Sistem

### 1️⃣ Login sebagai Admin
1. Buka browser dan akses `http://localhost:8000/login`
2. Klik tab **"Admin"**
3. Masukkan:
   - Username/Email: `admin@presencex.com`
   - Password: `admin123`
4. Klik **"Masuk sebagai Admin"**

### 2️⃣ Membuat Akun Guru (Admin Only)
1. Setelah login sebagai admin
2. Klik menu **"Guru"** di sidebar
3. Klik tombol **"Tambah Guru"**
4. Isi form:
   - **Nama**: Nama lengkap guru
   - **NIP**: Nomor Induk Pegawai (opsional)
   - **No HP**: Nomor telepon (opsional)
   - **Email**: Email untuk login (wajib, harus unique)
   - **Password**: Password untuk login (minimal 6 karakter)
5. Klik **"Simpan"**
6. Guru sekarang bisa login dengan email dan password yang dibuat

### 3️⃣ Login sebagai Guru
1. Buka `http://localhost:8000/login`
2. Klik tab **"Guru"**
3. Masukkan:
   - Email: Email yang dibuat oleh admin
   - Password: Password yang dibuat oleh admin
4. Klik **"Masuk sebagai Guru"**

### 4️⃣ Login sebagai Siswa
1. Buka `http://localhost:8000/login`
2. Klik tab **"Siswa"**
3. Masukkan:
   - **NIS**: Nomor Induk Siswa (dari API)
   - **Nama**: Nama lengkap (harus sesuai dengan data di API)
4. Klik **"Masuk sebagai Siswa"**
5. Sistem akan validasi ke API dan otomatis membuat akun jika valid

### 5️⃣ Logout
1. Klik **icon profile** di pojok kanan atas header
2. Klik **"Logout"**
3. Konfirmasi dengan klik **"Ya, Logout"** di popup SweetAlert

## 🎨 Fitur Halaman Login

- ✨ **Modern Design**: Gradient biru-ungu dengan glassmorphism
- ✨ **3 Tab Role**: Siswa, Guru, Admin dalam 1 halaman
- ✨ **Animasi Smooth**: Slide up, fade in, floating background
- ✨ **SweetAlert2**: Notifikasi cantik untuk error/success
- ✨ **Responsive**: Tampil bagus di semua device
- ✨ **Icon Visual**: Emoji icon untuk setiap input field

## 🔐 Keamanan

- ✅ Password di-hash dengan bcrypt
- ✅ CSRF protection di semua form
- ✅ Session regeneration setelah login
- ✅ Multi-guard authentication
- ✅ Protected routes dengan middleware

## 🛠️ Troubleshooting

### Login gagal?
- Pastikan email/username dan password benar
- Pastikan role yang dipilih sesuai
- Clear browser cache

### Login siswa gagal?
- Pastikan NIS benar
- Pastikan Nama sesuai dengan data di API
- Check koneksi internet (untuk API call)

### SweetAlert tidak muncul?
- Pastikan koneksi internet (untuk CDN)
- Check browser console untuk error

## 📞 Need Help?

Jika ada masalah, check file `CHANGELOG_IMPROVEMENTS.md` untuk dokumentasi lengkap.

---

**Happy Coding! 🎉**
