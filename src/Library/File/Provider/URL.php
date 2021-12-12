<?php

namespace App\Library\File\Provider;

/**
 * Class URL
 * @package App\Library\File\Provider
 */
class URL extends Base implements IFile
{
    /**
     * Get the name of provider
     * @return string
     */
    public static function getName(): string {
        return 'url';
    }

    /**
     * Check if a given url exist
     * @return bool
     */
    public function exist(): bool {
        $headers = get_headers($this->getResource());

        return ($headers && strpos($headers[0], '200'));
    }

    /**
     * Parse the xml file into array
     *
     * @return $this|IFile
     * @throws \Exception
     */
    public function parse(): IFile {
        $xmlContents = file_get_contents($this->getResource());

        $this->loadXML($xmlContents);

        return $this;
    }
}
