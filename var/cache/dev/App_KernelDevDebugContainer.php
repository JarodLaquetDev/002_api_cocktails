<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerOtnt9GO\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerOtnt9GO/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerOtnt9GO.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerOtnt9GO\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerOtnt9GO\App_KernelDevDebugContainer([
    'container.build_hash' => 'Otnt9GO',
    'container.build_id' => '7247a966',
    'container.build_time' => 1668518079,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerOtnt9GO');
