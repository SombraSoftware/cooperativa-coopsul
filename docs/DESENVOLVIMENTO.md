# Desenvolvimento local

## Requisitos

- PHP 8.0 ou mais recente
- Extensões PHP `pdo_sqlite` e `fileinfo`
- Apache com suporte a `.htaccess`

## Primeira execução

O banco `data/noticias.sqlite` e suas tabelas são criados automaticamente. Crie o primeiro administrador no terminal, na raiz do projeto:

```bash
php scripts/criar-admin.php admin "Administrador Coopsul" "troque-esta-senha-forte"
```

Depois acesse `http://localhost/cooperativa-coopsul/administracao-coopsul/`.

## Estrutura

```text
app/                       configuração, banco e funções compartilhadas
api/noticias.php           API pública somente leitura
administracao-coopsul/     autenticação e painel editorial
data/                      banco SQLite protegido
uploads/noticias/          imagens enviadas
scripts/criar-admin.php    criação do primeiro administrador
docs/                      documentação
```

As páginas públicas usam `index.php` como front controller, com as rotas `/`, `/reciclagem`, `/sobre`, `/contato` e `/social`. A apresentação fica em templates PHP dentro de `app/views/`. O `.htaccess` envia rotas desconhecidas ao front controller e redireciona acessos antigos em `.html` ou `.php` para a URL canônica sem extensão.

O formulário de contato é processado em `contato.php`, protegido por CSRF e campo antispam. As mensagens ficam em `contact_messages` e somente o Administrador pode consultá-las em `/administracao-coopsul/mensagens.php`.

Toda escrita exige sessão e token CSRF. A autorização de editar/excluir é repetida no servidor e as consultas recebem parâmetros preparados.

As verificações defensivas e o calendário de revisão ficam em `tests/security/`. Execute `php tests/security/verify.php` após mudanças no backend e antes de publicar.
