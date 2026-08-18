# Verificação periódica

## Semanal

- Executar `verify.php` e guardar o resultado com data e responsável.
- Revisar erros HTTP/PHP, tentativas repetidas de login e uploads recusados.
- Confirmar que o backup mais recente terminou sem erro.
- Verificar espaço em disco e validade do certificado HTTPS.

## Mensal

- Revisar Administradores e Redatores; desativar acessos sem necessidade.
- Atualizar PHP, Apache, sistema operacional e bibliotecas após teste prévio.
- Conferir se `data/`, `scripts/`, `app/` e uploads continuam protegidos via HTTP.
- Revisar permissões de escrita e confirmar que somente `data/` e uploads precisam delas.
- Testar login, CSRF, autorização por autoria, contato e resposta 404.

## Trimestral

- Restaurar banco e uploads em ambiente isolado.
- Revisar o plano de resposta a incidentes e contatos responsáveis.
- Conferir retenção e descarte seguro das mensagens de contato.
- Realizar revisão de código focada em autenticação, autorização, upload e injeções.

## Anual ou após mudança relevante

- Avaliação de segurança por profissional autorizado.
- Revisão de riscos, impacto LGPD e política de senhas/acessos.
- Simulação documentada de incidente e recuperação.

Para cada execução, registre: data, versão implantada, responsável, itens verificados, evidências, falhas, prazo e pessoa responsável pela correção.

