class Centre {
  final int id;
  final String nom;
  final String? qmsMode; // 'fifo' ou 'fenetre_tolerance'
  final int? qmsFenetreMinutes; // Fenêtre de tolérance en minutes

  Centre({
    required this.id,
    required this.nom,
    this.qmsMode,
    this.qmsFenetreMinutes,
  });

  factory Centre.fromJson(Map<String, dynamic> json) {
    return Centre(
      id: json['id'] as int,
      nom: json['nom'] as String,
      qmsMode: json['qms_mode'] as String?,
      qmsFenetreMinutes: json['qms_fenetre_minutes'] as int?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nom': nom,
      'qms_mode': qmsMode,
      'qms_fenetre_minutes': qmsFenetreMinutes,
    };
  }
}
