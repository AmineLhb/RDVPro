@extends('central.layout')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Domaines</h1>
        <p class="text-muted text-sm font-outfit mt-1">Gérez les sous-domaines et domaines personnalisés liés aux clients.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Domain Form -->
        <div class="bg-card border border-line rounded-2xl shadow-sm p-6 flex flex-col h-fit">
            <h3 class="text-lg font-semibold mb-4 text-clay border-b border-line pb-2">Lier un domaine</h3>
            <form action="{{ route('central.domains.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="tenant_id" class="block text-sm font-semibold mb-2">Choisir le client</label>
                    <select name="tenant_id" id="tenant_id" required class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                        <option value="">-- Sélectionner un client --</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }} (ID: {{ $tenant->id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="domain" class="block text-sm font-semibold mb-2">Nom de domaine complet</label>
                    <input type="text" name="domain" id="domain" required placeholder="Ex: monbarber.ma ou bahja.rdvpro.ma" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-clay text-paper font-semibold text-sm hover:bg-[#b05830] transition shadow-sm mt-2">
                    Associer le domaine
                </button>
            </form>
        </div>

        <!-- Domains List Table -->
        <div class="bg-card border border-line rounded-2xl shadow-sm overflow-hidden lg:col-span-2">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-paper border-b border-line text-muted uppercase text-xs tracking-wider">
                            <th class="p-4">Nom de domaine</th>
                            <th class="p-4">Client associé</th>
                            <th class="p-4">Créé le</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($domains as $domain)
                            <tr class="hover:bg-paper/50">
                                <td class="p-4 font-bold text-olive">
                                    <a href="http://{{ $domain->domain }}:8000" target="_blank" class="hover:underline">
                                        {{ $domain->domain }} ↗
                                    </a>
                                </td>
                                <td class="p-4 text-ink font-semibold">
                                    {{ $domain->tenant->name ?? 'Client inconnu' }}
                                </td>
                                <td class="p-4 text-muted">{{ $domain->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('central.domains.destroy', $domain->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmActionCentral(this, {title: 'Délier le domaine ?', text: 'Êtes-vous sûr de vouloir délier ce domaine ?', icon: 'warning', confirmText: 'Oui, délier', confirmColor: '#c2683f'})" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-800 text-paper hover:bg-red-900 transition">
                                            Délier
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-muted">Aucun domaine enregistré.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
