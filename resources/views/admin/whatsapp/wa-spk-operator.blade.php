Halo {{ $spk->operator?->name ?? 'Operator' }},
layanan transportasi untuk order:

<b>Order Number:</b> <i>{{ $spk->order_number ?? '-' }}</i>
<b>Date:</b> <i>{{ $spk->spk_date ?? '-' }}</i>
<b>SPK Number:</b> <i>{{ $spk->spk_number ?? '-' }}</i>
<b>Number of Guests:</b> <i>{{ $spk->number_of_guests ?? '-' }}</i>
<b>Vehicle:</b> <i>{{ $spk->transport?->brand ?? '' }} - {{ $spk->transport?->name ?? '' }}</i>
<b>Driver:</b> <i>{{ $spk->driver?->name ?? '-' }}</i>

untuk melihat SPK gunakan link berikut:
<a href="{{ url('spk/report/'.$spk->id) }}">{{ url('spk/report/'.$spk->id) }}</a>

Terima kasih,
online.balikamitour.com
