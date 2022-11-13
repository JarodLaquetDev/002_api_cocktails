<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/ingredient' => [[['_route' => 'app_ingredient', '_controller' => 'App\\Controller\\IngredientController::index'], null, null, null, false, false, null]],
        '/api/ingredients' => [[['_route' => 'ingredients.getAll', '_controller' => 'App\\Controller\\IngredientController::getAllIngredient'], null, ['GET' => 0], null, false, false, null]],
        '/api/ingredient' => [[['_route' => 'ingredient.create', '_controller' => 'App\\Controller\\IngredientController::createIngredient'], null, ['POST' => 0], null, false, false, null]],
        '/instruction' => [[['_route' => 'app_instruction', '_controller' => 'App\\Controller\\InstructionController::index'], null, null, null, false, false, null]],
        '/api/instructions' => [[['_route' => 'instructions.getAll', '_controller' => 'App\\Controller\\InstructionController::getAllInstructions'], null, ['GET' => 0], null, false, false, null]],
        '/api/instruction' => [[['_route' => 'instruction.create', '_controller' => 'App\\Controller\\InstructionController::createInstruction'], null, ['POST' => 0], null, false, false, null]],
        '/picture' => [[['_route' => 'app_picture', '_controller' => 'App\\Controller\\PictureController::index'], null, null, null, false, false, null]],
        '/api/pictures' => [[['_route' => 'picture.getAll', '_controller' => 'App\\Controller\\PictureController::getAllPictures'], null, null, null, false, false, null]],
        '/api/picture' => [[['_route' => 'picture.create', '_controller' => 'App\\Controller\\PictureController::createPicture'], null, ['POST' => 0], null, false, false, null]],
        '/recette' => [[['_route' => 'app_recette', '_controller' => 'App\\Controller\\RecetteController::index'], null, null, null, false, false, null]],
        '/api/recettes' => [[['_route' => 'recette.getAll', '_controller' => 'App\\Controller\\RecetteController::getAllRecettes'], null, null, null, false, false, null]],
        '/api/recette' => [[['_route' => 'recette.create', '_controller' => 'App\\Controller\\RecetteController::createRecette'], null, ['POST' => 0], null, false, false, null]],
        '/user' => [[['_route' => 'app_user', '_controller' => 'App\\Controller\\UserController::index'], null, null, null, false, false, null]],
        '/api/users' => [[['_route' => 'user.getAll', '_controller' => 'App\\Controller\\UserController::getAllUsers'], null, null, null, false, false, null]],
        '/api/user' => [[['_route' => 'user.create', '_controller' => 'App\\Controller\\UserController::createUser'], null, ['POST' => 0], null, false, false, null]],
        '/api/login_check' => [[['_route' => 'api_login_check'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/(?'
                    .'|in(?'
                        .'|gredient(?'
                            .'|/([^/]++)(?'
                                .'|(*:78)'
                                .'|(*:85)'
                            .')'
                            .'|_(?'
                                .'|recette_(?'
                                    .'|add/([^/]++)(*:120)'
                                    .'|delete/([^/]++)(*:143)'
                                .')'
                                .'|image_(?'
                                    .'|add/([^/]++)(*:173)'
                                    .'|delete/([^/]++)(*:196)'
                                .')'
                            .')'
                        .')'
                        .'|struction(?'
                            .'|/([^/]++)(?'
                                .'|(*:231)'
                                .'|(*:239)'
                            .')'
                            .'|_recette_(?'
                                .'|add/([^/]++)(*:272)'
                                .'|delete/([^/]++)(*:295)'
                            .')'
                        .')'
                    .')'
                    .'|picture/([^/]++)(?'
                        .'|(*:325)'
                    .')'
                    .'|recette(?'
                        .'|/(?'
                            .'|([^/]++)(?'
                                .'|(*:359)'
                                .'|(*:367)'
                            .')'
                            .'|ingredient/([^/]++)(*:395)'
                        .')'
                        .'|_i(?'
                            .'|n(?'
                                .'|gredient_(?'
                                    .'|add/([^/]++)(*:437)'
                                    .'|delete/([^/]++)(*:460)'
                                .')'
                                .'|struction_(?'
                                    .'|add/([^/]++)(*:494)'
                                    .'|delete/([^/]++)(*:517)'
                                .')'
                            .')'
                            .'|mage_(?'
                                .'|add/([^/]++)(*:547)'
                                .'|delete/([^/]++)(*:570)'
                            .')'
                        .')'
                    .')'
                    .'|user/([^/]++)(?'
                        .'|(*:597)'
                        .'|(*:605)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        78 => [
            [['_route' => 'ingredient.get', '_controller' => 'App\\Controller\\IngredientController::getIngredient'], ['idIngredient'], ['GET' => 0], null, false, true, null],
            [['_route' => 'ingredient.delete', '_controller' => 'App\\Controller\\IngredientController::deleteIngredient'], ['idIngredient'], ['DELETE' => 0], null, false, true, null],
        ],
        85 => [[['_route' => 'ingredient.update', '_controller' => 'App\\Controller\\IngredientController::updateIngredient'], ['id'], ['PUT' => 0], null, false, true, null]],
        120 => [[['_route' => 'ingredientRecetteAdd.update', '_controller' => 'App\\Controller\\IngredientController::addRecetteInIngredient'], ['id'], ['PUT' => 0], null, false, true, null]],
        143 => [[['_route' => 'ingredientRecetteDelete.update', '_controller' => 'App\\Controller\\IngredientController::deleteRecetteInIngredient'], ['id'], ['PUT' => 0], null, false, true, null]],
        173 => [[['_route' => 'ingredientPictureAdd.update', '_controller' => 'App\\Controller\\IngredientController::addPictureInIngredient'], ['id'], ['PUT' => 0], null, false, true, null]],
        196 => [[['_route' => 'ingredientPictureDelete.update', '_controller' => 'App\\Controller\\IngredientController::deletePictureInIngredient'], ['id'], ['PUT' => 0], null, false, true, null]],
        231 => [
            [['_route' => 'instruction.get', '_controller' => 'App\\Controller\\InstructionController::getInstruction'], ['idInstruction'], ['GET' => 0], null, false, true, null],
            [['_route' => 'instruction.delete', '_controller' => 'App\\Controller\\InstructionController::deleteInstruction'], ['idInstruction'], ['DELETE' => 0], null, false, true, null],
        ],
        239 => [[['_route' => 'instruction.update', '_controller' => 'App\\Controller\\InstructionController::updateInstruction'], ['id'], ['PUT' => 0], null, false, true, null]],
        272 => [[['_route' => 'instructionRecetteAdd.update', '_controller' => 'App\\Controller\\InstructionController::addRecetteInInstruction'], ['id'], ['PUT' => 0], null, false, true, null]],
        295 => [[['_route' => 'instructionRecetteDelete.update', '_controller' => 'App\\Controller\\InstructionController::deleteRecetteInInstruction'], ['id'], ['PUT' => 0], null, false, true, null]],
        325 => [
            [['_route' => 'picture.get', '_controller' => 'App\\Controller\\PictureController::getPicture'], ['idPicture'], ['GET' => 0], null, false, true, null],
            [['_route' => 'picture.delete', '_controller' => 'App\\Controller\\PictureController::deletePicture'], ['idPicture'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'picture.update', '_controller' => 'App\\Controller\\PictureController::updatePicture'], ['idPicture'], ['PUT' => 0], null, false, true, null],
        ],
        359 => [
            [['_route' => 'recette.get', '_controller' => 'App\\Controller\\RecetteController::getRecette'], ['idRecette'], ['GET' => 0], null, false, true, null],
            [['_route' => 'recette.delete', '_controller' => 'App\\Controller\\RecetteController::deleteRecette'], ['idRecette'], ['DELETE' => 0], null, false, true, null],
        ],
        367 => [[['_route' => 'recette.update', '_controller' => 'App\\Controller\\RecetteController::updateRecette'], ['id'], ['PUT' => 0], null, false, true, null]],
        395 => [[['_route' => 'recette.getByIngredient', '_controller' => 'App\\Controller\\RecetteController::getRecetteByIngredient'], ['name'], ['GET' => 0], null, false, true, null]],
        437 => [[['_route' => 'recetteIngredientAdd.update', '_controller' => 'App\\Controller\\RecetteController::addIngredientInRecette'], ['id'], ['PUT' => 0], null, false, true, null]],
        460 => [[['_route' => 'recetteIngredientDelete.update', '_controller' => 'App\\Controller\\RecetteController::deleteIngredientInRecette'], ['id'], ['PUT' => 0], null, false, true, null]],
        494 => [[['_route' => 'recetteInstructionAdd.update', '_controller' => 'App\\Controller\\RecetteController::addInstructionInRecette'], ['id'], ['PUT' => 0], null, false, true, null]],
        517 => [[['_route' => 'recetteInstructionDelete.update', '_controller' => 'App\\Controller\\RecetteController::deleteInstructionInRecette'], ['id'], ['PUT' => 0], null, false, true, null]],
        547 => [[['_route' => 'recetteImageAdd.update', '_controller' => 'App\\Controller\\RecetteController::addPictureInRecette'], ['id'], ['PUT' => 0], null, false, true, null]],
        570 => [[['_route' => 'recetteImageDelete.update', '_controller' => 'App\\Controller\\RecetteController::deletePictureInRecette'], ['id'], ['PUT' => 0], null, false, true, null]],
        597 => [
            [['_route' => 'users.get', '_controller' => 'App\\Controller\\UserController::getUserById'], ['idUser'], ['GET' => 0], null, false, true, null],
            [['_route' => 'user.delete', '_controller' => 'App\\Controller\\UserController::deleteUser'], ['idUser'], ['DELETE' => 0], null, false, true, null],
        ],
        605 => [
            [['_route' => 'user.update', '_controller' => 'App\\Controller\\UserController::updateUser'], ['id'], ['PUT' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
