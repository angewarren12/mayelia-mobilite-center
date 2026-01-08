@extends('layouts.dashboard')

@section('title', 'Edition Retrait')
@section('subtitle', 'Mise à jour des informations du récépissé')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('retraits.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center font-bold text-sm transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Retour au cahier
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-mayelia-600 to-mayelia-700 p-10 text-white relative">
            <div class="flex justify-between items-center relative z-10">
                <div>
                    <h3 class="text-3xl font-black mb-1 uppercase tracking-tighter">Modification Retrait</h3>
                    <div class="flex items-center text-mayelia-100 text-sm font-bold">
                        <i class="fas fa-user-circle mr-2"></i>
                        <span>Client : {{ $retrait->client->nom_complet }}</span>
                    </div>
                </div>
                @if($retrait->ticket)
                <div class="text-right">
                    <span class="text-4xl font-black opacity-30 select-none block leading-none">TICKET</span>
                    <span class="text-5xl font-black relative">{{ $retrait->ticket->numero }}</span>
                </div>
                @endif
            </div>
            <i class="fas fa-edit absolute right-[-2rem] top-[-2rem] text-[12rem] text-white opacity-5"></i>
        </div>

        <div class="p-10">
            <form action="{{ route('retraits.store-info', $retrait) }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Infos Dossier -->
                    <div class="space-y-6">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3">Informations Récépissé</h4>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Type de pièce</label>
                            <select name="type_piece" required
                                    class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-mayelia-500 transition-all font-black text-gray-800">
                                <option value="CNI" {{ $retrait->type_piece == 'CNI' ? 'selected' : '' }}>Carte Nationale d'Identité (CNI)</option>
                                <option value="Résident" {{ $retrait->type_piece == 'Résident' ? 'selected' : '' }}>Carte de Résident</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Numéro du récépissé</label>
                            <input type="text" name="numero_recepisse" value="{{ old('numero_recepisse', $retrait->numero_recepisse) }}" required
                                   class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-mayelia-500 transition-all font-black text-mayelia-700 text-lg shadow-inner">
                        </div>
                    </div>

                    <!-- Scan Récépissé -->
                    <div class="space-y-6">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3">Ressources Visuelles</h4>

                        <div class="relative group">
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-wider">Photo / Scan Actuel</label>
                            @if($retrait->scan_recepisse)
                                <div class="mb-6 relative rounded-[2rem] overflow-hidden border-4 border-white shadow-xl">
                                    <img src="{{ Storage::url($retrait->scan_recepisse) }}" alt="Récépissé" class="w-full h-48 object-cover">
                                </div>
                            @endif
                            
                            <div class="relative">
                                <input type="file" name="scan_recepisse" id="scan_recepisse" accept="image/*" class="hidden" onchange="document.getElementById('file-status').innerText = 'Fichier prêt : ' + this.files[0].name; document.getElementById('file-status').classList.remove('hidden')">
                                <button type="button" onclick="document.getElementById('scan_recepisse').click()" 
                                        class="w-full py-8 px-6 border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50 text-gray-400 hover:bg-mayelia-50 hover:border-mayelia-300 transition-all flex flex-col items-center justify-center">
                                    <i class="fas fa-camera-retro mb-3 text-3xl opacity-50"></i>
                                    <span class="text-xs font-black uppercase tracking-widest">@if($retrait->scan_recepisse) Remplacer la photo @else Ajouter une photo @endif</span>
                                </button>
                                <p id="file-status" class="mt-3 text-[10px] font-black text-mayelia-600 uppercase text-center hidden"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-6 pt-10 border-t border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold max-w-xs italic uppercase tracking-tighter">
                        Les modifications sont enregistrées immédiatement. Si le stock est vide, vous ne pourrez pas finaliser la remise physique sur l'index.
                    </p>
                    <button type="submit" class="w-full md:w-auto px-12 py-5 bg-gray-900 text-white font-black rounded-2xl hover:bg-black transition-all shadow-2xl shadow-gray-200 flex items-center justify-center uppercase tracking-widest">
                        <i class="fas fa-save mr-3"></i> Enregistrer les changements
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
