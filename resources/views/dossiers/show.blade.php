@extends('layouts.dashboard')

@section('title', 'Détails du Dossier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Dossier #{{ $dossier->id }}</h3>
                    <a href="{{ route('dossiers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <!-- Informations du client et du rendez-vous -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informations Client</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nom :</strong></td>
                                    <td>{{ $dossier->rendezVous->client->nom_complet ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email :</strong></td>
                                    <td>{{ $dossier->rendezVous->client->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Téléphone :</strong></td>
                                    <td>{{ $dossier->rendezVous->client->telephone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date de naissance :</strong></td>
                                    <td>{{ $dossier->rendezVous->client->date_naissance ? $dossier->rendezVous->client->date_naissance->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informations Rendez-vous</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Service :</strong></td>
                                    <td>{{ $dossier->rendezVous->service->nom ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Formule :</strong></td>
                                    <td>{{ $dossier->rendezVous->formule->nom ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date :</strong></td>
                                    <td>{{ $dossier->rendezVous->date_rendez_vous->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Heure :</strong></td>
                                    <td>{{ $dossier->rendezVous->tranche_horaire }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Numéro de suivi :</strong></td>
                                    <td>{{ $dossier->rendezVous->numero_suivi ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Statut du dossier -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Statut du Dossier</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h6>Statut</h6>
                                            <span class="badge bg-{{ $dossier->statut === 'valide' ? 'success' : ($dossier->statut === 'ouvert' ? 'warning' : 'secondary') }} fs-6">
                                                {{ ucfirst($dossier->statut) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h6>Paiement</h6>
                                            @if($dossier->paiement_effectue)
                                                <span class="badge bg-success fs-6">
                                                    <i class="fas fa-check"></i> Payé
                                                </span>
                                            @else
                                                <span class="badge bg-danger fs-6">
                                                    <i class="fas fa-times"></i> Non payé
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h6>Biométrie</h6>
                                            @if($dossier->biometrie_passee)
                                                <span class="badge bg-success fs-6">
                                                    <i class="fas fa-check"></i> Passée
                                                </span>
                                            @else
                                                <span class="badge bg-warning fs-6">
                                                    <i class="fas fa-clock"></i> En attente
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h6>Agent</h6>
                                            <small>{{ $dossier->agent->nom_complet ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vérification des documents -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Vérification des Documents</h5>
                            <form id="documents-form">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document</th>
                                                <th>Type de Demande</th>
                                                <th>Obligatoire</th>
                                                <th>Vérifié</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($documentsRequis as $document)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $document->nom_document }}</strong>
                                                        @if($document->description)
                                                            <br><small class="text-muted">{{ $document->description }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $document->type_demande }}</td>
                                                    <td>
                                                        @if($document->obligatoire)
                                                            <span class="badge bg-danger">Obligatoire</span>
                                                        @else
                                                            <span class="badge bg-secondary">Optionnel</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input document-check" 
                                                                   type="checkbox" 
                                                                   name="documents_verifies[{{ $document->id }}]"
                                                                   value="1"
                                                                   {{ isset($dossier->documents_verifies[$document->id]) && $dossier->documents_verifies[$document->id] ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" 
                                                               name="notes_{{ $document->id }}" 
                                                               placeholder="Notes...">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes_documents_manquants" class="form-label">Notes générales sur les documents manquants</label>
                                    <textarea class="form-control" id="notes_documents_manquants" name="notes_documents_manquants" rows="3">{{ $dossier->notes_documents_manquants }}</textarea>
                                </div>

                                <button type="button" class="btn btn-primary" onclick="updateDocuments()">
                                    <i class="fas fa-save"></i> Mettre à jour les documents
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Informations de paiement -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Informations de Paiement</h5>
                            <form id="payment-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="paiement_effectue" name="paiement_effectue" 
                                                   {{ $dossier->paiement_effectue ? 'checked' : '' }}>
                                            <label class="form-check-label" for="paiement_effectue">
                                                Paiement effectué
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="reference_paiement" class="form-label">Référence de paiement</label>
                                            <input type="text" class="form-control" id="reference_paiement" name="reference_paiement" 
                                                   value="{{ $dossier->reference_paiement }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="montant_paiement" class="form-label">Montant (FCFA)</label>
                                            <input type="number" class="form-control" id="montant_paiement" name="montant_paiement" 
                                                   value="{{ $dossier->montant_paiement }}" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success" onclick="updatePayment()">
                                    <i class="fas fa-save"></i> Mettre à jour le paiement
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Biométrie -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Biométrie</h5>
                            <form id="biometrie-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="biometrie_passee" name="biometrie_passee" 
                                                   {{ $dossier->biometrie_passee ? 'checked' : '' }}>
                                            <label class="form-check-label" for="biometrie_passee">
                                                Biométrie passée
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-info" onclick="updateBiometrie()">
                                            <i class="fas fa-save"></i> Mettre à jour la biométrie
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <h5>Actions</h5>
                            <div class="btn-group" role="group">
                                @if($dossier->statut === 'ouvert')
                                    <button type="button" class="btn btn-success" onclick="validateDossier()">
                                        <i class="fas fa-check"></i> Valider le dossier
                                    </button>
                                @endif
                                <button type="button" class="btn btn-warning" onclick="showRescheduleModal()">
                                    <i class="fas fa-calendar-alt"></i> Reprogrammer
                                </button>
                                <button type="button" class="btn btn-primary" onclick="printDossier()">
                                    <i class="fas fa-print"></i> Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.toast')
@endsection

@push('scripts')
<script>
function updateDocuments() {
    const form = document.getElementById('documents-form');
    const formData = new FormData(form);
    
    fetch(`/dossiers/{{ $dossier->id }}/update-documents`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur lors de la mise à jour', 'error');
    });
}

function updatePayment() {
    const form = document.getElementById('payment-form');
    const formData = new FormData(form);
    
    fetch(`/dossiers/{{ $dossier->id }}/update-payment`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur lors de la mise à jour', 'error');
    });
}

function updateBiometrie() {
    const form = document.getElementById('biometrie-form');
    const formData = new FormData(form);
    
    fetch(`/dossiers/{{ $dossier->id }}/update-biometrie`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur lors de la mise à jour', 'error');
    });
}

function validateDossier() {
    if (confirm('Êtes-vous sûr de vouloir valider ce dossier ?')) {
        fetch(`/dossiers/{{ $dossier->id }}/validate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                location.reload();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de la validation', 'error');
        });
    }
}

function showRescheduleModal() {
    // Implémenter la modal de reprogrammation
    alert('Fonctionnalité de reprogrammation à implémenter');
}

function printDossier() {
    window.print();
}
</script>
@endpush


