@component('mail::message')
#Pengajuan Modal
 
Ada Pengajuan Modal Hari Ini {{ $data->tanggal_modal }} sebesar Rp. {{ number_format($data->jumlah_modal) }},
Yuk Cek Sekarang untuk Memproses Modal!, Klik Button Dibawah Ini
 
@component('mail::button', ['url' => $url])
Proses Modal
@endcomponent
 
Pegawai Bertugas,<br>
{{ $data->Pegawai->name }}
@endcomponent