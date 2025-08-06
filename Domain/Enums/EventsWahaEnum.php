<?php

namespace Domain\Enums;

enum EventsWahaEnum
{
    // STATUS
    public const MESSAGE = 'message';

    // MESSAGES
    public const AWAIT = "🙋🏻‍♂️ aguarde um momento... estamos validando";

    public const AUTHSUCCESS = "✅ Certo! verificado.";

    public const SCOPE = "*Digite o número opção desejada:*\n _1 - Verificar seus pontos batidos hoje_\n _2 - Bater ponto_\n _3 - Verificar seus pontos batidos no mês atual_\n _4 - Falar com suporte_\n _5 - Finalizar_";

    public const HITSTODAYMENU = "*O que deseja fazer:*\n_1 - Confirmar pontos de hoje_\n_2 - Receber PDF no email_\n_3 - Retornar ao MENU_";

    public const HITPOINTMENU = "*O que deseja fazer:*\n_1 - Adicionar observação_\n_2 - Deletar ponto_\n_3 - Retornar ao MENU_\n\n\n_*Para adicionar uma observação*_ _Digite o numero da opção seguido da virgula e a descriação da observação, por exemplo: 1, Cheguei atrasado porque o trânsito estava ruim._";

    public const THISUSERNAME = " - Nome do usuário: ";

    public const HI = "🙋🏻‍♂️ Olá, ";

    public const MENU = "🔄 Lembrando que a qualquer momento você pode enviar a mensagem *menu*, que nós retornamos ao começo.";

    public const USERNOTAUTH = ", Você ainda não está autenticado. Para isso, enviei um codigo no seu email. Por favor, verifique se chegou e me envie para continuarmos...";

    public const MESSAGENOTUNDERSTOOD = "🙎🏻‍♂️ Não foi possivel entender a mensagem, por favor, tente novamente.";

    public const MESSAGERESEND = "🙎🏻‍♂️ Não foi possivel entender a mensagem, digite o número de acordo com essas opções.";

    public const SERVERERROR = "🤦🏻‍♂️ Tivemos um problema ao processar sua mensagem, por favor, tente novamente.";

    public const USERNOTFOUND = "🙅🏻‍♂️ Usuário não encontrado, por favor, verifique se você está cadastrado e tente novamente.";

    public const CODEINVALIDRESEND = "🔄 Código inválido, vamos tentar novamente.";
}
