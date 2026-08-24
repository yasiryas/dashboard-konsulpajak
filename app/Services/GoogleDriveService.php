<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class GoogleDriveService
{
    protected ?Drive $drive = null;

    /**
     * Buat folder klien baru di Drive, kembalikan folder ID.
     */
    public function createClientFolder(string $namaKlien): string
    {
        $folder = new DriveFile([
            'name' => $namaKlien,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => array_values(array_filter([
                config('services.google.drive.parent_folder_id'),
            ])),
        ]);

        $created = $this->drive()->files->create($folder, ['fields' => 'id']);

        return (string) $created->id;
    }

    /**
     * Upload file ke folder Drive, kembalikan file ID + URL pratinjau.
     *
     * @return array{id: string, url: string|null}
     */
    public function uploadFile(string $folderId, UploadedFile $file): array
    {
        $driveFile = new DriveFile([
            'name' => $file->getClientOriginalName(),
            'parents' => [$folderId],
        ]);

        $created = $this->drive()->files->create($driveFile, [
            'data' => fopen($file->getRealPath(), 'rb'),
            'mimeType' => $file->getMimeType() ?: 'application/octet-stream',
            'uploadType' => 'multipart',
            'fields' => 'id, webViewLink',
        ]);

        return [
            'id' => (string) $created->id,
            'url' => $created->webViewLink,
        ];
    }

    protected function drive(): Drive
    {
        return $this->drive ??= $this->buildDrive();
    }

    protected function buildDrive(): Drive
    {
        $credentials = config('services.google.drive.credentials');

        if (blank($credentials) || ! is_file($credentials)) {
            throw new RuntimeException('Google Drive service account belum dikonfigurasi (GOOGLE_DRIVE_CREDENTIALS).');
        }

        $client = new GoogleClient;
        $client->setAuthConfig($credentials);
        $client->addScope(Drive::DRIVE);

        return new Drive($client);
    }
}
