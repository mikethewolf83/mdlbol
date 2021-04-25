<?php

namespace MdlBol\Model;

use Pop\Db\Record;
use Pop\Auth\Table;
use MdlBol\Model\UserValid;

class User extends Record
{
    protected $table = 'user';
    protected $prefix = 'mdl_';
    protected $primaryKeys = ['id'];

    public function loginUser($username, $password)
    {
        $auth = new Table('MdlBol\Model\User');

        // Modelo UserValid
        $data = new UserValid();

        // Buscar si el usuario existe
        $user = $data->getIsUser($username);

        // Si el usuario no es null y machea
        if (!empty($user) && in_array($username, $user)) {
            if ($auth->authenticate($username, $password)) {
                $user = $auth->getUser();
                return $user;
            } else {
                return false;
            }
        }
    }

    public function getUserMdl($id)
    {
        $user = User::findById($id);
        return $user;
    }
}
