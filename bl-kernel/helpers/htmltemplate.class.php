<?php defined('BLUDIT') or die('Bludit CMS.');

/*
 | HTML templates
 |
 | The user writes the theme as plain HTML with {{ placeholders }}, from the
 | admin or through the API. The HTML is compiled to one PHP file when it is
 | saved, never on a request, so the site renders as fast as a PHP theme.
 |
 | The template can't run code: every placeholder maps to a PHP snippet
 | written here, an unknown placeholder fails the save, and any "<?" in the
 | user's HTML or CSS is printed as text, never opened as a PHP tag.
 |
 | Syntax
 |	{{ page.title }}			value or block from the list below
 |	{{#if page.cover}} ... {{else}} ... {{/if}}
*/

class HtmlTemplate
{
	// Template files, the name is the key on the API
	const FILES = array(
		'layout.html' => 'Base HTML of every page, prints {{ content }}',
		'home.html' => 'Lists of posts: home, blog, category, tag and search, prints {{ posts }}',
		'post-item.html' => 'One post inside {{ posts }}',
		'page.html' => 'A single post or static page',
		'404.html' => 'Page not found',
		'style.css' => 'Stylesheet, printed inline by {{ styles }}'
	);

	const MAX_FILE_SIZE = 262144;

	// Compiled parts, filled by the compiled file
	public static $parts = array();

	public static function path($file = '')
	{
		return PATH_CONTENT . 'templates' . DS . $file;
	}

	// The compiled file calls the kernel, a new version of Bludit compiles
	// it again instead of running code written for the old one
	public static function compiledFile()
	{
		return self::path('compiled-' . BLUDIT_VERSION . '.php');
	}

	public static function defaultsPath()
	{
		return PATH_THEMES . 'template' . DS . 'default' . DS;
	}

	// ------------------------------------------------------------------------
	// Placeholders
	// ------------------------------------------------------------------------

