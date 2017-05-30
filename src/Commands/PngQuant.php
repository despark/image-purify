<?php


namespace Despark\ImagePurify\Commands;


/**
 * Class PngQuant.
 */
class PngQuant extends Command
{
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

        $arguments[] = '--output='.escapeshellarg($this->getOutFile());

        return $arguments;
    }


}