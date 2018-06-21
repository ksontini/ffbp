<?php

namespace Controllers;


use Whoops\Exception\ErrorException;

class UserController extends  BaseController
{
    protected $modelName   = 'model.administrateur';

    protected $listView    = 'User/index.html';
    protected $formView    = 'User/add.html';
    protected $detailView  = 'User/update.html';

    protected $accessRight =  "gestion_administrateur";

    public function showAll()
    {
        $this->setBreadcrumbs("Menu général" ,"#", true);
        $this->setBreadcrumbs("Gestion des administrateurs", "#", true);

        $page = 1;
        if (isset ($_GET['page']))
            $page = $_GET['page'];
        $object = $this->f3->get($this->modelName)->findAll($page-1);
        $this->f3->set('list', $object);
        $this->render($this->listView);
    }

    public function rights()
    {
        $this->setBreadcrumbs("Menu général" ,"#", true);
        $this->setBreadcrumbs("Gestion des administrateurs", "/user");
        $this->setBreadcrumbs("Droits administrateur  ", "#",true);

        $obj = $this->loadObject();

        if (isset($_POST)) {
            $obj->copyFrom('POST');
            $obj->save();
        }

        $this->f3->set('object', $obj);
        $this->render('User/rights.html');
    }

    public function update()
    {
        $this->setBreadcrumbs("Menu général" ,"#", true);
        $this->setBreadcrumbs("Gestion des administrateurs", "#", true);

        $object = $this->loadObject();

        $object->copyFrom('POST');
        if ($object->mot_passe) {
            $object->mot_passe = $this->f3->get('service.user')->hash($object->mot_passe);
        }

        $object->save();
        $this->f3->set('object', $object);
        $this->render($this->detailView);
    }


    public function save()
    {

        $this->f3->get($this->modelName)->model->copyFrom('POST');
        $obj = $this->f3->get($this->modelName)->model;

        if ($obj->mot_passe) {
            $obj->mot_passe = $this->f3->get('service.user')->hash($obj->mot_passe);
        }

        $obj->save();
        $this->f3->set('object', $obj);
        $this->render($this->detailView);
    }
}