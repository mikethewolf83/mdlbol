<?php

namespace MdlBol\Http\Controller;

// use Pop\Filter\Filter;
use MdlBol\Exception;
use MdlBol\Model\Grade;
use MdlBol\Model\Feedback;
use Dompdf\Dompdf;

class GradesController extends AbstractController
{
    // Listado de estudiantes y asignaturas por grupo
    public function gradesByTrimCampusGroup()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $data = new Grade();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            // URIs
            $courseUri = '/' . $trim . '/' . $campus . '/' . $group . '/course';
            $studentsUri = '/' . $trim . '/' . $campus . '/' . $group . '/student';
            $campusGroup = $campus . '/' . $group;
            $uri = $this->request()->getRequestUri();

            // Mostrar las notas si la sede y el trimestre devuelven datos
            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup)) {

                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);

                // Obtiene la vista desde la cache
                if ($this->cacheFileAdapter()->hasItem($_SESSION['user_id'] . $trim . $campus . $group)) {

                    print_r($this->cacheEngine()->getItem($_SESSION['user_id'] . $trim . $campus . $group));
                } else {

                    // Si no existe la cache de la vista entonces crea una
                    $this->prepareView('grades/group.php');

                    $this->view->title = 'Calificaciones de ' . $group . ' Trimestre ' . $trim;
                    $this->view->courses = $data->getCoursesInGroup($group);
                    $this->view->students = $data->getStudentsInGroup($group);
                    $this->view->courseUri = $courseUri;
                    $this->view->studentsUri = $studentsUri;
                    $this->view->uri = $uri;
                    $this->view->totalCourses = count($this->view->courses);
                    $this->view->totalStudents = count($this->view->students);

                    $this->cacheEngine()->saveItem($_SESSION['user_id'] . $trim . $campus . $group, $this->view->render());
                    print_r($this->cacheEngine()->getItem($_SESSION['user_id'] . $trim . $campus . $group));
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Calificaciones por grupo y asignatura
    public function gradesByTrimCampusGroupCourse()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $dataGrade = new Grade();

            // Modelo Feedback
            $dataFeedback = new Feedback();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            // 5to parámetro de la URI (asignatura)
            $course = $this->request()->getSegment(4);

            // URIs
            $studentsUri = '/' . $trim . '/' . $campus . '/' . $group . '/student';
            $courseUri = '/' . $trim . '/' . $campus . '/' . $group . '/course';
            $uri = $this->request()->getRequestUri();
            $campusGroup = $campus . '/' . $group;
            $groupCourse = $group . '/course/' . $course;

            // Request query para eliminar el comentario al CIDEAD
            $queryAction = $this->request()->getQuery('action');

            // Mostrar las notas si la sede, el trimestre y el grupo devuelven datos
            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup) && $this->parseGroupCourse($groupCourse) && $course && $studentsUri) {

                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);

                // Si no existe la cache de la vista entonces crea una
                $this->prepareView('grades/group_course.php');

                $this->view->title = $dataGrade->getCourseName($course)['Curso'] . ' Trimestre ' . $trim . ' Grupo ' . $group;
                // $this->view->filterHtml = new Filter('strip_tags'); // Filtra todo el html en forma de '<tag> </tag>' que venga en los comentarios
                // $this->view->filterHtml = new Filter('htmlentities', [ENT_QUOTES, 'UTF-8']); // Filtra todo el html que venga en los comentarios y lo convierte a texto plano
                $this->view->grades = $dataGrade->getTrimCampusGroupCourse($trim, $campus, $group, $course);
                $this->view->dataFeedback = $dataFeedback;
                $this->view->trim = $trim;
                $this->view->campus = $campus;
                $this->view->group = $group;
                $this->view->course = $course;
                $this->view->studentsUri = $studentsUri;
                $this->view->courseUri = $courseUri;
                $this->view->total = count($this->view->grades);
                $this->view->execResult = '';
                $this->view->uri = $uri;

                // Eliminar comentario para el CIDEAD
                if (($queryAction == 'deleteFeedbackCidead') && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
                    //Filtrar datos del POST
                    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $this->view->student = $_POST['student'];

                    // Validar csrf token
                    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $this->view->execResult = 'Token CSRF no válido';
                    }

                    // Validar student-id
                    if (empty($this->view->student)) {
                        $this->view->execResult = 'El estudiante no puede estar vacío';
                    }

                    // Comprobar si no hay errores
                    if (empty($this->view->execResult) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $deleteFeedbackCidead = $dataFeedback->deleteFeedbackCidead($trim, $campus, $group, $course, $this->view->student);

                        if ($deleteFeedbackCidead) {
                            $this->redirect(BASE_URL . $courseUri . '/' . $course);
                        } else {
                            $this->view->execResult = "Algo pasó y no se pudo eliminar el comentario al CIDEAD. Por favor intente otra vez.";
                        }
                    }
                    $this->send();
                } else {
                    $this->send();
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Calificaciones por grupo y estudiante 
    public function gradesByTrimCampusGroupStudent()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $data = new Grade();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            // 5to parámetro de la URI (estudiante)
            $student = $this->request()->getSegment(4);

            // URI completa
            $courseUri = '/' . $trim . '/' . $campus . '/' . $group . '/course';
            $campusGroup = $campus . '/' . $group;
            $uri = $this->request()->getRequestUri();

            // Mostrar las notas si la sede, el trimestre, el grupo y el estudiante devuelven datos
            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup) && $student) {

                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);

                // Si no existe la cache de la vista entonces crea una
                $this->prepareView('grades/group_course_stud.php');

                $this->view->title = $data->getStudentName($student)['username'] . ' Trimestre ' . $trim . ' Grupo ' . $group;
                // $this->view->filterHtml = new Filter('strip_tags'); // Filtra todo el html en forma de '<tag> </tag>' que venga en los comentarios
                // $this->view->filterHtml = new Filter('htmlentities', [ENT_QUOTES, 'UTF-8']); // Filtra todo el html que venga en los comentarios y lo convierte a texto plano
                $this->view->grades = $data->getTrimCampusGroupStudent($trim, $campus, $group, $student);
                $this->view->courseUri = $courseUri;
                $this->view->total = count($this->view->grades);
                $this->view->uri = $uri;
                $this->send();
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Editar el comentario al estudiante en la vista asignatura
    public function editStudentCourseFeedback()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $dataGrade = new Grade();

            // Modelo Feedback
            $dataFeedback = new Feedback();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            // 5to parámetro de la URI (asignatura)
            $course = $this->request()->getSegment(4);

            // 7mo parámetro de la URI (estudiante)
            $student = $this->request()->getSegment(6);

            // URI completa
            $courseUri = '/' . $trim . '/' . $campus . '/' . $group . '/course/' . $course;
            $campusGroup = $campus . '/' . $group;
            $groupCourse = $group . '/course/' . $course;
            $uri = $this->request()->getRequestUri();

            // Mostrar las notas si la sede, el trimestre, el grupo y el estudiante devuelven datos
            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup) && $this->parseGroupCourse($groupCourse) && $this->parseCourseStud($course, $student) && $student && $course) {

                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);

                // Se prepara la vista
                $this->prepareView('grades/edit_feedback_stud.php');

                $this->view->title = $dataGrade->getStudentName($student)['username'] . ' Trimestre ' . $trim . ' Grupo ' . $group;
                $this->view->studentFeedback = $dataFeedback->getFeedbackMdl($trim, $campus, $group, $course, $student);
                // $this->view->filterHtml = new Filter('strip_tags'); // Filtra todo el html en forma de '<tag> </tag>' que venga en los comentarios
                // $this->view->filterHtml = new Filter('htmlentities', [ENT_QUOTES, 'UTF-8']); // Filtra todo el html que venga en los comentarios y lo convierte a texto plano
                $this->view->uri = $uri;
                $this->view->formError = '';
                $this->view->editFeedbackError = '';

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    //Filtrar datos del POST
                    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $this->view->editFeedback = trim($_POST['edit-feedback']);

                    // Validar csrf token
                    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $this->view->formError  = 'Token CSRF no válido';
                    }

                    // Validar edit-feedback
                    if (empty($this->view->editFeedback)) {
                        $this->view->editFeedbackError = 'El comentario no puede estar vacío';
                    }

                    // Comprobar si no hay errores
                    if (empty($this->view->formError) && empty($this->view->editFeedbackError) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $addFeedback = $dataFeedback->updateFeedbackMdl($this->view->editFeedback, $trim, $campus, $group, $course, $student);

                        if ($addFeedback) {
                            $this->redirect(BASE_URL . $courseUri);
                        } else {
                            $this->view->formError = 'Algo pasó y no se pudo actualizar el comentario';
                        }
                    }
                    $this->send();
                } else {
                    $this->send();
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Editar el comentario al estudiante en la vista estudiante
    public function editStudentFeedback()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $dataGrade = new Grade();

            // Modelo Feedback
            $dataFeedback = new Feedback();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            // 5to parámetro de la URI (estudiante)
            $student = $this->request()->getSegment(4);

            // 7mo parámetro de la URI (asignatura)
            $course  = $this->request()->getSegment(6);

            // URI completa
            $studentsUri = '/' . $trim . '/' . $campus . '/' . $group . '/student/' . $student;
            $campusGroup = $campus . '/' . $group;
            $groupCourse = $group . '/course/' . $course;
            $uri = $this->request()->getRequestUri();

            // Mostrar las notas si la sede, el trimestre, el grupo y el estudiante devuelven datos
            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup) && $this->parseGroupCourse($groupCourse) && $this->parseCourseStud($course, $student) && $student && $course) {

                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);

                // Se prepara la vista
                $this->prepareView('grades/edit_feedback_stud.php');

                $this->view->title = $dataGrade->getCourseName($course)['Curso'] . ' Trimestre ' . $trim . ' Grupo ' . $group;
                $this->view->studentFeedback = $dataFeedback->getFeedbackMdl($trim, $campus, $group, $course, $student);
                // $this->view->filterHtml = new Filter('strip_tags'); // Filtra todo el html en forma de '<tag> </tag>' que venga en los comentarios
                // $this->view->filterHtml = new Filter('htmlentities', [ENT_QUOTES, 'UTF-8']); // Filtra todo el html que venga en los comentarios y lo convierte a texto plano
                $this->view->uri = $uri;
                $this->view->formError = '';
                $this->view->editFeedbackError = '';

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    //Filtrar datos del POST
                    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $this->view->editFeedback = trim($_POST['edit-feedback']);

                    // Validar csrf token
                    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $this->view->formError  = 'Token CSRF no válido';
                    }

                    // Validar username
                    if (empty($this->view->editFeedback)) {
                        $this->view->editFeedbackError = 'El comentario no puede estar vacío';
                    }

                    // Comprobar si no hay errores
                    if (empty($this->view->formError) && empty($this->view->editFeedbackError) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $addFeedback = $dataFeedback->updateFeedbackMdl($this->view->editFeedback, $trim, $campus, $group, $course, $student);

                        if ($addFeedback) {
                            $this->redirect(BASE_URL . $studentsUri);
                        } else {
                            $this->view->formError = 'Algo pasó y no se pudo actualizar el comentario';
                        }
                    }
                    $this->send();
                } else {
                    $this->send();
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Agregar comentario para el CIDEAD
    public function addStudentFeedbackCidead()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $dataGrade = new Grade();

            // Modelo Feedback
            $dataCidead = new Feedback();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            // 5to parámetro de la URI (asignatura)
            $course = $this->request()->getSegment(4);

            // 7mo parámetro de la URI (estudiante)
            $student = $this->request()->getSegment(6);

            // URI completa
            $courseUri = '/' . $trim . '/' . $campus . '/' . $group . '/course/' . $course;
            $campusGroup = $campus . '/' . $group;
            $groupCourse = $group . '/course/' . $course;
            $uri = $this->request()->getRequestUri();

            // Mostrar las notas si la sede, el trimestre, el grupo y el estudiante devuelven datos
            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup) && $this->parseGroupCourse($groupCourse) && $this->parseCourseStud($course, $student) && $student && $course) {

                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);

                // Se prepara la vista
                $this->prepareView('grades/add_feedback_cidead.php');

                $this->view->title = $dataGrade->getStudentName($student)['username'] . ' Trimestre ' . $trim . ' Grupo ' . $group;
                $this->view->uri = $uri;
                $this->view->formError = '';
                $this->view->addFeedbackError = '';

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    //Filtrar datos del POST
                    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $this->view->addFeedback = trim($_POST['add-feedback-cidead']);

                    // Validar csrf token
                    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $this->view->formError  = 'Token CSRF no válido';
                    }

                    // Validar add-feedback-cidead
                    if (empty($this->view->addFeedback)) {
                        $this->view->addFeedbackError = 'El comentario no puede estar vacío';
                    }

                    // Comprobar si no hay errores
                    if (empty($this->view->formError) && empty($this->view->addFeedbackError) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $addFeedbackCidead = $dataCidead->addFeedbackCidead((int)$student, $trim, $campus, $group, (int)$course, $this->view->addFeedback);

                        if ($addFeedbackCidead) {
                            $this->redirect(BASE_URL . $courseUri);
                        } else {
                            $this->view->formError = 'Algo pasó y no se pudo agregar el comentario';
                        }

                        /*\var_dump((int)$student).PHP_EOL;
                        \var_dump($trim).PHP_EOL;
                        \var_dump($campus).PHP_EOL;
                        \var_dump($group).PHP_EOL;
                        \var_dump((int)$course).PHP_EOL;
                        \var_dump($this->view->addFeedback).PHP_EOL;*/
                    }
                    $this->send();
                } else {
                    $this->send();
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Editar comentario para el CIDEAD
    public function editStudentFeedbackCidead()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $dataGrade = new Grade();

            // Modelo Feedback
            $dataCidead = new Feedback();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            // 5to parámetro de la URI (asignatura)
            $course = $this->request()->getSegment(4);

            // 7mo parámetro de la URI (estudiante)
            $student = $this->request()->getSegment(6);

            // URI completa
            $courseUri = '/' . $trim . '/' . $campus . '/' . $group . '/course/' . $course;
            $campusGroup = $campus . '/' . $group;
            $groupCourse = $group . '/course/' . $course;
            $uri = $this->request()->getRequestUri();

            // Mostrar las notas si la sede, el trimestre, el grupo y el estudiante devuelven datos
            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup) && $this->parseGroupCourse($groupCourse) && $this->parseCourseStud($course, $student) && $student && $course) {

                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);

                // Se prepara la vista
                $this->prepareView('grades/edit_feedback_cidead.php');

                $this->view->title = $dataGrade->getStudentName($student)['username'] . ' Trimestre ' . $trim . ' Grupo ' . $group;
                $this->view->studentFeedbackCidead = $dataCidead->getFeedbackCidead($trim, $campus, $group, $course, $student);
                $this->view->uri = $uri;
                $this->view->formError = '';
                $this->view->editFeedbackError = '';

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    //Filtrar datos del POST
                    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $this->view->editFeedbackCidead = trim($_POST['edit-feedback-cidead']);

                    // Validar csrf token
                    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $this->view->formError  = 'Token CSRF no válido';
                    }

                    // Validar add-feedback-cidead
                    if (empty($this->view->editFeedbackCidead)) {
                        $this->view->editFeedbackCideadkError = 'El comentario no puede estar vacío';
                    }

                    // Comprobar si no hay errores
                    if (empty($this->view->formError) && empty($this->view->editFeedbackCideadkError) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                        $editFeedbackCidead = $dataCidead->updateFeedbackCidead((int)$student, $trim, $campus, $group, (int)$course, $this->view->editFeedbackCidead);

                        if ($editFeedbackCidead) {
                            $this->redirect(BASE_URL . $courseUri);
                        } else {
                            $this->view->formError = 'Algo pasó y no se pudo actualizar el comentario';
                        }
                    }
                    $this->send();
                } else {
                    $this->send();
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Generar boletín individual por asignatura (CIDEAD)
    public function genPdfCourse()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $dataGrade = new Grade();

            // Modelo Feedback
            $dataFeedback = new Feedback();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            // 5to parámetro de la URI (asignatura)
            $course = $this->request()->getSegment(4);

            // Request query para descargar y ver el pdf
            $viewDownload = $this->request()->getQuery('action');

            // URIs
            $studentsUri = '/' . $trim . '/' . $campus . '/' . $group . '/student';
            $campusGroup = $campus . '/' . $group;
            $groupCourse = $group . '/course/' . $course;

            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup) && $this->parseGroupCourse($groupCourse) && $course && $studentsUri) {

                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);
                $grades = $dataGrade->getTrimCampusGroupCourse($trim, $campus, $group, $course);

                $titleText = '<b>CENTRO EDUCATIVO ESPAÑOL DE LA HABANA</b>';
                $trimText = 'Informe de calificaciones Trimestre ' . $trim;
                $courseText = '<b>Asignatura:</b> ' . $dataGrade->getCourseName($course)['Curso'];
                $groupText = '<b>Grupo:</b> ' . $group;
                $title = 'CEEH ' . $trimText . ' ' . $dataGrade->getCourseName($course)['Curso'];

                $html = '<html>';
                $html .= '<head>';
                $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
                $html .= '<meta name="description" content="MdlBol: Boletines de calificaciones de Moodle CEEH">';
                $html .= '<meta name="keywords" content="CEEH, Moodle CEEH, Boletines de calificaciones CEEH">';
                $html .= '<meta name="author" content="Centro Educativo Español de La Habana">';
                $html .= '<link rel="stylesheet" type="text/css" href="assets/css/mdlbol.min.css">';
                $html .= '<link rel="stylesheet" type="text/css" href="assets/css/mdlbol-pdf.min.css">';
                $html .= '<title>';
                $html .= $title;
                $html .= '</title>';
                $html .= '</head>';
                $html .= '<body>';
                $html .= '<header>';
                $html .= '<img src="assets/images/pdf_logo.png">';
                $html .= '</header>';
                $html .= '<footer class="subtitle is-7">';
                $html .= $title;
                $html .= '</footer>';
                $html .= '<main>';
                $html .= '<h3 id="titleText" class="subtitle is-4 has-text-info-dark">' . $titleText . '</h3>';
                $html .= '<h5 id="trimText" class="subtitle is-5 has-text-dark"><b>' . $trimText  . '</b></h5>';
                $html .= '<h5 id="courseText" class="subtitle is-6 has-text-dark">' . $courseText  . '</h5>';
                $html .= '<h5 id="groupText" class="subtitle is-6 has-text-dark">' . $groupText  . '</h5>';
                $html .= '<table class="table">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th class="has-text-left" style="width: 25%;">Nombre y Apellidos</th>';
                $html .= '<th style="width: 15%;">Calificación</th>';
                $html .= '<th>Comentario</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                foreach ($grades as $g) {
                    $html .= '<tr>';
                    $html .= '<td>' . $g['firstname'] . ' ' . $g['lastname'] . '</td>';
                    $html .= '<td class="has-text-centered">' . round($g['NotaTrimestre'], 1) . '</td>';
                    $hasFeedback = $dataFeedback->hasFeedbackCidead($g['userid'], $course);
                    if ($hasFeedback['count'] == 1) {
                        $html .= '<td style="text-align: justify;">' . $dataFeedback->getFeedbackCidead($trim, $campus, $group, (int)$course, $g['userid'])['feedback_cidead'] . '</td>';
                    } else {
                        $html .= '<td style="text-align: justify;">' . $g['TrimestreObservaciones'] . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
                $html .= '</table>';
                $html .= '</main>';
                $html .= '</body>';
                $html .= '</html>';

                $dompdf = new Dompdf();
                $dompdf->getOptions()->setIsRemoteEnabled(TRUE);
                $dompdf->getOptions()->set('isHtml5ParserEnabledd' . TRUE);
                $dompdf->getOptions()->set('isPhpEnabledd' . TRUE);
                $dompdf->getOptions()->setChroot(APPDIR . '/public/');
                $dompdf->loadHtml($html);
                $dompdf->setPaper('letter', 'landscape');
                $dompdf->render();

                if ($viewDownload == 'view') {

                    $dompdf->stream($group . '_' . $dataGrade->getCourseName($course)['Curso'] . '.pdf', ["Attachment" => false]);
                } elseif ($viewDownload == 'download') {

                    $dompdf->stream($group . '_' . $dataGrade->getCourseName($course)['Curso'] . '.pdf');
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Generar boletín individual por estudiante
    public function genPdfStudent()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {

            // Modelo Grade
            $data = new Grade();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            // 5to parámetro de la URI (estudiante)
            $student = $this->request()->getSegment(4);

            // Request query para descargar y ver el pdf
            $viewDownload = $this->request()->getQuery('action');

            // URI completa
            $campusGroup = $campus . '/' . $group;

            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup) && $student) {
                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);
                $grades = $data->getTrimCampusGroupStudent($trim, $campus, $group, $student);

                $titleText = '<b>CENTRO EDUCATIVO ESPAÑOL DE LA HABANA</b>';
                $trimText = 'Informe de calificaciones Trimestre ' . $trim;
                $studText = '<b>Estudiante:</b> ' . $data->getStudentName($student)['username'];
                $groupText = '<b>Grupo:</b> ' . $group;
                $title = 'CEEH ' . $trimText . ' ' . $data->getStudentName($student)['username'];

                $html = '<html>';
                $html .= '<head>';
                $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
                $html .= '<meta name="description" content="MdlBol: Boletines de calificaciones de Moodle CEEH">';
                $html .= '<meta name="keywords" content="CEEH, Moodle CEEH, Boletines de calificaciones CEEH">';
                $html .= '<meta name="author" content="Centro Educativo Español de La Habana">';
                $html .= '<link rel="stylesheet" type="text/css" href="assets/css/mdlbol.min.css">';
                $html .= '<link rel="stylesheet" type="text/css" href="assets/css/mdlbol-pdf.min.css">';
                $html .= '<title>';
                $html .= $title;
                $html .= '</title>';
                $html .= '</head>';
                $html .= '<body>';
                $html .= '<header>';
                $html .= '<img src="assets/images/pdf_logo.png">';
                $html .= '</header>';
                $html .= '<footer class="subtitle is-7">';
                $html .= $title;
                $html .= '</footer>';
                $html .= '<main>';
                $html .= '<h3 id="titleText" class="subtitle is-4 has-text-info-dark">' . $titleText . '</h3>';
                $html .= '<h5 id="trimText" class="subtitle is-5 has-text-dark"><b>' . $trimText  . '</b></h5>';
                $html .= '<h5 id="studText" class="subtitle is-6 has-text-dark">' . $studText  . '</h5>';
                $html .= '<h5 id="groupText" class="subtitle is-6 has-text-dark">' . $groupText  . '</h5>';
                $html .= '<table class="table">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th class="has-text-left" style="width: 25%;">Asignatura</th>';
                $html .= '<th style="width: 15%;">Calificación</th>';
                $html .= '<th>Comentario</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                foreach ($grades as $g) {
                    $html .= '<tr>';
                    $html .= '<td>' . $g['Curso'] . '</td>';
                    $html .= '<td class="has-text-centered">' . round($g['NotaTrimestre'], 1) . '</td>';
                    $html .= '<td style="text-align: justify;">' . $g['TrimestreObservaciones'] . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
                $html .= '</table>';
                $html .= '</main>';
                $html .= '</body>';
                $html .= '</html>';

                $dompdf = new Dompdf();
                $dompdf->getOptions()->setIsRemoteEnabled(TRUE);
                $dompdf->getOptions()->set('isHtml5ParserEnabledd' . TRUE);
                $dompdf->getOptions()->set('isPhpEnabledd' . TRUE);
                $dompdf->getOptions()->setChroot(APPDIR . '/public/');
                $dompdf->loadHtml($html);
                $dompdf->setPaper('letter', 'landscape');
                $dompdf->render();

                if ($viewDownload == 'view') {

                    $dompdf->stream($group . '_' . $data->getStudentName($student)['username'] . '.pdf', ['Attachment' => false]);
                } elseif ($viewDownload == 'download') {

                    $dompdf->stream($group . '_' . $data->getStudentName($student)['username'] . '.pdf');
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Generación masiva de boletines de asignatutas (CIDEAD)
    public function genBulkPdfCourse()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $dataGrade = new Grade();

            // Modelo Feedback
            $dataFeedback = new Feedback();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            $campusGroup = $campus . '/' . $group;

            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup)) {
                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);

                $coursesInGroup = $dataGrade->getCoursesInGroup($group);

                // Directorio dónde se guardarán los pdf temporales
                $pdfDir = APPDIR . '/pdf/' . strtoupper($campus) . '/' . $group . '/Asignaturas/';

                // Si el directorio no existe se crea
                if (!is_dir($pdfDir)) {
                    mkdir($pdfDir, 0700, true);
                }

                // Iterar por cada curso en el grado para obtener los pdf con las asignaturas
                foreach ($coursesInGroup as $course) {
                    // Obtener los datos de las asignaturas para generar pdf
                    $grades = $dataGrade->getTrimCampusGroupCourse($trim, $campus, $group, $course['CursoId']);

                    $titleText = '<b>CENTRO EDUCATIVO ESPAÑOL DE LA HABANA</b>';
                    $trimText = 'Informe de calificaciones Trimestre ' . $trim;
                    $courseText = '<b>Asignatura:</b> ' . $dataGrade->getCourseName($course['CursoId'])['Curso'];
                    $groupText = '<b>Grupo:</b> ' . $group;
                    $title = 'CEEH ' . $trimText . ' ' . $dataGrade->getCourseName($course['CursoId'])['Curso'];

                    $html = '<html>';
                    $html .= '<head>';
                    $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
                    $html .= '<meta name="description" content="MdlBol: Boletines de calificaciones de Moodle CEEH">';
                    $html .= '<meta name="keywords" content="CEEH, Moodle CEEH, Boletines de calificaciones CEEH">';
                    $html .= '<meta name="author" content="Centro Educativo Español de La Habana">';
                    $html .= '<link rel="stylesheet" type="text/css" href="assets/css/mdlbol.min.css">';
                    $html .= '<link rel="stylesheet" type="text/css" href="assets/css/mdlbol-pdf.min.css">';
                    $html .= '<title>';
                    $html .= $title;
                    $html .= '</title>';
                    $html .= '</head>';
                    $html .= '<body>';
                    $html .= '<header>';
                    $html .= '<img src="assets/images/pdf_logo.png">';
                    $html .= '</header>';
                    $html .= '<footer class="subtitle is-7">';
                    $html .= $title;
                    $html .= '</footer>';
                    $html .= '<main>';
                    $html .= '<h3 id="titleText" class="subtitle is-4 has-text-info-dark">' . $titleText . '</h3>';
                    $html .= '<h5 id="trimText" class="subtitle is-5 has-text-dark"><b>' . $trimText  . '</b></h5>';
                    $html .= '<h5 id="courseText" class="subtitle is-6 has-text-dark">' . $courseText  . '</h5>';
                    $html .= '<h5 id="groupText" class="subtitle is-6 has-text-dark">' . $groupText  . '</h5>';
                    $html .= '<table class="table is-fullwidth">';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th class="has-text-left" style="width: 25%;">Nombre y Apellidos</th>';
                    $html .= '<th style="width: 15%;">Calificación</th>';
                    $html .= '<th>Comentario</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';
                    foreach ($grades as $g) {
                        $html .= '<tr>';
                        $html .= '<td>' . $g['firstname'] . ' ' . $g['lastname'] . '</td>';
                        $html .= '<td class="has-text-centered">' . round($g['NotaTrimestre'], 1) . '</td>';
                        $hasFeedback = $dataFeedback->hasFeedbackCidead($g['userid'], $course['CursoId']);
                        if ((int)$hasFeedback['count'] == 1) {
                            $html .= '<td style="text-align: justify;">' . $dataFeedback->getFeedbackCidead($trim, $campus, $group, $course['CursoId'], $g['userid'])['feedback_cidead'] . '</td>';
                        } else {
                            $html .= '<td style="text-align: justify;">' . $g['TrimestreObservaciones'] . '</td>';
                        }
                        $html .= '</tr>';
                    }
                    $html .= '</tbody>';
                    $html .= '</table>';
                    $html .= '</main>';
                    $html .= '</body>';
                    $html .= '</html>';

                    $dompdf = new Dompdf();
                    $dompdf->getOptions()->setIsRemoteEnabled(TRUE);
                    $dompdf->getOptions()->set('isHtml5ParserEnabledd' . TRUE);
                    $dompdf->getOptions()->set('isPhpEnabledd' . TRUE);
                    $dompdf->getOptions()->setChroot(APPDIR . '/public/');
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('letter', 'landscape');
                    $dompdf->render();

                    // Guardar el archivo pdf en un directorio
                    $output = $dompdf->output();
                    file_put_contents($pdfDir . $group . '_' . $dataGrade->getCourseName($course['CursoId'])['Curso'] . '.pdf', $output);
                } 

                // Archivo zip
                $zipfile = APPDIR . '/public/' . $group . "_Boletines_Asignaturas.zip";

                // Si existe el archivo \
                // (quedó por algún error HTTP o el usuario detuvo la carga en el navegador) \
                if (is_file($zipfile)) {
                    // Se elimina para evitar conflictos
                    unlink($zipfile);

                    // Si no entonces se procede a generar el zip con los boletines
                } else {
                    // Nuena clase zip
                    $zip = new \ZipArchive;
                    if ($zip->open($zipfile, \ZipArchive::CREATE) === TRUE) {
                        $dir = opendir($pdfDir);
                        while ($file = readdir($dir)) {
                            if (is_file($pdfDir . $file)) {
                                $zip->addFile($pdfDir . $file, $file);
                            }
                        }
                        $zip->close();
                    }
                }

                // Se borran los pdf, ya están en el zip
                $dir = opendir($pdfDir);
                while ($file = readdir($dir)) {
                    if (is_file($pdfDir . $file)) {
                        unlink($pdfDir . $file);
                    }
                }

                // Se descarga el zip
                if (file_exists($zipfile)) {
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Cache-Control: private', false);
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($zipfile)) . ' GMT');
                    header('Content-Type: application/zip');
                    header('Content-Disposition: inline; filename=' .  basename($group . '_Boletines_Asignaturas.zip'));
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . filesize($zipfile));

                    flush();
                    readfile($zipfile);
                    sleep(2);
                    // Luego de que se descargue se borra el zip, ya no es necesario
                    unlink($zipfile);
                    die();
                } else {
                    $this->send(404);
                    die();
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Generación masiva de boletines de estudiantes
    public function genBulkPdfStudent()
    {
        // Hay usuario autenticado?
        if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) {
            // Modelo Grade
            $data = new Grade();

            // 1er parámetro de la URI (trimestre)
            $trim = $this->request()->getSegment(0);

            // 2do parámetro de la URI (sede)
            $campus = $this->request()->getSegment(1);

            // 3er parámetro de la URI (grupo)
            $group = $this->request()->getSegment(2);

            $campusGroup = $campus . '/' . $group;

            // Mostrar las notas si la sede y el trimestre devuelven datos
            if ($this->translateTrim($trim) && $this->parseCampus($campus) && $this->parseGroup($group) && $this->parseCampusGroup($campusGroup)) {
                $trim = $this->translateTrim($trim);
                $group = $this->parseGroup($group);

                $studentsInGroup = $data->getStudentsInGroup($group);

                // Directorio dónde se guardarán los pdf temporales
                $pdfDir = APPDIR . '/pdf/' . strtoupper($campus) . '/' . $group . '/Estudiantes/';

                // Si el directorio no existe se crea
                if (!is_dir($pdfDir)) {
                    mkdir($pdfDir, 0700, true);
                }

                // Iterar por cada estudiante en el grado para obtener los pdf con las asignaturas
                foreach ($studentsInGroup as $student) {
                    // Obtener los datos de los estudiantes para generar pdf
                    $grades = $data->getTrimCampusGroupStudent($trim, $campus, $group, $student['id']);

                    $titleText = '<b>CENTRO EDUCATIVO ESPAÑOL DE LA HABANA</b>';
                    $trimText = 'Informe de calificaciones Trimestre ' . $trim;
                    $studText = '<b>Estudiante:</b> ' . $data->getStudentName($student['id'])['username'];
                    $groupText = '<b>Grupo:</b> ' . $group;
                    $title = 'CEEH ' . $trimText . ' ' . $data->getStudentName($student['id'])['username'];

                    $html = '<html>';
                    $html .= '<head>';
                    $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
                    $html .= '<meta name="description" content="MdlBol: Boletines de calificaciones de Moodle CEEH">';
                    $html .= '<meta name="keywords" content="CEEH, Moodle CEEH, Boletines de calificaciones CEEH">';
                    $html .= '<meta name="author" content="Centro Educativo Español de La Habana">';
                    $html .= '<link rel="stylesheet" type="text/css" href="assets/css/mdlbol.min.css">';
                    $html .= '<link rel="stylesheet" type="text/css" href="assets/css/mdlbol-pdf.min.css">';
                    $html .= '<title>';
                    $html .= $title;
                    $html .= '</title>';
                    $html .= '</head>';
                    $html .= '<body>';
                    $html .= '<header>';
                    $html .= '<img src="assets/images/pdf_logo.png">';
                    $html .= '</header>';
                    $html .= '<footer class="subtitle is-7">';
                    $html .= $title;
                    $html .= '</footer>';
                    $html .= '<main>';
                    $html .= '<h3 id="titleText" class="subtitle is-4 has-text-info-dark">' . $titleText . '</h3>';
                    $html .= '<h5 id="trimText" class="subtitle is-5 has-text-dark"><b>' . $trimText  . '</b></h5>';
                    $html .= '<h5 id="studText" class="subtitle is-6 has-text-dark">' . $studText  . '</h5>';
                    $html .= '<h5 id="groupText" class="subtitle is-6 has-text-dark">' . $groupText  . '</h5>';
                    $html .= '<table class="table is-fullwidth">';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th class="has-text-left" style="width: 25%;">Asignatura</th>';
                    $html .= '<th style="width: 15%;">Calificación</th>';
                    $html .= '<th>Comentario</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';
                    foreach ($grades as $g) {
                        $html .= '<tr>';
                        $html .= '<td>' . $g['Curso'] . '</td>';
                        $html .= '<td class="has-text-centered">' . round($g['NotaTrimestre'], 1) . '</td>';
                        $html .= '<td style="text-align: justify;">' . $g['TrimestreObservaciones'] . '</td>';
                        $html .= '</tr>';
                    }
                    $html .= '</tbody>';
                    $html .= '</table>';
                    $html .= '</main>';
                    $html .= '</body>';
                    $html .= '</html>';

                    $dompdf = new Dompdf();
                    $dompdf->getOptions()->setIsRemoteEnabled(TRUE);
                    $dompdf->getOptions()->set('isHtml5ParserEnabledd' . TRUE);
                    $dompdf->getOptions()->set('isPhpEnabledd' . TRUE);
                    $dompdf->getOptions()->setChroot(APPDIR . '/public/');
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('letter', 'landscape');
                    $dompdf->render();

                    // Guardar el archivo pdf en un directorio
                    $output = $dompdf->output();
                    file_put_contents($pdfDir . $group . '_' . $data->getStudentName($student['id'])['username'] . '.pdf', $output);
                }
                // Archivo zip
                $zipfile = APPDIR . '/public/' . $group . "_Boletines_Estudiantes.zip";

                // Si existe el archivo \
                // (quedó por algún error HTTP o el usuario detuvo la carga en el navegador) \
                if (is_file($zipfile)) {
                    // Se elimina para evitar conflictos
                    unlink($zipfile);

                    // Si no entonces se procede a generar el zip con los boletines
                } else {
                    // Nuena clase zip
                    $zip = new \ZipArchive;
                    if ($zip->open($zipfile, \ZipArchive::CREATE) === TRUE) {
                        $dir = opendir($pdfDir);
                        while ($file = readdir($dir)) {
                            if (is_file($pdfDir . $file)) {
                                $zip->addFile($pdfDir . $file, $file);
                            }
                        }
                        $zip->close();
                    }
                }

                // Se borran los pdf, ya están en el zip
                $dir = opendir($pdfDir);
                while ($file = readdir($dir)) {
                    if (is_file($pdfDir . $file)) {
                        unlink($pdfDir . $file);
                    }
                }

                // Se descarga el zip
                if (file_exists($zipfile)) {
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Cache-Control: private', false);
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($zipfile)) . ' GMT');
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename=' . basename($group . '_Boletines_Estudiantes.zip'));
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . filesize($zipfile));

                    flush();
                    readfile($zipfile);
                    sleep(2);
                    // Luego de que se descargue se borra el zip, ya no es necesario
                    unlink($zipfile);
                    die();
                } else {
                    $this->send(404);
                    die();
                }
            }
        } else {
            // No hay usuario autenticado, se redirige al login
            $this->redirect(BASE_URL . '/users/login');
        }
    }

    // Función helper para traducir el parametro trim a numeros romanos
    private function translateTrim($t)
    {
        if ($t == 'first-trim') {
            return $t = 'I';
        } elseif ($t == 'second-trim') {
            return $t = 'II';
        } elseif ($t == 'third-trim') {
            return $t = 'III';
        } else {
            throw new Exception("Error: La URI está mal declarada, '" . $t . "' no existe como parámetro válido");
        }
        return $this;
    }

    // Función helper para comprobar el parametro campus
    private function parseCampus($c)
    {
        if (($c == 'primaria') || ($c == 'eso') || ($c == 'bach')) {
            return true;
        } else {
            throw new Exception("Error: La URI está mal declarada, '" . $c . "' no existe como parámetro válido");
        }
        return $this;
    }

    // Función helper para traducir el parametro group al formato en la BD
    private function parseGroup($gr)
    {
        if (preg_match('/^[1-6]-EP$/', $gr)) {
            $part = explode("-", $gr);
            return $part[0] . $part[1];
        } elseif (preg_match('/^[1-4]-ESO$/', $gr)) {
            $part = explode("-", $gr);
            return $part[0] . $part[1];
        } elseif (preg_match('/^[1-2]-BACH$/', $gr)) {
            $part = explode("-", $gr);
            return $part[0] . $part[1];
        } else {
            throw new Exception("Error: La URI está mal declarada, '" . $gr . "' no existe como parámetro válido");
        }
        return $this;
    }

    // Función helper para comprobar que el grupo pertenece a la sede
    private function parseCampusGroup($cgr)
    {
        if ((preg_match('/^primaria\/[1-6]-EP$/', $cgr))) {
            $part = explode("/", $cgr);
            return $part[1];
        } elseif (preg_match('/^eso\/[1-4]-ESO$/', $cgr)) {
            $part = explode("/", $cgr);
            return $part[1];
        } elseif (preg_match('/^bach\/[1-2]-BACH$/', $cgr)) {
            $part = explode("/", $cgr);
            return $part[1];
        } else {
            $part = explode("/", $cgr);
            throw new Exception("Error: La sede " . strtoupper($part[0]) . " y el grupo " . $this->parseGroup($part[1]) . " no están relacionados");
        }
        return $this;
    }

    // Función helper para comprobar que la asignatura pertenece al grupo
    private function parseGroupCourse($grc)
    {
        // Modelo Grade
        $data = new Grade();

        if ((preg_match('/^[1-6]-EP\/course\/([1-9])*/', $grc))) {
            $part = explode("/", $grc);
            $campus = $data->getCoursesInCampus('Primaria');
            if (in_array($part[2], array_column($campus, 'CursoId'))) {
                return $part[2];
            } else {
                throw new Exception('Error: El grupo ' . strtoupper($part[0]) . ' y la asignatura "' . $data->getCourseName($part[2])['Curso'] . '" no están relacionados');
            }
        } elseif (preg_match('/^[1-4]-ESO\/course\/([1-9])*/', $grc)) {
            $part = explode("/", $grc);
            $campus = $data->getCoursesInCampus('ESO');
            if (in_array($part[2], array_column($campus, 'CursoId'))) {
                return $part[2];
            } else {
                throw new Exception('Error: El grupo ' . strtoupper($part[0]) . ' y la asignatura "' . $data->getCourseName($part[2])['Curso'] . '" no están relacionados');
            }
        } elseif (preg_match('/^[1-2]-BACH\/course\/([1-9])*/', $grc)) {
            $part = explode("/", $grc);
            $campus = $data->getCoursesInCampus('BACH');
            if (in_array($part[2], array_column($campus, 'CursoId'))) {
                return $part[2];
            } else {
                throw new Exception('Error: El grupo ' . strtoupper($part[0]) . ' y la asignatura "' . $data->getCourseName($part[2])['Curso'] . '" no están relacionados');
            }
        }
        return $this;
    }

    // Función helper para comprobar que el estudiante pertenece a la asignatura
    private function parseCourseStud($c, $st)
    {
        // Modelo Grade
        $data = new Grade();

        $users = $data->getStudentsInCourse($c);
        if (preg_match('/^[0-9]+$/', $c) && preg_match('/^[0-9]+$/', $st) && in_array($st, array_column($users, 'userid'))) {
            return $st;
        } else {
            throw new Exception('Error: "' . $data->getCourseName($c)['Curso'] . '" y "' . $data->getStudentName($st)['username'] . '" no guardan relación');
        }
        return $this;
    }
}
