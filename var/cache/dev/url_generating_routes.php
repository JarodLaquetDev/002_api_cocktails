<?php

// This file has been auto-generated by the Symfony Routing Component.

return [
    '_preview_error' => [['code', '_format'], ['_controller' => 'error_controller::preview', '_format' => 'html'], ['code' => '\\d+'], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '\\d+', 'code', true], ['text', '/_error']], [], [], []],
    'app_ingredient' => [[], ['_controller' => 'App\\Controller\\IngredientController::index'], [], [['text', '/ingredient']], [], [], []],
    'ingredients.getAll' => [[], ['_controller' => 'App\\Controller\\IngredientController::getAllIngredient'], [], [['text', '/api/ingredients']], [], [], []],
    'ingredient.get' => [['idIngredient'], ['_controller' => 'App\\Controller\\IngredientController::getIngredient'], [], [['variable', '/', '[^/]++', 'idIngredient', true], ['text', '/api/ingredient']], [], [], []],
    'ingredient.delete' => [['idIngredient'], ['_controller' => 'App\\Controller\\IngredientController::deleteIngredient'], [], [['variable', '/', '[^/]++', 'idIngredient', true], ['text', '/api/ingredient']], [], [], []],
    'ingredient.create' => [[], ['_controller' => 'App\\Controller\\IngredientController::createIngredient'], [], [['text', '/api/ingredient']], [], [], []],
    'ingredient.update' => [['id'], ['_controller' => 'App\\Controller\\IngredientController::updateIngredient'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/ingredient']], [], [], []],
    'app_instruction' => [[], ['_controller' => 'App\\Controller\\InstructionController::index'], [], [['text', '/instruction']], [], [], []],
    'instructions.getAll' => [[], ['_controller' => 'App\\Controller\\InstructionController::getAllInstructions'], [], [['text', '/api/instructions']], [], [], []],
    'instruction.get' => [['idInstruction'], ['_controller' => 'App\\Controller\\InstructionController::getInstruction'], [], [['variable', '/', '[^/]++', 'idInstruction', true], ['text', '/api/instruction']], [], [], []],
    'instruction.delete' => [['idInstruction'], ['_controller' => 'App\\Controller\\InstructionController::deleteInstruction'], [], [['variable', '/', '[^/]++', 'idInstruction', true], ['text', '/api/instruction']], [], [], []],
    'instruction.create' => [[], ['_controller' => 'App\\Controller\\InstructionController::createInstruction'], [], [['text', '/api/instruction']], [], [], []],
    'instruction.update' => [['id'], ['_controller' => 'App\\Controller\\InstructionController::updateIngredient'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/instruction']], [], [], []],
    'app_picture' => [[], ['_controller' => 'App\\Controller\\PictureController::index'], [], [['text', '/picture']], [], [], []],
    'picture.getAll' => [[], ['_controller' => 'App\\Controller\\PictureController::getAllPictures'], [], [['text', '/api/pictures']], [], [], []],
    'picture.get' => [['idPicture'], ['_controller' => 'App\\Controller\\PictureController::getPicture'], [], [['variable', '/', '[^/]++', 'idPicture', true], ['text', '/api/picture']], [], [], []],
    'picture.delete' => [['idPicture'], ['_controller' => 'App\\Controller\\PictureController::deletePicture'], [], [['variable', '/', '[^/]++', 'idPicture', true], ['text', '/api/picture']], [], [], []],
    'picture.create' => [[], ['_controller' => 'App\\Controller\\PictureController::createPicture'], [], [['text', '/api/picture']], [], [], []],
    'picture.update' => [['idPicture'], ['_controller' => 'App\\Controller\\PictureController::updatePicture'], [], [['variable', '/', '[^/]++', 'idPicture', true], ['text', '/api/picture']], [], [], []],
    'app_recette' => [[], ['_controller' => 'App\\Controller\\RecetteController::index'], [], [['text', '/recette']], [], [], []],
    'recette.getAll' => [[], ['_controller' => 'App\\Controller\\RecetteController::getAllRecettes'], [], [['text', '/api/recettes']], [], [], []],
    'recette.get' => [['idRecette'], ['_controller' => 'App\\Controller\\RecetteController::getRecette'], [], [['variable', '/', '[^/]++', 'idRecette', true], ['text', '/api/recette']], [], [], []],
    'recette.delete' => [['idRecette'], ['_controller' => 'App\\Controller\\RecetteController::deleteRecette'], [], [['variable', '/', '[^/]++', 'idRecette', true], ['text', '/api/recette']], [], [], []],
    'recette.create' => [[], ['_controller' => 'App\\Controller\\RecetteController::createRecette'], [], [['text', '/api/recette']], [], [], []],
    'recette.update' => [['id'], ['_controller' => 'App\\Controller\\RecetteController::updateRecette'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/recette']], [], [], []],
    'recette.getByIngredient' => [['name'], ['_controller' => 'App\\Controller\\RecetteController::getRecetteByIngredient'], [], [['variable', '/', '[^/]++', 'name', true], ['text', '/api/recette/ingredient']], [], [], []],
    'app_user' => [[], ['_controller' => 'App\\Controller\\UserController::index'], [], [['text', '/user']], [], [], []],
    'user.getAll' => [[], ['_controller' => 'App\\Controller\\UserController::getAllUsers'], [], [['text', '/api/users']], [], [], []],
    'users.get' => [['idUser'], ['_controller' => 'App\\Controller\\UserController::getUserById'], [], [['variable', '/', '[^/]++', 'idUser', true], ['text', '/api/user']], [], [], []],
    'user.delete' => [['idUser'], ['_controller' => 'App\\Controller\\UserController::deleteUser'], [], [['variable', '/', '[^/]++', 'idUser', true], ['text', '/api/user']], [], [], []],
    'user.create' => [[], ['_controller' => 'App\\Controller\\UserController::createUser'], [], [['text', '/api/user']], [], [], []],
    'user.update' => [['id'], ['_controller' => 'App\\Controller\\UserController::updateUser'], [], [['variable', '/', '[^/]++', 'id', true], ['text', '/api/user']], [], [], []],
    'api_login_check' => [[], [], [], [['text', '/api/login_check']], [], [], []],
];
