<!DOCTYPE html>
<html>
<head>
    <title>Rappel de Dette</title>
</head>
<body>
    <p>Bonjour {{ $prenom }} {{ $nom }},</p>
    
    @if($photo)
        <p>Voici votre photo :</p>
        <img src="{{ url($photo) }}" alt="Photo de {{ $prenom }} {{ $nom }}" style="max-width: 150px; height: auto;">
    @endif

    <p>Nous vous rappelons que vous avez une dette impayée d'un montant total de {{ $totalDue }} Francs.</p>
    <p>Merci de régulariser votre situation dès que possible.</p>
    <p>Cordialement,<br>
    L'équipe de gestion des dettes</p>
</body>
</html>
