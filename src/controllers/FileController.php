<?php

namespace ClinicManagement\Controllers;

use ClinicManagement\Services\FileService;

class FileController
{
    /**
     * @var FileService
     */
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Download a secure medical report
     * GET /reports/{id}/download
     */
    public function download(\WP_REST_Request $request)
    {
        $reportId = (int) $request->get_param('id');

        // Note: In a full app, we would query the `clinic_medical_reports` table by ID
        // and fetch the actual $fileName and check if the user is authorized.
        // For demonstration, we assume we retrieved $fileName from DB securely.
        
        // Mocking database retrieval
        $fileName = 'patient_2_60d3babc12345.pdf'; // Example

        $filePath = $this->fileService->getFilePath($fileName);

        if (!file_exists($filePath)) {
            return new \WP_Error('not_found', 'File not found.', ['status' => 404]);
        }

        // Standard PHP way to serve file out of root
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf'); // Should be dynamic based on mime
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
        exit;
    }
}
