<?php

// src/AppBundle/Routing/CategoryLoader.php

namespace App\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

//use Closas\ShopBundle\Controller\CategoryController;

class CategoryLoader extends Loader {

    private $loaded = false;

    public function load($resource, $type = null) {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();


        // prepare a new route
        $path = '/category/{parameter}';
        $defaults = array(
            '_controller' => 'Closas/ShopBundle:Category:category',
        );
        $requirements = array(
        );

        $route = new Route($path, $defaults, $requirements);

        // add the new route to the route collection
        $routeName = 'categoryRoute';
        $routes->add($routeName, $route);

        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, $type = null) {
        return 'category' === $type;
    }

}
