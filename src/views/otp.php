<?php
declare(strict_types=1);
$title = 'OTP';
$js = ['otp.js'];

ob_start();
?>
	<h1>OTP</h1>
	<code id='otp'><?= $otp ?></code>
<?php
require_once 'layout.php';
