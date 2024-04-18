<?php

namespace OHMedia\BackendBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

class BoilerplateCommand extends Command
{
    private string $templateDir;
    private string $projectDir;
    private Filesystem $filesystem;
    private EnglishInflector $inflector;
    private SymfonyStyle $io;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir.'/';
        $this->templateDir = __DIR__.'/../../boilerplate/';
        $this->filesystem = new Filesystem();

        $this->inflector = new EnglishInflector();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ohmedia:backend:boilerplate')
            ->setDescription('Command to create the files needed for an entity')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $className = $this->io->ask('Class name of the entity');

        if (!$className) {
            $this->io->error('Please provide the class name');

            return Command::INVALID;
        }

        $icon = $this->io->ask('Enter a Bootstrap icon name excluding the "bi-" prefix');

        if (!$icon) {
            $this->io->error('Please provide the icon name');

            return Command::INVALID;
        }

        $singular = $this->inflector->singularize($className)[0];
        $plural = $this->inflector->pluralize($singular)[0];

        $parameters = [
            'singular' => $this->generateParameters($singular),
            'plural' => $this->generateParameters($plural),
            'icon' => $icon,
        ];

        $parameters['alias'] = strtolower(preg_replace('/[^A-Z]/', '', $parameters['singular']['pascal_case']));

        $parameters['determiner'] = preg_match('/^(a|e|i|o|u)/i', $className) ? 'an' : 'a';

        $pascalCase = $parameters['singular']['pascal_case'];
        $snakeCase = $parameters['singular']['snake_case'];

        $parameters['has_view_route'] = $this->io->confirm(sprintf(
            'Does the %s entity require a view route? (eg. /%s/{id})',
            $pascalCase,
            $parameters['singular']['kebab_case']
        ), false);

        $parameters['has_reorder'] = $this->io->confirm(sprintf(
            'Does the %s entity require reordering?',
            $pascalCase
        ), true);

        $entityPhpFile = sprintf('src/Entity/%s.php', $pascalCase);
        $repositoryPhpFile = sprintf('src/Repository/%sRepository.php', $pascalCase);
        $formPhpFile = sprintf('src/Form/%sType.php', $pascalCase);
        $controllerPhpFile = sprintf('src/Controller/Backend/%sController.php', $pascalCase);
        $entityChoicePhpFile = sprintf('src/Service/EntityChoice/%sEntityChoice.php', $pascalCase);
        $navItemProviderPhpFile = sprintf('src/Service/Backend/Nav/%sNavItemProvider.php', $pascalCase);
        $voterPhpFile = sprintf('src/Security/Voter/%sVoter.php', $pascalCase);
        $indexTwigFile = sprintf('templates/backend/%s/%s_index.html.twig', $snakeCase, $snakeCase);
        $createTwigFile = sprintf('templates/backend/%s/%s_create.html.twig', $snakeCase, $snakeCase);
        $editTwigFile = sprintf('templates/backend/%s/%s_edit.html.twig', $snakeCase, $snakeCase);
        $deleteTwigFile = sprintf('templates/backend/%s/%s_delete.html.twig', $snakeCase, $snakeCase);

        $this
            ->generateFile('Entity.tpl.php', $entityPhpFile, $parameters)
            ->generateFile('Repository.tpl.php', $repositoryPhpFile, $parameters)
            ->generateFile('Form.tpl.php', $formPhpFile, $parameters)
            ->generateFile('Controller.tpl.php', $controllerPhpFile, $parameters)
            ->generateFile('EntityChoice.tpl.php', $entityChoicePhpFile, $parameters)
            ->generateFile('NavItemProvider.tpl.php', $navItemProviderPhpFile, $parameters)
            ->generateFile('Voter.tpl.php', $voterPhpFile, $parameters)
            ->generateFile('twig/index.tpl.php', $indexTwigFile, $parameters)
            ->generateFile('twig/create.tpl.php', $createTwigFile, $parameters)
            ->generateFile('twig/edit.tpl.php', $editTwigFile, $parameters)
            ->generateFile('twig/delete.tpl.php', $deleteTwigFile, $parameters)
        ;

        if ($parameters['has_view_route']) {
            $viewTwigFile = sprintf('templates/backend/%s/%s_view.html.twig', $snakeCase, $snakeCase);

            $this->generateFile('twig/view.tpl.php', $viewTwigFile, $parameters);
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
}
