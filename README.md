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

Bludit the Simple, Fast, and Flexible CMS.

With Bludit, you can build your own website or blog in just seconds. It’s completely free, open-source, and easy to use. Bludit stores content in JSON format, eliminating the need for database installation or configuration. All you need is a web server with PHP support.

As a Flat-File CMS, Bludit offers unparalleled flexibility and speed. Plus, with support for both Markdown and HTML code, creating and managing content has never been easier.

## Resources

- [Plugins](https://plugins.bludit.com)
- [Themes](https://themes.bludit.com)
- [Documentation](https://docs.bludit.com)
- News and announcement on [Twitter](https://twitter.com/bludit), [Facebook](https://www.facebook.com/bluditcms), and [Reddit](https://www.reddit.com/r/bludit/)
- Talk & Chat on [Discord](https://discord.gg/CFaXEdZWds)
- Help and Support on [Forum](https://forum.bludit.org)
- Bugs reports on [Github Issues](https://github.com/bludit/bludit/issues)

## Requirements

- Webserver with PHP support.
- PHP v8.0 or higher version.
- PHP [mbstring](http://php.net/manual/en/book.mbstring.php) module for full UTF-8 support.
- PHP [gd](http://php.net/manual/en/book.image.php) module for image processing.
- PHP [dom](http://php.net/manual/en/book.dom.php) module for DOM manipulation.
- PHP [json](http://php.net/manual/en/book.json.php) module for JSON manipulation.

## Installation

1. Download the latest version from the official page: [Bludit.com](https://www.bludit.com)
2. Extract the zip file into a directory, such as `bludit`.
3. Upload the `bludit` directory to your web server or hosting.
4. Visit your domain (e.g., https://example.com/bludit/).
5. Follow the Bludit Installer to set up your website.

## Quick installation for testing

You can use PHP Built-in web server (`php -S localhost:8000`) or Docker:

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

Then open http://localhost:8000

## Upgrading Bludit

The following steps are valid for upgrading to any version of Bludit from the same major version. The major version is the first digit in the version number, for example, Bludit v3.x.

1. **Make a full backup**, no discussion here, entire backup of the files and directories. That means copy ALL files into a new folder.
2. **Remember which version of Bludit you are using** for a possible roll-back.
3. **Download the latest version** from the [official page](https://www.bludit.com).
4. **Extract the zip file**.
5. **Replace existing files** with the new files.
6. **Clean your browser cache**, and please read the note below.
7. **Log into the admin area** and check your settings.
8. Done.

> **Note:** If your website is behind some kind of server cache system (for example, Cloudflare provides one by default) you need to purge the files there, too. It's also a good idea to clear your browser cache. Bludit tries to reload the files with the new ones, but some components like TinyMCE may not reload, and provoke some issues in the UI or throw out JavaScript errors.

## Support Bludit

Bludit is open-source and free to use, but if you find the project useful and would like to support its development, you can contribute on [Patreon](https://www.patreon.com/bePatron?c=921115&rid=2458860). As a token of our appreciation, supporters will receive Bludit PRO.

If you prefer, you can also make a one-time donation to buy us a coffee or beer. Every contribution helps us continue to improve Bludit and provide the best possible experience for our users.

- [PayPal](https://www.paypal.me/bludit/10)
- BTC (Bitcoin): bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph
- ETH (Ethereum): 0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7

## License

Bludit is open source software licensed under the [MIT license](https://tldrlegal.com/license/mit-license).
