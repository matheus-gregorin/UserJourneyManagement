<?php

namespace App\Domain\Enums;

enum EventsWahaEnum
{
    // STATUS
    public const MESSAGE = 'message';

    // MESSAGES
    public const AWAIT = "Aguarde um momento enquanto verificamos a mensagem, por favor, não envie mensagens repetidas, isso pode causar lentidão no sistema.";

    public const SCOPE = "Selecione uma das opções abaixo:\n1 - Verificar seus pontos batidos hoje\n2 - Bater o ponto de entrada\n3 - Bater o ponto de saída para almoço\n4 - Bater o ponto de volta do almoço\n5 - Bater o ponto de saída";
    
    public const THISUSERNAME = " - Nome do usuário: ";
        
    public const MESSAGENOTUNDERSTOOD = "Não foi possivel entender a mensagem, por favor, tente novamente.";
    
    public const USERNOTFOUND = "Usuário não encontrado, por favor, verifique se você está cadastrado e tente novamente.";
}
