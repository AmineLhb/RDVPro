@extends('tenant.layout')

@section('content')
<div x-data="{
    showDrawer: false,
    drawerMode: 'create',
    selectedResourceId: null,
    formData: {
        name: {{ json_encode(old('name', '')) }},
        role: {{ json_encode(old('role', '')) }},
        color: {{ json_encode(old('color', '#3d5a3d')) }},
        active: {{ old('active', 1) ? 'true' : 'false' }},
        services: {{ json_encode(old('services', [])) }},
        working_hours: {{ json_encode(old('working_hours') ?: [
            'Monday' => ['active' => true, 'open' => '09:00', 'close' => '18:00'],
            'Tuesday' => ['active' => true, 'open' => '09:00', 'close' => '18:00'],
            'Wednesday' => ['active' => true, 'open' => '09:00', 'close' => '18:00'],
            'Thursday' => ['active' => true, 'open' => '09:00', 'close' => '18:00'],
            'Friday' => ['active' => true, 'open' => '09:00', 'close' => '18:00'],
            'Saturday' => ['active' => true, 'open' => '09:00', 'close' => '18:00'],
            'Sunday' => ['active' => false, 'open' => '09:00', 'close' => '18:00']
        ]) }}
    },
    getDayLabel(day) {
        const labels = {
            Monday: 'Lundi',
            Tuesday: 'Mardi',
            Wednesday: 'Mercredi',
            Thursday: 'Jeudi',
            Friday: 'Vendredi',
            Saturday: 'Samedi',
            Sunday: 'Dimanche'
        };
        return labels[day] || day;
    },
    openCreateDrawer() {
        this.drawerMode = 'create';
        this.selectedResourceId = null;
        this.formData = {
            name: '',
            role: '',
            color: '#3d5a3d',
            active: true,
            services: [],
            working_hours: {
                Monday: { active: true, open: '09:00', close: '18:00' },
                Tuesday: { active: true, open: '09:00', close: '18:00' },
                Wednesday: { active: true, open: '09:00', close: '18:00' },
                Thursday: { active: true, open: '09:00', close: '18:00' },
                Friday: { active: true, open: '09:00', close: '18:00' },
                Saturday: { active: true, open: '09:00', close: '18:00' },
                Sunday: { active: false, open: '09:00', close: '18:00' }
            }
        };
        this.showDrawer = true;
    },
    openEditDrawer(resource) {
        this.drawerMode = 'edit';
        this.selectedResourceId = resource.id;
        
        let hours = {
            Monday: { active: true, open: '09:00', close: '18:00' },
            Tuesday: { active: true, open: '09:00', close: '18:00' },
            Wednesday: { active: true, open: '09:00', close: '18:00' },
            Thursday: { active: true, open: '09:00', close: '18:00' },
            Friday: { active: true, open: '09:00', close: '18:00' },
            Saturday: { active: true, open: '09:00', close: '18:00' },
            Sunday: { active: false, open: '09:00', close: '18:00' }
        };
        
        if (resource.working_hours) {
            Object.keys(hours).forEach(day => {
                if (resource.working_hours[day]) {
                    hours[day].active = !!resource.working_hours[day].active;
                    hours[day].open = resource.working_hours[day].open || '09:00';
                    hours[day].close = resource.working_hours[day].close || '18:00';
                }
            });
        }

        this.formData = {
            name: resource.name,
            role: resource.role || '',
            color: resource.color || '#3d5a3d',
            active: !!resource.active,
            services: resource.services || [],
            working_hours: hours
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
                const btn = document.querySelector('button[data-resource-id=\'' + editId + '\']');
                if (btn) btn.click();
            });
        }
        @if($errors->any())
            @if(old('id'))
                this.drawerMode = 'edit';
                this.selectedResourceId = '{{ old('id') }}';
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
            <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Personnel & Calendrier</h1>
            <p class="text-muted text-sm font-outfit mt-1">Gérez vos prestataires de services, rôles, couleurs et horaires de travail.</p>
        </div>
        <button type="button" @click="openCreateDrawer()" class="px-5 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition-all duration-200 active:scale-95 shadow-sm hover:shadow text-sm font-outfit">
            + Recruter un membre
        </button>
    </div>

    <!-- Staff List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($resources as $r)
            <div class="bg-card border border-line rounded-2xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition relative">
                <!-- Color tag indicator -->
                <div class="absolute top-6 left-6 h-3 w-3 rounded-full" style="background-color: {{ $r->color }}"></div>
                <span class="absolute top-6 right-6 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $r->active ? 'bg-[#d1e7dd] text-olive' : 'bg-gray-100 text-gray-700' }}">
                    {{ $r->active ? 'Actif' : 'Inactif' }}
                </span>

                <div class="space-y-4 pl-6">
                    <div>
                        <h3 class="text-xl font-bold text-ink pr-16">{{ $r->name }}</h3>
                        <p class="text-xs text-muted font-semibold uppercase tracking-wider mt-0.5">{{ $r->role ?? 'Prestataire' }}</p>
                    </div>

                    <!-- Display working hours summary -->
                    <div>
                        <span class="text-xs text-muted block mb-1">Horaires de travail :</span>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                @if(isset($r->working_hours[$day]) && ($r->working_hours[$day]['active'] ?? false))
                                    <div class="flex justify-between border-b border-line/40 py-0.5">
                                        <span class="font-semibold text-muted capitalize">
                                            @if($day === 'Monday') Lun
                                            @elseif($day === 'Tuesday') Mar
                                            @elseif($day === 'Wednesday') Mer
                                            @elseif($day === 'Thursday') Jeu
                                            @elseif($day === 'Friday') Ven
                                            @elseif($day === 'Saturday') Sam
                                            @elseif($day === 'Sunday') Dim
                                            @endif
                                        </span>
                                        <span class="text-ink font-mono">{{ $r->working_hours[$day]['open'] }} - {{ $r->working_hours[$day]['close'] }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between border-b border-line/40 py-0.5 text-muted/60">
                                        <span class="capitalize">
                                            @if($day === 'Monday') Lun
                                            @elseif($day === 'Tuesday') Mar
                                            @elseif($day === 'Wednesday') Mer
                                            @elseif($day === 'Thursday') Jeu
                                            @elseif($day === 'Friday') Ven
                                            @elseif($day === 'Saturday') Sam
                                            @elseif($day === 'Sunday') Dim
                                            @endif
                                        </span>
                                        <span>Fermé</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-line flex justify-end space-x-2">
                    <button type="button" data-resource-id="{{ $r->id }}" title="Modifier & Horaires" @click="openEditDrawer({{ json_encode([
                        'id' => $r->id,
                        'name' => $r->name,
                        'role' => $r->role ?? '',
                        'color' => $r->color,
                        'active' => (bool) $r->active,
                        'services' => $r->services->pluck('id')->toArray(),
                        'working_hours' => $r->working_hours ?? []
                    ]) }})" class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-line bg-paper text-ink hover:bg-line transition-all duration-200 active:scale-95 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form action="{{ route('tenant.resources.destroy', $r->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" title="Supprimer" onclick="confirmDelete(this, 'Supprimer ce profil ? L\'historique de ses rendez-vous sera conservé.')" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-red-100 text-red-800 hover:bg-red-800 hover:text-paper transition-all duration-200 active:scale-95 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-card border border-line p-8 rounded-2xl shadow-sm text-center text-muted col-span-2">
                Aucun collaborateur enregistré. Configurez votre personnel pour définir qui gère les prestations.
            </div>
        @endforelse
    </div>

    <!-- Right Drawer for Create / Edit Staff -->
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
                            <h3 class="text-xl font-bold text-ink font-serif" x-text="drawerMode === 'create' ? 'Recruter un membre' : 'Modifier le personnel'"></h3>
                            <button @click="showDrawer = false" class="text-muted hover:text-ink text-2xl font-bold">&times;</button>
                        </div>

                        <form :action="drawerMode === 'create' ? '{{ route('tenant.resources.store') }}' : '{{ route('tenant.resources.update', '') }}/' + selectedResourceId" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="_method" :value="drawerMode === 'edit' ? 'PUT' : 'POST'">
                            <input type="hidden" name="id" :value="selectedResourceId">

                            <!-- Profile Section -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold border-b border-line pb-1 text-clay">Profil</h4>
                                
                                <div>
                                    <label for="drawer_name" class="block text-xs font-semibold mb-1">Nom Complet</label>
                                    <input type="text" name="name" id="drawer_name" required x-model="formData.name" placeholder="Ex: Karim Alami" class="w-full px-3 py-2 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                                </div>

                                <div>
                                    <label for="drawer_role" class="block text-xs font-semibold mb-1">Poste / Rôle</label>
                                    <input type="text" name="role" id="drawer_role" x-model="formData.role" placeholder="Ex: Barbier, Styliste, Coach" class="w-full px-3 py-2 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="drawer_color" class="block text-xs font-semibold mb-1">Couleur</label>
                                        <input type="color" name="color" id="drawer_color" x-model="formData.color" class="h-10 w-full border border-line rounded bg-paper cursor-pointer p-1">
                                    </div>
                                    <div class="flex items-center pt-5">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="active" value="1" :checked="formData.active" @change="formData.active = $el.checked" class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                                            <span class="ml-2 text-xs font-semibold">Profil actif</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Qualified Services Section -->
                            <div class="space-y-2">
                                <h4 class="text-sm font-semibold border-b border-line pb-1 text-clay">Prestations qualifiées</h4>
                                <div class="flex flex-wrap gap-2 p-2 border border-line rounded-xl bg-paper max-h-32 overflow-y-auto">
                                    @forelse($services as $s)
                                        <label class="flex items-center space-x-2 cursor-pointer bg-card px-2.5 py-1 rounded border border-line/50 hover:border-line">
                                            <input type="checkbox" name="services[]" value="{{ $s->id }}" 
                                                   :checked="formData.services.includes({{ $s->id }})"
                                                   @change="if ($el.checked) { if (!formData.services.includes({{ $s->id }})) formData.services.push({{ $s->id }}) } else { formData.services = formData.services.filter(id => id !== {{ $s->id }}) }"
                                                   class="h-3.5 w-3.5 text-olive focus:ring-olive border-line rounded">
                                            <span class="text-xs font-semibold text-ink">{{ $s->name }}</span>
                                        </label>
                                    @empty
                                        <span class="text-xs text-muted">Aucune prestation configurée.</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Working Hours Section -->
                            <div class="space-y-3">
                                <h4 class="text-sm font-semibold border-b border-line pb-1 text-clay">Heures de travail hebdomadaires</h4>
                                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                    <template x-for="day in ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']" :key="day">
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center p-2.5 border border-line rounded-xl bg-paper">
                                            <div class="sm:col-span-5 flex items-center">
                                                <input type="checkbox" :name="'active_' + day" :id="'active_' + day" value="1"
                                                       :checked="formData.working_hours[day].active"
                                                       @change="formData.working_hours[day].active = $el.checked"
                                                       class="h-3.5 w-3.5 text-olive focus:ring-olive border-line rounded">
                                                <label :for="'active_' + day" class="ml-1.5 text-xs font-bold capitalize" x-text="getDayLabel(day)"></label>
                                            </div>
                                            <div class="sm:col-span-7 flex items-center space-x-1.5 justify-end">
                                                <span class="text-[10px] text-muted">De</span>
                                                <input type="time" :name="'open_' + day" x-model="formData.working_hours[day].open" class="px-1 py-0.5 border border-line rounded bg-card text-[11px] focus:ring-1 focus:ring-olive w-16">
                                                <span class="text-[10px] text-muted">À</span>
                                                <input type="time" :name="'close_' + day" x-model="formData.working_hours[day].close" class="px-1 py-0.5 border border-line rounded bg-card text-[11px] focus:ring-1 focus:ring-olive w-16">
                                            </div>
                                        </div>
                                    </template>
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
