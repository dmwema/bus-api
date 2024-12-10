<?php

namespace App\Service;

use App\Entity\NfcCard;
use App\Entity\Payment;
use App\Entity\RechargeCarte;
use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Helper\StringGenerator;
use App\Repository\CurrencyRepository;
use App\Repository\NfcCardRepository;
use App\Repository\PaymentRepository;
use App\Repository\SubscriptionPlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RechargeService extends AbstractController
{
    public function __construct(
        private readonly NfcCardRepository $cardRepository,
        private readonly SubscriptionPlanRepository $subscriptionPlanRepository,
        private readonly PaymentService $paymentService,
        private readonly CurrencyRepository $currencyRepository,
        private readonly PaymentRepository $paymentRepository,
    ) {
    }

    public function processInfosStep (Request $request, int $step): array {
        $type = $request->request->get('type');
        $success = true;
        $message = null;

        if (empty($type) || ($type != "FIXE" && $type != "SUBSCRIPTION")) {
            $success = false;
            $message = "Vous devez choisir un type de recharge valide";
        }

        $code = $request->request->get('card');
        $card = $this->cardRepository->findOneBy(['code' => $code]);
        if ($card === null) {
            $success = false;
            $message = "Carte invalide";
        }

        $step++;
        $request->getSession()->set('recharge_step', $step);
        $request->getSession()->set('recharge_card', $code);
        $request->getSession()->set('recharge_type', $type);

        return compact('success', 'message');
    }

    /**
     * @throws JsonException
     */
    public function processPaymentStep (Request $request, string $type, NfcCard $card): array {
        $success = true;
        $message = null;

        $amount = $request->request->get('amount');
        $currency = $this->currencyRepository->findCurrentCurrency();

        if ($type === "SUBSCRIPTION") {
            $subscriptionId = $request->request->get('subscription');

            if (empty($subscriptionId)) {
                $success = false;
                $message = "Veuillez choisir un abonnement";
            }

            $request->getSession()->set('recharge_subscription_id', $subscriptionId);
            $subscription = $this->subscriptionPlanRepository->find($subscriptionId);

            if (empty($subscription)) {
                $success = false;
                $message = "Veuillez choisir un abonnement";
            }

            $amount = $subscription->getAmount();
            if ($subscription->getCurrency() !== null) {
                $currency = $subscription->getCurrency();
            }
        } else {
            if (empty($amount)) {
                $success = false;
                $message = "Veuillez entrer un montant valide";
            }
        }

        $request->getSession()->set('recharge_amount', $amount);

        $phoneNumber = $this->paymentService->formatPhoneNumber($request->request->get('phoneNumber'));
        if (empty($phoneNumber)) {
            $success = false;
            $message = "Veuillez choisir un numero de telephone valide";
        }

        $payment = (new Payment())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setPhoneNumber($phoneNumber)
            ->setAmount($amount)
            ->setCurrency($currency)
            ->setResourceId($card->getId())
            ->setRef(uniqid())
        ;
        $paymentInfos = $this->paymentService->makePayment($payment);

        if (!$paymentInfos['success']) {
            $success = false;
            $message = $paymentInfos['message'];
        }

        $payment->setOrderNumber($paymentInfos['orderNumber']);
        $this->paymentRepository->save($payment);

        $paymentId = $payment->getId();
        $request->getSession()->set('recharge_payment_id', $paymentId);

        $request->getSession()->set("recharge_paying", true);

        return compact('success', 'message');
    }
}