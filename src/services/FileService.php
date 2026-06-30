<?php

namespace ClinicManagement\Services;

class FileService
{
    /**
     * @var string
     */
    protected $uploadDir;

    public function __construct()
    {
        $wpUploadDir = wp_upload_dir();
        // Move outside the standard year/month folders into a dedicated directory
        $this->uploadDir = $wpUploadDir['basedir'] . '/clinic-secure-uploads/';
        
        $this->ensureDirectoryExists();
    }

    protected function ensureDirectoryExists()
    {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        // Drop an .htaccess to prevent direct access on Apache
        $htaccessPath = $this->uploadDir . '.htaccess';
        if (!file_exists($htaccessPath)) {
            file_put_contents($htaccessPath, "Order Deny,Allow\nDeny from all");
        }

        // Drop an index.php to prevent directory listing on Nginx/Litespeed
        $indexPath = $this->uploadDir . 'index.php';
        if (!file_exists($indexPath)) {
            file_put_contents($indexPath, "<?php\n// Silence is golden.");
        }
    }

    /**
     * Upload a secure medical report.
     *
     * @param array $fileData ($_FILES array item)
     * @param int $patientId
     * @return string|false The path to the file or false on failure.
     */
    public function uploadSecureReport(array $fileData, int $patientId)
    {
        // Basic validation
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($fileData['type'], $allowedMimes)) {
            return false;
        }

        // Generate safe name
        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $safeFileName = 'patient_' . $patientId . '_' . uniqid() . '.' . $extension;
        $destination = $this->uploadDir . $safeFileName;

        if (move_uploaded_file($fileData['tmp_name'], $destination)) {
            return $safeFileName; // Return just the name, not absolute path, to store in DB
        }

        return false;
    }

    /**
     * Get the absolute path to a file.
     *
     * @param string $fileName
     * @return string
     */
    public function getFilePath(string $fileName): string
    {
        return $this->uploadDir . $fileName;
    }
}
