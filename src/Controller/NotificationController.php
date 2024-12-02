<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\UserData;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\JWT\Contract\Keys;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class NotificationController extends AbstractController
{
  

    public function __construct(private NotificationService $notifyer, private EntityManagerInterface $em){
       

    }
    #[Route('/notification', name: 'app_notification',methods:'POST')]
    public function index(Request $request): Response
    {
        $rs = json_decode($request->getContent(), true);
        $title = $rs['title'];
        $body = $rs['body'];
        $type = $rs['type'];
        $ids = $rs['ids'];
        $notif = new Notification();
        $notif->setTitle($title);
        $notif->setBody($body);
        $notif->setType($type);
        $notif->setIsSent(false);
        
        $notif->setUsers($ids);
        $devices = array();

        $users = $this->em->getRepository(UserData::class)->findBy(array('id'=>$ids));
        if ($users) {
            # code...
            foreach ($users as $key => $value) {
            array_push($devices,$value->getDeviceToken());
            }
            //return $this->send($devices,$notif);
            return $this->notifyer->notify($devices,$notif);
        }
        else {
            # code...
            return $this->json(['message'=>'Failed to send notification','failed'=>1],400);
        }
       


        /*return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
        ]);*/
 }

    #[Route('/notification/all', name: 'app_notification_all',methods:'POST')]
    public function notifyAll(Request $request): Response
    {
        $rs = json_decode($request->getContent(), true);
        $title = $rs['title'];
        $body = $rs['body'];
        $type = $rs['type'];
        $notif = new Notification();
        $notif->setTitle($title);
        $notif->setBody($body);
        $notif->setType($type);
        $notif->setIsSent(false);
        $notif->setUsers(['all']);
        /*$this->em->persist($notif);
        $this->em->flush();*/
        
        $users = $this->em->getRepository(UserData::class)->findAll();
        $devices = array();
        if ($users) {
            # code...
            foreach ($users as $key => $value) {
            array_push($devices,$value->getDeviceToken());
            }
            //return $this->send($devices,$notif);
            return $this->notifyer->notify($devices,$notif);
        }
        else {
            # code...
            return $this->json(['message'=>'Failed to send notification','failed'=>1],400);
        }
        
    }

    
    /*private function send(array $registratio_ids, Notification $notif):Response{
        $response=$this->client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'=> 'key=AAAA74CDm8k:APA91bHNL1Hoi9UXIRpfRTW-8Oc36azqS58RmRFtMUcswrYo_IRhErI9S1SRR5NJKPnepCuSOvdLOhZv6vBfyeflG9KPo0HACncQjaXQ3xdMKhjnRX6n-j_-YDZgu3iL3xLxUfGoDfrj'
            ],
            
            'json'=>[
 
                "registration_ids"=>$registratio_ids,
                "notification" => [
                    "body" => $notif->getBody(),
                    "title"=> $notif->getTitle()
                ],
                "data" => [
                    "body" => $notif->getBody(),
                    "title"=>$notif->getTitle(),
                    "type" => $notif->getType(),
                ]
            ]
        ]);
        if($response->getStatusCode() == 200){
            $notif->setIsSent(true);
            $notif->setSentTime(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));
            $this->em->flush();
        }

    return $this->json(['content'=>$response->getContent()/*,"ids"=>$registratio_ids],$response->getStatusCode());

    }*/

}
