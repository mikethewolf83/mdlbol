<?php

namespace MdlBol\Http\Controller;

use MdlBol\Model\UserValid;

class AdminController extends AbstractController
{
    public function admin()
    {
        if (isset($_SESSION['user_admin']) && $_SESSION['user_admin'] == true) {

            // Modelo UserValid
            $data = new UserValid();

            $this->prepareView('admin/admin.php');
            $this->view->title = 'Área de admins';
            $this->view->users = $data->getNromalUsers();
            $this->view->admins = $data->getAdminUsers();
            $this->view->execResult = '';
            $this->view->usernameError = '';
            $this->view->formError = '';
            $this->view->usertypeError = '';

            $queryAction = $this->request()->getQuery('action');

            if ($queryAction == 'clearCache') {
                $execResult = shell_exec('php -f ' . APPDIR . '/script/clearcache');

                $this->view->execResult = $execResult;
                $this->send();
            } elseif (($queryAction == 'addUser') && ($_SERVER['REQUEST_METHOD'] == 'POST')) {

                //Filtrar datos del POST
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $this->view->username = trim($_POST['username']);
                $this->view->usertype = $_POST['usertype'];

                // Validar csrf token
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    // $this->view->formError = 'Token CSRF no válido';
                    $this->view->execResult = 'Token CSRF no válido';
                }
                
                if (empty($this->view->username)) {
                    // $this->view->usernameError = 'El usuario no puede estar vacío';
                    $this->view->execResult = 'El usuario no puede estar vacío';
                }

                if (empty($this->view->usertype)) {
                    // $this->view->usertypeError = 'El rol de usuario no puede estar vacío';
                    $this->view->execResult = 'El rol de usuario no puede estar vacío';
                }

                // Comprobar si no hay errores
                if (/*empty($this->view->formError) && empty($this->view->usernameError) && empty($this->view->usertypeError)*/ empty($this->view->execResult) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                   $rowUser = $data->addUser($this->view->usertype, $this->view->username);
                    if ($rowUser) {
                        $this->view->execResult = 'El usuario ' . $this->view->username . ' se creó con éxito';
                    } else {
                        $this->view->formError = 'El usuario ' . $this->view->username . ' ya existe en ese rol';
                    }
                }
                $this->send();
            } else {
                $this->send();
            }
        } else {
            $urlReferer = $this->request()->getFullRequestUri();
            $this->prepareView('error.php');
            $this->view->title = '403';
            $this->view->message = 'Su usuario no está autorizado a acceder a "' . BASE_URL . $urlReferer . '"';
            $this->send(403);
        }
    }

    public function deleteValidUser()
    {
        // Modelo UserValid
        $data = new UserValid();

        // 2do parámetro de la URI (user o admin)
        $userType = $this->request()->getSegment(1);

        // 3er parámetro de la URI (username)
        $userid = $this->request()->getSegment(2);

        $this->prepareView('admin/admin.php');

        if ($userType == 'delete-user') {
            $data->deleteUser('mb_normal_user', $userid);
            $this->view->title = 'Área de admins de MdlBol';
            $this->view->execResult = 'El usuario se eliminó con éxito';
        } elseif ($userType == 'delete-admin') {
            $data->deleteUser('mb_admin_user', $userid);
            $this->view->title = 'Área de admins de MdlBol';
            $this->view->execResult = 'El usuario se eliminó con éxito';
        } else {
            $this->view->execResult = '';
        }
        $this->send();
    }
}
