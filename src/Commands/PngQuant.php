<?php


namespace Despark\ImagePurify\Commands;


use Despark\ImagePurify\Exceptions\CommandException;

/**
 * Class PngQuant.
 */
class PngQuant extends Command
{
    /**
     * @return string
     */
    public function buildCommand(): string
    {
        return $this->getBin().' '.$this->buildArguments().' -- '.escapeshellarg($this->getSourceFile());
    }

    /**
     * @throws CommandException
     */
    public function execute()
    {
        try {
            parent::execute();
        } catch (CommandException $exception) {
            if ($exception->getExitCode() === 98) {
                throw new CommandException('File probably already optimized', 98, 98);
            }
        }
    }


    /**
     * @return array
     */
    public function getArguments(): array
    {
        $arguments = parent::getArguments();

        // If we have output setup don't act!
        foreach ($arguments as $argument) {
            if (strstr($argument, '--output') !== false) {
                return $arguments;
            }
        }

        $arguments[] = '--output '.escapeshellarg($this->getOutFile());

        return $arguments;
    }
}