<?php
namespace Controllers;

use Services\Tools;

class DefaultController extends BaseController {

    public function login() 
    {
        $this->layout = "layout/layout-general.html";
        $ip = Tools::getClientIp();
        $this->f3->set('ipClient', $ip);
        $this->f3->get('logger')->write("get login");
        $this->render('Default/login.html');
    }

    public function authenticate()
    {
        $this->layout = "layout/layout-general.html";
        $ip = Tools::getClientIp();
        $this->f3->set('ipClient', $ip);
        $this->f3->get('logger')->write("post authenticate");
        //dump($_SERVER['REQUEST_METHOD']);
        $this->f3->get('logger')->write($_POST['username']);
        
        if (!$this->f3->get('service.user')->connect($_POST['username'], $_POST['password'])){
            $this->f3->set('flashMessage', 'Login or password failed');
            $this->render('Default/login.html');
        } else
            $this->f3->reroute('/dashboard'); 
    }

    public function dashbord()
    {
        $this->f3->get('logger')->write('I was here');
        $user = $this->getUser();

        /*** Vos 5 Derniers Dossiers **/
        $criteria['id_admin_last_update'] = $user->id_administrateur;
        $option = array('order'=>'date_heure_creation desc');
        $this->f3->set('lastCmd' , $this->f3->get('service.reservation')->findAll($criteria, 1, 5,$option));

        /*** Vos 5 Prochains Arrivées ***/
        $criteria['etat_reservation !'] = 4;
        $criteria['etat_reservation!'] = 5;
        $criteria['date_du_sejour >']   = date('Y-m-d');
        $this->f3->set('CmdProchainsArrive' , $this->f3->get('service.reservation')->findAll($criteria, 1, 5,array()));

        /*** A Facturer ***/
        $criteria = array();
        $criteria['id_admin_last_update'] = $user->id_administrateur;
        $criteria['etat_reservation !'] = 4;
        $criteria['etat_reservation!'] = 5;
        $criteria['date_au_sejour <']   = date('Y-m-d');
        $this->f3->set('CmdAfacturer' , $this->f3->get('service.reservation')->findAll($criteria, 1, 5,array()));

        /**Confirmations / Options - Clients - Fournisseurs **/

        $this->f3->set('ConfirmationsFournisseurs' , $this->f3->get('service.reservation')->GetConfirmationsOptions("f",$user->id_administrateur,5) );
        $this->f3->set('ConfirmationsClients'      , $this->f3->get('service.reservation')->GetConfirmationsOptions("c",$user->id_administrateur,5) );

        $this->render('Default/dashboard.html');
    }

    public function disconnect()
    {
        $this->f3->get('logger')->write("disconnection of the user " . $this->f3->get('SESSION.user'));
        $this->f3->get('service.user')->disconnect();
        $this->f3->reroute("/");
    }
}