	// Each placeholder has the PHP that prints it ("echo") and the PHP
	// expression that {{#if}} tests ("test"). Values stored by Bludit are
	// already sanitized on save, they are printed as the PHP themes do.
	public static function placeholders()
	{
		$value = function ($expression, $description) {
			return array('echo' => 'echo ' . $expression . ';', 'test' => $expression, 'description' => $description);
		};

		return array(
			// Site
			'site.title' => $value('$site->title()', 'Site title'),
			'site.slogan' => $value('$site->slogan()', 'Site slogan'),
			'site.description' => $value('$site->description()', 'Site description'),
			'site.footer' => $value('$site->footer()', 'Footer text from the settings'),
			'site.logo' => $value('$site->logo()', 'Logo URL'),
			'site.url' => $value('Theme::siteUrl()', 'Home URL'),
			'site.lang' => $value('Theme::lang()', 'Language code for <html lang>'),
			'site.rss' => $value('Theme::rssUrl()', 'RSS URL, empty if the RSS plugin is disabled'),

			// Blocks
			'head' => array(
				'echo' => 'echo \'<meta charset="utf-8">\' . PHP_EOL . \'<meta name="viewport" content="width=device-width, initial-scale=1">\' . PHP_EOL . \'<meta name="generator" content="Bludit">\' . PHP_EOL; echo Theme::metaTags(\'title\'); echo Theme::metaTags(\'description\'); echo Theme::favicon(\'img/favicon.png\'); Theme::plugins(\'siteHead\');',
				'description' => 'Charset, viewport, title, description and plugins of the <head>, required in layout.html'
			),
			'styles' => array(
				'description' => 'style.css printed inline in a <style> tag'
			),
			'content' => array(
				'echo' => 'HtmlTemplate::content();',
				'description' => 'The body of the current URL: home.html, page.html or 404.html. Only in layout.html',
				'only' => array('layout.html')
			),
			'menu' => array(
				'echo' => 'HtmlTemplate::menu();',
				'test' => '!empty($staticContent) || $site->homepage()',
				'description' => 'Links to the static pages, <a class="active"> for the current one'
			),
			'posts' => array(
				'echo' => 'foreach ($content as $page) { HtmlTemplate::render(\'post-item.html\'); }',
				'test' => '!empty($content)',
				'description' => 'Renders post-item.html for each post of the list. Only in home.html',
				'only' => array('home.html')
			),
			'pagination' => array(
				'echo' => 'HtmlTemplate::pagination();',
				'test' => 'Paginator::numberOfPages() > 1',
				'description' => '<nav class="pagination"> with the previous and next links'
			),
			'pagination.prev' => $value('(Paginator::showPrev() ? htmlspecialchars(Paginator::previousPageUrl(), ENT_QUOTES, \'UTF-8\') : \'\')', 'URL of the previous page of the list, empty on the first'),
			'pagination.next' => $value('(Paginator::showNext() ? htmlspecialchars(Paginator::nextPageUrl(), ENT_QUOTES, \'UTF-8\') : \'\')', 'URL of the next page of the list, empty on the last'),
			'listing.title' => $value('HtmlTemplate::listingTitle()', 'Name of the tag or category of the list, empty on the home'),
			'search' => array(
				'echo' => 'if (pluginActivated(\'pluginSearch\')) { echo getPlugin(\'pluginSearch\')->searchBox(); }',
				'test' => 'pluginActivated(\'pluginSearch\')',
				'description' => 'Search box, needs the Search plugin'
			),

			// Page, the current page in page.html, each post in post-item.html
			'page.title' => $value('$page->title()', 'Title'),
			'page.url' => $value('$page->permalink()', 'URL'),
			'page.content' => $value('$page->content()', 'Full content'),
			'page.excerpt' => $value('$page->contentBreak()', 'Content until the page break'),
			'page.readMore' => $value('$page->readMore()', 'True when the content has a page break, for {{#if}}'),
			'page.description' => $value('$page->description()', 'Description'),
			'page.date' => $value('$page->date()', 'Date, with the format of the settings'),
			'page.dateISO' => $value('$page->date(\'c\')', 'Date in ISO 8601, for <time datetime>'),
			'page.cover' => $value('$page->coverImage()', 'Cover image URL'),
			'page.category' => $value('$page->category()', 'Category name'),
			'page.categoryUrl' => $value('$page->categoryPermalink()', 'Category URL'),
			'page.author' => $value('$page->user(\'nickname\')', 'Author nickname'),
			'page.readingTime' => $value('$page->readingTime()', 'Reading time'),
			'page.static' => array('test' => '$page->isStatic()', 'description' => 'True for static pages, for {{#if}}'),
			'page.tags' => array(
				'echo' => 'foreach ($page->tags(true) as $tagKey => $tagName) { echo \'<a class="tag" href="\' . DOMAIN_TAGS . $tagKey . \'">\' . $tagName . \'</a> \'; }',
				'test' => '!empty($page->tags(true))',
				'description' => 'Links <a class="tag"> to the tags'
			),

			// Plugin hooks
			'plugins.bodyBegin' => array('echo' => 'Theme::plugins(\'siteBodyBegin\');', 'description' => 'Plugins at the start of <body>'),
			'plugins.bodyEnd' => array('echo' => 'Theme::plugins(\'siteBodyEnd\');', 'description' => 'Plugins at the end of <body>'),
			'plugins.sidebar' => array('echo' => 'Theme::plugins(\'siteSidebar\');', 'description' => 'Sidebar plugins'),
			'plugins.pageBegin' => array('echo' => 'Theme::plugins(\'pageBegin\');', 'description' => 'Plugins before a page or post'),
			'plugins.pageEnd' => array('echo' => 'Theme::plugins(\'pageEnd\');', 'description' => 'Plugins after a page or post'),

			// Where the visitor is, for {{#if}}
			'where.home' => array('test' => '$WHERE_AM_I === \'home\' && $url->pageNumber() === 1', 'description' => 'First page of the home'),
			'where.list' => array('test' => 'in_array($WHERE_AM_I, array(\'home\', \'blog\', \'tag\', \'category\', \'search\'))', 'description' => 'Any list of posts'),
			'where.page' => array('test' => '$WHERE_AM_I === \'page\' && !$url->notFound()', 'description' => 'A single post or page'),
			'where.tag' => array('test' => '$WHERE_AM_I === \'tag\'', 'description' => 'Posts of a tag'),
			'where.category' => array('test' => '$WHERE_AM_I === \'category\'', 'description' => 'Posts of a category'),
			'where.search' => array('test' => '$WHERE_AM_I === \'search\'', 'description' => 'Search results'),
			'where.notFound' => array('test' => '$url->notFound()', 'description' => 'Page not found')
		);
	}

