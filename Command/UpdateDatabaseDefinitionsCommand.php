<?php

namespace Mesd\RuleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Mesd\RuleBundle\Model\Definition\DefinitionManagerDoctrineWriter;

class UpdateDatabaseDefinitionsCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('mesd_rule:definitions:update_database')
            ->setDescription('Updates the rule definitions in the database from a definition file')
            ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'The definitions file')
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Will not make database transactions')
            ->addOption('delete', 'x', InputOption::VALUE_NONE, 'Whether to delete definitions in the database but not in the file')
        ;
    }


    /**
     * Execute the command
     *
     * @param  InputInterface  $input  The input interface
     * @param  OutputInterface $output The output interface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Get the loader factory
        $loaderFactory = $this->getContainer()->get('mesd_rule.definition_manager_loader_factory');

        //Get the options
        $file = $input->getOption('file') ?: $loaderFactory->getDefinitionFile();
        $dry = $input->getOption('dry-run') ? true : false;
        $delete = $input->getOption('delete') ? true : false;
        $emName = $loaderFactory->getEmName();

        //Get the loader factory to make use a pretty new definition manager loader
        $loaderFactory->setSource('file');
        $loaderFactory->setDefinitionFile($file);

        //Loadup the definition manager
        $dm = $loaderFactory->getLoader()->load();

        //Get the entity manager
        $em = $this->getContainer()->get('doctrine')->getManager($emName);

        //Create a new writer
        $writer = new DefinitionManagerDoctrineWriter($em);
        $messages = $writer->write($dm, $delete);

        //Display the messages
        foreach($messages as $message) {
            $output->writeln($message);
        }

        //Flush if not a dry run
        if (!$dry) {
            $em->flush();
        }

        //Done
        $output->writeln('Process Complete');
    }
}