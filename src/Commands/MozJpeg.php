<?php


namespace Despark\ImagePurify\Commands;


use Despark\ImagePurify\Exceptions\CommandException;
use Symfony\Component\Process\ExecutableFinder;

/**
 * Class MozJpeg.
 */
class MozJpeg extends Command
{

    /**
     * @var string
     */
    protected $spongeBin;

    /**
     * @var ExecutableFinder
     */
    protected $execFinder;

    /**
     * Temporary output file.
     * @var string
     */
    protected $outFileTemp;

    /**
     * @return void
     * @throws CommandException
     */
    public function execute()
    {
        parent::execute();
        if ($this->isOutSourceEqual()) // Now move the file if we successfully optimized
        {
            rename($this->outFileTemp, $this->getOutFile());
        }
    }

    /**
     * Gets all the arguments
     *
     * @return array
     */
    public function getArguments(): array
    {
        // We will need to process the arguments and merge them.
        $arguments = parent::getArguments();

        // If we have outfile setup don't act!
        foreach ($arguments as $argument) {
            if (strstr($argument, '-outfile') !== false) {
                return $arguments;
            }
        }

        if ($this->isOutSourceEqual()) // Check if not already specified
        {
            $this->outFileTemp = tempnam(sys_get_temp_dir(), 'img-prfy-');
            $arguments[] = '-outfile '.escapeshellarg($this->outFileTemp);
        } else {
            $arguments[] = '-outfile '.escapeshellarg($this->getOutFile());
        }

        return $arguments;
    }


}