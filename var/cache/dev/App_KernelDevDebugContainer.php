<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerOMIRtO9\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerOMIRtO9/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerOMIRtO9.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerOMIRtO9\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerOMIRtO9\App_KernelDevDebugContainer([
    'container.build_hash' => 'OMIRtO9',
    'container.build_id' => '0517495b',
    'container.build_time' => 1666709400,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerOMIRtO9');
