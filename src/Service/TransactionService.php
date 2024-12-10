<?php

namespace App\Service;

use App\Entity\NfcCard;
use App\Entity\RechargeCarte;
use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Helper\StringGenerator;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionService extends AbstractController
{
    private StringGenerator $generator;

    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly EntityManagerInterface $em,
    ) {
        $this->generator = new StringGenerator();
    }

    public function recharge ($type, $subsId, $amount, $cardUid): array {
        $rest = 0;
        $currency = $this->currencyRepository->findCurrentCurrency();

        $subs = new SubscriptionPlan();
        if($type == "SUBSCRIPTION"){
            $subs_id = $subsId;
            $subs = $this->em->getRepository(SubscriptionPlan::class)->find($subs_id);
            if(!$subs){
                return ["status"=>false,"message"=> "Subscription Plan invalid"];
            }
            $amount = $subs->getAmount();
            if (!empty($subs->getCurrency())) {
                $currency = $subs->getCurrency();
            }
        }

        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        if ($user !== null) {
            $userId = $user->getUserIdentifier();
            $user = $this->em->getRepository(User::class)->findOneBy(["username"=>$userId]);
        }
        $card = $this->em->getRepository(NfcCard::class)->findOneBy(["uid"=>$cardUid]);

        //$card = new NfcCard();
        if($card){
            if(!$card->isIsActive()){
                return ["status"=>false,"message"=>"Votre carte est invalide."];
            }

            if ($user !== null) {
                $rest = $user->getBalance() - $amount;
                $bal = $card->getBalance();

                if($rest < 0){
                    return ["status"=>false,"message"=>"Votre balance est insufisante."];
                }
            }

            $date = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
            $now = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
            $card->setUpdatedAt($date);

            $trans = (new RechargeCarte())
                ->setAmount($amount)
                ->setCard($card)
                ->setCreatedAt($date)
                ->setCreatedBy($user !== null ? $user->getUsername() : "UNKNOWN");

            if ($user !== null) {
                $user->setUpdatedAt($date);
                $trans->setCreatedBy($user->getUsername());
            }
            $trans->setReference($this->generator->generate(10));

            if($type == "FIX"){
                $card->setBalance($card->getBalance() + $amount);

                $trans->setOldBalance($bal);
                $trans->setNewBalance($card->getBalance());

            }

            if($type == "SUBSCRIPTION"){
                $durationInDays = $subs->getDuration();
                $interval = new \DateInterval("P{$durationInDays}D");
                $now->add($interval);
                $card->setSubscriptionEndDate($date);
                $card->setSubscriptionEndDate( $now);
                $trans->setFromDate($date);
                $trans->setToDate( $now);
                $trans->setOldFromDate($card->getSubscriptionFromDate());
                $trans->setOldToDate($card->getSubscriptionEndDate());
                $trans->setSubscriptionId($subsId);
                $trans->setRechargeType($type);

            }

            if ($user !== null) {
                $user->setBalance($rest);
                $this->em->persist($user);
            }

            $this->em->persist($card);
            $this->em->persist($trans);
            $this->em->flush();
            return [
                "status"  => true,
                "message" => "Recharge effectuee avec succes",
                "balance" => $rest,
                "ref"     => $trans->getReference()
            ];


        }else{
            return ["status"=>false,"message"=>"Votre carte est invalide."];
        }
    }
}