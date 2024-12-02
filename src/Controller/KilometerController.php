<?php

namespace App\Controller;

use App\Entity\KilometerTrack;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KilometerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }
    #[Route('/kilometer', name: 'app_kilometer')]
    public function index(): Response
    {
        return $this->render('kilometer/index.html.twig', [
            'controller_name' => 'KilometerController',
        ]);
    }
    #[Route('/kilometer/add', name: 'app_kilometer_add')]
    public function add(Request $request): Response
    {
        $decoded = json_decode($request->getContent());
        $vehicleId = $decoded->vehicleId;
        $driverId = $decoded->driverId;
        $kilo = $decoded->kilometer;
        $v = $this->em->getRepository(Vehicle::class)->find($vehicleId);
        $d = $this->em->getRepository(User::class)->find($driverId);
        if($kilo < $v->getKilometer()){
            return $this->json(["status"=>false,"message"=>"kilométrage n\'est pas valide"], 400);
        }

        $v->setKilometer($kilo);
        $kt = new KilometerTrack();
        $kt->setDriver($d);
        $kt->setKilometer($kilo);
        $kt->setVehicle($v);
        $this->em->persist($kt);
        $this->em->flush();

        return $this->json(["satatus"=>true,"message"=>"kilométrage ajouté avec succès"]);  
    }
    
}
