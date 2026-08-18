# Funcionamento do módulo de notícias

## Área pública

A página `index.php` é processada pelo backend e consulta `api/noticias.php` ao carregar. A API retorna somente notícias publicadas, da mais recente para a mais antiga. O JavaScript cria os cartões usando `textContent`, evitando a execução de HTML inserido por usuários.

Todas as páginas públicas usam o front controller PHP e URLs sem extensão. Endereços legados são redirecionados para a rota canônica. O contato é persistido no banco e aparece na caixa de mensagens do Administrador.

O endereço administrativo não aparece no site público. O acesso é feito diretamente por `/administracao-coopsul/`. A segurança é garantida por autenticação, sessão, hash de senha, CSRF e validação das permissões no servidor.

## Perfis

- **Administrador:** superusuário. Cria usuários e pode criar, editar ou excluir qualquer notícia.
- **Redator:** cria notícias e pode editar ou excluir somente as notícias das quais é autor.

Uma notícia pode ficar como rascunho ou ser publicada. Apenas as publicadas aparecem na página inicial.

## Imagens

São aceitos JPG, PNG e WebP de até 5 MB. O servidor detecta o MIME real, cria um nome aleatório e bloqueia scripts na pasta de uploads por `.htaccess`.
