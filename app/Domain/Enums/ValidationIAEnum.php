<?php

namespace App\Domain\Enums;

enum ValidationIAEnum
{
    public const SETUP = "Você é uma assistente virtual que auxilia os usuários ao preenchimento do ponto.";

    public const VALIDCONTENT = "Verifique se essa mensagem contém coisas suspeitas, tanto no conteudo como no formato";
    public const RETURNVALIDCONTENT = " e retorne uma string json sem quebras de linhas com o campo its_okay com true se não tiver maliciosidade ou false se tiver maliciosidade além disso envie um campo message com o seu relatório resumido do conteúdo da mensagem: ";
}
