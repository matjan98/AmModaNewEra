# Backend PHP (AmModa)

Struktura wzorowana na projekcie UrmateAi: endpointy w `api/`, konfiguracja w `config/`.

## Zdjęcia

- **POST api/upload.php** – wgrywanie zdjęć (pole formularza: `photo` lub `photos[]`). Zapis w katalogu `photos/` jako `photo_{uniqid}.{ext}`.
- **GET api/photo.php?list=1** – JSON: `{ ok, photos: [{ id, v, url }] }` — lista zdjęć galerii.
- **GET api/photo.php?img=1&id=photo_xxx.webp** – serwowanie pojedynczego pliku z galerii.
- **DELETE api/delete.php** – usuwanie zdjęcia po `id` (wymaga autoryzacji admina).

Dozwolone formaty: JPG, PNG, GIF, WEBP (max 5 MB na plik).

## Uruchomienie (dev)

Z katalogu repozytorium (nad `Server`):

```bash
php -S localhost:8080 -t Server
```

Frontend ustaw np. `VITE_API_BASE=http://localhost:8080` w `.env`.
