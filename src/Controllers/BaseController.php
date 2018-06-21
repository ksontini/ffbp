<?php
namespace Controllers;


class BaseController
{
    /**
     * @var \Base
     */
    protected $f3;
    protected $layout;

    protected $serviceName;
    protected $modelName;

    protected $user;

    protected $listView;
    protected $formView;
    protected $detailView;

    protected $accessRight;

    protected $breadcrumbs = array();

    protected $criteria = array();
    protected $sort = array();
    protected $page;
    protected $maxPerPage;

    public function __construct()
    {
        $this->layout = "layout/layout.html";
        $this->f3 = $f3 = \Base::instance();
        $user = json_decode($this->f3->get('SESSION.user'));
        $this->user = $user;
        //dump($user);
        if ($this->accessRight) {
            if (!$user->{$this->accessRight})
            {
                $this->f3->get('logger')->write("access dined for " . $this->accessRight);
                throw new \HttpException(403);
            }
        }
    }

    public function beforeroute() {

        $this->f3->get('service.tools')->createActivity($this->user);

        foreach ($_GET as $field => $value) {
            if ($field !== 'page' && $field !== 'maxPerPage' &&  $value != null && $field != 'order' && $field != 'export') {
                if (!isset($this->criteria[0]))
                    $this->criteria[0] = '';
                else
                    $this->criteria[0] .= ' and ';

                $field = str_replace('__', '.',$field);
                $this->criteria[0] .= "$field=?";
                $this->criteria[] = $value;
                $this->f3->set($field, $value);
            }

            if ( $value != null && $field == 'order') {
                $this->f3->set('sortValue', $value);
                $this->sort = array('order' =>  implode(' ', explode('-', $value)));
            }
        }

        $this->page = isset($_GET['page']) ? $_GET['page'] : 1;
        $this->maxPerPage = isset($_GET['maxPerPage']) ? $_GET['maxPerPage'] : 10;
    }

    public function afterroute() {

    }

    protected function getModelByName($fullModelName) {
        return new $fullModelName($this->f3->get('DB'));
    }

    protected function getModel() {
        return $this->getModelByName($this->modelName);
    }


    public function showAll()
    {
        $model = $this->f3->get($this->modelName);
        $where = array('');
        foreach ($_GET as $field => $value)
        {
            if (isset($model->{$field}) && !empty($value) ) {
                if (count($where)>2) {
                    $where[0] .= " and ";
                }
                $where[0] .= "$field =?";
                $where[] = $value;
            }
        }

        $object = $model->findAll($this->page, $where,$this->sort,$this->maxPerPage);

        $this->f3->set('list', $object);
        $this->f3->get('debugger')->debug("LIST :");
        $this->f3->get('debugger')->debug($object);
        $this->render($this->listView);
    }

    public function get()
    {
        $object = $this->f3->get($this->modelName);
        $this->f3->set('object', $object);
        $this->render($this->formView);
    }

    public function save()
    {
        $this->f3->get($this->modelName)->copyFrom('POST');
        $this->f3->get($this->modelName)->save();
        $this->f3->set('object', $this->f3->get($this->modelName));
        $this->render($this->detailView);
    }

    public function getById()
    {
        $object = $this->loadObject();
        $this->f3->set('object', $object);
        $this->render($this->detailView);
    }

    public function update()
    {
        $object = $this->loadObject();
        $object->copyFrom('POST');
        $object->save();
        $this->f3->set('object', $object);
        $this->render($this->detailView);
    }

    public function delete()
    {
        $this->loadObject()->erase();
        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    protected function loadObject() {
        $id = $this->f3->get('PARAMS.idMontage') ? $this->f3->get('PARAMS.idMontage') : $this->f3->get('PARAMS.id');
        //dump($this->modelName); exit;
        $object = $this->f3->get($this->modelName)->findById($id);

        if (!$object)
            throw new \HttpException(404);

        return $object;
    }

    public function render($viewsName,$menuNames=null)
    {
        try{
            if ($this->f3->get('debugger')->active && $this->f3->get('cfappMode')=='dev') {
                $this->f3->set('head',$this->f3->get('debugger')->renderHead() );
                $this->f3->set('body',$this->f3->get('debugger')->render() );
            }

            if ($this->f3->exists('SESSION.user')) {
                $this->f3->set('userName', json_decode($this->f3->get('SESSION.user'))->prenom);
                $this->f3->set('userId', json_decode($this->f3->get('SESSION.user'))->id_administrateur);
                $this->f3->set('userAr', json_decode($this->f3->get('SESSION.user')));
            }
            $this->f3->get('service.tools')->getFlashMessages();
            $this->f3->set('views', $viewsName);
            if (!empty($menuNames)){
                $this->f3->set('menus', $menuNames);
            }

            $this->f3->set('breadcrumbs', $this->breadcrumbs);

            echo \Template::instance()->render($this->layout);
        }catch (\Error $e){
            dump($e);
        }

    }

    public function sendJson($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }


    public function updateEtat()
    {
        $obj = $this->loadObject();
        if ($obj->get('etat'))
            $obj->set('etat' , false);
        else
            $obj->set('etat' , true);

        $obj->save();
        $this->f3->reroute($_SERVER['HTTP_REFERER']);
    }

    protected function setBreadcrumbs($name, $url, $isActive=false)
    {
        $this->breadcrumbs[] = array (
            "name" => $name,
            "url" => $url,
            "isActive" => $isActive
        );
    }

    public function popUpRender($View)
    {
        $this->layout = false;
        echo \Template::instance()->render($View);
    }


    public function enable()
    {
        $object = $this->loadObject();

        $object->etat = $object->etat ? false : true;

        $object->save();

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    public function getUser()
    {
        return json_decode($this->f3->get('SESSION.user'));
    }

    public function getService()
    {
        return $this->f3->get($this->serviceName);
    }
}