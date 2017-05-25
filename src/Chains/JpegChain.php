<?php


namespace Despark\ImagePurify\Chains;


class JpegChain extends ChainAbstract
{
    /**
     * @param $filePath
     * @return mixed
     */
    public function canHandle($filePath)
    {
        $imageType = exif_imagetype($filePath);

        if ($imageType == IMAGETYPE_JPEG) {
            return true;
        }

        return false;
    }


}