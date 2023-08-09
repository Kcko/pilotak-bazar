<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\CliRouter,
	App\FrontModule\Routers\NavigationRouter,
	App\FrontModule\Routers\InvalidRequestRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public static function createRouter(NavigationRouter $navigationRouter)
	{
		$router = new RouteList();
		//$secured = $request->isSecured() ? Route::SECURED : 0;

		// admin
		$router[] = new Route('/admin/<presenter>/<action>', [
			'module' => 'Back',
			'presenter' => 'Main',
			'action' => 'default'
		]);

		$router[] = new Route('/storage/images/<size>/<id>.<ext>[/<slug>]', 'Service:Image:getImage');

		// service file downloader
		$router[] = new Route('/file/<id>/<token>', [
			'module' => 'Service',
			'presenter' => 'FileDownloader',
			'action' => 'default',
			'language' => 1,
			'domain' => 1
		]);


		$router[] = new Route('/service/<presenter>/<action>', [
			'module' => 'Service'
		]);

		// sitemap
		$router[] = new Route('/sitemap.xml', [
			'module' => 'Service',
			'presenter' => 'Sitemap',
			'action' => 'default',
		]);


		$router[] = new Route('/api/v1/test', [
			'module' => 'Service',
			'presenter' => 'TestApi',
		]);
		
		$router[] = new Route('/api/v1/test', [
			'module' => 'Service',
			'presenter' => 'TestApi',
		]);

		$router[] = $navigationRouter;
		$router[] = new InvalidRequestRouter();
		
		return $router;
	}
}
