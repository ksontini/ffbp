<?php

namespace Services;


class UserService extends BaseService
{

    public function connect($login, $pwd)
    {
        $connect =  $this->f3->get('auth')->login($login, $this->hash($pwd));

        if ($connect)
        {
            $user = $this->f3->get('model.administrateur')->findBy(array("email=?", $login));
            $user->last_ip_connect = Tools::getClientIp();
            $user->last_date_time_connect = date("Y-m-d H:i:s");
            $user->save();
            $UserSession = array(
                "id_administrateur" => $user->id_administrateur,
                "email" => $user->email,
                "nom" => $user->nom,
                "prenom" => $user->prenom,
                "gestion_administrateur" => $user->gestion_administrateur,
                "gestion_ville" => $user->gestion_ville,
                "gestion_categorie_client" => $user->gestion_categorie_client,
                "gestion_type_chambre" => $user->gestion_type_chambre,
                "gestion_langue" => $user->gestion_langue,
                "gestion_client" => $user->gestion_client,
                "gestion_fournisseur" => $user->gestion_fournisseur,
                "gestion_reservation" => $user->gestion_reservation,
                "gestion_statistique" => $user->gestion_statistique,
            );

            $this->f3->set('SESSION.user',json_encode($UserSession));
        } else
            $this->f3->get('logger')->write("attend to authenticate failed to $login from " . Tools::getClientIp());

        return $connect;
    }

    public function hash($password, $salt= '$1$#HoTTEls?Ger96i?I.S3AsG0IbaieCP/RteBook!?ing**')
    {
        if ((is_null($salt)) || (strlen($salt) < 1)) {
            $salt = '';
            while (strlen($salt) < 10)
                $salt.=chr(rand(64, 126));
            $salt = '$G5weRHGIoNInx8ttEl$' . $salt . '$';
        }
        if ($salt{0} != '$')
            return crypt($password, $salt);
        $tmp = explode('$', $salt);
        if ($tmp[1] != 'G5weRHGIoNInx8ttEl')
            return crypt($password, $salt);
        $saltstr = $tmp[2];
        if (strlen($saltstr) != 10)
            return crypt($password, $salt);
        $encrypt = base64_encode(sha1($saltstr . $password, true));
        return '$G5weRHGIoNInx8ttEl$' . $saltstr . '$' . $encrypt;
    }

    public function isConnected()
    {
        return $this->f3->exists('SESSION.user');
    }

    public function disconnect()
    {
        $this->f3->clear('SESSION');
    }

}