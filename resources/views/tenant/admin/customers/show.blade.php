@extends('tenant.layout')

@section('content')
<script>
    window.customerProfileData = {
        historyList: {!! json_encode($allAppointmentsArray) !!},
        allAttachments: {!! json_encode($allAttachments) !!},
        fieldDefinitions: {!! json_encode($template->field_definitions ?? []) !!}
    };
</script>

<div x-data="{
    activeTab: (new URLSearchParams(window.location.search).has('history_page') || new URLSearchParams(window.location.search).has('start_date') || new URLSearchParams(window.location.search).has('status') || new URLSearchParams(window.location.search).has('search_note')) ? 'history' : 'overview',
    showConfigModal: false,
    previewAttachment: null,
    previewIndex: -1,
    attachmentsList: [],
    
    // History filters & list
    historyList: window.customerProfileData.historyList,
    historyFilters: {
        startDate: '',
        endDate: '',
        status: '',
        serviceId: '',
        resourceId: '',
        searchNote: '',
        sort: 'desc'
    },
    historyPage: 1,

    getStatusLabel(status) {
        const labels = {
            completed: 'Complété',
            confirmed: 'Confirmé',
            pending: 'En attente',
            cancelled: 'Annulé',
            no_show: 'Absence'
        };
        return labels[status] || status;
    },

    get filteredAppointments() {
        let filtered = this.historyList.filter(app => {
            if (this.historyFilters.startDate) {
                let appDate = app.starts_at.substring(0, 10);
                if (appDate < this.historyFilters.startDate) return false;
            }
            if (this.historyFilters.endDate) {
                let appDate = app.starts_at.substring(0, 10);
                if (appDate > this.historyFilters.endDate) return false;
            }
            if (this.historyFilters.status && app.status !== this.historyFilters.status) {
                return false;
            }
            if (this.historyFilters.serviceId && app.service_id != this.historyFilters.serviceId) {
                return false;
            }
            if (this.historyFilters.resourceId && app.resource_id != this.historyFilters.resourceId) {
                return false;
            }
            if (this.historyFilters.searchNote) {
                let term = this.historyFilters.searchNote.toLowerCase();
                if (!app.note.toLowerCase().includes(term)) return false;
            }
            return true;
        });

        filtered.sort((a, b) => {
            let dateA = new Date(a.starts_at_raw);
            let dateB = new Date(b.starts_at_raw);
            return this.historyFilters.sort === 'asc' ? dateA - dateB : dateB - dateA;
        });

        return filtered;
    },

    get paginatedAppointments() {
        let start = (this.historyPage - 1) * 10;
        return this.filteredAppointments.slice(start, start + 10);
    },

    // Lightbox modal functionality
    openPreview(attachmentId) {
        this.previewIndex = this.attachmentsList.findIndex(a => a.id == attachmentId);
        if (this.previewIndex !== -1) {
            this.previewAttachment = this.attachmentsList[this.previewIndex];
        }
    },
    nextPreview() {
        if (this.attachmentsList.length === 0) return;
        this.previewIndex = (this.previewIndex + 1) % this.attachmentsList.length;
        this.previewAttachment = this.attachmentsList[this.previewIndex];
    },
    prevPreview() {
        if (this.attachmentsList.length === 0) return;
        this.previewIndex = (this.previewIndex - 1 + this.attachmentsList.length) % this.attachmentsList.length;
        this.previewAttachment = this.attachmentsList[this.previewIndex];
    },
    closePreview() {
        this.previewAttachment = null;
        this.previewIndex = -1;
    },
    
    init() {
        // Populate the JS attachments list for the lightbox
        this.attachmentsList = window.customerProfileData.allAttachments;
    }
}" class="space-y-8">

    <!-- Header / Navigation Back -->
    <div>
        <a href="{{ route('tenant.customers.index') }}" class="text-sm text-muted hover:text-ink font-semibold flex items-center mb-2 font-outfit">
            ← Retour aux clients
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="h-16 w-16 rounded-full bg-clay/10 text-clay font-bold flex items-center justify-center font-fraunces text-2xl shadow-sm border border-clay/10">
                    {{ substr($customer->name, 0, 2) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-ink font-fraunces">{{ $customer->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="text-sm text-muted font-semibold font-outfit">{{ $customer->phone }}</span>
                        @if($customer->email)
                            <span class="text-xs text-muted/70 font-outfit">&bull;</span>
                            <span class="text-sm text-muted/70 font-outfit">{{ $customer->email }}</span>
                        @endif
                        <span class="text-xs text-muted/70 font-outfit">&bull;</span>
                        <div class="flex space-x-1">
                            @forelse($customer->labels as $label)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold text-paper font-outfit" style="background-color: {{ $label->color }}">
                                    {{ $label->name }}
                                </span>
                            @empty
                                <span class="text-xs text-muted/50 font-outfit">Aucune étiquette</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('tenant.customers.index', ['edit' => $customer->id]) }}" class="px-4 py-2 border border-line bg-card hover:bg-paper text-ink text-sm font-semibold rounded-xl transition-all duration-200 active:scale-95 shadow-sm font-outfit">
                    Modifier la fiche
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs Nav -->
    <div class="border-b border-line">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'overview'" 
                    :class="activeTab === 'overview' ? 'border-clay text-clay' : 'border-transparent text-muted hover:text-ink hover:border-line'" 
                    class="py-4 px-1 border-b-2 font-semibold text-sm transition">
                Vue d'ensemble
            </button>
            <button @click="activeTab = 'history'" 
                    :class="activeTab === 'history' ? 'border-clay text-clay' : 'border-transparent text-muted hover:text-ink hover:border-line'" 
                    class="py-4 px-1 border-b-2 font-semibold text-sm transition">
                Historique RDV
            </button>
            <button @click="activeTab = 'records'" 
                    :class="activeTab === 'records' ? 'border-clay text-clay' : 'border-transparent text-muted hover:text-ink hover:border-line'" 
                    class="py-4 px-1 border-b-2 font-semibold text-sm transition">
                Dossier & Documents
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div>
        <!-- OVERVIEW TAB -->
        <div x-show="activeTab === 'overview'" class="grid grid-cols-1 md:grid-cols-3 gap-8" x-cloak>
            <!-- Info Card -->
            <div class="bg-card border border-line rounded-2xl shadow-sm p-6 space-y-6">
                <h3 class="text-lg font-bold font-serif text-ink border-b border-line pb-2">Coordonnées</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs text-muted font-bold uppercase tracking-wider">Téléphone</p>
                        <p class="font-bold text-ink mt-0.5">{{ $customer->phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted font-bold uppercase tracking-wider">Adresse E-mail</p>
                        <p class="font-bold text-ink mt-0.5">{{ $customer->email ?? 'Non renseignée' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted font-bold uppercase tracking-wider">Notifications préférées</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($customer->preferred_channel as $channel)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold capitalize 
                                    @if($channel === 'whatsapp') bg-emerald-50 text-emerald-700 border border-emerald-100
                                    @else bg-purple-50 text-purple-700 border border-purple-100
                                    @endif">
                                    {{ $channel }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-muted font-bold uppercase tracking-wider">Dernière visite</p>
                        <p class="font-bold text-ink mt-0.5">
                            {{ $customer->last_visit_at ? $customer->last_visit_at->translatedFormat('d F Y à H:i') : 'Jamais venu' }}
                            @if($customer->last_visit_at)
                                <span class="text-xs text-muted block">({{ $customer->last_visit_at->diffForHumans() }})</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notes & General Info -->
            <div class="md:col-span-2 space-y-6">
                <!-- Stats Summary -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div class="bg-card border border-line rounded-2xl shadow-sm p-5 flex flex-col justify-between">
                        <span class="text-xs text-muted font-bold uppercase tracking-wider">Total Visites</span>
                        <span class="text-3xl font-bold font-serif text-clay mt-2">{{ $customer->appointments()->where('status', 'completed')->count() }}</span>
                    </div>
                    <div class="bg-card border border-line rounded-2xl shadow-sm p-5 flex flex-col justify-between">
                        <span class="text-xs text-muted font-bold uppercase tracking-wider">RDV Prévus</span>
                        <span class="text-3xl font-bold font-serif text-olive mt-2">{{ $customer->appointments()->where('starts_at', '>', now())->where('status', 'confirmed')->count() }}</span>
                    </div>
                    <div class="bg-card border border-line rounded-2xl shadow-sm p-5 flex flex-col justify-between col-span-2 md:col-span-1">
                        <span class="text-xs text-muted font-bold uppercase tracking-wider">No-shows</span>
                        <span class="text-3xl font-bold font-serif text-muted mt-2">{{ $customer->appointments()->where('status', 'no_show')->count() }}</span>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-card border border-line rounded-2xl shadow-sm p-6 space-y-4">
                    <h3 class="text-lg font-bold font-serif text-ink border-b border-line pb-2">Notes Administratives</h3>
                    <p class="text-sm text-ink whitespace-pre-line leading-relaxed">
                        {{ $customer->notes ?: 'Aucune note administrative pour ce client. Vous pouvez en ajouter en modifiant sa fiche.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- HISTORY TAB -->
        <div x-show="activeTab === 'history'" class="space-y-6" x-cloak>
            <!-- History Filters -->
            <div class="bg-card border border-line p-5 rounded-2xl shadow-sm">
                <form @submit.prevent class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <!-- Date range from -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Du</label>
                        <input type="date" x-model="historyFilters.startDate" @input="historyPage = 1" class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-xs focus:outline-none focus:ring-1 focus:ring-olive">
                    </div>

                    <!-- Date range to -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Au</label>
                        <input type="date" x-model="historyFilters.endDate" @input="historyPage = 1" class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-xs focus:outline-none focus:ring-1 focus:ring-olive">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Statut</label>
                        <select x-model="historyFilters.status" @change="historyPage = 1" class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-xs focus:outline-none focus:ring-1 focus:ring-olive">
                            <option value="">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="confirmed">Confirmé</option>
                            <option value="completed">Complété</option>
                            <option value="cancelled">Annulé</option>
                            <option value="no_show">Absence</option>
                        </select>
                    </div>

                    <!-- Service -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Prestation</label>
                        <select x-model="historyFilters.serviceId" @change="historyPage = 1" class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-xs focus:outline-none focus:ring-1 focus:ring-olive">
                            <option value="">Toutes</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Staff -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Collaborateur</label>
                        <select x-model="historyFilters.resourceId" @change="historyPage = 1" class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-xs focus:outline-none focus:ring-1 focus:ring-olive">
                            <option value="">Tous</option>
                            @foreach($resources as $resource)
                                <option value="{{ $resource->id }}">{{ $resource->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notes search -->
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1">Recherche Notes</label>
                        <input type="text" x-model="historyFilters.searchNote" @input="historyPage = 1" placeholder="Mots-clés..." class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-xs focus:outline-none focus:ring-1 focus:ring-olive">
                    </div>

                    <!-- Sorting and CTA buttons -->
                    <div class="sm:col-span-2 md:col-span-4 lg:col-span-6 flex flex-wrap items-center justify-between pt-2 border-t border-line mt-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-muted font-semibold">Trier par date :</span>
                            <select x-model="historyFilters.sort" @change="historyPage = 1" class="px-2.5 py-1 border border-line rounded-lg bg-paper text-xs focus:outline-none">
                                <option value="desc">Plus récent au plus ancien</option>
                                <option value="asc">Plus ancien au plus récent</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center space-x-4 mt-2 sm:mt-0">
                            <span class="text-xs text-muted font-semibold" x-text="filteredAppointments.length + ' rdv trouvé(s)'"></span>
                            <button type="button" @click="historyFilters = { startDate: '', endDate: '', status: '', serviceId: '', resourceId: '', searchNote: '', sort: 'desc' }; historyPage = 1" class="px-4 py-2 border border-line bg-paper rounded-xl text-xs font-semibold hover:bg-line transition">
                                Réinitialiser
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Appointments List Table -->
            <div class="bg-card border border-line rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-paper border-b border-line text-muted uppercase text-xs tracking-wider">
                                <th class="p-4">Date & Heure</th>
                                <th class="p-4">Prestation</th>
                                <th class="p-4">Personnel</th>
                                <th class="p-4">Prix (MAD)</th>
                                <th class="p-4">Statut</th>
                                <th class="p-4">Notes de la visite</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <template x-for="app in paginatedAppointments" :key="app.id">
                                <tr class="hover:bg-paper/30">
                                    <td class="p-4 font-bold text-ink whitespace-nowrap">
                                        <span x-text="app.starts_at_formatted"></span>
                                        <span class="block text-xs font-normal text-muted mt-0.5" x-text="'à ' + app.starts_at_time"></span>
                                    </td>
                                    <td class="p-4 font-semibold text-ink" x-text="app.service_name"></td>
                                    <td class="p-4 text-ink" x-text="app.resource_name"></td>
                                    <td class="p-4 font-bold text-ink" x-text="parseFloat(app.price).toFixed(2)"></td>
                                    <td class="p-4">
                                        <span :class="{
                                            'bg-emerald-50 text-emerald-800 border-emerald-100': app.status === 'completed',
                                            'bg-blue-50 text-blue-800 border-blue-100': app.status === 'confirmed',
                                            'bg-amber-50 text-amber-800 border-amber-100': app.status === 'pending',
                                            'bg-red-50 text-red-800 border-red-100': app.status === 'cancelled',
                                            'bg-gray-50 text-gray-800 border-gray-100': app.status === 'no_show'
                                        }" class="inline-block px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider border" x-text="getStatusLabel(app.status)">
                                        </span>
                                    </td>
                                    <td class="p-4 text-muted text-xs max-w-xs whitespace-pre-line leading-relaxed" x-text="app.note || '-'"></td>
                                </tr>
                            </template>
                            <tr x-show="filteredAppointments.length === 0">
                                <td colspan="6" class="p-8 text-center text-muted">Aucun rendez-vous ne correspond à vos filtres.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Alpine pagination controls -->
                <div class="p-4 bg-paper border-t border-line flex items-center justify-between" x-show="filteredAppointments.length > 10">
                    <button type="button" @click="if(historyPage > 1) historyPage--" :disabled="historyPage === 1" class="px-4 py-2 border border-line bg-card hover:bg-paper rounded-xl text-xs font-semibold disabled:opacity-50 transition shadow-sm">
                        Précédent
                    </button>
                    <span class="text-xs text-muted font-semibold" x-text="'Page ' + historyPage + ' sur ' + Math.ceil(filteredAppointments.length / 10)"></span>
                    <button type="button" @click="if(historyPage < Math.ceil(filteredAppointments.length / 10)) historyPage++" :disabled="historyPage === Math.ceil(filteredAppointments.length / 10)" class="px-4 py-2 border border-line bg-card hover:bg-paper rounded-xl text-xs font-semibold disabled:opacity-50 transition shadow-sm">
                        Suivant
                    </button>
                </div>
            </div>
        </div>

        <!-- RECORDS TAB -->
        <div x-show="activeTab === 'records'" class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-cloak>
            <!-- Left panel: New record card -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Record Form Card -->
                <div class="bg-card border border-line rounded-2xl shadow-sm p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-line pb-3">
                        <h3 class="text-lg font-bold font-serif text-ink">Nouvelle Entrée</h3>
                        <button type="button" @click="showConfigModal = true" class="text-xs text-olive hover:text-emerald-700 font-bold flex items-center">
                            ⚙ Configurer
                        </button>
                    </div>

                    <!-- Create Record Form -->
                    <form action="{{ route('tenant.records.store', $customer->id) }}" method="POST" id="record-form" class="space-y-4" x-data="{
                        attachmentIds: [],
                        isUploading: false,
                        uploadProgress: 0,
                        uploadedFiles: [],
                        
                        triggerUpload() {
                            this.$refs.fileInput.click();
                        },
                        
                        handleFileDrop(e) {
                            let files = e.dataTransfer.files;
                            this.uploadFiles(files);
                        },
                        
                        handleFileSelect(e) {
                            let files = e.target.files;
                            this.uploadFiles(files);
                        },
                        
                        uploadFiles(files) {
                            if (files.length === 0) return;
                            
                            this.isUploading = true;
                            this.uploadProgress = 0;
                            
                            // Upload files one by one or in batch
                            for (let i = 0; i < files.length; i++) {
                                let file = files[i];
                                
                                // Client-side size limit check
                                if (file.size > 10 * 1024 * 1024) {
                                    Swal.fire({
                                        title: 'Erreur',
                                        text: 'Le fichier ' + file.name + ' dépasse la limite autorisée de 10 Mo.',
                                        icon: 'error',
                                        confirmButtonColor: '#c2683f'
                                    });
                                    this.isUploading = false;
                                    continue;
                                }
                                
                                let formData = new FormData();
                                formData.append('file', file);
                                formData.append('customer_id', '{{ $customer->id }}');
                                
                                let appointmentSelect = document.getElementById('record_appointment_id');
                                if (appointmentSelect && appointmentSelect.value) {
                                    formData.append('appointment_id', appointmentSelect.value);
                                }
                                
                                let xhr = new XMLHttpRequest();
                                xhr.open('POST', '{{ route('tenant.attachments.store', $customer->id) }}', true);
                                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                                
                                // Progress bar callback
                                xhr.upload.addEventListener('progress', (e) => {
                                    if (e.lengthComputable) {
                                        this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                                    }
                                });
                                
                                xhr.onload = () => {
                                    this.isUploading = false;
                                    if (xhr.status === 200) {
                                        let response = JSON.parse(xhr.responseText);
                                        if (response.success) {
                                            this.attachmentIds.push(response.id);
                                            this.uploadedFiles.push({
                                                id: response.id,
                                                name: response.original_name,
                                                size: Math.round(response.size / 1024)
                                            });
                                            // Add to global attachments list for immediate lightbox sync
                                            this.attachmentsList.unshift({
                                                id: response.id,
                                                original_name: response.original_name,
                                                mime_type: response.mime_type,
                                                size: response.size,
                                                file_path: 'serve'
                                            });
                                        } else {
                                            Swal.fire('Erreur', response.error || 'Erreur lors du dépôt.', 'error');
                                        }
                                    } else {
                                        Swal.fire('Erreur', 'Erreur serveur lors de l\'envoi.', 'error');
                                    }
                                };
                                
                                xhr.onerror = () => {
                                    this.isUploading = false;
                                    Swal.fire('Erreur', 'La connexion au serveur a échoué.', 'error');
                                };
                                
                                xhr.send(formData);
                            }
                        },
                        
                        deleteUploadedFile(index, fileId) {
                            if (confirm('Voulez-vous supprimer ce fichier déposé ?')) {
                                fetch('/admin/attachments/' + fileId, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                }).then(res => {
                                    if (res.ok) {
                                        this.uploadedFiles.splice(index, 1);
                                        this.attachmentIds = this.attachmentIds.filter(id => id != fileId);
                                        this.attachmentsList = this.attachmentsList.filter(a => a.id != fileId);
                                    }
                                });
                            }
                        }
                    }">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                        <!-- Link to Appointment -->
                        <div>
                            <label for="record_appointment_id" class="block text-xs font-semibold mb-1.5">Associer à un rendez-vous (Optionnel)</label>
                            <select name="appointment_id" id="record_appointment_id" class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-sm focus:outline-none focus:ring-2 focus:ring-olive">
                                <option value="">Aucun - Dossier général</option>
                                @foreach($linkableAppointments as $la)
                                    <option value="{{ $la->id }}">
                                        {{ $la->starts_at->translatedFormat('d M Y H:i') }} - {{ $la->service->name }} ({{ $la->resource->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Custom JSON Template Fields -->
                        @if($template && is_array($template->field_definitions))
                            @foreach($template->field_definitions as $field)
                                <div>
                                    <label class="block text-xs font-semibold mb-1.5">
                                        {{ $field['label'] }}
                                        @if(!empty($field['required'])) <span class="text-red-500">*</span> @endif
                                    </label>
                                    @if($field['type'] === 'long_text')
                                        <textarea name="{{ $field['name'] }}" @if(!empty($field['required'])) required @endif placeholder="Saisir..." rows="3" class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-sm focus:outline-none focus:ring-2 focus:ring-olive"></textarea>
                                    @else
                                        <input type="text" name="{{ $field['name'] }}" @if(!empty($field['required'])) required @endif placeholder="Saisir..." class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-sm focus:outline-none focus:ring-2 focus:ring-olive">
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        <!-- General Visit Notes -->
                        <div>
                            <label for="record_notes" class="block text-xs font-semibold mb-1.5">Notes complémentaires / Observations</label>
                            <textarea name="notes" id="record_notes" placeholder="Notes générales..." rows="3" class="w-full px-3 py-2 border border-line rounded-xl bg-paper text-sm focus:outline-none focus:ring-2 focus:ring-olive"></textarea>
                        </div>

                        <!-- Drag and Drop Dropzone -->
                        <div>
                            <label class="block text-xs font-semibold mb-1.5">Pièces Jointes (Images, PDF, Docx - Max 10Mo)</label>
                            
                            <div @dragover.prevent="$el.classList.add('border-olive', 'bg-olive/5')" 
                                 @dragleave.prevent="$el.classList.remove('border-olive', 'bg-olive/5')" 
                                 @drop.prevent="$el.classList.remove('border-olive', 'bg-olive/5'); handleFileDrop($event)"
                                 @click="triggerUpload()"
                                 class="border-2 border-dashed border-line rounded-xl p-4 text-center cursor-pointer hover:border-olive hover:bg-paper/50 transition bg-paper flex flex-col items-center justify-center space-y-2">
                                
                                <svg class="w-8 h-8 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <p class="text-xs font-bold text-ink">Glissez-déposez des fichiers ici</p>
                                <p class="text-[10px] text-muted">ou cliquez pour parcourir les dossiers</p>
                                
                                <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" multiple class="hidden">
                            </div>

                            <!-- Live Upload Progress -->
                            <div x-show="isUploading" class="mt-3 space-y-1.5" x-cloak>
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span>Téléversement...</span>
                                    <span x-text="uploadProgress + '%'"></span>
                                </div>
                                <div class="w-full bg-line rounded-full h-2">
                                    <div class="bg-olive h-2 rounded-full transition-all duration-150" :style="'width: ' + uploadProgress + '%'"></div>
                                </div>
                            </div>

                            <!-- List of uploaded files to attach -->
                            <div x-show="uploadedFiles.length > 0" class="mt-3 space-y-1.5" x-cloak>
                                <template x-for="(file, index) in uploadedFiles" :key="index">
                                    <div class="flex items-center justify-between bg-paper p-2 rounded-lg border border-line text-xs font-semibold">
                                        <span class="truncate pr-4" x-text="file.name + ' (' + file.size + ' KB)'"></span>
                                        <button type="button" @click="deleteUploadedFile(index, file.id)" class="text-red-700 hover:text-red-950 font-bold">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Hidden Attachment IDs -->
                        <template x-for="id in attachmentIds" :key="id">
                            <input type="hidden" name="attachment_ids[]" :value="id">
                        </template>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-clay text-paper text-sm font-semibold hover:bg-[#b05830] transition shadow-sm">
                                Enregistrer l'entrée
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right panel: Timeline of record entries -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Subtitle / Title -->
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold font-serif text-ink">Chronologie du dossier</h3>
                    <span class="text-xs text-muted font-semibold bg-card px-2.5 py-1 rounded-lg border border-line">
                        {{ $records->total() }} entrées
                    </span>
                </div>

                <!-- Record Entries Timeline -->
                <div class="space-y-6 relative border-l border-line pl-6 ml-4">
                    @forelse($records as $rec)
                        <!-- Timeline Node Dot -->
                        <div class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full bg-clay border-2 border-paper"></div>

                        <!-- Record Card -->
                        <div class="bg-card border border-line rounded-2xl shadow-sm p-6 space-y-4 relative">
                            <!-- Top Details -->
                            <div class="flex items-center justify-between border-b border-line pb-3">
                                <div>
                                    <span class="text-xs text-muted font-bold block">{{ $rec->created_at->translatedFormat('d F Y à H:i') }}</span>
                                    <span class="text-xs font-semibold text-ink/80 mt-0.5 block">
                                        Par : <strong>{{ $rec->staff->name ?? 'Admin' }}</strong>
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($rec->appointment)
                                        <span class="text-[10px] bg-olive/10 text-emerald-800 border border-olive/10 px-2 py-0.5 rounded font-bold uppercase tracking-wider">
                                            RDV : {{ $rec->appointment->starts_at->translatedFormat('d M Y') }}
                                        </span>
                                    @endif
                                    
                                    <form action="{{ route('tenant.records.destroy', $rec->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this, 'Voulez-vous supprimer cette entrée de dossier ? Cette action supprimera également toutes ses pièces jointes.')" class="text-red-700 hover:text-red-900 text-xs font-semibold">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Dynamic fields content -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-paper/30 p-4 rounded-xl border border-line/50">
                                @if($template && is_array($template->field_definitions))
                                    @foreach($template->field_definitions as $field)
                                        @php
                                            $val = $rec->entry_data[$field['name']] ?? '-';
                                        @endphp
                                        <div class="space-y-0.5 @if($field['type'] === 'long_text') md:col-span-2 @endif">
                                            <span class="text-[10px] text-muted uppercase font-bold tracking-wider">{{ $field['label'] }}</span>
                                            <p class="text-sm text-ink font-semibold whitespace-pre-line leading-relaxed">{{ $val }}</p>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <!-- General Notes -->
                            @if($rec->notes)
                                <div class="space-y-1">
                                    <span class="text-[10px] text-muted uppercase font-bold tracking-wider">Observations / Diagnostic complémentaire</span>
                                    <p class="text-sm text-ink whitespace-pre-line leading-relaxed">{{ $rec->notes }}</p>
                                </div>
                            @endif

                            <!-- Record Attachments list -->
                            @if($rec->attachments->count() > 0)
                                <div class="space-y-2 border-t border-line pt-3 mt-3">
                                    <span class="text-[10px] text-muted uppercase font-bold tracking-wider block">Pièces Jointes</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($rec->attachments as $att)
                                            <div class="flex items-center justify-between p-2.5 rounded-xl border border-line bg-paper/50 hover:bg-paper transition">
                                                <div class="flex items-center space-x-2.5 overflow-hidden">
                                                    <!-- File Icon -->
                                                    @if(str_starts_with($att->mime_type, 'image/'))
                                                        <svg class="w-5 h-5 text-clay flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    @elseif($att->mime_type === 'application/pdf')
                                                        <svg class="w-5 h-5 text-red-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    @else
                                                        <svg class="w-5 h-5 text-blue-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    @endif
                                                    
                                                    <div class="overflow-hidden">
                                                        <button type="button" @click="openPreview({{ $att->id }})" class="text-xs font-semibold text-ink hover:text-clay hover:underline truncate block text-left">
                                                            {{ $att->original_name }}
                                                        </button>
                                                        <span class="text-[9px] text-muted block">{{ number_format($att->size / 1024, 1) }} KB</span>
                                                    </div>
                                                </div>

                                                <div class="flex items-center space-x-1 pl-2 flex-shrink-0">
                                                    <button type="button" onclick="renameAttachment({{ $att->id }}, '{{ addslashes(pathinfo($att->original_name, PATHINFO_FILENAME)) }}')" class="p-1 text-muted hover:text-ink text-xs" title="Renommer">✏</button>
                                                    <a href="{{ route('tenant.attachments.serve', $att->id) }}?download=1" class="p-1 text-muted hover:text-olive text-xs" title="Télécharger" target="_blank">💾</a>
                                                    <form action="{{ route('tenant.attachments.destroy', $att->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="confirmDelete(this, 'Supprimer ce fichier ?')" class="p-1 text-muted hover:text-red-700 text-xs" title="Supprimer">&times;</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-card border border-line rounded-2xl p-8 text-center text-muted">
                            Aucune entrée dans le dossier pour l'instant.
                        </div>
                    @endforelse

                    @if($records->hasPages())
                        <div class="pt-4 bg-paper">
                            {{ $records->links() }}
                        </div>
                    @endif
                </div>

                <!-- Standalone / General Files Card -->
                <div class="bg-card border border-line rounded-2xl shadow-sm p-6 space-y-4 mt-8">
                    <h3 class="text-lg font-bold font-serif text-ink border-b border-line pb-2">Documents généraux (Hors consultations)</h3>
                    
                    @if($generalAttachments->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($generalAttachments as $att)
                                <div class="flex items-center justify-between p-2.5 rounded-xl border border-line bg-paper/50 hover:bg-paper transition">
                                    <div class="flex items-center space-x-2.5 overflow-hidden">
                                        <!-- File Icon -->
                                        @if(str_starts_with($att->mime_type, 'image/'))
                                            <svg class="w-5 h-5 text-clay flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @elseif($att->mime_type === 'application/pdf')
                                            <svg class="w-5 h-5 text-red-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg class="w-5 h-5 text-blue-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @endif
                                        
                                        <div class="overflow-hidden">
                                            <button type="button" @click="openPreview({{ $att->id }})" class="text-xs font-semibold text-ink hover:text-clay hover:underline truncate block text-left">
                                                {{ $att->original_name }}
                                            </button>
                                            <span class="text-[9px] text-muted block">{{ number_format($att->size / 1024, 1) }} KB</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-1 pl-2 flex-shrink-0">
                                        <button type="button" onclick="renameAttachment({{ $att->id }}, '{{ addslashes(pathinfo($att->original_name, PATHINFO_FILENAME)) }}')" class="p-1 text-muted hover:text-ink text-xs" title="Renommer">✏</button>
                                        <a href="{{ route('tenant.attachments.serve', $att->id) }}?download=1" class="p-1 text-muted hover:text-olive text-xs" title="Télécharger" target="_blank">💾</a>
                                        <form action="{{ route('tenant.attachments.destroy', $att->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this, 'Supprimer ce document ?')" class="p-1 text-muted hover:text-red-700 text-xs" title="Supprimer">&times;</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-muted">Aucun document libre rattaché à ce dossier.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 1. TEMPLATE CONFIGURATOR MODAL -->
    <div x-show="showConfigModal" x-cloak class="fixed inset-0 overflow-hidden z-50 animate-fade-in" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-[#1a1410]/50 backdrop-blur-sm transition-opacity" @click="showConfigModal = false"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-md" 
                     x-show="showConfigModal" 
                     x-transition:enter="transform transition ease-in-out duration-300" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-300" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full">
                    
                    <div class="h-full flex flex-col bg-card border-l border-line p-6 shadow-2xl overflow-y-auto space-y-6" @click.stop x-data="{
                        fields: JSON.parse(JSON.stringify(window.customerProfileData.fieldDefinitions)),
                        addField() {
                            this.fields.push({ label: '', type: 'text', required: false });
                        },
                        removeField(index) {
                            if (this.fields.length > 1) {
                                this.fields.splice(index, 1);
                            } else {
                                Swal.fire('Attention', 'Le formulaire doit contenir au moins un champ.', 'warning');
                            }
                        }
                    }">
                        <div class="flex items-center justify-between border-b border-line pb-3">
                            <div>
                                <h3 class="text-xl font-bold text-ink font-serif">Modèle de Dossier</h3>
                                <p class="text-xs text-muted mt-0.5">Personnalisez les champs du formulaire</p>
                            </div>
                            <button @click="showConfigModal = false" class="text-muted hover:text-ink text-2xl font-bold">&times;</button>
                        </div>

                        <form action="{{ route('tenant.config.record-template') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <div class="space-y-4">
                                <template x-for="(field, index) in fields" :key="index">
                                    <div class="p-4 rounded-xl border border-line bg-paper/30 space-y-3 relative">
                                        <!-- Remove button -->
                                        <button type="button" @click="removeField(index)" class="absolute top-2 right-3 text-red-700 hover:text-red-950 font-bold text-sm" title="Supprimer le champ">
                                            Supprimer
                                        </button>

                                        <!-- Field Label -->
                                        <div>
                                            <label class="block text-xs font-semibold mb-1">Nom / Libellé du champ</label>
                                            <input type="text" :name="'fields['+index+'][label]'" x-model="field.label" required placeholder="Ex: Prescription, Couleur cheveux" class="w-full px-3 py-1.5 border border-line rounded-lg bg-paper text-xs focus:outline-none focus:ring-1 focus:ring-olive">
                                        </div>

                                        <!-- Grid Type / Required -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold mb-1">Type d'entrée</label>
                                                <select :name="'fields['+index+'][type]'" x-model="field.type" class="w-full px-3 py-1.5 border border-line rounded-lg bg-paper text-xs focus:outline-none">
                                                    <option value="text">Texte court (1 ligne)</option>
                                                    <option value="long_text">Texte long (paragraphe)</option>
                                                </select>
                                            </div>
                                            <div class="flex items-end pb-1.5">
                                                <label class="flex items-center space-x-2 cursor-pointer select-none">
                                                    <input type="checkbox" :name="'fields['+index+'][required]'" x-model="field.required" value="1" class="h-4 w-4 text-olive border-line rounded">
                                                    <span class="text-xs font-semibold">Champ obligatoire</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <button type="button" @click="addField()" class="w-full py-2 border border-dashed border-olive text-olive hover:bg-olive/5 rounded-xl text-xs font-bold transition">
                                + Ajouter un champ personnalisé
                            </button>

                            <div class="pt-4 border-t border-line flex items-center justify-end space-x-3">
                                <button type="button" @click="showConfigModal = false" class="px-4 py-2 border border-line bg-paper text-xs font-semibold rounded-lg hover:bg-line">
                                    Annuler
                                </button>
                                <button type="submit" class="px-5 py-2 bg-clay text-paper text-xs font-semibold rounded-lg hover:bg-[#b05830]">
                                    Enregistrer le modèle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. FILE LIGHTBOX / PREVIEW MODAL -->
    <div x-show="previewAttachment !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#1a1410]/80 backdrop-blur-md" role="dialog" aria-modal="true" @keydown.escape.window="closePreview()">
        <!-- Close trigger overlay -->
        <div class="absolute inset-0 cursor-default" @click="closePreview()"></div>

        <!-- Lightbox Content Container -->
        <div class="bg-card border border-line rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col relative z-10 shadow-2xl" @click.stop>
            <!-- Top bar -->
            <div class="px-6 py-4 border-b border-line flex items-center justify-between bg-paper/50">
                <div>
                    <h3 class="text-base font-bold text-ink truncate max-w-md" x-text="previewAttachment?.original_name"></h3>
                    <p class="text-[10px] text-muted font-semibold mt-0.5" x-text="'Taille : ' + (previewAttachment ? Math.round(previewAttachment.size/1024) : 0) + ' KB'"></p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Download Link -->
                    <a :href="'/admin/attachments/' + previewAttachment?.id + '/serve?download=1'" class="px-3.5 py-1.5 rounded-lg border border-line bg-card hover:bg-paper text-xs font-bold text-ink transition" target="_blank">
                        Télécharger
                    </a>
                    <!-- Close button -->
                    <button @click="closePreview()" class="text-muted hover:text-ink text-2xl font-bold leading-none">&times;</button>
                </div>
            </div>

            <!-- Preview Frame Area -->
            <div class="flex-1 bg-paper/20 p-6 overflow-y-auto flex items-center justify-center min-h-[350px] relative">
                
                <!-- Left Nav Button -->
                <button @click="prevPreview()" class="absolute left-4 p-3 rounded-full bg-card/80 border border-line shadow hover:bg-card text-ink hover:text-clay text-xl font-bold z-10">&lsaquo;</button>

                <!-- Dynamic Loader content based on MIME -->
                <div class="w-full h-full flex items-center justify-center">
                    
                    <!-- Images -->
                    <template x-if="previewAttachment && previewAttachment.mime_type.startsWith('image/')">
                        <img :src="'/admin/attachments/' + previewAttachment.id + '/serve'" class="max-h-[60vh] max-w-full rounded-lg object-contain shadow-md">
                    </template>

                    <!-- PDFs -->
                    <template x-if="previewAttachment && previewAttachment.mime_type === 'application/pdf'">
                        <iframe :src="'/admin/attachments/' + previewAttachment.id + '/serve'" class="w-full h-[60vh] rounded-lg border border-line shadow-sm" frameborder="0"></iframe>
                    </template>

                    <!-- Other types (fallback) -->
                    <template x-if="previewAttachment && !previewAttachment.mime_type.startsWith('image/') && previewAttachment.mime_type !== 'application/pdf'">
                        <div class="text-center space-y-4 max-w-sm">
                            <div class="h-20 w-20 bg-clay/15 text-clay rounded-full flex items-center justify-center mx-auto">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-ink">Aperçu non disponible</h4>
                                <p class="text-xs text-muted mt-1">Ce type de fichier (document Word, zip, etc.) ne peut pas être prévisualisé en ligne.</p>
                            </div>
                            <a :href="'/admin/attachments/' + previewAttachment.id + '/serve?download=1'" class="inline-block px-5 py-2.5 bg-clay hover:bg-[#b05830] text-paper text-xs font-semibold rounded-xl transition shadow">
                                Télécharger le fichier
                            </a>
                        </div>
                    </template>

                </div>

                <!-- Right Nav Button -->
                <button @click="nextPreview()" class="absolute right-4 p-3 rounded-full bg-card/80 border border-line shadow hover:bg-card text-ink hover:text-clay text-xl font-bold z-10">&rsaquo;</button>

            </div>
        </div>
    </div>

</div>

<!-- Common Script for sweetalert deletions and renaming -->
<script>
    function confirmDelete(button, text) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: text || 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c2683f',
            cancelButtonColor: '#9a9183',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }

    function renameAttachment(fileId, currentName) {
        Swal.fire({
            title: 'Renommer le fichier',
            input: 'text',
            inputValue: currentName,
            inputPlaceholder: 'Saisir le nouveau nom sans extension...',
            showCancelButton: true,
            confirmButtonColor: '#3d5a3d',
            cancelButtonColor: '#9a9183',
            confirmButtonText: 'Renommer',
            cancelButtonText: 'Annuler',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'Le nom ne peut pas être vide !';
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                let form = document.createElement('form');
                form.action = '/admin/attachments/' + fileId + '/rename';
                form.method = 'POST';
                
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'name';
                input.value = result.value.trim();
                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
