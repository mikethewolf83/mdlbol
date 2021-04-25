CREATE TABLE IF NOT EXISTS `ceehabana_moodle_db`.`mdlbol_feedback_cidead`(
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `mdl_grade_grades_id` BIGINT NOT NULL,
    `mdl_user_id` BIGINT NOT NULL,
    `trimester` VARCHAR(3) NOT NULL,
    `campus` VARCHAR(50) NOT NULL,
    `group` VARCHAR(20) NOT NULL,
    `mdl_course_id` BIGINT NOT NULL,
    `feedback_cidead` TEXT NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE = INNODB COMMENT = 'Tabla auxiliar para almacenar los comentarios al CIDEAD';