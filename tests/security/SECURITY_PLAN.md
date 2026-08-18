# Plano de cibersegurança

## Objetivos e ativos

Proteger contas administrativas, notícias, mensagens de contato, imagens enviadas, banco SQLite e disponibilidade do site. Dados de contato devem ser tratados como informação pessoal e acessados apenas quando necessário.

## Controles preventivos

- HTTPS obrigatório em produção, cookies `HttpOnly`, `Secure` em HTTPS e `SameSite=Lax`.
- Senhas armazenadas exclusivamente com `password_hash()` e contas individuais.
- Administrador como único perfil autorizado a criar usuários e consultar mensagens.
- Redator limitado às próprias publicações para edição e exclusão.
- Token CSRF em toda operação autenticada e no formulário de contato.
- Consultas preparadas para dados recebidos; saída HTML escapada.
- Upload limitado por tamanho e MIME real; execução de scripts bloqueada na pasta.
- Banco, scripts e arquivos de configuração bloqueados para acesso HTTP.
- Privilégio mínimo no sistema operacional e `display_errors=Off` em produção.

## Resposta a incidentes

1. Registrar horário, origem do alerta, contas e recursos afetados sem destruir evidências.
2. Conter: desativar a conta comprometida, preservar logs e restringir o acesso afetado.
3. Trocar credenciais e chaves relacionadas a partir de um dispositivo confiável.
4. Corrigir a causa, atualizar dependências/servidor e validar com os testes.
5. Restaurar somente backups verificados e monitorar recorrência.
6. Avaliar obrigação de comunicar titulares e autoridades conforme a LGPD.
7. Documentar causa, impacto, decisões e ações preventivas.

Não apagar logs nem executar testes invasivos em produção durante a investigação.

## Recuperação

- Backup diário do SQLite e uploads, com retenção definida pelo responsável.
- Uma cópia fora do servidor público e protegida por acesso restrito.
- Teste trimestral de restauração em ambiente isolado.
- Registro do tempo de restauração e de qualquer arquivo ausente ou inconsistente.

