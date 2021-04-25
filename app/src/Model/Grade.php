<?php

namespace MdlBol\Model;

use Pop\Db\Db;
use Pop\Model\AbstractModel;
use MdlBol\Controller\AbstractController as Ac;
use MdlBol\Exception;

class Grade extends AbstractModel
{
    private $db;

    public function __construct()
    {
        $this->db = Db::connect(
            $_ENV['DB_ADAPTER'],
            [
                'database' => $_ENV['DB_NAME'],
                'username' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASSWORD'],
                'host'     => $_ENV['DB_HOST'],
                'type'     => $_ENV['DB_TYPE'],
                'options'   => [
                    $_ENV['DB_OPTIONS'],
                ]
            ]
        );
    }

    /* BEGIN Por grupo */
    public function getCoursesInGroup($group)
    {
        $group = preg_replace('/^[1-9](?=[A-Z])/', '$0 ', $group);
        $this->db->prepare('SELECT `cursoprofesores`.`CursoId`, `cursoprofesores`.`Curso` FROM `cursoprofesores` WHERE `cursoprofesores`.`CursoId` IN (SELECT DISTINCT `pivotnotas`.`courseid` FROM `pivotnotas`) AND `cursoprofesores`.`CatCurso` = ? AND `cursoprofesores`.`Curso` NOT LIKE "Tutoría%" AND `cursoprofesores`.`Curso` NOT LIKE "Orientación Educativa%" AND `cursoprofesores`.`Curso` NOT LIKE "%Lector%" ORDER BY `Curso` ASC');

        $params = ['cursoprofesores.CatCurso' => $group];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getStudentsInGroup($group)
    {
        $this->db->prepare('SELECT `cohorts_users`.`id`, `cohorts_users`.`Cohorte`, CONCAT(`cohorts_users`.`firstname`, " ", `cohorts_users`.`lastname`) AS username FROM `cohorts_users` WHERE `cohorts_users`.`id` IN (SELECT DISTINCT `pivotnotas`.`userid` FROM `pivotnotas`) AND `cohorts_users`.`Cohorte` = ? ORDER BY username ASC');

        $params = ['cohorts_users.Cohorte' => $group];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }
    /* END Por grupo */

    /* BEGIN Por asignatura */
    public function getTrimCampusGroupCourse($trim, $campus, $group, $course)
    {
        $this->db->prepare('SELECT `pivotnotas`.*, `cohorts_users`.*, `cursoprofesores`.* FROM `pivotnotas` JOIN `cohorts_users` ON (`cohorts_users`.`id` = `pivotnotas`.`userid`) JOIN `cursoprofesores` ON (`pivotnotas`.`courseid` = `cursoprofesores`.`CursoId`) WHERE(`pivotnotas`.`Trimestre` = ?) AND (`cursoprofesores`.`ParentCatCurso` = ?)  AND (`cohorts_users`.`Cohorte` = ?)  AND (`cursoprofesores`.`CursoId` = ?) AND (`pivotnotas`.`NotaTrimestre` != "") AND (`pivotnotas`.`TrimestreObservaciones` != "")');

        $params = [
            'pivotnotas.Trimestre'           => $trim,
            'cursoprofesores.ParentCatCurso' => $campus,
            'cohorts_users.Cohorte'          => $group,
            'cursoprofesores.CursoId'        => $course
        ];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getCourseName($course)
    {
        $this->db->prepare('SELECT `cursoprofesores`.`CursoId`, `cursoprofesores`.`Curso` FROM `cursoprofesores` WHERE `cursoprofesores`.`CursoId` = ? LIMIT 1');

        $params = ['cursoprofesores.CursoId' => $course];
        $this->db->bindParams($params);
        $this->db->execute();

        if ($this->db->getNumberOfRows() > 0) {
            return $this->db->fetch();
        } else {
            throw new Exception("La asignatura con id " . $course . " no existe");
        }
    }

    public function getCoursesInCampus($campus)
    {
        $this->db->prepare('SELECT `cursoprofesores`.`CursoId` FROM `cursoprofesores` WHERE `cursoprofesores`.`ParentCatCurso` = ?');

        $params = ['cursoprofesores.ParentCatCurso' => $campus];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getStudentsInCourse($course)
    {
        $this->db->prepare('SELECT `pivotnotas`.`userid` FROM `pivotnotas` WHERE `pivotnotas`.`courseid` = ?');

        $params = ['pivotnotas.courseid' => $course];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }
    /* END Por asignatura */

    /* BEGIN Por estudiante */
    public function getTrimCampusGroupStudent($trim, $campus, $group, $student)
    {
        $this->db->prepare('SELECT `pivotnotas`.*, `cohorts_users`.*, `cursoprofesores`.* FROM `pivotnotas` JOIN `cohorts_users` ON (`cohorts_users`.`id` = `pivotnotas`.`userid`) JOIN `cursoprofesores` ON (`pivotnotas`.`courseid` = `cursoprofesores`.`CursoId`) WHERE(`pivotnotas`.`Trimestre` = ?) AND (`cursoprofesores`.`ParentCatCurso` = ?)  AND (`cohorts_users`.`Cohorte` = ?)  AND (`cohorts_users`.`id` = ?) AND (`pivotnotas`.`NotaTrimestre` != "") AND (`pivotnotas`.`TrimestreObservaciones` != "")');

        $params = [
            'pivotnotas.Trimestre'           => $trim,
            'cursoprofesores.ParentCatCurso' => $campus,
            'cohorts_users.Cohorte'          => $group,
            'cursoprofesores.CursoId'        => $student
        ];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getStudentName($student)
    {
        $this->db->prepare('SELECT CONCAT(`cohorts_users`.`firstname`, " ", `cohorts_users`.`lastname`) AS username FROM `cohorts_users` WHERE `cohorts_users`.`id` = ? LIMIT 1');

        $params = ['cohorts_users.id' => $student];
        $this->db->bindParams($params);
        $this->db->execute();

        if ($this->db->getNumberOfRows() > 0) {
            return $this->db->fetch();
        } else {
            throw new Exception("El estudiante con id " . $student . " no existe");
        }
    }
    /* END Por estudiante */

    /* BEGIN Dashborad */
    public function getStudentsAmountCampus($campus)
    {
        $this->db->prepare("SELECT COUNT(`id`) AS count FROM `cohorts_users` WHERE `Cohorte` LIKE ? LIMIT 1");

        $params = ['Cohorte' => "%$campus"];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }

    public function getCoursesAmountCampus($campus)
    {
        $this->db->prepare('SELECT COUNT(`cursoprofesores`.`CursoId`) AS count FROM `cursoprofesores` WHERE `cursoprofesores`.`ParentCatCurso` = ? LIMIT 1');

        $params = ['cursoprofesores.ParentCatCurso' => $campus];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }
    /* END Dashborad */
}
