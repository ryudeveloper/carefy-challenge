<?php

namespace Tests\Unit;

use App\Services\CensusValidationService;
use Tests\TestCase;

class CensusValidationServiceTest extends TestCase
{
    protected $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new CensusValidationService();
    }

    /** @test */
    public function it_validates_a_valid_row()
    {
        $row = [
            'nome' => 'Maria',
            'nascimento' => '05/08/2004',
            'codigo' => '852369',
            'guia' => '14785236',
            'entrada' => '01/09/2024',
            'saida' => '10/09/2024',
        ];

        $result = $this->validationService->validateRow($row);
        $this->assertEquals('valid', $result['status']);
    }

    /** @test */
    public function it_invalidates_a_row_with_future_entry()
    {
        $row = [
            'nome' => 'João',
            'nascimento' => '26/03/1995',
            'codigo' => '7635785',
            'guia' => '9653232',
            'entrada' => '01/01/2025',
            'saida' => '08/01/2025',
        ];

        $result = $this->validationService->validateRow($row);
        $this->assertEquals('invalid', $result['status']);
        $this->assertEquals('A data de entrada não pode ser no futuro.', $result['message']);
    }
}
