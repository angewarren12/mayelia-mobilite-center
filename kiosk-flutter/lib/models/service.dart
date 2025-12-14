class Service {
  final int id;
  final String nom;
  final String? description;
  final String statut; // 'actif' ou 'inactif'

  Service({
    required this.id,
    required this.nom,
    this.description,
    required this.statut,
  });

  factory Service.fromJson(Map<String, dynamic> json) {
    return Service(
      id: json['id'] as int,
      nom: json['nom'] as String,
      description: json['description'] as String?,
      statut: json['statut'] as String? ?? 'actif',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nom': nom,
      'description': description,
      'statut': statut,
    };
  }
}


