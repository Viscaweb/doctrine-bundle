<?php

namespace Visca\Bundle\DoctrineBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Visca\Bundle\DoctrineBundle\Util\EntityManagerPurger;

/**
 * Class PurgerCommand.
 */
final class PurgerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:purge:entity-manager')
            ->setDescription(
                'Purge the entity manager. DO NOT EXECUTE ON PRODUCTION !'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Set this parameter to execute this action'
            )
            ->addOption(
                'em',
                null,
                InputOption::VALUE_REQUIRED,
                'The entity manager to use for this command.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManagerPurger = new EntityManagerPurger();

        if ($input->isInteractive() && !$input->getOption('force')) {
            /** @var DialogHelper $dialog */
            $dialog = $this->getHelperSet()->get('dialog');
            if (!$dialog->askConfirmation(
                $output,
                '<question>Careful, database will be purged. Do you want to continue Y/N ?</question>',
                false
            )
            ) {
                return;
            }
        }

        $objectManagers = $this->getObjectManagers($input->getOption('em'));

        foreach ($objectManagers as $managerName => $manager) {
            if ($manager instanceof EntityManager) {
                $entityManagerPurger->purge($manager);
                $output->writeln(
                    sprintf(
                        '<info>[OK]</info> Manager "%s" has been purged',
                        $managerName
                    )
                );
            } else {
                $output->writeln(
                    sprintf(
                        '<comment>[IGNORED]/comment> Manager "%s" has been ignored',
                        $managerName
                    )
                );
            }
        }
    }

    /**
     * @param string $nameFilter
     *
     * @return ObjectManager[]
     */
    private function getObjectManagers($nameFilter = null)
    {
        $managerNames = $this
            ->getContainer()
            ->get('doctrine')
            ->getManagerNames();

        $objectManagers = [];

        foreach (array_keys($managerNames) as $managerName) {
            if (null !== $nameFilter) {
                if ($nameFilter != $managerName) {
                    continue;
                }
            }

            $objectManagers[$managerName] = $this
                ->getContainer()
                ->get('doctrine')
                ->getManager($managerName);
        }

        return $objectManagers;
    }
}
