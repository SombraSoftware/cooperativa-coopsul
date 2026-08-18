# Implantação PHP em produção

## Preparação

1. Use PHP 8.0+ com `pdo_sqlite`, `fileinfo` e HTTPS.
2. Garanta escrita para o servidor web somente em `data/` e `uploads/noticias/`.
3. Crie o administrador pelo terminal com `scripts/criar-admin.php`; bloqueie `scripts/` na web.
4. Entre em `/administracao-coopsul/`, crie contas individuais e use senhas fortes.
5. Confirme que `mod_rewrite` está habilitado e que `DirectoryIndex index.php` é aceito.

No Apache, mantenha `AllowOverride All` para que os `.htaccess` protejam banco e uploads. Como defesa adicional, mova `data/` para fora da raiz pública e atualize `COOPSUL_DB_PATH` em `app/config.php`.

## Exemplo de permissões Linux

```bash
chown -R www-data:www-data data uploads/noticias
chmod 750 data uploads/noticias
chmod 640 data/noticias.sqlite
```

O usuário do PHP varia entre hospedagens; ajuste o exemplo ao servidor.

## Backup e atualização

Faça backup do banco e da pasta `uploads/noticias/`:

```bash
sqlite3 data/noticias.sqlite ".backup 'backup/noticias.sqlite'"
```

Antes de atualizar, preserve ambos. Nunca os substitua por diretórios vazios do repositório.

## Checklist

- HTTPS e redirecionamento de HTTP ativos
- `display_errors=Off` e logs fora da área pública
- `data/` inacessível pelo navegador
- execução de PHP bloqueada em `uploads/noticias/`
- restauração de backup testada periodicamente
- contas antigas desativadas
- `php tests/security/verify.php` executado sem falhas
