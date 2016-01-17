[Bludit](http://www.bludit.com/)
================================
Fast, simple, extensible and flat file CMS.

Bludit is a simple web application to make your own blog or site in seconds, it's completly free and open source. Bludit uses flat-files (text files in JSON format) to store the posts and pages, you don't need to install or configure a database.

- [Documentation](http://docs.bludit.com)
- [Help and Support](http://forum.bludit.com)
- [Plugins](https://github.com/dignajar/bludit-plugins)
- [Themes](https://github.com/dignajar/bludit-themes)
- [More plugins and themes](http://forum.bludit.com/viewforum.php?f=14)

Social networks
---------------

- [Twitter](https://twitter.com/bludit)
- [Facebook](https://www.facebook.com/bluditcms)
- [Google+](https://plus.google.com/+Bluditcms)
- [Freenode IRC](https://webchat.freenode.net) channel **#bludit**

[![Join the chat at https://gitter.im/dignajar/bludit](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/dignajar/bludit?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Requirements
------------

You only need a web server with PHP support.

- PHP 5.3 or higher.
- PHP [mbstring](http://php.net/manual/en/book.mbstring.php) module for full UTF-8 support.
- Webserver:
  * Apache with [mod_rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) module.
  * Lighttpd with [mod_rewrite](http://redmine.lighttpd.net/projects/1/wiki/docs_modrewrite) module.
  * Nginx with [ngx_http_rewrite_module](http://nginx.org/en/docs/http/ngx_http_rewrite_module.html) module.
  * PHP Built-in web server

Installation guide
------------------

1. Download the latest version from http://www.bludit.com/bludit_latest.zip
2. Extract the zip file into a directory like `bludit`.
3. Upload the directory `bludit` to your hosting server.
4. Done!

License
-------
Bludit is opensource software licensed under the [MIT license](https://tldrlegal.com/license/mit-license)
