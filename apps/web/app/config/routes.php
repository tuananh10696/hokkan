<?php

/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;
use Cake\Core\Configure;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */


Router::defaultRouteClass(DashedRoute::class);

Router::extensions(['json']);
Router::scope('/', function (RouteBuilder $routes) {
	/**
	 * Here, we are connecting '/' (base path) to a controller called 'Pages',
	 * its action called 'display', and we pass a param to select the view file
	 * to use (in this case, src/Template/Pages/home.ctp)...
	 */

	$routes->connect('/index', ['controller' => 'Contents', 'action' => 'index', 'sitehome']);

        $routes->connect('/info', ['controller' => 'News', 'action' => 'index']);
	$routes->connect('/info/:id', ['controller' => 'News', 'action' => 'detail'])
			->setPatterns(['id' => '[1-9]?[0-9]+'])
			->setPass(['id']);

	$routes->connect('/admin', ['controller' => 'Admin', 'action' => 'index', 'prefix' => 'admin']);
	$routes->connect('/admin/logout', ['controller' => 'Admin', 'action' => 'logout', 'prefix' => 'admin']);

	// user
	$routes->connect('/user', ['controller' => 'Users', 'action' => 'index', 'prefix' => 'user']);
	$routes->connect('/user/logout', ['controller' => 'Users', 'action' => 'logout', 'prefix' => 'user']);


	if (Configure::read('Contents.enabledJson')) {
		$routes->connect('/:id', ['controller' => 'Contents', 'action' => 'home'])
			->setPatterns(['id' => '[1-9]?[0-9]+\.html'])
			->setPass(['id']);
		// user infos
		// $exclusive = '[^(admin)(user)(user\-regist)]+';
		$exclusive = '[a-z][a-zA-Z0-9_\-]+';
		$routes->connect('/:site_slug/:slug/index', ['controller' => 'Contents', 'action' => 'index'])
			->setPatterns(['slug' => $exclusive, 'site_slug' => $exclusive])
			->setPass(['site_slug', 'slug']);

		// preview
		$routes->connect('/:site_slug/:id', ['controller' => 'Contents', 'action' => 'previewHome'])
			->setPatterns(['site_slug' => $exclusive, 'id' => 'pre-[1-9]?[0-9]+\.html'])
			->setPass(['site_slug', 'id']);

		$routes->connect('/:site_slug/:slug/:id', ['controller' => 'Contents', 'action' => 'preview'])
			->setPatterns(['slug' => $exclusive, 'site_slug' => $exclusive, 'id' => 'pre-[1-9]?[0-9]+\.html'])
			->setPass(['site_slug', 'slug', 'id']);

		// detail
		$routes->connect('/:site_slug/:slug/:id', ['controller' => 'Contents', 'action' => 'detail'])
			->setPatterns(['slug' => $exclusive, 'site_slug' => $exclusive, 'id' => '[1-9]?[0-9]+\.html'])
			->setPass(['site_slug', 'slug', 'id']);

		$routes->connect('/:site_slug/:slug/jsdata/:api_name', ['controller' => 'Contents', 'action' => 'ajax_data'])
			->setPatterns(['slug' => $exclusive, 'site_slug' => $exclusive])
			->setPass(['site_slug', 'slug', 'api_name']);

		$routes->connect('/:site_slug/jsdata/:api_name', ['controller' => 'Contents', 'action' => 'ajax_data_top'])
			->setPatterns(['site_slug' => $exclusive])
			->setPass(['site_slug', 'api_name']);



		$routes->connect('/:site_slug/:id', ['controller' => 'Contents', 'action' => 'home'])
			->setPatterns(['site_slug' => $exclusive, 'id' => '[1-9]?[0-9]+\.html'])
			->setPass(['site_slug', 'id']);
	} else {
		$exclusive = '[a-z][a-zA-Z0-9_\-]+';

		$routes->connect('/:controller/:id', ['action' => 'detail'])
			->setPatterns(['controller' => $exclusive, 'id' => '[1-9]?[0-9]+'])
			->setPass(['id']);

		$routes->connect('/:controller/preview/:id', ['action' => 'preview'])
			->setPatterns(['controller' => $exclusive, 'id' => '[1-9]?[0-9]+'])
			->setPass(['id']);

		/* この下から必要に応じて設定する */
		$routes->connect('/column/', ['controller' => 'columns', 'action' => 'index'])
			->setPatterns(['controller' => 'columns', 'id' => '[1-9]?[0-9]+'])
			->setPass(['id']);
		$routes->connect('/column/:id', ['controller' => 'columns', 'action' => 'detail'])
			->setPatterns(['id' => '[1-9]?[0-9]+'])
			->setPass(['id']);
		$routes->connect('/column/preview/:id', ['controller' => 'columns', 'action' => 'preview'])
			->setPatterns(['id' => '[1-9]?[0-9]+'])
			->setPass(['id']);

		/* ----------------------- */
	}

	// API
	$routes->connect('/:site_slug/v1/:controller/:action', ['prefix' => 'v1'])
		->setPass(['site_slug']);

	/**
	 * ...and connect the rest of 'Pages' controller's URLs.
	 */
	$routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

	$routes->connect('/', ['controller' => 'Homes', 'action' => 'index']);
	/**
	 * Connect catchall routes for all controllers.
	 *
	 * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
	 *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
	 *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
	 *
	 * Any route class can be used with this method, such as:
	 * - DashedRoute
	 * - InflectedRoute
	 * - Route
	 * - Or your own route class
	 *
	 * You can remove these routes once you've connected the
	 * routes you want in your application.
	 */
	$routes->fallbacks(DashedRoute::class);
});

Router::prefix('admin', function ($routes) {

	// $routes->connect('/', ['plugin' => 'Admin','controller' => 'admin', 'action' => 'index']);
	// $routes->connect('/', ['controller' => 'admin', 'action' => 'index']);    
	$routes->fallbacks('DashedRoute');
});

Router::prefix('user', function ($routes) {

	// $routes->connect('/', ['plugin' => 'Admin','controller' => 'admin', 'action' => 'index']);
	// $routes->connect('/', ['controller' => 'admin', 'action' => 'index']);    
	$routes->fallbacks('DashedRoute');
});

Router::prefix('v1', function ($routes) {

	// $routes->connect('/v1/:site_slug/:controller', )

	$routes->fallbacks('DashedRoute');
});


/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
