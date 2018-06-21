<?php

namespace Models;


class Administrateur extends ModelBase
{
    protected $tableName='administrateur';
    public $nomComplet ;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->administrateur = "select concat(prenom, ' ', nom ) from administrateur where id_admin_last_update=administrateur.id_administrateur";
    }




}