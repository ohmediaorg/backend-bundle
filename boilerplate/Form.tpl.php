<?php echo "<?php\n"; ?>

namespace App\Form;

use App\Entity\<?php echo $singular['pascal_case']; ?>;
// use Doctrine\ORM\EntityRepository;
// use Doctrine\ORM\QueryBuilder;
// use OHMedia\FileBundle\Form\Type\FileEntityType;
// use OHMedia\MetaBundle\Form\Type\MetaEntityType;
// use OHMedia\TimezoneBundle\Form\Type\DateTimeType;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
// use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
// use Symfony\Component\Form\Extension\Core\Type\EmailType;
// use Symfony\Component\Form\Extension\Core\Type\IntegerType;
// use Symfony\Component\Form\Extension\Core\Type\MoneyType;
// use Symfony\Component\Form\Extension\Core\Type\NumberType;
// use Symfony\Component\Form\Extension\Core\Type\TelType;
// use Symfony\Component\Form\Extension\Core\Type\TextareaType;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class <?php echo $singular['pascal_case']; ?>Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $<?php echo $singular['camel_case']; ?> = $options['data'];

        $builder
            // TIP: these all do the same thing
            // ->add('name')
            // ->add('name', null)
            // ->add('name', TextType::class)

            // always use the file-bundle for files
            // for some reason it is necessary to specify 'data'
            // when usually 'mapped' => true (default) is enough
            // ->add('file', FileEntityType::class, [
            //     'data' => $<?php echo $singular['camel_case']; ?>->getFile(),
            // ])
            // ->add('image', FileEntityType::class, [
            //     'image' => true,
            //     'data' => $<?php echo $singular['camel_case']; ?>->getFile(),
            // ])

            // always use the datetime-bundle to ensure timezones are good
            // ->add('start_datetime', DateTimeType::class)
            // ->add('end_datetime', DateTimeType::class)

            // if you have a checkbox for a toggle, make sure it is not required
            // ->add('is_featured', CheckboxType::class, [
            //     'required' => false,
            // ])

            // <select>
            // ->add('selection', ChoiceType::class)

            // <select multiple>
            // ->add('selection', ChoiceType::class, [
            //     'multiple' => true,
            // ])

            // array of <input type="radio">
            // ->add('selection', ChoiceType::class, [
            //     'expanded' => true,
            // ])

            // array of <input type="checkbox">
            // ->add('selection', ChoiceType::class, [
            //     'expanded' => true,
            //     'multiple' => true,
            // ])

            // for a OneToOne or ManyToOne relationship selection
            // ->add('owner', EntityType::class, [
            //     'class' => User::class,
            //     'query_builder' => function (EntityRepository $er): QueryBuilder {
            //         return $er->createQueryBuilder('u')
            //             ->orderBy('u.email', 'ASC');
            //     },
            //     'choice_label' => 'email',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => <?php echo $singular['pascal_case']; ?>::class,
        ]);
    }
}
