<?php echo "<?php\n"; ?>

namespace App\Repository;

use App\Entity\<?php echo $singular['pascal_case']; ?>ItemPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method <?php echo $singular['pascal_case']; ?>ItemPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method <?php echo $singular['pascal_case']; ?>ItemPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method <?php echo $singular['pascal_case']; ?>ItemPrice[]    findAll()
 * @method <?php echo $singular['pascal_case']; ?>ItemPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class <?php echo $singular['pascal_case']; ?>ItemPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, <?php echo $singular['pascal_case']; ?>ItemPrice::class);
    }
}
