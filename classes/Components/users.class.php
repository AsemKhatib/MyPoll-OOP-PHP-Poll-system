<?php

namespace MyPoll\Classes\Components;

use Exception;
use MyPoll\Classes\Database\DBInterface;
use MyPoll\Classes\FeaturesAbstract;
use MyPoll\Classes\Pagination;
use Twig_Environment;

/**
 * Class Users
 *
 * @package MyPoll\Classes
 */
class Users extends FeaturesAbstract
{
    /** @var DBInterface */
    protected $database;

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

    const INVALID_EMAIL = 'The email address that you are trying to use is invalid or empty';

    const USER_OR_EMAIL_EXIST = 'User name or Email that you are trying to use is already Exist in the database';

    /**
     * Users constructor.
     *
     * @param DBInterface      $database
     * @param Twig_Environment $twig
     * @param Pagination       $pagination
     * @param Settings         $settings
     */
    public function __construct(
        DBInterface $database,
        Twig_Environment $twig,
        Pagination $pagination,
        Settings $settings
    ) {
        $this->database = $database;
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
        return $this->twig->render('add_user.html');
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
        if (!$paramsArray['email']) {
            return self::INVALID_EMAIL;
        }

        if ($this->checkIsExist($paramsArray['user'], $paramsArray['email'])) {
            return self::USER_OR_EMAIL_EXIST;
        }

        return $this->addUser($paramsArray['user'], $paramsArray['password'], $paramsArray['email']);
    }

    /**
     * @param string $user
     * @param string $password
     * @param string $email
     *
     * @return string
     *
     * @throws Exception
     */
    private function addUser($user, $password, $email)
    {
        $newUser = $this->database->addRows(
            'users',
            array(
                array(
                    'user_name' => $user,
                    'user_pass' => password_hash($password, PASSWORD_DEFAULT),
                    'email' => $email
                )
            )
        );

        if (empty($this->database->store($newUser))) {
            throw new Exception('Something went wrong while trying to add the user');
        }

        return 'User Added successfully';
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
        $checkIskExist = $this->database->find(
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
        $this->pagination->setParams('users', $this->maxResults, $startPage, $this->database->count('users'));
        return $this->twig->render(
            'show_user.html',
            array(
                'results' => $this->pagination->getResults(),
                'pagesNumber' => $this->pagination->getPagesNumber()
            )
        );
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function edit($id)
    {
        $user = $this->database->getById('users', $id);

        if (empty($user)) {
            return General::messageSent(
                'The user is not exist in the system',
                $this->settings->getIndexPage() . '?do=show&route=users'
            );
        }

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
        if (!$paramsArray['email']) {
            return self::INVALID_EMAIL;
        }

        if ($this->checkIsExist($paramsArray['user'], $paramsArray['email'], $paramsArray['id'])) {
            return self::USER_OR_EMAIL_EXIST;
        }

        return $this->editUser(
            $paramsArray['id'],
            $paramsArray['user'],
            $paramsArray['password'],
            $paramsArray['email']
        );
    }

    /**
     * @param int    $id
     * @param string $user
     * @param string $password
     * @param string $email
     *
     * @return string
     *
     * @throws Exception
     */
    private function editUser($id, $user, $password, $email)
    {
        $userUpdate = $this->database->getById('users', $id, 'bean');
        $newPassword = password_hash($password, PASSWORD_DEFAULT);

        if (!empty($password)) {
            $this->database->editRow(
                $userUpdate,
                array('user_name' => $user, 'user_pass' => $newPassword, 'email' => $email)
            );
        } else {
            $this->database->editRow($userUpdate, array('user_name' => $user, 'email' => $email));
        }

        if (empty($this->database->store($userUpdate))) {
            throw new Exception('Something went wrong while trying to edit the user');
        }

        return 'User edited successfully';
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
            $this->database->deleteById('users', $id);
            $message = 'The user with the ID ' . $id . ' has been deleted successfully';
        }
        return General::messageSent($message, $this->settings->getIndexPage() . '?do=show&route=users');
    }

    /**
     * @param string $userName
     *
     * @return string|bool
     */
    public function getHash($userName)
    {
        $result = $this->database->findOne('users', 'user_name = :user', [':user' => $userName]);
        if (empty($result)) {
            return false;
        }
        return (string) $result['user_pass'];
    }

    /**
     * @param array $cookie
     *
     * @return array
     *
     * @throws Exception
     */
    public function getUserUsingCookie($cookie)
    {
        $user = $this->database->getById('users', $cookie['userID']);
        if (empty($user)) {
            throw new Exception('No user that matches the sent cookie has been found in the system');
        }
        return $user;
    }
}
