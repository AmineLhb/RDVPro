@extends('tenant.layout')

@section('content')
<div x-data="{
    showDrawer: false,
    drawerMode: 'create',
    selectedServiceId: null,
    formData: {
        name: {{ json_encode(old('name', '')) }},
        duration_minutes: {{ json_encode((int) old('duration_minutes', 30)) }},
        price: {{ json_encode(old('price', '')) }},
        requires_deposit: {{ old('requires_deposit') ? 'true' : 'false' }},
        active: {{ old('active', 1) ? 'true' : 'false' }},
        resources: {{ json_encode(old('resources', [])) }}
    },
    openCreateDrawer() {
        this.drawerMode = 'create';
        this.selectedServiceId = null;
        this.formData = {
            name: '',
            duration_minutes: 30,
            price: '',
            requires_deposit: false,
            active: true,
            resources: []
        };
        this.showDrawer = true;
    },
    openEditDrawer(service) {
        this.drawerMode = 'edit';
        this.selectedServiceId = service.id;
        this.formData = {
            name: service.name,
            duration_minutes: service.duration_minutes,
            price: service.price,
            requires_deposit: !!service.requires_deposit,
            active: !!service.active,
            resources: service.resources || []
        };
        this.showDrawer = true;
    },
    init() {
        const params = new URLSearchParams(window.location.search);
        if (params.get('create') === '1') {
            this.openCreateDrawer();
        }
        const editId = params.get('edit');
        if (editId) {
            this.$nextTick(() => {
                const btn = document.querySelector('button[data-service-id=\'' + editId + '\']');
                if (btn) btn.click();
            });
        }
        @if($errors->any())
            @if(old('id'))
                this.drawerMode = 'edit';
                this.selectedServiceId = '{{ old('id') }}';
                this.showDrawer = true;
            @else
                this.drawerMode = 'create';
                this.showDrawer = true;
            @endif
        @endif
    }
}" class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Prestations (Services)</h1>
            <p class="text-muted text-sm font-outfit mt-1">Définissez vos prestations, durées et tarifs.</p>
        </div>
        <button type="button" @click="openCreateDrawer()" class="px-5 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition-all duration-200 active:scale-95 shadow-sm hover:shadow text-sm font-outfit">
            + Ajouter une prestation
        </button>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($services as $s)
            <div class="bg-card border border-line rounded-2xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition relative">
                <!-- Status badge -->
                <span class="absolute top-6 right-6 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $s->active ? 'bg-[#d1e7dd] text-olive' : 'bg-gray-100 text-gray-700' }}">
                    {{ $s->active ? 'Actif' : 'Inactif' }}
                </span>

                <div class="space-y-3">
                    <h3 class="text-xl font-bold text-ink pr-16">{{ $s->name }}</h3>
                    <div class="flex space-x-6 text-sm text-muted">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-clay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $s->duration_minutes }} min
                        </span>
                        <span class="flex items-center font-bold text-ink font-serif text-base">
                            {{ number_format($s->price, 2) }} MAD
                        </span>
                    </div>

                    <!-- Requires deposit -->
                    @if($s->requires_deposit)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#fff3cd] text-gold border border-gold/30">
                            Acompte Requis
                        </span>
                    @endif

                    <!-- Staff associated -->
                    <div class="pt-3 border-t border-line/60">
                        <span class="text-xs text-muted block mb-1">Personnel qualifié :</span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($s->resources as $r)
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold text-paper" style="background-color: {{ $r->color }}">
                                    {{ $r->name }}
                                </span>
                            @empty
                                <span class="text-xs text-muted italic">Aucun personnel affecté</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-line flex justify-end space-x-2">
                    <button type="button" data-service-id="{{ $s->id }}" title="Modifier" @click="openEditDrawer({{ json_encode([
                        'id' => $s->id,
                        'name' => $s->name,
                        'duration_minutes' => (int) $s->duration_minutes,
                        'price' => (float) $s->price,
                        'requires_deposit' => (bool) $s->requires_deposit,
                        'active' => (bool) $s->active,
                        'resources' => $s->resources->pluck('id')->toArray()
                    ]) }})" class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-line bg-paper text-ink hover:bg-line transition-all duration-200 active:scale-95 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form action="{{ route('tenant.services.destroy', $s->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" title="Supprimer" onclick="confirmDelete(this, 'Êtes-vous sûr de vouloir supprimer cette prestation ?')" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-red-100 text-red-800 hover:bg-red-800 hover:text-paper transition-all duration-200 active:scale-95 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-card border border-line p-8 rounded-2xl shadow-sm text-center text-muted col-span-2">
                Aucune prestation définie. Ajoutez votre premier service pour commencer à réserver.
            </div>
        @endforelse
    </div>

    <!-- Right Drawer for Create / Edit Service -->
    <div x-show="showDrawer" x-cloak class="fixed inset-0 overflow-hidden z-50" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-[#1a1410]/50 backdrop-blur-sm transition-opacity" @click="showDrawer = false"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-md" 
                     x-show="showDrawer" 
                     x-transition:enter="transform transition ease-in-out duration-300" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-300" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full">
                    <div class="h-full flex flex-col bg-card border-l border-line p-6 shadow-2xl overflow-y-auto space-y-6" @click.stop>
                        <div class="flex items-center justify-between border-b border-line pb-3">
                            <h3 class="text-xl font-bold text-ink font-serif" x-text="drawerMode === 'create' ? 'Ajouter une prestation' : 'Modifier la prestation'"></h3>
                            <button @click="showDrawer = false" class="text-muted hover:text-ink text-2xl font-bold">&times;</button>
                        </div>

                        <form :action="drawerMode === 'create' ? '{{ route('tenant.services.store') }}' : '{{ route('tenant.services.update', '') }}/' + selectedServiceId" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="_method" :value="drawerMode === 'edit' ? 'PUT' : 'POST'">
                            <input type="hidden" name="id" :value="selectedServiceId">

                            <!-- Name -->
                            <div>
                                <label for="drawer_name" class="block text-sm font-semibold mb-2">Nom de la prestation</label>
                                <input type="text" name="name" id="drawer_name" required x-model="formData.name" placeholder="Ex: Coupe + Barbe" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            </div>

                            <!-- Duration -->
                            <div>
                                <label for="drawer_duration" class="block text-sm font-semibold mb-2">Durée (minutes)</label>
                                <input type="number" name="duration_minutes" id="drawer_duration" required x-model="formData.duration_minutes" placeholder="30" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="drawer_price" class="block text-sm font-semibold mb-2">Tarif (MAD)</label>
                                <input type="number" step="0.01" name="price" id="drawer_price" required x-model="formData.price" placeholder="150" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            </div>

                            <!-- Options -->
                            <div class="space-y-3 pt-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="requires_deposit" value="1" :checked="formData.requires_deposit" @change="formData.requires_deposit = $el.checked" class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                                    <span class="ml-2 text-sm font-semibold">Exiger un acompte à la réservation</span>
                                </label>

                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="active" value="1" :checked="formData.active" @change="formData.active = $el.checked" class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                                    <span class="ml-2 text-sm font-semibold">Prestation active</span>
                                </label>
                            </div>

                            <!-- Resources affecting -->
                            <div>
                                <label class="block text-sm font-semibold mb-2">Personnel qualifié pour ce service</label>
                                <div class="flex flex-wrap gap-2.5 p-3 border border-line rounded-xl bg-paper">
                                    @foreach($resources as $r)
                                        <label class="flex items-center space-x-2 cursor-pointer bg-card px-2.5 py-1.5 rounded border border-line/60 hover:border-line">
                                            <input type="checkbox" name="resources[]" value="{{ $r->id }}" 
                                                   :checked="formData.resources.includes({{ $r->id }})"
                                                   @change="if ($el.checked) { if (!formData.resources.includes({{ $r->id }})) formData.resources.push({{ $r->id }}) } else { formData.resources = formData.resources.filter(id => id !== {{ $r->id }}) }"
                                                   class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                                            <span class="text-xs font-semibold text-ink">{{ $r->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="pt-4 border-t border-line flex justify-end space-x-3">
                                <button type="button" @click="showDrawer = false" class="px-5 py-2.5 rounded-xl border border-line bg-paper hover:bg-line transition text-sm font-semibold">
                                    Annuler
                                </button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition shadow-sm text-sm" x-text="drawerMode === 'create' ? 'Enregistrer' : 'Modifier'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
