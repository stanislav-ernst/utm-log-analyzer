# UTM Log-Analyzer

## Projektüberblick

Dieses Projekt analysiert HTTP-Zugriffslogs von UTM-Appliances, die regelmäßig den Update-Server kontaktieren. Ziel ist es, aus einem Tageslog aussagekräftige Erkenntnisse über die Nutzung von
Lizenzen, die Zuordnung zu physischen Geräten sowie die eingesetzte Hardware zu gewinnen.

Die Logdatei enthält neben den eigentlichen Zugriffsinformationen strukturierte Metadaten wie Lizenz-Seriennummern, Firmware-Versionen und einen komprimierten Block mit System- und
Hardwareinformationen. Das Projekt verarbeitet diese Logdatei zeilenweise, dekodiert die enthaltenen System- und Hardwaredaten und wertet sie anschließend aus.

Die Umsetzung basiert auf dem [Laravel-Framework](https://laravel.com/docs) und legt besonderen Wert auf eine klare Trennung von Verantwortlichkeiten zwischen Log-Parsing, Datenaufbereitung und
Analyse-Logik. Die Verarbeitung
erfolgt speicherschonend, sodass auch sehr große Logdateien effizient analysiert werden können.

Das Projekt wurde im Rahmen einer technischen Testaufgabe erstellt und konzentriert sich bewusst auf Code-Qualität, Verständlichkeit und Erweiterbarkeit, nicht auf Benutzeroberflächen oder Persistenz.

> [!WARNING]
> Dieses Projekt ist nicht für Produktionszwecke vorgesehen!

### Funktionen und Merkmale

#### Zugriffshäufigkeit von Lizenzen

- Ermittlung der zehn Lizenz-Seriennummern mit den meisten Zugriffen auf den Update-Server
- Ausgabe der jeweiligen Anzahl von Zugriffsversuchen pro Lizenz

#### Erkennung von Lizenz-Mehrfachnutzung

- Identifikation von Lizenz-Seriennummern, die auf mehr als einem physischen Gerät verwendet werden
- Bestimmung der zehn Lizenz-Seriennummern mit den meisten Gerätezuordnungen
- Ermittlung der Anzahl unterschiedlicher Geräte pro betroffener Lizenz

#### Analyse der eingesetzten Hardware

- Dekodierung und Auswertung der in den specs-Metadaten enthaltenen System- und Hardwareinformationen
- Klassifizierung der eingesetzten Hardware anhand reproduzierbarer Kriterien
- Ermittlung der Anzahl aktiver Lizenzen pro Hardware-Klasse

#### Technische Merkmale

- Zeilenweise, streamingbasierte Verarbeitung großer Logdateien
- Nache zu konstantem Speicherverbrauch unabhängig von der Loggröße
- Klare Trennung zwischen Parsing, Analyse und Datenrepräsentation
- Erweiterbare Architektur für zusätzliche Auswertungen

## Technologie-Stack

- Laravel 12
- PHP
- NGINX/Apache
- (Laravel Homestead - for local development)

## Technologie-Anforderungen

- PHP: ^8.3
    - gzdecode zlib-Erweiterung
- Composer: ^2.9.2
- NPM: ^11.6.2

## Einrichtung

Um die Erstkonfiguration durchzuführen, gehen Sie wie folgt vor:

``` bash
# Klonen Sie das Repository mit SSH
git clone git@github.com:stanislav-ernst/utm-log-analyzer.git

# oder klonen Sie das Repository über HTTPS
git clone https://github.com/stanislav-ernst/utm-log-analyzer.git

cd utm-log-analyzer

# Composer-Abhängigkeit installieren (für lokale Entwicklung ohne --no-dev)
composer install --no-dev

# Erstellen Sie eine Umgebungsdatei (kopieren Sie die Datei .env.example nach .env)
cp .env.example .env

# Legen Sie Ihre APP_ENV, APP_DEBUG und APP_URL in Ihrer .env-Datei fest

# Setzen Sie den Anwendungsschlüssel
php artisan key:generate

# Richten Sie die Datenbank ein
php artisan migrate

# Kompilieren Sie die Assets
npm install && npm run build
```

## Verwendung

Die Analyse wird über einen Artisan Command gestartet. Als Parameter wird der Pfad zur zu analysierenden Logdatei übergeben.

```bash
php artisan utm:analyze storage/app/private/utm-logs/[filename].log
```

Der Command liest die angegebene Logdatei zeilenweise ein, dekodiert die enthaltenen Metadaten und führt alle Auswertungen in einem Durchlauf durch. Die Ergebnisse der einzelnen Analysen werden
anschließend direkt in der Konsole ausgegeben.

Die Verarbeitung erfolgt speicherschonend und ist auch für sehr große Logdateien geeignet.

## Zusammenarbeit

### Versionskontrolle

[GitHub Repository](https://github.com/stanislav-ernst/utm-log-analyzer) bereitgestellt von Stanislav Ernst.

### Codierungsstil

Laravel folgt dem PSR-2-Codierungsstandard und dem PSR-4-Autoloading-Standard.

In diesem Projekt wird [Laravel Pint](https://laravel.com/docs/12.x/pint) verwendet, um sicherzustellen, dass der Code-Stil sauber und konsistent bleibt.
Es wird empfohlen, nach jeder Fertigstellung einer Version der Anwendung Laravel Pint auszuführen.

Die wichtigsten Artisan-Befehle:

``` bash
# Um Probleme mit dem Code-Stil zu beheben:
./vendor/bin/pint

# Führen Sie Pint für bestimmte Dateien oder Verzeichnisse aus:
./vendor/bin/pint app/Console/Commands
./vendor/bin/pint app/Console/Commands/AnalyzeLogsCommand.php
```

### Architektur

Die Architektur trennt klar zwischen Log-Parsing, strukturierter Datenrepräsentation (DTOs) und Analyse-Logik. Ein zentraler Artisan Command übernimmt ausschließlich die Orchestrierung, während
spezialisierte Analyzer-Services die fachlichen Auswertungen inkrementell durchführen.

Alle Auswertungen erfolgen aggregiert und zustandsarm, es wird bewusst auf eine Persistenzschicht verzichtet. Die Struktur ermöglicht eine einfache Erweiterung um zusätzliche Analysen, ohne bestehende
Komponenten zu verändern, und folgt den Best Practices des Laravel-Frameworks.

## Ressourcen

- https://laravel.com/docs/12.x
- https://www.php.net
- https://tailwindcss.com
- https://www.material-tailwind.com
