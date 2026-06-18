# Backend PHP (AmModa)

Struktura wzorowana na projekcie UrmateAi: endpointy w `api/`, konfiguracja w `config/`.

## Zdjęcia

- **POST api/upload.php** – wgrywanie zdjęć (pole formularza: `photo` lub `photos[]`). Każde zdjęcie jest konwertowane do WebP i zapisywane w katalogu `photos/` jako `photo_{uniqid}.webp`.
- **GET api/photo.php?list=1** – JSON: `{ ok, photos: [{ id, v, url }] }` — lista zdjęć galerii.
- **GET api/photo.php?img=1&id=photo_xxx.webp** – serwowanie pojedynczego pliku z galerii.
- **POST api/delete.php** – usuwanie zdjęć po `id` lub wielu `ids[]` (wymaga autoryzacji admina).

### Optymalizacja przy wgrywaniu

`api/upload.php` przetwarza każde zdjęcie przez `lib/ImageProcessor.php`:

- konwersja do WebP (jakość 85),
- zmniejszanie zbyt dużych zdjęć: dłuższy bok ≤ 2500 px oraz krótszy bok ≤ 1500 px (z zachowaniem proporcji, tylko zmniejszanie),
- automatyczny obrót na podstawie EXIF i usuwanie metadanych.

Silnik: preferowany Imagick, w razie braku — GD (wymagana obsługa WebP). Wynik jest zawsze zapisywany jako WebP.

Dozwolone formaty wejściowe: JPG, PNG, GIF, WEBP, BMP (max 70 MB na plik).

## Uruchomienie (dev)

Z katalogu repozytorium (nad `Server`):

```bash
php -S localhost:8080 -t Server
```

Frontend ustaw np. `VITE_API_BASE=http://localhost:8080` w `.env`.
