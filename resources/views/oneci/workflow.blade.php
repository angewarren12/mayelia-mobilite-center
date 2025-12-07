@extends('layouts.oneci')

@section('title', 'Workflow du Dossier')
@section('subtitle', 'Détails des étapes passées à Mayelia')

@section('content')
<div class="space-y-6">
    <!-- En-tête du dossier -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-mayelia-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder-open text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Dossier #{{ $dossierOuvert->id }}</h1>
                        <p class="text-gray-600">Code-barres: <span class="font-mono">{{ $item->code_barre }}</span></p>
                        <p class="text-gray-600">Ouvert le {{ $dossierOuvert->date_ouverture->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-user text-mayelia-600"></i>
                            <span class="font-semibold text-gray-700">Client</span>
                        </div>
                        <p class="text-gray-900">{{ $dossierOuvert->rendezVous->client->nom }} {{ $dossierOuvert->rendezVous->client->prenom }}</p>
                        <p class="text-sm text-gray-600">{{ $dossierOuvert->rendezVous->client->email }}</p>
                        @if($dossierOuvert->rendezVous->client->telephone)
                        <p class="text-sm text-gray-600">{{ $dossierOuvert->rendezVous->client->telephone }}</p>
                        @endif
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-cogs text-green-600"></i>
                            <span class="font-semibold text-gray-700">Service</span>
                        </div>
                        <p class="text-gray-900">{{ $dossierOuvert->rendezVous->service->nom }}</p>
                        <p class="text-sm text-gray-600">{{ $dossierOuvert->rendezVous->formule->nom }}</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-map-marker-alt text-purple-600"></i>
                            <span class="font-semibold text-gray-700">Centre Mayelia</span>
                        </div>
                        <p class="text-gray-900">{{ $dossierOuvert->rendezVous->centre->nom }}</p>
                        <p class="text-sm text-gray-600">{{ $dossierOuvert->rendezVous->centre->ville->nom ?? '' }}</p>
                    </div>
                </div>

                @if($dossierOuvert->rendezVous->date_rendez_vous)
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-calendar text-purple-600"></i>
                        <span class="font-semibold text-gray-700">Rendez-vous</span>
                    </div>
                    <p class="text-gray-900">{{ $dossierOuvert->rendezVous->date_rendez_vous->format('d/m/Y') }}</p>
                    <p class="text-sm text-gray-600">{{ $dossierOuvert->rendezVous->tranche_horaire }}</p>
                </div>
                @endif
            </div>
            
            <div class="ml-6 text-right">
                <div class="mb-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        @if($dossierOuvert->statut === 'ouvert') bg-mayelia-100 text-mayelia-800
                        @elseif($dossierOuvert->statut === 'en_cours') bg-yellow-100 text-yellow-800
                        @elseif($dossierOuvert->statut === 'finalise') bg-green-100 text-green-800
                        @endif">
                        <i class="fas fa-circle mr-2 text-xs"></i>
                        {{ $dossierOuvert->statut_formate }}
                    </span>
                </div>
                
                <div class="bg-white border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progression</span>
                        <span class="text-sm font-bold text-mayelia-600">{{ $dossierOuvert->progression }}%</span>
                    </div>
                    <div class="w-48 bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-300" 
                             style="width: {{ $dossierOuvert->progression }}%"></div>
                    </div>
                </div>

                @if($item->statut)
                <div class="mt-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        @if($item->statut === 'en_attente') bg-yellow-100 text-yellow-800
                        @elseif($item->statut === 'recu') bg-mayelia-100 text-mayelia-800
                        @elseif($item->statut === 'traite') bg-indigo-100 text-indigo-800
                        @elseif($item->statut === 'carte_prete') bg-green-100 text-green-800
                        @elseif($item->statut === 'recupere') bg-gray-100 text-gray-800
                        @endif">
                        <i class="fas fa-circle mr-2 text-xs"></i>
                        Statut ONECI: {{ $item->statut_formate }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Étapes du workflow -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Étape 1: Fiche de pré-enrôlement -->
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-mayelia-500 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-mayelia-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-mayelia-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Fiche de pré-enrôlement</h3>
                        <p class="text-sm text-gray-600">Étape 1/4</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs rounded-full font-medium
                    @if($dossierOuvert->fiche_pre_enrolement_verifiee) bg-green-100 text-green-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    @if($dossierOuvert->fiche_pre_enrolement_verifiee) 
                        <i class="fas fa-check mr-1"></i>Vérifiée 
                    @else 
                        <i class="fas fa-clock mr-1"></i>Non vérifiée 
                    @endif
                </span>
            </div>
            
            @if($dossierOuvert->fiche_pre_enrolement_verifiee)
                <div class="bg-green-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-900 mb-1">
                                <i class="fas fa-check-circle mr-2"></i>
                                Fiche de pré-enrôlement vérifiée
                            </p>
                            @if($dossierOuvert->agent)
                            <p class="text-xs text-green-700">
                                Vérifiée par: {{ $dossierOuvert->agent->nom_complet }}
                            </p>
                            @endif
                        </div>
                        @if($dossierOuvert->rendezVous->client->fiche_pre_enrolement_url ?? null)
                        <a href="{{ $dossierOuvert->rendezVous->client->fiche_pre_enrolement_url }}" 
                           target="_blank"
                           class="px-4 py-2 bg-mayelia-600 text-white rounded-lg hover:bg-mayelia-700 text-sm">
                            <i class="fas fa-download mr-2"></i>
                            Voir le fichier
                        </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 text-sm">
                        <i class="fas fa-info-circle text-mayelia-500 mr-2"></i>
                        Vérification de la fiche de pré-enrôlement remplie par le client en ligne.
                    </p>
                </div>
            @endif
        </div>

        <!-- Étape 2: Documents requis -->
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-green-500 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-folder-open text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Documents requis</h3>
                        <p class="text-sm text-gray-600">Étape 2/4</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs rounded-full font-medium
                    @if($dossierOuvert->documents_verifies) bg-green-100 text-green-800
                    @elseif($dossierOuvert->documents_manquants) bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    @if($dossierOuvert->documents_verifies) 
                        <i class="fas fa-check mr-1"></i>Vérifiés
                    @elseif($dossierOuvert->documents_manquants) 
                        <i class="fas fa-exclamation-triangle mr-1"></i>Manquants
                    @else 
                        <i class="fas fa-clock mr-1"></i>En attente
                    @endif
                </span>
            </div>
            
            @php
                $documentVerifications = $dossierOuvert->documentVerifications;
            @endphp
            
            @if($documentVerifications->count() > 0)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">
                        <i class="fas fa-list-check mr-2"></i>Documents vérifiés par l'agent Mayelia
                    </h4>
                    <div class="space-y-2">
                        @foreach($documentVerifications as $docVerif)
                            <div class="flex items-center justify-between p-3 bg-white rounded border {{ $docVerif->present ? 'border-green-200' : 'border-red-200' }}">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-file {{ $docVerif->present ? 'text-green-600' : 'text-red-600' }}"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $docVerif->documentRequis->nom }}</span>
                                    </div>
                                    @if($docVerif->commentaire)
                                        <p class="text-xs text-gray-500 mt-1 ml-6">{{ $docVerif->commentaire }}</p>
                                    @endif
                                    @if($docVerif->verifiePar)
                                        <p class="text-xs text-gray-400 mt-1 ml-6">
                                            Vérifié par: {{ $docVerif->verifiePar->nom_complet }} 
                                            @if($docVerif->date_verification)
                                                le {{ $docVerif->date_verification->format('d/m/Y H:i') }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    @if($docVerif->present)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Présent
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Manquant
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 text-sm">
                        <i class="fas fa-info-circle text-green-500 mr-2"></i>
                        Aucun document vérifié pour le moment. Documents requis pour ce service : {{ $documentsRequis->count() }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Étape 3: Informations client -->
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-purple-500 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Informations client</h3>
                        <p class="text-sm text-gray-600">Étape 3/4</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs rounded-full font-medium
                    @if($dossierOuvert->informations_client_verifiees) bg-green-100 text-green-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    @if($dossierOuvert->informations_client_verifiees) 
                        <i class="fas fa-check mr-1"></i>Complétées
                    @else 
                        <i class="fas fa-clock mr-1"></i>À compléter
                    @endif
                </span>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-user text-purple-500 w-4"></i>
                            <div>
                                <span class="text-xs text-gray-500">Nom complet</span>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $dossierOuvert->rendezVous->client->nom }} {{ $dossierOuvert->rendezVous->client->prenom }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-purple-500 w-4"></i>
                            <div>
                                <span class="text-xs text-gray-500">Email</span>
                                <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->rendezVous->client->email }}</p>
                            </div>
                        </div>
                        
                        @if($dossierOuvert->rendezVous->client->telephone)
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-phone text-purple-500 w-4"></i>
                            <div>
                                <span class="text-xs text-gray-500">Téléphone</span>
                                <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->rendezVous->client->telephone }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="space-y-3">
                        @if($dossierOuvert->rendezVous->client->date_naissance)
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-calendar text-purple-500 w-4"></i>
                            <div>
                                <span class="text-xs text-gray-500">Date de naissance</span>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($dossierOuvert->rendezVous->client->date_naissance)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        @if($dossierOuvert->rendezVous->client->numero_piece_identite)
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-id-card text-purple-500 w-4"></i>
                            <div>
                                <span class="text-xs text-gray-500">CNI/Passport</span>
                                <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->rendezVous->client->numero_piece_identite }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Étape 4: Vérification du paiement -->
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-orange-500 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-credit-card text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Paiement</h3>
                        <p class="text-sm text-gray-600">Étape 4/4</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs rounded-full font-medium
                    @if($dossierOuvert->paiement_verifie) bg-green-100 text-green-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    @if($dossierOuvert->paiement_verifie) 
                        <i class="fas fa-check mr-1"></i>Vérifié 
                    @else 
                        <i class="fas fa-clock mr-1"></i>Non vérifié 
                    @endif
                </span>
            </div>
            
            @if($dossierOuvert->paiement_verifie && $dossierOuvert->paiementVerification)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-money-bill text-orange-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Montant payé</span>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ number_format($dossierOuvert->paiementVerification->montant_paye, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-credit-card text-orange-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Mode de paiement</span>
                                    <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->paiementVerification->mode_paiement ?? 'Non spécifié' }}</p>
                                </div>
                            </div>
                            
                            @if($dossierOuvert->paiementVerification->reference_paiement)
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-hashtag text-orange-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Référence</span>
                                    <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->paiementVerification->reference_paiement }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-calendar text-orange-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Date de paiement</span>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $dossierOuvert->paiementVerification->date_paiement->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            @if($dossierOuvert->agent)
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-user-check text-orange-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Agent Mayelia</span>
                                    <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->agent->nom_complet }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 text-sm">
                        <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                        Le paiement n'a pas encore été vérifié par l'agent Mayelia.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Informations ONECI -->
    @if($item->transfer)
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-truck mr-2"></i>Informations de transfert
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500 mb-1">Code transfert</div>
                <div class="font-mono font-semibold text-gray-900">{{ $item->transfer->code_transfert }}</div>
            </div>
            @if($item->date_reception)
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500 mb-1">Date réception ONECI</div>
                <div class="font-semibold text-gray-900">{{ $item->date_reception->format('d/m/Y H:i') }}</div>
            </div>
            @endif
            @if($item->date_traitement)
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500 mb-1">Date traitement</div>
                <div class="font-semibold text-gray-900">{{ $item->date_traitement->format('d/m/Y H:i') }}</div>
            </div>
            @endif
            @if($item->date_carte_prete)
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500 mb-1">Date carte prête</div>
                <div class="font-semibold text-green-700">{{ $item->date_carte_prete->format('d/m/Y H:i') }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Notes -->
    @if($dossierOuvert->notes)
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-sticky-note mr-2"></i>Notes
        </h3>
        <p class="text-gray-700 whitespace-pre-wrap">{{ $dossierOuvert->notes }}</p>
    </div>
    @endif

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('oneci.dossiers') }}" class="flex items-center space-x-2 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à la liste</span>
            </a>
        </div>
    </div>
</div>
@endsection