	// How to build a template, for the LLMs that design the site through the
	// API or the MCP. The placeholders themselves come from reference().
	public static function guide()
	{
		return <<<'GUIDE'
# Building a Bludit theme with the Template system

The whole design is 6 files. No PHP, no JavaScript files, no build step.

## Workflow
1. Read the current files and build on them.
2. If the request is vague, propose a direction (mood, palette, fonts, layout) in a few lines, then build it. Ask at most one question.
3. Send every changed file in one save, each with its full content. Files not sent keep their content.
4. The whole set is checked before saving. On an error nothing changes and the message says "<file> line <n>: <problem>": fix that line and save again.
5. Tell the user what changed and which URLs to check: home, a post, a static page, a tag, /search/<word>, a missing page. One step of undo is available (revert), calling it again goes forward.

## Files
- layout.html: the frame of every page. Must print {{ head }} inside <head>, {{ styles }} inside <head>, and {{ content }} in the body.
- home.html: every list of posts (home, blog, category, tag, search). Prints {{ posts }}.
- post-item.html: one post card, repeated by {{ posts }}.
- page.html: a single post or static page.
- 404.html: page not found.
- style.css: the CSS, printed inline by {{ styles }}. Web fonts can be loaded with @import. Use CSS and inline SVG for decoration, there are no image files.
Max 256 KB per file.

## Syntax, only this
- {{ name }} prints a placeholder.
- {{#if name}} ... {{/if}}, {{#if name}} ... {{else}} ... {{/if}}, {{#if not name}} ... {{/if}}. Ifs can be nested.
- Everything else is printed as written. No loops, filters, expressions, comparisons or variables.
- Only the listed placeholders exist. Some only print, some only test (if), some only work in some files: the list says which.

## Required
- All plugin hooks, or the plugins break: {{ plugins.bodyBegin }} right after <body>, {{ plugins.bodyEnd }} right before </body>, {{ plugins.sidebar }} somewhere visible (sidebar or footer), {{ plugins.pageBegin }} and {{ plugins.pageEnd }} around the content in page.html (comments go there).
- Placeholders print values as Bludit stores them, do not add escaping.

## Markup you have to style
- {{ menu }}: plain <a> links, the current one has class="active". No wrapper, add your own <nav>.
- {{ pagination }}: <nav class="pagination"> with <a class="prev"> and <a class="next">. Or build your own with pagination.prev and pagination.next.
- {{ page.tags }}: <a class="tag"> links.
- {{ plugins.sidebar }}: each widget is <div class="plugin plugin-NAME"> with <h2 class="plugin-label"> and <div class="plugin-content">, usually a <ul>. It cannot be tested with #if: wrap it (<aside class="plugins">) and hide it when empty with .plugins:empty{display:none}. Add li:empty{display:none}.
- {{ search }}: the Search plugin form.
- Search pages: the Search plugin prints its own header and, with no results, its own "No results found" box, at the start of {{ content }}. Style .search-results-header, .search-query, .search-count, .search-no-results. In home.html do not print your own empty message there: {{#if posts}}...{{else}}{{#if not where.search}}<p>Nothing here.</p>{{/if}}{{/if}}
- {{ page.content }} and {{ page.excerpt }} are free HTML from the editor. Style every element inside your content wrapper: h2-h4, p, a, ul, ol, blockquote, pre, code, kbd, table (display:block; overflow-x:auto for phones), img, video, iframe (max-width:100%), figure, figcaption, hr, details, summary. Headings in excerpts on lists should be smaller than the card title.

## Patterns
- Intro (slogan, description, search) only on {{#if where.home}}. Show {{ listing.title }} on tag and category lists.
- Wrap optional values so nothing empty is printed: {{#if page.cover}}<img src="{{ page.cover }}" alt="">{{/if}}, {{#if page.tags}}<p class="tags">{{ page.tags }}</p>{{/if}}.
- {{#if page.static}} tells static pages (About, Contact) from posts: hide date, author and reading time on them.
- "Read more" link only {{#if page.readMore}}.
- Use <time datetime="{{ page.dateISO }}">{{ page.date }}</time>.

## Quality
- Responsive from 360 px to wide screens, never a horizontal scroll.
- Semantic HTML: header, nav, main, article, aside, footer, time.
- Readable posts: about 65-75 characters per line, comfortable line height.
- Visible focus styles and enough colour contrast.
- CSS custom properties for colours and fonts, so the palette is easy to change.
- Support prefers-color-scheme (light and dark) unless asked for only one.
GUIDE;
	}

	// List for the API and the MCP, what each placeholder does and where it works
	public static function reference()
	{
		$list = array();
		foreach (self::placeholders() as $name => $placeholder) {
			$list[$name] = array(
				'description' => $placeholder['description'],
				'print' => isset($placeholder['echo']) || $name === 'styles',
				'if' => isset($placeholder['test'])
			);
			if (isset($placeholder['only'])) {
				$list[$name]['only'] = $placeholder['only'];
			}
		}
		return $list;
	}

	// ------------------------------------------------------------------------
	// Compiler
	// ------------------------------------------------------------------------

	// Prints the text as text, never as a PHP open tag
	private static function literal($text)
	{
		return str_replace('<?', '<?php echo \'<?\'; ?>', $text);
	}

	// Compiles one HTML file to PHP, throws an Exception with the line of the error
	public static function compileFile($name, $source, $css)
	{
		$placeholders = self::placeholders();
		$php = '';
		$stack = array();
		$position = 0;

		$lineOf = function ($offset) use ($source) {
			return substr_count($source, "\n", 0, $offset) + 1;
		};

		preg_match_all('/\{\{(.*?)\}\}/s', $source, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

		foreach ($matches as $match) {
			$offset = $match[0][1];
			$tag = trim($match[1][0]);
			$line = $lineOf($offset);

			$php .= self::literal(substr($source, $position, $offset - $position));
			$position = $offset + strlen($match[0][0]);

			if ($tag === 'else') {
				if (empty($stack) || $stack[count($stack) - 1]['else']) {
					throw new Exception("$name line $line: {{else}} without {{#if}}");
				}
				$stack[count($stack) - 1]['else'] = true;
				$php .= '<?php else: ?>';
				continue;
			}

			if ($tag === '/if') {
				if (empty($stack)) {
					throw new Exception("$name line $line: {{/if}} without {{#if}}");
				}
				array_pop($stack);
				$php .= '<?php endif; ?>';
				continue;
			}

			if (preg_match('/^#if\s+(not\s+)?([A-Za-z0-9_.]+)$/', $tag, $parts)) {
				$key = $parts[2];
				if (!isset($placeholders[$key]['test'])) {
					throw new Exception("$name line $line: {{#if $key}} is not a condition");
				}
				self::checkOnly($name, $line, $key, $placeholders[$key]);
				$stack[] = array('line' => $line, 'else' => false);
				$php .= '<?php if (' . ($parts[1] ? '!' : '') . '(' . $placeholders[$key]['test'] . ')): ?>';
				continue;
			}

			if (!preg_match('/^[A-Za-z0-9_.]+$/', $tag) || !isset($placeholders[$tag])) {
				throw new Exception("$name line $line: unknown placeholder {{ $tag }}");
			}

			if ($tag === 'styles') {
				$php .= '<style>' . self::literal($css) . '</style>';
				continue;
			}

			if (!isset($placeholders[$tag]['echo'])) {
				throw new Exception("$name line $line: {{ $tag }} can only be used in {{#if}}");
			}

			self::checkOnly($name, $line, $tag, $placeholders[$tag]);
			$php .= '<?php ' . $placeholders[$tag]['echo'] . ' ?>';
		}

		if (!empty($stack)) {
			$open = array_pop($stack);
			throw new Exception("$name line {$open['line']}: {{#if}} without {{/if}}");
		}

		$php .= self::literal(substr($source, $position));

		return $php;
	}

	private static function checkOnly($name, $line, $tag, $placeholder)
	{
		if (isset($placeholder['only']) && !in_array($name, $placeholder['only'])) {
			throw new Exception("$name line $line: {{ $tag }} only works in " . implode(', ', $placeholder['only']));
		}
	}

	// Compiles the whole set to the PHP file that the theme includes
	public static function compile($files)
	{
		$css = isset($files['style.css']) ? $files['style.css'] : '';

		$php = '<?php defined(\'BLUDIT\') or die(\'Bludit CMS.\');' . PHP_EOL;
		$php .= '// Compiled from bl-content/templates/ on ' . date('Y-m-d H:i:s') . ', do not edit this file' . PHP_EOL;

		foreach (self::FILES as $name => $description) {
			if ($name === 'style.css') {
				continue;
			}
			$body = self::compileFile($name, isset($files[$name]) ? $files[$name] : '', $css);
			$php .= 'HtmlTemplate::$parts[' . var_export($name, true) . '] = function () {' . PHP_EOL;
			$php .= "\t" . 'global $site, $url, $L, $page, $content, $staticContent, $WHERE_AM_I;' . PHP_EOL;
			$php .= '?>' . $body . '<?php' . PHP_EOL;
			$php .= '};' . PHP_EOL;
		}

		return $php;
	}

	// ------------------------------------------------------------------------
	// Storage
	// ------------------------------------------------------------------------

	// Returns the current source files, the default ones until the set is installed
	public static function files()
	{
		$files = self::read(self::path());
		return $files ? $files : self::read(self::defaultsPath());
	}

	// Saves some files of the set, the missing ones keep their current
	// content. Nothing is written if the set doesn't compile.
	// Returns TRUE or the error message.
	public static function save($changes)
	{
		foreach ($changes as $name => $source) {
			if (!isset(self::FILES[$name])) {
				return 'Unknown file ' . $name . ', valid files: ' . implode(', ', array_keys(self::FILES));
			}
			if (!is_string($source)) {
				return $name . ' must be a string';
			}
			if (strlen($source) > self::MAX_FILE_SIZE) {
				return $name . ' is bigger than ' . self::MAX_FILE_SIZE . ' bytes';
			}
		}

		$current = self::files();
		$files = array_merge($current, $changes);

		try {
			$compiled = self::compile($files);
		} catch (Exception $e) {
			return $e->getMessage();
		}

		// One step of undo, the set before this save, written before publishing
		// so a successful save always has its revert
		$previousPath = self::path('previous' . DS);
		$previous = self::read($previousPath);
		if (!self::writeFiles($current, $previousPath)) {
			return 'Error writing the previous version of the template';
		}

		if (!self::writeSet($files, $compiled)) {
			// The sources could be half written while the old compiled file is
			// still the live one. Put the current set and the undo back, so a
			// failed save changes nothing and a later partial save doesn't
			// publish the rejected changes
			if (!self::writeFiles($current, self::path()) || ($previous && !self::writeFiles($previous, $previousPath))) {
				Log::set(__METHOD__ . LOG_SEP . 'Error restoring the template files after a failed save');
			}
			return 'Error writing the template files';
		}

		return true;
	}

	// Restores the set before the last save, the current set becomes the
	// previous one, so a second revert undoes the revert
	public static function revert()
	{
		$previousPath = self::path('previous' . DS);
		if (!file_exists($previousPath . 'layout.html')) {
			return 'There is no previous version';
		}

		$previous = self::read($previousPath);

		// save() keeps the current set as the previous one
		return self::save($previous);
	}

	// Copies the default set of the theme, the first time the theme runs
	public static function install()
	{
		$files = self::files();

		try {
			$compiled = self::compile($files);
		} catch (Exception $e) {
			Log::set(__METHOD__ . LOG_SEP . $e->getMessage());
			return false;
		}

		return self::writeSet($files, $compiled);
	}

	// The set saved in a folder, FALSE when there is none
	private static function read($path)
	{
		if (!file_exists($path . 'layout.html')) {
			return false;
		}
		$files = array();
		foreach (self::FILES as $name => $description) {
			$files[$name] = (string) @file_get_contents($path . $name);
		}
		return $files;
	}

	private static function writeFiles($files, $path)
	{
		if (!is_dir($path) && !mkdir($path, DIR_PERMISSIONS, true)) {
			return false;
		}
		foreach ($files as $name => $source) {
			if (file_put_contents($path . $name, $source, LOCK_EX) === false) {
				return false;
			}
		}
		return true;
	}

	private static function writeSet($files, $compiled)
	{
		// The compiled file is written first, if it fails the sources are not touched.
		// Then rename, a request never includes a half written file
		if (!is_dir(self::path()) && !mkdir(self::path(), DIR_PERMISSIONS, true)) {
			return false;
		}
		$compiledFile = self::compiledFile();
		$tmp = $compiledFile . '.' . bin2hex(random_bytes(4)) . '.tmp';
		if (file_put_contents($tmp, $compiled) === false) {
			@unlink($tmp);
			return false;
		}
		if (!self::writeFiles($files, self::path()) || !rename($tmp, $compiledFile)) {
			@unlink($tmp);
			return false;
		}

		// Opcache keeps the old version until it checks the timestamp again
		if (function_exists('opcache_invalidate')) {
			opcache_invalidate($compiledFile, true);
		}
		return true;
	}

	// ------------------------------------------------------------------------
	// Render
	// ------------------------------------------------------------------------

	// Entry point of the theme
	private static $searchHeader = '';

	public static function run()
	{
		if ((@include self::compiledFile()) === false) {
			if (!self::build() || (@include self::compiledFile()) === false) {
				exit('The template can not be compiled, check the Bludit logs.');
			}
		}
		// The Search plugin prints its results header in siteBodyBegin, above
		// the layout, it goes at the start of {{ content }} instead
		if (pluginActivated('pluginSearch')) {
			self::$searchHeader = (string) getPlugin('pluginSearch')->takeHeader();
		}
		self::render('layout.html');
	}

	// The body of the current URL, {{ content }} of layout.html
	public static function content()
	{
		global $url, $WHERE_AM_I;

		echo self::$searchHeader;
		if ($url->notFound()) {
			self::render('404.html');
		} elseif ($WHERE_AM_I === 'page') {
			self::render('page.html');
		} else {
			self::render('home.html');
		}
	}

	// Compiles the saved files, or installs the default ones the first time
	private static function build()
	{
		if (!file_exists(self::path('layout.html'))) {
			return self::install();
		}

		try {
			$compiled = self::compile(self::files());
		} catch (Exception $e) {
			Log::set(__METHOD__ . LOG_SEP . $e->getMessage());
			return false;
		}
		return self::writeSet(array(), $compiled);
	}

	public static function render($name)
	{
		if (isset(self::$parts[$name])) {
			call_user_func(self::$parts[$name]);
		}
	}

	// Helpers used by the compiled template
	// ------------------------------------------------------------------------

	public static function menu()
	{
		global $site, $url, $staticContent, $WHERE_AM_I, $L;

		if ($site->homepage()) {
			$active = ($WHERE_AM_I === 'blog') ? ' class="active"' : '';
			echo '<a' . $active . ' href="' . DOMAIN_BASE . ltrim($url->filters('blog'), '/') . '">' . $L->get('Blog') . '</a>' . PHP_EOL;
		}

		foreach ($staticContent as $staticPage) {
			if ($staticPage->isChild()) {
				continue;
			}
			$active = ($url->slug() == $staticPage->slug()) ? ' class="active"' : '';
			echo '<a' . $active . ' href="' . $staticPage->permalink() . '">' . $staticPage->title() . '</a>' . PHP_EOL;
		}
	}

	public static function pagination()
	{
		global $L;

		if (Paginator::numberOfPages() <= 1) {
			return;
		}

		echo '<nav class="pagination">';
		if (Paginator::showPrev()) {
			echo '<a class="prev" href="' . htmlspecialchars(Paginator::previousPageUrl(), ENT_QUOTES, 'UTF-8') . '">&larr; ' . $L->get('Previous') . '</a>';
		}
		if (Paginator::showNext()) {
			echo '<a class="next" href="' . htmlspecialchars(Paginator::nextPageUrl(), ENT_QUOTES, 'UTF-8') . '">' . $L->get('Next') . ' &rarr;</a>';
		}
		echo '</nav>';
	}

	public static function listingTitle()
	{
		global $url, $WHERE_AM_I;

		try {
			if ($WHERE_AM_I === 'tag') {
				return (new Tag($url->slug()))->name();
			}
			if ($WHERE_AM_I === 'category') {
				return (new Category($url->slug()))->name();
			}
		} catch (Exception $e) {
		}
		return '';
	}
}
