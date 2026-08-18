# Desenvolvimento local

## Requisitos

- PHP 8.1 ou mais recente
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

Toda escrita exige sessão e token CSRF. A autorização de editar/excluir é repetida no servidor e as consultas recebem parâmetros preparados.

