<?php echo "<?php\n"; ?>

namespace App\Form\Page;

use OHMedia\PageBundle\Form\Type\AbstractPageTemplateType;

class MenuPage extends AbstractPageTemplateType
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
        return '@frontend/menu_page.html.twig';
    }

    public static function getTemplateName(): string
    {
        return 'Menu';
    }
}
