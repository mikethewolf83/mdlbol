<?php

namespace MdlBol\Http\Controller;

use MdlBol\Model\User;
use MdlBol\Model\UserValid;

class UsersController extends AbstractController
{
    public function login()
    {
        // Se crea un hash para ser usado como token
        $token = bin2hex(random_bytes(24));

        // Comprobar si hay un usuario logeado
        if (!isset($_SESSION['user_loggedin'])) {
            // Modelo User
            $data = new User();

            $this->prepareView('users/login.php');
            $this->view->title = 'Acceder a MdlBol v' . VERSION;

            $this->view->userTokenError = '';
            $this->view->formError = '';
            $this->view->usernameError = '';
            $this->view->passwordError = '';

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                //Filtrar datos del POST
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $this->view->username = trim($_POST['username']);
                $this->view->password = trim($_POST['password']);

                // Validar csrf token
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    $this->view->userTokenError = 'Token CSRF no válido';
                }

                // Validar username
                if (empty($this->view->username)) {
                    $this->view->usernameError = 'El usuario no puede estar vacío';
                }

                // Validar password
                if (empty($this->view->password)) {
                    $this->view->passwordError  = 'La contraseña no puede estar vacía';
                }

                // Comprobar si no hay errores
                if (empty($this->view->usernameError) && empty($this->view->passwordError) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    $rowUser = $data->loginUser($this->view->username, $this->view->password);

                    if ($rowUser) {
                        $this->createUserSession($rowUser);
                    } else {
                        $this->view->formError = 'Error en el usuario o la contraseña. Debe intentar de nuevo.';
                    }
                }
            } else {
                // Si el método es GET definir valores del formulario
                $_SESSION['csrf_token'] = $token; // Se crea un token csrf y se asigna a una variable de sesión
                $this->view->username = '';
                $this->view->password = '';
                $this->view->userTokenError = '';
                $this->view->formError = '';
                $this->view->usernameError = '';
                $this->view->passwordError = '';
            }
            $this->send();
        } else {
            // Si ya hay un usuario autenticado, se redirige al home
            $this->redirect(BASE_URL);
        }
    }

    public function logout()
    {
        // Borrar las variables de la sesión
        unset($_SESSION['user_loggedin']);
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_fullname']);
        unset($_SESSION['csrf_token']);
        unset($_SESSION['user_admin']);
        $this->redirect(BASE_URL . '/users/login');
    }

    private function createUserSession($user)
    {
        // Modelo UserValid
        $data = new UserValid();
        // True si el usuario es admin
        $admin = $data->getIsAdmin($user['username']);

        // Si el usuario pertenece al grupo admins se crea una variable de sesión
        if (in_array($user->username, $admin)) {
            $_SESSION['user_admin'] = true;
        } else {
            $_SESSION['user_admin'] = false;
        }

        // Se establecen las demás variables de la sesión
        $_SESSION['user_loggedin'] = true;
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->username;
        $_SESSION['user_fullname'] = $user->firstname . ' ' . $user->lastname;

        $this->redirect(BASE_URL);
        die();
    }
}
