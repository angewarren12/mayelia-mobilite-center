@extends('layouts.dashboard')

@section('title', 'Modifier le Document Requis')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier le Document Requis : {{ $documentRequis->nom_document }}</h3>
                    <a href="{{ route('document-requis.index') }}" class="btn btn-secondary float-end">
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

                    <form action="{{ route('document-requis.update', $documentRequis) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_id" class="form-label">Service *</label>
                                    <select class="form-select" id="service_id" name="service_id" required>
                                        <option value="">Sélectionner un service</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" 
                                                    {{ old('service_id', $documentRequis->service_id) == $service->id ? 'selected' : '' }}>
                                                {{ $service->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_demande" class="form-label">Type de Demande *</label>
                                    <input type="text" class="form-control" id="type_demande" name="type_demande" 
                                           value="{{ old('type_demande', $documentRequis->type_demande) }}" required
                                           placeholder="Ex: Première demande, Renouvellement, Duplicata">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom_document" class="form-label">Nom du Document *</label>
                                    <input type="text" class="form-control" id="nom_document" name="nom_document" 
                                           value="{{ old('nom_document', $documentRequis->nom_document) }}" required
                                           placeholder="Ex: Carte nationale d'identité">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ordre" class="form-label">Ordre d'affichage *</label>
                                    <input type="number" class="form-control" id="ordre" name="ordre" 
                                           value="{{ old('ordre', $documentRequis->ordre) }}" required min="1">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Description détaillée du document...">{{ old('description', $documentRequis->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="obligatoire" name="obligatoire" 
                                       {{ old('obligatoire', $documentRequis->obligatoire) ? 'checked' : '' }}>
                                <label class="form-check-label" for="obligatoire">
                                    Document obligatoire
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('document-requis.index') }}" class="btn btn-secondary me-2">
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


