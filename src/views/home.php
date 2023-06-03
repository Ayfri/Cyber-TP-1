<?php
declare(strict_types=1);

$title = 'Home';
$js = ['home.js'];

ob_start();

global $user;
?>
	<h1>Welcome <?= $user->email ?></h1>
	<button id='disconnect'>Disconnect</button>
<?php
require_once 'layout.php';
