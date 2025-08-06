<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resumo de Pontos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 26px;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 18px;
            color: #34495e;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #ecf0f1;
            color: #2c3e50;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        .emoji {
            font-size: 16px;
            vertical-align: middle;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #95a5a6;
        }

        @media print {
            body {
                background-color: #fff;
            }

            .container {
                box-shadow: none;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Resumo de Pontos deste mês</h1>
        <h2>Olá, {{ $username }}!</h2>

        @if (!empty($hits))
        <table>
            <thead>
                <tr>
                    <th>📌 Tipo</th>
                    <th>🕓 Horário</th>
                    <th>📝 Observação</th>
                    <th>✔️ Verificado</th>
                </tr>
            </thead>
            <tbody>
                @php
                $hitLabels = [
                'Entrada',
                'Início do Almoço',
                'Fim do Almoço',
                'Saída',
                ];
                @endphp

                @foreach ($hits as $key => $hit)
                <tr>
                    <td>{{ $hitLabels[$key] ?? 'Outros' }}</td>
                    <td>{{ \Carbon\Carbon::parse($hit['date'])->format('d/m/Y H:i:s') }}</td>
                    <td>
                        @if (!empty($hit['observation']))
                        📝 {{ $hit['observation'] }}
                        @else
                        ❗ Nenhuma
                        @endif
                    </td>
                    <td>
                        @if (!empty($hit['checked']) && $hit['checked'] === 'true')
                        ✅
                        @else
                        ❌
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="no-hits">Parece que você não registrou nenhum ponto neste mês.</p>
        @endif

        <p class="footer">Este é um e-mail automático. Por favor, não responda.</p>
    </div>
</body>

</html>