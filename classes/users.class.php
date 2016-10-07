<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;
use Exception;

/**
 * Class Users
 *
 * @package MyPoll\Classes
 */
class Users
{
    /** @var  Factory */
    protected $factory;

    /** @var int  */
    protected $maxResults;

    /**
     * @param Factory $factory
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
        $this->maxResults = $this->factory->getSettingsObj()->getResultNumber();
    }

    /**
     * @return string
     */
    public function add()
    {
        return $this->factory->getTwigAdminObj()->display('add_user.html');
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    public function addExecute($paramsArray)
    {
        try {
            if ($this->checkIsExist($paramsArray['user'], $paramsArray['email']) == false && $paramsArray['email']) {
                $newUser = Facade::dispense('users');
                $newUser->user_name = $paramsArray['user'];
                $newUser->user_pass = md5($paramsArray['password']);
                $newUser->email = $paramsArray['email'];
                Facade::store($newUser);
                echo 'User Added successfully';
            } elseif ($paramsArray['email'] == false) {
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
        $pagenation = $this->factory->getPagenationObj();
        $pagenation->setParams('users', $this->maxResults, $startPage);
        return $this->factory->getTwigAdminObj()->render(
            'show_user.html',
            array('results' => $pagenation->getResults(), 'pagesNumber' => $pagenation->getPagesNumber())
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
            return $this->factory->getTwigAdminObj()->render('edit_user.html', array(
                'id' => $user->id,
                'user' => $user->user_name,
                'email' => $user->email
            ));
        } else {
            return General::ref('index.php');
        }
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    public function editExecute($paramsArray)
    {

        try {
            $userUpdate = Facade::load('users', $paramsArray['id']);
            if ($this->checkIsExist($paramsArray['user'], $paramsArray['email'], $paramsArray['id']) == false
                && $paramsArray['email']) {
                $userUpdate->user_name = $paramsArray['user'];
                if ($paramsArray['password'] && !empty($password)) {
                    $userUpdate->user_pass = password_hash($password, PASSWORD_DEFAULT);
                }
                $userUpdate->email = $paramsArray['email'];
                Facade::store($userUpdate);
                echo "User edited successfully";
            } elseif ($paramsArray['email'] == false) {
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

    /**
     * @param string $userName
     *
     * @return mixed|null
     */
    public function getHash($userName)
    {
        $result = Facade::findOne('users', 'user_name = :user', [':user' => $userName]);
        if (empty($result)) {
            return false;
        }
        return $result['user_pass'];
    }

}
