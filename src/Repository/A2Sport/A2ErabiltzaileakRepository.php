<?php

namespace App\Repository\A2Sport;

use App\Entity\A2Sport\A2Erabiltzaileak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method A2Erabiltzaileak|null find($id, $lockMode = null, $lockVersion = null)
 * @method A2Erabiltzaileak|null findOneBy(array $criteria, array $orderBy = null)
 * @method A2Erabiltzaileak[]    findAll()
 * @method A2Erabiltzaileak[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class A2ErabiltzaileakRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, A2Erabiltzaileak::class);
    }

    public function findOneByDni(string $dni): ?A2Erabiltzaileak
    {
        $ultimoCaracter = mb_substr($dni, -1);
        if (ctype_alpha($ultimoCaracter)) {
            $letra = $ultimoCaracter;
            $dni = substr($dni, 0, -1);
        } else {
            $letra = null;
        }
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.dni = :dni')
            ->setParameter('dni', $dni);
        if ($letra !== null) {
            $qb->andWhere('a.letra = :letra')
               ->setParameter('letra', $letra);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneByTarjeta(string $tarjeta): ?A2Erabiltzaileak
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.tarjeta = :tarjeta')
            ->setParameter('tarjeta',$tarjeta);
        return $qb->getQuery()->getOneOrNullResult();
    }

}