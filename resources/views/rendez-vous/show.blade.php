@extends('layouts.dashboard')

@section('title', 'Détails du Rendez-vous')
@section('subtitle', 'Informations complètes du rendez-vous')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Détails du Rendez-vous</h2>
            <p class="text-gray-600">Numéro de suivi: {{ $rendezVous->numero_suivi }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('rendez-vous.edit', $rendezVous) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            <a href="{{ route('dossiers.create', ['rendez_vous_id' => $rendezVous->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-folder-plus mr-2"></i>
                Créer Dossier
            </a>
            <a href="{{ route('rendez-vous.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations du rendez-vous -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                    Informations du Rendez-vous
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Date</label>
                        <p class="text-gray-900 font-medium">{{ $rendezVous->date_rendez_vous->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Heure</label>
                        <p class="text-gray-900 font-medium">{{ $rendezVous->tranche_horaire }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Statut</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($rendezVous->statut === 'confirme') bg-green-100 text-green-800
                            @elseif($rendezVous->statut === 'annule') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ $rendezVous->statut_formate }}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Numéro de suivi</label>
                        <p class="text-gray-900 font-mono">{{ $rendezVous->numero_suivi }}</p>
                    </div>
                </div>
                
                @if($rendezVous->notes)
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">Notes</label>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg mt-1">{{ $rendezVous->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Informations du client -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user mr-2 text-green-600"></i>
                    Informations Client
                </h3>
                @if($rendezVous->client)
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nom complet</label>
                        <p class="text-gray-900 font-medium">{{ $rendezVous->client->nom_complet }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900">{{ $rendezVous->client->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Téléphone</label>
                        <p class="text-gray-900">{{ $rendezVous->client->telephone }}</p>
                    </div>
                    @if($rendezVous->client->date_naissance)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Date de naissance</label>
                        <p class="text-gray-900">{{ $rendezVous->client->date_naissance->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-gray-500 italic">Aucune information client disponible</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Informations du service -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-cogs mr-2 text-purple-600"></i>
                Service
            </h3>
            @if($rendezVous->service)
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nom du service</label>
                    <p class="text-gray-900 font-medium">{{ $rendezVous->service->nom }}</p>
                </div>
                @if($rendezVous->service->description)
                <div>
                    <label class="text-sm font-medium text-gray-500">Description</label>
                    <p class="text-gray-900">{{ $rendezVous->service->description }}</p>
                </div>
                @endif
            </div>
            @else
            <p class="text-gray-500 italic">Aucune information de service disponible</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-star mr-2 text-yellow-600"></i>
                Formule
            </h3>
            @if($rendezVous->formule)
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nom de la formule</label>
                    <p class="text-gray-900 font-medium">{{ $rendezVous->formule->nom }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Prix</label>
                    <p class="text-gray-900 font-bold text-lg">{{ number_format($rendezVous->formule->prix, 0, ',', ' ') }} FCFA</p>
                </div>
                @if($rendezVous->formule->description)
                <div>
                    <label class="text-sm font-medium text-gray-500">Description</label>
                    <p class="text-gray-900">{{ $rendezVous->formule->description }}</p>
                </div>
                @endif
            </div>
            @else
            <p class="text-gray-500 italic">Aucune information de formule disponible</p>
            @endif
        </div>
    </div>

    <!-- Informations du centre -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-building mr-2 text-indigo-600"></i>
            Centre
        </h3>
        @if($rendezVous->centre)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-500">Nom du centre</label>
                <p class="text-gray-900 font-medium">{{ $rendezVous->centre->nom }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Ville</label>
                <p class="text-gray-900">{{ $rendezVous->centre->ville->nom ?? 'Non renseigné' }}</p>
            </div>
            @if($rendezVous->centre->adresse)
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-500">Adresse</label>
                <p class="text-gray-900">{{ $rendezVous->centre->adresse }}</p>
            </div>
            @endif
        </div>
        @else
        <p class="text-gray-500 italic">Aucune information de centre disponible</p>
        @endif
    </div>

    <!-- Dossiers associés -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-folder mr-2 text-orange-600"></i>
                Dossiers Associés
            </h3>
            <a href="{{ route('dossiers.create', ['rendez_vous_id' => $rendezVous->id]) }}" class="bg-orange-600 text-white px-3 py-2 rounded-lg hover:bg-orange-700 text-sm">
                <i class="fas fa-plus mr-1"></i>
                Nouveau Dossier
            </a>
        </div>
        
        @if($rendezVous->dossiers && $rendezVous->dossiers->count() > 0)
        <div class="space-y-3">
            @foreach($rendezVous->dossiers as $dossier)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $dossier->nom ?? 'Dossier #' . $dossier->id }}</h4>
                        <p class="text-sm text-gray-500">{{ $dossier->created_at->format('d/m/Y à H:i') }}</p>
                        @if($dossier->description)
                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($dossier->description, 100) }}</p>
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('dossiers.show', $dossier) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('dossiers.edit', $dossier) }}" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Aucun dossier associé à ce rendez-vous</p>
            <a href="{{ route('dossiers.create', ['rendez_vous_id' => $rendezVous->id]) }}" class="text-orange-600 hover:text-orange-800 font-medium">
                Créer le premier dossier
            </a>
        </div>
        @endif
    </div>
</div>
@endsection