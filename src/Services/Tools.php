<?php
/**
 * Created by PhpStorm.
 * User: anis
 * Date: 26/05/17
 * Time: 05:03 م
 */

namespace Services;

class Tools extends BaseService
{

    public $msg = array();

    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->setTemplateFunctions();

    }

    public static function getClientIp()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function flash($message, $type)
    {
        $this->msg[] = array(
            'message' => $message,
            'type' => $type,
        );
    }

    public function getFlashMessages()
    {
        $this->f3->set('flashs', $this->msg);
    }

    protected function setTemplateFunctions()
    {
        $this->create_selector();
        $this->pagination();
        $this->elementsPerPage();
        $this->dateFormat();
        $this->dayOfWeek();
        $this->icon();
    }

    protected function create_selector()
    {
        /**
         * create selector
         */
        $this->f3->set('select', function ($id, $class, $options, $selectedValue = null, $style = null, $change=null) {

            $data = '<select name="' . $id . '" id="' . $id . '" class="' . $class . '" style="' . $style . '" ONCHANGE="'.$change .'">';

            foreach ($options as $key => $value) {
                $selected = $value == $selectedValue ? 'selected' : '';
                $data .= "<option value='$value' $selected >$key</option>";
            }
            $data .= "</select>";
            return $data;
        });
    }

    protected function create_input()
    {
        /**
         * create input fields
         */

        $this->f3->set('input', function ($id, $class, $inputType, $arrayFields, $style = null, $onChange = null, $autocomplete = 'off') {
            $data = "";
            foreach ($arrayFields as $key => $value) {
                $data .= "<input type='$inputType' name='$id' id='$id' class='$class' style='$style'  value='$value' onChange='$onChange' autocomplete='$autocomplete' />";
            }
            return $data;
        });
    }

    public function pagination()
    {
        /**
         * pagination
         */
        $this->f3->set('pagination', function ($link, $data = null, $maxPerPage=10) {
            $cursor = $data['pos']+1 !=0 ? $data['pos']+1 : 1;
            $nbrPage =$data['count'];
            $maxPerPage = is_numeric($maxPerPage) ? $maxPerPage : 10;

            /*$url = explode("?", $_SERVER['REQUEST_URI']);
            $url = $url[1] ? $url[1] :  'page=1&maxPerPage='.$maxPerPage;
            dump($url);*/
            $token=null;
            if(isset($_GET))
                foreach($_GET as $key=>$value){
                    $token .= $key!='page' && $key!='maxPerPage' ? "&$key=$value" : "";
                }

            $pagination = "<ul class=\"liste_paginate\">";
            $previous = $cursor-- > 0 ? $cursor - 1 : $cursor;
            $next = $cursor++ > $nbrPage ? $cursor : $cursor + 1;
            $pagination .= "<li><a href='$link?page=1&maxPerPage=$maxPerPage$token' onclick='patientez();'>First</a></li>";
            $pagination .= "<li><a href='$link?page=$previous&maxPerPage=$maxPerPage$token' onclick='patientez();'>◄</a></li>";
            for ($p = 6 ; $p>0 ; $p--){
                $page = $cursor-$p;
                if($page>0){
                    $pagination .= "<li ><a href='$link?page=$page&maxPerPage=$maxPerPage$token' onclick=\"patientez();\">$page</a></li>";
                }
            }
            $pagination .= "<li class=\"selected\" ><a href='$link?page=$cursor&maxPerPage=$maxPerPage$token' onclick=\"patientez();\">$cursor</a></li>";

            for ($n = 1 ; $n<6 ; $n++){
                $page = $cursor+$n;
                if($page<=$nbrPage){
                    $pagination .= "<li ><a href='$link?page=$page&maxPerPage=$maxPerPage$token' onclick=\"patientez();\">$page</a></li>";
                }
            }

           /* for ($i = 1; $i <= $nbrPage; $i++) {
                $selected = $cursor == $i ? 'class="selected"' : '';
                $pagination .= "<li $selected ><a href='$link?page=$i&maxPerPage=$maxPerPage' onclick=\"patientez();\">$i</a></li>";
            }*/

            $pagination .= "<li><a href='$link?page=$next&maxPerPage=$maxPerPage$token' onclick='patientez();'>►</a></li>";
            $pagination .= "<li><a href='$link?page=$nbrPage&maxPerPage=$maxPerPage$token' onclick='patientez();'>Last</a></li>";

            $pagination .= "</ul>";

            return $pagination;
        });
    }

    public function elementsPerPage()
    {
        $this->f3->set('elementsPerPage',function ($link,$elementsPerPage=10){
            $list = array(10,20,50,100,500,1000);
            $options = "";
            foreach ($list as $p) {
                if ($p == $elementsPerPage)
                    $options .= "<option selected >$p</option>";
                else
                    $options .= "<option>$p</option>";
            }
          $html = <<<HTML
        <select onchange="location = addParameter('maxPerPage', this.options[this.selectedIndex].value);  patientez();">
            $options
        </select>
HTML;
        return $html;
        });
    }

    public function dateFormat()
    {
        $this->f3->set('df', function($date, $heure = false) {
            if (!$heure)
                return date('d/m/Y',strtotime($date));
            else
                return date('d/m/Y H:m',strtotime($date));
        });
    }

    public function dayOfWeek()
    {
        $this->f3->set('dow', function($days) {
            $dowMap = array('Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam');
            $result = "";

            foreach (explode("--", $days) as $day) {
                $result .= $dowMap[$day-1] . ' ';
            }
            return $result;
        });
    }

    public function createActivity($user)
    {
        $log = $this->f3->get('model.log_activity');
        $log->id_administrateur = $user->id_administrateur;
        $log->date = date('Y-m-d H:i:s');
        $log->hive = base64_encode(json_encode($this->f3->hive()));
        if (strlen($log->hive) < 21500)
            $log->save();
    }

    public function prepareSelect($array, $key, $label, $default = null)
    {
        $list = array();

        if ($default) {
            $list[$default] = null;
        }

        foreach ($array as $element) {
            $list[$element->{$label}] = $element->{$key};
        }

        return $list;
    }

    public function icon()
    {
        $this->f3->set("icon",function ($etat=1){
            switch ($etat){
                case 1 : $etat_reservation = "dossier_en_cours"; break;
                case 2 : $etat_reservation = "a_confirmer"; break;
                case 3 : $etat_reservation = "dossier_valider"; break;
                case 4 : $etat_reservation = "dossier_termine"; break;
                case 5 : $etat_reservation = "dossier_refuse"; break;
            }

            return "<img src='assets/img/icons/$etat_reservation.png'  alt='$etat_reservation' title='".strtoupper(str_replace('_' ,' ' , $etat_reservation))."'/>";
        });

    }

}