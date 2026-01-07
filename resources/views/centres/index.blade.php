@extends('layouts.dashboard')

@section('title', 'Configuration des services & formules')
@section('subtitle', 'Gérez l\'activation des services et formules pour votre centre')

@section('header-actions')
<div class="flex items-center space-x-4">
    <div class="relative">
        <input type="text" id="searchServices" placeholder="Rechercher un service..." 
               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-transparent">
        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Informations du centre -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
            <div class="flex items-center">
                <i class="fas fa-building text-mayelia-600 text-xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">{{ $centre->nom }}</h3>
            </div>
            @isAdmin
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('qms.display', $centre) }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors text-sm">
                    <i class="fas fa-tv mr-2"></i>
                    Écran TV
                </a>
                <a href="{{ route('admin.centres.qms.edit', $centre) }}" 
                   class="inline-flex items-center px-4 py-2 bg-mayelia-600 hover:bg-mayelia-700 text-white font-medium rounded-lg transition-colors text-sm">
                    <i class="fas fa-users-cog mr-2"></i>
                    Paramètres QMS
                </a>
            </div>
            @endisAdmin
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
            <div>
                <i class="fas fa-map-marker-alt mr-2"></i>
                {{ $centre->adresse }}
            </div>
            <div>
                <i class="fas fa-phone mr-2"></i>
                {{ $centre->telephone }}
            </div>
            <div>
                <i class="fas fa-envelope mr-2"></i>
                {{ $centre->email }}
            </div>
            <div>
                <i class="fas fa-city mr-2"></i>
                {{ $centre->ville->nom }}
            </div>
        </div>
    </div>

    <!-- Configuration TV (Slider) -->
    @isAdmin
    <div class="bg-white rounded-lg shadow p-6 mb-6" x-data="tvAdmin()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-images text-mayelia-600 text-xl mr-3"></i>
                Configuration Écran TV (Mode Attente)
            </h3>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500" x-text="config.enabled ? 'Activé' : 'Désactivé'"></span>
                <button @click="toggleEnabled" 
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:ring-offset-2"
                        :class="config.enabled ? 'bg-mayelia-600' : 'bg-gray-200'">
                    <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                          :class="config.enabled ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Paramètres -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Intervalle (millisecondes)</label>
                    <div class="flex items-center space-x-2">
                        <input type="number" x-model="config.interval" @change="updateSettings" min="1000" step="500" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-mayelia-500 focus:ring-mayelia-500 sm:text-sm">
                        <span class="text-gray-500 text-sm">ms</span>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Fonctionnement</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Le slider s'activera automatiquement sur la TV lorsque :</p>
                                <ul class="list-disc pl-5 space-y-1 mt-1">
                                    <li>L'option "Activé" est cochée</li>
                                    <li>Tous les guichets ouverts sont occupés</li>
                                    <li>Aucun nouvel appel n'est en cours (flash)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload & Galerie -->
            <div class="col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700">Diapositives TV</label>
                    <button @click="$refs.fileInput.click()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-mayelia-600 hover:bg-mayelia-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mayelia-500">
                        <i class="fas fa-plus mr-1"></i> Ajouter une image
                    </button>
                    <input x-ref="fileInput" type="file" class="hidden" accept="image/*" @change="uploadImage">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <template x-for="(img, index) in config.images" :key="index">
                        <div class="group relative aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                            <img :src="img" class="object-cover w-full h-full">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button @click="deleteImage(img)" class="text-white bg-red-600 hover:bg-red-700 p-2 rounded-full transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Empty State -->
                    <div x-show="config.images.length === 0" class="col-span-full border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
                        <i class="fas fa-images text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">Aucune diapositive configurée</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification -->
        <div x-show="notification.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed bottom-4 right-4 z-50">
            <div class="rounded-lg shadow-lg p-4 text-white" :class="notification.type === 'success' ? 'bg-green-600' : 'bg-red-600'">
                <div class="flex items-center">
                    <i class="fas" :class="notification.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                    <span class="ml-2 font-medium" x-text="notification.message"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration du Scan -->
    <div class="bg-white rounded-lg shadow p-6 mb-6" x-data="scanAdmin()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-print text-mayelia-600 text-xl mr-3"></i>
                Configuration du Scan de Documents
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <label class="block text-sm font-medium text-gray-700">Méthode de capture active</label>
                <div class="grid grid-cols-1 gap-3">
                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" :class="config.mode === 'manuel' ? 'border-mayelia-500 ring-2 ring-mayelia-500' : 'border-gray-300'">
                        <input type="radio" name="scan_mode" value="manuel" x-model="config.mode" @change="updateSettings" class="sr-only">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">Upload Manuel (Ancienne méthode)</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500">L'agent doit scanner sur son PC, sauvegarder le fichier, puis le sélectionner.</span>
                            </span>
                        </span>
                        <i class="fas fa-check-circle text-mayelia-600" x-show="config.mode === 'manuel'"></i>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" :class="config.mode === 'python' ? 'border-mayelia-500 ring-2 ring-mayelia-500' : 'border-gray-300'">
                        <input type="radio" name="scan_mode" value="python" x-model="config.mode" @change="updateSettings" class="sr-only">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">Scan Direct (Pont Python)</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500">Scan automatique depuis l'imprimante via le logiciel pont installé sur le PC.</span>
                            </span>
                        </span>
                        <i class="fas fa-check-circle text-mayelia-600" x-show="config.mode === 'python'"></i>
                    </label>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-amber-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-amber-800">Note Importante</h3>
                        <div class="mt-2 text-sm text-amber-700">
                            <p>Le mode <strong>Scan Direct</strong> nécessite que le script <code>scan_bridge.py</code> (ou son exécutable) soit lancé sur le PC de l'agent. Sinon, le bouton de scan affichera une erreur de connexion.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification -->
        <div x-show="notification.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed bottom-4 right-4 z-50">
            <div class="rounded-lg shadow-lg p-4 text-white" :class="notification.type === 'success' ? 'bg-green-600' : 'bg-red-600'">
                <div class="flex items-center">
                    <i class="fas" :class="notification.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                    <span class="ml-2 font-medium" x-text="notification.message"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function scanAdmin() {
            return {
                config: { mode: 'manuel' },
                notification: { show: false, message: '', type: 'success' },
                init() {
                    let saved = @json($centre->options_scan ?? ['mode' => 'manuel']);
                    this.config.mode = saved.mode || 'manuel';
                },
                updateSettings() {
                    fetch('{{ route("centres.update-scan-options") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ mode: this.config.mode })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.showNotification('Mode de scan mis à jour : ' + (this.config.mode === 'python' ? 'Direct' : 'Manuel'));
                        }
                    })
                    .catch(() => this.showNotification('Erreur lors de la sauvegarde', 'error'));
                },
                showNotification(msg, type = 'success') {
                    this.notification = { show: true, message: msg, type: type };
                    setTimeout(() => this.notification.show = false, 3000);
                }
            }
        }

        function tvAdmin() {
            return {
                config: {
                    enabled: false,
                    interval: 4000,
                    images: []
                },
                notification: { show: false, message: '', type: 'success' },

                init() {
                    // Initialiser avec les données serveur si disponibles
                    this.config = @json($centre->options_tv ?? ['enabled' => false, 'interval' => 4000, 'images' => []]);
                    // S'assurer que les valeurs par défaut sont là
                    if (!this.config.interval) this.config.interval = 4000;
                    if (!this.config.images) this.config.images = [];
                },

                toggleEnabled() {
                    this.config.enabled = !this.config.enabled;
                    this.updateSettings();
                },

                updateSettings() {
                    fetch('{{ route("centres.update-tv-options") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            enabled: this.config.enabled,
                            interval: this.config.interval
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.showNotification('Paramètres mis à jour');
                        }
                    })
                    .catch(() => this.showNotification('Erreur lors de la sauvegarde', 'error'));
                },

                uploadImage(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route("centres.upload-slide") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.config.images = data.options.images;
                            this.showNotification('Diapositive ajoutée');
                        } else {
                            this.showNotification(data.message, 'error');
                        }
                    })
                    .catch(() => this.showNotification('Erreur upload', 'error'))
                    .finally(() => {
                        e.target.value = ''; // Reset input
                    });
                },

                deleteImage(imgUrl) {
                    if(!confirm('Supprimer cette diapositive ?')) return;

                    fetch('{{ route("centres.delete-slide") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ image_url: imgUrl })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.config.images = data.options.images; // Mise à jour depuis le serveur pour être sûr
                            this.showNotification('Diapositive supprimée');
                        }
                    });
                },

                showNotification(msg, type = 'success') {
                    this.notification = { show: true, message: msg, type: type };
                    setTimeout(() => this.notification.show = false, 3000);
                }
            }
        }
    </script>
    @endisAdmin
    @if($servicesGlobaux->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="servicesContainer">
            @foreach($servicesGlobaux as $service)
                @php
                    $serviceActive = $servicesActives->contains('id', $service->id);
                    $formulesService = $formulesGlobales->where('service_id', $service->id);
                @endphp
                
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 service-card" data-service-name="{{ strtolower($service->nom) }}">
                    <!-- En-tête de la carte service -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service->nom }}</h3>
                                <p class="text-sm text-gray-600 mb-3">{{ $service->description }}</p>
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span><i class="fas fa-clock mr-1"></i>{{ $service->duree_rdv }} min</span>
                                </div>
                            </div>
                            
                            <!-- Toggle principal du service -->
                            <div class="ml-4">
                                @isAdmin
                                <form method="POST" action="{{ route('centres.toggle-service', $service) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="actif" value="{{ $serviceActive ? '0' : '1' }}">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" {{ $serviceActive ? 'checked' : '' }} 
                                               onchange="this.form.submit()">
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-mayelia-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-mayelia-600"></div>
                                    </label>
                                </form>
                                @else
                                <div class="px-3 py-1 text-xs font-medium rounded-full {{ $serviceActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $serviceActive ? 'Actif' : 'Inactif' }}
                                </div>
                                @endisAdmin
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formules du service -->
                    <div class="p-6">
                        @if($formulesService->count() > 0)
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Formules disponibles :</h4>
                            <div class="space-y-3">
                                @foreach($formulesService as $formule)
                                    @php
                                        $formuleActive = $formulesActives->contains('id', $formule->id);
                                    @endphp
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $formule->couleur }}"></div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">{{ $formule->nom }}</span>
                                                <span class="text-xs text-gray-500 ml-2">{{ number_format($formule->prix, 2) }} XOF</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Toggle de la formule -->
                                        @isAdmin
                                        <form method="POST" action="{{ route('centres.toggle-formule', $formule) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="actif" value="{{ $formuleActive ? '0' : '1' }}">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer" {{ $formuleActive ? 'checked' : '' }} 
                                                       onchange="this.form.submit()" {{ !$serviceActive ? 'disabled' : '' }}>
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-mayelia-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-mayelia-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                                            </label>
                                        </form>
                                        @else
                                        <div class="px-2 py-1 text-xs font-medium rounded-full {{ $formuleActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $formuleActive ? 'Actif' : 'Inactif' }}
                                        </div>
                                        @endisAdmin
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">Aucune formule disponible pour ce service</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-concierge-bell text-gray-300 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun service disponible</h3>
            <p class="text-gray-600">Contactez l'administrateur général pour ajouter des services.</p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchServices');
    const serviceCards = document.querySelectorAll('.service-card');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        serviceCards.forEach(card => {
            const serviceName = card.getAttribute('data-service-name');
            if (serviceName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
