<?php

namespace ContainerTMMKlex;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_WWHc_STService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.WWHc.ST' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.WWHc.ST'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'Ingredient' => ['privates', '.errored..service_locator.WWHc.ST.App\\Entity\\Ingredient', NULL, 'Cannot autowire service ".service_locator.WWHc.ST": it references class "App\\Entity\\Ingredient" but no such service exists.'],
            'serializer' => ['privates', 'serializer', 'getSerializerService', true],
        ], [
            'Ingredient' => 'App\\Entity\\Ingredient',
            'serializer' => '?',
        ]);
    }
}