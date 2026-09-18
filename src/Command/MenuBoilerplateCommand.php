<?php

namespace OHMedia\BackendBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

class MenuBoilerplateCommand extends Command
{
    private string $templateDir;
    private string $projectDir;
    private Filesystem $filesystem;
    private EnglishInflector $inflector;
    private SymfonyStyle $io;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        string $projectDir,
    ) {
        $this->projectDir = $projectDir.'/';
        $this->templateDir = __DIR__.'/../../menu-boilerplate/';
        $this->filesystem = new Filesystem();

        $this->inflector = new EnglishInflector();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ohmedia:backend:menu-boilerplate')
            ->setDescription('Command to create the files needed for a menu module')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $prefix = $this->io->ask('Enter a prefix for the menu (or leave blank for none)', '');

        $icon = $this->io->ask('Enter a Bootstrap icon name excluding the "bi-" prefix', 'fork-knife');

        if (!$icon) {
            $this->io->error('Please provide the icon name');

            return Command::INVALID;
        }

        $className = $prefix
            ? u($prefix)->camel()->title().'Menu'
            : 'Menu';

        $parameters = [
            'singular' => $this->generateParameters($className),
            'plural' => $this->generateParameters($className),
            'icon' => $icon,
        ];

        $parameters['alias'] = strtolower(preg_replace('/[^A-Z]/', '', $parameters['singular']['pascal_case']));

        $parameters['determiner'] = preg_match('/^(a|e|i|o|u)/i', $className) ? 'an' : 'a';

        $pascalCase = $parameters['singular']['pascal_case'];
        $snakeCase = $parameters['singular']['snake_case'];

        $phpFileMap = [
            'controller/MenuController.tpl.php' => 'src/Controller/Backend/%sController.php',
            'controller/MenuItemController.tpl.php' => 'src/Controller/Backend/%sItemController.php',
            'controller/MenuSectionController.tpl.php' => 'src/Controller/Backend/%sSectionController.php',
            'entity/Menu.tpl.php' => 'src/Entity/%s.php',
            'entity/MenuItem.tpl.php' => 'src/Entity/%sItem.php',
            'entity/MenuItemPrice.tpl.php' => 'src/Entity/%sItemPrice.php',
            'entity/MenuSection.tpl.php' => 'src/Entity/%sSection.php',
            'form/MenuType.tpl.php' => 'src/Entity/%sType.php',
            'form/MenuItemType.tpl.php' => 'src/Entity/%sItemType.php',
            'form/MenuItemPriceType.tpl.php' => 'src/Entity/%sItemPriceType.php',
            'form/MenuSectionType.tpl.php' => 'src/Entity/%sSectionType.php',
            'form/page/MenuPage.tpl.php' => 'src/Form/Page/%sPage.php',
            'repository/MenuRepository.tpl.php' => 'src/Entity/%sRepository.php',
            'repository/MenuItemRepository.tpl.php' => 'src/Entity/%sItemRepository.php',
            'repository/MenuItemPriceRepository.tpl.php' => 'src/Entity/%sItemPriceRepository.php',
            'repository/MenuSectionRepository.tpl.php' => 'src/Entity/%sSectionRepository.php',
            'voter/MenuItemVoter.tpl.php' => 'src/Security/Voter/%sItemVoter.php',
            'voter/MenuSectionVoter.tpl.php' => 'src/Security/Voter/%sSectionVoter.php',
            'voter/MenuVoter.tpl.php' => 'src/Security/Voter/%sVoter.php',
            'MenuEntityChoice.tpl.php' => 'src/Service/EntityChoice/%sEntityChoice.php',
            'MenuExtension.tpl.php' => 'src/Twig/%sExtension.php',
            'MenuNavItemProvider.tpl.php' => 'src/Service/Backend/Nav/%sNavItemProvider.php',
        ];

        $twigFileMap = [
            'backend/menu/menu_create.tpl.php' => 'templates/backend/%s/%s_create.html.twig',
            'backend/menu/menu_delete.tpl.php' => 'templates/backend/%s/%s_delete.html.twig',
            'backend/menu/menu_edit.tpl.php' => 'templates/backend/%s/%s_edit.html.twig',
            'backend/menu/menu_index.tpl.php' => 'templates/backend/%s/%s_index.html.twig',
            'backend/menu/menu_view.tpl.php' => 'templates/backend/%s/%s_view.html.twig',
            'backend/menu/menu_item_create.tpl.php' => 'templates/backend/%s/%s_item_create.html.twig',
            'backend/menu/menu_item_delete.tpl.php' => 'templates/backend/%s/%s_item_delete.html.twig',
            'backend/menu/menu_item_edit.tpl.php' => 'templates/backend/%s/%s_item_edit.html.twig',
            'backend/menu/menu_item_view.tpl.php' => 'templates/backend/%s/%s_item_view.html.twig',
            'backend/menu/menu_section_create.tpl.php' => 'templates/backend/%s/%s_section_create.html.twig',
            'backend/menu/menu_section_delete.tpl.php' => 'templates/backend/%s/%s_section_delete.html.twig',
            'backend/menu/menu_section_edit.tpl.php' => 'templates/backend/%s/%s_section_edit.html.twig',
            'backend/menu/menu_section_view.tpl.php' => 'templates/backend/%s/%s_section_view.html.twig',
            'frontend/menu/menu.tpl.php' => 'templates/frontend/%s/%s.html.twig',
            'frontend/menu_page.tpl.php' => 'templates/frontend/%s_page.html.twig',
        ];

        foreach ($phpFileMap as $src => $dest) {
            $dest = sprintf($dest, $pascalCase);

            $this->generateFile($src, $desc, $parameters);
        }

        foreach ($twigFileMap as $src => $dest) {
            $dest = sprintf($dest, $snakeCase);

            $this->generateFile($src, $desc, $parameters);
        }

        // TODO: copy svg files

        return Command::SUCCESS;
    }

    private function generateParameters(string $word)
    {
        $camelCase = u($word)->camel();
        $snakeCase = u($word)->snake();
        $pascalCase = u($camelCase)->title();
        $kebabCase = u($snakeCase)->replace('_', '-');
        $readable = u($snakeCase)->replace('_', ' ');
        $title = u($readable)->title(true);

        return [
            'camel_case' => $camelCase,
            'snake_case' => $snakeCase,
            'pascal_case' => $pascalCase,
            'kebab_case' => $kebabCase,
            'readable' => $readable,
            'title' => $title,
        ];
    }

    private function generateFile(string $template, string $destination, array $parameters)
    {
        $absoluteDestination = $this->projectDir.$destination;

        if (file_exists($absoluteDestination)) {
            $continue = $this->io->confirm(sprintf(
                'The destination file <fg=yellow>%s</> exists. Do you want to overwrite it?',
                $destination
            ), false);

            if (!$continue) {
                return $this;
            }
        }

        ob_start();

        extract($parameters);

        include $this->templateDir.$template;

        $contents = ob_get_clean();

        $this->filesystem->mkdir(\dirname($absoluteDestination));

        file_put_contents($absoluteDestination, $contents);

        $this->io->success(sprintf('Generated %s', $destination));

        return $this;
    }
}
