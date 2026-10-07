<?php

/*
| Build a release of Bludit
|
| Creates the zip file with Bludit and the PRO plugins and themes, and the
| file core.json signed with the release key, both files are uploaded to
| https://bludit.com/release/
|
| Usage:
|	php tools/release.php --pro=/path/to/pro [--out=dist] [--key=~/.bludit-release/signing.key] [--php=8.0]
|
| --pro		Directory with the PRO plugins and themes, bl-plugins/ and bl-themes/ inside
| --out		Directory where the files are created, by default dist/
| --key		Secret key to sign core.json, by default ~/.bludit-release/signing.key
| --php		Minimum version of PHP required by the release, by default 8.0
|
| The content of Bludit comes from the last commit (git archive HEAD), the
| working tree must be clean, so a release always matches a commit
*/

if (PHP_SAPI !== 'cli') {
	exit(1);
}

function stop($message)
{
	fwrite(STDERR, 'Error: ' . $message . PHP_EOL);
	exit(1);
}

$options = getopt('', array('pro:', 'out:', 'key:', 'php:'));
$root = dirname(__DIR__);
$home = getenv('HOME');

$pro = isset($options['pro']) ? rtrim($options['pro'], '/') : false;
$out = isset($options['out']) ? rtrim($options['out'], '/') : $root . '/dist';
$keyFile = isset($options['key']) ? $options['key'] : $home . '/.bludit-release/signing.key';
$php = isset($options['php']) ? $options['php'] : '8.0';

if (!function_exists('sodium_crypto_sign_detached')) {
	stop('the PHP extension sodium is required.');
}
if (!class_exists('ZipArchive')) {
	stop('the PHP extension zip is required.');
}
if ($pro === false || !is_dir($pro)) {
	stop('--pro is required, the directory with the PRO plugins and themes.');
}
if (!is_readable($keyFile)) {
	stop('the key ' . $keyFile . ' is not readable.');
}

// --- Version ---
$init = file_get_contents($root . '/bl-kernel/boot/init.php');
if (!preg_match("/define\('BLUDIT_VERSION',\s*'([^']+)'\)/", $init, $matches)) {
	stop('unable to read BLUDIT_VERSION from bl-kernel/boot/init.php');
}
$version = $matches[1];

// --- Clean working tree ---
chdir($root);
exec('git status --porcelain --untracked-files=no', $status, $code);
if ($code !== 0) {
	stop('git status failed.');
}
if (!empty($status)) {
	stop('the working tree has changes, commit them first.');
}

// --- Public key in the code must match the secret key ---
$secretKey = base64_decode(trim(file_get_contents($keyFile)), true);
if ($secretKey === false || strlen($secretKey) !== SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
	stop('the key ' . $keyFile . ' is not valid.');
}
$publicKey = base64_encode(sodium_crypto_sign_publickey_from_secretkey($secretKey));
if (strpos(file_get_contents($root . '/bl-kernel/boot/variables.php'), "'" . $publicKey . "'") === false) {
	stop('CORE_UPDATE_PUBLIC_KEY in bl-kernel/boot/variables.php does not match the key, the release could not be verified.');
}

// --- Build directory ---
$name = 'bludit-' . $version;
$build = sys_get_temp_dir() . '/bludit-release-' . uniqid();
$target = $build . '/' . $name;
if (!mkdir($target, 0755, true)) {
	stop('unable to create ' . $target);
}

// Only the files tracked by git, the gitignored plugins never end up in a release
$paths = array('index.php', 'install.php', '.htaccess', 'LICENSE', 'bl-kernel', 'bl-languages', 'bl-plugins', 'bl-themes', 'bl-content/.keep');
$command = 'git archive --format=tar HEAD ' . implode(' ', array_map('escapeshellarg', $paths)) . ' | tar -x -C ' . escapeshellarg($target);
exec($command, $output, $code);
if ($code !== 0) {
	stop('git archive failed.');
}

// --- PRO plugins and themes ---
$added = array();
foreach (array('bl-plugins', 'bl-themes') as $type) {
	if (!is_dir($pro . '/' . $type)) {
		continue;
	}
	foreach (glob($pro . '/' . $type . '/*', GLOB_ONLYDIR) as $directory) {
		$id = basename($directory);
		if (file_exists($target . '/' . $type . '/' . $id)) {
			stop($type . '/' . $id . ' exists in Bludit and in the PRO directory.');
		}

		// A PRO plugin without "pro": true runs without a license
		$metadata = json_decode((string) @file_get_contents($directory . '/metadata.json'), true);
		if (!isset($metadata['pro']) || $metadata['pro'] !== true) {
			stop($type . '/' . $id . '/metadata.json must have "pro": true');
		}

		exec('cp -R ' . escapeshellarg($directory) . ' ' . escapeshellarg($target . '/' . $type . '/' . $id), $output, $code);
		if ($code !== 0) {
			stop('unable to copy ' . $directory);
		}
		$added[] = $type . '/' . $id;
	}
}

// --- Zip ---
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
foreach ($iterator as $file) {
	if ($file->isLink()) {
		stop('symbolic link found, ' . $file->getPathname());
	}
}

if (!is_dir($out) && !mkdir($out, 0755, true)) {
	stop('unable to create ' . $out);
}
$zipFile = $out . '/' . $name . '.zip';
@unlink($zipFile);

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
	stop('unable to create ' . $zipFile);
}
foreach ($iterator as $file) {
	$relative = $name . '/' . substr($file->getPathname(), strlen($target) + 1);
	if ($file->isDir()) {
		$zip->addEmptyDir($relative);
	} else {
		$zip->addFile($file->getPathname(), $relative);
	}
}
if (!$zip->close()) {
	stop('unable to write ' . $zipFile);
}
exec('rm -rf ' . escapeshellarg($build));

// --- core.json ---
$release = array(
	'schema' => 1,
	'version' => $version,
	'download' => 'https://bludit.com/release/' . $name . '.zip',
	'sha256' => hash_file('sha256', $zipFile),
	'php' => $php,
	'date' => gmdate('Y-m-d')
);
$payload = json_encode($release, JSON_UNESCAPED_SLASHES);
$signature = sodium_crypto_sign_detached($payload, $secretKey);
sodium_memzero($secretKey);

$core = array(
	'payload' => base64_encode($payload),
	'signature' => base64_encode($signature)
);
file_put_contents($out . '/core.json', json_encode($core, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);

echo 'Bludit ' . $version . PHP_EOL;
echo 'PRO: ' . (empty($added) ? 'nothing added' : implode(', ', $added)) . PHP_EOL;
echo 'Zip: ' . $zipFile . ' (' . round(filesize($zipFile) / 1048576, 1) . ' MB)' . PHP_EOL;
echo 'sha256: ' . $release['sha256'] . PHP_EOL;
echo 'core.json: ' . $out . '/core.json' . PHP_EOL;
echo PHP_EOL . 'Upload the zip first and core.json last to https://bludit.com/release/' . PHP_EOL;
