<?php

namespace MyPoll\Classes;

use Exception;
use RedBeanPHP\Facade;

/**
 * Class Users
 *
 * @package MyPoll\Classes
 */
class Users
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var Pagination */
    protected $pagination;

    /** @var  Settings */
    protected $settings;

    /** @var int */
    protected $maxResults;

    /** @var  int */
    protected $adminID = 1;

    /**
     * @param Factory $factory
     */
    public function __construct($factory)
    {
        $this->twig = $factory->getTwigAdminObj();
        $this->pagination = $factory->getPaginationObj();
        $this->settings = $factory->getSettingsObj();
        $this->maxResults = $this->settings->getResultNumber();
    }

    /**
     * @return string
     */
    public function add()
    {
        return $this->twig->display('add_user.html');
    }

    /**
     * @param array $paramsArray
     *
     * @return string|Exception
     */
    public function addExecute($paramsArray)
    {
        try {
            if (!$paramsArray['email']) {
                echo 'The email address that you are trying to use is invalid or empty';
            } elseif ($this->checkIsExist($paramsArray['user'], $paramsArray['email'])) {
                echo 'User name or Email that you are trying to use is already Exist in the database';
            } else {
                $this->addUser($paramsArray['user'], $paramsArray['password'], $paramsArray['email']);
            }
        } catch (Exception $e) {
            echo 'Error :' . $e->getMessage();
        }
    }

    /**
     * @param string $user
     * @param string $password
     * @param string $email
     */
    private function addUser($user, $password, $email)
    {
        $newUser = Facade::dispense('users');
        $newUser->user_name = $user;
        $newUser->user_pass = password_hash($password, PASSWORD_DEFAULT);
        $newUser->email = $email;
        Facade::store($newUser);
        echo 'User Added successfully';
    }

    /**
     * @param int    $id
     * @param string $user
     * @param string $email
     *
     * @return bool
     */
    private function checkIsExist($user, $email, $id = -1)
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
        $this->pagination->setParams('users', $this->maxResults, $startPage);
        return $this->twig->render(
            'show_user.html',
            array('results' => $this->pagination->getResults(), 'pagesNumber' => $this->pagination->getPagesNumber())
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
        if ($user->isEmpty()) {
            return General::ref('index.php');
        }
        return $this->twig->render('edit_user.html', array(
            'id' => $user->id,
            'user' => $user->user_name,
            'email' => $user->email
        ));
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    public function editExecute($paramsArray)
    {
        try {
            if (!$paramsArray['email']) {
                echo 'The email address that you are trying to use is invalid or empty';
            } elseif ($this->checkIsExist($paramsArray['user'], $paramsArray['email'], $paramsArray['id'])) {
                echo 'User name or Email that you are trying to use is already Exist in the database';
            } else {
                $this->editUser(
                    $paramsArray['id'],
                    $paramsArray['user'],
                    $paramsArray['password'],
                    $paramsArray['email']
                );
            }
        } catch (Exception $e) {
            echo 'Error :' . $e->getMessage();
        }
    }

    /**
     * @param int    $id
     * @param string $user
     * @param string $password
     * @param string $email
     */
    private function editUser($id, $user, $password, $email)
    {
        $userUpdate = Facade::load('users', $id);
        $userUpdate->user_name = $user;
        if (!empty($password)) {
            $userUpdate->user_pass = password_hash($password, PASSWORD_DEFAULT);
        }
        $userUpdate->email = $email;
        Facade::store($userUpdate);
        echo "User edited successfully";
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function delete($id)
    {
        if ($id == $this->adminID) {
            $message = 'Admin user could not be deleted';
        } else {
            Facade::trash('users', $id);
            $message = 'the user with the ID ' . $id . ' deleted successfully';
        }
        echo General::messageSent($message, 'index.php?do=users');
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
