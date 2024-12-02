<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Entity\Vehicle;
use App\Entity\VehicleTracker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehicleTrackerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
        
        
    }
    #[Route('/vehicle/tracker', name: 'app_vehicle_tracker')]
    public function index(): Response
    {
        return $this->render('vehicle_tracker/index.html.twig', [
            'controller_name' => 'VehicleTrackerController',
        ]);
    }
    #[Route('/api/vehicle/tracker/{id}', name: 'app_vehicle_tracker_update', methods: 'POST')]
    public function update(Request $request, $id): Response
    {
        $decoded = json_decode($request->getContent());
        $currentLat = $decoded->currentLat;
        $currentLng = $decoded->currentLng;
        
        $car = $this->em->getRepository(Vehicle::class)->find($id);
        if($car){
            $car->setCurrentLat($currentLat);
            $car->setCurrentLng($currentLng);
            //$this->em->persist($car);
            $vt = new VehicleTracker();
            $vt->setLat($currentLat);
            $vt->setLng($currentLng);
            $vt->setVehicle($car);
            $this->em->persist($vt);
            $this->em->flush();
            return $this->json($car);
        }
        return $this->json(["success"=>false, "message"=>"Some fields are missing."],400);
        
    }

    #[Route('/alert/update', methods:'POST', name: 'app_map_alert_update')]
    public function updateAlert(Request $request): Response
    {
        $id = $request->request->get('id');
        $alert = $this->em->getRepository(Alert::class)->find($id);
        $alert->setIsSeen(true);
        $this->em->flush();
        
       return $this->json(["success"=>true,"message"=>"Alert updated!"],200);
      
    }
    #[Route('/alert/send', methods:'POST', name: 'app_map_alert_send')]
    public function sendAlert(Request $request): Response
    {
        $decoded = json_decode($request->getContent());
        $v = $this->em->getRepository(Vehicle::class)->find($decoded->vehicleId);
        if($v){
            $alert = new Alert();
            $alert->setDescription($decoded->description);
            $alert->setTitle($decoded->title);
            $alert->setVehicle($v);
            $alert->setIsSeen(false);
            $this->em->persist($alert);
            $this->em->flush();
            return $this->json(["success"=>true,"message"=>"Alert sent!"],200);
        }
        else{
            return $this->json(["sucess"=>false, "message"=>"Vehicle not found!"],400);
        }
    }
    #[Route('/api/alert/resume/{id}', methods:'GET', name: 'app_alert_resume_app')]
    public function resumeAlert($id): Response
    {
        $sql = '
        UPDATE `alert` SET is_seen = true WHERE vehicle_id = :id AND title = :title
        ';
        $conn = $this->em->getConnection();
        $resultSet = $conn->executeQuery($sql, ['id'=>$id,'title'=>'App closed']);
        $this->em->flush();
        return $this->json(["success"=>true,"message"=>"Alert updated!"],200);
    }
}
