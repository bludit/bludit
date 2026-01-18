# [Bludit](https://www.bludit.com/)

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

Bludit - il CMS semplice, veloce e flessibile.

Con Bludit, puoi creare il tuo sito web o blog in pochi secondi. È completamente gratuito, open-source e facile da usare. Bludit memorizza i contenuti in formato JSON, eliminando la necessità di installare o configurare un database. Tutto ciò di cui hai bisogno è un server web con supporto PHP.

Come CMS flat-file, Bludit offre una flessibilità e velocità senza pari. Inoltre, con il supporto per codice Markdown e HTML, creare e gestire contenuti non è mai stato così facile.

## Risorse

- [Plugin](https://plugins.bludit.com)
- [Temi](https://themes.bludit.com)
- [Documentazione](https://docs.bludit.com)
- Notizie e annunci su [Twitter](https://twitter.com/bludit), [Facebook](https://www.facebook.com/bluditcms) e [Reddit](https://www.reddit.com/r/bludit/)
- Conversazioni e chat su [Discord](https://discord.gg/CFaXEdZWds)
- Aiuto e supporto su [Forum](https://forum.bludit.org)
- Segnalazioni di bug su [Github Issues](https://github.com/bludit/bludit/issues)

## Requisiti

- Server web con supporto PHP.
- PHP versione 8.0 o superiore.
- Modulo PHP [mbstring](http://php.net/manual/en/book.mbstring.php) per supporto completo UTF-8.
- Modulo PHP [gd](http://php.net/manual/en/book.image.php) per elaborazione immagini.
- Modulo PHP [dom](http://php.net/manual/en/book.dom.php) per manipolazione DOM.
- Modulo PHP [json](http://php.net/manual/en/book.json.php) per manipolazione JSON.

## Installazione

1. Scarica l'ultima versione dalla pagina ufficiale: [Bludit.com](https://www.bludit.com)
2. Estrai il file zip in una directory, come `bludit`.
3. Carica la directory `bludit` sul tuo server web o hosting.
4. Visita il tuo dominio (ad esempio, https://example.com/bludit/).
5. Segui l'installatore Bludit per configurare il tuo sito.

## Installazione rapida per test

Puoi usare il server web integrato di PHP (`php -S localhost:8000`) oppure Docker:

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

Poi apri http://localhost:8000

## Supporta Bludit

Bludit è open-source e gratuito da usare, ma se trovi il progetto utile e vuoi supportarne lo sviluppo, puoi contribuire su [Patreon](https://www.patreon.com/bePatron?c=921115&rid=2458860). Come segno della nostra apprezzamento, i sostenitori riceveranno Bludit PRO.

Se preferisci, puoi anche fare una donazione una tantum per offrirci un caffè o una birra. Ogni contributo ci aiuta a continuare a migliorare Bludit e fornire la migliore esperienza possibile ai nostri utenti.

- [PayPal](https://www.paypal.me/bludit/10)
- BTC (Bitcoin): bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph
- ETH (Ethereum): 0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7

## Licenza

Bludit è software open-source con licenza [MIT](https://tldrlegal.com/license/mit-license).
