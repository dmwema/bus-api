<?php

namespace App\EventListener;

use App\Entity\Line;
use App\Entity\Logins;
use App\Entity\Place;
use App\Entity\Region;
use App\Entity\Stop;
use App\Entity\UserData;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }
    /**
 * @param AuthenticationSuccessEvent $event
 */
public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
{
    $data = $event->getData();
    $user = $event->getUser();

    if (!$user instanceof UserInterface) {
        return;
    }
    //$userData = $this->em->getRepository(UserData::class)->findOneBy(['uid'=>"15"]);
    $login = new Logins();
    $login->setUser($user);
    $this->em->persist($login);
    $this->em->flush();
    
    $data['sub'] = $user->getId();
    $data['username'] = $user->getUsername();
    $data['fullname'] = $user->getFullname();
    $data['phone'] = $user->getPhone();
    $data['isActive'] = $user->isIsActive();
    $data['address'] = $user->getAddress();
    $data['balance']  = $user->getBalance();

    if($user->getVehicle() !== null){
    //$vh = $user->getVehicle();
    $vehicle = $this->em->getRepository(Vehicle::class)->findOneBy(["id"=>$user->getVehicle()->getId()]);
    $line = $this->em->getRepository(Line::class)->findOneBy(["id"=>$vehicle->getLine()->getId()]);
        $places = $line->getPlaces();//$this->em->getRepository(Place::class)->findBy(["line"=>$line->getId()]);
        $stops = $line->getStops();//$this->em->getRepository(Stop::class)->findBy(["line"=>$line->getId()]);
        $region =   $this->em->getRepository(Region::class)->findOneBy(["id"=>$line->getRegion()->getId()]);
        
        $placeData = array();
        $stopData = array();
        if(!empty($places)){
            $placeData = array_map(function($place) {
                return $place->toArray();
            }, $places->toArray());
        }
        if(!empty($stops)){
            $stopData = array_map(function($stop) {
                return $stop->toArray();
            }, $stops->toArray());
        }
        $v = $vehicle->toArray();
        $v["lineData"] = $line->toArray();
        $v["lineData"]["places"] = $placeData;
        $v["lineData"]["stops"] = $stopData;
    
        
    $data['vehicle'] = $v;
    }

    $event->setData($data);
}
}