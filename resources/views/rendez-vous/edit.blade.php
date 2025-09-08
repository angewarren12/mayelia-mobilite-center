@extends('layouts.dashboard')

@section('title', 'Modifier le Rendez-vous')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier le Rendez-vous #{{ $rendezVous->id }}</h3>
                    <a href="{{ route('rendez-vous.index') }}" class="btn btn-secondary float-end">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('rendez-vous.update', $rendezVous) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client *</label>
                                    <select class="form-select" id="client_id" name="client_id" required>
                                        <option value="">Sélectionner un client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" 
                                                    {{ old('client_id', $rendezVous->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->nom_complet }} - {{ $client->telephone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="centre_id" class="form-label">Centre *</label>
                                    <select class="form-select" id="centre_id" name="centre_id" required>
                                        <option value="">Sélectionner un centre</option>
                                        @foreach($centres as $centre)
                                            <option value="{{ $centre->id }}" 
                                                    {{ old('centre_id', $rendezVous->centre_id) == $centre->id ? 'selected' : '' }}>
                                                {{ $centre->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_id" class="form-label">Service *</label>
                                    <select class="form-select" id="service_id" name="service_id" required>
                                        <option value="">Sélectionner un service</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" 
                                                    {{ old('service_id', $rendezVous->service_id) == $service->id ? 'selected' : '' }}>
                                                {{ $service->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="formule_id" class="form-label">Formule *</label>
                                    <select class="form-select" id="formule_id" name="formule_id" required>
                                        <option value="">Sélectionner une formule</option>
                                        @foreach($formules as $formule)
                                            <option value="{{ $formule->id }}" 
                                                    {{ old('formule_id', $rendezVous->formule_id) == $formule->id ? 'selected' : '' }}>
                                                {{ $formule->nom }} - {{ number_format($formule->prix, 0, ',', ' ') }} FCFA
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_rendez_vous" class="form-label">Date du rendez-vous *</label>
                                    <input type="date" class="form-control" id="date_rendez_vous" name="date_rendez_vous" 
                                           value="{{ old('date_rendez_vous', $rendezVous->date_rendez_vous->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tranche_horaire" class="form-label">Tranche horaire *</label>
                                    <input type="text" class="form-control" id="tranche_horaire" name="tranche_horaire" 
                                           value="{{ old('tranche_horaire', $rendezVous->tranche_horaire) }}" required
                                           placeholder="Ex: 08:00:00 - 08:15:00">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut *</label>
                                    <select class="form-select" id="statut" name="statut" required>
                                        <option value="confirme" {{ old('statut', $rendezVous->statut) == 'confirme' ? 'selected' : '' }}>Confirmé</option>
                                        <option value="annule" {{ old('statut', $rendezVous->statut) == 'annule' ? 'selected' : '' }}>Annulé</option>
                                        <option value="termine" {{ old('statut', $rendezVous->statut) == 'termine' ? 'selected' : '' }}>Terminé</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Notes additionnelles...">{{ old('notes', $rendezVous->notes) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('rendez-vous.index') }}" class="btn btn-secondary me-2">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


