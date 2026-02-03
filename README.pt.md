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

Bludit - o CMS simples, rápido e flexível.

Com o Bludit, você pode criar seu próprio site ou blog em segundos. É completamente gratuito, de código aberto e fácil de usar. O Bludit armazena o conteúdo em formato JSON, eliminando a necessidade de instalação ou configuração de banco de dados. Tudo o que você precisa é de um servidor web com suporte a PHP.

Como um CMS de arquivos planos, o Bludit oferece flexibilidade e velocidade incomparáveis. Além disso, com suporte a código Markdown e HTML, criar e gerenciar conteúdo nunca foi tão fácil.

## Recursos

- [Plugins](https://plugins.bludit.com)
- [Temas](https://themes.bludit.com)
- [Documentação](https://docs.bludit.com)
- Notícias e anúncios no [Twitter](https://twitter.com/bludit), [Facebook](https://www.facebook.com/bluditcms) e [Reddit](https://www.reddit.com/r/bludit/)
- Conversa e chat no [Discord](https://discord.gg/CFaXEdZWds)
- Ajuda e suporte no [Fórum](https://forum.bludit.org)
- Relatórios de bugs no [Github Issues](https://github.com/bludit/bludit/issues)

## Requisitos

- Servidor web com suporte a PHP.
- PHP v8.0 ou versão superior.
- Módulo PHP [mbstring](http://php.net/manual/en/book.mbstring.php) para suporte completo a UTF-8.
- Módulo PHP [gd](http://php.net/manual/en/book.image.php) para processamento de imagens.
- Módulo PHP [dom](http://php.net/manual/en/book.dom.php) para manipulação de DOM.
- Módulo PHP [json](http://php.net/manual/en/book.json.php) para manipulação de JSON.

## Instalação

1. Baixe a versão mais recente da página oficial: [Bludit.com](https://www.bludit.com)
2. Extraia o arquivo zip em um diretório, como `bludit`.
3. Carregue o diretório `bludit` para o seu servidor web ou hospedagem.
4. Visite o seu domínio (ex.: https://example.com/bludit/).
5. Siga o instalador do Bludit para configurar o seu site.

## Instalação rápida para testes

Você pode usar o servidor web integrado do PHP (`php -S localhost:8000`) ou Docker:

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

Em seguida, abra http://localhost:8000

## Atualizar o Bludit

As etapas a seguir são válidas para atualizar para qualquer versão do Bludit da mesma versão principal. A versão principal é o primeiro dígito no número da versão, por exemplo, Bludit v3.x.

1. **Faça um backup completo**, sem discussão aqui, backup completo dos arquivos e diretórios. Isso significa copiar TODOS os arquivos para uma nova pasta.
2. **Lembre-se de qual versão do Bludit você está usando** para um possível rollback.
3. **Baixe a versão mais recente** da [página oficial](https://www.bludit.com).
4. **Extraia o arquivo zip**.
5. **Substitua os arquivos existentes** pelos novos arquivos.
6. **Limpe o cache do seu navegador**, e por favor leia a nota abaixo.
7. **Faça login na área de administração** e verifique suas configurações.
8. Concluído.

> **Nota:** Se o seu site estiver atrás de algum tipo de sistema de cache de servidor (por exemplo, Cloudflare fornece um por padrão), você precisa limpar os arquivos lá também. Também é uma boa ideia limpar o cache do seu navegador. O Bludit tenta recarregar os arquivos com os novos, mas alguns componentes como TinyMCE podem não recarregar e provocar problemas na interface do usuário ou gerar erros de JavaScript.

## Apoie o Bludit

O Bludit é de código aberto e gratuito para uso, mas se você achar o projeto útil e quiser apoiar seu desenvolvimento, pode contribuir no [Patreon](https://www.patreon.com/join/bludit). Como sinal de nossa apreciação, os apoiadores receberão o Bludit PRO.

Se preferir, você também pode fazer uma doação única para nos comprar um café ou uma cerveja. Cada contribuição nos ajuda a continuar melhorando o Bludit e fornecer a melhor experiência possível aos nossos usuários.

- [PayPal](https://www.paypal.me/bludit/10)
- BTC (Bitcoin): [bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph](https://www.blockchain.com/explorer/addresses/btc/bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph)
- ETH (Ethereum): [0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7](https://www.blockchain.com/explorer/addresses/eth/0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7)

## Licença

O Bludit é software de código aberto licenciado sob a [licença MIT](https://tldrlegal.com/license/mit-license).
