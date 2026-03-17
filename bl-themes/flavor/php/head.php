<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="generator" content="Bludit">

<!-- Dynamic title tag -->
<?php echo Theme::metaTags('title'); ?>

<!-- Dynamic description tag -->
<?php echo Theme::metaTags('description'); ?>

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
