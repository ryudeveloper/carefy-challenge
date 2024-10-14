<?php

namespace App\Services;

use League\Csv\Reader;

class CensusProcessingService
{
    /**
     * processCsvFile
     * Método responsável por processar o arquivo CSV em que foi feito upload.
     * @author Miquéias Silva
     * @since 10/2024
     */
    public function processCsvFile(string $filePath)
    {
        $csvData = Reader::createFromPath($filePath, 'r');
        $csvData->setHeaderOffset(0);
        $data = [];
        foreach ($csvData as $row) {
            $row['nascimento'] = $this->convertDateFormat($row['nascimento']);
            $row['entrada'] = $this->convertDateFormat($row['entrada']);
            $row['saida'] = $this->convertDateFormat($row['saida']);
            $data[] = $row;
        }
        return $data;
    }
    /**
     * convertDateFormat
     * Método responsável por converter data de yyyy-mm-dd para dd/mm/yyyy.
     * @author Miquéias Silva
     * @since 10/2024
     */
    private function convertDateFormat($date)
    {
        if (empty($date)) {
            return null;
        }
        $dateTime = \DateTime::createFromFormat('d/m/Y', $date);
        return $dateTime ? $dateTime->format('Y-m-d') : null;
    }

}
