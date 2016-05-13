<?php
namespace PRaptor\User;

use PRaptor\Router\Result\JsonResult;
use PRaptor\Router\Result\Results;
use Twig_Environment;

/**
 * @controller('path' => '/users')
 */
class UsersController
{
    /**
     * @get()
     */
    public function listUsers()
    {
        return Results::template('User/users.html', array(
            'name' => 'Fabien'
        ));
    }

    /**
     * @get('path' => '/{id}')
     *
     * @param int $id 
     * @param string $name
     * @return JsonResult
     */
    public function getUser($id, $name = 'Jones')
    {
        $data = [
            'id' => $id,
            'name' => "Jose $name"
        ];
        
        return Results::json($data);
    }
}