<?php

namespace app\Library\Storage;

use App\Library\Storage\Provider\CSV;
use App\Library\Storage\Provider\IStorage;

/**
 * Class Storage
 * @package app\Library\Storage
 */
class Storage
{
    /** @var IStorage */
    protected $Provider;

    /**
     * Storage constructor.
     * @param IStorage $Storage
     * @throws \Exception
     */
    public function __construct(IStorage $Storage) {
        $this->Provider = $Storage;

        if (!$this->isAvailable()) {
            throw new \Exception('Given storage is not available');
        }
    }

    /**
     * Write data into storage provider
     *
     * @param array $data
     * @param bool $bulk (OPTIONAL) If true then process as nested array
     * @return bool
     */
    public function write(array $data, $bulk = false) {
        return $this->Provider->write($data, $bulk);
    }

    /**
     * Read the data
     * @return array
     */
    public function read(): array {
        return $this->Provider->read();
    }

    /**
     * Check if storage is configured and available
     * @return bool
     */
    public function isAvailable(): bool {
        return $this->Provider->isAvailable();
    }

    public function getOutputPath() {
        return $this->Provider->outputPath();
    }

    /**
     * Return the instance of the class
     *
     * @param string $name
     * @return static
     *
     * @throws \Exception
     */
    public static function get(string $name = 'csv'): self {
        /** @todo later we can register other providers here */
        if (strtolower($name) == 'csv') {
            return new self(new CSV());
        }

        throw new \Exception("[$name] is not supported");
    }
}
