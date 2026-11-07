@extends('tenant.layout')

@section('content')
<div class="space-y-8" x-data="templateManager($el)" data-templates="{{ json_encode($templates) }}" data-preview-url="{{ route('tenant.templates.preview') }}">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold tracking-tight font-fraunces text-ink">Modèles de messages</h1>
        <p class="text-muted text-sm font-outfit mt-1">Personnalisez les messages automatiques envoyés à vos clients par WhatsApp et E-mail.</p>
    </div>

    <!-- Double panel layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Editor panel -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Templates list -->
            <div class="bg-card border border-line rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4 text-clay border-b border-line pb-2">Sélectionnez le modèle</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($templates as $tpl)
                        <button type="button" @click="selectTemplate({{ json_encode($tpl) }})" class="p-4 border text-left rounded-xl hover:border-olive hover:bg-paper/30 transition flex flex-col justify-between" :class="activeTemplateId == {{ $tpl->id }} ? 'border-olive bg-paper' : 'border-line bg-card'">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider 
                                    @if($tpl->channel === 'whatsapp') bg-[#25d366]/10 text-emerald-800 border border-[#25d366]/20
                                    @else bg-purple-100 text-purple-800 border border-purple-200
                                    @endif">
                                    {{ $tpl->channel }}
                                </span>
                                <h4 class="font-bold text-ink mt-2 capitalize">{{ str_replace('_', ' ', $tpl->trigger) }}</h4>
                            </div>
                            <span class="text-xs text-muted mt-2 truncate">{{ $tpl->body }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Editor Form -->
            <template x-if="activeTemplate">
                <div class="bg-card border border-line rounded-2xl shadow-sm p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-clay capitalize" x-text="activeTemplate.trigger.replace(/_/g, ' ') + ' (' + activeTemplate.channel + ')'">Éditeur</h3>
                    <form :action="'{{ route('tenant.templates.update', '') }}/' + activeTemplate.id" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <template x-if="activeTemplate.channel === 'email'">
                            <div>
                                <label for="subject" class="block text-sm font-semibold mb-2">Objet du mail</label>
                                <input type="text" name="subject" id="subject" x-model="activeTemplate.subject" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm">
                            </div>
                        </template>

                        <div>
                            <label for="body" class="block text-sm font-semibold mb-2">Corps du message</label>
                            <textarea name="body" id="body" rows="6" x-model="activeTemplate.body" @input="debouncedRender()" class="w-full px-4 py-3 border border-line rounded-xl bg-paper focus:outline-none focus:ring-2 focus:ring-olive text-sm font-mono"></textarea>
                            <div class="mt-2 text-xs text-muted flex flex-wrap gap-2">
                                <span class="font-bold">Variables disponibles :</span>
                                <code class="px-1.5 py-0.5 rounded bg-paper border border-line">{name}</code>
                                <code class="px-1.5 py-0.5 rounded bg-paper border border-line">{business}</code>
                                <code class="px-1.5 py-0.5 rounded bg-paper border border-line">{date}</code>
                                <code class="px-1.5 py-0.5 rounded bg-paper border border-line">{time}</code>
                                <code class="px-1.5 py-0.5 rounded bg-paper border border-line">{service}</code>
                                <code class="px-1.5 py-0.5 rounded bg-paper border border-line">{staff}</code>
                                <code class="px-1.5 py-0.5 rounded bg-paper border border-line">{price}</code>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 py-2">
                            <input type="checkbox" name="active" value="1" :checked="activeTemplate.active" class="h-4 w-4 text-olive focus:ring-olive border-line rounded">
                            <span class="text-sm font-semibold">Modèle actif</span>
                        </div>

                        <div class="pt-4 border-t border-line flex justify-end">
                            <button type="submit" class="px-6 py-3 rounded-xl bg-clay text-paper font-semibold hover:bg-[#b05830] transition shadow-sm text-sm">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>

        <!-- Phone Preview Container -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-ink pl-2">Aperçu en direct</h3>
            
            <div class="relative mx-auto w-[310px] h-[610px] bg-black rounded-[40px] border-[10px] border-black shadow-2xl overflow-hidden">
                <!-- Screen content -->
                <div class="absolute inset-0 bg-stone-100 flex flex-col pt-6">
                    <!-- WhatsApp Simulator Header -->
                    <div class="bg-emerald-800 text-white p-3 flex items-center space-x-2 flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs">P</div>
                        <div>
                            <p class="font-bold text-xs">{{ tenant('name') }}</p>
                            <p class="text-[10px] opacity-85">Compte Entreprise</p>
                        </div>
                    </div>
                    <!-- Message list simulated -->
                    <div class="flex-1 p-3 overflow-y-auto space-y-3 bg-[#e5ddd5]" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover;">
                        <!-- Speech bubble -->
                        <div class="bg-white p-3 rounded-xl shadow-sm text-xs max-w-[85%] float-left relative">
                            <p class="text-ink whitespace-pre-line text-[11px]" x-text="renderedPreview">Rendu en cours...</p>
                            <span class="text-[9px] text-muted block text-right mt-1.5">09:12 ✓✓</span>
                        </div>
                    </div>
                    <!-- Input bar -->
                    <div class="bg-stone-200 p-2 flex items-center space-x-2 flex-shrink-0 border-t border-line">
                        <div class="flex-1 bg-white rounded-full px-3 py-1.5 text-[10px] text-muted">Écrire un message...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ global_asset('js/templates.js') }}"></script>
@endsection
