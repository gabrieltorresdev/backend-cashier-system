<?php

return [
    "common" => [
        "unauthorized" => "Não autorizado",
        "no-data-found" => "Nenhum dado encontrado",
        "too-many-requests" => "Aguarde alguns instantes para fazer uma nova requisição",
        "action-not-permitted" => "Ação não autorizada",
        "response-error-message" => "Oops! Tivemos um problema ao completar sua requisição. Por favor, tente novamente",
        "response-success-message" => "Sucesso ao completar sua requisição",
    ],
    "auth" => [
        "invalid-credentials" => "Credenciais inválidas",
        "invalid-password" => "Senha inválida",
        "password-changed" => "A senha foi alterada com sucesso.",
        "invalid-verification-code" => "Código de verificação inválido",
        "user-activation-failed" => "Não foi possível prosseguir com a ativação do usuário. Verifique as informações inseridas e tente novamente",
        "user-activation-success" => "Usuário ativado com sucesso. Use a nova senha escolhida para acessar o sistema",
        "user-activation-email-sent" => "Enviamos um código de confirmação para o email cadastrado. Verifique a caixa de entrada/spam",
        "user-activation-email-not-sent" => "Não foi possível prosseguir com a ativação do seu usuário. Por favor, entre em contato com o suporte",
        "user-already-actived" => "Usuário já está ativo",
        "user-returned-successfully" => "Usuário retornado com sucesso",
        "login-success" => "Usuário logado com sucesso",
    ],
    "product" => [
        "quantity-bigger-than-stock" => "Quantidade do produto maior que seu estoque",
    ],
    "transaction" => [
        "error-on-create" => "Erro ao iniciar a transação",
        "error-on-update" => "Erro ao atualizar a transação",
        "success-on-create" => "Transação iniciada com sucesso",
        "success-on-update" => "Transação atualizada com sucesso",
        "invalid-type" => "Tipo de transação inválido",
        "need-products" => "Para finalizar esse tipo de transação, é necessário um ou mais produtos",
        "cannot-modify-closed" => "Você não pode modificar uma transação fechada",
    ],
    "cash-register" => [
        "insufficient-balance" => "Valor em caixa insuficiente",
        "success-on-close" => "Caixa fechado com sucesso",
        "error-on-close" => "Erro ao fechar o caixa",
        "success-on-open" => "Caixa aberto com sucesso",
        "error-on-open" => "Erro ao abrir o caixa",
        "opened-not-found" => "Não há caixa aberto para o usuário",
    ],
];
