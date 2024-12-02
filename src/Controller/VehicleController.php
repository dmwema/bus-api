<?php

namespace App\Controller;

use App\Entity\Line;
use App\Entity\Place;
use App\Entity\Region;
use App\Entity\Vehicle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class VehicleController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private LoggerInterface $logger){

    }
    #[Route('/api/v1/vehicle', name: 'app_vehicle')]
    public function index(): Response
    {
        $vehicles = $this->em->getRepository(Vehicle::class)->findAll();

        return $this->json($vehicles, Response::HTTP_OK);
    }
    #[Route('/api/v1/vehicle/{id}', name:'app_vehicle_by_id')]
    public function show($id): Response{
        $vehicle = $this->em->getRepository(Vehicle::class)->find($id);
        //var_dump($vehicle);
        $line = $this->em->getRepository(Line::class)->findOneBy(["id"=>$vehicle->getLine()->getId()]);
        $places = $line->getPlaces();//$this->em->getRepository(Place::class)->findAll();
        $stops = $line->getStops();
        $region =   $this->em->getRepository(Region::class)->findOneBy(["id"=>$line->getRegion()->getId()]);
        $v=[
            "id"=>$vehicle->getId(),
    "name"=>$vehicle->getName(),
    "matricule"=>$vehicle->getMatricule(),
    "currentLat"=> $vehicle->getCurrentLat(),
    "currentLng"=> $vehicle->getCurrentLng(),
    
    "deviceID"=> $vehicle->getDeviceID(),
    "voletJaune"=>$vehicle->getVoletJaune(),
    "updatedAt"=>$vehicle->getUpdatedAt(),
    //"t"=>$vehicle->getLine(),
    "line"=> [
        "id"=>$line->getId(),
        "region"=>$region->getId(),
        "enterprise"=>$line->getEnterprise()->getId(),
        "name"=>$line->getName(),
        "paymentType"=>$line->getPaymentType(),
        "ticketPrice"=>$line->getTicketPrice(),
        //"region"=>"/api/regions/".$line->getRegion()->getId(),
        "description"=>$line->getDescription(),
        "places"=>$places,
        "stops"=>$stops
        ]
    ];
    //$this->logger->info(print_r($line) . PHP_EOL);
        return $this->json(["vehicle"=>$v]);
    }
    #[Route('/api/v1/vehicle/by-device/{device}', name:'app_vehicle_by_device')]
    public function showByDevice($device): Response{
        $vehicle = $this->em->getRepository(Vehicle::class)->findOneBy(["deviceID"=>$device]);
        //var_dump($vehicle);
        $line = $this->em->getRepository(Line::class)->findOneBy(["id"=>$vehicle->getLine()->getId()]);
        $places = $line->getPlaces();//$this->em->getRepository(Place::class)->findAll();
        $stops = $line->getStops();
        $region =   $this->em->getRepository(Region::class)->findOneBy(["id"=>$line->getRegion()->getId()]);
        $v=[
            "id"=>$vehicle->getId(),
    "name"=>$vehicle->getName(),
    "matricule"=>$vehicle->getMatricule(),
    "currentLat"=> $vehicle->getCurrentLat(),
    "currentLng"=> $vehicle->getCurrentLng(),
    
    "deviceID"=> $vehicle->getDeviceID(),
    "voletJaune"=>$vehicle->getVoletJaune(),
    "updatedAt"=>$vehicle->getUpdatedAt(),
    //"t"=>$vehicle->getLine(),
    "line"=> [
        "id"=>$line->getId(),
        "region"=>$region->getId(),
        "name"=>$line->getName(),
        "paymentType"=>$line->getPaymentType(),
        "ticketPrice"=>$line->getTicketPrice(),
        //"region"=>"/api/regions/".$line->getRegion()->getId(),
        "description"=>$line->getDescription(),
        "places"=>$places,
        "stops"=>$stops
        ]
    ];
    //$this->logger->info(print_r($line) . PHP_EOL);
        return $this->json(["vehicle"=>$v]);
    }
}
