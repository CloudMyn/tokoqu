# Standar Issue, PR dan Commit

Created time: March 17, 2024 4:12 PM
Last edited time: April 3, 2024 11:24 PM

# A. Format Issue

1. **☢️ Bug**
    - **Format:** `[☢️ Bug] Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[☢️ Bug] Login Error: User cannot log in using Google account`
    - **Aturan:** Gunakan untuk melaporkan kesalahan atau masalah dalam sistem yang memerlukan perbaikan.
2. **🛠️ Dependencies**
    - **Format:** `[🛠️ Dependencies] Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[🛠️ Dependencies] React Update: The latest version fixes security issues`
    - **Aturan:** Gunakan untuk isu terkait pemeliharaan atau pembaruan dependensi.
3. **📚 Documentation**
    - **Format:** `[📚 Documentation] Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[📚 Documentation] README Update: Added section about contributions`
    - **Aturan:** Gunakan untuk perbaikan atau penambahan pada dokumentasi proyek.
4. **✏️ Feature**
    - **Format:** `[✏️ Feature] Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[✏️ Feature] New Chat System: Implementation of a real-time chat system`
    - **Aturan:** Gunakan untuk permintaan atau ide tentang fitur baru.
5. **🧹 Updates**
    - **Format:** `[🧹 Updates] Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[🧹 Updates] Refactor Login Function: Improve code efficiency`
    - **Aturan:** Gunakan untuk perubahan atau pembaruan umum yang tidak masuk dalam kategori lain.

# B. Format Pull Request

1. **☢️ Bug**
    - **Format:** `[☢️ Bug] (Issue #Nomor) Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[☢️ Bug] (Issue #102) Fix Login Error: Fixed authentication bug`
    - **Aturan:** PR harus langsung mengatasi bug yang dijelaskan dalam issue terkait.
2. **🛠️ Dependencies**
    - **Format:** `[🛠️ Dependencies] (Issue #Nomor) Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[🛠️ Dependencies] (Issue #78) Update React Version: Upgrade React version to 17.0.2`
    - **Aturan:** PR harus fokus pada pemeliharaan atau pembaruan dependensi.
3. **📚 Documentation**
    - **Format:** `[📚 Documentation] (Issue #Nomor) Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[📚 Documentation] (Issue #56) Update Installation Guide: Added instructions for macOS`
    - **Aturan:** PR harus berfokus pada perbaikan atau penambahan dokumentasi.
4. **✏️ Feature**
    - **Format:** `[✏️ Feature] (Issue #Nomor) Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[✏️ Feature] (Issue #145) New Dashboard UI: Redesigned dashboard interface`
    - **Aturan:** PR harus mencakup implementasi atau penambahan fitur baru.
5. **🧹 Updates**
    - **Format:** `[🧹 Updates] (Issue #Nomor) Judul Singkat: Deskripsi Detil`
    - **Contoh:** `[🧹 Updates] (Issue #88) Code Optimization: Search function optimization`
    - **Aturan:** PR harus mencakup pembaruan atau perubahan umum pada kode atau sistem.

# C. Commit

### Format Umum:

```
<tipe>: <pesan singkat>
```

### Keterangan:

- `<tipe>`: Jenis perubahan yang dilakukan.
    - `feat`: Fitur baru atau perubahan signifikan pada fitur yang ada.
    - `fix`: Perbaikan bug.
    - `docs`: Perubahan pada dokumentasi.
    - `style`: Perubahan yang tidak memengaruhi arti kode.
    - `refactor`: Perubahan kode yang tidak menambah atau mengurangi fitur atau bug.
    - `perf`: Perubahan kode yang meningkatkan kinerja.
    - `test`: Menambah atau memperbaiki tes.
    - `chore`: Perubahan pada proses build atau alat bantu.
    - `assets`: Perubahan pada assets
    
- `<pesan singkat>`: Pesan singkat yang menjelaskan perubahan (maksimal 50 karakter).

