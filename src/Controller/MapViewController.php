<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MapViewController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private UrlGeneratorInterface $url)
    {
        
    }

    #[Route('/admin/map/view', name: 'app_map_view')]
    public function index(): Response
    {
        $vehicles = $this->em->getRepository(Vehicle::class)->findAll();
       // $admin_pool = $this->get('sonata.admin.pool');
        return $this->render('map_view/index.html.twig', [
           // 'admin_pool' => $admin_pool,
            'vehicles' => json_encode($vehicles),
            'data'=>$this->getData()
        ]);
    }

    #[Route('/admin/map/auto', name: 'app_map_view_auto')]
    public function autoLoad(): Response
    {
        
       return $this->json($this->getData(),200);
      
    }

    private function getData() : array{
        
        $vehicles = $this->em->getRepository(Vehicle::class)->findAll();
        $conn = $this->em->getConnection();
        $data = array();
        foreach($vehicles as $v){
            $sql = '
            SELECT SUM(e.ticket_price * e.passengers) AS total FROM `route` e 
            WHERE DATE(e.starting_time) =  CURDATE() AND e.vehicle_id=:vehicleId';
        //die(var_dump($dql_sum));
        $resultSet = $conn->executeQuery($sql, ['vehicleId' => $v->getId()]);
        $res = $resultSet->fetchAllAssociative();
        $total = 0;
        if($res[0]["total"] != null){
            $total = $res[0]["total"];

        }
        $alert = $this->em->getRepository(Alert::class)->findOneBy(["vehicle"=>$v->getId(),"isSeen"=>false]);
       
 
              $sql = '
              SELECT u.username, u.id, u.roles, l.created_at FROM user u 
              INNER JOIN logins l ON u.id = l.user_id WHERE u.vehicle_id = :vehicleId
              AND u.roles LIKE "%ROLE_DRIVER%" ORDER BY l.created_At DESC LIMIT 1;
            ';
            $resultSet = $conn->executeQuery($sql, ['vehicleId' => $v->getId()]);
            $res = $resultSet->fetchAllAssociative();
            
             
            $driver = "";
            $startingAt = "";
            $phone = "";
            if(isset($res[0]["username"])){
                $driver = $res[0]["username"];
            }
            if(isset($res[0]["phone"])){
                $phone = $res[0]["phone"];
            }
            if(isset($res[0]["created_at"])){
                $startingAt = $res[0]["created_at"];
            }
           

            if($alert){
                $color = "bus_red.png";
                /*if($v->getRegion()->getId() == 1){
                    $color = "bus_red_alert.png";
                }else if($v->getRegion()->getId() == 2){
                    $color = "bus_green_alert.png";
                }else{
                    $color = "bus_blue_alert.png";
                }*/
                array_push($data,["name"=>$v->getName(), "id"=>$v->getMatricule(), 
            "lat"=>$v->getCurrentLat(),"lng"=>$v->getCurrentLng(), "total"=>$total,
              "driver"=>$driver,
              "startingAt"=>$startingAt,
              "color"=>$color,
              "phone"=>$phone,
              "alert"=>[$alert->getId(),$alert->getTitle(),$alert->getCreatedAt()],
              "transUrl"=> $this->url->generate('app_admin_chart',['vehicle'=>$v->getName()]),
              "routeUrl"=> $this->url->generate('app_map_vehicle',['vehicle'=>$v->getName()])
              
                ]);
            }else{
                $color = "bus_blue.png";
                if($v->getLine()->getId() == 1){
                    $color = "bus_yellow.png";
                }else if($v->getLine()->getId() == 2){
                    $color = "bus_green.png";
                }else{
                    $color = "bus_blue.png";
                }
                array_push($data,["name"=>$v->getName(), "id"=>$v->getMatricule(), 
            "lat"=>$v->getCurrentLat(),"lng"=>$v->getCurrentLng(), "total"=>$total,
              "driver"=>$driver,
              "startingAt"=>$startingAt,
              "color"=>$color,
              "phone"=>$phone,
              "transUrl"=> $this->url->generate('app_admin_chart',['vehicle'=>$v->getName()]),
              "routeUrl"=> $this->url->generate('app_map_vehicle',['vehicle'=>$v->getName()])
              
                ]);

            }

        }

        return $data;

    }

    #[Route('/admin/map/alert/update', methods:'POST', name: 'admin_map_alert_update')]
    public function updateAlert(Request $request): Response
    {
        $id = $request->request->get('id');
        $alert = $this->em->getRepository(Alert::class)->find($id);
        $alert->setIsSeen(true);
        $this->em->flush();
       return $this->json(["success"=>true,"message"=>"Alert updated!"],200);
    }
    #[Route('/admin/map/one', name: 'app_map_vehicle')]
    public function mapView(Request $request): Response
    {
        $name = $request->query->get('vehicle');
        $v = $this->em->getRepository(Vehicle::class)->findOneBy(["name"=>$name]);
        $rts = array();
        
        if($v){
            $tracks = $v->getVehicleTrackers();
            
            foreach($tracks as $r){
                //dd($r->getCreatedAt()->format('d-m-y H:i:s'));
                array_push($rts,["lat"=>$r->getLat(),
                 "lng"=>$r->getLng(), "time"=>$r->getCreatedAt()->format('d-m-Y H:i:s')]);
            }
        }

        
        return $this->render('map_view/vehicle_map.html.twig', [
            // 'admin_pool' => $admin_pool,
             'vehicle' => $v->getName(),
             'routes'=>$rts
         ]);
      
    }




    

}
