<?php
declare(strict_types=1);

namespace App\Services;

use JetBrains\PhpStorm\NoReturn;
use RuntimeException;

abstract class Service {
	protected array $data = [];
	protected bool $requiresAuth;

	public function __construct(bool $requires_auth = true) {
		$this->requiresAuth = $requires_auth;
	}

	public static function isHandledRoute(): bool {
		$route = $_SERVER['REQUEST_URI'];
		return in_array($route, static::routes(), true);
	}

	abstract public static function routes(): array;

	#[NoReturn]
	public function handle(): void {
		if ($this->requiresAuth) {
			$this->checkAuth();
		}
		$this->data = [...$_POST, ...$_GET];
		$this->handleRoutes();
	}

	private function checkAuth(): void {
		if (!isset($_SESSION['user'])) {
			$this->redirect('/login');
		}
	}

	#[NoReturn]
	protected function redirect(string $url): void {
		header("Location: $url");
		exit;
	}

	#[NoReturn]
	abstract protected function handleRoutes(): void;

	public function onRoutePost(string $route): bool {
		return $_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === $route;
	}

	protected function getRequiredParam(string $param, ?string $error = null): string {
		$error_message = $error ?? "Missing required parameter '$param'.";
		if (!isset($this->data[$param])) {
			$this->sendError($error_message);
		}

		return $this->data[$param];
	}

	#[NoReturn]
	protected function sendError(string $message): void {
		http_response_code(400);
		echo $message;
		exit();
	}

	protected function renderOnGet(string $route, ?string $view = null, array $data = []): void {
		$view ??= $route;
		if ($this->onRouteGet($route)) {
			$this->render($view, $data);
		}
	}

	public function onRouteGet(string $route): bool {
		return $_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === $route;
	}

	protected function render(string $view, array $data = []): void {
		$view_path = __DIR__ . "/../views/$view.php";
		if (!file_exists($view_path)) {
			throw new RuntimeException("View $view not found");
		}
		extract($data);
		require_once $view_path;
	}

	#[NoReturn]
	protected function sendResponse(array $data): void {
		header('Content-Type: application/json');
		echo json_encode($data, JSON_THROW_ON_ERROR);
		exit();
	}

	#[NoReturn]
	protected function sendSuccess(): void {
		http_response_code(200);
		exit();
	}
}