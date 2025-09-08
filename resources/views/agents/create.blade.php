@extends('layouts.dashboard')

@section('title', 'Créer un Agent')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Créer un Nouvel Agent</h3>
                    <a href="{{ route('agents.index') }}" class="btn btn-secondary float-end">
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

                    <form action="{{ route('agents.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="{{ old('nom') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prenom" class="form-label">Prénom *</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" 
                                           value="{{ old('prenom') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone *</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" 
                                           value="{{ old('telephone') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="centre_id" class="form-label">Centre *</label>
                                    <select class="form-select" id="centre_id" name="centre_id" required>
                                        <option value="">Sélectionner un centre</option>
                                        @foreach($centres as $centre)
                                            <option value="{{ $centre->id }}" 
                                                    {{ old('centre_id') == $centre->id ? 'selected' : '' }}>
                                                {{ $centre->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="actif" name="actif" 
                                               {{ old('actif') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="actif">
                                            Agent actif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note :</strong> Le mot de passe par défaut sera "password123". 
                            L'agent devra le changer lors de sa première connexion.
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('agents.index') }}" class="btn btn-secondary me-2">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer l'Agent
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


