<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerX4aeNxe\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerX4aeNxe/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerX4aeNxe.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerX4aeNxe\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerX4aeNxe\App_KernelDevDebugContainer([
    'container.build_hash' => 'X4aeNxe',
    'container.build_id' => '08672e5d',
    'container.build_time' => 1668364440,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerX4aeNxe');
