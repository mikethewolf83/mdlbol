<?php

namespace MdlBol\Model;

use Pop\Db\Db;
use Pop\Model\AbstractModel;

class Feedback extends AbstractModel
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

    public function getFeedbackMdl($trim, $campus, $group, $course, $student)
    {
        $this->db->prepare('SELECT `mdl_grade_grades`.`id`, `mdl_grade_grades`.`feedback` FROM `pivotnotas` JOIN `cohorts_users` ON(`cohorts_users`.`id` = `pivotnotas`.`userid`) JOIN `cursoprofesores` ON(`pivotnotas`.`courseid` = `cursoprofesores`.`CursoId`) JOIN `mdl_grade_items` ON(`pivotnotas`.`courseid` = `mdl_grade_items`.`courseid`) JOIN `mdl_grade_grades` ON(`pivotnotas`.`userid` = `mdl_grade_grades`.`userid`) WHERE (`pivotnotas`.`Trimestre` = ?) AND(`cursoprofesores`.`ParentCatCurso` = ?) AND(`cohorts_users`.`Cohorte` = ?) AND(`cursoprofesores`.`CursoId` = ?) AND(`pivotnotas`.`NotaTrimestre` != "") AND(`pivotnotas`.`TrimestreObservaciones` != "") AND(`mdl_grade_items`.`id` = `mdl_grade_grades`.`itemid`) AND(`pivotnotas`.`TrimestreObservaciones` = `mdl_grade_grades`.`feedback`) AND(`mdl_grade_grades`.`userid` = ?)');

        $params = [
            'pivotnotas.Trimestre'           => $trim,
            'cursoprofesores.ParentCatCurso' => $campus,
            'cohorts_users.Cohorte'          => $group,
            'cursoprofesores.CursoId'        => $course,
            'mdl_grade_grades.userid'        => $student
        ];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }

    /**
    * Por algún bug del framework no se pudieron utilizar los métodos de la implementación
    * propia de PDO de este framework, en este caso para la función UPDATE e INSERT de MySQL,
    * para crear una función como las demás. En cambio se empleó PDO nativo de PHP.
    */

    // Actualizar o agregar comentario del estudiante
    public function updateFeedbackMdl($feedback, $trim, $campus, $group, $course, $student)
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        
        $stmt = $pdo->prepare('UPDATE `mdl_grade_grades` SET `feedback` = :feedback WHERE `id` = :id');
        $stmt->bindValue(':feedback', $feedback);
        $stmt->bindValue(':id', $this->getFeedbackMdl($trim, $campus, $group, $course, $student)['id']);
        return $stmt->execute();
    }

    // Agregar comentario al CIDEAD
    public function addFeedbackCidead($student, $trim, $campus, $group, $course, $feedback)
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        
        $stmt = $pdo->prepare('INSERT INTO `mdlbol_feedback_cidead` (`mdl_grade_grades_id`, `mdl_user_id`, `trimester`, `campus`, `group`, `mdl_course_id`, `feedback_cidead`) VALUES (:mdl_grade_grades_id, :mdl_user_id, :trimester, :campus, :group, :mdl_course_id, :feedback_cidead)');
        $stmt->bindValue(':mdl_grade_grades_id', $this->getFeedbackMdl($trim, $campus, $group, $course, $student)['id']);
        $stmt->bindValue(':mdl_user_id', $student);
        $stmt->bindValue(':trimester', $trim);
        $stmt->bindValue(':campus', $campus);
        $stmt->bindValue(':group', $group);
        $stmt->bindValue(':mdl_course_id', $course);
        $stmt->bindValue(':feedback_cidead', $feedback);
        return $stmt->execute();
    }

    // Actualizar comentario al CIDEAD
    public function updateFeedbackCidead($student, $trim, $campus, $group, $course, $feedback)
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        
        $stmt = $pdo->prepare('UPDATE `mdlbol_feedback_cidead` SET `feedback_cidead` = :feedback_cidead WHERE `mdl_grade_grades_id` = :mdl_grade_grades_id');
        $stmt->bindValue(':feedback_cidead', $feedback);
        $stmt->bindValue(':mdl_grade_grades_id', $this->getFeedbackMdl($trim, $campus, $group, $course, $student)['id']);
        return $stmt->execute();
    }

    // Obtener el comentario al CIDEAD
    public function getFeedbackCidead($trim, $campus, $group, $course, $student)
    {
        /*$pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        
        $stmt = $pdo->prepare('SELECT `feedback_cidead` FROM `mdlbol_feedback_cidead` WHERE `mdl_grade_grades_id` = :grades_id AND `mdl_user_id` = :userid');
        $stmt->bindValue(':grades_id', $this->getFeedbackMdl($trim, $campus, $group, $course, $student)['id']);
        $stmt->bindValue(':userid', $student);
        return $stmt->execute();*/

        $gradesId = $this->getFeedbackMdl($trim, $campus, $group, $course, $student)['id'];

        $this->db->prepare("SELECT `feedback_cidead` FROM `mdlbol_feedback_cidead` WHERE `mdl_grade_grades_id` = ? AND `mdl_user_id` = ?");

        $params = [
            'mdl_grade_grades_id' => $gradesId,
            'mdl_user_id'         => $student
        ];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }
    // Tiene comentario al CIDEAD?
    public function hasFeedbackCidead($student, $course)
    {
        // $this->db->prepare("SELECT `mdl_user_id`, `mdl_course_id` FROM `mdlbol_feedback_cidead` WHERE `mdl_user_id` = ? AND `mdl_course_id` = ?");

        $this->db->prepare("SELECT COUNT(*) AS `count` FROM `mdlbol_feedback_cidead` WHERE `mdl_user_id` = ? AND `mdl_course_id` = ?");

        $params = [
            'mdl_user_id'   => $student,
            'mdl_course_id' => $course
        ];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }

    // Eliminar comentario al CIDEAD
    public function deleteFeedbackCidead($trim, $campus, $group, $course, $student)
    {
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        
        $stmt = $pdo->prepare('DELETE FROM `mdlbol_feedback_cidead` WHERE `mdl_grade_grades_id` = :mdl_grade_grades_id');
        $stmt->bindValue(':mdl_grade_grades_id', $this->getFeedbackMdl($trim, $campus, $group, $course, $student)['id']);
        return $stmt->execute();
    }
}
