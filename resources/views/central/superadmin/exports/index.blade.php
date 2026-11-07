@extends('central.layout')

@section('content')
<div class="space-y-8 max-w-2xl">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Export des reçus (ZIP)</h1>
        <p class="text-muted text-sm font-outfit mt-1">Sélectionnez une période pour exporter l'ensemble des reçus de virement bancaire téléversés par vos clients.</p>
    </div>

    <!-- Export Card -->
    <div class="bg-card border border-line rounded-2xl shadow-sm p-6 md:p-8">
        <h3 class="text-lg font-semibold border-b border-line pb-2 mb-6 text-clay font-serif">Période personnalisée</h3>
        
        <form action="{{ route('central.exports.zip') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-semibold mb-2">Date de début</label>
                    <input type="date" name="start_date" id="start_date" required class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm" value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-semibold mb-2">Date de fin</label>
                    <input type="date" name="end_date" id="end_date" required class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm" value="{{ old('end_date', now()->format('Y-m-d')) }}">
                </div>
            </div>

            <!-- Hint text -->
            <p class="text-xs text-muted">L'archive ZIP générée contiendra tous les reçus physiques (PDF, PNG, JPG) dont l'action de téléversement s'est déroulée entre ces deux dates incluses.</p>

            <!-- Actions -->
            <div class="pt-4 border-t border-line flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition shadow-sm text-sm flex items-center space-x-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Générer & Télécharger le ZIP
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
