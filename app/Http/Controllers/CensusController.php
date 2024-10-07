<?php

namespace App\Http\Controllers;

use App\Services\CensusValidationService;
use App\Services\CensusProcessingService;
use App\Models\Patient;
use App\Models\Internment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CensusController extends Controller
{
    protected $validationService;
    protected $processingService;

    public function __construct(
        CensusValidationService $validationService,
        CensusProcessingService $processingService
    ) {
        $this->validationService = $validationService;
        $this->processingService = $processingService;
    }
    public function upload(Request $request)
    {
        $this->validateFile($request);
        $filePath = $request->file('census_file')->getRealPath();
        $data = $this->processingService->processCsvFile($filePath);
        [$validData, $invalidData] = $this->validateCensusData($data);
        session(['valid_data' => $validData, 'invalid_data' => $invalidData]);
        return redirect()->route('review.page');
    }
    public function save(Request $request)
    {
        if (is_string($request->valid_data)) {
            $validData = json_decode($request->valid_data, true);
        } else {
            $validData = $request->valid_data;
        }
        $result = $this->storeValidData($validData);
        session([
            'newPatients' => $result['newPatients'],
            'newInternments' => $result['newInternments'],
        ]);

        return response()->json(['message' => 'Dados salvos com sucesso']);
    }
    protected function storeValidData(array $validData)
    {
        $newPatientsCount = 0;
        $newInternmentsCount = 0;
        DB::transaction(function () use ($validData, &$newPatientsCount, &$newInternmentsCount) {
            foreach ($validData as $row) {
                $patient = $this->firstOrCreatePatient($row);
                if ($patient->wasRecentlyCreated) {
                    $newPatientsCount++;
                }
                $internment = $this->createInternment($patient->id, $row);
                if ($internment->wasRecentlyCreated) {
                    $newInternmentsCount++;
                }
            }
        });
        return [
            'newPatients' => $newPatientsCount,
            'newInternments' => $newInternmentsCount,
        ];
    }
    protected function createInternment($patientId, array $row)
    {
        $internment = Internment::where('var_guia', $row['guia'])->first();
        if ($internment) {
            $internment->update([
                'var_patient_id' => $patientId,
                'var_entrada' => $row['entrada'],
                'var_saida' => $row['saida'],
            ]);
            return $internment;
        }
        return Internment::create([
            'var_patient_id' => $patientId,
            'var_guia' => $row['guia'],
            'var_entrada' => $row['entrada'],
            'var_saida' => $row['saida'],
        ]);
    }

    protected function validateFile(Request $request)
    {
        $request->validate([
            'census_file' => 'required|file|mimes:csv,txt',
        ]);
    }
    protected function validateCensusData(array $data)
    {
        $validData = [];
        $invalidData = [];
        foreach ($data as $row) {
            $validationResult = $this->validationService->validateRow($row);

            if ($validationResult['status'] === 'valid') {
                $validData[] = $validationResult['data'];
            } else {
                $invalidData[] = [
                    'row' => $validationResult['data'],
                    'error' => $validationResult['message']
                ];
            }
        }
        return [$validData, $invalidData];
    }
    protected function firstOrCreatePatient(array $row)
    {
        $patient = Patient::firstOrCreate([
            'var_nome' => $row['nome'],
            'var_nascimento' => $row['nascimento'],
            'var_codigo' => $row['codigo'],
        ]);

        \Log::info('Paciente criado ou encontrado:', ['patient' => $patient]);
        return $patient;
    }
    protected function createInternmentsInBatch($patientId, array $rows)
    {
        $internments = [];
        foreach ($rows as $row) {
            $internments[] = [
                'var_patient_id' => $patientId,
                'var_guia' => $row['guia'],
                'var_entrada' => $row['entrada'],
                'var_saida' => $row['saida'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Internment::insert($internments);
    }
    public function review()
    {
        return view('review', [
            'validData' => session('valid_data', []),
            'invalidData' => session('invalid_data', []),
        ]);
    }
    public function listPatients()
    {
        $patients = Patient::with('internments')->get();
        return view('patients', ['patients' => $patients]);
    }
}
