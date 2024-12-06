<?php

namespace App\Controller;

use App\Entity\NfcCard;
use App\Entity\Payment;
use App\Entity\Vehicle;
use App\Form\TrackRequestFormType;
use App\Repository\NfcCardRepository;
use App\Repository\SubscriptionPlanRepository;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PublicController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NfcCardRepository $cardRepository,
        private readonly SubscriptionPlanRepository $subscriptionPlanRepository,
        private readonly PaymentService $paymentService,
    ){}

    /**
     * @return Response
     */
    #[Route('/', name: 'app_public')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(TrackRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $code = $data['code'];
            $type = $data['type'];

            return $this->redirectToRoute('buses_map', [
                'code' => $code,
                'type' => $type
            ]);
        }

        return $this->render('public/index.html.twig', [
            'controller_name' => 'PublicController',
            'form'            => $form->createView()
        ]);
    }

    #[Route('/bus/mapview/{code}/{type}', name:'buses_map')]
    public function mapView($code, $type): Response{
        $card = $this->em->getRepository(NfcCard::class)->findOneBy(['code'=> $code]);
        if (!$card) {
            $this->addFlash('error',"Code fourni ($code)  ne correspond em aucune carte.");
            return $this->redirectToRoute('app_public');
        }

        $lines = $card->getLiness();
        $vehicles = [];

        foreach ($lines as $line) {
            $lineVehicles = $this->em->getRepository(Vehicle::class)->findBy(['line' => $line]);
            foreach ($lineVehicles as $vehicle) {
                $vehicles[] = $vehicle->toArray();
            };
        }

        return $this->render('public/mapview2.html.twig', ['vehicles'=>$vehicles,"card"=>$card, "type"=>$type,"code"=>$code]);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/recharge', name:'app_recharge')]
    public function recharge(Request $request): Response{
        $step = $request->getSession()->get('recharge_step') ?? 1;
        $subs = null;
        $type = $request->getSession()->get('recharge_type') ?? null;
        $paying = $request->getSession()->get('paying') ?? false;

        $card = null;
        $uid = $request->getSession()->get('recharge_card') ?? null;

        if ($uid != null) {
            $card = $this->cardRepository->findOneBy(['uid' => $uid]);
        }
        if ($request->getMethod() === Request::METHOD_POST) {
            if ($step == 1) {
                $type = $request->request->get('type');

                if (empty($type) || ($type != "FIXE" && $type != "SUBSCRIPTION")) {
                    $this->addFlash("error", "Vous devez choisir un type de recharge valide");
                    return $this->redirect($request->headers->get('referer'));
                }

                $uid = $request->request->get('card');
                $card = $this->cardRepository->findOneBy(['uid' => $uid]);
                if ($card === null) {
                    $this->addFlash("error", "Carte invalide");
                    return $this->redirect($request->headers->get('referer'));
                }

                $step++;
                $request->getSession()->set('recharge_step', $step);
                $request->getSession()->set('recharge_card', $uid);
                $request->getSession()->set('recharge_type', $type);
            } else if ($step == 2) {
                $amount = $request->request->get('amount');
                if (empty($amount)) {
                    $this->addFlash("error", "Veuillez entrer un montant valide");
                    return $this->redirect($request->headers->get('referer'));
                }

                $subscription = null;
                if ($type = "SUBSCRIPTION") {
                    $subscriptionId = $request->request->get('subscription');
                    if (empty($subscriptionId)) {
                        $this->addFlash("error", "Veuillez choisir un abonnement");
                        return $this->redirect($request->headers->get('referer'));
                    }
                    $subscription = $this->subscriptionPlanRepository->find($subscriptionId);
                    if (empty($subscription)) {
                        $this->addFlash("error", "Veuillez choisir un abonnement");
                        return $this->redirect($request->headers->get('referer'));
                    }
                    $phoneNumber = $this->paymentService->formatPhoneNumber($request->request->get('phoneNumber'));
                    if (empty($phoneNumber)) {
                        $this->addFlash("error", "Veuillez choisir un numero de telephone valide");
                        return $this->redirect($request->headers->get('referer'));
                    }

                    $payment = (new Payment())
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setPhoneNumber($phoneNumber)
                        ->setAmount($amount)
                        ->setResourceId($subscriptionId)
                        ->setRef(uniqid())
                    ;
                    $paymentInfos = $this->paymentService->makePayment($payment);

                    if (!$paymentInfos['success']) {
                        $this->addFlash("error", $paymentInfos['message']);
                        return $this->redirect($request->headers->get('referer'));
                    }

                    $payment
                        ->setOrderNumber($paymentInfos['orderNumber']);

                    $paying = true;
                    $request->getSession()->set("paying", $paying);
                    $this->addFlash("success", "Processuss de paiement en cours... Veuillez continuer avec votre telephone");
                    return $this->redirect($request->headers->get('referer'));
                }
            }
        }

        if ($type == "SUBSCRIPTION") {
            $subs = $this->subscriptionPlanRepository->findAll();
        }

        return $this->render('public/recharge.html.twig', [
            'step' => $step,
            'type' => $type,
            'subs' => $subs,
            'paying' => $paying,
            'card' => $card,
        ]);
    }

    #[Route('/bus/mapview/api', name:'buses_map-api')]
    public function mapViewJson(Request $request): Response{
        $code = $request->request->get('code');
        $type = $request->request->get('type');
        $card = $this->em->getRepository(NfcCard::class)->findOneBy(['code'=> $code]);
        if(!$card){
            $this->addFlash('error',"Code fourni ($code)  ne correspond em aucune carte.");
            return $this->json(["message"=>"Code fourni ($code)  ne correspond em aucune carte."],404);
        }
        $vehicles = $card->getLiness()[0]->getVehicles();
        return $this->json($vehicles, 200);


    }
}
