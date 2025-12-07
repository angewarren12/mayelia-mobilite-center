<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert de dossiers</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #2563eb;">MAYELIA MOBILITÉ</h1>
        </div>

        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="color: #1f2937; margin-top: 0;">Transfert de dossiers à l'ONECI</h2>
            
            <p>Bonjour,</p>
            
            <p>Vous trouverez en pièce jointe le récapitulatif détaillé du transfert <strong>{{ $transfer->code_transfert }}</strong>.</p>
            
            <div style="background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 5px 0;"><strong>Code transfert :</strong> {{ $transfer->code_transfert }}</p>
                <p style="margin: 5px 0;"><strong>Centre Mayelia :</strong> {{ $transfer->centre->nom ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Date d'envoi :</strong> {{ $transfer->date_envoi->format('d/m/Y') }}</p>
                <p style="margin: 5px 0;"><strong>Nombre de dossiers :</strong> {{ $transfer->nombre_dossiers }}</p>
                @if($transfer->agentMayelia)
                <p style="margin: 5px 0;"><strong>Agent Mayelia :</strong> {{ $transfer->agentMayelia->nom_complet }}</p>
                @endif
            </div>

            <p>Le PDF en pièce jointe contient pour chaque dossier :</p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Le code-barres unique du dossier</li>
                <li>Les informations du client (nom, prénom, email, téléphone)</li>
                <li>Le service et la formule demandés</li>
                <li>La liste détaillée des documents vérifiés</li>
                <li>Les informations de paiement</li>
                <li>Le récapitulatif complet du workflow effectué à Mayelia</li>
            </ul>

            <p style="margin-top: 20px;">Veuillez traiter ces dossiers et nous appeler au centre <strong>{{ $transfer->centre->nom ?? 'N/A' }}</strong> 
            ({{ $transfer->centre->telephone ?? 'N/A' }}) lorsque les cartes seront prêtes pour récupération.</p>

            <p style="margin-top: 20px;">Cordialement,<br>
            <strong>L'équipe Mayelia Mobilité</strong></p>
        </div>

        @if($transfer->notes)
        <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">
            <p style="margin: 0;"><strong>Notes :</strong> {{ $transfer->notes }}</p>
        </div>
        @endif
    </div>
</body>
</html>


