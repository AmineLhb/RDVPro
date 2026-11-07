<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\AttachmentUploadRequest;
use App\Services\Tenant\AttachmentService;
use App\Models\Tenant\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    protected AttachmentService $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Upload an attachment.
     */
    public function store(AttachmentUploadRequest $request)
    {
        try {
            $attachment = $this->attachmentService->uploadAttachment(
                $request->file('file'),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'mime_type' => $attachment->mime_type,
                'size' => $attachment->size,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Securely serve a private file.
     */
    public function serve(Attachment $attachment, Request $request)
    {
        // Serve private download or stream
        try {
            $download = $request->query('download') == '1';
            return $this->attachmentService->getDownloadResponse($attachment, $download);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Rename an attachment.
     */
    public function rename(Request $request, Attachment $attachment)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $this->attachmentService->renameAttachment($attachment, $request->get('name'));

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Fichier renommé avec succès.');
    }

    /**
     * Delete an attachment.
     */
    public function destroy(Request $request, Attachment $attachment)
    {
        $customerId = $attachment->customer_id;
        $this->attachmentService->deleteAttachment($attachment);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('tenant.customers.show', $customerId)
            ->with('success', 'Fichier supprimé.');
    }
}
