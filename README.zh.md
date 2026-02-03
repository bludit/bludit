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

Bludit - 简单、快速、灵活的CMS。

使用Bludit，您可以在几秒钟内构建自己的网站或博客。它完全免费、开源且易于使用。Bludit将内容存储在JSON格式中，消除了安装或配置数据库的需要。您只需要一个支持PHP的Web服务器。

作为Flat-File CMS，Bludit提供了无与伦比的灵活性和速度。而且，支持Markdown和HTML代码，创建和管理内容从未如此简单。

## 资源

- [插件](https://plugins.bludit.com)
- [主题](https://themes.bludit.com)
- [文档](https://docs.bludit.com)
- 新闻和公告在[Twitter](https://twitter.com/bludit)、[Facebook](https://www.facebook.com/bluditcms)和[Reddit](https://www.reddit.com/r/bludit/)上
- 对话和聊天在[Discord](https://discord.gg/CFaXEdZWds)上
- 帮助和支持在[论坛](https://forum.bludit.org)上
- 错误报告在[Github Issues](https://github.com/bludit/bludit/issues)上

## 要求

- 支持PHP的Web服务器。
- PHP版本8.0或更高。
- PHP [mbstring](http://php.net/manual/en/book.mbstring.php)模块用于完整的UTF-8支持。
- PHP [gd](http://php.net/manual/en/book.image.php)模块用于图像处理。
- PHP [dom](http://php.net/manual/en/book.dom.php)模块用于DOM操作。
- PHP [json](http://php.net/manual/en/book.json.php)模块用于JSON操作。

## 安装

1. 从官方页面下载最新版本：[Bludit.com](https://www.bludit.com)
2. 将zip文件解压到目录中，例如`bludit`。
3. 将`bludit`目录上传到您的Web服务器或托管。
4. 访问您的域名（例如，https://example.com/bludit/）。
5. 按照Bludit安装程序设置您的网站。

## 测试的快速安装

您可以使用PHP内置Web服务器（`php -S localhost:8000`）或 Docker：

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

然后打开 http://localhost:8000
## 升级 Bludit

以下步骤适用于从相同主版本升级到 Bludit 的任何版本。主版本是版本号中的第一位数字,例如 Bludit v3.x。

1. **进行完整备份**,这里不讨论,完整备份文件和目录。这意味着将所有文件复制到一个新文件夹。
2. **记住您正在使用的 Bludit 版本**,以便可能的回滚。
3. **从[官方页面](https://www.bludit.com)下载最新版本**。
4. **解压 zip 文件**。
5. **用新文件替换现有文件**。
6. **清除浏览器缓存**,请阅读下面的注释。
7. **登录管理区域**并检查您的设置。
8. 完成。

> **注意:** 如果您的网站位于某种服务器缓存系统后面(例如,Cloudflare 默认提供一个),您也需要在那里清除文件。清除浏览器缓存也是一个好主意。Bludit 尝试用新文件重新加载文件,但某些组件(如 TinyMCE)可能无法重新加载,并在 UI 中引发一些问题或抛出 JavaScript 错误。
## 支持Bludit

Bludit是开源且免费使用的，但如果您发现该项目有用并希望支持其开发，您可以在[Patreon](https://www.patreon.com/join/bludit)上贡献。作为我们感激的象征，支持者将收到Bludit PRO。

如果您愿意，您也可以进行一次性捐赠，为我们买杯咖啡或啤酒。每笔贡献都帮助我们继续改进Bludit并为用户提供最佳体验。

- [PayPal](https://www.paypal.me/bludit/10)
- BTC (Bitcoin): [bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph](https://www.blockchain.com/explorer/addresses/btc/bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph)
- ETH (Ethereum): [0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7](https://www.blockchain.com/explorer/addresses/eth/0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7)

## 许可证

Bludit是根据[MIT许可证](https://tldrlegal.com/license/mit-license)许可的开源软件。
