@extends('layouts.dashboard')

@section('title', 'Nouveau Rendez-vous')
@section('subtitle', 'Créer un nouveau rendez-vous')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Nouveau Rendez-vous</h2>
            <p class="text-gray-600">Créer un nouveau rendez-vous pour un client</p>
        </div>
        <a href="{{ route('rendez-vous.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('rendez-vous.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Client -->
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                    <select id="client_id" name="client_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        <option value="">Sélectionner un client</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->nom_complet }} - {{ $client->email }}
                        </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Centre -->
                <div>
                    <label for="centre_id" class="block text-sm font-medium text-gray-700 mb-2">Centre *</label>
                    <select id="centre_id" name="centre_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        <option value="">Sélectionner un centre</option>
                        @foreach($centres as $centre)
                        <option value="{{ $centre->id }}" {{ old('centre_id') == $centre->id ? 'selected' : '' }}>
                            {{ $centre->nom }} - {{ $centre->ville->nom ?? 'Ville non renseignée' }}
                        </option>
                        @endforeach
                    </select>
                    @error('centre_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service -->
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">Service *</label>
                    <select id="service_id" name="service_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        <option value="">Sélectionner un service</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->nom }}
                        </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Formule -->
                <div>
                    <label for="formule_id" class="block text-sm font-medium text-gray-700 mb-2">Formule *</label>
                    <select id="formule_id" name="formule_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        <option value="">Sélectionner une formule</option>
                        @foreach($formules as $formule)
                        <option value="{{ $formule->id }}" {{ old('formule_id') == $formule->id ? 'selected' : '' }}>
                            {{ $formule->nom }} - {{ number_format($formule->prix, 0, ',', ' ') }} FCFA
                        </option>
                        @endforeach
                    </select>
                    @error('formule_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="date_rendez_vous" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                    <input type="date" id="date_rendez_vous" name="date_rendez_vous" 
                           value="{{ old('date_rendez_vous', date('Y-m-d', strtotime('+1 day'))) }}" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    @error('date_rendez_vous')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tranche horaire -->
                <div>
                    <label for="tranche_horaire" class="block text-sm font-medium text-gray-700 mb-2">Tranche horaire *</label>
                    <input type="text" id="tranche_horaire" name="tranche_horaire" 
                           value="{{ old('tranche_horaire') }}" 
                           placeholder="Ex: 08:00 - 09:00" 
                           required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    @error('tranche_horaire')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">Statut *</label>
                    <select id="statut" name="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        <option value="confirme" {{ old('statut', 'confirme') == 'confirme' ? 'selected' : '' }}>Confirmé</option>
                        <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                        <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                    </select>
                    @error('statut')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500"
                          placeholder="Notes additionnelles sur le rendez-vous...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('rendez-vous.index') }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Créer le rendez-vous
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Mise à jour des formules en fonction du service sélectionné
document.getElementById('service_id').addEventListener('change', function() {
    const serviceId = this.value;
    const formuleSelect = document.getElementById('formule_id');
    
    if (serviceId) {
        // Charger les formules pour ce service
        fetch(`/api/services/${serviceId}/formules`)
            .then(response => response.json())
            .then(data => {
                formuleSelect.innerHTML = '<option value="">Sélectionner une formule</option>';
                data.forEach(formule => {
                    const option = document.createElement('option');
                    option.value = formule.id;
                    option.textContent = `${formule.nom} - ${new Intl.NumberFormat('fr-FR').format(formule.prix)} FCFA`;
                    formuleSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des formules:', error);
            });
    } else {
        formuleSelect.innerHTML = '<option value="">Sélectionner une formule</option>';
    }
});
</script>
@endsection