### Format Commit dengan Ikon

1. **`feat` - Fitur Baru atau Perubahan Signifikan**
    - Ikon: ✨
    - Contoh: `✨ feat: Implementation of a login system with OAuth`
2. **`fix` - Perbaikan Bug**
    - Ikon: 🐛
    - Contoh: `🐛 fix: Fixed a bug in database queries`
3. **`docs` - Perubahan pada Dokumentasi**
    - Ikon: 📚
    - Contoh: `📚 docs: Added installation instructions to README`
4. **`style` - Perubahan yang Tidak Memengaruhi Arti Kode**
    - Ikon: 💅
    - Contoh: `💅 style: Improve code formatting according to standards`
5. **`refactor` - Perubahan Kode yang Tidak Menambah atau Mengurangi Fitur atau Bug**
    - Ikon: 🔨
    - Contoh: `🔨 refactor: Changed the X function to be more modular`
6. **`perf` - Perubahan Kode yang Meningkatkan Kinerja**
    - Ikon: 🚀
    - Contoh: `🚀 perf: Search function performance optimization`
7. **`test` - Menambah atau Memperbaiki Tes**
    - Ikon: 🧪
    - Contoh: `🧪 test: Added unit tests for authentication module`
8. **`chore` - Perubahan pada Proses Build atau Alat Bantu**
    - Ikon: 🧹
    - Contoh: `🧹 chore: Updated build scripts for CI/CD integration`
9. **`assets` - Perubahan pada Assets**
    - Ikon: 🖼️
    - Contoh: `🖼️ assets: Added a new image to the assets directory`
10. 🔥**`hotfix`**
    - Ikon: `🔥`
    - Contoh: `🔥 hotfix: Fixed critical login bug affecting all users`

Mengingat kebutuhan Anda untuk mencerminkan detail yang lebih lengkap dari deskripsi issue dalam penamaan branch, kita dapat memperluas format untuk mengakomodasi deskripsi yang lebih deskriptif dan mendetail. Namun, perlu diingat bahwa nama branch sebaiknya tetap pada batas wajar untuk memudahkan pengelolaan.

# D. Branch

### 1. Format Umum:

```
<tipe>/<issue-nomor>-<deskripsi-lengkap>

```

- `<tipe>`: Jenis tugas yang diwakili oleh branch (bug, feature, docs, dll.)
- `<issue-nomor>`: Nomor issue terkait untuk referensi langsung.
- `<deskripsi-lengkap>`: Deskripsi lengkap yang mencerminkan isi dari issue.

### 2. Contoh Penamaan:

1. **Bug**
    - Format: `bug/<issue-nomor>-<deskripsi-lengkap>`
    - Contoh: `bug/102-login-error-user-cannot-login-using-google-account`
2. **Dependencies**
    - Format: `dependencies/<issue-nomor>-<deskripsi-lengkap>`
    - Contoh: `dependencies/78-react-update-latest-version-fixes-security-issues`
3. **Documentation**
    - Format: `docs/<issue-nomor>-<deskripsi-lengkap>`
    - Contoh: `docs/56-readme-update-added-section-about-contributions`
4. **Feature**
    - Format: `feature/<issue-nomor>-<deskripsi-lengkap>`
    - Contoh: `feature/145-new-chat-system-implementation-of-real-time-chat`
5. **Updates**
    - Format: `update/<issue-nomor>-<deskripsi-lengkap>`
    - Contoh: `update/88-refactor-login-function-improve-code-efficiency`

### 3. Aturan Tambahan:

- Meskipun lebih deskriptif, cobalah untuk menjaga penamaan branch cukup ringkas untuk keterbacaan.
- Gunakan tanda hubung untuk memisahkan kata-kata.
- Pastikan penamaan branch secara langsung mencerminkan fokus kerja pada branch tersebut.