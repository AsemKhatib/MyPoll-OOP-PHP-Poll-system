<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;

/**
 * Class Users
 *
 * @package MyPoll\Classes
 */
class Users
{
    /** @var  \Twig_Environment */
    protected $twig;

    /** @var  Settings */
    protected $settingsObj;

    /** @var int  */
    protected $maxResults;

    /**
     * @param object $twig
     * @param object $settingsObj
     */
    public function __construct($twig, $settingsObj)
    {
        $this->twig = $twig;
        $this->settingsObj = $settingsObj;
        $this->maxResults = $this->settingsObj->getResultNumber();
    }

    /**
     * @param string $user
     * @param string $password
     * @param string $email
     *
     * @return void
     */
    public function addExecute($user, $password, $email)
    {
        try {
            if ($this->checkIsExist($user, $email) == false && $email) {
                $newUser = Facade::dispense('users');
                $newUser->user_name = $user;
                $newUser->user_pass = md5($password);
                $newUser->email = $email;
                Facade::store($newUser);
                echo 'User Added successfully';
            } elseif ($email == false) {
                echo 'The email address that you are trying to use is invalid';
            } else {
                echo 'User name or Email that you are trying to use is already Exist in the database';
            }
        } catch (Exception $e) {
            echo 'Error :' . $e->getMessage();
        }
    }

    /**
     * @param int $id
     * @param string $user
     * @param string $email
     *
     * @return bool
     */
    private function checkIsExist($user, $email, $id = 0)
    {
        $checkIskExist = Facade::find(
            'users',
            'id != :id AND (user_name = :user OR email = :email)',
            array(':user' => $user, ':email' => $email, ':id' => $id)
        );

        $result = empty($checkIskExist) ? false : true;

        return $result;
    }

    /**
     * @param int $startPage
     *
     * @return string
     */
    public function show($startPage = 0)
    {
        $Pagenation = new Pagenation('users', $this->maxResults, $startPage);
        return $this->twig->render(
            'show_user.html',
            array('results' => $Pagenation->getResults(), 'pagesNumber' => $Pagenation->getPagesNumber())
        );
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function edit($id)
    {

        $user = Facade::load('users', $id);
        if (!$user->isEmpty()) {
            return $this->twig->render('edit_user.html', array(
                'id' => $user->id,
                'user' => $user->user_name,
                'email' => $user->email
            ));
        } else {
            return General::ref('index.php');
        }
    }

    /**
     * @param int $id
     * @param string $user
     * @param string $password
     * @param string $email
     *
     * @return void
     */
    public function editExecute($id, $user, $password, $email)
    {

        try {
            $userUpdate = Facade::load('users', $id);
            if ($this->checkIsExist($user, $email, $id) == false && $email) {
                $userUpdate->user_name = $user;
                if ($password && !empty($password)) {
                    $userUpdate->user_pass = md5($password);
                }
                $userUpdate->email = $email;
                Facade::store($userUpdate);
                echo "User edited successfully";
            } elseif ($email == false) {
                echo 'The email address that you are trying to use is invalid';
            } else {
                echo 'User name or Email that you are trying to use is already Exist in the database';
            }
        } catch (Exception $e) {
            echo 'Error :' . $e->getMessage();
        }

    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function delete($id)
    {
        if ($id == 1) {
            $message = 'Admin user could not be deleted';
        } else {
            Facade::trash('users', $id);
            $message = 'the user with the ID ' . $id . ' deleted successfully';
        }
        return General::messageSent($message, 'index.php?do=users');
    }

}
