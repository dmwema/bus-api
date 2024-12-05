<?php

namespace App\Controller;

use App\Entity\Line;
use App\Entity\NfcCard;
use App\Entity\RechargeCarte;
use App\Entity\Route as EntityRoute;
use App\Entity\SubscriptionPlan;
use App\Entity\Transaction;
use App\Entity\User;
use App\Helper\StringGenerator;
use DateInterval;
use DateTimeUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    private $generator;
    public function __construct(private EntityManagerInterface $em){
        $this->generator = new StringGenerator();
    }

    #[Route('/transaction', name: 'app_transaction')]
    public function index(): Response
    {
        return $this->render('transaction/index.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #[Route('/transaction/process', name: 'app_transaction_process')]
    public function process(Request $request): Response
    {
        $decoded = json_decode($request->getContent());
        $cardUid = $decoded->uid;
        $routeId = $decoded->routeId;
        $amount = $decoded->amount;
        $card = $this->em->getRepository(NfcCard::class)->findOneBy(["uid"=>$cardUid]);
        $route = $this->em->getRepository(EntityRoute::class)->find($routeId);
        $line = $route->getLine();
        $trans = new Transaction();

        if($card){
            if(!$card->isIsActive()){
                return $this->json(["status"=>false,"message"=>"Votre carte est invalide."],400);

            }
            $payType = $line->getPaymentType();
            if($payType == "SUBSCRIPTION"){
                if($card->isSubscribed()){
                return $this->json(["status"=>false,"message"=>"Votre balance est insufisante."],400);
                }
                $trans->setOldFromDate($card->getSubscriptionFromDate());
                $trans->setOldToDate($card->getSubscriptionEndDate());
                
            }
            elseif($payType == "DEDUCTED"){
                $rest = $card->getBalance() - $amount;
                $bal = $card->getBalance();
                if($rest < 0){
                    return $this->json(["status"=>false,"message"=>"Votre balance est insufisante."],400);
                }
                $card->setBalance($rest);
                
            $trans->setOldBalance($bal);
            $trans->setNewBalance($rest);

            }else{
                return $this->json(["status"=>false,"message"=>"Type de payment inconnu."],400);
            }
            
            
            $card->setUpdatedAt(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));
            $this->em->persist($card);
            
            $trans->setPaymentType($line->getPaymentType());
            $trans->setAmount($amount);
            $trans->setCard($card);
            $trans->setRoute($route);
            $this->em->persist($trans);
            $this->em->flush();
            return $this->json(["status"=>true,"message"=>"Paiement effectue avec succes"]);
        }else{
            return $this->json(["status"=>false,"message"=>"Votre carte est invalide."],400);
        }
    }
    #[Route('/api/transaction/recharge', name: 'app_transaction_recharge')]
    public function recharge(Request $request): Response
    {
        $dateUtil = new DateTimeUtil();
        $decoded = json_decode($request->getContent());
        $cardUid = $decoded->uid;
        $type = $decoded->type;
        $subs = new SubscriptionPlan();
        if($type == "SUBSCRIPTION"){
            $subs_id = $decoded->subs_id;
            $subs = $this->em->getRepository(SubscriptionPlan::class)->find($subs_id);
            if(!$subs){
                return $this->json(["status"=>false,"message"=> "Subscription Plan invalid"]);
            }
            
            $amount = $subs->getAmount();
        }elseif($type == "Fix"){
            $amount = $decoded->amount;
        }else{
            return $this->json(["status"=>false,"message"=> "Type de Recharge invalide"]);
        }
                        
        $userId = $this->getUser()->getUserIdentifier();
        $user = $this->em->getRepository(User::class)->findOneBy(["username"=>$userId]);
        $card = $this->em->getRepository(NfcCard::class)->findOneBy(["uid"=>$cardUid]);
        
        //$card = new NfcCard();
        if($card){
            if(!$card->isIsActive()){
                return $this->json(["status"=>false,"message"=>"Votre carte est invalide."],400);

            }
            $rest = $user->getBalance() - $amount;
            $bal = $card->getBalance();
        
            if($rest < 0){
            return $this->json(["status"=>false,"message"=>"Votre balance est insufisante."],400);
            }
            
            $date = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
            $now = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
            $card->setUpdatedAt($date);
            $user->setUpdatedAt($date);
            
            $trans = new RechargeCarte();
            $trans->setAmount($amount);
            $trans->setCard($card);
            $trans->setCreatedBy($user->getUsername());
            $trans->setCreatedAt($date);
            
            $trans->setReference($this->generator->generate(10));
           
            if($type == "FIX"){
               
                $card->setBalance($card->getBalance() + $amount);
                
            $trans->setOldBalance($bal);
            $trans->setNewBalance($card->getBalance());

            }
            
            if($type == "SUBSCRIPTION"){
                $durationInDays = $subs->getDuration();
                $interval = new DateInterval("P{$durationInDays}D");
                $now->add($interval);
            $card->setSubscriptionEndDate($date);
            $card->setSubscriptionEndDate( $now);
            $trans->setFromDate($date);
            $trans->setToDate( $now);
            $trans->setOldFromDate($card->getSubscriptionFromDate());
            $trans->setOldToDate($card->getSubscriptionEndDate());
            $trans->setSubscriptionId($subs_id);
            $trans->setRechargeType($type);
            
            }
            $user->setBalance($rest);
            $this->em->persist($user);
            $this->em->persist($card);
            //$trans->setRouteId($routeId);
            $this->em->persist($trans);
            $this->em->flush();
            return $this->json(["status"=>true,"message"=>"Recharge effectuee avec succes","balance"=>$rest]);


        }else{
            return $this->json(["status"=>false,"message"=>"Votre carte est invalide."],400);
        }
        

    }

    #[Route('/api/card/create', name: 'app_card_create')]
    public function createCard(Request $request): Response
    {
        $by = $this->getUser()->getUserIdentifier();
        $u = $this->em->getRepository(User::class)->findOneBy(['username'=>$by]);
        $decoded = json_decode($request->getContent());
        $cardUid = $decoded->uid;
        $holder = $decoded->cardHolder;
        $phoneNumber = $decoded->phoneNumber;
        if(property_exists($decoded, "balance"))
            $balance = $decoded->balance;
        else
            $balance = 0;
        $c = $this->em->getRepository(NfcCard::class)->findOneBy(["uid"=>$cardUid]);
        if($c){
            return $this->json(["success"=>false,"message"=>"Carte deja enregistree"],400);
        }
        $card = new NfcCard();
        $card->setUid($cardUid);
        $card->setCardHolder($holder);
        $card->setPhoneNumber($phoneNumber);

        if($balance >= 0){
            $card->setBalance($balance);

        }
        else
            $card->setBalance(0);
        $by_balance = $u->getBalance() - $balance;
        $u->setBalance($by_balance);
        $card->setIsActive(true);
        $card->setCreatedBy($by);
        $this->em->persist($card);

        $this->em->flush();
        return $this->json(["success"=>true,"message"=>"Carte enregistree avec success"]);

    }
}
