@extends('layouts.dashboard')

@section('title', 'Gestion des Guichets')
@section('subtitle', 'Configurez et gérez les guichets de votre centre.')

@section('header-actions')
<button onclick="openCreateModal()" class="bg-mayelia-600 hover:bg-mayelia-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-all shadow-sm">
    <i class="fas fa-plus"></i>
    <span>Nouveau Guichet</span>
</button>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Liste des Guichets -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($guichets as $guichet)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-mayelia-100 text-mayelia-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-desktop text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $guichet->nom }}</h3>
                                <p class="text-sm text-gray-500">{{ $guichet->centre->nom }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="openEditModal({{ $guichet->toJson() }})" class="p-2 text-gray-400 hover:text-mayelia-600 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.guichets.destroy', $guichet) }}" method="POST" onsubmit="return confirm('Supprimer ce guichet ?')">
                                @csrf
                                @method('DELETE')
                                <button class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <!-- Agent Assigné -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-user-tie text-gray-400"></i>
                                <span class="text-sm font-medium text-gray-700">Agent</span>
                            </div>
                            <span class="text-sm {{ $guichet->agent ? 'text-mayelia-600 font-bold' : 'text-gray-400 italic' }}">
                                {{ $guichet->agent ? $guichet->agent->nom_complet : 'Aucun' }}
                            </span>
                        </div>

                        <!-- Statut -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-power-off text-gray-400"></i>
                                <span class="text-sm font-medium text-gray-700">Statut</span>
                            </div>
                            @php
                                $statusColors = [
                                    'ouvert' => 'bg-green-100 text-green-700',
                                    'fermé' => 'bg-red-100 text-red-700',
                                    'pause' => 'bg-yellow-100 text-yellow-700',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $statusColors[$guichet->statut] ?? 'bg-gray-100' }}">
                                {{ strtoupper($guichet->statut) }}
                            </span>
                        </div>

                        <!-- Services -->
                        <div class="mt-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Services autorisés</p>
                            <div class="flex flex-wrap gap-2">
                                @if($guichet->type_services)
                                    @foreach($guichet->type_services as $serviceId)
                                        @php $service = $services->firstWhere('id', $serviceId); @endphp
                                        @if($service)
                                            <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-[10px] font-bold border border-blue-100">
                                                {{ $service->nom }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-xs text-gray-400 italic">Tous les services</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center">
                    <button onclick="toggleGuichetStatus({{ $guichet->id }})" id="btn-toggle-{{ $guichet->id }}" 
                        class="text-xs font-bold py-1 px-3 border border-gray-200 rounded-md hover:bg-white transition-colors">
                        {{ $guichet->statut === 'ouvert' ? 'FERMER LE GUICHET' : 'OUVRIR LE GUICHET' }}
                    </button>
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 rounded-full {{ $guichet->statut === 'ouvert' ? 'bg-green-500 animate-pulse' : 'bg-gray-300' }}"></div>
                        <span class="text-[10px] text-gray-500 font-bold">{{ $guichet->statut === 'ouvert' ? 'EN LIGNE' : 'HORS LIGNE' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-xl border-2 border-dashed border-gray-200 text-center">
                <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-desktop text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Aucun guichet configuré</h3>
                <p class="text-gray-500 mb-6">Commencez par créer votre premier guichet pour le QMS.</p>
                <button onclick="openCreateModal()" class="bg-mayelia-600 text-white px-6 py-2 rounded-lg font-bold">
                    Créer un guichet
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Création -->
<div id="createModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        <form action="{{ route('admin.guichets.store') }}" method="POST">
            @csrf
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Nouveau Guichet</h3>
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nom du Guichet</label>
                    <input type="text" name="nom" required placeholder="Ex: Guichet 1"
                        class="w-full rounded-lg border-gray-300 focus:border-mayelia-500 focus:ring-mayelia-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Centre</label>
                    <select name="centre_id" required class="w-full rounded-lg border-gray-300 focus:border-mayelia-500 focus:ring-mayelia-500">
                        @foreach($centres as $centre)
                            <option value="{{ $centre->id }}">{{ $centre->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Agent Assigné (Facultatif)</label>
                    <select name="user_id" class="w-full rounded-lg border-gray-300 focus:border-mayelia-500 focus:ring-mayelia-500">
                        <option value="">-- Aucun agent --</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->nom_complet }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Services Autorisés</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($services as $service)
                            <label class="flex items-center space-x-2 p-2 border border-gray-100 rounded hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="type_services[]" value="{{ $service->id }}" class="rounded text-mayelia-600 focus:ring-mayelia-500">
                                <span class="text-xs text-gray-700">{{ $service->nom }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">Annuler</button>
                <button type="submit" class="px-6 py-2 bg-mayelia-600 text-white rounded-lg font-bold shadow-sm hover:bg-mayelia-700">Créer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Édition -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Modifier le Guichet</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nom du Guichet</label>
                    <input type="text" name="nom" id="edit_nom" required
                        class="w-full rounded-lg border-gray-300 focus:border-mayelia-500 focus:ring-mayelia-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Agent Assigné</label>
                    <select name="user_id" id="edit_user_id" class="w-full rounded-lg border-gray-300 focus:border-mayelia-500 focus:ring-mayelia-500">
                        <option value="">-- Aucun agent --</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->nom_complet }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="edit_statut" required class="w-full rounded-lg border-gray-300 focus:border-mayelia-500 focus:ring-mayelia-500">
                        <option value="ouvert">Ouvert</option>
                        <option value="fermé">Fermé</option>
                        <option value="pause">En pause</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Services Autorisés</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($services as $service)
                            <label class="flex items-center space-x-2 p-2 border border-gray-100 rounded hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="type_services[]" value="{{ $service->id }}" class="edit_service_checkbox rounded text-mayelia-600 focus:ring-mayelia-500">
                                <span class="text-xs text-gray-700">{{ $service->nom }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">Annuler</button>
                <button type="submit" class="px-6 py-2 bg-mayelia-600 text-white rounded-lg font-bold shadow-sm hover:bg-mayelia-700">Sauvegarder</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }
    
    function openEditModal(guichet) {
        const form = document.getElementById('editForm');
        form.action = `/admin/guichets/${guichet.id}`;
        document.getElementById('edit_nom').value = guichet.nom;
        document.getElementById('edit_user_id').value = guichet.user_id || '';
        document.getElementById('edit_statut').value = guichet.statut;
        
        // Cocher les services
        document.querySelectorAll('.edit_service_checkbox').forEach(cb => {
            cb.checked = guichet.type_services && guichet.type_services.includes(cb.value);
        });
        
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function toggleGuichetStatus(id) {
        const btn = document.getElementById(`btn-toggle-${id}`);
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mise à jour...';

        fetch(`/admin/guichets/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            btn.disabled = false;
            btn.innerText = 'ERREUR';
        });
    }
</script>
@endpush
@endsection
