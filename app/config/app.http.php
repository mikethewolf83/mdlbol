<?php

return [
    'routes' => [
        // Raíz de la aplicación
        '[/]' => [
            'controller' => 'MdlBol\Http\Controller\HomeController',
            'action'     => 'home'
        ],
        // Login de usuarios
        '/users/login' => [
            'controller' => 'MdlBol\Http\Controller\UsersController',
            'action'     => 'login'
        ],
        // Logout de usuarios
        '/users/logout' => [
            'controller' => 'MdlBol\Http\Controller\UsersController',
            'action'     => 'logout'
        ],
        // Endpoint de admins
        '/admin[/]' => [
            'controller' => 'MdlBol\Http\Controller\AdminController',
            'action'     => 'admin'
        ],
        // Eliminar usuarios que pueden acceder a la aplicación
        '/admin/delete-user/:user[/]' => [
            'controller' => 'MdlBol\Http\Controller\AdminController',
            'action'     => 'deleteValidUser'
        ],
        // Eliminar usuarios admins de la aplicación
        '/admin/delete-admin/:user[/]' => [
            'controller' => 'MdlBol\Http\Controller\AdminController',
            'action'     => 'deleteValidUser'
        ],
        // Endpoint de cada grupo
        '/:trim/:campus/:group/' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'gradesByTrimCampusGroup'
        ],
        // Generación masiva de boletines de asignatutas (CIDEAD)
        '/:trim/:campus/:group/courses-pdf[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'genBulkPdfCourse'
        ],
        // Generación masiva de boletines de estudiantes
        '/:trim/:campus/:group/students-pdf[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'genBulkPdfStudent'
        ],
        // Endpoint de cada asignatura
        '/:trim/:campus/:group/course/:course[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'gradesByTrimCampusGroupCourse'
        ],
        // Agregar comentario al CIDEAD en la vista de la asignatura
        '/:trim/:campus/:group/course/:course/student/:student/add-feedback-cidead[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'addStudentFeedbackCidead'
        ],
        // Editar comentario al CIDEAD en la vista de la asignatura
        '/:trim/:campus/:group/course/:course/student/:student/edit-feedback-cidead[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'editStudentFeedbackCidead'
        ],
        // Eliminar comentario al CIDEAD en la vista de la asignatura con request query en el controlador
        '/:trim/:campus/:group/course/:course/delete-feedback-cidead[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'gradesByTrimCampusGroupCourse'
        ],
        // Editar el comentario del estudiante en la vista de la asignatura
        '/:trim/:campus/:group/course/:course/student/:student/edit-feedback[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'editStudentCourseFeedback'
        ],
        // Editar el comentario del estudiante en la vista del estudiante
        '/:trim/:campus/:group/student/:student/course/:course/edit-feedback[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'editStudentFeedback'
        ],
        // Endpoint de cada estudiante
        '/:trim/:campus/:group/student/:student[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'gradesByTrimCampusGroupStudent'
        ],
        // Generación de boletín individual de cada asignatura
        '/:trim/:campus/:group/course/:course/pdf[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'genPdfCourse'
        ],
        // Generación de boletín individual de cada estudiante
        '/:trim/:campus/:group/student/:student/pdf[/]' => [
            'controller' => 'MdlBol\Http\Controller\GradesController',
            'action'     => 'genPdfStudent'
        ],
        // Si no es una de las url anteriores entonces se muestra un error
        '*' => [
            'controller' => 'MdlBol\Http\Controller\HomeController',
            'action'     => 'error'
        ]
    ],
    'database' => include __DIR__ . '/database.php'
];
