@extends('layouts.dashboard')

@section('title', 'Traitement Retrait - Ticket ' . $ticket->numero)
@section('subtitle', 'Enregistrement de l\'identité et du récépissé')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('retraits.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-mayelia-600 p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Traitement du retrait</h3>
                    <p class="text-mayelia-100 italic">{{ optional($ticket->retraitCarte)->type_piece ?? 'Type de carte non spécifié' }}</p>
                </div>
<div class="text-right">
                    <span class="text-4xl font-black">{{ $ticket->numero }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('retraits.store', $ticket) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <!-- Étape 1 : Identification du client -->
            <div class="mb-10">
                <div class="flex items-center mb-6 border-b pb-2">
                    <span class="w-8 h-8 bg-mayelia-100 text-mayelia-600 rounded-full flex items-center justify-center font-bold mr-3">1</span>
                    <h4 class="font-bold text-gray-800 text-lg">Identification du client</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher un client existant (Optionnel)</label>
                        <div class="relative">
                            <input type="text" id="client_search" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-mayelia-500 focus:outline-none" placeholder="Nom, Prénom ou Téléphone...">
                            <div class="absolute left-3 top-3.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                            <div id="search_results" class="absolute z-10 w-full mt-1 bg-white shadow-xl rounded-xl border hidden max-h-60 overflow-y-auto">
                                <!-- Résultats injectés ici -->
                            </div>
                        </div>
                        <input type="hidden" name="client_id" id="client_id">
                    </div>

                    <div class="client-info">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="client_nom" value="{{ old('nom') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-mayelia-500 focus:outline-none" required>
                    </div>

                    <div class="client-info">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" id="client_prenom" value="{{ old('prenom') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-mayelia-500 focus:outline-none" required>
                    </div>

                    <div class="client-info">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone <span class="text-red-500">*</span></label>
                        <input type="text" name="telephone" id="client_telephone" value="{{ old('telephone') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-mayelia-500 focus:outline-none" required>
                    </div>
                </div>
            </div>

            <!-- Étape 2 : Détails du récépissé -->
            <div class="mb-10">
                <div class="flex items-center mb-6 border-b pb-2">
                    <span class="w-8 h-8 bg-mayelia-100 text-mayelia-600 rounded-full flex items-center justify-center font-bold mr-3">2</span>
                    <h4 class="font-bold text-gray-800 text-lg">Détails du récépissé</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de pièce <span class="text-red-500">*</span></label>
                        <select name="type_piece" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-mayelia-500 focus:outline-none bg-white">
                            <option value="CNI" {{ (optional($ticket->retraitCarte)->type_piece ?? '') === 'CNI' ? 'selected' : '' }}>CNI</option>
                            <option value="Résident" {{ (optional($ticket->retraitCarte)->type_piece ?? '') === 'Résident' ? 'selected' : '' }}>Carte de Résident</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Numéro du récépissé <span class="text-red-500">*</span></label>
                        <input type="text" name="numero_recepisse" value="{{ old('numero_recepisse') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-mayelia-500 focus:outline-none" required placeholder="Ex: REC-12345678">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scan du récépissé (Photo)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-mayelia-400 transition-colors bg-gray-50 cursor-pointer" onclick="document.getElementById('scan_recepisse').click()">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-camera text-4xl text-gray-400 mb-2"></i>
                                <div class="flex text-sm text-gray-600">
                                    <span class="relative cursor-pointer rounded-md font-medium text-mayelia-600 hover:text-mayelia-500">
                                        Prendre une photo ou uploader
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG jusqu'à 5MB</p>
                                <p id="filename_display" class="text-sm text-mayelia-600 font-bold mt-2 hidden"></p>
                            </div>
                            <input id="scan_recepisse" name="scan_recepisse" type="file" class="sr-only" onchange="displayFileName(this)">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t flex justify-between">
                <button type="reset" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition-colors">
                    Réinitialiser
                </button>
                <button type="submit" class="px-10 py-3 bg-mayelia-600 text-white font-black rounded-xl shadow-lg hover:bg-mayelia-700 transition-all transform hover:scale-105 active:scale-95">
                    Enregistrer et continuer <i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function displayFileName(input) {
        const display = document.getElementById('filename_display');
        if (input.files && input.files[0]) {
            display.innerText = "Fichier sélectionné : " + input.files[0].name;
            display.classList.remove('hidden');
        } else {
            display.classList.add('hidden');
        }
    }

    // Recherche client temps réel
    const searchInput = document.getElementById('client_search');
    const resultsDiv = document.getElementById('search_results');
    const idInput = document.getElementById('client_id');

    searchInput.addEventListener('input', function() {
        if (this.value.length < 3) {
            resultsDiv.classList.add('hidden');
            return;
        }

        fetch(`{{ route('api.clients.search') }}?q=${encodeURIComponent(this.value)}`)
            .then(res => res.json())
            .then(data => {
                resultsDiv.innerHTML = '';
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="p-4 text-sm text-gray-500">Aucun client trouvé.</div>';
                } else {
                    data.forEach(client => {
                        const div = document.createElement('div');
                        div.className = 'p-4 hover:bg-mayelia-50 cursor-pointer border-b last:border-0 transition-colors';
                        div.innerHTML = `
                            <div class="font-bold text-gray-800">${client.nom} ${client.prenom}</div>
                            <div class="text-xs text-gray-500">${client.telephone} | ${client.numero_piece_identite || 'Pas de pièce'}</div>
                        `;
                        div.onclick = () => selectClient(client);
                        resultsDiv.appendChild(div);
                    });
                }
                resultsDiv.classList.remove('hidden');
            });
    });

    function selectClient(client) {
        idInput.value = client.id;
        document.getElementById('client_nom').value = client.nom;
        document.getElementById('client_prenom').value = client.prenom;
        document.getElementById('client_telephone').value = client.telephone;
        resultsDiv.classList.add('hidden');
        searchInput.value = `${client.nom} ${client.prenom}`;
        
        // Style feedback
        const infos = document.querySelectorAll('.client-info input');
        infos.forEach(i => {
            i.classList.add('bg-green-50', 'border-green-300');
            i.readOnly = true;
        });
    }

    // Fermer les résultats si clic ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.add('hidden');
        }
    });
</script>
@endsection
