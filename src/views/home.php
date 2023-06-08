<?php
declare(strict_types=1);

$title = 'Home';
$js = ['logout-button.js'];

ob_start();

global $user;
?>
	<h1>Welcome <?= $user->email ?></h1>
	<a href='/change-password' class='no-hover'>
		<button>Change password</button>
	</a>

	<button id='disconnect'>Disconnect</button>

	<a href='/delete-account' class='no-hover'>
		<button class='red'>Delete account</button>
	</a>
<?php
require_once 'layout.php';
