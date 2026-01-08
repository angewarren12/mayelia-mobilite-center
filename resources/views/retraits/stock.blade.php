@extends('layouts.dashboard')

@section('title', 'Gestion des Stocks')
@section('subtitle', 'Réception et inventaire des cartes physiques')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('retraits.index') }}" class="text-gray-500 hover:text-mayelia-600 font-bold flex items-center transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux retraits
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Inventaire Actuel -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight mb-8">Inventaire Physique</h3>
                
                <div class="space-y-4">
                    @foreach(['CNI' => 'blue', 'Résident' => 'purple'] as $type => $color)
                        @php $stock = $stocks->where('type_piece', $type)->first(); @endphp
                        <div class="p-6 bg-{{ $color }}-50 rounded-2xl border border-{{ $color }}-100 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-{{ $color }}-600 text-white rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-{{ $color }}-100">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase text-{{ $color }}-800 tracking-widest opacity-60">Cartes {{ $type }}</p>
                                    <p class="text-3xl font-black text-{{ $color }}-900 leading-none">{{ $stock->quantite ?? 0 }}</p>
                                </div>
                            </div>
                            @if(($stock->quantite ?? 0) < 10)
                                <span class="bg-red-100 text-red-600 text-[9px] font-black px-2 py-1 rounded-full uppercase animate-pulse">Stock bas</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold uppercase leading-tight">
                        <i class="fas fa-info-circle mr-1"></i> 
                        Le stock est décrémenté automatiquement à chaque fois qu'un agent finalise un retrait dans ce centre.
                    </p>
                </div>
            </div>

            <!-- Formulaire de Réception -->
            <div class="bg-gray-900 rounded-[2rem] shadow-xl p-8 text-white">
                <h3 class="text-lg font-black uppercase tracking-tight mb-6">Enregistrer un arrivage</h3>
                <form action="{{ route('retraits.stock.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Type de cartes reçues</label>
                        <select name="type_piece" required class="w-full px-4 py-3 bg-white/10 border-none rounded-xl focus:ring-2 focus:ring-mayelia-500 font-bold text-white">
                            <option value="CNI" class="text-gray-800">CNI</option>
                            <option value="Résident" class="text-gray-800">Carte de Résident</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Quantité Reçue</label>
                        <input type="number" name="quantite" min="1" required class="w-full px-4 py-3 bg-white/10 border-none rounded-xl focus:ring-2 focus:ring-mayelia-500 font-black text-white text-xl" placeholder="Ex: 50">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Date de réception</label>
                        <input type="date" name="date_reception" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-3 bg-white/10 border-none rounded-xl focus:ring-2 focus:ring-mayelia-500 font-bold text-white">
                    </div>

                    <button type="submit" class="w-full py-4 bg-mayelia-600 text-white rounded-xl font-black uppercase tracking-widest hover:bg-mayelia-700 shadow-xl shadow-mayelia-900 transition-all flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i> Ajouter au stock
                    </button>
                </form>
            </div>
        </div>

        <!-- Historique des réceptions -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50">
                    <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Historique des entrées</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-black uppercase text-gray-400 tracking-widest border-b border-gray-100">
                                <th class="px-8 py-4">Date</th>
                                <th class="px-8 py-4">Type</th>
                                <th class="px-8 py-4">Quantité</th>
                                <th class="px-8 py-4">Par</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($receptions as $reception)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-800">{{ $reception->date_reception->format('d/m/Y') }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Saisi le {{ $reception->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $reception->type_piece == 'CNI' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $reception->type_piece }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xl font-black text-gray-800">+{{ $reception->quantite }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-2 text-[10px] font-black text-gray-500 uppercase">
                                            {{ substr($reception->createur->nom, 0, 1) }}{{ substr($reception->createur->prenom, 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-gray-600">{{ $reception->createur->full_name }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-gray-400 font-bold uppercase text-xs tracking-widest">
                                    <i class="fas fa-history mb-3 block text-2xl opacity-20"></i>
                                    Aucune réception enregistrée
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($receptions->hasPages())
                <div class="p-8 bg-gray-50 border-t border-gray-100">
                    {{ $receptions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
