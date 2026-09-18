<?php echo "<?php\n"; ?>

namespace App\Repository;

use App\Entity\<?php echo $singular['pascal_case']; ?>Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;

/**
 * @method <?php echo $singular['pascal_case']; ?>Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method <?php echo $singular['pascal_case']; ?>Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method <?php echo $singular['pascal_case']; ?>Item[]    findAll()
 * @method <?php echo $singular['pascal_case']; ?>Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class <?php echo $singular['pascal_case']; ?>ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, <?php echo $singular['pascal_case']; ?>Item::class);
    }

    public function save(<?php echo $singular['pascal_case']; ?>Item $menuItem, bool $flush = false): void
    {
        $this->getEntityManager()->persist($menuItem);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(<?php echo $singular['pascal_case']; ?>Item $menuItem, bool $flush = false): void
    {
        $this->getEntityManager()->remove($menuItem);

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
