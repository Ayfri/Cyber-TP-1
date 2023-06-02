<?php
declare(strict_types=1);
$content = ob_get_clean();
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title><?= $title ?? '' ?></title>
		<?php
		$css ??= [];
		$css[] = 'main.css';
		foreach ($css as $css_file) { ?>
			<link rel="stylesheet" href="../../static/styles/<?= $css_file ?>">
			<?php
		}

		if (isset($js)) {
			foreach ($js as $js_file) { ?>
				<script defer type="module" src="../../static/scripts/<?= $js_file ?>"></script>
				<?php
			}
		}
		?>
	</head>
	<body>
		<?= $content ?>
	</body>
</html>
