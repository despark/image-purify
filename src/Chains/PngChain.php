<?php


namespace Despark\ImagePurify\Chains;


class PngChain extends ChainAbstract
{

    /**
     * @param $filePath
     * @return mixed
     */
    public function canHandle($filePath)
    {
        $imageType = exif_imagetype($filePath);

        if ($imageType == IMAGETYPE_PNG) {
            return true;
        }

        return false;
    }

}