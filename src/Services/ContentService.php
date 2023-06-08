<?php
declare(strict_types=1);

namespace App\Services;

class ContentService extends Service {
	protected function handleRoutes(): never {
		$this->renderOnGet('/', 'home');

		Service::sendError('Not found.', 404);
	}

	public static function routes(): array {
		return ['/'];
	}
}
