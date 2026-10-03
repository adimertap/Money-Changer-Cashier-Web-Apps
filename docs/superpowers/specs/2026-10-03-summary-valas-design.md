# Desain Summary Valas

## Tujuan

Menambahkan menu **Summary Valas** di bawah **Pelaporan** untuk menyajikan saldo awal, mutasi periode, saldo akhir, dan nilai rupiah mutasi currency dalam format Excel atau PDF landscape.

## Filter

- Cabang: satu cabang tertentu atau Semua Cabang.
- Tanggal mulai dan tanggal akhir: periode inklusif.
- Currency: satu currency tertentu atau Semua Currency.
- Output: Excel atau PDF.
- User hanya dapat memilih cabang aktif yang diizinkan untuknya.

## Sumber Data

| Kebutuhan | Tabel | Kolom | Keterangan |
|---|---|---|---|
| Daftar cabang | `tb_master_cabang` | `cabang_id`, `cabang_name`, `is_active` | Hanya cabang aktif yang diizinkan user. |
| Daftar currency | `tb_currency` | `id_currency`, `nama_currency`, `country` | `nama_currency` menjadi FOREX CODE; `country` menjadi NAMA CURRENCY. |
| Saldo dan mutasi | `tb_jurnal` | `cabang_id`, `id_currency`, `tanggal_jurnal`, `jenis_jurnal`, `jumlah_tukar`, `total_tukar` | Sumber utama laporan. |

Relasi jurnal ke currency adalah `tb_jurnal.id_currency = tb_currency.id_currency`.

## Definisi Kolom dan Rumus

| Kolom | Rumus / aturan |
|---|---|
| `FOREX CODE` | `tb_currency.nama_currency`. |
| `NAMA CURRENCY` | `tb_currency.country`, dengan fallback ke kode jika kosong. |
| `BEGINNING BALANCE` | `SUM(jumlah_tukar Debit sebelum start) - SUM(jumlah_tukar Kredit Jual sebelum start)`. |
| `MUTATION BUY` | `SUM(jumlah_tukar Debit selama periode)`. |
| `MUTATION SELL` | `SUM(jumlah_tukar Kredit Jual selama periode)`. |
| `ENDING BALANCE` | `BEGINNING BALANCE + MUTATION BUY - MUTATION SELL`. |
| `MUTATION (IDR) BUY` | `SUM(total_tukar Debit selama periode)`. |
| `MUTATION (IDR) SELL` | `SUM(total_tukar Kredit Jual selama periode)`. |

`Debit` dipetakan sebagai BUY dan `Kredit Jual` sebagai SELL. Periode memakai batas tanggal mulai pukul 00:00:00 sampai tanggal akhir pukul 23:59:59. Currency ditampilkan bila memiliki saldo awal atau jurnal pada periode terpilih.

## Output

### Excel

Satu worksheet berisi delapan kolom laporan. Nilai quantity dan IDR dikirim sebagai angka; kolom `MUTATION (IDR) BUY` dan `MUTATION (IDR) SELL` menggunakan format angka `Rp #,##0`, bukan string berawalan `Rp.`.

### PDF

Satu tabel landscape A4 dengan delapan kolom yang sama. Nilai IDR diformat untuk tampilan menggunakan pemisah ribuan Indonesia dan awalan `Rp.`.

## Akses dan Validasi

- Menu memakai key `summary-valas` pada sistem `AccessMenu`/`RoleMenu`.
- Cabang yang dipilih harus aktif dan termasuk cabang yang diizinkan user.
- Currency yang dipilih harus berasal dari `tb_currency` yang tersedia.
- Tanggal akhir tidak boleh mendahului tanggal mulai.
- Currency wajib dipilih bila Semua Currency tidak dicentang.
- Download ditolak dengan pesan jika tidak ada row laporan.

## Ruang Lingkup

- Tidak membuat tabel snapshot saldo baru.
- Tidak menambahkan dependency atau pipeline frontend baru.
- Tidak mengubah format jurnal yang sudah ada.
