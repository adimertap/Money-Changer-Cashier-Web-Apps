# Desain LKUB

## Tujuan

Menambahkan menu **LKUB** di bawah **Pelaporan > Rekapitulasi Cabang** untuk membuat laporan kegiatan usaha bulanan per currency dan cabang dalam format Excel atau PDF.

## Filter

- Bulan: Januari sampai Desember.
- Tahun: tahun laporan.
- Cabang: checkbox cabang aktif dan opsi Semua Cabang.
- User hanya dapat memilih cabang yang memang diizinkan untuknya.

## Sumber Data

| Kebutuhan | Tabel | Kolom | Keterangan |
|---|---|---|---|
| Daftar cabang | `tb_master_cabang` | `cabang_id`, `cabang_name`, `is_active` | Hanya cabang aktif dan yang diizinkan user. |
| Daftar currency | `tb_currency` | `id_currency`, `nama_currency` | Nama forex yang ditampilkan pada laporan. |
| Arus transaksi | `tb_jurnal` | `cabang_id`, `id_currency`, `tanggal_jurnal`, `jenis_jurnal`, `jumlah_tukar`, `total_tukar` | Sumber utama saldo jumlah valas dan saldo rupiah. |
| Jenis transaksi | `tb_jurnal` | `jenis_jurnal` | `Debit` berarti BUY; `Kredit Jual` berarti SELL. |

Relasi currency pada jurnal adalah:

```text
tb_jurnal.id_currency = tb_currency.id_currency
```

Relasi cabang pada jurnal adalah:

```text
tb_jurnal.cabang_id = tb_master_cabang.cabang_id
```

## Definisi Kolom dan Rumus

Laporan dihitung untuk setiap kombinasi **cabang + currency** yang memiliki jurnal Debit atau Kredit Jual pada bulan dan tahun terpilih. Saldo pembukaan tetap dihitung untuk currency tersebut bila tersedia.

| Kolom LKUB | Sumber data | Rumus / aturan |
|---|---|---|
| `NO` | Hasil urutan laporan | Nomor urut baris, dimulai dari 1. |
| `FOREX (CURRENCY)` | `tb_currency.nama_currency` | Nama currency berdasarkan `tb_jurnal.id_currency`. |
| `TYPE` | Nilai tetap | Selalu diisi `UKA`. |
| `BG. BALANCE` | `tb_jurnal.jumlah_tukar` | Saldo jumlah valas sebelum tanggal 1 bulan laporan: `SUM(jumlah_tukar Debit) - SUM(jumlah_tukar Kredit Jual)`. |
| `BG. BALANCE (Rp.)` | `tb_jurnal.total_tukar` | Saldo rupiah sebelum tanggal 1 bulan laporan: `SUM(total_tukar Debit) - SUM(total_tukar Kredit Jual)`. |
| `BUY` | `tb_jurnal.jumlah_tukar` | Total `jumlah_tukar` dengan `jenis_jurnal = Debit` selama bulan dan tahun terpilih. |
| `BUY (Rp.)` | `tb_jurnal.total_tukar` | Total `total_tukar` dengan `jenis_jurnal = Debit` selama bulan dan tahun terpilih. |
| `SELL` | `tb_jurnal.jumlah_tukar` | Total `jumlah_tukar` dengan `jenis_jurnal = Kredit Jual` selama bulan dan tahun terpilih. |
| `SELL (Rp.)` | `tb_jurnal.total_tukar` | Total `total_tukar` dengan `jenis_jurnal = Kredit Jual` selama bulan dan tahun terpilih. |
| `BALANCE` | Kolom LKUB | `BG. BALANCE + BUY - SELL`. |
| `MIDDLE RATE` | Kolom LKUB | `BALANCE (Rp.) / BALANCE` jika `BALANCE != 0`; jika nol, diisi 0. |
| `BALANCE (Rp.)` | Kolom LKUB | `BG. BALANCE (Rp.) + BUY (Rp.) - SELL (Rp.)`. |

### Batas Periode

- Currency laporan ditentukan dari jurnal Debit atau Kredit Jual pada tanggal 1 sampai hari terakhir bulan laporan.
- Saldo awal (`BG. BALANCE`) untuk currency tersebut memakai semua jurnal dengan tanggal `< tanggal 1 bulan laporan`.
- BUY dan SELL memakai jurnal dari tanggal 1 sampai hari terakhir bulan laporan.
- Jika tidak ada jurnal Debit atau Kredit Jual pada bulan terpilih, laporan tidak membuat baris currency hanya dari saldo pembukaan.
- Filter cabang selalu diterapkan pada `tb_jurnal.cabang_id`.
- Filter currency selalu diterapkan pada `tb_jurnal.id_currency`.
- Query mengikuti seluruh baris `tb_jurnal` yang tersedia; model `Jurnal` saat ini tidak menggunakan soft delete.

### Contoh Rumus

Misalnya untuk USD pada cabang tertentu:

```text
BG. BALANCE       = saldo Debit sebelum periode - saldo Kredit Jual sebelum periode
BG. BALANCE (Rp.) = saldo Rupiah Debit sebelum periode - saldo Rupiah Kredit Jual sebelum periode
BUY               = jumlah Debit bulan berjalan
BUY (Rp.)         = Rupiah Debit bulan berjalan
SELL              = jumlah Kredit Jual bulan berjalan
SELL (Rp.)        = Rupiah Kredit Jual bulan berjalan
BALANCE           = BG. BALANCE + BUY - SELL
BALANCE (Rp.)     = BG. BALANCE (Rp.) + BUY (Rp.) - SELL (Rp.)
MIDDLE RATE       = BALANCE (Rp.) / BALANCE
```

Jika `BALANCE` bernilai nol, `MIDDLE RATE` bernilai `0` agar tidak terjadi pembagian dengan nol.

## Output

### Excel

- Satu workbook.
- Satu worksheet untuk setiap cabang yang dipilih.
- Setiap worksheet berisi judul LKUB, periode bulan/tahun, nama cabang, dan tabel 12 kolom.
- Angka diberi format numerik dan header diberi border, bold, dan alignment terpusat.

### PDF

- Satu PDF untuk semua cabang yang dipilih.
- Setiap cabang menjadi section terpisah.
- Landscape agar 12 kolom tetap terbaca.
- Judul dan susunan kolom mengikuti Excel.

## Akses Menu

Menu memakai key `lkub` pada sistem `AccessMenu`/`RoleMenu`.

- Owner otomatis dapat melihat menu.
- Role lain hanya dapat melihat menu jika key `lkub` diberikan pada hak akses role.

## Validasi

- Bulan wajib bernilai 1 sampai 12.
- Tahun wajib berupa tahun yang valid.
- Jika `Semua Cabang` tidak dipilih, minimal satu checkbox cabang wajib dipilih.
- Semua `cabang_id` harus termasuk cabang aktif yang diizinkan user.
- Format output harus `excel` atau `pdf`.
- Jika tidak ada jurnal Debit atau Kredit Jual pada bulan terpilih untuk seluruh cabang/currency yang dipilih, proses download ditolak dengan pesan yang jelas.

## Ruang Lingkup yang Tidak Ditambahkan

- Tidak membuat tabel snapshot saldo baru.
- Tidak memakai saldo terkini `tb_currency.jumlah_valas` untuk laporan historis.
- Tidak mengubah proses transaksi atau jurnal yang sudah berjalan.
- Tidak menambahkan pipeline frontend baru.
