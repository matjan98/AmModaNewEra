# Potencjalne ulepszenia SEO - A&M Moda

Lista rzeczy, które można jeszcze zrobić pod SEO, ale świadomie odłożyliśmy je na później (poza zakresem aktualnego wdrożenia). Każdy punkt ma krótkie uzasadnienie i wskazówkę, jak to zrobić.

> Stan na teraz (już wdrożone): prerender stron `/` i `/galeria` (Puppeteer), per-trasowe meta + JSON-LD `ClothingStore` z dynamiczną oceną Google przez `@unhead/vue`, `noindex` na `/admin` i 404, `robots.txt` z `Disallow: /admin`, sitemap z `/galeria`, mocny `.htaccess` (HTTPS, bez `www`, kompresja, cache, nagłówki bezpieczeństwa).

---

## 1. Sekcja FAQ + dane strukturalne `FAQPage`

**Dlaczego warto:** FAQ to jeden z najmocniejszych elementów SEO w projekcie wzorcowym (Urmate). Pytania i odpowiedzi mogą pojawić się bezpośrednio w wynikach Google (rich results), a do tego realnie pomagają klientkom.

**Jak zrobić (wzorem Urmate):**
- Dodać dane FAQ w jednym pliku, np. `src/data/faqData.js` (jedno źródło prawdy dla UI i JSON-LD).
- Wyświetlić je w komponencie (np. sekcja "Najczęstsze pytania" na stronie głównej).
- Wygenerować JSON-LD typu `FAQPage` - albo przez `@unhead/vue` (jak `useBusinessJsonLd`), albo pluginem Vite wstrzykującym JSON-LD do `index.html` przy buildzie (jak `faqJsonLdPlugin` w Urmate).

**Propozycja pytań i odpowiedzi (do akceptacji / uzupełnienia):**

1. **Gdzie znajduje się sklep A&M Moda Damska?**
   W Kozach przy ul. Bielskiej 166 (43-340). Dojazd ułatwia nawigacja Google Maps dostępna na stronie.

2. **W jakich godzinach jest otwarte?**
   Poniedziałek-piątek 9:00-18:00, sobota 9:00-14:00, niedziela nieczynne. Aktualne godziny (w tym ewentualne zmiany świąteczne) są zawsze widoczne na stronie głównej.

3. **Czy mogę przymierzyć ubrania w sklepie?**
   Tak, zapraszamy do przymierzalni w naszym salonie stacjonarnym w Kozach.

4. **Czy prowadzicie sprzedaż online lub wysyłkę?** *(do potwierdzenia)*
   Zapraszamy przede wszystkim do sklepu stacjonarnego. W sprawie rezerwacji lub wysyłki prosimy o kontakt telefoniczny: 503 115 446.

5. **Jakie rozmiary są dostępne?** *(do potwierdzenia)*
   Oferujemy szeroki zakres rozmiarów odzieży damskiej. Aktualną dostępność najlepiej sprawdzić w salonie lub telefonicznie.

6. **Jak można zapłacić?** *(do potwierdzenia)*
   Przyjmujemy płatność gotówką i kartą.

7. **Czy przy sklepie jest parking?** *(do potwierdzenia)*
   Tak, w pobliżu salonu można wygodnie zaparkować.

8. **Jak się z wami skontaktować?**
   Telefonicznie pod numerem 503 115 446 lub przez Facebooka (A&M Moda Damska).

---

## 2. Analityka (GA4) - świadomie pominięta

Stary projekt (`AmmodaOld`) używał Universal Analytics (`UA-114132769-1`), który Google **wyłączyło w lipcu 2023** - nie zbiera już danych i nie da się go przenieść. Trzeba założyć nową usługę **GA4** (identyfikator w formacie `G-XXXXXXXXXX`).

**Jak dodać (wzorem Urmate, z wyłączeniem przy prerenderze):**
- W `index.html` (lub w boot file) wstawić snippet `gtag.js`, ale tylko gdy `!window.__PRERENDER__`, żeby nie zliczać "wizyt" Puppeteera podczas buildu:

```html
<script>
  if (!window.__PRERENDER__) {
    var s = document.createElement('script');
    s.async = true;
    s.src = 'https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX';
    document.head.appendChild(s);
    window.dataLayer = window.dataLayer || [];
    function gtag(){ dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'G-XXXXXXXXXX');
  }
</script>
```

- Identyfikator najlepiej trzymać w zmiennej środowiskowej (np. `build.env` w `quasar.config.js`), nie na sztywno.
- Opcjonalnie Microsoft Clarity (mapy ciepła / nagrania sesji) - analogicznie, z guardem `__PRERENDER__`.

## 3. Baner zgody RODO (cookies)

Jeśli wejdzie analityka (GA4 / Clarity / Meta Pixel), z punktu widzenia RODO należy dodać baner zgody na cookies (zapis wyboru np. w `localStorage`) i ładować trackery dopiero po zgodzie. Urmate ma gotowy wzorzec (`CookieBanner.vue` + `consent.js`).

## 4. Rozszerzone dane strukturalne (schema.org)

- `WebSite` + `SearchAction` (pole wyszukiwania w wynikach Google) - jeśli powstanie wyszukiwarka.
- `BreadcrumbList` - jeśli przybędzie podstron.
- `ImageGallery` / `ImageObject` dla `/galeria` + **image sitemap** (osobny wpis na każde zdjęcie) - realnie pomaga w Grafice Google.

## 5. Galeria w snapshocie prerenderu (dane z API podczas buildu)

Obecnie prerender odpala się lokalnie podczas buildu, gdzie API PHP (`/server/api/...`) zwykle jest nieosiągalne. Skutek: w **snapshocie** dla botów bez JS galeria bywa pusta, a ocena to wartość fallback. Zwykli użytkownicy i Googlebot (renderuje JS) i tak widzą świeże dane.

**Aby do samego snapshotu trafiały realne dane:**
- Ustawić `VITE_API_BASE` na produkcyjne API podczas buildu (np. `https://ammoda.pl/server`).
- Zapewnić nagłówki CORS na endpointach `reviews.php` / `photo.php`, bo Puppeteer renderuje z `localhost` (żądanie cross-origin).

## 6. Obrazy i Core Web Vitals

- Dodać `width`/`height` (lub `aspect-ratio`) do zdjęć hero i miniatur galerii - ogranicza CLS.
- Rozważyć `srcset`/`<picture>` (różne rozmiary) dla hero i miniatur.
- `preconnect` do origin API, jeśli zdjęcia są serwowane z innego hosta niż frontend.

## 7. Wielojęzyczność (i18n) + `hreflang`

Strona jest jednojęzyczna (PL) i to jest OK. Gdyby kiedyś doszła wersja np. EN/CZ/SK:
- dodać `vue-i18n` + lokalizowane URL-e (`/en/...`),
- dodać `<link rel="alternate" hreflang="...">`,
- uwzględnić nowe trasy w prerenderze i sitemapie.

## 8. Sitemap zależny od środowiska

`scripts/gen-seo-files.mjs` generuje `sitemap.xml` i `robots.txt` z `SITE_ORIGIN`. Jeśli pojawi się staging na innej domenie, wystarczy ustawić `SITE_ORIGIN` przy buildzie - URL-e same się dostosują. Datę `lastmod` (`LAST_MODIFIED` w skrypcie) podbijamy ręcznie przy większych zmianach treści.
