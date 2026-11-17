<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_GET
// ----------------------------------------------------------------------------
// (string) $_GET['query']
$query = isset($_GET['query']) && is_string($_GET['query']) ? Text::cut(trim($_GET['query']), 0, 100) : '';
// ----------------------------------------------------------------------------
if ($query === '') {
	ajaxResponse(1, 'Invalid query.');
}

$result = array();
$isAuthor = checkRole(array('author'), false);

// Search only the pages, matches the title, the URL and the tags.
// Uses the pages database already in memory, the content files are not read.
// Authors only get their own pages, the same as the content manager.
foreach ($pages->db as $pageKey => $fields) {
	if ($fields['type'] === 'autosave') {
		continue;
	}
	if ($isAuthor && $fields['username'] !== $login->username()) {
		continue;
	}
	$title = Sanitize::htmlDecode($fields['title']);
	$haystack = $title.' '.$pageKey;
	if (!empty($fields['tags'])) {
		$haystack .= ' '.Sanitize::htmlDecode(implode(' ', $fields['tags']));
	}
	if (Text::stringContains($haystack, $query, false)) {
		array_push($result, array(
			'type'=>$fields['type'],
			'typeLabel'=>$L->g(ucfirst($fields['type'])),
			'id'=>$pageKey,
			'text'=>($title === '' ? $L->g('Empty title') : $title),
			'public'=>in_array($fields['type'], array('published', 'sticky', 'static'))
		));
	}
}

exit (json_encode(array('results'=>$result)));
