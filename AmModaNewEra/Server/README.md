# Backend PHP (AmModa)

Struktura wzorowana na projekcie UrmateAi: endpointy w `api/`, konfiguracja w `config/`.

## Zdjęcia

- **POST api/upload.php** – wgrywanie zdjęcia (pole formularza: `photo`). Zapis w katalogu `photos/` jako `main.<ext>` (nadpisuje poprzednie).
- **GET api/photo.php** – JSON: `{ ok, hasPhoto, url }`. Gdy `url` ustawione, obrazek: **GET api/photo.php?img=1**.

Dozwolone formaty: JPG, PNG, GIF, WEBP (max 5 MB).

## Uruchomienie (dev)

Z katalogu repozytorium (nad `Server`):

```bash
php -S localhost:8080 -t Server
```

Frontend ustaw np. `VITE_API_BASE=http://localhost:8080` w `.env`.
