<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="generator" content="Bludit">

<!-- Dynamic title tag -->
<?php echo Theme::metaTags('title'); ?>

<!-- Dynamic description tag -->
<?php echo Theme::metaTags('description'); ?>

<!-- Canonical URL -->
<?php if ($WHERE_AM_I === 'page'): ?>
<link rel="canonical" href="<?php echo $page->permalink(); ?>">
<?php else: ?>
<link rel="canonical" href="<?php echo Theme::siteUrl(); ?>">
<?php endif; ?>

<!-- Robots meta -->
<?php if ($WHERE_AM_I === 'page'): ?>
<?php
	$robotsDirectives = [];
	if ($page->noindex()) $robotsDirectives[] = 'noindex';
	if ($page->nofollow()) $robotsDirectives[] = 'nofollow';
	if ($page->noarchive()) $robotsDirectives[] = 'noarchive';
	if (!empty($robotsDirectives)):
?>
<meta name="robots" content="<?php echo implode(', ', $robotsDirectives); ?>">
<?php endif; ?>
<?php endif; ?>

<!-- Open Graph -->
<meta property="og:site_name" content="<?php echo $site->title(); ?>">
<meta property="og:locale" content="<?php echo $site->locale(); ?>">
<?php if ($WHERE_AM_I === 'page'): ?>
<meta property="og:type" content="article">
<meta property="og:title" content="<?php echo $page->title(); ?>">
<meta property="og:description" content="<?php echo $page->description(); ?>">
<meta property="og:url" content="<?php echo $page->permalink(); ?>">
<?php if ($page->coverImage()): ?>
<meta property="og:image" content="<?php echo $page->coverImage(true); ?>">
<meta property="og:image:alt" content="<?php echo $page->title(); ?>">
<?php endif; ?>
<meta property="article:published_time" content="<?php echo $page->dateRaw(); ?>">
<?php if ($page->dateModified('Y-m-d\TH:i:sP')): ?>
<meta property="article:modified_time" content="<?php echo $page->dateModified('Y-m-d\TH:i:sP'); ?>">
<?php endif; ?>
<?php if ($page->user('nickname')): ?>
<meta property="article:author" content="<?php echo $page->user('nickname'); ?>">
<?php endif; ?>
<?php $ogTags = $page->tags(true); ?>
<?php foreach ($ogTags as $tagKey => $tagName): ?>
<meta property="article:tag" content="<?php echo $tagName; ?>">
<?php endforeach; ?>
<?php else: ?>
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo $site->title(); ?>">
<meta property="og:description" content="<?php echo $site->description(); ?>">
<meta property="og:url" content="<?php echo Theme::siteUrl(); ?>">
<?php endif; ?>

<!-- Twitter Card -->
<meta name="twitter:card" content="<?php echo ($WHERE_AM_I === 'page' && $page->coverImage()) ? 'summary_large_image' : 'summary'; ?>">
<?php if ($site->twitter()): ?>
<meta name="twitter:site" content="<?php echo $site->twitter(); ?>">
<?php endif; ?>
<?php if ($WHERE_AM_I === 'page'): ?>
<meta name="twitter:title" content="<?php echo $page->title(); ?>">
<meta name="twitter:description" content="<?php echo $page->description(); ?>">
<?php if ($page->coverImage()): ?>
<meta name="twitter:image" content="<?php echo $page->coverImage(true); ?>">
<meta name="twitter:image:alt" content="<?php echo $page->title(); ?>">
<?php endif; ?>
<?php else: ?>
<meta name="twitter:title" content="<?php echo $site->title(); ?>">
<meta name="twitter:description" content="<?php echo $site->description(); ?>">
<?php endif; ?>

<!-- RSS Feed -->
<?php if (Theme::rssUrl()): ?>
<link rel="alternate" type="application/rss+xml" title="<?php echo $site->title(); ?> - RSS Feed" href="<?php echo Theme::rssUrl(); ?>">
<?php endif; ?>

<!-- JSON-LD Structured Data -->
<?php if ($WHERE_AM_I === 'page' && !$page->isStatic()): ?>
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
		'description' => $page->description(),
		'datePublished' => $page->dateRaw(),
		'url' => $page->permalink(),
		'publisher' => [
			'@type' => 'Organization',
			'name' => $site->title()
		]
	];
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
		$jsonLd['timeRequired'] = 'PT' . intval($page->readingTime()) . 'M';
	}
	echo json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
</script>

<!-- Breadcrumb JSON-LD -->
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
	echo json_encode([
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => $breadcrumbItems
	], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
</script>
<?php elseif ($WHERE_AM_I !== 'page'): ?>
<script type="application/ld+json">
<?php
	echo json_encode([
		'@context' => 'https://schema.org',
		'@type' => 'WebSite',
		'name' => $site->title(),
		'description' => $site->description(),
		'url' => Theme::siteUrl()
	], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
</script>
<?php endif; ?>

<!-- Include Favicon -->
<?php echo Theme::favicon('img/favicon.png'); ?>

<!-- Tailwind CSS v4 via CDN -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style type="text/tailwindcss">
@theme {
	--font-sans: 'Inter', system-ui, -apple-system, sans-serif;
	--color-accent-50: #ecfdf5;
	--color-accent-100: #d1fae5;
	--color-accent-200: #a7f3d0;
	--color-accent-300: #6ee7b7;
	--color-accent-400: #34d399;
	--color-accent-500: #10b981;
	--color-accent-600: #059669;
	--color-accent-700: #047857;
	--color-accent-800: #065f46;
	--color-accent-900: #064e3b;
}
</style>

<!-- Google Fonts: Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Include CSS Styles from this theme -->
<?php echo Theme::css('css/style.css'); ?>

<!-- Load Bludit Plugins: Site head -->
<?php Theme::plugins('siteHead'); ?>
