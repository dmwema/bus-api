<?php

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MapsViewController extends CRUDController{

    
    public function listAction(Request $request):Response
    {
        //return $this->render('YourBundle::stats.html.twig');
        //return $this->render('mapsview/index.html.twig',[]);
        return new Response("response");
    }

}