<?php
declare(strict_types=1);

$title = 'Unauthorized';
$js = ['logout-button.js'];

ob_start();
?>
	<h1>Unauthorized</h1>
	<p class='hint'>You are not authorized to access this page.</p>
	<button class='red' id='disconnect'>Disconnect</button>
<?php
require_once 'layout.php';
