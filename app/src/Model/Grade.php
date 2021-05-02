<?php

namespace MdlBol\Model;

use Pop\Db\Db;
use Pop\Model\AbstractModel;
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
        $this->db->prepare('SELECT `CursoProfesores`.`CursoId`, `CursoProfesores`.`Curso` FROM `CursoProfesores` WHERE `CursoProfesores`.`CursoId` IN (SELECT DISTINCT `PivotNotas`.`courseid` FROM `PivotNotas`) AND `CursoProfesores`.`CatCurso` = ? AND `CursoProfesores`.`Curso` NOT LIKE "Tutoría%" AND `CursoProfesores`.`Curso` NOT LIKE "Orientación Educativa%" AND `CursoProfesores`.`Curso` NOT LIKE "%Lector%" ORDER BY `Curso` ASC');

        $params = ['CursoProfesores.CatCurso' => $group];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getStudentsInGroup($group)
    {
        $this->db->prepare('SELECT `COHORTS_USERS`.`id`, `COHORTS_USERS`.`Cohorte`, CONCAT(`COHORTS_USERS`.`firstname`, " ", `COHORTS_USERS`.`lastname`) AS username FROM `COHORTS_USERS` WHERE `COHORTS_USERS`.`id` IN (SELECT DISTINCT `PivotNotas`.`userid` FROM `PivotNotas`) AND `COHORTS_USERS`.`Cohorte` = ? ORDER BY username ASC');

        $params = ['COHORTS_USERS.Cohorte' => $group];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }
    /* END Por grupo */

    /* BEGIN Por asignatura */
    public function getTrimCampusGroupCourse($trim, $campus, $group, $course)
    {
        $this->db->prepare('SELECT `PivotNotas`.*, `COHORTS_USERS`.*, `CursoProfesores`.* FROM `PivotNotas` JOIN `COHORTS_USERS` ON (`COHORTS_USERS`.`id` = `PivotNotas`.`userid`) JOIN `CursoProfesores` ON (`PivotNotas`.`courseid` = `CursoProfesores`.`CursoId`) WHERE(`PivotNotas`.`Trimestre` = ?) AND (`CursoProfesores`.`ParentCatCurso` = ?)  AND (`COHORTS_USERS`.`Cohorte` = ?)  AND (`CursoProfesores`.`CursoId` = ?) AND (`PivotNotas`.`NotaTrimestre` != "")');

        $params = [
            'PivotNotas.Trimestre'           => $trim,
            'CursoProfesores.ParentCatCurso' => $campus,
            'COHORTS_USERS.Cohorte'          => $group,
            'CursoProfesores.CursoId'        => $course
        ];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getCourseName($course)
    {
        $this->db->prepare('SELECT `CursoProfesores`.`CursoId`, `CursoProfesores`.`Curso` FROM `CursoProfesores` WHERE `CursoProfesores`.`CursoId` = ? LIMIT 1');

        $params = ['CursoProfesores.CursoId' => $course];
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
        $this->db->prepare('SELECT `CursoProfesores`.`CursoId` FROM `CursoProfesores` WHERE `CursoProfesores`.`ParentCatCurso` = ?');

        $params = ['CursoProfesores.ParentCatCurso' => $campus];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getStudentsInCourse($course)
    {
        $this->db->prepare('SELECT `PivotNotas`.`userid` FROM `PivotNotas` WHERE `PivotNotas`.`courseid` = ?');

        $params = ['PivotNotas.courseid' => $course];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getCoursesWithU3($trim)
    {
        $this->db->prepare('SELECT DISTINCT `courseid` FROM `PivotNotas` WHERE `U3_NotaFinal` IS NOT NULL AND `Trimestre` = ?');

        $params = ['Trimestre`' => $trim];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getCoursesWithU4($trim)
    {
        $this->db->prepare('SELECT DISTINCT `courseid` FROM `PivotNotas` WHERE `U4_NotaFinal` IS NOT NULL AND `Trimestre` = ?');

        $params = ['Trimestre`' => $trim];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getCoursesWithU5($trim)
    {
        $this->db->prepare('SELECT DISTINCT `courseid` FROM `PivotNotas` WHERE `U5_NotaFinal` IS NOT NULL AND `Trimestre` = ?');

        $params = ['Trimestre`' => $trim];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }
    /* END Por asignatura */

    /* BEGIN Por estudiante */
    public function getTrimCampusGroupStudent($trim, $campus, $group, $student)
    {
        $this->db->prepare('SELECT `PivotNotas`.*, `COHORTS_USERS`.*, `CursoProfesores`.* FROM `PivotNotas` JOIN `COHORTS_USERS` ON (`COHORTS_USERS`.`id` = `PivotNotas`.`userid`) JOIN `CursoProfesores` ON (`PivotNotas`.`courseid` = `CursoProfesores`.`CursoId`) WHERE(`PivotNotas`.`Trimestre` = ?) AND (`CursoProfesores`.`ParentCatCurso` = ?)  AND (`COHORTS_USERS`.`Cohorte` = ?)  AND (`COHORTS_USERS`.`id` = ?) AND (`PivotNotas`.`NotaTrimestre` != "")');

        $params = [
            'PivotNotas.Trimestre'           => $trim,
            'CursoProfesores.ParentCatCurso' => $campus,
            'COHORTS_USERS.Cohorte'          => $group,
            'CursoProfesores.CursoId'        => $student
        ];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getStudentName($student)
    {
        $this->db->prepare('SELECT CONCAT(`COHORTS_USERS`.`firstname`, " ", `COHORTS_USERS`.`lastname`) AS username FROM `COHORTS_USERS` WHERE `COHORTS_USERS`.`id` = ? LIMIT 1');

        $params = ['COHORTS_USERS.id' => $student];
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
        $this->db->prepare("SELECT COUNT(`id`) AS cuenta FROM `COHORTS_USERS` WHERE `Cohorte` LIKE ? LIMIT 1");

        $params = ['Cohorte' => "%$campus"];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }

    public function getCoursesAmountCampus($campus)
    {
        $this->db->prepare('SELECT COUNT(`CursoProfesores`.`CursoId`) AS cuenta FROM `CursoProfesores` WHERE `CursoProfesores`.`ParentCatCurso` = ? LIMIT 1');

        $params = ['CursoProfesores.ParentCatCurso' => $campus];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }
    /* END Dashborad */
}
