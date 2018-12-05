<?php


namespace App\Command;


use App\Entity\Door;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

class TestWorkflowCommand extends Command
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * TestWorkflowCommand constructor.
     *
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('workflow:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = $io = new SymfonyStyle($input, $output);
        $door = new Door();
        $workflow = $this->registry->get($door);
        $question = new ChoiceQuestion('What to do with the door?', ['open', 'close', 'lock', 'unlock']);
        while ($update = $io->askQuestion($question)) {
            $this->updateState($workflow, $door, $update, $io);
        }
    }

    /**
     * @param Workflow     $workflow
     * @param Door         $door
     * @param string       $update
     * @param SymfonyStyle $io
     */
    protected function updateState(Workflow $workflow, Door $door, string $update, SymfonyStyle $io): void
    {
        try {
            $workflow->apply($door, $update);
        } catch (NotEnabledTransitionException $e) {
            $io->error(sprintf('Can\'t %s the door now it is %s', $update, $door->state));

            return;
        }
        $io->success(sprintf('The door state is now %s', $update));
    }
}