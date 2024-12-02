<?php

namespace App\Controller;

use App\Entity\NfcCard;
use App\Entity\Vehicle;
use App\Form\TrackRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PublicController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em){}

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
