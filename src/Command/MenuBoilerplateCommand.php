<?php

namespace OHMedia\BackendBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

use function Symfony\Component\String\u;

class MenuBoilerplateCommand extends Command
{
    private string $templateDir;
    private string $projectDir;
    private Filesystem $filesystem;
    private SymfonyStyle $io;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        string $projectDir,
    ) {
        $this->projectDir = $projectDir.'/';
        $this->templateDir = __DIR__.'/../../menu-boilerplate/';
        $this->filesystem = new Filesystem();

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

        $prefix = $this->io->ask('Enter a prefix for the Menu entity (leave blank for none)', '');

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
            'icon' => $icon,
        ];

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
            'form/MenuType.tpl.php' => 'src/Form/%sType.php',
            'form/MenuItemType.tpl.php' => 'src/Form/%sItemType.php',
            'form/MenuItemPriceType.tpl.php' => 'src/Form/%sItemPriceType.php',
            'form/MenuSectionType.tpl.php' => 'src/Form/%sSectionType.php',
            'form/page/MenuPage.tpl.php' => 'src/Form/Page/%sPage.php',
            'repository/MenuRepository.tpl.php' => 'src/Repository/%sRepository.php',
            'repository/MenuItemRepository.tpl.php' => 'src/Repository/%sItemRepository.php',
            'repository/MenuItemPriceRepository.tpl.php' => 'src/Repository/%sItemPriceRepository.php',
            'repository/MenuSectionRepository.tpl.php' => 'src/Repository/%sSectionRepository.php',
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
            'backend/menu_item/menu_item_create.tpl.php' => 'templates/backend/%s_item/%s_item_create.html.twig',
            'backend/menu_item/menu_item_delete.tpl.php' => 'templates/backend/%s_item/%s_item_delete.html.twig',
            'backend/menu_item/menu_item_edit.tpl.php' => 'templates/backend/%s_item/%s_item_edit.html.twig',
            'backend/menu_item/menu_item_form.tpl.php' => 'templates/backend/%s_item/%s_item_form.html.twig',
            'backend/menu_section/menu_section_create.tpl.php' => 'templates/backend/%s_section/%s_section_create.html.twig',
            'backend/menu_section/menu_section_delete.tpl.php' => 'templates/backend/%s_section/%s_section_delete.html.twig',
            'backend/menu_section/menu_section_edit.tpl.php' => 'templates/backend/%s_section/%s_section_edit.html.twig',
            'backend/menu_section/menu_section_view.tpl.php' => 'templates/backend/%s_section/%s_section_view.html.twig',
            'frontend/menu/menu.tpl.php' => 'templates/frontend/%s/%s.html.twig',
        ];

        foreach ($phpFileMap as $src => $dest) {
            $dest = sprintf($dest, $pascalCase);

            $this->generateFile($src, $dest, $parameters);
        }

        foreach ($twigFileMap as $src => $dest) {
            $dest = sprintf($dest, $snakeCase, $snakeCase);

            $this->generateFile('twig/'.$src, $dest, $parameters);
        }

        // this file has 1 param in sprintf
        $this->generateFile(
            'twig/frontend/menu_page.tpl.php',
            sprintf('templates/frontend/%s_page.html.twig', $snakeCase),
            $parameters
        );

        $svgs = scandir($this->templateDir.'/twig/frontend/menu/svg');

        foreach ($svgs as $svg) {
            if ('.' === $svg || '..' === $svg) {
                continue;
            }

            $this->copySvg($svg, $parameters);
        }

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

    private function copySvg(string $svg, array $parameters)
    {
        $source = 'twig/frontend/menu/svg/'.$svg;
        $destination = 'templates/frontend/'.$parameters['singular']['snake_case'].'/svg/'.$svg;

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

        $this->filesystem->mkdir(\dirname($absoluteDestination));

        $contents = file_get_contents($this->templateDir.$source);

        file_put_contents($absoluteDestination, $contents);

        $this->io->success(sprintf('Generated %s', $destination));

        return $this;
    }
}
