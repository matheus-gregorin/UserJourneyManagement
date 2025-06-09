<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Código de Acesso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            /* Centraliza o conteúdo principal */
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 28px;
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #555;
        }

        strong {
            color: #2980b9;
            /* Cor azul para o código */
            font-size: 24px;
            /* Aumenta o tamanho do código */
            display: block;
            /* Garante que o código fique em sua própria linha */
            padding: 10px 15px;
            background-color: #ecf0f1;
            border-radius: 5px;
            margin: 15px auto;
            /* Centraliza e adiciona espaço */
            max-width: fit-content;
            /* Ajusta a largura ao conteúdo */
            letter-spacing: 2px;
            /* Espaçamento entre as letras do código */
        }

        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #95a5a6;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Olá, {{ $username }}!</h1>
        <p>Seu código de acesso é:</p>
        <strong>{{ $code }}</strong>
        <p>Use este código para acessar sua conta ou confirmar sua operação.</p>
        <p class="footer">Este é um e-mail automático. Por favor, não responda.</p>
    </div>
</body>

</html>