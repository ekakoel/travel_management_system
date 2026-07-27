# Tour Package Map Display

Status: active
Updated: 2026-07-27

Dokumen ini menjelaskan standar data lokasi dan map pada tour package.

## Data Model

Domain map tour memakai model/relasi terkait:

- `Tours`
- `TourPackageLocation`
- `TourLocationReference`
- service `App\Services\Tours\TourLocationService`

Lokasi harus disimpan sebagai data terstruktur, bukan hanya teks bebas di Blade.

## Admin Usage

- Create/edit tour memakai Form Request dan `TourLocationService`.
- Location repeater behavior berada di JS domain Tours, bukan inline script.
- Validasi koordinat, urutan itinerary, nama lokasi, dan referensi lokasi dilakukan sebelum sync.
- Cover/marker/gambar mengikuti upload validation domain tour.

## Frontend Behavior

- Detail tour menampilkan itinerary/map dari data yang sudah dibentuk controller/service.
- Blade tidak melakukan geocoding, sorting kompleks, atau query lokasi.
- Jika peta tidak memiliki koordinat valid, tampilkan fallback/empty state yang diterjemahkan.

## Files

- `app/Http/Controllers/Backend/Operations/Tours/TourAdminController.php`
- `app/Services/Tours/TourLocationService.php`
- `app/Http/Controllers/Concerns/BuildsTourLocationItinerary.php`
- `resources/views/backend/operations/tours/forms`
- `resources/views/frontend/landing-page/tours/detail.blade.php`
- `resources/lang/{en,zh,zh-CN}/tour-map.php`
