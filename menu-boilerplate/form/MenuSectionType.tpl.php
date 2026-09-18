<?php echo "<?php\n"; ?>

namespace App\Form;

use App\Entity\Menu;
use App\Entity\MenuSection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use OHMedia\TimezoneBundle\Form\Type\DateTimeType;
use OHMedia\WysiwygBundle\Form\Type\WysiwygType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuSectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $menuSection = $options['data'];

        $builder->add('name');

        $builder->add('description', WysiwygType::class, [
            'required' => false,
            'allow_shortcodes' => false,
        ]);

        if ($menuSection->getId()) {
            $builder->add('menu', EntityType::class, [
                'class' => Menu::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.ordinal', \SortDirection::Ascending);
                },
            ]);
        }

        $builder->add('published_at', DateTimeType::class, [
            'label' => 'Published Date/Time',
            'required' => false,
            'help' => 'The section will only be shown if this value is populated and in the past.',
            'widget' => 'single_text',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuSection::class,
        ]);
    }
}
