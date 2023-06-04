<?php
declare(strict_types=1);

$title = 'Delete Account';
$js = ['delete-account.js'];

ob_start();
?>
	<h1>Delete password</h1>
	<form id='delete-account-form' method='post'>
		<label for='password'>Password</label>
		<input
			autocomplete='current-password'
			id='password'
			maxlength='64'
			minlength='8'
			name='password'
			required
			type='password'
		>
		<button type='submit' class='red'>Delete Account</button>
	</form>
<?php
require_once 'layout.php';
