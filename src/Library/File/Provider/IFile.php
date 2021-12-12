<?php

namespace App\Library\File\Provider;

interface IFile
{
    public function getResource(): string;
    public static function getName(): string;
    public function getData(): array;
    public function parse(): IFile;
    public function exist(): bool;
}
