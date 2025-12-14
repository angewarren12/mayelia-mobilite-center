class Ticket {
  final int id;
  final String numero;
  final int centreId;
  final int? serviceId;
  final String type; // 'rdv' ou 'sans_rdv'
  final String statut; // 'en_attente', 'appelé', 'terminé', 'absent'
  final String? heureRdv;
  final int priorite;
  final DateTime createdAt;
  final DateTime? calledAt;
  final DateTime? completedAt;
  final int? guichetId;

  Ticket({
    required this.id,
    required this.numero,
    required this.centreId,
    this.serviceId,
    required this.type,
    required this.statut,
    this.heureRdv,
    required this.priorite,
    required this.createdAt,
    this.calledAt,
    this.completedAt,
    this.guichetId,
  });

  factory Ticket.fromJson(Map<String, dynamic> json) {
    return Ticket(
      id: json['id'] as int,
      numero: json['numero'] as String,
      centreId: json['centre_id'] as int,
      serviceId: json['service_id'] as int?,
      type: json['type'] as String,
      statut: json['statut'] as String,
      heureRdv: json['heure_rdv'] as String?,
      priorite: json['priorite'] as int? ?? 1,
      createdAt: DateTime.parse(json['created_at'] as String),
      calledAt: json['called_at'] != null 
          ? DateTime.parse(json['called_at'] as String)
          : null,
      completedAt: json['completed_at'] != null
          ? DateTime.parse(json['completed_at'] as String)
          : null,
      guichetId: json['guichet_id'] as int?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'numero': numero,
      'centre_id': centreId,
      'service_id': serviceId,
      'type': type,
      'statut': statut,
      'heure_rdv': heureRdv,
      'priorite': priorite,
      'created_at': createdAt.toIso8601String(),
      'called_at': calledAt?.toIso8601String(),
      'completed_at': completedAt?.toIso8601String(),
      'guichet_id': guichetId,
    };
  }
}


