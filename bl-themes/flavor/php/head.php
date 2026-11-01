<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="generator" content="Bludit">

<?php echo Theme::metaTags('title'); ?>

<?php echo Theme::metaTags('description'); ?>

<?php
	// Bludit stores text with HTML entities, JSON-LD needs the plain text
	$encodeJsonLd = function ($data) {
		array_walk_recursive($data, function (&$value) {
			if (is_string($value)) {
				$value = Sanitize::htmlDecode($value);
			}
		});
		return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
	};
?>
<?php if ($WHERE_AM_I === 'page' && !$url->notFound() && !$page->isStatic()): ?>
<script type="application/ld+json">
<?php
	$jsonLd = [
		'@context' => 'https://schema.org',
		'@type' => 'BlogPosting',
		'mainEntityOfPage' => [
			'@type' => 'WebPage',
			'@id' => $page->permalink()
		],
		'headline' => $page->title(),
		'datePublished' => $page->date('c'),
		'url' => $page->permalink(),
		'publisher' => [
			'@type' => 'Organization',
			'name' => $site->title()
		]
	];
	if ($page->description()) {
		$jsonLd['description'] = $page->description();
	}
	if ($page->dateModified('Y-m-d\TH:i:sP')) {
		$jsonLd['dateModified'] = $page->dateModified('Y-m-d\TH:i:sP');
	}
	if ($page->coverImage()) {
		$jsonLd['image'] = [
			'@type' => 'ImageObject',
			'url' => $page->coverImage(true)
		];
	}
	if ($page->user('nickname')) {
		$jsonLd['author'] = [
			'@type' => 'Person',
			'name' => $page->user('nickname')
		];
	}
	$seoTags = $page->tags(true);
	if (!empty($seoTags)) {
		$jsonLd['keywords'] = implode(', ', array_values($seoTags));
	}
	if ($page->readingTime()) {
		$jsonLd['timeRequired'] = 'PT' . max(1, intval($page->readingTime())) . 'M';
	}
	echo $encodeJsonLd($jsonLd);
?>
</script>

<script type="application/ld+json">
<?php
	$breadcrumbItems = [
		[
			'@type' => 'ListItem',
			'position' => 1,
			'name' => $site->title(),
			'item' => Theme::siteUrl()
		]
	];
	$position = 2;
	if ($page->categoryKey()) {
		$breadcrumbItems[] = [
			'@type' => 'ListItem',
			'position' => $position,
			'name' => $page->category(),
			'item' => $page->categoryPermalink()
		];
		$position++;
	}
	$breadcrumbItems[] = [
		'@type' => 'ListItem',
		'position' => $position,
		'name' => $page->title(),
		'item' => $page->permalink()
	];
	echo $encodeJsonLd([
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => $breadcrumbItems
	]);
?>
</script>
<?php elseif ($WHERE_AM_I !== 'page' && !$url->notFound()): ?>
<script type="application/ld+json">
<?php
	echo $encodeJsonLd([
		'@context' => 'https://schema.org',
		'@type' => 'WebSite',
		'name' => $site->title(),
		'description' => $site->description(),
		'url' => Theme::siteUrl()
	]);
?>
</script>
<?php endif; ?>

<?php echo Theme::favicon('img/favicon.png'); ?>

<?php echo Theme::css('css/tailwind.css'); ?>

<?php echo Theme::css('css/style.css'); ?>

<?php Theme::plugins('siteHead'); ?>
