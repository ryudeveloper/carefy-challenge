<?php

namespace App\Services;

use League\Csv\Reader;

class CensusProcessingService
{
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
    private function convertDateFormat($date)
    {
        if (empty($date)) {
            return null;
        }
        $dateTime = \DateTime::createFromFormat('d/m/Y', $date);
        return $dateTime ? $dateTime->format('Y-m-d') : null;
    }

}
