<?php

namespace MdlBol\Model;

use Pop\Db\Db;
use Pop\Model\AbstractModel;

class UserValid extends AbstractModel
{
    private $db;

    public function __construct()
    {
        $this->db = Db::connect(
            'sqlite',
            [
                'database' => APPDIR . '/database/internal.sqlite3'
            ]
        );
    }

    /* Obtener el admin */
    public function getIsAdmin($user)
    {
        $this->db->prepare('SELECT "username" FROM "mb_admin_user" WHERE "username" = :username LIMIT 1');

        $params = ['username' => $user];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }

    /* Obtener los admins */
    public function getAdminUsers()
    {
        $this->db->query('SELECT * FROM "mb_admin_user"  ORDER BY "username" ASC');
        return $this->db->fetchAll();
    }

    /* Obtener el usuario */
    public function getIsUser($user)
    {
        $this->db->prepare('SELECT "username" FROM "mb_normal_user" WHERE "username" = :username LIMIT 1');

        $params = ['username' => $user];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }

    /* Obtener los users */
    public function getNromalUsers()
    {
        $this->db->query('SELECT * FROM "mb_normal_user" ORDER BY "username" ASC');
        return $this->db->fetchAll();
    }

    /* Eliminar un UserValid */
    public function deleteUser($dbtable, $id)
    {
        $this->db->prepare('DELETE FROM "' . $dbtable . '" WHERE "id" = :id');

        $params = ['id' => $id];
        $this->db->bindParams($params);
        return $this->db->execute();
    }

    /* Validar si el usuario ya existe o no */
    private function getUserExists($dbtable, $username)
    {
        $this->db->prepare('SELECT "username" FROM "' . $dbtable . '" WHERE "username" = :username LIMIT 1');

        $params = ['username' => $username];
        $this->db->bindParams($params);
        $this->db->execute();
        return $this->db->fetch();
    }

    /* Agregar un UserValid */
    public function addUser($dbtable, $username)
    {
        if ($this->getUserExists($dbtable, $username)) {
            return false;
        } else {
            $this->db->prepare('INSERT INTO "' . $dbtable . '" ("username") VALUES(:username)');
            $params = ['username' => $username];
            $this->db->bindParams($params);
            return $this->db->execute();
        }
    }
}
