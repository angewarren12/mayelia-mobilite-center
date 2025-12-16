@extends('layouts.dashboard')

@section('title', 'Gestion du Dossier')
@section('subtitle', 'Workflow de traitement du dossier client')

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
                        <p class="text-gray-600">Ouvert le {{ $dossierOuvert->date_ouverture->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($dossierOuvert->rendezVous)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-user text-mayelia-600"></i>
                                <span class="font-semibold text-gray-700">Client</span>
                            </div>
                            <p class="text-gray-900">{{ $dossierOuvert->rendezVous->client->nom }} {{ $dossierOuvert->rendezVous->client->prenom }}</p>
                            <p class="text-sm text-gray-600">{{ $dossierOuvert->rendezVous->client->email }}</p>
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
                                <i class="fas fa-calendar text-purple-600"></i>
                                <span class="font-semibold text-gray-700">Rendez-vous</span>
                            </div>
                            <p class="text-gray-900">{{ $dossierOuvert->rendezVous->date_rendez_vous->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-600">{{ $dossierOuvert->rendezVous->tranche_horaire }}</p>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 md:col-span-3">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-info-circle text-orange-600"></i>
                                <span class="font-semibold text-gray-700">Dossier sans rendez-vous</span>
                            </div>
                            <p class="text-sm text-gray-600">Ce dossier n'est associé à aucun rendez-vous.</p>
                        </div>
                    @endif
                </div>
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
                
                <div class="bg-white border rounded-lg p-4" id="progression-container">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progression</span>
                        <span class="text-sm font-bold text-mayelia-600" id="progression-text">{{ $dossierOuvert->progression }}%</span>
                    </div>
                    <div class="w-48 bg-gray-200 rounded-full h-3">
                        <div id="progression-bar" class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-300" 
                             style="width: {{ $dossierOuvert->progression }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Étapes du workflow -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Étape 1: Fiche de pré-enrôlement -->
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-mayelia-500 p-6 hover:shadow-xl transition-shadow">
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
                        <i class="fas fa-clock mr-1"></i>En attente 
                    @endif
                </span>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <p class="text-gray-700 text-sm mb-2">
                    <i class="fas fa-info-circle text-mayelia-500 mr-2"></i>
                    Vérification de la fiche de pré-enrôlement remplie par le client en ligne.
                </p>
                <div class="text-xs text-gray-500">
                    <i class="fas fa-calendar mr-1"></i>
                    À implémenter prochainement
                </div>
            </div>
            
            @if($dossierOuvert->fiche_pre_enrolement_verifiee)
                <div id="fiche-verifiee-message" class="bg-green-50 border border-green-200 rounded-lg p-3 text-green-800 text-sm mb-3">
                    <i class="fas fa-check-circle mr-2"></i>Fiche vérifiée avec succès
                </div>
            @endif
            
            @userCan('dossiers', 'update')
            <button id="btn-verifier-fiche" onclick="verifierFichePreEnrolement()" class="w-full {{ $dossierOuvert->fiche_pre_enrolement_verifiee ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-mayelia-600 text-white hover:bg-mayelia-700' }} px-4 py-3 rounded-lg transition-colors font-medium">
                <i class="fas {{ $dossierOuvert->fiche_pre_enrolement_verifiee ? 'fa-edit' : 'fa-check' }} mr-2"></i>{{ $dossierOuvert->fiche_pre_enrolement_verifiee ? 'Modifier la vérification' : 'Vérifier la fiche' }}
            </button>
            @enduserCan
        </div>

        <!-- Étape 2: Vérification des documents -->
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-green-500 p-6 hover:shadow-xl transition-shadow">
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
            
            
            @if($documentsVerifies->isNotEmpty())
                <!-- Affichage des documents vérifiés -->
                <div class="documents-results bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">
                        <i class="fas fa-list-check mr-2"></i>Documents vérifiés
                    </h4>
                    <div class="space-y-2">
                        @foreach($documentsVerifies as $docVerif)
                            <div class="flex items-center justify-between p-2 rounded {{ $docVerif->present ? 'bg-green-50' : 'bg-red-50' }}">
                                <div class="flex items-center space-x-2">
                                    <i class="fas {{ $docVerif->present ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600' }}"></i>
                                    <span class="text-sm {{ $docVerif->present ? 'text-green-800' : 'text-red-800' }}">
                                        {{ $docVerif->documentRequis->nom_document }}
                                    </span>
                                </div>
                                @if($docVerif->present && $docVerif->nom_fichier)
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-paperclip mr-1"></i>Fichier joint
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            @userCan('dossiers', 'update')
            <button onclick="verifierDocuments()" class="w-full {{ $dossierOuvert->documents_verifies ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-green-600 text-white hover:bg-green-700' }} px-4 py-3 rounded-lg transition-colors font-medium">
                <i class="fas {{ $dossierOuvert->documents_verifies ? 'fa-edit' : 'fa-list-check' }} mr-2"></i>{{ $dossierOuvert->documents_verifies ? 'Modifier les documents' : 'Vérifier les documents' }}
            </button>
            @enduserCan
        </div>

        <!-- Étape 3: Informations client -->
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-purple-500 p-6 hover:shadow-xl transition-shadow">
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
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <p class="text-gray-700 text-sm mb-2">
                    <i class="fas fa-info-circle text-purple-500 mr-2"></i>
                    Mise à jour et vérification des informations du client.
                </p>
                <div class="text-xs text-gray-500">
                    <i class="fas fa-user-edit mr-1"></i>
                    Nom, prénom, date de naissance, adresse, profession, etc.
                </div>
            </div>
            
            @if($dossierOuvert->informations_client_verifiees)
                <!-- Affichage des informations client vérifiées -->
                <div class="client-info-results bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">
                        <i class="fas fa-user-check mr-2"></i>Informations client vérifiées
                    </h4>
                    @if($dossierOuvert->rendezVous)
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
                                
                                @if($dossierOuvert->rendezVous->client->adresse)
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-map-marker-alt text-purple-500 w-4"></i>
                                    <div>
                                        <span class="text-xs text-gray-500">Adresse</span>
                                        <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->rendezVous->client->adresse }}</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($dossierOuvert->rendezVous->client->profession)
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-briefcase text-purple-500 w-4"></i>
                                    <div>
                                        <span class="text-xs text-gray-500">Profession</span>
                                        <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->rendezVous->client->profession }}</p>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-check-circle text-green-500 w-4"></i>
                                    <div>
                                        <span class="text-xs text-gray-500">Statut</span>
                                        <p class="text-sm font-medium text-green-700">Informations validées</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <p class="text-sm text-orange-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Ce dossier n'est associé à aucun rendez-vous. Les informations client ne peuvent pas être affichées.
                            </p>
                        </div>
                    @endif
                </div>
                
            @endif 
            
            @userCan('dossiers', 'update')
            <button onclick="modifierInformationsClient()" class="w-full {{ $dossierOuvert->informations_client_verifiees ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-purple-600 text-white hover:bg-purple-700' }} px-4 py-3 rounded-lg transition-colors font-medium">
                <i class="fas {{ $dossierOuvert->informations_client_verifiees ? 'fa-edit' : 'fa-check' }} mr-2"></i>{{ $dossierOuvert->informations_client_verifiees ? 'Modifier les informations' : 'Modifier les informations' }}
            </button>
            @enduserCan
        </div>

        <!-- Étape 4: Vérification du paiement -->
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-orange-500 p-6 hover:shadow-xl transition-shadow">
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
                        <i class="fas fa-clock mr-1"></i>En attente 
                    @endif
                </span>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <p class="text-gray-700 text-sm mb-2">
                    <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                    Vérification du paiement et génération du reçu de traçabilité.
                </p>
                <div class="text-xs text-gray-500">
                    <i class="fas fa-receipt mr-1"></i>
                    Le client doit présenter le reçu de paiement de la caisse
                </div>
            </div>
            
            @if($dossierOuvert->paiement_verifie && $dossierOuvert->paiementVerification)
                <!-- Affichage des détails du paiement vérifié -->
                <div class="paiement-results bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">
                        <i class="fas fa-receipt mr-2"></i>Détails du paiement vérifié
                    </h4>
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
                                <i class="fas fa-hashtag text-orange-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Référence</span>
                                    <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->paiementVerification->reference_paiement ?? 'Non spécifiée' }}</p>
                                </div>
                            </div>
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
                            
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-user-check text-orange-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Vérifié par</span>
                                    <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->paiementVerification->verifiePar->nom ?? 'Agent' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-check-circle text-green-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Statut</span>
                                    <p class="text-sm font-medium text-green-700">Paiement vérifié</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @userCan('dossiers', 'update')
                <button onclick="verifierPaiement()" class="w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium hover:bg-green-200 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Modifier le paiement
                </button>
                @enduserCan
            @else
                @userCan('dossiers', 'update')
                <button onclick="verifierPaiement()" class="w-full bg-orange-600 text-white px-4 py-3 rounded-lg hover:bg-orange-700 transition-colors font-medium">
                    <i class="fas fa-receipt mr-2"></i>Vérifier le paiement
                </button>
                @enduserCan
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('rendez-vous.index') }}" class="flex items-center space-x-2 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à la liste</span>
            </a>
            
            <div class="flex space-x-3">
                @if($dossierOuvert->statut !== 'finalise' && $dossierOuvert->statut !== 'annulé')
                    @userCan('dossiers', 'update')
                    <button onclick="rejeterDossier()" class="flex items-center space-x-2 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-times-circle"></i>
                        <span>Rejeter le dossier</span>
                    </button>
                    <button onclick="finaliserDossier()" class="flex items-center space-x-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                        <i class="fas fa-check-circle"></i>
                        <span>Finaliser le dossier</span>
                    </button>
                    @enduserCan
                @elseif($dossierOuvert->statut === 'finalise')
                    <div class="flex items-center space-x-2 bg-green-100 text-green-800 px-6 py-3 rounded-lg font-medium">
                        <i class="fas fa-check-circle"></i>
                        <span>Dossier finalisé</span>
                    </div>
                @elseif($dossierOuvert->statut === 'annulé')
                    <div class="flex items-center space-x-2 bg-red-100 text-red-800 px-6 py-3 rounded-lg font-medium">
                        <i class="fas fa-times-circle"></i>
                        <span>Dossier rejeté</span>
                    </div>
                @endif
                
                @if($dossierOuvert->statut === 'finalise')
                <a href="{{ route('dossier.imprimer-recu', $dossierOuvert) }}" 
                   target="_blank"
                   class="flex items-center space-x-2 bg-mayelia-600 text-white px-6 py-3 rounded-lg hover:bg-mayelia-700 transition-colors font-medium">
                    <i class="fas fa-print"></i>
                    <span>Imprimer le reçu</span>
                </a>
                
                <a href="{{ route('dossier.imprimer-etiquette', $dossierOuvert) }}" 
                   target="_blank"
                   class="flex items-center space-x-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-barcode"></i>
                    <span>Imprimer étiquette</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Étape 1: Vérification fiche pré-enrôlement -->
