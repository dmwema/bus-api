<?php

namespace App\Controller;

use App\Entity\Line;
use App\Entity\Logins;
use App\Entity\Region;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/auth', name: 'auth_')]
class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em,
    private UserPasswordHasherInterface $ph, private JWTTokenManagerInterface $jwtManager, 
    private AuthenticationSuccessHandler $authHandler, private MailerInterface $mailer){
       

    }
    #[Route('/registration', name: 'app_registration')]
    public function index(Request $request): Response 
    {
        //$authenticationSuccessHandler = $this->container->get('lexik_jwt_authentication.handler.authentication_success');
        //$jwtManager = $this->container->get('lexik_jwt_authentication.handler.authentication_success');
        $decoded = json_decode($request->getContent());
        $username = $decoded->username;
        $plaintextPassword = $decoded->password;
        $roles = $decoded->roles;
        $phone = $decoded->phone;
        $address = $decoded->address;
        //$email = $decoded->email;
        $fullname = $decoded->fullname;

        $ck_user=$this->em->getRepository(User::class)->findOneBy(["username"=>$username]);
        if($ck_user){
            return $this->json(["success"=>"false","message"=>"Cet utilisateur ($username) est déjà pris"],400);
        }

  
        $user = new User();
        $hashedPassword = $this->ph->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setFullname($fullname);
        $user->setPhone($phone);
        $user->setAddress($address);
        $user->setIsActive(true);
        $this->em->persist($user);
        $this->em->flush();
        
            
            // ...
        
        $auth = $this->authHandler->handleAuthenticationSuccess($user);

        //$token = $this->jwtManager->create($user);
        $authContent = json_decode($auth->getContent());
  
        return $this->json(['success'=>true,
        'message' => 'Enregistré avec succès',
        'sub'=>$user->getId(),
        'username'=>$user->getUsername(),
        'fullname' => $user->getFullname(),
        'phone' => $user->getPhone(),
        'isActive' => $user->isIsActive(),
        'address' => $user->getAddress(),
        //'token'=>$token
        "token"=>$authContent->token,
        "refresh_token"=>$authContent->refresh_token,
        
    ]);
    }

    #[Route('/nfc_login', name: 'app_nfc_login', methods:"POST")]
    public function login(Request $request): Response 
    {
        //$authenticationSuccessHandler = $this->container->get('lexik_jwt_authentication.handler.authentication_success');
        //$jwtManager = $this->container->get('lexik_jwt_authentication.handler.authentication_success');
        $decoded = json_decode($request->getContent());
        $taguid = $decoded->taguid;
        $type = $decoded->login_type;

        if($type !== "NFC_LOGIN"){
            return $this->json(["success"=>"false","message"=>"Undefined login"],400);
        }

        $ck_user=$this->em->getRepository(User::class)->findOneBy(["tagUid"=>$taguid]);
        if($ck_user){
                // ...
        
        $auth = $this->authHandler->handleAuthenticationSuccess($ck_user);

        //$token = $this->jwtManager->create($user);
        $authContent = json_decode($auth->getContent());
        $data = ['success'=>true,
        'message' => 'Enregistré avec succès',
        'sub'=>$ck_user->getId(),
        'username'=>$ck_user->getUsername(),
        'fullname' => $ck_user->getFullname(),
        'phone' => $ck_user->getPhone(),
        'isActive' => $ck_user->isIsActive(),
        'address' => $ck_user->getAddress(),
        //'token'=>$token
        "token"=>$authContent->token,
        "refresh_token"=>$authContent->refresh_token,
        
        ];

        if($ck_user->getVehicle() !== null){
            
            $vehicle = $this->em->getRepository(Vehicle::class)->findOneBy(["id"=>$ck_user->getVehicle()->getId()]);
            $line = $this->em->getRepository(Line::class)->findOneBy(["id"=>$vehicle->getLine()->getId()]);
                $places = $line->getPlaces();
                $stops = $line->getStops();
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
  
        return $this->json($data);
            
        }else{
            return $this->json(["success"=>"false","message"=>"Invalid user"],400);
        }
  
        
    }

    
    

    #[Route(path:"/mail",name:"app_mailer", methods:"POST")]
    public function sendMail(Request $request):Response
    {
        $params = json_decode($request->getContent());
        $message = $params->message;
        $to = $params->to;
        $object = $params->object;
        try {
            //code...
            $email = (new Email())
                ->from('noreply@maajabutalent.com')
                ->to($to)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject($object)
                ->text('Sending emails is fun again!')
                ->html($message);
    
            $rs = $this->mailer->send($email);
            return $this->json(['message'=>"Mail sent successfully"],200);
    
        } catch (\Throwable $th) {
            //throw $th;
            return $this->json(['code'=>$th->getCode(), 'message'=>$th->getMessage()],500);

        }

    }

    #[Route("/logout", name:"app_logout", methods:"POST")]
    public function logout(Request $request):Response{
        
        $user= $this->em->getRepository(User::class)->findOneBy([$this->getUser()->getUserIdentifier()]);
        $date = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        $conn = $this->em->getConnection();
        $sql = '
        UPDATE  `logins` SET end_time = :endTime
        WHERE  user_id = :userId AND end_time IS NULL';
        $resultSet = $conn->executeQuery($sql, ['endTime' => $date, 'userId'=> $user->getId()]);
        //$res = $resultSet->fetchAllAssociative();
        return $this->json(["success"=>true,"message"=>"Logged out."]);
     
    }

    
    
}
