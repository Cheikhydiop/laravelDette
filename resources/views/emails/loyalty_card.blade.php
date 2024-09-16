<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fidelity Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .card {
            border: 1px solid #000;
            padding: 20px;
            width: 300px;
            margin: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .qr-code {
            margin-top: 20px;
            width: 150px;
            height: 150px;
        }
        span {
            font-size: 25px;
            color: #be53fc;
            font-weight: bolder;
            position: absolute;
            top: 40;

            z-index: 3;
            font-size: 30px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="card">
        


        
           

        
      
        <img src="{{ $client->loyalty_card_qr_code }}" alt="QR Code pour téléchargement">

      



    </div>
</body>
</html>