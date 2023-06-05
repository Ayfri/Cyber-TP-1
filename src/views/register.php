<?php
declare(strict_types=1);

$title = 'Register';
$js = ['register.js'];

ob_start();
?>
	<h1>Register</h1>
	<form action="/register" id="register-form" method="post">
		<label for="email">Email</label>
		<input
			autocomplete='email'
			id='email'
			maxlength='312'
			minlength='3'
			name='email'
			pattern='[^@\s]+@[^@\s]+\.[^@\s]+'
			required
			type='email'
		>
		<label for="password">Password</label>
		<input
			autocomplete='new-password'
			id='password'
			name='password'
			minlength='8'
			maxlength='64'
			required
			type='password'
		>
		<label for='confirm-password'>Confirm Password</label>
		<input
			autocomplete='new-password'
			id='confirm-password'
			name='confirm-password'
			minlength='8'
			maxlength='64'
			required
			type='password'
		>
		<button type="submit">Register</button>
	</form>
	<p>
		Already have an account? <a href="/login">Login</a>
	</p>
<?php
require_once 'layout.php';
