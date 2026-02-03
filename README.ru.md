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

Bludit - простой, быстрый и гибкий CMS.

С Bludit вы можете создать собственный веб-сайт или блог всего за несколько секунд. Это полностью бесплатный, с открытым исходным кодом и простой в использовании CMS. Bludit хранит контент в формате JSON, что устраняет необходимость установки или настройки базы данных. Все, что вам нужно — это веб-сервер с поддержкой PHP.

Как Flat-File CMS, Bludit предлагает непревзойденную гибкость и скорость. Кроме того, с поддержкой как Markdown, так и HTML кода, создание и управление контентом стало еще проще.

## Ресурсы

- [Плагины](https://plugins.bludit.com)
- [Темы](https://themes.bludit.com)
- [Документация](https://docs.bludit.com)
- Новости и объявления в [Твиттере](https://twitter.com/bludit), [Фейсбуке](https://www.facebook.com/bluditcms) и [Реддите](https://www.reddit.com/r/bludit/)
- Общение и чат в [Discord](https://discord.gg/CFaXEdZWds)
- Помощь и поддержка на [форуме](https://forum.bludit.org)
- Сообщения об ошибках на [Github Issues](https://github.com/bludit/bludit/issues)

## Требования

- Веб-сервер с поддержкой PHP.
- PHP версии 8.0 или выше.
- PHP-модуль [mbstring](http://php.net/manual/en/book.mbstring.php) для полной поддержки UTF-8.
- PHP-модуль [gd](http://php.net/manual/en/book.image.php) для обработки изображений.
- PHP-модуль [dom](http://php.net/manual/en/book.dom.php) для работы с DOM.
- PHP-модуль [json](http://php.net/manual/en/book.json.php) для работы с JSON.

## Установка

1. Скачайте последнюю версию с официальной страницы: [Bludit.com](https://www.bludit.com)
2. Распакуйте zip-файл в каталог, например `bludit`.
3. Загрузите каталог `bludit` на ваш веб-сервер или хостинг.
4. Перейдите на ваш домен (например, https://example.com/bludit/).
5. Следуйте установщику Bludit для настройки вашего сайта.

## Быстрая установка для тестирования

Вы можете использовать встроенный веб-сервер PHP (`php -S localhost:8000`) или Docker:

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

Затем откройте http://localhost:8000
## Обновление Bludit

Следующие шаги действительны для обновления до любой версии Bludit той же основной версии. Основная версия - это первая цифра в номере версии, например, Bludit v3.x.

1. **Сделайте полную резервную копию**, никаких обсуждений, полная резервная копия файлов и каталогов. Это означает копирование ВСЕХ файлов в новую папку.
2. **Запомните, какую версию Bludit вы используете** для возможного отката.
3. **Загрузите последнюю версию** с [официальной страницы](https://www.bludit.com).
4. **Извлеките zip-файл**.
5. **Замените существующие файлы** новыми файлами.
6. **Очистите кеш браузера**, и пожалуйста, прочитайте примечание ниже.
7. **Войдите в административную область** и проверьте свои настройки.
8. Готово.

> **Примечание:** Если ваш веб-сайт находится за какой-либо системой кеширования сервера (например, Cloudflare предоставляет ее по умолчанию), вам также необходимо очистить файлы там. Также рекомендуется очистить кеш браузера. Bludit пытается перезагрузить файлы с новыми, но некоторые компоненты, такие как TinyMCE, могут не перезагрузиться и вызвать проблемы в пользовательском интерфейсе или вызвать ошибки JavaScript.
## Поддержать Bludit

Bludit - это проект с открытым исходным кодом и бесплатный для использования, но если вы считаете его полезным и хотите поддержать развитие, вы можете сделать пожертвование на [Patreon](https://www.patreon.com/join/bludit). В знак нашей благодарности, поддерживающие получат Bludit PRO.

Если хотите, вы также можете сделать одноразовое пожертвование, чтобы угостить нас кофе или пивом. Каждое пожертвование помогает нам продолжать улучшать Bludit и предоставлять лучший опыт нашим пользователям.

- [PayPal](https://www.paypal.me/bludit/10)
- BTC (Bitcoin): [bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph](https://www.blockchain.com/explorer/addresses/btc/bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph)
- ETH (Ethereum): [0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7](https://www.blockchain.com/explorer/addresses/eth/0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7)

## Лицензия

Bludit - это программное обеспечение с открытым исходным кодом, лицензированное по [MIT лицензии](https://tldrlegal.com/license/mit-license).
