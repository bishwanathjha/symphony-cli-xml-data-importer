<?php

namespace App\Library\File\Provider;

use App\Library\XML;

/**
 * Class Base
 * @package App\Library\File\Provider
 */
abstract class Base
{
    /** @var string */
    protected $resource;

    /** @var array */
    protected $data = [];

    /**
     * Base constructor.
     * @param string $resource
     */
    public function __construct(string $resource) {
        $this->resource = $resource;
    }

    /**
     * Return the resource path
     * @return string
     */
    public function getResource(): string {
        return $this->resource;
    }

    /**
     * Return the data
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * Helper function to load data from file
     *
     * @param string $xml
     * @throws \Exception
     */
    protected function loadXML(string $xml) {
        if (!XML\Validator::isValid($xml)) {
            throw new \Exception('Given content is not a valid xml');
        }

        // Load the xml string
        $data = simplexml_load_string($xml, 'SimpleXMLElement',LIBXML_NOCDATA);

        if ($data === false) {
            $errors = [];
            foreach(libxml_get_errors() as $error) {
                $errors[] = $error->message;
            }

            throw new \Exception('Failed to load XML, Errors: ' . json_encode($errors));
        }

        foreach ($data as $row) {
            // Set the data to be used
            $this->data[] = $this->sanitizeNodes($row);
        }
    }

    /**
     * Here we do some cleanup like empty array to string
     *
     * @param array|\SimpleXMLElement[]|\SimpleXMLElement $data
     *
     * @return array
     */
    private function sanitizeNodes($data): ?array
    {
        if ($data instanceof \SimpleXMLElement and $data->count() === 0) {
            return null;
        }

        $data = (array) $data;
        foreach ($data as &$value) {
            if (is_array($value) or $value instanceof \SimpleXMLElement) {
                $value = $this->sanitizeNodes($value);
            }
        }

        return $data;
    }
}
