<?php

namespace App\Repository;

use App\Entity\ArrivalJournalDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArrivalJournalDetail>
 *
 * @method ArrivalJournalDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArrivalJournalDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArrivalJournalDetail[]    findAll()
 * @method ArrivalJournalDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArrivalJournalDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArrivalJournalDetail::class);
    }

    // Добавьте кастомные методы для работы с репозиторием, если необходимо
}