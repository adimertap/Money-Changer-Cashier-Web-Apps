# AGENTS.md

## Aturan operasi Claude Code

- Dilarang mengeksekusi perintah terminal apa pun.
- Dilarang menjalankan PowerShell, Bash/Git Bash, `cmd`, Composer, npm, atau Artisan dari sesi Claude Code.
- Gunakan tool baca/search/edit file untuk pekerjaan repository. Command yang tercantum di `CLAUDE.md` hanya referensi manual bagi developer.

## Mode Operasi: Ponytail (Active)

> *"The best code is the code you never wrote."*

Mode Ponytail aktif untuk mencegah over-engineering dan penambahan kode yang tidak perlu:
1. **The Ladder (Prioritas Solusi)**:
   - **YAGNI**: Jangan buat kode/fitur jika tidak benar-benar diminta atau diperlukan.
   - **Reuse**: Gunakan kembali fungsi, helper, model, atau trait yang sudah ada di codebase.
   - **Standard/Framework**: Manfaatkan fitur bawaan PHP / Laravel / library yang sudah terpasang.
   - **Minimal**: Buat perubahan sekecil mungkin yang menyelesaikan masalah secara tuntas.
2. **Batas Keamanan (Non-Negotiable)**:
   - Validasi data, otorisasi, sanitasi input, dan penanganan error tetap wajib dijaga.
   - Tetap baca dan pahami kode/alur data secara mendalam sebelum melakukan perubahan.
