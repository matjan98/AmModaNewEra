# AmModaDeploy

Konsolowa aplikacja do pełnego deploymentu strony ammoda.pl (frontend + backend) na hostingu FTP.

## Konfiguracja sekretów użytkownika

Aplikacja korzysta z mechanizmu `dotnet user-secrets`. Po wejściu do katalogu `AmModaDeploy` wykonaj następujące polecenia:

### Wymagane ustawienia

```bash
cd AmModaNewEra/AmModaDeploy

# Konfiguracja FTP
dotnet user-secrets set "Deployment:FtpHost" "ftp.example.com"
dotnet user-secrets set "Deployment:FtpPort" "21"
dotnet user-secrets set "Deployment:FtpUsername" "deploy_user"
dotnet user-secrets set "Deployment:FtpPassword" "SkomplikowaneHaslo123!"
dotnet user-secrets set "Deployment:RemoteBasePath" "public_html/ammoda"

# Konfiguracja SMTP (opcjonalne – tylko gdy backend ma config/smtp.php)
dotnet user-secrets set "Deployment:SmtpPassword" "twoje-haslo-smtp"
```

* `Deployment:FtpHost` - nazwa hosta serwera FTP (np. `ftp.twojadomena.pl`)
* `Deployment:FtpPort` - port FTP (najczęściej `21`, dla SFTP `22`)
* `Deployment:FtpUsername` i `Deployment:FtpPassword` - dane logowania FTP
* `Deployment:RemoteBasePath` - katalog docelowy dla frontendu (np. `public_html/subfolder`)
* `Deployment:SmtpPassword` - opcjonalne; hasło SMTP wstrzykiwane do `config/smtp.php` przy deployu (gdy plik istnieje)

### Opcjonalne ustawienia ścieżek

```bash
# Frontend (opcjonalne - wykrywa automatycznie)
dotnet user-secrets set "Deployment:FrontendPath" "/pelna/sciezka/do/WebSiteFrontend"
dotnet user-secrets set "Deployment:BuildOutputSubPath" "dist/spa"

# Backend PHP (opcjonalne - wykrywa automatycznie katalog Server)
dotnet user-secrets set "Deployment:BackendPath" "/pelna/sciezka/do/Server"
dotnet user-secrets set "Deployment:RemoteBackendPath" "server"
```

* `FrontendPath` - ścieżka do katalogu `WebSiteFrontend` (wykrywa automatycznie)
* `BuildOutputSubPath` - podfolder z buildem (domyślnie "dist/spa")
* `BackendPath` - ścieżka do katalogu `Server/` (wykrywa automatycznie katalog **Server**)
* `RemoteBackendPath` - nazwa katalogu na serwerze dla backendu (domyślnie "server")

## Użycie

Uruchom aplikację poleceniem:

```bash
dotnet run --project AmModaNewEra/AmModaDeploy  # Windows
dotnet run --project AmModaNewEra/AmModaDeployMacVersion  # macOS
```

Po starcie zobaczysz menu:

1. Deploy – wykona:
   - Build frontendu (`npx quasar build`)
   - Upload frontendu do głównego katalogu
   - Upload backendu do podkatalogu `server/`
2. Show git statistics – pokaże statystyki repozytorium
3. Test connection – sprawdzi połączenie FTP
4. Exit – zakończy program

Podczas działania możesz użyć `Ctrl+C`, aby przerwać aktualny proces.

## Struktura katalogów na serwerze

```
public_html/ammoda/  # RemoteBasePath
├── index.html          # Frontend (dist/spa)
├── ...                 # Pozostałe pliki frontendu
└── server/            # RemoteBackendPath
    ├── api/           # Endpointy API (upload.php, photo.php itd.)
    ├── config/        # Konfiguracja (opcjonalnie smtp.php)
    ├── photos/        # Zdjęcia wgrywane przez stronę (nie są usuwane przy deployu)
    └── ...           # Pozostałe pliki backendu
```

Katalog `server/photos/` na serwerze jest zachowywany przy każdym deployu (zdjęcia użytkowników nie są nadpisywane ani usuwane).

## Favicon

Aby deploy podmieniał favicon na logo AmModa, umieść plik `ammoda-logo.ico` w katalogu `public/` frontendu (WebSiteFrontend). W przeciwnym razie zostanie użyty domyślny favicon z buildu.

## Wersje
- `AmModaDeploy` - wersja dla Windows
- `AmModaDeployMacVersion` - wersja dla macOS (używa natywnych komend)
