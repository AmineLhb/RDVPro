<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RecordRequest;
use App\Services\Tenant\RecordService;
use App\Models\Tenant\Record;
use App\Models\Tenant\RecordFieldTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RecordController extends Controller
{
    protected RecordService $recordService;

    public function __construct(RecordService $recordService)
    {
        $this->recordService = $recordService;
    }

    /**
     * Store a record.
     */
    public function store(RecordRequest $request)
    {
        $record = $this->recordService->createRecord($request->validated());
        return redirect()
            ->route('tenant.customers.show', $record->customer_id)
            ->with('success', 'Entrée de dossier enregistrée avec succès.');
    }

    /**
     * Delete a record.
     */
    public function destroy(Record $record)
    {
        $customerId = $record->customer_id;
        $this->recordService->deleteRecord($record);
        return redirect()
            ->route('tenant.customers.show', $customerId)
            ->with('success', 'Entrée de dossier supprimée.');
    }

    /**
     * Update the record entry template definitions.
     */
    public function updateTemplate(Request $request)
    {
        $request->validate([
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.type' => ['required', 'string', 'in:text,long_text'],
            'fields.*.required' => ['nullable', 'boolean'],
        ]);

        $fields = [];
        foreach ($request->get('fields') as $field) {
            $label = $field['label'];
            // Create a clean field name: lowercase, alpha-numeric, underscores
            $name = Str::snake(Str::ascii($label));
            $name = preg_replace('/[^a-z0-9_]/', '', $name);
            if (empty($name)) {
                $name = 'field_' . rand(100, 999);
            }

            $fields[] = [
                'name' => $name,
                'label' => $label,
                'type' => $field['type'],
                'required' => !empty($field['required']),
            ];
        }

        $template = RecordFieldTemplate::first();
        if (!$template) {
            $template = new RecordFieldTemplate();
            $template->business_type = tenant('business_type') ?? 'general';
        }

        $template->field_definitions = $fields;
        $template->save();

        return back()->with('success', 'Modèle de dossier mis à jour.');
    }
}
