<?php
declare(strict_types=1);

$title = 'Home';
$js = ['home.js'];

ob_start();

global $user;
?>
	<h1>Welcome <?= $user->email ?></h1>
	<a href='/change-password' class='no-hover'>
		<button>Change password</button>
	</a>

	<button id='disconnect'>Disconnect</button>
<?php
require_once 'layout.php';
