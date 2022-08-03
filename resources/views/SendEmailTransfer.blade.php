@component('mail::message')
#Pengajuan Transfer Modal
 
Ada Pengajuan Transfer Modal Hari Ini {{ $item->tanggal_modal }} sebesar Rp. {{ number_format($item->riwayat_modal) }},
Yuk Cek Sekarang untuk Memproses Modal!, Klik Button Dibawah Ini

@component('mail::button', ['url' => $url])
Proses Modal
@endcomponent
 
Pegawai Bertugas,<br>
{{ $item->Pegawai->name }}
@endcomponent