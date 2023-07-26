<?php

namespace App\Command;

use App\Service\MixRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

#[AsCommand(
    name: 'app:talk-to-me',
    description: 'Une commande de ouf qui fait juste une chose',
)]
class TalkToMeCommand extends Command
{
    public function __construct(private MixRepository $mixRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Ton nom')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'Crie ça ?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io          = new SymfonyStyle($input, $output);
        $name        = $input->getArgument('name') ?: 'qui que tu sois';
        $shouldIYell = $input->getOption('yell');

        $message = sprintf('Salut %s!', $name);

        if ($shouldIYell)
        {
            $message = strtoupper($message);
        }

        $io->success($message);

        if ($io->confirm('Tu veux une recommandation ?'))
        {
            $mixes = $this->mixRepository->findAll();
            $mix   = $mixes[array_rand($mixes)];

            $io->note($mix['title']);
        }

        return Command::SUCCESS;
    }
}
