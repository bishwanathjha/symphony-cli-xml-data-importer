<?php

namespace App\Library\Storage\Provider;

/**
 * Class CSV
 * @package app\Library\Storage\Provider
 */
class CSV implements IStorage
{
    const DEFAULT_STORAGE_FILE = "data.csv";

    /**
     * Write the row into CSV file
     *
     * @param array $data
     * @param bool $bulk (OPTIONAL)
     */
    public function write(array $data, $bulk = false): bool
    {
        if (empty($data))
            return true;

        $this->purge(); // Clear the storage if already exist, we can make it configurable

        $file = fopen($this->outputPath(), "a");

        if (!$bulk) {
            fputcsv($file, array_keys($data));
            fputcsv($file, $data);
        } else {
            $first = reset($data);
            fputcsv($file, array_keys($first));
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
        }

        fclose($file);

        return true;
    }

    public function purge(): void {
        @unlink($this->outputPath());
    }

    /**
     * Read the file and return the data
     *
     * @return array
     */
    public function read(): array
    {
        $file = fopen($this->outputPath(), "r");

        $result = [];
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $result[] = $data;
        }

        fclose($file);

        return $result;
    }

    /**
     * Check if storage is configured and is available
     *
     * @return bool
     * @throws \Exception
     */
    public function isAvailable(): bool
    {
        $storageEngine = $_ENV['STORAGE_ENGINE'];
        $storagePath = $_ENV['STORAGE_PATH'];
        if ($storageEngine != 'csv') {
            throw new \Exception('You need to set STORAGE_ENGINE to csv before using it in .env file');
        }

        if (empty($storagePath)) {
            throw new \Exception('STORAGE_PATH can not be empty when using STORAGE_ENGINE as csv');
        }

        if (!is_writable(dirname($storagePath))) {
            throw new \Exception('STORAGE_PATH ['.$storagePath.'] is not writable');
        }

        return true;
    }

    /**
     * Return the full path of file
     *
     * @return string
     */
    public function outputPath(): string {
        return $_ENV['STORAGE_PATH'] . DIRECTORY_SEPARATOR . self::DEFAULT_STORAGE_FILE;
    }
}
