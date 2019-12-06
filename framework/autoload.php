<?php


function autoload($classname)
{
    $folders = ['model/', 'framework/', 'controller/'];

    foreach ($folders as $folder) {
        $classpath = $folder . $classname . '.php';

        if (file_exists($classpath)) {
            require $classpath;
        }

    }
}

spl_autoload_register('autoload');
