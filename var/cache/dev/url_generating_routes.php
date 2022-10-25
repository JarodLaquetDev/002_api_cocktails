<?php

// This file has been auto-generated by the Symfony Routing Component.

return [
    '_preview_error' => [['code', '_format'], ['_controller' => 'error_controller::preview', '_format' => 'html'], ['code' => '\\d+'], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '\\d+', 'code', true], ['text', '/_error']], [], [], []],
    'app_ingredient' => [[], ['_controller' => 'App\\Controller\\IngredientController::index'], [], [['text', '/ingredient']], [], [], []],
    'ingredients.getAll' => [[], ['_controller' => 'App\\Controller\\IngredientController::getAllIngredient'], [], [['text', '/api/ingredients']], [], [], []],
    'ingredient.get' => [['idIngredient'], ['_controller' => 'App\\Controller\\IngredientController::getIngredient'], [], [['variable', '/', '[^/]++', 'idIngredient', true], ['text', '/api/ingredients']], [], [], []],
    'ingredient.delete' => [['idIngredient'], ['_controller' => 'App\\Controller\\IngredientController::deleteIngredient'], [], [['variable', '/', '[^/]++', 'idIngredient', true], ['text', '/api/ingredients']], [], [], []],
    'ingredient.create' => [[], ['_controller' => 'App\\Controller\\IngredientController::createIngredient'], [], [['text', '/api/ingredients']], [], [], []],
    'ingredient.update' => [['id'], ['_controller' => 'App\\Controller\\IngredientController::updateIngredient'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/ingredients']], [], [], []],
    'app_picture' => [[], ['_controller' => 'App\\Controller\\PictureController::index'], [], [['text', '/picture']], [], [], []],
    'picture.get' => [['idPicture'], ['_controller' => 'App\\Controller\\PictureController::getPicture'], [], [['variable', '/', '[^/]++', 'idPicture', true], ['text', '/api/picture']], [], [], []],
    'picture.create' => [[], ['_controller' => 'App\\Controller\\PictureController::createPicture'], [], [['text', '/api/picture']], [], [], []],
    'app_recette' => [[], ['_controller' => 'App\\Controller\\RecetteController::index'], [], [['text', '/recette']], [], [], []],
    'recette.getAll' => [[], ['_controller' => 'App\\Controller\\RecetteController::getAllRecettes'], [], [['text', '/api/recettes']], [], [], []],
    'recette.get' => [['idRecette'], ['_controller' => 'App\\Controller\\RecetteController::getRecette'], [], [['variable', '/', '[^/]++', 'idRecette', true], ['text', '/api/recette']], [], [], []],
    'recette.delete' => [['idRecette'], ['_controller' => 'App\\Controller\\RecetteController::deleteRecette'], [], [['variable', '/', '[^/]++', 'idRecette', true], ['text', '/api/recette']], [], [], []],
    'recette.create' => [[], ['_controller' => 'App\\Controller\\RecetteController::createRecette'], [], [['text', '/api/recette']], [], [], []],
    'recette.update' => [['id'], ['_controller' => 'App\\Controller\\RecetteController::updateRecette'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/recette']], [], [], []],
    'recette.getByIngredient' => [['idRecette'], ['_controller' => 'App\\Controller\\RecetteController::getRecetteByIngredient'], [], [['variable', '/', '[^/]++', 'idRecette', true], ['text', '/api/recette']], [], [], []],
    'api_login_check' => [[], [], [], [['text', '/api/login_check']], [], [], []],
];
