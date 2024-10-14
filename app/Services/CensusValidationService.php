<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Internment;

class CensusValidationService
{
    public function validateRow(array $row)
    {
        $patient = $this->findExistingPatient($row['nome'], $row['nascimento']);

        if ($patient && $patient->var_codigo !== $row['codigo']) {
            return $this->invalidRow($row, 'Código divergente para o mesmo paciente.');
        }

        if ($this->internmentExists($row['guia'])) {
            return $this->invalidRow($row, 'Guia de internação já existe.');
        }

        if ($this->entryBeforeBirth($row['entrada'], $row['nascimento'])) {
            return $this->invalidRow($row, 'Data de entrada anterior ao nascimento.');
        }

        if ($this->exitBeforeOrEqualEntry($row['saida'], $row['entrada'])) {
            return $this->invalidRow($row, 'Data de alta igual ou inferior à data de entrada.');
        }

        return $this->validRow($row);
    }

    protected function findExistingPatient($name, $birthDate)
    {
        return Patient::where('var_nome', $name)
            ->where('var_nascimento', $birthDate)
            ->first();
    }

    protected function internmentExists($guia)
    {
        return Internment::where('var_guia', $guia)->exists();
    }

    protected function entryBeforeBirth($entryDate, $birthDate)
    {
        return strtotime($entryDate) < strtotime($birthDate);
    }

    protected function exitBeforeOrEqualEntry($exitDate, $entryDate)
    {
        return strtotime($exitDate) <= strtotime($entryDate);
    }

    protected function invalidRow($row, $message)
    {
        return ['status' => 'invalid', 'data' => $row, 'message' => $message];
    }

    protected function validRow($row)
    {
        return ['status' => 'valid', 'data' => $row];
    }
}
