<?php echo "<?php\n"; ?>

namespace App\Twig;

use App\Entity\<?php echo $singular['pascal_case']; ?>Item;
use App\Entity\<?php echo $singular['pascal_case']; ?>Section;
use App\Repository\<?php echo $singular['pascal_case']; ?>ItemRepository;
use OHMedia\FileBundle\Service\FileManager;
use OHMedia\SettingsBundle\Service\Settings;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class <?php echo $singular['pascal_case']; ?>Extension extends AbstractExtension
{
    public function __construct(
        private FileManager $fileManager,
        private <?php echo $singular['pascal_case']; ?>ItemRepository $menuItemRepository,
        private RequestStack $requestStack,
        private Settings $settings,
        private UrlHelper $urlHelper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu', [$this, 'menu'], [
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

        $request = $this->requestStack->getMainRequest();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Restaurant',
            'url' => $this->urlHelper->getAbsoluteUrl($request->getPathInfo()),
            'name' => $this->settings->get('schema_organization_name'),
            'hasMenu' => [],
        ];

        foreach ($menus as $menu) {
            $menuSchema = [
                '@type' => 'Menu',
                'name' => (string) $menu['entity'],
                'hasMenuSection' => [],
            ];

            foreach ($menu['sections'] as $section) {
                $menuSchema['hasMenuSection'][] = $this->get<?php echo $singular['pascal_case']; ?>SectionSchema(
                    $section['entity'],
                    ...$section['items'],
                );
            }

            $schema['hasMenu'][] = $menuSchema;
        }

        return $twig->render('@frontend/menu/menu.html.twig', [
            'menus' => $menus,
            'schema' => $schema,
        ]);
    }

    private function get<?php echo $singular['pascal_case']; ?>SectionSchema(
        <?php echo $singular['pascal_case']; ?>Section $section,
        <?php echo $singular['pascal_case']; ?>Item ...$items,
    ): array {
        $schema = [
            '@type' => '<?php echo $singular['pascal_case']; ?>Section',
            'name' => (string) $section,
            'hasMenuItem' => [],
        ];

        foreach ($items as $item) {
            $schema['hasMenuItem'][] = $this->get<?php echo $singular['pascal_case']; ?>ItemSchema($item);
        }

        return $schema;
    }

    private function get<?php echo $singular['pascal_case']; ?>ItemSchema(<?php echo $singular['pascal_case']; ?>Item $item): array
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
            '@type' => '<?php echo $singular['pascal_case']; ?>Item',
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
