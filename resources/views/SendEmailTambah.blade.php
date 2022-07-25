@component('mail::message')
#Pengajuan Tambahan Modal
 
Ada Pengajuan Tambahan Modal Hari Ini {{ $item->tanggal_modal }} sebesar Rp. {{ number_format($item->pengajuan_tambah) }},
Yuk Cek Sekarang untuk Memproses Modal!, Klik Button Dibawah Ini

@component('mail::button', ['url' => $url])
Proses Modal
@endcomponent
 
Pegawai Bertugas,<br>
{{ $item->Pegawai->name }}
@endcomponent