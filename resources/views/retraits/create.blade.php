@extends('layouts.dashboard')

@section('title', 'Nouveau Retrait')
@section('subtitle', 'Enregistrer une intention de retrait')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('retraits.index') }}" class="text-gray-500 hover:text-mayelia-600 flex items-center font-bold text-sm transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Retour au cahier
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-gray-900 px-10 py-10 text-white relative">
            <div class="relative z-10">
                <h3 class="text-3xl font-black mb-2 uppercase tracking-tighter">Initialiser un retrait</h3>
                <p class="text-gray-400 font-bold flex items-center">
                    <i class="fas fa-info-circle mr-2 text-mayelia-500"></i>
                    @if($ticket)
                        Liaison avec le Ticket QMS #{{ $ticket->numero }}
                    @else
                        Enregistrement autonome (Sans rendez-vous / Sans ticket)
                    @endif
                </p>
            </div>
            <i class="fas fa-id-card-alt absolute right-[-2rem] bottom-[-2rem] text-[10rem] text-white opacity-5"></i>
        </div>

        <form action="{{ route('retraits.store') }}" method="POST" enctype="multipart/form-data" class="p-10">
            @csrf
            
            @if($ticket)
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Colonne Client -->
                <div class="space-y-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3 mb-6">Identité du Bénéficiaire</h4>
                    
                    @if(!$ticket)
                    <div class="mb-6 p-4 bg-mayelia-50 rounded-2xl border border-mayelia-100">
                        <label class="block text-[10px] font-black text-mayelia-800 uppercase mb-2">Recherche Rapide</label>
                        <div class="relative">
                            <input type="text" id="client_search" autocomplete="off" class="w-full pl-10 pr-4 py-3 bg-white border-2 border-mayelia-100 rounded-xl focus:border-mayelia-500 focus:ring-0 transition-all font-bold placeholder-mayelia-200" placeholder="Nom ou Téléphone...">
                            <div class="absolute left-3 top-3.5 text-mayelia-300"><i class="fas fa-search"></i></div>
                            <div id="search_results" class="absolute z-20 w-full mt-2 bg-white shadow-2xl rounded-2xl border border-gray-100 hidden"></div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Nom de famille</label>
                        <input type="text" name="nom" id="client_nom" value="{{ old('nom', $ticket?->client?->nom) }}" required
                               class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-mayelia-500 font-black text-gray-800 uppercase text-lg shadow-inner">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Prénom(s)</label>
                        <input type="text" name="prenom" id="client_prenom" value="{{ old('prenom', $ticket?->client?->prenom) }}" required
                               class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-mayelia-500 font-bold text-gray-700 text-lg shadow-inner">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Téléphone Contact</label>
                        <input type="text" name="telephone" id="client_telephone" value="{{ old('telephone', $ticket?->client?->telephone) }}" required
                               class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-mayelia-500 font-black text-mayelia-600 text-lg shadow-inner">
                    </div>
                </div>

                <!-- Colonne Pièce -->
                <div class="space-y-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3 mb-6">Dossier de Retrait</h4>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Type de carte attendue</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="type_piece" value="CNI" checked class="peer sr-only">
                                <div class="p-4 text-center border-2 border-gray-100 rounded-2xl peer-checked:border-mayelia-500 peer-checked:bg-mayelia-50 transition-all">
                                    <i class="fas fa-id-card mb-2 block text-xl text-gray-300 peer-checked:text-mayelia-600"></i>
                                    <span class="text-xs font-black uppercase text-gray-400 peer-checked:text-mayelia-800">CNI</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type_piece" value="Résident" class="peer sr-only">
                                <div class="p-4 text-center border-2 border-gray-100 rounded-2xl peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all">
                                    <i class="fas fa-id-badge mb-2 block text-xl text-gray-300 peer-checked:text-purple-600"></i>
                                    <span class="text-xs font-black uppercase text-gray-400 peer-checked:text-purple-800">Résident</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Numéro du récépissé</label>
                        <input type="text" name="numero_recepisse" required
                               class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-mayelia-500 font-black text-mayelia-700 text-lg shadow-inner placeholder-gray-300"
                               placeholder="Ex: 24-XXXXXXXX">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Scan du récépissé (Optionnel)</label>
                        <label class="flex flex-col items-center justify-center h-32 px-4 py-6 bg-gray-50 text-gray-400 rounded-2xl border-2 border-dashed border-gray-200 cursor-pointer hover:bg-mayelia-50 hover:border-mayelia-300 transition-all">
                            <i class="fas fa-camera-retro text-2xl mb-2"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest" id="file-name">Photo du reçu</span>
                            <input type="file" name="scan_recepisse" class="hidden" accept="image/*" onchange="document.getElementById('file-name').innerText = this.files[0].name">
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-10 border-t border-gray-100 flex items-center justify-between">
                <div class="text-[10px] text-gray-400 font-bold max-w-xs italic uppercase tracking-tighter">
                    L'enregistrement créera une ligne "En attente" dans le cahier de retraits. La décrémentation du stock se fera lors de la remise physique.
                </div>
                <button type="submit" class="px-12 py-5 bg-mayelia-600 text-white rounded-[1.5rem] font-black uppercase tracking-widest shadow-2xl shadow-mayelia-200 hover:bg-mayelia-700 hover:scale-105 active:scale-95 transition-all flex items-center">
                    Enregistrer l'intention <i class="fas fa-chevron-right ml-4"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    @if(!$ticket)
    const searchInput = document.getElementById('client_search');
    const resultsDiv = document.getElementById('search_results');

    searchInput.addEventListener('input', function() {
        if (this.value.length < 3) { resultsDiv.classList.add('hidden'); return; }
        fetch(`{{ route('api.clients.search') }}?q=${encodeURIComponent(this.value)}`)
            .then(res => res.json())
            .then(data => {
                resultsDiv.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(client => {
                        const div = document.createElement('div');
                        div.className = 'p-4 hover:bg-mayelia-50 cursor-pointer border-b border-gray-50 flex flex-col last:border-0';
                        div.innerHTML = `<span class="font-black text-gray-800 uppercase">${client.nom} ${client.prenom}</span><span class="text-[10px] text-mayelia-600 font-bold">${client.telephone}</span>`;
                        div.onclick = () => {
                            document.getElementById('client_nom').value = client.nom;
                            document.getElementById('client_prenom').value = client.prenom;
                            document.getElementById('client_telephone').value = client.telephone;
                            resultsDiv.classList.add('hidden');
                            searchInput.value = '';
                        };
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.classList.remove('hidden');
                } else { resultsDiv.classList.add('hidden'); }
            });
    });
    document.addEventListener('click', e => { if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) resultsDiv.classList.add('hidden'); });
    @endif
</script>
@endsection
