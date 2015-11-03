<?php

namespace Visca\Bundle\DoctrineBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand as BaseDoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateUniqueValuesClassCommand.
 */
class GenerateUniqueValuesClassCommand extends BaseDoctrineCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:generate:unique-values-class')
            ->setDescription(
                'Generates entity classes and method stubs from your mapping information.'
            )
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'A bundle name, a namespace, or a class name.'
            )
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'The path where to generate entities when it cannot be guessed (Ex: src/).'
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
        $path = $input->getArgument('path');

        /*
         * Collect all entities
         */
        $mappingCollection = $this
            ->getContainer()
            ->get('visca_doctrine.mapping.metadata_collector')
            ->collect(
                $name,
                $path
            );

        $filteredMappingCollection = $this
            ->getContainer()
            ->get('visca_doctrine.mapping.collector.filter.with_unique_key')
            ->filter($mappingCollection);

        /*
         * Generate all files
         */
        $entityManager = $this->getEntityManager($input->getOption('em'));
        foreach ($filteredMappingCollection as $mapping) {
            $generator = $mapping->getClassGenerator();
            $metaData = $mapping->getMetaData();

            $output->writeln(
                sprintf(
                    ' > generating unique values for <comment>%s</comment> '.
                    'using the generator <comment>%s</comment>',
                    $metaData->getName(),
                    get_class($generator)
                )
            );

            $generator->generate($metaData, $entityManager, $path);
        }
    }
}
