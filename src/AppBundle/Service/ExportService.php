<?php
namespace AppBundle\Service;

class ExportService implements IExportService
{
    /**
     * Transforms a 2d string array into a character separated values text
     * @param  string[][] $rows
     * @param  string $delimiter delimiter character, default ;
     * @param  string $enclosure enclosure character, default "
     * @param  string $escape    escape character, default \
     * @return string            csv text
     */
    public function getCsvText($rows, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        // https://stackoverflow.com/questions/4249432/export-to-csv-via-php
        //$delimiter = chr(9); // tab (for excel)
        ob_start();
        $df = fopen("php://output", 'w');
        //add BOM to fix UTF-8 in Excel
        fputs($df, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        foreach ($rows as $row ) {
            fputcsv($df, $row, $delimiter, $enclosure, $escape);
        }
        fclose($df);
        return ob_get_clean();
    }
}
