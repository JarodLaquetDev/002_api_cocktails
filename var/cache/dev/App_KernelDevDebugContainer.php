<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerUahxTeF\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerUahxTeF/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerUahxTeF.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerUahxTeF\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerUahxTeF\App_KernelDevDebugContainer([
    'container.build_hash' => 'UahxTeF',
    'container.build_id' => '30b15d1b',
    'container.build_time' => 1668419725,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerUahxTeF');
