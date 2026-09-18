<?php echo "<?php\n"; ?>

namespace App\Form\Page;

use OHMedia\PageBundle\Form\Type\AbstractPageTemplateType;

class <?php echo $singular['pascal_case']; ?>Page extends AbstractPageTemplateType
{
    protected function buildFormContent()
    {
        $this
            ->addPageContentText('title')
            ->addPageContentImage('banner', [
                'required' => false,
            ])
            // ->addPageContentWysiwyg('content')
        ;
    }

    public static function getTemplate(): string
    {
        return '@frontend/<?php echo $singular['snake_case']; ?>_page.html.twig';
    }

    public static function getTemplateName(): string
    {
        return '<?php echo $singular['title']; ?>';
    }
}
