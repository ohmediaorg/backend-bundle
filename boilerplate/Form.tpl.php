<?php echo "<?php\n"; ?>

namespace App\Form;

use App\Entity\<?php echo $singular['pascal_case']; ?>;
// use Doctrine\ORM\EntityRepository;
// use Doctrine\ORM\QueryBuilder;
// use OHMedia\FileBundle\Form\Type\FileEntityType;
// use OHMedia\MetaBundle\Form\Type\MetaEntityType;
<?php if ($is_publishable) { ?>
use OHMedia\TimezoneBundle\Form\Type\DateTimeType;
<?php } else { ?>
// use OHMedia\TimezoneBundle\Form\Type\DateTimeType;
<?php } ?>
// use OHMedia\WysiwygBundle\Form\Type\WysiwygType;
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

        // TIP: these all do the same thing
        // $builder->add('name');
        // $builder->add('name', null);
        // $builder->add('name', TextType::class);

        // always use the file-bundle for files
        // for some reason it is necessary to specify 'data'
        // when usually 'mapped' => true (default) is enough
        // $builder->add('file', FileEntityType::class, [
        //     'data' => $<?php echo $singular['camel_case']; ?>->getFile(),
        // ]);
        // $builder->add('image', FileEntityType::class, [
        //     'image' => true,
        //     'data' => $<?php echo $singular['camel_case']; ?>->getImage(),
        // ]);

        // <input type="datetime-local">
        // always use the timezone-bundle to ensure timezones are good
        // $builder->add('starts_at', DateTimeType::class, [
        //     'widget' => 'single_text',
        // ]);
        // $builder->add('ends_at', DateTimeType::class, [
        //     'widget' => 'single_text',
        // ]);

        // if you have a checkbox for a toggle, make sure it is not required
        // $builder->add('is_featured', CheckboxType::class, [
        //     'required' => false,
        // ]);

        // <select>
        // $builder->add('selection', ChoiceType::class);

        // <select multiple>
        // $builder->add('selection', ChoiceType::class, [
        //     'multiple' => true,
        // ]);

        // array of <input type="radio">
        // $builder->add('selection', ChoiceType::class, [
        //     'expanded' => true,
        // ]);

        // array of <input type="checkbox">
        // $builder->add('selection', ChoiceType::class, [
        //     'expanded' => true,
        //     'multiple' => true,
        // ]);

        // <input type="url">
        // $builder->add('url', UrlType::class, [
        //     'default_protocol' => null,
        // ]);

        // TinyMCE
        // $builder->add('content', WysiwygType::class);

        // for a OneToOne or ManyToOne relationship selection
        // $builder->add('owner', EntityType::class, [
        //     'class' => User::class,
        //     'query_builder' => function (EntityRepository $er): QueryBuilder {
        //         return $er->createQueryBuilder('u')
        //             ->orderBy('u.email', 'ASC');
        //     },
        //     'choice_label' => 'email',
        // ]);
<?php if ($is_publishable) { ?>

        $builder->add('published_at', DateTimeType::class, [
            'label' => 'Published Date/Time',
            'required' => false,
            'help' => 'The <?php echo $singular['readable']; ?> will only be shown if this value is populated and in the past.',
            'widget' => 'single_text',
        ]);
<?php } ?>
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => <?php echo $singular['pascal_case']; ?>::class,
        ]);
    }
}
