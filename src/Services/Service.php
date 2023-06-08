<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AccountAuthorizationRepository;
use App\Repositories\UserRepository;
use RuntimeException;
use function App\Utils\log_message;

abstract class Service {
	protected AccountAuthorizationRepository $accountAuthorizationRepository;
	protected array $data = [];
	protected bool $requiresAuth;
	protected UserRepository $userRepository;

	public function __construct(bool $requires_auth = true) {
		$this->requiresAuth = $requires_auth;
		$this->accountAuthorizationRepository = new AccountAuthorizationRepository();
		$this->userRepository = new UserRepository();
	}

	public static function isHandledRoute(): bool {
		$route = $_SERVER['REQUEST_URI'];
		return in_array($route, static::routes(), true);
	}

	public static function onRouteGet(string $route): bool {
		return $_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === $route;
	}

	public static function onRoutePost(string $route): bool {
		return $_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === $route;
	}

	abstract public static function routes(): array;

	protected static function redirect(string $url): never {
		header("Location: $url");
		exit();
	}

	protected static function sendError(string $message, int $code = 400): never {
		http_response_code($code);
		echo $message;
		exit();
	}

	protected static function sendResponse(string $response, int $code = 200): never {
		http_response_code($code);
		echo $response;
		exit();
	}

	protected static function sendSuccess(int $code = 200): never {
		http_response_code($code);
		exit();
	}

	public function handle(): never {
		log_message('Handling request from service: ' . basename(static::class));
		if ($this->requiresAuth) {
			$this->checkAuth();
			$this->checkAuthorization();
		}

		$this->data = [...$_POST, ...$_GET];
		$this->handleRoutes();
	}

	protected function checkAuth(): void {
		$session_present = isset($_SESSION['guid']);
		if (!$session_present) {
			static::redirect('/login');
		}

		global $user;
		$user = $this->userRepository->getUserByGUID($_SESSION['guid']);
		$user_exists = $user !== null;
		if (!$user_exists) {
			static::redirect('/login');
		}
	}

	protected function getRequiredParam(string $param, ?string $error = null): string {
		$error_message = $error ?? "Missing required parameter '$param'.";
		if (!isset($this->data[$param])) {
			static::sendError($error_message);
		}

		return $this->data[$param];
	}

	abstract protected function handleRoutes(): never;

	protected function render(string $view, array $data = []): void {
		$view_path = __DIR__ . "/../views/$view.php";
		if (!file_exists($view_path)) {
			throw new RuntimeException("View $view not found");
		}
		extract($data);
		require_once $view_path;
		exit();
	}

	protected function renderOnGet(string $route, ?string $view = null, array $data = []): void {
		$view ??= $route;
		if (static::onRouteGet($route)) {
			$this->render($view, $data);
		}
	}

	private function checkAuthorization(): void {
		$guid = $_SESSION['guid'];
		$authorized = $this->accountAuthorizationRepository->isPublicAuthorization($guid);
		if (!$authorized) {
			$this->render('unauthorized');
		}
	}
}
