<?php

namespace ContainerTMMKlex;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_FlinONaService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.FlinONa' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.FlinONa'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'IngredientRepository' => ['privates', 'App\\Repository\\IngredientRepository', 'getIngredientRepositoryService', true],
            'entityManager' => ['services', 'doctrine.orm.default_entity_manager', 'getDoctrine_Orm_DefaultEntityManagerService', true],
            'serializer' => ['privates', 'serializer', 'getSerializerService', true],
            'urlGenerator' => ['services', 'router', 'getRouterService', false],
            'validator' => ['privates', 'validator', 'getValidatorService', true],
        ], [
            'IngredientRepository' => 'App\\Repository\\IngredientRepository',
            'entityManager' => '?',
            'serializer' => '?',
            'urlGenerator' => '?',
            'validator' => '?',
        ]);
    }
}