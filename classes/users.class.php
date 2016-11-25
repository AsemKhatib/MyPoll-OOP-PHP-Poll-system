<?php

namespace MyPoll\Classes;

use Exception;
use MyPoll\Classes\Database\DBInterface;
use Twig_Environment;
use RedBeanPHP\Facade;

/**
 * Class Users
 *
 * @package MyPoll\Classes
 */
class Users extends FeaturesAbstract
{
    /** @var DBInterface */
    protected $db;

    /** @var Twig_Environment */
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
     * Users constructor.
     *
     * @param DBInterface      $db
     * @param Twig_Environment $twig
     * @param Pagination       $pagination
     * @param Settings         $settings
     */
    public function __construct(DBInterface $db, Twig_Environment $twig, Pagination $pagination, Settings $settings)
    {
        $this->db = $db;
        $this->twig = $twig;
        $this->pagination = $pagination;
        $this->settings = $settings;
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
     * @return array
     */
    public function getPostParamsForAddMethod()
    {
        $user = General::cleanInput('string', $_POST['user_name']);
        $password = General::cleanInput('string', $_POST['user_password']);
        $email = General::cleanInput('email', $_POST['user_email']);
        return array('user' => $user, 'password' => $password, 'email' => $email);
    }

    /**
     * @param array $paramsArray
     *
     * @return string
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
        $newUser = $this->db->addRows('users', array(
            array('user_name' => $user, 'user_pass' => password_hash($password, PASSWORD_DEFAULT), 'email' => $email)
            )
        );
        $this->db->store($newUser);
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
        $checkIskExist = $this->db->find(
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
        $this->pagination->setParams('users', $this->maxResults, $startPage, $this->db->count('users'));
        return $this->twig->render(
            'show_user.html',
            array(
                'results' => $this->pagination->getResults(),
                'pagesNumber' => $this->pagination->getPagesNumber())
        );
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function edit($id)
    {
        $user = $this->db->getById('users', $id);

        if (empty($user)) return General::ref($this->settings->getIndexPage());

        return $this->twig->render('edit_user.html', array(
            'id' => $user['id'],
            'user' => $user['user_name'],
            'email' => $user['email']
        ));
    }

    /**
     * @return array
     */
    public function getPostParamsForEditMethod()
    {
        $id = General::cleanInput('int', $_POST['user_id']);
        $user = General::cleanInput('string', $_POST['user_name']);
        $password = General::cleanInput('password', $_POST['user_password']);
        $email = General::cleanInput('email', $_POST['user_email']);
        return array('id' => $id, 'user' => $user, 'password' => $password, 'email' => $email);
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
     *
     * @return string
     */
    private function editUser($id, $user, $password, $email)
    {
        $userUpdate = $this->db->getById('users', $id, 'bean');
        $newPassword = password_hash($password, PASSWORD_DEFAULT);

        if (!empty($password)) {
            $this->db->editRow($userUpdate, array('user_name' => $user, 'user_pass' => $newPassword, 'email' => $email));
        } else {
            $this->db->editRow($userUpdate, array('user_name' => $user, 'email' => $email));
        }

        $this->db->store($userUpdate);
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
            $this->db->deleteById('users', $id);
            $message = 'The user with the ID ' . $id . ' has been deleted successfully';
        }
        echo General::messageSent($message, $this->settings->getIndexPage() . '?do=show&route=users');
    }

    /**
     * @param string $userName
     *
     * @return string|bool
     */
    public function getHash($userName)
    {
        $result = $this->db->findOne('users', 'user_name = :user', [':user' => $userName]);
        if (empty($result)) return false;
        return (string) $result['user_pass'];
    }

}
