# FleetLog Flutter Implementation Plan

Dacă mergem pe **Flutter**, abordarea se schimbă puțin. Aplicația mobilă nu va mai "citi" paginile web, ci va comunica cu serverul tău prin date (JSON).

## 1. Arhitectura Sistemului
*   **Frontend Mobile**: Flutter (Dart) - O singură bază de cod pentru iOS și Android.
*   **Backend API**: Vom crea o secțiune nouă în PHP-ul tău (`fleetlog/app/Controllers/ApiController.php`) care să trimită date curate aplicației.

## 2. Pasi de implementare
1.  **Pregătirea Backend-ului**: 
    *   Endpoint pentru Login (primește email/parolă -> returnează un Token).
    *   Endpoint pentru Dashboard Sofer (listă mașini active, km actuali).
    *   Endpoint pentru Start/End Trip.
2.  **Construcția Flutter**:
    *   Ecran de Login (Modern, cu logo Dash).
    *   Dashboard cu butoane mari, ușor de apăsat în mers.
    *   Modul GPS care rulează în fundal (Background Service).

## 3. Avantajul Premium
Cu Flutter, aplicația se va simți ca o aplicație "de fabrică" (ca cea de la Tesla sau Uber). Este alegerea corectă dacă vrei să impresionezi utilizatorii finali și să ai o bază solidă pentru viitor.

---

> [!IMPORTANT]
> Pentru ca eu să generez fișierele Flutter direct aici pe calculatorul tău, sistemul tău trebuie să aibă **Flutter SDK** instalat. Dacă nu îl ai, eu voi scrie codul, dar nu îl voi putea "compila" sau rula comanda `flutter create`.
