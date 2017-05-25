<?php


namespace Despark\ImagePurify\Commands;


use Despark\ImagePurify\Exceptions\CommandException;

class MozJpeg extends Command
{

    /**
     * @return string
     * @throws CommandException
     */
    public function buildCommand()
    {
        if (! $sponge = $this->getSpongeBin()) {
            throw new CommandException('`sponge` is not found. You must install it with sudo apt-get install moreutils');
        }

        // Build the command
        $arguments = implode(' ', $this->getArguments());

        return $this->getBin().' '.$arguments.' | '.$sponge.' '.$this->getOutFile();
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getSpongeBin()
    {
        return shell_exec('which sponge');
    }

}