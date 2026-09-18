<?php

namespace App\Form;

use App\Entity\MenuItem;
use App\Entity\MenuSection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use OHMedia\FileBundle\Form\Type\FileEntityType;
use OHMedia\TimezoneBundle\Form\Type\DateTimeType;
use OHMedia\WysiwygBundle\Form\Type\WysiwygType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $menuItem = $options['data'];

        $builder->add('name');

        $builder->add('description', WysiwygType::class, [
            'required' => false,
            'allow_shortcodes' => false,
        ]);

        $builder->add('image', FileEntityType::class, [
            'image' => true,
            'required' => false,
        ]);

        $builder->add('prices', CollectionType::class, [
            'entry_type' => MenuItemPriceType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'error_bubbling' => false,
        ]);

        $tags = $builder->create('tags', FormType::class, [
            'inherit_data' => true,
            'required' => false,
        ]);

        $builder->add($tags);

        $tags->add('favourite', CheckboxType::class, [
            'required' => false,
            'label' => 'Fan Favourite',
        ]);

        $tags->add('dairy_free', CheckboxType::class, [
            'required' => false,
            'label' => 'Dairy-Free',
        ]);

        $tags->add('eggs', CheckboxType::class, [
            'required' => false,
            'label' => 'Contains eggs',
        ]);

        $tags->add('gluten_free', CheckboxType::class, [
            'required' => false,
            'label' => 'Gluten-Free',
        ]);

        $tags->add('organic', CheckboxType::class, [
            'required' => false,
        ]);

        $tags->add('spicy', CheckboxType::class, [
            'required' => false,
        ]);

        $tags->add('vegan', CheckboxType::class, [
            'required' => false,
        ]);

        $tags->add('vegetarian', CheckboxType::class, [
            'required' => false,
        ]);

        if ($menuItem->getId()) {
            $builder->add('section', EntityType::class, [
                'class' => MenuSection::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('s')
                        ->join('s.menu', 'm')
                        ->orderBy('m.ordinal', \SortDirection::Ascending)
                        ->addOrderBy('s.ordinal', \SortDirection::Ascending);
                },
                'group_by' => 'menu',
            ]);
        }

        $builder->add('published_at', DateTimeType::class, [
            'label' => 'Published Date/Time',
            'required' => false,
            'help' => 'The menu item will only be shown if this value is populated and in the past.',
            'widget' => 'single_text',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuItem::class,
        ]);
    }
}
