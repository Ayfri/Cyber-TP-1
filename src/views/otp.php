<?php
declare(strict_types=1);
$title = 'OTP';
$js = ['otp.js'];

ob_start();
?>
	<h1>OTP</h1>
	<code id='otp'>
		<?= $otp ?? '' ?>
	</code>
	<p class='hint'> Click to copy </p>
<?php
require_once 'layout.php';
