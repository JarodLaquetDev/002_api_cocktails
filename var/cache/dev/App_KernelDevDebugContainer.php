<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerCLhMX1X\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerCLhMX1X/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerCLhMX1X.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerCLhMX1X\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerCLhMX1X\App_KernelDevDebugContainer([
    'container.build_hash' => 'CLhMX1X',
    'container.build_id' => 'dc975d8f',
    'container.build_time' => 1668510365,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerCLhMX1X');