<div id="modalFichePreEnrolement" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-file-alt text-mayelia-600 mr-2"></i>
                        Vérification fiche pré-enrôlement
                    </h3>
                    <button onclick="closeModal('modalFichePreEnrolement')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-gray-600 text-sm mb-4">
                        Vérifiez que la fiche de pré-enrôlement du client est correctement remplie.
                    </p>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload de la fiche (optionnel)
                        </label>
                        <input type="file" id="ficheFile" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-mayelia-50 file:text-mayelia-700 hover:file:bg-mayelia-100">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Commentaires
                        </label>
                        <textarea id="ficheCommentaires" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500" placeholder="Ajoutez des commentaires si nécessaire...">{{ $dossierOuvert->notes }}</textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('modalFichePreEnrolement')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button onclick="validerFichePreEnrolement()" class="px-4 py-2 bg-mayelia-600 text-white rounded-lg hover:bg-mayelia-700">
                        <i class="fas fa-check mr-2"></i>Valider la fiche
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Étape 2: Vérification documents -->
<div id="modalDocuments" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-folder-open text-green-600 mr-2"></i>
                        Vérification des documents
                    </h3>
                    <button onclick="closeModal('modalDocuments')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-600 text-sm mb-4">
                        Sélectionnez le type de demande du client, puis cochez les documents présents. Les documents obligatoires sont marqués en rouge.
                    </p>
                    
                    <!-- Sélection du type de demande -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type de demande du client <span class="text-red-500">*</span>
                        </label>
                        <select id="typeDemandeClient" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" onchange="filtrerDocumentsParType()">
                            <option value="">Sélectionner le type de demande</option>
                            <option value="Première demande">Première demande</option>
                            <option value="Renouvellement">Renouvellement</option>
                            <option value="Modification">Modification</option>
                            <option value="Duplicata">Duplicata</option>
                        </select>
                    </div>
                    
                    <div class="space-y-3" id="documentsList">
                        @foreach($documentsRequis as $document)
                        <div class="p-4 bg-gray-50 rounded-lg border document-item" data-type="{{ $document->type_demande }}" style="display: none;">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" id="doc_{{ $document->id }}" 
                                           class="w-5 h-5 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500"
                                           value="{{ $document->id }}"
                                           data-nom="{{ $document->nom_document }}"
                                           data-obligatoire="{{ $document->obligatoire ? 'true' : 'false' }}">
                                    <label for="doc_{{ $document->id }}" class="text-sm font-medium text-gray-700">
                                        {{ $document->nom_document }}
                                    </label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($document->obligatoire)
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                            Obligatoire
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-mayelia-100 text-mayelia-700">
                                            Optionnel
                                        </span>
                                    @endif
                                    
                                    <!-- Bouton pour afficher la zone d'upload (facultatif) -->
                                    <button type="button" 
                                            id="btn_upload_{{ $document->id }}"
                                            onclick="toggleFileInput({{ $document->id }})"
                                            class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors hidden">
                                        <i class="fas fa-paperclip mr-1"></i>Ajouter fichier
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Zone d'upload (visible si activée) -->
                            <div id="upload_zone_{{ $document->id }}" class="ml-8 mt-2 hidden">
                                <div class="flex items-center space-x-2">
                                    <input type="file" id="file_{{ $document->id }}" 
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <button type="button" 
                                            onclick="removeFileInput({{ $document->id }})"
                                            class="px-2 py-1 text-xs rounded bg-red-100 text-red-700 hover:bg-red-200">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>Formats acceptés: PDF, JPG, PNG (Max 10Mo) - Facultatif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div id="noDocumentsMessage" class="text-center text-gray-500 py-8" style="display: none;">
                        <i class="fas fa-info-circle text-4xl mb-2"></i>
                        <p>Veuillez d'abord sélectionner un type de demande</p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('modalDocuments')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button onclick="validerDocuments()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Valider les documents
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Étape 3: Informations client -->
<div id="modalInformationsClient" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user text-purple-600 mr-2"></i>
                        Informations client
                    </h3>
                    <button onclick="closeModal('modalInformationsClient')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                @if($dossierOuvert->rendezVous)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                            <input type="text" id="clientNom" value="{{ $dossierOuvert->rendezVous->client->nom }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                            <input type="text" id="clientPrenom" value="{{ $dossierOuvert->rendezVous->client->prenom }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="clientEmail" value="{{ $dossierOuvert->rendezVous->client->email }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                            <input type="tel" id="clientTelephone" value="{{ $dossierOuvert->rendezVous->client->telephone ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                            <input type="date" id="clientDateNaissance" value="{{ $dossierOuvert->rendezVous->client->date_naissance ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                            <input type="text" id="clientAdresse" value="{{ $dossierOuvert->rendezVous->client->adresse ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Profession</label>
                            <input type="text" id="clientProfession" value="{{ $dossierOuvert->rendezVous->client->profession ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                @else
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-orange-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Ce dossier n'est associé à aucun rendez-vous. La modification des informations client n'est pas disponible.
                        </p>
                    </div>
                @endif
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('modalInformationsClient')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button onclick="validerRAS()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-check mr-2"></i>R.A.S
                    </button>
                    <button onclick="validerInformationsClient()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-save mr-2"></i>Valider les modifications
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rejeter Dossier -->
<div id="modalRejeterDossier" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                        Rejeter le dossier
                    </h3>
                    <button onclick="closeModal('modalRejeterDossier')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-600 text-sm mb-4 bg-red-50 p-3 rounded-lg border-l-4 border-red-500">
                        <i class="fas fa-info-circle text-red-600 mr-2"></i>
                        Vous êtes sur le point de rejeter ce dossier. Veuillez indiquer la raison du rejet.
                    </p>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Motif du rejet <span class="text-red-500">*</span>
                        </label>
                        <textarea id="noteRejet" rows="4" 
                                  class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                  placeholder="Ex: Documents incomplets, informations erronées, client absent..." required></textarea>
                        <p class="text-xs text-gray-500 mt-1">Cette note sera enregistrée dans l'historique du dossier.</p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('modalRejeterDossier')" class="px-5 py-2.5 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium transition-colors">
                        Annuler
                    </button>
                    <button onclick="validerRejet()" class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium shadow-lg transition-all transform hover:scale-105">
                        <i class="fas fa-times-circle mr-2"></i>Confirmer le rejet
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Étape 4: Vérification paiement -->
<div id="modalPaiement" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-credit-card text-orange-600 mr-2"></i>
                        Vérification du paiement
                    </h3>
                    <button onclick="closeModal('modalPaiement')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-600 text-sm mb-6 bg-blue-50 p-3 rounded-lg border-l-4 border-blue-500">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Le client a effectué la biométrie et le paiement. Vérifiez le reçu de paiement.
                    </p>
                    
                    <!-- Montant -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Montant payé (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="montantPaye" 
                                   class="w-full px-4 py-3 pr-16 text-lg font-semibold border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                   placeholder="Ex: 50000"
                                   value="{{ $dossierOuvert->paiementVerification->montant_paye ?? '' }}"
                                   oninput="formatMontant(this)">
                            <span class="absolute right-4 top-3 text-gray-500 font-medium">FCFA</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" id="montantEnLettres"></p>
                    </div>
                    
                    <!-- Référence -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro de référence <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="referencePaiement" 
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent uppercase" 
                               placeholder="Ex: REF-2024-001234"
                               value="{{ $dossierOuvert->paiementVerification->reference_paiement ?? '' }}">
                    </div>
                    
                    <!-- Upload reçu -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reçu de paiement (optionnel)
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Cliquer pour uploader</span> ou glisser-déposer</p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (MAX. 5MB)</p>
                                </div>
                                <input type="file" id="recuPaiement" class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="displayFileName(this)">
                            </label>
                        </div>
                        <p id="fileName" class="text-xs text-green-600 mt-2 hidden"></p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('modalPaiement')" class="px-5 py-2.5 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium transition-colors">
                        Annuler
                    </button>
                    <button onclick="validerPaiement()" class="px-5 py-2.5 bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-lg hover:from-orange-700 hover:to-orange-800 font-medium shadow-lg transition-all transform hover:scale-105">
                        <i class="fas fa-check mr-2"></i>Valider le paiement
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

// Formater le montant et afficher en lettres
function formatMontant(input) {
    const montant = parseInt(input.value) || 0;
    const montantEnLettres = document.getElementById('montantEnLettres');
    
    if (montant > 0) {
        montantEnLettres.textContent = `${montant.toLocaleString('fr-FR')} FCFA`;
        montantEnLettres.classList.remove('hidden');
    } else {
        montantEnLettres.textContent = '';
    }
}

// Afficher le nom du fichier uploadé
function displayFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = `✓ Fichier sélectionné: ${input.files[0].name}`;
        fileName.classList.remove('hidden');
    }
}
</script>

<script>
// Fonctions pour les modals
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

// Étape 1: Fiche pré-enrôlement
function verifierFichePreEnrolement() {
    openModal('modalFichePreEnrolement');
}

function validerFichePreEnrolement() {
    const commentaires = document.getElementById('ficheCommentaires').value;
    const fichier = document.getElementById('ficheFile').files[0];
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validation...';
    button.disabled = true;
    
    // Préparer les données
    const formData = new FormData();
    formData.append('commentaires', commentaires);
    if (fichier) {
        formData.append('fiche_file', fichier);
    }
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Appel AJAX
    fetch(`/dossier/{{ $dossierOuvert->id }}/etape1-fiche`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('modalFichePreEnrolement');
            showSuccessToast(data.message);
            updateEtapeStatus(1, true, data.progression);
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showErrorToast('Erreur lors de la validation');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Étape 2: Documents
function verifierDocuments() {
    openModal('modalDocuments');
    
    // Vérifier si des documents sont déjà cochés ou vérifiés
    const checkedDocs = document.querySelectorAll('#documentsList input[type="checkbox"]:checked');
    const typeDemandeSelect = document.getElementById('typeDemandeClient');
    
    // Si aucun document n'est affiché (première ouverture ou rechargement), essayer de déduire
    if (typeDemandeSelect.value === '') {
        // Essayer de trouver un document déjà vérifié (via les attributs data ou checked au chargement)
        // Note: Les checkboxes sont générées par la boucle Blade, donc si elles sont checked, c'est que c'est bon.
        // Mais elles sont cachées par défaut (display: none sur le parent).
        
        let foundType = null;
        
        // Parcourir tous les items de documents pour voir s'il y en a un de coché (par le serveur)
        const allDocItems = document.querySelectorAll('.document-item');
        for (const item of allDocItems) {
            const checkbox = item.querySelector('input[type="checkbox"]');
            // Si on avait un injectage serveur "checked", on le détecterait ici.
            // Mais le blade actuel ne met pas "checked" directement.
            
            // Alternative: On va injecter les IDs vérifiés via JS au chargement de la page ci-dessous
        }
        
        // Si on a des documentsVérifies pasés par le contrôleur
        @if(isset($documentsVerifies) && $documentsVerifies->isNotEmpty())
            // On prend le premier document vérifié pour déduire le type
            const firstDoc = @json($documentsVerifies->first()->documentRequis);
            if (firstDoc && firstDoc.type_demande) {
                typeDemandeSelect.value = firstDoc.type_demande;
                filtrerDocumentsParType();
                
                // Cocher les cases correspondantes
                const verifiedIds = @json($documentsVerifies->pluck('document_requis_id'));
                verifiedIds.forEach(id => {
                    const cb = document.getElementById('doc_' + id);
                    if (cb) {
                        cb.checked = true;
                        // Afficher zone upload si un fichier existe (à améliorer si possible)
                    }
                });
            }
        @else
            // Comportement par défaut : reset si rien n'est fait
             document.getElementById('noDocumentsMessage').style.display = 'block';
             document.querySelectorAll('.document-item').forEach(item => {
                item.style.display = 'none';
            });
        @endif
    } else {
        // Si une sélection existe déjà (par l'utilisateur dans cette session), on ne touche à rien
        // sauf si on veut rafraichir.
    }
}

// Filtrer les documents par type de demande
function filtrerDocumentsParType() {
    const typeSelectionne = document.getElementById('typeDemandeClient').value;
    const documentsList = document.getElementById('documentsList');
    const noDocumentsMessage = document.getElementById('noDocumentsMessage');
    
    if (!typeSelectionne) {
        // Aucun type sélectionné
        document.querySelectorAll('.document-item').forEach(item => {
            item.style.display = 'none';
        });
        noDocumentsMessage.style.display = 'block';
        return;
    }
    
    // Masquer le message "aucun document"
    noDocumentsMessage.style.display = 'none';
    
    // Afficher/masquer les documents selon le type
    let documentsVisibles = 0;
    document.querySelectorAll('.document-item').forEach(item => {
        if (item.dataset.type === typeSelectionne) {
            item.style.display = 'block';
            documentsVisibles++;
            
            // Afficher le bouton d'upload pour chaque document
            const docId = item.querySelector('input[type="checkbox"]').value;
            const btnUpload = document.getElementById(`btn_upload_${docId}`);
            if (btnUpload) {
                btnUpload.classList.remove('hidden');
            }
        } else {
            item.style.display = 'none';
        }
    });
    
    // Si aucun document pour ce type, afficher un message
    if (documentsVisibles === 0) {
        noDocumentsMessage.innerHTML = `
            <i class="fas fa-info-circle text-4xl mb-2"></i>
            <p>Aucun document requis pour le type "${typeSelectionne}"</p>
        `;
        noDocumentsMessage.style.display = 'block';
    }
}

// Afficher/masquer le champ d'upload (facultatif)
function toggleFileInput(docId) {
    const uploadZone = document.getElementById(`upload_zone_${docId}`);
    const btnUpload = document.getElementById(`btn_upload_${docId}`);
    
    if (uploadZone.classList.contains('hidden')) {
        uploadZone.classList.remove('hidden');
        if (btnUpload) {
            btnUpload.innerHTML = '<i class="fas fa-check mr-1"></i>Fichier ajouté';
            btnUpload.classList.remove('bg-blue-100', 'text-blue-700');
            btnUpload.classList.add('bg-green-100', 'text-green-700');
        }
    } else {
        uploadZone.classList.add('hidden');
        if (btnUpload) {
            btnUpload.innerHTML = '<i class="fas fa-paperclip mr-1"></i>Ajouter fichier';
            btnUpload.classList.remove('bg-green-100', 'text-green-700');
            btnUpload.classList.add('bg-blue-100', 'text-blue-700');
        }
    }
}

// Supprimer le fichier sélectionné
function removeFileInput(docId) {
    const fileInput = document.getElementById(`file_${docId}`);
    const uploadZone = document.getElementById(`upload_zone_${docId}`);
    const btnUpload = document.getElementById(`btn_upload_${docId}`);
    
    if (fileInput) {
        fileInput.value = '';
    }
    
    uploadZone.classList.add('hidden');
    
    if (btnUpload) {
        btnUpload.innerHTML = '<i class="fas fa-paperclip mr-1"></i>Ajouter fichier';
        btnUpload.classList.remove('bg-green-100', 'text-green-700');
        btnUpload.classList.add('bg-blue-100', 'text-blue-700');
    }
}

function validerDocuments() {
    console.log('=== DÉBUT VALIDATION DOCUMENTS ===');
    
    // Récupérer le type de demande sélectionné
    const typeDemande = document.getElementById('typeDemandeClient').value;
    if (!typeDemande) {
        showErrorToast('Veuillez sélectionner un type de demande');
        return;
    }
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validation...';
    button.disabled = true;
    
    // Préparer les données avec FormData pour l'upload de fichiers
    const formData = new FormData();
    formData.append('type_demande', typeDemande);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Récupérer les documents cochés
    const checkboxes = document.querySelectorAll('#documentsList input[type="checkbox"]:checked');
    
    checkboxes.forEach((checkbox, index) => {
        const docId = checkbox.id.replace('doc_', '');
        const fileInput = document.getElementById(`file_${docId}`);
        
        // Ajouter l'ID du document
        formData.append(`documents[${index}][id]`, docId);
        
        // Ajouter le fichier s'il y en a un
        if (fileInput && fileInput.files[0]) {
            formData.append(`documents[${index}][fichier]`, fileInput.files[0]);
        }
    });
    
    // Appel AJAX
    fetch(`/dossier/{{ $dossierOuvert->id }}/etape2-documents`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Réponse serveur:', data);
        if (data.success) {
            closeModal('modalDocuments');
            showSuccessToast(data.message);
            
            // Mettre à jour l'affichage de l'étape 2 avec les résultats
            updateDocumentsDisplay(data.documents_selectionnes, data.documents_manquants, typeDemande);
            
            // Mettre à jour le statut et la progression
            updateEtapeStatus(2, true, data.progression);
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showErrorToast('Erreur lors de la validation');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Mettre à jour l'affichage des documents dans l'étape 2
function updateDocumentsDisplay(documentsSelectionnes, documentsManquants, typeDemande) {
    console.log('=== MISE À JOUR AFFICHAGE DOCUMENTS ===');
    console.log('Documents sélectionnés:', documentsSelectionnes);
    console.log('Documents manquants:', documentsManquants);
    console.log('Type de demande:', typeDemande);
    
    // Chercher la carte de l'étape 2 plus spécifiquement
    const etape2Cards = document.querySelectorAll('.bg-white.rounded-lg.shadow-lg.border-l-4.border-green-500');
    let etape2Card = null;
    
    // Trouver celle qui contient "Documents requis"
    etape2Cards.forEach(card => {
        if (card.textContent.includes('Documents requis')) {
            etape2Card = card;
        }
    });
    
    if (!etape2Card) {
        console.error('Carte étape 2 non trouvée');
        return;
    }
    
    console.log('Carte étape 2 trouvée:', etape2Card);
    
    // Trouver ou créer la section d'affichage des résultats
    let resultsSection = etape2Card.querySelector('.documents-results');
    if (!resultsSection) {
        console.log('Création de la section résultats');
        resultsSection = document.createElement('div');
        resultsSection.className = 'documents-results bg-gray-50 rounded-lg p-4 mb-4';
        
        // Insérer après le titre et avant le bouton
        const button = etape2Card.querySelector('button');
        if (button) {
            etape2Card.insertBefore(resultsSection, button);
            console.log('Section résultats insérée avant le bouton');
        } else {
            console.error('Bouton non trouvé dans la carte étape 2');
        }
    }
    
    // Afficher le type de demande
    resultsSection.innerHTML = `
        <h4 class="font-medium text-gray-700 mb-3">
            <i class="fas fa-list-check mr-2"></i>Résultats de la vérification
            <span class="text-sm text-gray-500 ml-2">(${typeDemande})</span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h5 class="text-sm font-medium text-green-700 mb-2">
                    <i class="fas fa-check-circle mr-1"></i>Documents présents (${documentsSelectionnes.length})
                </h5>
                <div class="space-y-1">
                    ${documentsSelectionnes.map(doc => `
                        <div class="text-xs text-gray-600 bg-green-50 p-2 rounded">
                            <i class="fas fa-file mr-1"></i>${doc.nom}
                        </div>
                    `).join('')}
                </div>
            </div>
            <div>
                <h5 class="text-sm font-medium text-red-700 mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Documents manquants (${documentsManquants.length})
                </h5>
                <div class="space-y-1">
                    ${documentsManquants.map(doc => `
                        <div class="text-xs text-gray-600 bg-red-50 p-2 rounded">
                            <i class="fas fa-file mr-1"></i>${doc.nom}
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    
    console.log('Affichage des documents mis à jour');
}

// Étape 3: Informations client
function modifierInformationsClient() {
    openModal('modalInformationsClient');
}

function validerRAS() {
    console.log('=== DÉBUT VALIDATION R.A.S ===');
    console.log('Dossier ID:', {{ $dossierOuvert->id }});
    
    // Appel AJAX pour R.A.S
    fetch(`/dossier/{{ $dossierOuvert->id }}/etape3-infos`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ras: true
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            closeModal('modalInformationsClient');
            showSuccessToast('Informations client validées (R.A.S)');
            updateEtapeStatus(3, true, data.progression);
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showErrorToast('Erreur lors de la validation');
    });
}

function validerInformationsClient() {
    console.log('=== DÉBUT VALIDATION INFORMATIONS CLIENT ===');
    
    const nom = document.getElementById('clientNom').value;
    const prenom = document.getElementById('clientPrenom').value;
    const email = document.getElementById('clientEmail').value;
    const telephone = document.getElementById('clientTelephone').value;
    const dateNaissance = document.getElementById('clientDateNaissance').value;
    const adresse = document.getElementById('clientAdresse').value;
    const profession = document.getElementById('clientProfession').value;
    
    console.log('Données récupérées:', { nom, prenom, email, telephone, dateNaissance, adresse, profession });
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validation...';
    button.disabled = true;
    
    const dataToSend = {
        nom, prenom, email, telephone, 
        date_naissance: dateNaissance, 
        adresse, profession
    };
    
    console.log('Données à envoyer:', dataToSend);
    
    // Appel AJAX
    fetch(`/dossier/{{ $dossierOuvert->id }}/etape3-infos`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(dataToSend)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            closeModal('modalInformationsClient');
            showSuccessToast('Informations client mises à jour avec succès');
            updateEtapeStatus(3, true, data.progression);
            
            // Mettre à jour l'affichage des informations client si les données sont fournies
            if (data.client) {
                updateClientInfoDisplay(data.client);
            }
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showErrorToast('Erreur lors de la validation');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Étape 4: Paiement
function verifierPaiement() {
    openModal('modalPaiement');
}

function validerPaiement() {
    const reference = document.getElementById('referencePaiement').value;
    const montant = document.getElementById('montantPaye').value;
    const recu = document.getElementById('recuPaiement').files[0];
    
    if (!reference || !montant) {
        showErrorToast('Veuillez remplir tous les champs obligatoires');
        return;
    }
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validation...';
    button.disabled = true;
    
    // Préparer les données
    const formData = new FormData();
    formData.append('reference', reference);
    formData.append('montant', montant);
    
    if (recu) {
        formData.append('recu_file', recu);
    }
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Appel AJAX
    fetch(`/dossier/{{ $dossierOuvert->id }}/etape4-paiement`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('modalPaiement');
            showSuccessToast('Paiement validé avec succès');
            updateEtapeStatus(4, true, data.progression);
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showErrorToast('Erreur lors de la validation');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Fonction pour mettre à jour le statut des étapes
function updateEtapeStatus(etape, valide, progression) {
    console.log(`Étape ${etape} ${valide ? 'validée' : 'invalidée'}, progression: ${progression}%`);
    
    // Mettre à jour la barre de progression (utiliser les IDs pour être plus précis)
    if (progression !== undefined && progression !== null) {
        const progressBar = document.getElementById('progression-bar');
        const progressText = document.getElementById('progression-text');
        
        if (progressBar) {
            progressBar.style.width = `${progression}%`;
            console.log(`Barre de progression mise à jour: ${progression}%`);
        } else {
            console.warn('Barre de progression non trouvée');
        }
        
        if (progressText) {
            progressText.textContent = `${progression}%`;
            console.log(`Texte de progression mis à jour: ${progression}%`);
        } else {
            console.warn('Texte de progression non trouvé');
        }
    }
    
    // Mettre à jour le statut de l'étape spécifique
    if (etape === 1) {
        updateEtape1Status(valide);
    } else if (etape === 2) {
        updateEtape2Status(valide);
    } else if (etape === 3) {
        updateEtape3Status(valide);
    } else if (etape === 4) {
        updateEtape4Status(valide);
    }
}

function updateEtape1Status(valide) {
    const etape1Card = document.querySelector('.bg-white.rounded-lg.shadow-lg.border-l-4.border-mayelia-500');
    if (!etape1Card) {
        console.error('Carte étape 1 non trouvée');
        return;
    }
    
    // Mettre à jour le statut dans l'en-tête
    const statusSpan = etape1Card.querySelector('span.px-3.py-1.text-xs.rounded-full');
    if (statusSpan) {
        if (valide) {
            statusSpan.className = 'px-3 py-1 text-xs rounded-full font-medium bg-green-100 text-green-800';
            statusSpan.innerHTML = '<i class="fas fa-check mr-1"></i>Vérifiée';
            console.log('Statut étape 1 mis à jour: Vérifiée');
        } else {
            statusSpan.className = 'px-3 py-1 text-xs rounded-full font-medium bg-yellow-100 text-yellow-800';
            statusSpan.innerHTML = '<i class="fas fa-clock mr-1"></i>En attente';
        }
    }
    
    // Si validée, mettre à jour le bouton
    if (valide) {
        // Chercher le bouton par son ID
        const btnVerifier = document.getElementById('btn-verifier-fiche');
        if (btnVerifier) {
            btnVerifier.innerHTML = '<i class="fas fa-edit mr-2"></i>Modifier la vérification';
            btnVerifier.className = 'w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium hover:bg-green-200 transition-colors';
            btnVerifier.disabled = false;
        }
    }
}

function updateEtape2Status(valide) {
    const etape2Card = document.querySelector('.bg-white.rounded-lg.shadow-lg.border-l-4.border-green-500');
    if (!etape2Card) return;
    
    // Mettre à jour le statut dans l'en-tête
    const statusSpan = etape2Card.querySelector('span.px-3.py-1.text-xs.rounded-full');
    if (statusSpan) {
        if (valide) {
            statusSpan.className = 'px-3 py-1 text-xs rounded-full font-medium bg-green-100 text-green-800';
            statusSpan.innerHTML = '<i class="fas fa-check mr-1"></i>Vérifiés';
        } else {
            statusSpan.className = 'px-3 py-1 text-xs rounded-full font-medium bg-red-100 text-red-800';
            statusSpan.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Manquants';
        }
    }
    
    // Mettre à jour le bouton
    const button = etape2Card.querySelector('button');
    if (button && valide) {
        button.innerHTML = '<i class="fas fa-edit mr-2"></i>Modifier les documents';
        button.className = 'w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium hover:bg-green-200 transition-colors';
        button.disabled = false;
    }
}

function updateEtape3Status(valide) {
    const etape3Card = document.querySelector('.bg-white.rounded-lg.shadow-lg.border-l-4.border-purple-500');
    if (!etape3Card) return;
    
    // Mettre à jour le statut dans l'en-tête
    const statusSpan = etape3Card.querySelector('span.px-3.py-1.text-xs.rounded-full');
    if (statusSpan) {
        if (valide) {
            statusSpan.className = 'px-3 py-1 text-xs rounded-full font-medium bg-green-100 text-green-800';
            statusSpan.innerHTML = '<i class="fas fa-check mr-1"></i>Complétées';
        } else {
            statusSpan.className = 'px-3 py-1 text-xs rounded-full font-medium bg-yellow-100 text-yellow-800';
            statusSpan.innerHTML = '<i class="fas fa-clock mr-1"></i>À compléter';
        }
    }
    
    // Mettre à jour le bouton
    const button = etape3Card.querySelector('button');
    if (button && valide) {
        button.innerHTML = '<i class="fas fa-edit mr-2"></i>Modifier les informations';
        button.className = 'w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium hover:bg-green-200 transition-colors';
        button.disabled = false;
    }
    
    // Afficher les informations client vérifiées
    if (valide) {
        updateClientInfoDisplay();
    }
}

function updateClientInfoDisplay(clientData = null) {
    const etape3Card = document.querySelector('.bg-white.rounded-lg.shadow-lg.border-l-4.border-purple-500');
    if (!etape3Card) return;
    
    // Vérifier si la section existe déjà
    let infoSection = etape3Card.querySelector('.client-info-results');
    if (!infoSection) {
        // Créer la section d'informations
        infoSection = document.createElement('div');
        infoSection.className = 'client-info-results bg-gray-50 rounded-lg p-4 mb-4';
        
        // Insérer avant le bouton
        const button = etape3Card.querySelector('button');
        if (button) {
            etape3Card.insertBefore(infoSection, button);
        }
    }
    
    // Récupérer les informations du client depuis les champs du modal
    const nom = document.getElementById('clientNom')?.value || '{{ $dossierOuvert->rendezVous->client->nom }}';
    const prenom = document.getElementById('clientPrenom')?.value || '{{ $dossierOuvert->rendezVous->client->prenom }}';
    const email = document.getElementById('clientEmail')?.value || '{{ $dossierOuvert->rendezVous->client->email }}';
    const telephone = document.getElementById('clientTelephone')?.value || '{{ $dossierOuvert->rendezVous->client->telephone ?? "" }}';
    const dateNaissance = document.getElementById('clientDateNaissance')?.value || '{{ $dossierOuvert->rendezVous->client->date_naissance ?? "" }}';
    const adresse = document.getElementById('clientAdresse')?.value || '{{ $dossierOuvert->rendezVous->client->adresse ?? "" }}';
    const profession = document.getElementById('clientProfession')?.value || '{{ $dossierOuvert->rendezVous->client->profession ?? "" }}';
    
    // Formater la date
    let dateFormatee = '';
    if (dateNaissance) {
        const date = new Date(dateNaissance);
        dateFormatee = date.toLocaleDateString('fr-FR');
    }
    
    // Afficher les informations
    infoSection.innerHTML = `
        <h4 class="font-medium text-gray-700 mb-3">
            <i class="fas fa-user-check mr-2"></i>Informations client vérifiées
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-user text-purple-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Nom complet</span>
                        <p class="text-sm font-medium text-gray-900">${nom} ${prenom}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <i class="fas fa-envelope text-purple-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Email</span>
                        <p class="text-sm font-medium text-gray-900">${email}</p>
                    </div>
                </div>
                
                ${telephone ? `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-phone text-purple-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Téléphone</span>
                        <p class="text-sm font-medium text-gray-900">${telephone}</p>
                    </div>
                </div>
                ` : ''}
            </div>
            
            <div class="space-y-3">
                ${dateFormatee ? `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-calendar text-purple-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Date de naissance</span>
                        <p class="text-sm font-medium text-gray-900">${dateFormatee}</p>
                    </div>
                </div>
                ` : ''}
                
                ${adresse ? `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-map-marker-alt text-purple-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Adresse</span>
                        <p class="text-sm font-medium text-gray-900">${adresse}</p>
                    </div>
                </div>
                ` : ''}
                
                ${profession ? `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-briefcase text-purple-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Profession</span>
                        <p class="text-sm font-medium text-gray-900">${profession}</p>
                    </div>
                </div>
                ` : ''}
                
                <div class="flex items-center space-x-3">
                    <i class="fas fa-check-circle text-green-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Statut</span>
                        <p class="text-sm font-medium text-green-700">Informations validées</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function updateEtape4Status(valide) {
    const etape4Card = document.querySelector('.bg-white.rounded-lg.shadow-lg.border-l-4.border-orange-500');
    if (!etape4Card) return;
    
    // Mettre à jour le statut dans l'en-tête
    const statusSpan = etape4Card.querySelector('span.px-3.py-1.text-xs.rounded-full');
    if (statusSpan) {
        if (valide) {
            statusSpan.className = 'px-3 py-1 text-xs rounded-full font-medium bg-green-100 text-green-800';
            statusSpan.innerHTML = '<i class="fas fa-check mr-1"></i>Vérifié';
        } else {
            statusSpan.className = 'px-3 py-1 text-xs rounded-full font-medium bg-yellow-100 text-yellow-800';
            statusSpan.innerHTML = '<i class="fas fa-clock mr-1"></i>En attente';
        }
    }
    
    // Mettre à jour le bouton
    const button = etape4Card.querySelector('button');
    if (button && valide) {
        button.innerHTML = '<i class="fas fa-edit mr-2"></i>Modifier le paiement';
        button.className = 'w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium hover:bg-green-200 transition-colors';
        button.disabled = false;
    }
    
    // Afficher les détails du paiement
    if (valide) {
        updatePaiementDisplay();
    }
}

function updatePaiementDisplay() {
    const etape4Card = document.querySelector('.bg-white.rounded-lg.shadow-lg.border-l-4.border-orange-500');
    if (!etape4Card) return;
    
    // Vérifier si la section existe déjà
    let paiementSection = etape4Card.querySelector('.paiement-results');
    if (!paiementSection) {
        // Créer la section de paiement
        paiementSection = document.createElement('div');
        paiementSection.className = 'paiement-results bg-gray-50 rounded-lg p-4 mb-4';
        
        // Insérer avant le bouton
        const button = etape4Card.querySelector('button');
        if (button) {
            etape4Card.insertBefore(paiementSection, button);
        }
    }
    
    // Récupérer les informations du paiement depuis les champs du modal
    const montant = document.getElementById('montantPaye')?.value || '0';
    const reference = document.getElementById('referencePaiement')?.value || 'Non spécifiée';
    
    // Formater le montant
    const montantFormate = new Intl.NumberFormat('fr-FR').format(montant);
    
    // Afficher les détails du paiement
    paiementSection.innerHTML = `
        <h4 class="font-medium text-gray-700 mb-3">
            <i class="fas fa-receipt mr-2"></i>Détails du paiement vérifié
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-money-bill text-orange-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Montant payé</span>
                        <p class="text-sm font-medium text-gray-900">${montantFormate} FCFA</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <i class="fas fa-hashtag text-orange-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Référence</span>
                        <p class="text-sm font-medium text-gray-900">${reference}</p>
                    </div>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-calendar text-orange-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Date de paiement</span>
                        <p class="text-sm font-medium text-gray-900">${new Date().toLocaleDateString('fr-FR')} ${new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <i class="fas fa-user-check text-orange-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Vérifié par</span>
                        <p class="text-sm font-medium text-gray-900">Agent</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <i class="fas fa-check-circle text-green-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Statut</span>
                        <p class="text-sm font-medium text-green-700">Paiement vérifié</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Fonctions de notification (utilisent le composant toast unifié)
// Ces fonctions sont définies dans components/toast.blade.php
// Elles sont redéfinies ici pour éviter les erreurs si le composant n'est pas chargé
if (typeof showSuccessToast === 'undefined') {
    function showSuccessToast(message) {
        showToast(message, 'success', 5000);
    }
}

if (typeof showErrorToast === 'undefined') {
    function showErrorToast(message) {
        showToast(message, 'error', 7000);
    }
}

function rejeterDossier() {
    openModal('modalRejeterDossier');
}

function validerRejet() {
    const note = document.getElementById('noteRejet').value.trim();
    
    if (!note) {
        showErrorToast('Veuillez saisir le motif du rejet');
        return;
    }
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement...';
    button.disabled = true;
    
    // Appeler l'API pour rejeter le dossier
    fetch(`/dossier/{{ $dossierOuvert->id }}/rejeter`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            note: note
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('modalRejeterDossier');
            showSuccessToast('Dossier rejeté avec succès');
            // Recharger la page après 1.5 secondes
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showErrorToast(data.message || 'Erreur lors du rejet du dossier');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showErrorToast('Erreur lors du rejet du dossier');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function finaliserDossier() {
    // Vérifier que toutes les étapes sont validées (utiliser les vrais noms d'attributs)
    const etapes = [
        { nom: 'Fiche de pré-enrôlement', valide: {{ $dossierOuvert->fiche_pre_enrolement_verifiee ? 'true' : 'false' }} },
        { nom: 'Documents', valide: {{ $dossierOuvert->documents_verifies ? 'true' : 'false' }} },
        { nom: 'Informations client', valide: {{ $dossierOuvert->informations_client_verifiees ? 'true' : 'false' }} },
        { nom: 'Paiement', valide: {{ $dossierOuvert->paiement_verifie ? 'true' : 'false' }} }
    ];
    
    const etapesNonValidees = etapes.filter(e => !e.valide);
    
    if (etapesNonValidees.length > 0) {
        const etapesNoms = etapesNonValidees.map(e => e.nom).join(', ');
        showErrorToast(`Veuillez valider toutes les étapes avant de finaliser le dossier. Étapes manquantes : ${etapesNoms}`);
        return;
    }
    
    // Afficher le modal de chargement
    const modal = document.createElement('div');
    modal.id = 'modal-finalisation';
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 shadow-xl">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-spinner fa-spin text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Finalisation du dossier</h3>
                <p class="text-sm text-gray-600">Traitement en cours...</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Appeler l'API pour finaliser le dossier
    fetch('{{ route("dossier.finaliser", $dossierOuvert->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Afficher le message de succès
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 shadow-xl">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                            <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Dossier finalisé avec succès !</h3>
                        <p class="text-sm text-gray-600 mb-6">${data.message || 'Le dossier a été finalisé avec succès.'}</p>
                        <button onclick="location.reload()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            OK
                        </button>
                    </div>
                </div>
            `;
            
            // Recharger la page après 2 secondes
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            // Afficher l'erreur
            document.body.removeChild(modal);
            showErrorToast(data.message || 'Erreur lors de la finalisation du dossier');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        document.body.removeChild(modal);
        showErrorToast('Erreur lors de la finalisation du dossier');
    });
}
</script>
@endsection