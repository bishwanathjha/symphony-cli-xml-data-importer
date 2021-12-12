<?php

namespace App\Library\XML;

/**
 * Class Validator related to XML
 * @package App\Library\XML
 */
class Validator
{
    /**
     * Validate an xml string
     * @param string $xmlContents
     * @return bool
     */
    public static function isValid(string $xmlContents): bool {
        if (trim($xmlContents) == '') {
            return false;
        }

        libxml_use_internal_errors(true);

        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xmlContents);

        $errors = libxml_get_errors();
        libxml_clear_errors();

        return empty($errors);
    }
}
