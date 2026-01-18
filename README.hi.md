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

Bludit - सरल, तेज और लचीला CMS.

Bludit के साथ, आप कुछ सेकंड में अपनी खुद की वेबसाइट या ब्लॉग बना सकते हैं। यह पूरी तरह से मुफ्त, ओपन-सोर्स और उपयोग में आसान है। Bludit सामग्री को JSON प्रारूप में संग्रहीत करता है, जो डेटाबेस इंस्टॉलेशन या कॉन्फ़िगरेशन की आवश्यकता को समाप्त कर देता है। आपको केवल PHP सपोर्ट वाले वेब सर्वर की आवश्यकता है।

एक Flat-File CMS के रूप में, Bludit अद्वितीय लचीलापन और गति प्रदान करता है। इसके अलावा, Markdown और HTML कोड के समर्थन के साथ, सामग्री बनाना और प्रबंधित करना कभी इतना आसान नहीं रहा।

## संसाधन

- [प्लगइन्स](https://plugins.bludit.com)
- [थीम्स](https://themes.bludit.com)
- [दस्तावेज़ीकरण](https://docs.bludit.com)
- समाचार और घोषणाएं [ट्विटर](https://twitter.com/bludit), [फेसबुक](https://www.facebook.com/bluditcms) और [रेडिट](https://www.reddit.com/r/bludit/) पर
- बातचीत और चैट [डिस्कॉर्ड](https://discord.gg/CFaXEdZWds) पर
- मदद और समर्थन [फोरम](https://forum.bludit.org) पर
- बग रिपोर्ट [गिटहब इश्यूज](https://github.com/bludit/bludit/issues) पर

## आवश्यकताएँ

- PHP सपोर्ट वाला वेबसर्वर।
- PHP v8.0 या उच्च संस्करण।
- पूर्ण UTF-8 समर्थन के लिए PHP [mbstring](http://php.net/manual/en/book.mbstring.php) मॉड्यूल।
- छवि प्रसंस्करण के लिए PHP [gd](http://php.net/manual/en/book.image.php) मॉड्यूल।
- DOM मैनिपुलेशन के लिए PHP [dom](http://php.net/manual/en/book.dom.php) मॉड्यूल।
- JSON मैनिपुलेशन के लिए PHP [json](http://php.net/manual/en/book.json.php) मॉड्यूल।

## इंस्टॉलेशन

1. आधिकारिक पेज से नवीनतम संस्करण डाउनलोड करें: [Bludit.com](https://www.bludit.com)
2. ज़िप फ़ाइल को एक डायरेक्टरी में निकालें, जैसे `bludit`।
3. `bludit` डायरेक्टरी को अपने वेब सर्वर या होस्टिंग पर अपलोड करें।
4. अपना डोमेन विज़िट करें (उदा., https://example.com/bludit/)।
5. अपनी वेबसाइट सेट अप करने के लिए Bludit इंस्टॉलर का पालन करें।

## परीक्षण के लिए त्वरित इंस्टॉलेशन

आप PHP बिल्ट-इन वेब सर्वर (`php -S localhost:8000`) या Docker का उपयोग कर सकते हैं:

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

फिर http://localhost:8000 खोलें

## Bludit का समर्थन करें

Bludit ओपन-सोर्स और उपयोग करने के लिए मुफ्त है, लेकिन यदि आपको प्रोजेक्ट उपयोगी लगता है और इसके विकास का समर्थन करना चाहते हैं, तो आप [पैट्रियन](https://www.patreon.com/bePatron?c=921115&rid=2458860) पर योगदान दे सकते हैं। हमारी सराहना के प्रतीक के रूप में, समर्थक Bludit PRO प्राप्त करेंगे।

यदि आप चाहें, तो आप हमें कॉफी या बीयर खरीदने के लिए एक बार का दान भी कर सकते हैं। हर योगदान हमें Bludit को बेहतर बनाने और हमारे उपयोगकर्ताओं के लिए सर्वोत्तम संभव अनुभव प्रदान करने में मदद करता है।

- [पेपाल](https://www.paypal.me/bludit/10)
- BTC (बिटकॉइन): bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph
- ETH (ईथीरियम): 0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7

## लाइसेंस

Bludit MIT लाइसेंस के तहत लाइसेंस प्राप्त ओपन सोर्स सॉफ्टवेयर है।
