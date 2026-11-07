<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentService
{
    /**
     * Upload and create attachment record.
     */
    public function uploadAttachment(UploadedFile $file, array $data): Attachment
    {
        $tenantId = tenant('id') ?? 'default';
        $userId = Auth::guard('web')->id() ?? 0;
        $userEmail = Auth::guard('web')->user()->email ?? 'system';

        // Store file in private folder: storage/app/records/{tenant_id}
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();
        
        $path = $file->store("records/{$tenantId}", 'local');

        $attachment = Attachment::create([
            'record_id' => $data['record_id'] ?? null,
            'customer_id' => $data['customer_id'],
            'appointment_id' => $data['appointment_id'] ?? null,
            'file_path' => $path,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'uploaded_by' => $userId,
        ]);

        Log::info("[Activity Log] User {$userEmail} (ID: {$userId}) uploaded file '{$originalName}' (ID: {$attachment->id}) for customer ID {$data['customer_id']}");

        return $attachment;
    }

    /**
     * Rename an attachment.
     */
    public function renameAttachment(Attachment $attachment, string $newName): bool
    {
        $userId = Auth::guard('web')->id() ?? 0;
        $userEmail = Auth::guard('web')->user()->email ?? 'system';
        
        // Ensure extension is kept or sanitized
        $oldName = $attachment->original_name;
        $extension = pathinfo($oldName, PATHINFO_EXTENSION);
        $cleanName = pathinfo($newName, PATHINFO_FILENAME);
        
        if (!empty($extension)) {
            $newName = $cleanName . '.' . $extension;
        } else {
            $newName = $cleanName;
        }

        $updated = $attachment->update(['original_name' => $newName]);

        if ($updated) {
            Log::info("[Activity Log] User {$userEmail} (ID: {$userId}) renamed file ID {$attachment->id} from '{$oldName}' to '{$newName}'");
        }

        return $updated;
    }

    /**
     * Delete attachment file and record.
     */
    public function deleteAttachment(Attachment $attachment): bool
    {
        $userId = Auth::guard('web')->id() ?? 0;
        $userEmail = Auth::guard('web')->user()->email ?? 'system';
        $filePath = $attachment->file_path;
        $originalName = $attachment->original_name;

        // Delete physical file
        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);
        }

        $deleted = $attachment->delete();

        if ($deleted) {
            Log::info("[Activity Log] User {$userEmail} (ID: {$userId}) deleted file '{$originalName}' (ID: {$attachment->id})");
        }

        return $deleted;
    }

    /**
     * Log the file viewing / preview action.
     */
    public function logView(Attachment $attachment): void
    {
        $userId = Auth::guard('web')->id() ?? 0;
        $userEmail = Auth::guard('web')->user()->email ?? 'system';
        
        Log::info("[Activity Log] User {$userEmail} (ID: {$userId}) viewed/downloaded file '{$attachment->original_name}' (ID: {$attachment->id})");
    }

    /**
     * Get Response for serving the private file.
     */
    public function getDownloadResponse(Attachment $attachment, bool $download = false): \Symfony\Component\HttpFoundation\Response
    {
        $filePath = $attachment->file_path;

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'Fichier introuvable sur le disque.');
        }

        $this->logView($attachment);

        if ($download) {
            return Storage::disk('local')->download($filePath, $attachment->original_name, [
                'Content-Type' => $attachment->mime_type,
            ]);
        } else {
            return response()->file(Storage::disk('local')->path($filePath), [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => 'inline; filename="' . addslashes($attachment->original_name) . '"',
            ]);
        }
    }
}
