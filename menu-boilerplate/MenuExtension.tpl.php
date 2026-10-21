<?php echo "<?php\n"; ?>

namespace App\Twig;

use App\Entity\<?php echo $singular['pascal_case']; ?>Item;
use App\Entity\<?php echo $singular['pascal_case']; ?>Section;
use App\Repository\<?php echo $singular['pascal_case']; ?>ItemRepository;
use OHMedia\FileBundle\Service\FileManager;
use OHMedia\PageBundle\Service\PageRenderer;
use OHMedia\SettingsBundle\Service\Settings;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class <?php echo $singular['pascal_case']; ?>Extension extends AbstractExtension
{
    public function __construct(
        private FileManager $fileManager,
        private <?php echo $singular['pascal_case']; ?>ItemRepository $menuItemRepository,
        private PageRenderer $pageRenderer,
        private Settings $settings,
        private UrlGeneratorInterface $urlGenerator,
        private UrlHelper $urlHelper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('<?php echo $singular['snake_case']; ?>', [$this, 'menu'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function menu(Environment $twig): string
    {
        $now = DateTimeUtil::getDateTimeUtc();

        $items = $this->menuItemRepository
            ->createQueryBuilder('i')
            ->addSelect('s')
            ->addSelect('m')
            ->join('i.section', 's')
            ->join('s.menu', 'm')
            ->where('i.published_at IS NOT NULL')
            ->andWhere('i.published_at <= :now')
            ->andWhere('s.published_at IS NOT NULL')
            ->andWhere('s.published_at <= :now')
            ->andWhere('m.published_at IS NOT NULL')
            ->andWhere('m.published_at <= :now')
            ->setParameter('now', $now)
            ->orderBy('m.ordinal', \SortDirection::Ascending)
            ->addOrderBy('s.ordinal', \SortDirection::Ascending)
            ->addOrderBy('i.ordinal', \SortDirection::Ascending)
            ->getQuery()
            ->getResult();

        $menus = [];

        foreach ($items as $item) {
            $section = $item->getSection();
            $cid = $section->getId();

            $menu = $section->getMenu();
            $mid = $menu->getId();

            if (!isset($menus[$mid])) {
                $menus[$mid] = [
                    'entity' => $menu,
                    'sections' => [],
                ];
            }

            if (!isset($menus[$mid]['sections'][$cid])) {
                $menus[$mid]['sections'][$cid] = [
                    'entity' => $section,
                    'items' => [],
                ];
            }

            $menus[$mid]['sections'][$cid]['items'][] = $item;
        }

        if (!$menus) {
            return '<p>No menu found.</p>';
        }

        $pagePath = $this->pageRenderer->getCurrentPage()->getPath();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Restaurant',
            'url' => $this->urlGenerator->generate('oh_media_page_frontend', [
                'path' => $pagePath,
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'name' => $this->settings->get('schema_organization_name'),
            'hasMenu' => [],
        ];

        $slug = $this->pageRenderer->getDynamicPart();

        foreach ($menus as $mid => $menu) {
            if (!$slug) {
                // no slug so use the first menu's
                $slug = $menu['entity']->getSlug();
            }

            $menus[$mid]['active'] = $menu['entity']->getSlug() === $slug;

            $menuSchema = [
                '@type' => 'Menu',
                'name' => (string) $menu['entity'],
                'hasMenuSection' => [],
            ];

            foreach ($menu['sections'] as $section) {
                $menuSchema['hasMenuSection'][] = $this->getMenuSectionSchema(
                    $section['entity'],
                    ...$section['items'],
                );
            }

            $schema['hasMenu'][] = $menuSchema;
        }

        return $twig->render('@frontend/<?php echo $singular['snake_case']; ?>/<?php echo $singular['snake_case']; ?>.html.twig', [
            'menus' => $menus,
            'schema' => $schema,
            'menu_page_path' => $pagePath,
        ]);
    }

    private function getMenuSectionSchema(
        <?php echo $singular['pascal_case']; ?>Section $section,
        <?php echo $singular['pascal_case']; ?>Item ...$items,
    ): array {
        $schema = [
            '@type' => 'MenuSection',
            'name' => (string) $section,
            'hasMenuItem' => [],
        ];

        foreach ($items as $item) {
            $schema['hasMenuItem'][] = $this->getMenuItemSchema($item);
        }

        return $schema;
    }

    private function getMenuItemSchema(<?php echo $singular['pascal_case']; ?>Item $item): array
    {
        $suitableForDiet = [];

        if ($item->isDairyFree()) {
            $suitableForDiet[] = 'https://schema.org/LowLactoseDiet';
        }

        if ($item->isGlutenFree()) {
            $suitableForDiet[] = 'https://schema.org/GlutenFreeDiet';
        }

        if ($item->isVegan()) {
            $suitableForDiet[] = 'https://schema.org/VeganDiet';
        }

        if ($item->isVegetarian()) {
            $suitableForDiet[] = 'https://schema.org/VegetarianDiet';
        }

        $prices = $item->getPrices();

        if (1 === $prices->count()) {
            $offers = [
                '@type' => 'Offer',
                'price' => $prices->first()->getAmount(),
                'priceCurrency' => 'CAD',
            ];
        } else {
            $offers = [];

            foreach ($prices as $price) {
                $offers[] = [
                    '@type' => 'Offer',
                    'name' => $price->getLabel(),
                    'price' => $price->getAmount(),
                    'priceCurrency' => 'CAD',
                ];
            }
        }

        $schema = [
            '@type' => 'MenuItem',
            'name' => (string) $item,
            'description' => $item->getDescription(),
            'offers' => $offers,
            'suitableForDiet' => $suitableForDiet,
        ];

        if ($image = $item->getImage()) {
            $webPath = $this->fileManager->getWebPath($image);

            $schema['image'] = $this->urlHelper->getAbsoluteUrl($webPath);
        }

        return $schema;
    }
}
