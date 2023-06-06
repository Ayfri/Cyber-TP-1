<?php
declare(strict_types=1);

$title = 'Verify OTP';
$js = ['otp-verify.js'];

ob_start();
?>
	<form id='otp-verify-form' method="post">
		<label for="otp">OTP</label>
		<input
			class="form-control"
			id="otp"
			max="999999"
			min="100000"
			maxlength='6'
			name="otp"
			placeholder="000000"
			required
			type="number"
		>
		<button type="submit" class="btn btn-primary">Verify</button>
		<button id="cancel" class='red'>Cancel</button>
	</form>
	<p>
		Go to OTP <a href="/otp" target='_blank'>page</a>
	</p>
<?php
require_once 'layout.php';
