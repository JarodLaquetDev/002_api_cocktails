<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container7NAU9jA\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container7NAU9jA/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container7NAU9jA.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container7NAU9jA\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \Container7NAU9jA\App_KernelDevDebugContainer([
    'container.build_hash' => '7NAU9jA',
    'container.build_id' => '1984d8eb',
    'container.build_time' => 1668438038,
], __DIR__.\DIRECTORY_SEPARATOR.'Container7NAU9jA');
