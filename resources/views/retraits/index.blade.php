@extends('layouts.dashboard')

@section('title', 'Retraits de carte')
@section('subtitle', 'Gestion de la remise physique et Stocks')

@section('content')
<div class="space-y-6" x-data="{ showFinalModal: false, retraitId: null, clientNom: '' }">
    
    <!-- Zone Stock & Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Stock CNI -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-id-card text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-black uppercase tracking-wider">Stock CNI</p>
                <div class="flex items-baseline">
                    <span class="text-2xl font-black text-gray-800">{{ $stocks['CNI']->quantite ?? 0 }}</span>
                    <span class="ml-1 text-[10px] text-gray-400 font-bold italic">cartes</span>
                </div>
            </div>
        </div>

        <!-- Stock Résident -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-id-badge text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-black uppercase tracking-wider">Stock Résident</p>
                <div class="flex items-baseline">
                    <span class="text-2xl font-black text-gray-800">{{ $stocks['Résident']->quantite ?? 0 }}</span>
                    <span class="ml-1 text-[10px] text-gray-400 font-bold italic">cartes</span>
                </div>
            </div>
        </div>

        <!-- Stats Retraits du jour -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-check-double text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-black uppercase tracking-wider">Retraits Jour</p>
                <p class="text-2xl font-black text-gray-800">{{ $retraits->where('statut', 'termine')->where('updated_at', '>=', today())->count() }}</p>
            </div>
        </div>

        <!-- Action Stock -->
        <div class="flex items-center">
            <a href="{{ route('retraits.stock') }}" class="w-full bg-gray-800 text-white rounded-2xl p-5 flex items-center justify-between hover:bg-gray-900 transition-all shadow-lg shadow-gray-200">
                <div>
                    <p class="text-xs opacity-70 font-bold uppercase">Gérer le stock</p>
                    <p class="text-sm font-black">Réception de cartes</p>
                </div>
                <i class="fas fa-boxes text-2xl opacity-50"></i>
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('retraits.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un client, récépissé..." class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-mayelia-500 font-medium">
            </div>
            <div>
                <select name="statut" class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-mayelia-500 font-medium text-gray-500">
                    <option value="">Tous les statuts</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="terminé" {{ request('statut') == 'terminé' ? 'selected' : '' }}>Terminé</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-mayelia-600 text-white rounded-xl font-bold hover:bg-mayelia-700 transition-all">
                    Filtrer
                </button>
                <a href="{{ route('retraits.index') }}" class="px-4 py-3 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-black text-gray-800 text-lg uppercase tracking-tight">Cahier de Retraits</h3>
            <a href="{{ route('retraits.create') }}" class="px-6 py-2.5 bg-mayelia-600 text-white rounded-xl font-black text-sm hover:bg-mayelia-700 shadow-lg shadow-mayelia-100 transition-all">
                <i class="fas fa-plus mr-2"></i> Nouveau Retrait
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] font-black uppercase text-gray-400 tracking-widest border-b border-gray-100">
                        <th class="px-6 py-4">Client / Contact</th>
                        <th class="px-6 py-4">Récépissé / Type</th>
                        <th class="px-6 py-4">Status / Stock Impact</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($retraits as $retrait)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-gray-800 uppercase">{{ $retrait->client->nom_complet }}</span>
                                <span class="text-[11px] font-bold text-mayelia-600">{{ $retrait->client->telephone }}</span>
                                @if($retrait->ticket)
                                    <span class="text-[9px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded mt-1 w-max font-black tracking-tighter">Ticket QMS: {{ $retrait->ticket->numero }}</span>
                                @else
                                    <span class="text-[9px] bg-gray-50 text-gray-400 px-1.5 py-0.5 rounded mt-1 w-max font-bold">Sans ticket</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-gray-700 tracking-tight">{{ $retrait->numero_recepisse }}</span>
                                <span class="text-[10px] text-gray-400 font-bold italic">{{ $retrait->type_piece }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($retrait->statut === 'termine')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-green-100 text-green-700 uppercase">
                                    <i class="fas fa-check-circle mr-1"></i> Remis
                                </span>
                                <div class="text-[9px] text-gray-400 mt-1 font-bold">Pièce: {{ $retrait->numero_piece_finale }}</div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-orange-100 text-orange-700 uppercase">
                                    <i class="fas fa-clock mr-1"></i> En attente remise
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                @if($retrait->statut === 'en_cours')
                                    <a href="{{ route('retraits.traitement', $retrait) }}" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-mayelia-50 hover:text-mayelia-600 transition-colors" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button @click="showFinalModal = true; retraitId = '{{ $retrait->id }}'; clientNom = '{{ addslashes($retrait->client->nom_complet) }}'" 
                                            class="px-4 py-2 bg-orange-600 text-white rounded-xl font-black text-[10px] uppercase hover:bg-orange-700 shadow-md transition-all">
                                        Remettre la carte
                                    </button>
                                @else
                                    <span class="text-[10px] text-gray-400 italic">Clôturé le {{ $retrait->updated_at->format('d/m à H:i') }}</span>
                                @endif

                                @isAdmin
                                <form action="{{ route('retraits.destroy', $retrait) }}" method="POST" onsubmit="return confirm('Supprimer ce retrait ?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endisAdmin
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <i class="fas fa-folder-open text-4xl text-gray-100 mb-4 block"></i>
                            <p class="text-gray-400 font-bold uppercase text-xs tracking-widest">Aucun historique de retrait</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($retraits->hasPages())
        <div class="p-6 bg-gray-50 border-t border-gray-100">
            {{ $retraits->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Finalisation (Inspiré du précédent mais avec déstockage) -->
    <div x-show="showFinalModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-900 opacity-60 transition-opacity" @click="showFinalModal = false"></div>
            
            <div class="bg-white rounded-3xl shadow-2xl relative z-10 w-full max-w-md overflow-hidden transform transition-all border border-gray-100">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 text-white text-center">
                    <i class="fas fa-id-card-alt text-4xl mb-2 opacity-50"></i>
                    <h3 class="text-xl font-black uppercase tracking-tight">Finaliser la remise</h3>
                    <p class="text-xs text-orange-100 font-bold uppercase opacity-80" x-text="clientNom"></p>
                </div>

                <form :action="`/retraits/${retraitId}/finaliser`" method="POST" class="p-8 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-wider">Numéro de la carte finale</label>
                        <input type="text" name="numero_piece" required class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-500 font-black text-gray-800 uppercase" placeholder="EX: CNI-XXXXXXX">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-wider">Date d'expiration</label>
                        <input type="date" name="date_expiration" required class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-500 font-bold text-gray-700">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 tracking-wider">Confirmer téléphone</label>
                        <input type="tel" name="telephone" required class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-500 font-black text-orange-600">
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-orange-700 shadow-xl shadow-orange-100 transition-all flex items-center justify-center">
                            <i class="fas fa-check-circle mr-2 text-lg"></i> Valider et Déstocker
                        </button>
                        <button type="button" @click="showFinalModal = false" class="w-full py-3 text-gray-400 font-bold uppercase text-[10px] hover:text-gray-600 transition-all">
                            Annuler l'opération
                        </button>
                    </div>
                </form>

                <div class="bg-orange-50 p-4 border-t border-orange-100 flex items-center justify-center gap-3">
                    <i class="fas fa-exclamation-triangle text-orange-400"></i>
                    <p class="text-[9px] text-orange-700 font-black uppercase tracking-tight">Cette action déduira 1 unité du stock physique du centre.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
