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

Bludit - le CMS simple, rapide et flexible.

Avec Bludit, vous pouvez créer votre propre site web ou blog en quelques secondes. Il est complètement gratuit, open-source et facile à utiliser. Bludit stocke le contenu au format JSON, éliminant le besoin d'installer ou de configurer une base de données. Tout ce dont vous avez besoin est un serveur web avec support PHP.

En tant que CMS flat-file, Bludit offre une flexibilité et une vitesse inégalées. De plus, avec le support pour le code Markdown et HTML, créer et gérer du contenu n'a jamais été aussi simple.

## Ressources

- [Plugins](https://plugins.bludit.com)
- [Thèmes](https://themes.bludit.com)
- [Documentation](https://docs.bludit.com)
- Actualités et annonces sur [Twitter](https://twitter.com/bludit), [Facebook](https://www.facebook.com/bluditcms) et [Reddit](https://www.reddit.com/r/bludit/)
- Discussion et chat sur [Discord](https://discord.gg/CFaXEdZWds)
- Aide et support sur [Forum](https://forum.bludit.org)
- Rapports de bugs sur [Github Issues](https://github.com/bludit/bludit/issues)

## Exigences

- Serveur web avec support PHP.
- PHP version 5.6 ou supérieure.
- Module PHP [mbstring](http://php.net/manual/en/book.mbstring.php) pour un support UTF-8 complet.
- Module PHP [gd](http://php.net/manual/en/book.image.php) pour le traitement d'images.
- Module PHP [dom](http://php.net/manual/en/book.dom.php) pour la manipulation DOM.
- Module PHP [json](http://php.net/manual/en/book.json.php) pour la manipulation JSON.

## Installation

1. Téléchargez la dernière version depuis la page officielle : [Bludit.com](https://www.bludit.com)
2. Extrayez le fichier zip dans un répertoire, tel que `bludit`.
3. Téléchargez le répertoire `bludit` sur votre serveur web ou hébergement.
4. Visitez votre domaine (par exemple, https://example.com/bludit/).
5. Suivez l'installateur Bludit pour configurer votre site.

## Installation rapide pour tests

Vous pouvez utiliser le serveur web intégré de PHP (`php -S localhost:8000`) ou Docker :

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

Ensuite, ouvrez http://localhost:8000

## Soutenir Bludit

Bludit est open-source et gratuit à utiliser, mais si vous trouvez le projet utile et souhaitez soutenir son développement, vous pouvez contribuer sur [Patreon](https://www.patreon.com/bePatron?c=921115&rid=2458860). En signe de notre appréciation, les supporters recevront Bludit PRO.

Si vous préférez, vous pouvez également faire un don unique pour nous offrir un café ou une bière. Chaque contribution nous aide à continuer d'améliorer Bludit et à fournir la meilleure expérience possible à nos utilisateurs.

- [PayPal](https://www.paypal.me/bludit/10)
- BTC (Bitcoin): bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph
- ETH (Ethereum): 0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7

## Licence

Bludit est un logiciel open-source sous [licence MIT](https://tldrlegal.com/license/mit-license).
