<?php

namespace MdlBol\Http\Controller;

use MdlBol\Model\Grade;

class HomeController extends AbstractController
{
    public function home()
    {
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $data = new Grade();

            // Obtiene la vista desde la cache
            if ($this->cacheFileAdapter()->hasItem($_SESSION['user_id'] . 'home')) {

                print_r($this->cacheEngine()->getItem($_SESSION['user_id'] . 'home'));
            } else {
            $this->prepareView('home.php');
            $this->view->title = 'Boletines de Moodle';
            $this->view->countStudEP = $data->getStudentsAmountCampus('EP');
            $this->view->countStudESO = $data->getStudentsAmountCampus('ESO');
            $this->view->countStudBACH = $data->getStudentsAmountCampus('BACH');
            $this->view->countCoursesEP = $data->getCoursesAmountCampus('Primaria');
            $this->view->countCoursesESO = $data->getCoursesAmountCampus('ESO');
            $this->view->countCoursesBACH = $data->getCoursesAmountCampus('BACH');

            $this->cacheEngine()->saveItem($_SESSION['user_id'] . 'home', $this->view->render());
            print_r($this->cacheEngine()->getItem($_SESSION['user_id'] . 'home'));
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    public function error()
    {
        $urlReferer = $this->request()->getFullRequestUri();
        $this->prepareView('error.php');
        $this->view->title = '404';
        $this->view->message = 'El recurso "' . BASE_URL . $urlReferer . '" no existe';
        $this->send(404);
    }
}
