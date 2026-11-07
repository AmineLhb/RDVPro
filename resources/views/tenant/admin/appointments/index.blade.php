@extends('tenant.layout')

@section('content')
<div x-data="calendarManager($el)" 
     data-customers="{{ json_encode($customers) }}"
     data-services="{{ json_encode($services) }}"
     data-resources="{{ json_encode($resources) }}"
     data-old-form="{{ json_encode([
         'customer_id' => old('customer_id', ''),
         'service_id' => old('service_id', ''),
         'resource_id' => old('resource_id', ''),
         'starts_at' => old('starts_at') ? str_replace('T', ' ', old('starts_at')) : '',
         'ends_at' => old('ends_at') ? str_replace('T', ' ', old('ends_at')) : '',
         'price' => old('price', ''),
         'status' => old('status', 'confirmed'),
         'note' => old('note', '')
     ]) }}"
     data-old-edit-form="{{ json_encode([
         'customer_id' => old('customer_id', ''),
         'service_id' => old('service_id', ''),
         'resource_id' => old('resource_id', ''),
         'starts_at' => old('starts_at') ? str_replace('T', ' ', old('starts_at')) : '',
         'ends_at' => old('ends_at') ? str_replace('T', ' ', old('ends_at')) : '',
         'price' => old('price', ''),
         'status' => old('status', 'confirmed'),
         'note' => old('note', '')
     ]) }}"
     data-errors="{{ $errors->any() ? 'true' : 'false' }}"
     data-old-id="{{ old('id') ?? '' }}"
     data-old-starts-at="{{ old('starts_at') ? str_replace('T', ' ', old('starts_at')) : '' }}"
     data-old-ends-at="{{ old('ends_at') ? str_replace('T', ' ', old('ends_at')) : '' }}"
     data-events-url="{{ route('tenant.appointments.index') }}"
     class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Planning & RDV</h1>
            <p class="text-muted text-sm font-outfit mt-1">Gérez vos rendez-vous de manière interactive en faisant glisser les créneaux ou en cliquant sur le calendrier.</p>
        </div>
        <button @click="openCreateModal()" class="px-5 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition-all duration-200 active:scale-95 shadow-sm hover:shadow text-sm font-outfit">
            + Nouveau rendez-vous
        </button>
    </div>

    <!-- Error/Success alerts -->
    <div x-show="errorMessage" x-cloak class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-xl flex items-center justify-between shadow-sm">
        <span x-text="errorMessage"></span>
        <button @click="errorMessage = ''" class="font-bold text-red-800 hover:text-red-900 ml-2">×</button>
    </div>

    <!-- Calendar Container -->
    <div class="bg-card border border-line rounded-2xl shadow-sm p-6">
        <div id="calendar"></div>
    </div>

    <!-- Create Appointment Drawer -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 overflow-hidden z-50" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-[#1a1410]/50 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-lg" 
                     x-show="showCreateModal" 
                     x-transition:enter="transform transition ease-in-out duration-300" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-300" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full">
                    <div class="h-full flex flex-col bg-card border-l border-line p-6 shadow-2xl overflow-y-auto space-y-6" @click.stop>
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <h3 class="text-xl font-bold text-ink font-serif">Planifier un rendez-vous</h3>
                    <button @click="showCreateModal = false" class="text-muted hover:text-ink text-2xl font-bold">&times;</button>
                </div>

                <form action="{{ route('tenant.appointments.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Client -->
                    <div>
                        <label for="create_customer" class="block text-sm font-semibold mb-2">Client</label>
                        <select name="customer_id" id="create_customer" required x-model="formData.customer_id" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            <option value="">-- Choisir un client --</option>
                            <template x-for="c in customers" :key="c.id">
                                <option :value="c.id" x-text="c.name + ' (' + c.phone + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Service -->
                        <div>
                            <label for="create_service" class="block text-sm font-semibold mb-2">Prestation / Service</label>
                            <select name="service_id" id="create_service" required x-model="formData.service_id" @change="onServiceChange('create')" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                                <option value="">-- Choisir un service --</option>
                                <template x-for="s in services" :key="s.id">
                                    <option :value="s.id" x-text="s.name + ' (' + s.duration_minutes + ' min)'"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Staff -->
                        <div>
                            <label for="create_resource" class="block text-sm font-semibold mb-2">Personnel</label>
                            <select name="resource_id" id="create_resource" required x-model="formData.resource_id" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                                <option value="">-- Choisir le personnel --</option>
                                <template x-for="r in resources" :key="r.id">
                                    <option :value="r.id" x-text="r.name + ' (' + r.role + ')'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Date Start -->
                        <div>
                            <label for="create_starts_at" class="block text-sm font-semibold mb-2">Date & Heure Début</label>
                            <input type="text" name="starts_at" id="create_starts_at" x-ref="createStarts" required x-model="formData.starts_at" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm cursor-pointer" placeholder="Choisir date & heure">
                        </div>

                        <!-- Date End -->
                        <div>
                            <label for="create_ends_at" class="block text-sm font-semibold mb-2">Date & Heure Fin</label>
                            <input type="text" name="ends_at" id="create_ends_at" x-ref="createEnds" required x-model="formData.ends_at" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm cursor-pointer" placeholder="Choisir date & heure">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Price -->
                        <div>
                            <label for="create_price" class="block text-sm font-semibold mb-2">Prix (MAD)</label>
                            <input type="number" step="0.01" name="price" id="create_price" required x-model="formData.price" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="create_status" class="block text-sm font-semibold mb-2">Statut</label>
                            <select name="status" id="create_status" required x-model="formData.status" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                                <option value="pending">En attente (Pending)</option>
                                <option value="confirmed">Confirmé (Confirmed)</option>
                                <option value="completed">Terminé (Completed)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Note -->
                    <div>
                        <label for="create_note" class="block text-sm font-semibold mb-2">Notes</label>
                        <textarea name="note" id="create_note" rows="2" x-model="formData.note" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm" placeholder="Remarques spécifiques sur le client ou le RDV..."></textarea>
                    </div>

                    <div class="pt-4 border-t border-line flex justify-end space-x-3">
                        <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 rounded-xl border border-line bg-paper hover:bg-line transition text-sm font-semibold">
                            Annuler
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition shadow-sm text-sm">
                            Enregistrer
                        </button>
                    </div>
                </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit/View Appointment Drawer -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 overflow-hidden z-50" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-[#1a1410]/50 backdrop-blur-sm transition-opacity" @click="showEditModal = false"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-lg" 
                     x-show="showEditModal" 
                     x-transition:enter="transform transition ease-in-out duration-300" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-300" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full">
                    <div class="h-full flex flex-col bg-card border-l border-line p-6 shadow-2xl overflow-y-auto space-y-6" @click.stop>
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <h3 class="text-xl font-bold text-ink font-serif">Détails du rendez-vous</h3>
                    <button @click="showEditModal = false" class="text-muted hover:text-ink text-2xl font-bold">&times;</button>
                </div>

                <form :action="'{{ route('tenant.appointments.update', '') }}/' + selectedAppointmentId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" :value="selectedAppointmentId">
                    
                    <!-- Client -->
                    <div>
                        <label for="edit_customer" class="block text-sm font-semibold mb-2">Client</label>
                        <select name="customer_id" id="edit_customer" required x-model="editFormData.customer_id" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            <template x-for="c in customers" :key="c.id">
                                <option :value="c.id" x-text="c.name + ' (' + c.phone + ')'" :selected="c.id == editFormData.customer_id"></option>
                            </template>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Service -->
                        <div>
                            <label for="edit_service" class="block text-sm font-semibold mb-2">Prestation</label>
                            <select name="service_id" id="edit_service" required x-model="editFormData.service_id" @change="onServiceChange('edit')" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                                <template x-for="s in services" :key="s.id">
                                    <option :value="s.id" x-text="s.name" :selected="s.id == editFormData.service_id"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Staff -->
                        <div>
                            <label for="edit_resource" class="block text-sm font-semibold mb-2">Personnel</label>
                            <select name="resource_id" id="edit_resource" required x-model="editFormData.resource_id" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                                <template x-for="r in resources" :key="r.id">
                                    <option :value="r.id" x-text="r.name" :selected="r.id == editFormData.resource_id"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Date Start -->
                        <div>
                            <label for="edit_starts_at" class="block text-sm font-semibold mb-2">Date & Heure Début</label>
                            <input type="text" name="starts_at" id="edit_starts_at" x-ref="editStarts" required x-model="editFormData.starts_at" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm cursor-pointer" placeholder="Choisir date & heure">
                        </div>

                        <!-- Date End -->
                        <div>
                            <label for="edit_ends_at" class="block text-sm font-semibold mb-2">Date & Heure Fin</label>
                            <input type="text" name="ends_at" id="edit_ends_at" x-ref="editEnds" required x-model="editFormData.ends_at" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm cursor-pointer" placeholder="Choisir date & heure">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Price -->
                        <div>
                            <label for="edit_price" class="block text-sm font-semibold mb-2">Prix (MAD)</label>
                            <input type="number" step="0.01" name="price" id="edit_price" required x-model="editFormData.price" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="edit_status" class="block text-sm font-semibold mb-2">Statut</label>
                            <select name="status" id="edit_status" required x-model="editFormData.status" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                                <option value="pending" :selected="editFormData.status == 'pending'">En attente (Pending)</option>
                                <option value="confirmed" :selected="editFormData.status == 'confirmed'">Confirmé (Confirmed)</option>
                                <option value="completed" :selected="editFormData.status == 'completed'">Terminé (Completed)</option>
                                <option value="cancelled" :selected="editFormData.status == 'cancelled'">Annulé (Cancelled)</option>
                                <option value="no_show" :selected="editFormData.status == 'no_show'">No Show</option>
                            </select>
                        </div>
                    </div>

                    <!-- Note -->
                    <div>
                        <label for="edit_note" class="block text-sm font-semibold mb-2">Notes</label>
                        <textarea name="note" id="edit_note" rows="2" x-model="editFormData.note" class="w-full px-4 py-2.5 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm"></textarea>
                    </div>

                    <div class="pt-4 border-t border-line flex justify-between items-center">
                        <!-- Delete Button (triggers separate form) -->
                        <button type="button" @click="confirmDelete()" class="px-5 py-2.5 rounded-xl bg-red-800 text-paper font-semibold hover:bg-red-900 transition text-sm">
                            Supprimer
                        </button>
                        
                        <div class="flex space-x-3">
                            <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl border border-line bg-paper hover:bg-line transition text-sm font-semibold">
                                Annuler
                            </button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition shadow-sm text-sm">
                                Modifier
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Hidden Delete Form -->
                <form :action="'{{ route('tenant.appointments.destroy', '') }}/' + selectedAppointmentId" method="POST" id="deleteForm" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Local assets for FullCalendar and Custom Calendar configuration -->
<link rel="stylesheet" href="{{ global_asset('css/calendar.css') }}">
<script src="{{ global_asset('vendor/fullcalendar/index.global.min.js') }}"></script>
<script src="{{ global_asset('vendor/fullcalendar/fr.global.min.js') }}"></script>
<script src="{{ global_asset('js/calendar.js') }}"></script>
@endsection
