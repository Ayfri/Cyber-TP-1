<?php
declare(strict_types=1);
$title = 'Login';
ob_start();
?>
	<h1>Login</h1>
	<form action="/login" id="login-form" method="post">
		<label for="email">Email</label>
		<input
			autocomplete='password'
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
			autocomplete='password'
			id='password'
			name='password'
			minlength='8'
			maxlength='64'
			required
			type='password'
		>
		<button type="submit">Login</button>
	</form>
<?php
require_once 'layout.php';
