<?php

namespace App\Service;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationService{
    

    public function __construct(private HttpClientInterface $client, private EntityManagerInterface $em)
    {
        
    }

    public function notify(array $ids, Notification $notif):Response{
        $this->em->persist($notif);
        $this->em->flush();
        return $this->send($ids,$notif);
    }
    
    public function notifyAll($title,$body,$type):Response{
        //$rs = json_decode($request->getContent(), true);
        //$title = $rs['title'];
        //$body = $rs['body'];
        //$type = $rs['type'];
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
            return $this->notify($devices,$notif);
        }
        else {
            # code...
            return new JsonResponse(['message'=>'Failed to send notification','failed'=>1],400);
        }
    }
    
    private function send(array $registratio_ids, Notification $notif):Response{
        $response=$this->client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'=> 'key=AAAAEI7Eizo:APA91bFbxWpYtpcDpG7A7wNhbeqSVBGWBekGkXWsBfipWAMM_9i7907Z-Nup4971kdjN7yxxQqD47tqsLJZ2DTvJWpQpb_WxQEnW3gNeGJtdAjepuHW_pw2RI4YFkvoF_CVkke1-xHjc'
            ],
            
            'json'=>[
 
                "registration_ids"=>$registratio_ids,
                "notification" => [
                    "body" => $notif->getBody(),
                    "title"=> $notif->getTitle(),
                    "priority"=> "high",
                    "content_available"=> true,
                ],
                "data" => [
                    "body" => $notif->getBody(),
                    "title"=>$notif->getTitle(),
                    "type" => $notif->getType(),
                    "priority"=> "high",
                    "content_available"=> true,
                ]
            ]
        ]);
        if($response->getStatusCode() == 200){
            $notif->setIsSent(true);
            $notif->setSentTime(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));
            $this->em->flush();
        }

    return new JsonResponse(['content'=>$response->getContent()/*,"ids"=>$registratio_ids*/],$response->getStatusCode());

    }

}