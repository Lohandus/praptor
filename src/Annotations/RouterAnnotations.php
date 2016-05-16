<?php
namespace PRaptor\Annotations;

use mindplay\annotations\Annotations;

abstract class RouterAnnotations
{
    public static function register()
    {
        $manager = Annotations::getManager();
        
        $manager->registry['get'] = Get::class;
        $manager->registry['post'] = Post::class;
        $manager->registry['delete'] = Delete::class;
        $manager->registry['controller'] = Controller::class;
    }
}