<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerEOB9ZwF\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerEOB9ZwF/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerEOB9ZwF.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerEOB9ZwF\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerEOB9ZwF\App_KernelDevDebugContainer([
    'container.build_hash' => 'EOB9ZwF',
    'container.build_id' => '23027bb3',
    'container.build_time' => 1668440198,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerEOB9ZwF');
