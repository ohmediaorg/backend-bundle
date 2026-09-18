<?php

namespace App\Repository;

use App\Entity\MenuSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;

/**
 * @method MenuSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuSection[]    findAll()
 * @method MenuSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuSection::class);
    }

    public function save(MenuSection $menuSection, bool $flush = false): void
    {
        $this->getEntityManager()->persist($menuSection);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MenuSection $menuSection, bool $flush = false): void
    {
        $this->getEntityManager()->remove($menuSection);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createPublishedQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
        return $this->createQueryBuilder($alias, $indexBy)
            ->andWhere($alias.'.published_at IS NOT NULL')
            ->andWhere($alias.'.published_at <= :now')
            ->setParameter('now', DateTimeUtil::getDateTimeUtc())
            ->orderBy($alias.'.ordinal', \SortDirection::Ascending);
    }
}
