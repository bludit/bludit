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
	--color-accent-50: #f5f3ff;
	--color-accent-100: #ede9fe;
	--color-accent-200: #ddd6fe;
	--color-accent-300: #c4b5fd;
	--color-accent-400: #a78bfa;
	--color-accent-500: #8b5cf6;
	--color-accent-600: #7c3aed;
	--color-accent-700: #6d28d9;
	--color-accent-800: #5b21b6;
	--color-accent-900: #4c1d95;
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
