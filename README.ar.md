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

Bludit - نظام إدارة المحتوى البسيط والسريع والمرن.

مع Bludit، يمكنك بناء موقعك الإلكتروني أو مدونتك في ثوانٍ معدودة. إنه مجاني تمامًا، مفتوح المصدر، وسهل الاستخدام. يخزن Bludit المحتوى بتنسيق JSON، مما يلغي الحاجة إلى تثبيت أو تهيئة قاعدة بيانات. كل ما تحتاجه هو خادم ويب يدعم PHP.

كـ Flat-File CMS، يقدم Bludit مرونة وسرعة لا مثيل لهما. بالإضافة إلى ذلك، مع دعم كود Markdown وHTML، أصبح إنشاء وإدارة المحتوى أسهل من أي وقت مضى.

## الموارد

- [الإضافات](https://plugins.bludit.com)
- [القوالب](https://themes.bludit.com)
- [الوثائق](https://docs.bludit.com)
- الأخبار والإعلانات على [تويتر](https://twitter.com/bludit)، [فيسبوك](https://www.facebook.com/bluditcms)، و[ريديت](https://www.reddit.com/r/bludit/)
- الحديث والدردشة على [ديسكورد](https://discord.gg/CFaXEdZWds)
- المساعدة والدعم على [المنتدى](https://forum.bludit.org)
- تقارير الأخطاء على [مشكلات غيت هب](https://github.com/bludit/bludit/issues)

## المتطلبات

- خادم ويب يدعم PHP.
- إصدار PHP 8.0 أو أعلى.
- وحدة PHP [mbstring](http://php.net/manual/en/book.mbstring.php) لدعم UTF-8 الكامل.
- وحدة PHP [gd](http://php.net/manual/en/book.image.php) لمعالجة الصور.
- وحدة PHP [dom](http://php.net/manual/en/book.dom.php) للتعامل مع DOM.
- وحدة PHP [json](http://php.net/manual/en/book.json.php) للتعامل مع JSON.

## التثبيت

1. قم بتنزيل الإصدار الأحدث من الصفحة الرسمية: [Bludit.com](https://www.bludit.com)
2. استخرج ملف الـ zip إلى دليل، مثل `bludit`.
3. ارفع دليل `bludit` إلى خادم الويب أو الاستضافة الخاص بك.
4. قم بزيارة نطاقك (مثل https://example.com/bludit/).
5. اتبع مثبت Bludit لإعداد موقعك.

## تثبيت سريع للاختبار

يمكنك استخدام خادم الويب المدمج في PHP (`php -S localhost:8000`) أو Docker:

```bash
docker pull bludit/docker:latest
docker run -d --name bludit -p 8000:80 bludit/docker:latest
```

ثم افتح http://localhost:8000

## ترقية Bludit

قبل ترقية Bludit، **قم دائمًا بعمل نسخة احتياطية من موقعك**. هذا يشمل:
- مجلد `bl-content/` بأكمله (يحتوي على صفحاتك، القاعدة، الوسائط، الإعدادات)
- أي تخصيصات قمت بها على المواضيع أو الإضافات

### خطوات الترقية

1. **قم بتنزيل أحدث نسخة**: احصل على أحدث إصدار من Bludit من [الموقع الرسمي](https://www.bludit.com) أو [GitHub](https://github.com/bludit/bludit/releases)

2. **قم بعمل نسخة احتياطية**: انسخ المجلدات التالية إلى مكان آمن:
   - `bl-content/` (الأهم - يحتوي على جميع بياناتك)
   - أي مواضيع أو إضافات مخصصة قمت بتعديلها

3. **حذف المجلدات القديمة**: احذف هذه المجلدات من تثبيت Bludit الحالي:
   - `bl-kernel/`
   - `bl-languages/`
   - `bl-plugins/`
   - `bl-themes/`

4. **تحميل الملفات الجديدة**: من حزمة Bludit الجديدة، قم بتحميل:
   - `bl-kernel/`
   - `bl-languages/`
   - `bl-plugins/`
   - `bl-themes/`
   - `index.php`
   - `install.php`
   - `.htaccess` (إذا كان موجودًا)

5. **الاحتفاظ بمحتواك**: **لا تستبدل** مجلد `bl-content/` - فهو يحتوي على جميع بياناتك

6. **تحديث Bludit**: افتح موقعك في متصفح. سيكتشف Bludit الإصدار الجديد ويقوم بتشغيل عملية الترقية تلقائيًا

7. **التحقق من موقعك**: بعد الترقية:
   - تسجيل الدخول إلى لوحة الإدارة
   - التحقق من ظهور محتواك بشكل صحيح
   - اختبار المواضيع والإضافات
   - مراجعة إعداداتك

8. **مسح ذاكرة التخزين المؤقت**: إذا واجهت مشاكل:
   - مسح ذاكرة التخزين المؤقت للمتصفح
   - إذا كنت تستخددم إضافة للتخزين المؤقت، امسح ذاكرتها المؤقتة
   - تحقق من سجلات الخادم للأخطاء

> **ملاحظة**: يحفظ Bludit جميع بياناتك كملفات JSON في `bl-content/databases/`. طالما حافظت على هذا المجلد سليمًا، فإن بياناتك آمنة.

## دعم Bludit

Bludit مفتوح المصدر ومجاني الاستخدام، لكن إذا وجدت المشروع مفيدًا وترغب في دعم تطويره، يمكنك المساهمة على [باتيريون](https://www.patreon.com/join/bludit). كرمز لتقديرنا، سيحصل الداعمون على Bludit PRO.

إذا كنت تفضل، يمكنك أيضًا تقديم تبرع لمرة واحدة لشراء قهوة أو بيرة لنا. كل مساهمة تساعدنا على مواصلة تحسين Bludit وتقديم أفضل تجربة ممكنة لمستخدمينا.

- [باي بال](https://www.paypal.me/bludit/10)
- BTC (بيتكوين): [bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph](https://www.blockchain.com/explorer/addresses/btc/bc1qtets5pdj73uyysjpegfh2gar4pfywra4rglcph)
- ETH (إيثريوم): [0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7](https://www.blockchain.com/explorer/addresses/eth/0x0d7D58D848aA5f175D75Ce4bC746bAC107f331b7)

## الترخيص

Bludit هو برمجية مفتوحة المصدر مرخصة بموجب [ترخيص MIT](https://tldrlegal.com/license/mit-license).
