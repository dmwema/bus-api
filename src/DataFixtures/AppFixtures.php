<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use App\Entity\Enterprise;
use App\Entity\Line;
use App\Entity\NfcCard;
use App\Entity\Region;
use App\Entity\SubscriptionPlan;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        /* CURRENCIES */
        $cdfCurrency = (new Currency())
            ->setName("Francs Congolais")
            ->setCode("CDF")
            ->setUsdRate(2800)
        ;
        $usdCurrency = (new Currency())
            ->setName("Dollar Americain")
            ->setCode("USD")
            ->setUsdRate(1)
            ->setCurrent(true)
        ;

        $manager->persist($cdfCurrency);
        $manager->persist($usdCurrency);

        /* ENTERPRISES */
        for ($i = 0; $i < 5; $i ++) {
            $enterprise = (new Enterprise())
                ->setName($faker->company())
                ->setAddress($faker->address())
                ->setCreatedAt(new \DateTime())
            ;
            $manager->persist($enterprise);
        }

        /* REGIONS */
        $region = (new Region())
            ->setName('Kinshasa')
            ->setShape('Kinshasa')
            ->setCreatedAt(new \DateTime())
        ;
        $manager->persist($region);

        /* LINES */
        $line = (new Line())
            ->setName('Lemba - Gombe')
            ->setDescription('De super lemba a la gare centrale')
            ->setPaymentType('DEDUCTED')
            ->setTicketPrice(2)
            ->setCurrency($usdCurrency)
            ->setCreatedAt(new \DateTime())
            ->setRegion($region)
        ;
        $manager->persist($line);

        /* NFC CARDS */
        for ($i = 0; $i < 5; $i++) {
            $card = (new NfcCard())
                ->setUid(uniqid())
                ->setCardHolder($faker->name)
                ->setPhoneNumber($faker->phoneNumber())
                ->setBalance(0)
                ->setCreatedAt(new \DateTime())
                ->setIsActive(true)
                ->setCode(uniqid())
                ->addLiness($line)
            ;
            $line->addNfcCards($card);
            $manager->persist($card);
            $manager->persist($line);
        }

        $subs = ['Etudiant', 'Travailleur'];
        for ($i = 0; $i < 2; $i++) {
            $subscriptionPlan = (new SubscriptionPlan())
                ->setTitle($subs[$i])
                ->setDescription("Abonnement mensuel.")
                ->setDuration(30)
                ->setAmount(20)
                ->setCurrency($usdCurrency)
                ->setCreatedAt(new \DateTime())
            ;
            $manager->persist($subscriptionPlan);
        }

        $pos = [
            'V1' => [
                'lat' => -4.3390034241561,
                'lng' => 15.285175112679,
                'mat' => '123A'
            ],
            'V2' => [
                'lat' => -4.386183844749,
                'lng' => 15.396753695062,
                'mat' => '123B'
            ],
        ];

        foreach ($pos as $name => $p) {
            $vehicle = (new Vehicle())
                ->setName($name)
                ->setGpx(['N'])
                ->setLine($line)
                ->setMatricule($p['mat'])
                ->setCurrentLat($p['lat'])
                ->setCurrentLng($p['lng'])
            ;
            $manager->persist($vehicle);
        }

        $manager->flush();
    }
}
