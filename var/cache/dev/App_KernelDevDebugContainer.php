<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerX42ZN7T\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerX42ZN7T/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerX42ZN7T.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerX42ZN7T\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerX42ZN7T\App_KernelDevDebugContainer([
    'container.build_hash' => 'X42ZN7T',
    'container.build_id' => '27080a84',
    'container.build_time' => 1668357058,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerX42ZN7T');
