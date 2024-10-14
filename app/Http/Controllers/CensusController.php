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
    /**
     * upload
     * Método responsável por fazer upload e validação do arquivo CSV.
     * @author Miquéias Silva
     * @since 10/2024
     */
    public function upload(Request $request)
    {
        $this->validateFile($request);
        $filePath = $request->file('census_file')->getRealPath();
        $data = $this->processingService->processCsvFile($filePath);
        [$validData, $invalidData] = $this->validateCensusData($data);
        session(['valid_data' => $validData, 'invalid_data' => $invalidData]);
        return redirect()->route('review.page');
    }
    /**
     * save
     * Método responsável por salvar informações válidas do CSV no banco de dados.
     * @author Miquéias Silva
     * @since 10/2024
     */
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
    /**
     * storeValidData
     * Método responsável por salvar dados válidos criando novo paciente e internação no banco de dados.
     * @author Miquéias Silva
     * @since 10/2024
     */
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
    /**
     * createInternment
     * Método responsável por criar registros de internação no banco de dados.
     * @author Miquéias Silva
     * @since 10/2024
     */
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
    /**
     * validateFile
     * Método responsável por validar arquivo que irá ser feito o upload, irá verificar se é um .CSV.
     * @author Miquéias Silva
     * @since 10/2024
     */
    protected function validateFile(Request $request)
    {
        $request->validate([
            'census_file' => 'required|file|mimes:csv,txt',
        ]);
    }
    /**
     * validateCensusData
     * Método responsável por validar dados do arquivo que foi feito upload fazendo assim a distribuição de dados válidos e inválidos.
     * @author Miquéias Silva
     * @since 10/2024
     */
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
    /**
     * firstOrCreatePatient
     * Método responsável por buscar ou criar paciente.
     * @author Miquéias Silva
     * @since 10/2024
     */
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
    /**
     * createInternmentsInBatch
     * Método responsável criar internações.
     * @author Miquéias Silva
     * @since 10/2024
     */
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
    /**
     * review
     * Método responsável pela página de review de pacientes e internações.
     * @author Miquéias Silva
     * @since 10/2024
     */
    public function review()
    {
        return view('review', [
            'validData' => session('valid_data', []),
            'invalidData' => session('invalid_data', []),
        ]);
    }
    /**
     * listPatients
     * Método responsável por listar pacientes.
     * @author Miquéias Silva
     * @since 10/2024
     */
    public function listPatients()
    {
        $patients = Patient::with('internments')->get();
        return view('patients', ['patients' => $patients]);
    }
}
