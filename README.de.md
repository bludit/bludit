# [Bludit](https://www.bludit.com/de/)

[![English](https://img.shields.io/badge/Language-English-blue.svg)](README.md)
[![Español](https://img.shields.io/badge/Language-Español-green.svg)](README.es.md)
[![العربية](https://img.shields.io/badge/Language-العربية-yellow.svg)](README.ar.md)
[![中文](https://img.shields.io/badge/Language-中文-red.svg)](README.zh.md)
[![Français](https://img.shields.io/badge/Language-Français-purple.svg)](README.fr.md)
[![Deutsch](https://img.shields.io/badge/Language-Deutsch-orange.svg)](README.de.md)
[![हिंदी](https://img.shields.io/badge/Language-हिंदी-lightblue.svg)](README.hi.md)
[![日本語](https://img.shields.io/badge/Language-日本語-pink.svg)](README.ja.md)
[![Português](https://img.shields.io/badge/Language-Português-darkgreen.svg)](README.pt.md)
[![Русский](https://img.shields.io/badge/Language-Русский-cyan.svg)](README.ru.md)

Bludit - das einfache, schnelle und flexible CMS.

Mit Bludit können Sie Ihre eigene Website oder Ihren Blog in Sekunden erstellen. Es ist völlig kostenlos, open-source und einfach zu bedienen. Bludit speichert Inhalte im JSON-Format, sodass keine Datenbankinstallation oder -konfiguration erforderlich ist. Alles, was Sie brauchen, ist ein Webserver mit PHP-Unterstützung.

Als Flat-File-CMS bietet Bludit unvergleichliche Flexibilität und Geschwindigkeit. Zudem macht die Unterstützung für Markdown- und HTML-Code das Erstellen und Verwalten von Inhalten einfacher denn je.

## Ressourcen

- [Plugins](https://plugins.bludit.com)
- [Themes](https://themes.bludit.com)
- [Dokumentation](https://docs.bludit.com)
- Neuigkeiten und Ankündigungen auf [Twitter](https://twitter.com/bludit), [Facebook](https://www.facebook.com/bluditcms) und [Reddit](https://www.reddit.com/r/bludit/)
- Gespräche und Chat auf [Discord](https://discord.gg/CFaXEdZWds)
- Hilfe und Support auf [Forum](https://forum.bludit.org)
- Fehlermeldungen auf [Github Issues](https://github.com/bludit/bludit/issues)

## Anforderungen

- Webserver mit PHP-Unterstützung.
- PHP Version 8.0 oder höher.
- PHP-Modul [mbstring](http://php.net/manual/en/book.mbstring.php) für vollständige UTF-8-Unterstützung.
- PHP-Modul [gd](http://php.net/manual/en/book.image.php) für Bildverarbeitung.
- PHP-Modul [dom](http://php.net/manual/en/book.dom.php) für DOM-Manipulation.
- PHP-Modul [json](http://php.net/manual/en/book.json.php) für JSON-Manipulation.

## Installation

1. Laden Sie die neueste Version von der offiziellen Seite herunter: [Bludit.com](https://www.bludit.com/de/)
2. Entpacken Sie die Zip-Datei in ein Verzeichnis, wie `bludit`.
3. Laden Sie das Verzeichnis `bludit` auf Ihren Webserver oder Ihr Hosting hoch.
4. Besuchen Sie Ihre Domain (z. B. https://example.com/bludit/).
5. Folgen Sie dem Bludit-Installer, um Ihre Website einzurichten.

## Schnelle Installation zum Testen

Sie können den integrierten PHP-Webserver (`php -S localhost:8000`) oder Docker verwenden:

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

Dann öffnen Sie http://localhost:8000

## Bludit aktualisieren

Die folgenden Schritte gelten für die Aktualisierung auf jede Version von Bludit derselben Hauptversion. Die Hauptversion ist die erste Ziffer in der Versionsnummer, zum Beispiel Bludit v3.x.

1. **Erstellen Sie ein vollständiges Backup**, keine Diskussion hier, vollständiges Backup der Dateien und Verzeichnisse. Das bedeutet, ALLE Dateien in einen neuen Ordner zu kopieren.
2. **Merken Sie sich, welche Version von Bludit Sie verwenden** für ein mögliches Rollback.
3. **Laden Sie die neueste Version** von der [offiziellen Seite](https://www.bludit.com) herunter.
4. **Entpacken Sie die Zip-Datei**.
5. **Ersetzen Sie vorhandene Dateien** durch die neuen Dateien.
6. **Löschen Sie Ihren Browser-Cache**, und lesen Sie bitte den Hinweis unten.
7. **Melden Sie sich im Administrationsbereich an** und überprüfen Sie Ihre Einstellungen.
8. Fertig.

> **Hinweis:** Wenn Ihre Website hinter einem Server-Cache-System steht (z.B. bietet Cloudflare standardmäßig eines an), müssen Sie die Dateien dort ebenfalls löschen. Es ist auch eine gute Idee, Ihren Browser-Cache zu löschen. Bludit versucht, die Dateien mit den neuen zu laden, aber einige Komponenten wie TinyMCE werden möglicherweise nicht neu geladen und können Probleme in der Benutzeroberfläche verursachen oder JavaScript-Fehler werfen.

## Bludit unterstützen

Bludit ist open-source und kostenlos zu verwenden, aber wenn Sie das Projekt nützlich finden und seine Entwicklung unterstützen möchten, können Sie auf [Patreon](https://www.patreon.com/join/bludit) beitragen. Als Zeichen unserer Wertschätzung erhalten Unterstützer Bludit PRO.

Falls gewünscht, können Sie auch eine einmalige Spende machen, um uns einen Kaffee oder ein Bier zu spenden. Jeder Beitrag hilft uns, Bludit weiter zu verbessern und die beste Erfahrung für unsere Nutzer zu bieten.

- [PayPal](https://www.paypal.me/bludit/10)
- BTC (Bitcoin): [bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph](https://www.blockchain.com/explorer/addresses/btc/bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph)
- ETH (Ethereum): [0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7](https://www.blockchain.com/explorer/addresses/eth/0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7)

## Lizenz

Bludit ist open-source-Software unter der [MIT-Lizenz](https://tldrlegal.com/license/mit-license).
