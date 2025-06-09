<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seus Pontos Batidos</title>
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
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
        }

        h2 {
            color: #34495e;
            font-size: 22px;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }

        /* Novo estilo para o contêiner de rolagem */
        .scrollable-hits {
            max-height: 250px;
            /* Defina a altura máxima antes da rolagem */
            overflow-y: auto;
            /* Adiciona a barra de rolagem vertical quando o conteúdo excede a altura máxima */
            border: 1px solid #e0e0e0;
            /* Opcional: Adiciona uma borda para visualmente separar a área de rolagem */
            border-radius: 5px;
            padding: 10px;
            /* Espaçamento interno */
            margin-bottom: 20px;
            /* Espaçamento após a caixa de rolagem */
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            /* Removido margin padrão do ul que pode causar problemas */
        }

        li {
            background-color: #ecf0f1;
            margin-bottom: 10px;
            padding: 12px 15px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
        }

        li:last-child {
            margin-bottom: 0;
        }

        .hit-name {
            font-weight: bold;
            color: #2980b9;
            margin-right: 4px;
        }

        .hit-date {
            color: #7f8c8d;
            font-size: 14px;
        }

        .no-hits {
            text-align: center;
            color: #7f8c8d;
            padding: 20px;
            border: 1px dashed #bdc3c7;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #95a5a6;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Relatório de Pontos Batidos</h1>

        <h2>Olá, {{ $username }}!</h2>

        @if (!empty($hits))
        <h2>Detalhes dos Seus Pontos:</h2>
        {{-- Adicionado o novo contêiner para rolagem --}}
        <div class="scrollable-hits">
            {{-- Define os rótulos em um array para facilitar a leitura --}}
            @php
            $hitLabels = [
            'Entrada',
            'Início do Almoço',
            'Fim do Almoço',
            'Saída',
            ];
            @endphp

            <ul>
                @foreach ($hits as $key => $hit)
                <li>
                    <span class="hit-name">
                        {{ $hitLabels[$key] ?? 'Observação' }}:
                    </span>
                    <span class="hit-date">{{ \Carbon\Carbon::parse($hit['date'])->format('d/m/Y H:i:s') }}</span>
                </li>
                @endforeach
            </ul>
        </div> {{-- Fim do contêiner de rolagem --}}
        @else
        <p class="no-hits">Parece que você não registrou nenhum ponto hoje.</p>
        @endif

        <p class="footer">Este é um e-mail automático. Por favor, não responda.</p>
    </div>
</body>

</html>