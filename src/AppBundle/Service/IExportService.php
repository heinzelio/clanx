<?php
namespace App\Service;

interface IExportService
{
    /**
     * Transforms a 2d string array into a character separated values text
     * @param  string[][] $rows
     * @param  string $delimiter delimiter character, default ;
     * @param  string $enclosure enclosure character, default "
     * @param  string $escape    escape character, default \
     * @return string            csv text
     */
    public function getCsvText($rows, $delimiter = ';', $enclosure = '"', $escape = '\\');
}
