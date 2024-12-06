<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\SenderId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function save (Payment $payment, bool $flush = true): void
    {
        $this->getEntityManager()->persist($payment);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByOrderNumber(string $orderNumber): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.orderNumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
