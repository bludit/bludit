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

Bludit - シンプルで高速で柔軟なCMS。

Bluditを使えば、数秒で自分のウェブサイトやブログを作成できます。完全に無料でオープンソースで使いやすいです。BluditはコンテンツをJSON形式で保存するため、データベースのインストールや設定は必要ありません。必要なのはPHPをサポートするウェブサーバーだけです。

フラットファイルCMSとして、Bluditは比類のない柔軟性と速度を提供します。さらに、MarkdownとHTMLコードのサポートにより、コンテンツの作成と管理がこれまで以上に簡単になりました。

## リソース

- [プラグイン](https://plugins.bludit.com)
- [テーマ](https://themes.bludit.com)
- [ドキュメント](https://docs.bludit.com)
- ニュースと発表は[Twitter](https://twitter.com/bludit)、[Facebook](https://www.facebook.com/bluditcms)、[Reddit](https://www.reddit.com/r/bludit/)で
- トークとチャットは[Discord](https://discord.gg/CFaXEdZWds)で
- ヘルプとサポートは[フォーラム](https://forum.bludit.org)で
- バグレポートは[Github Issues](https://github.com/bludit/bludit/issues)で

## 要件

- PHPをサポートするウェブサーバー。
- PHPバージョン5.6以上。
- UTF-8の完全サポートのためのPHP [mbstring](http://php.net/manual/en/book.mbstring.php)モジュール。
- 画像処理のためのPHP [gd](http://php.net/manual/en/book.image.php)モジュール。
- DOM操作のためのPHP [dom](http://php.net/manual/en/book.dom.php)モジュール。
- JSON操作のためのPHP [json](http://php.net/manual/en/book.json.php)モジュール。

## インストール

1. 公式ページから最新バージョンをダウンロード：[Bludit.com](https://www.bludit.com)
2. zipファイルをディレクトリに展開、例えば`bludit`。
3. `bludit`ディレクトリをウェブサーバーまたはホスティングにアップロード。
4. ドメインにアクセス（例: https://example.com/bludit/）。
5. Bluditインストーラーに従ってサイトを設定。

## テストのためのクイックインストール

PHPの組み込みウェブサーバー（`php -S localhost:8000`）またはDockerを使用できます：

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

その後 http://localhost:8000 を開いてください

## Bluditをサポート

Bluditはオープンソースで無料ですが、プロジェクトが有用だと感じて開発をサポートしたい場合は、[Patreon](https://www.patreon.com/bePatron?c=921115&rid=2458860)で貢献できます。私たちの感謝の印として、サポーターはBludit PROを受け取ります。

好みに応じて、コーヒーやビールを買うための1回限りの寄付も可能です。すべての貢献がBluditの改善とユーザーへの最高の体験提供に役立ちます。

- [PayPal](https://www.paypal.me/bludit/10)
- BTC (Bitcoin): bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph
- ETH (Ethereum): 0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7

## ライセンス

Bluditは[MITライセンス](https://tldrlegal.com/license/mit-license)下のオープンソースソフトウェアです。
