<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }

    public function findBestSeries()
    {
        //via DQL
/*        $entityManager = $this->getEntityManager();
        $dql = "
            SELECT s
            FROM App\Entity\Series s
            WHERE s.popularity > 100
            AND s.vote > 8
            ORDER BY s.popularity DESC 
        ";
        $query = $entityManager->createQuery($dql);*/


        //version QueryBuilder
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->leftJoin('s.seasons', 'seas')
        ->addSelect('seas');

        $queryBuilder->andWhere('s.popularity > 100');
        $queryBuilder->andWhere('s.vote > 8');
        $queryBuilder->addOrderBy('s.popularity', 'DESC');
        $query = $queryBuilder->getQuery();

        $query->setMaxResults(50);
        $paginator = new Paginator($query);

        return $paginator;

    }

}
