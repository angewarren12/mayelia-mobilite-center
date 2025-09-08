@extends('layouts.dashboard')

@section('title', 'Détails de l\'Agent')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Détails de l'Agent : {{ $agent->nom_complet }}</h3>
                    <div>
                        <a href="{{ route('agents.edit', $agent) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('agents.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informations Personnelles</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nom :</strong></td>
                                    <td>{{ $agent->nom }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Prénom :</strong></td>
                                    <td>{{ $agent->prenom }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email :</strong></td>
                                    <td>{{ $agent->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Téléphone :</strong></td>
                                    <td>{{ $agent->telephone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td>
                                        <span class="badge {{ $agent->actif ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $agent->actif ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informations Professionnelles</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Centre :</strong></td>
                                    <td>{{ $agent->centre->nom ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dernière Connexion :</strong></td>
                                    <td>{{ $agent->derniere_connexion ? $agent->derniere_connexion->format('d/m/Y H:i') : 'Jamais' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date de Création :</strong></td>
                                    <td>{{ $agent->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dernière Modification :</strong></td>
                                    <td>{{ $agent->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($agent->dossiers->count() > 0)
                        <hr>
                        <h5>Dossiers Traités ({{ $agent->dossiers->count() }})</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Service</th>
                                        <th>Date RDV</th>
                                        <th>Statut</th>
                                        <th>Date Ouverture</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agent->dossiers->take(10) as $dossier)
                                        <tr>
                                            <td>{{ $dossier->rendezVous->client->nom_complet ?? 'N/A' }}</td>
                                            <td>{{ $dossier->rendezVous->service->nom ?? 'N/A' }}</td>
                                            <td>{{ $dossier->rendezVous->date_rendez_vous->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $dossier->statut === 'valide' ? 'success' : ($dossier->statut === 'ouvert' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($dossier->statut) }}
                                                </span>
                                            </td>
                                            <td>{{ $dossier->date_ouverture ? $dossier->date_ouverture->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($agent->dossiers->count() > 10)
                            <p class="text-muted">Affichage des 10 derniers dossiers sur {{ $agent->dossiers->count() }} au total.</p>
                        @endif
                    @else
                        <hr>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Cet agent n'a encore traité aucun dossier.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


