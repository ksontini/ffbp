<?php
namespace Tests;

class TestTest extends BaseTest
{

    public function testCalculePrixDossier()
    {
        $totalDossier = $this->f3->get('service.reservation')->GetMontantTotalDossier(1);
        //dump($totalDossier);
        $this->expect($totalDossier["achat"]==20.0,"Prix Achat Test Failed");
        $this->expect($totalDossier["vente"]==30.0,"Prix Vente Test Failed");
        $this->expect($totalDossier["marge"]==33.33,"Marge Test Failed");
    }
}