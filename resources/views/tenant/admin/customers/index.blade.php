@extends('tenant.layout')

@section('content')
<div x-data="{ 
    search: {{ json_encode($search ?? '') }},
    showDrawer: false,
    drawerMode: 'create',
    selectedCustomerId: null,
    formData: {
        name: {{ json_encode(old('name', '')) }},
        phone: {{ json_encode(old('phone', '')) }},
        email: {{ json_encode(old('email', '')) }},
        preferred_channel: {{ json_encode(old('preferred_channel', ['whatsapp'])) }},
        notes: {{ json_encode(old('notes', '')) }},
        labels: {{ json_encode(old('labels', [])) }}
    },
    isEmpty() {
        if (this.search === '') return false;
        let count = 0;
        document.querySelectorAll('[data-search]').forEach(el => {
            if (el.getAttribute('data-search').includes(this.search.toLowerCase())) {
                count++;
            }
        });
        return count === 0;
    },
    openCreateDrawer() {
        this.drawerMode = 'create';
        this.selectedCustomerId = null;
        this.formData = {
            name: '',
            phone: '',
            email: '',
            preferred_channel: ['whatsapp'],
            notes: '',
            labels: []
        };
        this.showDrawer = true;
    },
    openEditDrawer(customer) {
        this.drawerMode = 'edit';
        this.selectedCustomerId = customer.id;
        this.formData = {
            name: customer.name,
            phone: customer.phone,
            email: customer.email,
            preferred_channel: customer.preferred_channel,
            notes: customer.notes,
            labels: customer.labels
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
                const btn = document.querySelector('button[data-customer-id=\'' + editId + '\']');
                if (btn) btn.click();
            });
        }
        @if($errors->any())
            @if(old('id'))
                this.drawerMode = 'edit';
                this.selectedCustomerId = '{{ old('id') }}';
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
            <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Clients (CRM)</h1>
            <p class="text-muted text-sm font-outfit mt-1">Gérez votre base de données clients, historique et étiquettes.</p>
        </div>
        <button type="button" @click="openCreateDrawer()" class="px-5 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition-all duration-200 active:scale-95 shadow-sm hover:shadow text-sm font-outfit">
            + Ajouter un client
        </button>
    </div>

    <!-- Search / Filter bar -->
    <div class="bg-card border border-line p-4 rounded-2xl shadow-sm">
        <form @submit.prevent class="flex items-center space-x-4">
            <div class="flex-1 max-w-md">
                <input type="text" x-model="search" placeholder="Rechercher par nom, téléphone, e-mail..." class="w-full px-4 py-2 border border-line rounded-xl bg-paper text-sm focus:outline-none focus:ring-2 focus:ring-olive">
            </div>
            <button type="button" @click="search = ''" x-show="search !== ''" class="px-5 py-2 bg-line text-ink rounded-xl font-semibold hover:bg-paper transition text-sm" x-cloak>
                Effacer
            </button>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-card border border-line rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-paper border-b border-line text-muted uppercase text-xs tracking-wider">
                        <th class="p-4">Client</th>
                        <th class="p-4">Téléphone</th>
                        <th class="p-4">Canal préféré</th>
                        <th class="p-4">Étiquettes</th>
                        <th class="p-4">Dernière visite</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($customers as $c)
                        <tr class="hover:bg-paper/30" 
                            data-search="{{ strtolower($c->name . ' ' . $c->phone . ' ' . ($c->email ?? '')) }}"
                            x-show="search === '' || $el.getAttribute('data-search').includes(search.toLowerCase())"
                            x-transition>
                            <td class="p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-clay/15 text-clay font-bold flex items-center justify-center font-serif text-sm">
                                        {{ substr($c->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('tenant.customers.show', $c->id) }}" class="font-bold text-ink hover:text-clay transition">{{ $c->name }}</a>
                                        <p class="text-xs text-muted">{{ $c->email ?? 'Aucun e-mail' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 font-semibold">{{ $c->phone }}</td>
                            <td class="p-4 space-x-1">
                                @foreach($c->preferred_channel as $channel)
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold capitalize 
                                        @if($channel === 'whatsapp') bg-[#25d366]/10 text-emerald-800
                                        @else bg-purple-100 text-purple-800
                                        @endif">
                                        {{ $channel }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="p-4 space-x-1">
                                @forelse($c->labels as $label)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold text-paper" style="background-color: {{ $label->color }}">
                                        {{ $label->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-muted">-</span>
                                @endforelse
                            </td>
                            <td class="p-4 text-muted">
                                {{ $c->last_visit_at ? $c->last_visit_at->translatedFormat('d M Y') : 'Jamais venu' }}
                                @if($c->last_visit_at)
                                    <span class="text-xs block text-muted/80">({{ $c->last_visit_at->diffForHumans() }})</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('tenant.customers.show', $c->id) }}" title="Voir Profil" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-olive/10 text-olive hover:bg-olive hover:text-paper transition-all duration-200 active:scale-95 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <button type="button" data-customer-id="{{ $c->id }}" title="Modifier" @click="openEditDrawer({{ json_encode([
                                    'id' => $c->id,
                                    'name' => $c->name,
                                    'phone' => $c->phone,
                                    'email' => $c->email ?? '',
                                    'preferred_channel' => $c->preferred_channel,
                                    'notes' => $c->notes ?? '',
                                    'labels' => $c->labels->pluck('id')->toArray()
                                ]) }})" class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-line bg-paper text-ink hover:bg-line transition-all duration-200 active:scale-95 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                <form action="{{ route('tenant.customers.destroy', $c->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" title="Supprimer" onclick="confirmDelete(this, 'Êtes-vous sûr de vouloir supprimer définitivement ce client et son historique de rendez-vous ?')" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-red-100 text-red-800 hover:bg-red-800 hover:text-paper transition-all duration-200 active:scale-95 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr x-show="search === ''">
                            <td colspan="6" class="p-8 text-center text-muted">Aucun client trouvé.</td>
                        </tr>
                    @endforelse
                    <tr x-show="isEmpty()" x-cloak>
                        <td colspan="6" class="p-8 text-center text-muted">Aucun client ne correspond à votre recherche.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Drawer for Create / Edit Customer -->
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
                            <h3 class="text-xl font-bold text-ink font-serif" x-text="drawerMode === 'create' ? 'Ajouter un client' : 'Modifier le client'"></h3>
                            <button @click="showDrawer = false" class="text-muted hover:text-ink text-2xl font-bold">&times;</button>
                        </div>

                        <form :action="drawerMode === 'create' ? '{{ route('tenant.customers.store') }}' : '{{ route('tenant.customers.update', '') }}/' + selectedCustomerId" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="_method" :value="drawerMode === 'edit' ? 'PUT' : 'POST'">
                            <input type="hidden" name="id" :value="selectedCustomerId">

                            <!-- Name -->
                            <div>
                                <label for="drawer_name" class="block text-sm font-semibold mb-2">Nom Complet</label>
                                <input type="text" name="name" id="drawer_name" required x-model="formData.name" placeholder="Ex: Youssef Alami" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="drawer_phone" class="block text-sm font-semibold mb-2">N° de Téléphone (WhatsApp)</label>
                                <input type="text" name="phone" id="drawer_phone" required x-model="formData.phone" placeholder="Ex: +212 661 223344" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="drawer_email" class="block text-sm font-semibold mb-2">Adresse E-mail</label>
                                <input type="email" name="email" id="drawer_email" x-model="formData.email" placeholder="Ex: youssef@gmail.com" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            </div>

                            <!-- Preferred Channel -->
                            <div>
                                <label class="block text-sm font-semibold mb-2">Canal de notification préféré</label>
                                <div class="flex items-center space-x-6 p-3 border border-line rounded-xl bg-paper">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="preferred_channel[]" value="whatsapp" 
                                               :checked="formData.preferred_channel.includes('whatsapp')"
                                               @change="if ($el.checked) { if (!formData.preferred_channel.includes('whatsapp')) formData.preferred_channel.push('whatsapp') } else { formData.preferred_channel = formData.preferred_channel.filter(c => c !== 'whatsapp') }"
                                               class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                                        <span class="text-sm font-semibold">WhatsApp</span>
                                    </label>
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="preferred_channel[]" value="email" 
                                               :checked="formData.preferred_channel.includes('email')"
                                               @change="if ($el.checked) { if (!formData.preferred_channel.includes('email')) formData.preferred_channel.push('email') } else { formData.preferred_channel = formData.preferred_channel.filter(c => c !== 'email') }"
                                               class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                                        <span class="text-sm font-semibold">E-mail</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Labels -->
                            <div>
                                <label class="block text-sm font-semibold mb-2">Étiquettes (Labels)</label>
                                <div class="flex flex-wrap gap-2.5 p-3 border border-line rounded-xl bg-paper">
                                    @foreach($labels as $label)
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox" name="labels[]" value="{{ $label->id }}" 
                                                   :checked="formData.labels.includes({{ $label->id }})"
                                                   @change="if ($el.checked) { if (!formData.labels.includes({{ $label->id }})) formData.labels.push({{ $label->id }}) } else { formData.labels = formData.labels.filter(id => id !== {{ $label->id }}) }"
                                                   class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold text-paper" style="background-color: {{ $label->color }}">
                                                {{ $label->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="drawer_notes" class="block text-sm font-semibold mb-2">Commentaires / Antécédents</label>
                                <textarea name="notes" id="drawer_notes" rows="3" x-model="formData.notes" placeholder="Informations utiles sur le client..." class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm"></textarea>
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
