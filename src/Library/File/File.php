<?php

namespace App\Library\File;

use App\Library\File\Provider\URL;
use app\Library\File\Provider\IFile;
use App\Library\File\Provider\Local;

/**
 * Class File
 * @package App\Library\File
 */
class File
{
    /** @var IFile */
    protected $Adapter;

    /**
     * File constructor.
     * @param IFile $Adapter
     */
    private function __construct(IFile $Adapter) {
        $this->Adapter = $Adapter;

        if (!$this->Adapter->exist()) {
            throw new \Exception("Failed to load resource from [" . $Adapter->getResource() ."]");
        }
    }

    /**
     * Parse and load the data from xml file
     *
     * @return $this
     */
    public function load(): self {
        $this->Adapter->parse();

        return $this;
    }

    /**
     * Return the data from xml file
     * @return array
     */
    public function getData(): array {
        return $this->Adapter->getData();
    }

    /**
     * Create a new instance based on type
     *
     * @param string $type
     * @param string $resourcePath
     * @return static
     *
     * @throws \Exception
     */
    public static function get(string $type, string $resourcePath): self {
        $type = strtolower($type);
        if ($type == Local::getName()) {
            $Provider = new Local($resourcePath);
        } else if($type == URL::getName()) {
            $Provider = new URL($resourcePath);
        } else {
            throw new \Exception('Given source provider [' . $type . '] does not exist.');
        }

        return new self($Provider);
    }
}
