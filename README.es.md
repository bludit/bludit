# [Bludit](https://www.bludit.com/es/)

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

Bludit - CMS simple, rápido y flexible.

Con Bludit, puedes crear tu propio sitio web o blog en solo segundos. Es completamente gratuito, de código abierto y fácil de usar. Bludit almacena el contenido en formato JSON, eliminando la necesidad de instalar o configurar una base de datos. Todo lo que necesitas es un servidor web con soporte PHP.

Como CMS de archivos planos, Bludit ofrece una flexibilidad y velocidad inigualables. Además, con soporte para código Markdown y HTML, crear y gestionar contenido nunca ha sido tan fácil.

## Recursos

- [Plugins](https://plugins.bludit.com)
- [Temas](https://themes.bludit.com)
- [Documentación](https://docs.bludit.com)
- Noticias y anuncios en [Twitter](https://twitter.com/bludit), [Facebook](https://www.facebook.com/bluditcms) y [Reddit](https://www.reddit.com/r/bludit/)
- Charla y chat en [Discord](https://discord.gg/CFaXEdZWds)
- Ayuda y soporte en [Foro](https://forum.bludit.org)
- Reportes de errores en [Github Issues](https://github.com/bludit/bludit/issues)

## Requisitos

- Servidor web con soporte PHP.
- PHP versión 8.0 o superior.
- Módulo PHP [mbstring](http://php.net/manual/en/book.mbstring.php) para soporte completo de UTF-8.
- Módulo PHP [gd](http://php.net/manual/en/book.image.php) para procesamiento de imágenes.
- Módulo PHP [dom](http://php.net/manual/en/book.dom.php) para manipulación de DOM.
- Módulo PHP [json](http://php.net/manual/en/book.json.php) para manipulación de JSON.

## Instalación

1. Descarga la última versión desde la página oficial: [Bludit.com](https://www.bludit.com/es/)
2. Extrae el archivo zip en un directorio, como `bludit`.
3. Sube el directorio `bludit` a tu servidor web o hosting.
4. Visita tu dominio (por ejemplo, https://example.com/bludit/).
5. Sigue el instalador de Bludit para configurar tu sitio.

## Instalación rápida para pruebas

Puedes usar el servidor web incorporado de PHP (`php -S localhost:8000`) o Docker:

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

Luego abre http://localhost:8000

## Actualizar Bludit

Los siguientes pasos son válidos para actualizar a cualquier versión de Bludit de la misma versión mayor. La versión mayor es el primer dígito en el número de versión, por ejemplo, Bludit v3.x.

1. **Haz una copia de seguridad completa**, sin discusión aquí, copia de seguridad completa de los archivos y directorios. Eso significa copiar TODOS los archivos en una nueva carpeta.
2. **Recuerda qué versión de Bludit estás usando** para una posible reversión.
3. **Descarga la última versión** desde la [página oficial](https://www.bludit.com).
4. **Extrae el archivo zip**.
5. **Reemplaza los archivos existentes** con los nuevos archivos.
6. **Limpia la caché de tu navegador**, y por favor lee la nota a continuación.
7. **Inicia sesión en el área de administración** y verifica tu configuración.
8. Listo.

> **Nota:** Si tu sitio web está detrás de algún tipo de sistema de caché de servidor (por ejemplo, Cloudflare proporciona uno por defecto) necesitas purgar los archivos allí también. También es una buena idea limpiar la caché de tu navegador. Bludit intenta recargar los archivos con los nuevos, pero algunos componentes como TinyMCE pueden no recargarse y provocar problemas en la interfaz de usuario o generar errores de JavaScript.

## Apoya a Bludit

Bludit es de código abierto y gratuito para usar, pero si encuentras el proyecto útil y quieres apoyar su desarrollo, puedes contribuir en [Patreon](https://www.patreon.com/join/bludit). Como muestra de nuestra apreciación, los supporters recibirán Bludit PRO.

Si prefieres, también puedes hacer una donación única para comprarnos un café o una cerveza. Cada contribución nos ayuda a continuar mejorando Bludit y proporcionar la mejor experiencia posible a nuestros usuarios.

- [PayPal](https://www.paypal.me/bludit/10)
- BTC (Bitcoin): [bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph](https://www.blockchain.com/explorer/addresses/btc/bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph)
- ETH (Ethereum): [0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7](https://www.blockchain.com/explorer/addresses/eth/0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7)

## Licencia

Bludit es software de código abierto licenciado bajo la [licencia MIT](https://tldrlegal.com/license/mit-license).
