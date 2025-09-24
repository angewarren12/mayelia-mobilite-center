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
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder-open text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Dossier #{{ $dossierOuvert->id }}</h1>
                        <p class="text-gray-600">Ouvert le {{ $dossierOuvert->date_ouverture->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-user text-blue-600"></i>
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
                </div>
            </div>
            
            <div class="ml-6 text-right">
                <div class="mb-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        @if($dossierOuvert->statut === 'ouvert') bg-blue-100 text-blue-800
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
                        <span class="text-sm font-bold text-blue-600">{{ $dossierOuvert->progression }}%</span>
                    </div>
                    <div class="w-48 bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-300" 
                             style="width: {{ $dossierOuvert->progression }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Étapes du workflow -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Étape 1: Fiche de pré-enrôlement -->
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-blue-500 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600"></i>
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
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Vérification de la fiche de pré-enrôlement remplie par le client en ligne.
                </p>
                <div class="text-xs text-gray-500">
                    <i class="fas fa-calendar mr-1"></i>
                    À implémenter prochainement
                </div>
            </div>
            
            @if(!$dossierOuvert->fiche_pre_enrolement_verifiee)
                <button onclick="verifierFichePreEnrolement()" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-check mr-2"></i>Vérifier la fiche
                </button>
            @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-green-800 text-sm">
                    <i class="fas fa-check-circle mr-2"></i>Fiche vérifiée avec succès
                </div>
            @endif
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
            
            @if(isset($documentsVerifies) && !empty($documentsVerifies))
                <!-- Affichage des résultats des documents vérifiés -->
                <div class="documents-results bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">
                        <i class="fas fa-list-check mr-2"></i>Résultats de la vérification
                        <span class="text-sm text-gray-500 ml-2">({{ $documentsVerifies['type_demande'] }})</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h5 class="text-sm font-medium text-green-700 mb-2">
                                <i class="fas fa-check-circle mr-1"></i>Documents présents ({{ count($documentsVerifies['documents_selectionnes']) }})
                            </h5>
                            <div class="space-y-1">
                                @foreach($documentsVerifies['documents_selectionnes'] as $doc)
                                    <div class="text-xs text-gray-600 bg-green-50 p-2 rounded">
                                        <i class="fas fa-file mr-1"></i>{{ $doc['nom'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h5 class="text-sm font-medium text-red-700 mb-2">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Documents manquants ({{ count($documentsVerifies['documents_manquants']) }})
                            </h5>
                            <div class="space-y-1">
                                @foreach($documentsVerifies['documents_manquants'] as $doc)
                                    <div class="text-xs text-gray-600 bg-red-50 p-2 rounded">
                                        <i class="fas fa-file mr-1"></i>{{ $doc['nom'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($dossierOuvert->documents_verifies)
                <button class="w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium cursor-not-allowed" disabled>
                    <i class="fas fa-check mr-2"></i>Documents vérifiés
                </button>
            @else
                <button onclick="verifierDocuments()" class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <i class="fas fa-list-check mr-2"></i>Vérifier les documents
                </button>
            @endif
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
                    Nom, prénom, date de naissance, CNI/Passport, etc.
                </div>
            </div>
            
            @if($dossierOuvert->informations_client_verifiees)
                <!-- Affichage des informations client vérifiées -->
                <div class="client-info-results bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">
                        <i class="fas fa-user-check mr-2"></i>Informations client vérifiées
                    </h4>
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
                            
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-check-circle text-green-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Statut</span>
                                    <p class="text-sm font-medium text-green-700">Informations validées</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button class="w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium cursor-not-allowed" disabled>
                    <i class="fas fa-check mr-2"></i>Informations validées
                </button>
            @else
                <button onclick="modifierInformationsClient()" class="w-full bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition-colors font-medium">
                    <i class="fas fa-edit mr-2"></i>Modifier les informations
                </button>
            @endif
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
                                <i class="fas fa-credit-card text-orange-500 w-4"></i>
                                <div>
                                    <span class="text-xs text-gray-500">Mode de paiement</span>
                                    <p class="text-sm font-medium text-gray-900">{{ $dossierOuvert->paiementVerification->mode_paiement ?? 'Non spécifié' }}</p>
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
                
                <button class="w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium cursor-not-allowed" disabled>
                    <i class="fas fa-check mr-2"></i>Paiement vérifié
                </button>
            @else
                <button onclick="verifierPaiement()" class="w-full bg-orange-600 text-white px-4 py-3 rounded-lg hover:bg-orange-700 transition-colors font-medium">
                    <i class="fas fa-receipt mr-2"></i>Vérifier le paiement
                </button>
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
                @if($dossierOuvert->statut !== 'finalise')
                    <button class="flex items-center space-x-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                        <i class="fas fa-check-circle"></i>
                        <span>Finaliser le dossier</span>
                    </button>
                @else
                    <div class="flex items-center space-x-2 bg-green-100 text-green-800 px-6 py-3 rounded-lg font-medium">
                        <i class="fas fa-check-circle"></i>
                        <span>Dossier finalisé</span>
                    </div>
                @endif
                
                <button class="flex items-center space-x-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-print"></i>
                    <span>Imprimer</span>
                </button>
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
                        <i class="fas fa-file-alt text-blue-600 mr-2"></i>
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
                        <input type="file" id="ficheFile" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Commentaires
                        </label>
                        <textarea id="ficheCommentaires" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ajoutez des commentaires si nécessaire..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('modalFichePreEnrolement')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button onclick="validerFichePreEnrolement()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border document-item" data-type="{{ $document->type_demande }}" style="display: none;">
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
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                        Optionnel
                                    </span>
                                @endif
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CNI/Passport</label>
                        <input type="text" id="clientCni" value="{{ $dossierOuvert->rendezVous->client->numero_piece_identite ?? '' }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
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

<!-- Modal Étape 4: Vérification paiement -->
<div id="modalPaiement" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-credit-card text-orange-600 mr-2"></i>
                        Vérification du paiement
                    </h3>
                    <button onclick="closeModal('modalPaiement')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-gray-600 text-sm mb-4">
                        Le client a effectué la biométrie et le paiement. Vérifiez le reçu de paiement.
                    </p>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro de référence du paiement *
                        </label>
                        <input type="text" id="referencePaiement" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" 
                               placeholder="Ex: REF-2024-001234">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Montant payé (FCFA) *
                        </label>
                        <input type="number" id="montantPaye" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" 
                               placeholder="Ex: 50000">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mode de paiement
                        </label>
                        <select id="modePaiement" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Sélectionner...</option>
                            <option value="especes">Espèces</option>
                            <option value="carte">Carte bancaire</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="virement">Virement</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload du reçu (optionnel)
                        </label>
                        <input type="file" id="recuPaiement" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('modalPaiement')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button onclick="validerPaiement()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                        <i class="fas fa-check mr-2"></i>Valider le paiement
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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
    // Réinitialiser le filtre
    document.getElementById('typeDemandeClient').value = '';
    document.getElementById('noDocumentsMessage').style.display = 'block';
    document.querySelectorAll('.document-item').forEach(item => {
        item.style.display = 'none';
    });
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
            item.style.display = 'flex';
            documentsVisibles++;
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

function validerDocuments() {
    console.log('=== DÉBUT VALIDATION DOCUMENTS ===');
    
    // Récupérer le type de demande sélectionné
    const typeDemande = document.getElementById('typeDemandeClient').value;
    if (!typeDemande) {
        showErrorToast('Veuillez sélectionner un type de demande');
        return;
    }
    
    // Récupérer les documents cochés
    const documentsCoches = [];
    const checkboxes = document.querySelectorAll('#documentsList input[type="checkbox"]:checked');
    
    checkboxes.forEach(checkbox => {
        documentsCoches.push({
            id: checkbox.id.replace('doc_', ''),
            nom: checkbox.dataset.nom,
            obligatoire: checkbox.dataset.obligatoire === 'true'
        });
    });
    
    console.log('Type de demande:', typeDemande);
    console.log('Documents cochés:', documentsCoches);
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validation...';
    button.disabled = true;
    
    // Appel AJAX
    fetch(`/dossier/{{ $dossierOuvert->id }}/etape2-documents`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type_demande: typeDemande,
            documents: documentsCoches
        })
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
    const cni = document.getElementById('clientCni').value;
    
    console.log('Données récupérées:', { nom, prenom, email, telephone, dateNaissance, cni });
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validation...';
    button.disabled = true;
    
    const dataToSend = {
        nom, prenom, email, telephone, 
        date_naissance: dateNaissance, 
        cni
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
    const mode = document.getElementById('modePaiement').value;
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
    formData.append('mode_paiement', mode);
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
    
    // Mettre à jour la barre de progression
    if (progression !== undefined) {
        const progressBar = document.querySelector('.bg-gradient-to-r');
        if (progressBar) {
            progressBar.style.width = `${progression}%`;
        }
        
        const progressText = document.querySelector('.text-blue-600');
        if (progressText) {
            progressText.textContent = `${progression}%`;
        }
    }
    
    // Mettre à jour le statut de l'étape spécifique
    if (etape === 2) {
        updateEtape2Status(valide);
    } else if (etape === 3) {
        updateEtape3Status(valide);
    } else if (etape === 4) {
        updateEtape4Status(valide);
    }
    
    // Ne plus recharger la page automatiquement
    // setTimeout(() => {
    //     location.reload();
    // }, 1500);
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
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Documents vérifiés';
        button.className = 'w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium cursor-not-allowed';
        button.disabled = true;
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
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Informations validées';
        button.className = 'w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium cursor-not-allowed';
        button.disabled = true;
    }
    
    // Afficher les informations client vérifiées
    if (valide) {
        updateClientInfoDisplay();
    }
}

function updateClientInfoDisplay() {
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
    const cni = document.getElementById('clientCni')?.value || '{{ $dossierOuvert->rendezVous->client->numero_piece_identite ?? "" }}';
    
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
                
                ${cni ? `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-id-card text-purple-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">CNI/Passport</span>
                        <p class="text-sm font-medium text-gray-900">${cni}</p>
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
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Paiement vérifié';
        button.className = 'w-full bg-green-100 text-green-800 px-4 py-3 rounded-lg font-medium cursor-not-allowed';
        button.disabled = true;
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
    const mode = document.getElementById('modePaiement')?.value || 'Non spécifié';
    
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
                    <i class="fas fa-credit-card text-orange-500 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-500">Mode de paiement</span>
                        <p class="text-sm font-medium text-gray-900">${mode}</p>
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

// Fonctions de notification
function showSuccessToast(message) {
    // Créer un toast de succès
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function showErrorToast(message) {
    // Créer un toast d'erreur
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endsection