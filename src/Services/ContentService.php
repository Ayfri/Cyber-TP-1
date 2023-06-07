<?php
declare(strict_types=1);

namespace App\Services;

use JetBrains\PhpStorm\NoReturn;

class ContentService extends Service {
	public static function routes(): array {
		return ['/'];
	}

	#[NoReturn]
	protected function handleRoutes(): never {
		$this->renderOnGet('/', 'home');

		Service::sendError('Not found.', 404);
	}
}
