<?php
namespace PRaptorDemo\User;

use PRaptor\Router\Result\HttpResult;
use PRaptor\Router\Result\JsonResult;
use PRaptor\Router\Result\Results;

/**
 * @controller('path' => '/users')
 */
class UsersController
{
    /**
     * @var string
     */
    private $loggedInUser;

    /**
     * UsersController constructor.
     * @param string $loggedInUser
     */
    public function __construct($loggedInUser = 'Piro')
    {
        $this->loggedInUser = $loggedInUser;
    }

    /**
     * @get()
     */
    public function listUsers()
    {
        return Results::template('User/users-page.html.twig', array(
            'name' => $this->loggedInUser
        ));
    }

    /**
     * @get('path' => '/{id\d}')
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


    /**
     * @get('path' => '/fake-login')
     * @param string $name
     * @return HttpResult
     */
    public function fakeLogin($name)
    {
        $_SESSION['loggedInUser'] = $name;
        return Results::http("$name logged in!");
    }
}