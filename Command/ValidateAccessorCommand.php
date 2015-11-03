<?php

namespace Visca\Bundle\DoctrineBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand as BaseDoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Visca\Bundle\DoctrineBundle\Validator\Exceptions\ViolationException;

/**
 * Class ValidateGetterAndSetterCommand.
 */
final class ValidateAccessorCommand extends BaseDoctrineCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:validate:accessor')
            ->setDescription(
                'Validate getters and setters.'
            )
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'A bundle name.'
            )
            ->addOption(
                'em',
                null,
                InputOption::VALUE_REQUIRED,
                'The entity manager to use for this command.',
                'default'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $mappingCollection = $this
            ->getContainer()
            ->get('visca_doctrine.mapping.metadata_collector')
            ->collect(
                $name,
                ''
            );

        foreach ($mappingCollection->getMetadata() as $metadata) {
            try {
                $this
                    ->getContainer()
                    ->get('visca_doctrine.validator.accessor')
                    ->validate($metadata);

                $output->writeln(
                    sprintf('<info>[OK]</info>   %s', $metadata->name)
                );
            } catch (ViolationException $ex) {
                $output->writeln('<error>[FAIL]</error> '.$metadata->name);

                foreach ($ex->getViolations() as $violation) {
                    $output->writeln(
                        sprintf(
                            '<comment>%s</comment>',
                            $violation->getMessage()
                        )
                    );
                }
                $output->writeln('');
            }
        }
    }
}
