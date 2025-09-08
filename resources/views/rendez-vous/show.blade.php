@extends('layouts.dashboard')

@section('title', 'Détails du Rendez-vous')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Détails du Rendez-vous #{{ $rendezVous->id }}</h3>
                    <a href="{{ route('rendez-vous.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informations Client</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nom :</strong></td>
                                    <td>{{ $rendezVous->client->nom_complet ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email :</strong></td>
                                    <td>{{ $rendezVous->client->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Téléphone :</strong></td>
                                    <td>{{ $rendezVous->client->telephone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date de naissance :</strong></td>
                                    <td>{{ $rendezVous->client->date_naissance ? $rendezVous->client->date_naissance->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informations Rendez-vous</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Service :</strong></td>
                                    <td>{{ $rendezVous->service->nom ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Formule :</strong></td>
                                    <td>{{ $rendezVous->formule->nom ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Centre :</strong></td>
                                    <td>{{ $rendezVous->centre->nom ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date :</strong></td>
                                    <td>{{ $rendezVous->date_rendez_vous->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Heure :</strong></td>
                                    <td>{{ $rendezVous->tranche_horaire }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $rendezVous->statut === 'confirme' ? 'success' : ($rendezVous->statut === 'annule' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($rendezVous->statut) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Numéro de suivi :</strong></td>
                                    <td>{{ $rendezVous->numero_suivi ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($rendezVous->notes)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Notes</h5>
                                <div class="alert alert-info">
                                    {{ $rendezVous->notes }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Actions</h5>
                            <div class="btn-group" role="group">
                                @if($rendezVous->statut === 'confirme')
                                    <button type="button" class="btn btn-success open-dossier"
                                            data-rendez-vous-id="{{ $rendezVous->id }}">
                                        <i class="fas fa-folder-open"></i> Ouvrir Dossier
                                    </button>
                                @endif
                                <a href="{{ route('rendez-vous.edit', $rendezVous) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <button type="button" class="btn btn-primary" onclick="printRendezVous()">
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
document.addEventListener('DOMContentLoaded', function() {
    // Ouvrir un dossier
    document.querySelectorAll('.open-dossier').forEach(button => {
        button.addEventListener('click', function() {
            const rendezVousId = this.dataset.rendezVousId;
            
            if (confirm('Êtes-vous sûr de vouloir ouvrir un dossier pour ce rendez-vous ?')) {
                fetch(`/dossiers/open/${rendezVousId}`, {
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
                        // Rediriger vers la page du dossier
                        window.location.href = `/dossiers/${data.dossier_id}`;
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur lors de l\'ouverture du dossier', 'error');
                });
            }
        });
    });
});

function printRendezVous() {
    window.print();
}
</script>
@endpush


