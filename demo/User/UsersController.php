<?php
namespace PRaptorDemo\User;

use PRaptor\Result\Result;
use PRaptor\Result\Results;

/**
 * @public-access
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
     * @get('path' => '/{id\d}')
     *
     * @param int $id 
     * @param string $name
     * @return Result
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
     * @return Result
     */
    public function fakeLogin($name)
    {
        $_SESSION['loggedInUser'] = $name;
        return Results::http("$name logged in!");
    }
}