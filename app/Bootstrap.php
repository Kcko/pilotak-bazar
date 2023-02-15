<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;
use Tracy;


class Bootstrap
{
	/**
	 * Is page loaded over https?
	 * @var bool
	 */
	public static $isSecure = false;

	/**
	 * DSN for sentry error log
	 * @var string
	 */
	public static $sentryDsn = null;

	/**
	 * Start application in debug mode?
	 * @var boolean
	 */
	public static $debugMode = false;

	/**
	 * Enviroment identifier (project domain | cli | gitlab branche)
	 * @var mixed
	 */
	private $envIdent;

	/**
	 * Config section identification
	 * @var mixed
	 */
	public static $configSection = 'prod';

	/**
	 * Storage directory path
	 * @var string
	 */
	public static $storageDir = __DIR__ . '/../www/storage';

	/**
	 * Storage directory path
	 * @var string
	 */
	public static $tempDir = __DIR__ . '/../temp';

	/**
	 * E-mail address for error reporting
	 * @var string
	 */
	public static $debugEmail = 'admin@rjwebdesign.cz';

	/**
	 * Debug mode by envirments
	 * @var array
	 */
	public static $debugModeSetup = [
		'pilotak-bazar' => TRUE,
		'*.dev71.andweb.cz' => TRUE,
		'*.test71.andweb.cz' => TRUE,
		'*.local' => TRUE,
		'nette-bootstrap' => TRUE,
		'GITLAB_CI-*' => TRUE,
		'cli' => TRUE,
	];

	/**
	 * Config sections by envirments
	 * @var array
	 */
	public static $configSectionSetup = [
		'pilotak-bazar' => 'dev',
		'*.dev71.andweb.cz' => 'dev',
		'*.test71.andweb.cz' => 'preprod', // 'dev'
		'*.local' => 'dev',
		'*.pripravujeme.eu' => 'preprod',
		'GITLAB_CI-www' => 'prod',
		'GITLAB_CI-*' => 'preprod',
		//'cli'				 => 'prod',
		'cli' => 'dev',
	];

	/**
	 * Storage dir placement by enviroment
	 * @var array
	 */
	public static $storageDirSetup = [
		//	'*.dev.andweb.cz' => 'http://xclusiv-ink.pripravujeme.eu/storage',
		//	'*.dev.andweb.cz' => 'http://xclusiv-ink.pripravujeme.eu/storage',
		//'*.dev7.andweb.cz' => 'https://masoprofit.cz/storage',   // pro obrazky z ostreho ..
		//	'*.local' => 'https://masoprofit.cz/storage',
	];

	public function __construct()
	{
		$this->envIdent = isset($_SERVER['HTTP_HOST'])
			? $_SERVER['HTTP_HOST']
			: (
				isset($_SERVER['GITLAB_CI'])
				? 'GITLAB_CI-' . $_SERVER['CI_COMMIT_REF_NAME']
				: (PHP_SAPI === 'cli' ? 'cli' : 'localhost')
			);
	}


	public function boot(): Configurator
	{
		$this->checkSecure();

		$configurator = new Configurator;
		$configurator->setTempDirectory(self::$tempDir);


		self::$debugMode = $this->getForEnv(self::$debugModeSetup, self::$debugMode);
		self::$configSection = $this->getForEnv(self::$configSectionSetup, self::$configSection);
		self::$storageDir = $this->getForEnv(self::$storageDirSetup, self::$storageDir);

		define('STORAGE_DIR', self::$storageDir);
		define('TMP_DIR', self::$tempDir);

		$this->initDebugger($configurator);
		$this->initSentry();

		$robotLoader = $configurator->createRobotLoader();
		$robotLoader->addDirectory(__DIR__);

		$configurator->addConfig(__DIR__ . '/config/back.local.neon');
		$configurator->addConfig(__DIR__ . '/config/config.local.neon');
		$configurator->addConfig(__DIR__ . '/config/config.' . self::$configSection . '.neon');
		$configurator->addConfig(__DIR__ . '/config/database.' . self::$configSection . '.neon');

		$robotLoader->register();

		return $configurator;
	}

	/*
	public function bootForTests(): Configurator
	{
	$configurator = self::boot();
	\Tester\Environment::setup();
	return $configurator;
	}
	*/
	private function checkSecure()
	{
		// HTTPS behind proxy detection
		if (
			(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			|| (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
			|| (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
		) {
			$_SERVER['HTTPS'] = 'on';
			$_SERVER['SERVER_PORT'] = 443;
			self::$isSecure = true;
		}
	}

	private function initDebugger(Configurator $configurator)
	{
		if (self::$debugMode) {
			Tracy\Debugger::$maxDepth = 5;
			$configurator->setDebugMode(self::$debugMode);
			$configurator->enableDebugger(__DIR__ . '/../log');
		} else {
			$configurator->enableDebugger(__DIR__ . '/../log', self::$debugEmail);
		}
	}

	private function initSentry()
	{
		if (self::$sentryDsn) {
			new \Salamek\RavenNette\SentryLogger(
					self::$sentryDsn, //Sentry DSN
				false, //Log in DEBUG mode ? //You dont want that...
					Tracy\Debugger::$logDirectory, //Set where do you want to store file log (Tracy\Debugger::$logDirectory | null | string)
					Tracy\Debugger::$email //Send email as usual logger ?   (Tracy\Debugger::$email | null | string | array )
			);
		}
	}

	private function getForEnv(array $setup, $default)
	{
		foreach ($setup as $domain => $val) {
			if (fnmatch($domain, $this->envIdent)) {
				return $val;
			}
		}

		return $default;
	}
}