<?php
declare(strict_types=1);

$title = 'Change Password';
$js = ['change-password.js'];

ob_start();
?>
	<h1>Change password</h1>
	<form id='change-password-form' method='post'>
		<label for='current-password'>Old password</label>
		<input
			autocomplete='current-password'
			id='current-password'
			maxlength='64'
			minlength='8'
			name='current-password'
			required
			type='password'
		>
		<label for='new-password'>New password</label>
		<input
			autocomplete='new-password'
			id='new-password'
			maxlength='64'
			minlength='8'
			name='new-password'
			required
			type='password'
		>
		<label for='confirm-password'>Confirm new password</label>
		<input
			autocomplete='new-password'
			id='confirm-password'
			maxlength='64'
			minlength='8'
			name='confirm-password'
			required
			type='password'
		>
		<button type='submit'>Change password</button>
	</form>
<?php
require_once 'layout.php';
