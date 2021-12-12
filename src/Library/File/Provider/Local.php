<?php

namespace App\Library\File\Provider;

/**
 * Class Local
 * @package App\Library\File\Provider
 */
class Local extends Base implements IFile
{
    /**
     * Return the name of provider
     * @return string
     */
    public static function getName(): string {
        return 'local';
    }

    /**
     * Check if file exist locally
     * @return bool
     */
    public function exist(): bool {
        return file_exists($this->getResource());
    }

    /**
     * Parse the xml file into array
     *
     * @return $this|IFile
     * @throws \Exception
     */
    public function parse(): IFile {
        $contents = file_get_contents($this->getResource());
        if (empty($contents)) {
            throw new \Exception('Empty contents');
        }

        $this->loadXML($contents);

        return $this;
    }
}
