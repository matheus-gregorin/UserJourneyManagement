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

        /* Contêiner de hits: borda e padding */
        .hits-container {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }

        /* Removendo estilos para rolagem em tela para que a div cresça com o conteúdo */
        @media screen {
            .scrollable-on-screen {
                /* max-height: 250px; REMOVIDO */
                /* overflow-y: auto; REMOVIDO */
            }
        }

        /* Estilos para impressão/PDF (ignora rolagem) */
        @media print {
            .scrollable-on-screen {
                max-height: none !important;
                overflow-y: visible !important;
            }

            body {
                background-color: #fff;
                /* Fundo branco para impressão */
            }

            .container {
                box-shadow: none;
                /* Remove sombra para impressão */
                border: 1px solid #ddd;
                /* Borda simples para PDF */
            }
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            background-color: #ecf0f1;
            margin-bottom: 10px;
            padding: 12px 15px;
            border-radius: 5px;
            font-size: 16px;
            /* Usar display: block para maior compatibilidade,
                e controlar quebras de linha manualmente ou com divs internas */
            display: block;
        }

        li:last-child {
            margin-bottom: 0;
        }

        .hit-line {
            display: table;
            /* Usar table/table-row/table-cell para layout de colunas */
            width: 100%;
            margin-bottom: 5px;
            /* Espaço entre a linha principal e a observação */
        }

        .hit-name,
        .hit-date {
            display: table-cell;
            vertical-align: top;
            /* Alinha o texto ao topo da célula */
        }

        .hit-name {
            font-weight: bold;
            color: #2980b9;
            width: 70%;
            /* Ajuste a largura conforme necessário */
        }

        .hit-date {
            color: #7f8c8d;
            font-size: 14px;
            text-align: right;
            /* Alinha a data à direita */
            width: 30%;
            /* Ajuste a largura conforme necessário */
        }

        .hit-observation {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
            /* Espaço acima da observação */
            /* Display em linha com emoji, se necessário */
        }

        .hit-observation .emoji {
            margin-right: 5px;
            font-size: 16px;
            line-height: 1;
            vertical-align: middle;
            /* Alinha o emoji verticalmente com o texto */
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
        {{-- Contêiner de hits com rolagem APENAS em tela --}}
        <div class="hits-container scrollable-on-screen">
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
                    <div class="hit-line">
                        <span class="hit-name">
                            📌 {{ $hitLabels[$key] ?? 'Observação' }}:
                        </span>
                        <span class="hit-date">{{ \Carbon\Carbon::parse($hit['date'])->format('d/m/Y H:i:s') }}</span>
                    </div>
                    {{-- Adiciona a observação se ela existir --}}
                    @if (!empty($hit['observation']))
                    <div class="hit-observation">
                        <span class="emoji"> 📝 </span> <span> {{ $hit['observation'] }}</span>
                    </div>
                    @else
                    <div class="hit-observation">
                        <span class="emoji"> ❗ </span> <span> Nenhuma observação registrada</span>
                    </div>
                    @endif

                    @if (!empty($hit['checked']) && $hit['checked'] === 'true')
                    {{-- Exibe o emoji de verificado se o ponto foi verificado --}}
                    <div class="hit-observation">
                        <span class="emoji"> ✅ </span> <span> Ponto verificado</span>
                    </div>
                    @else
                    <div class="hit-observation">
                        <span class="emoji"> ❌ </span> <span> Ponto não verificado</span>
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <p class="no-hits">Parece que você não registrou nenhum ponto hoje.</p>
        @endif

        <p class="footer">Este é um e-mail automático. Por favor, não responda.</p>
    </div>
</body>

</html>