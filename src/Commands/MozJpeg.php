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
        // Get the permissions on the source file and keep them, as we faced problems with permission changes?!
        if ($this->isOutSourceEqual()) {
            $originalPermissions = fileperms($this->getSourceFile());
        }

        parent::execute();
        if ($this->isOutSourceEqual()) // Now move the file if we successfully optimized
        {
            if (! file_exists($this->outFileTemp)) {
                throw new CommandException('Cannot find compressed file.');
            }
            // Check if outfile exists and has size
            if (! filesize($this->outFileTemp)) {
                throw new CommandException('Compressed file is 0 bytes');
            }
            rename($this->outFileTemp, $this->getOutFile());
            chmod($this->getOutFile(), $originalPermissions);
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