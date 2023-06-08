<?php
declare(strict_types=1);

$title = 'Page not found.';

ob_start();
?>
	<h1>404</h1>
	<p>Page not found.</p>
<?php
require_once 'layout.php';
