<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerKOpoCQT\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerKOpoCQT/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerKOpoCQT.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerKOpoCQT\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerKOpoCQT\App_KernelDevDebugContainer([
    'container.build_hash' => 'KOpoCQT',
    'container.build_id' => 'a878cd5a',
    'container.build_time' => 1668812538,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerKOpoCQT');
