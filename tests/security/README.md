# Segurança do site Coopsul

Este diretório reúne o plano de cibersegurança, a rotina de verificações periódicas e um verificador local automatizado. Os testes são defensivos e não alteram banco, usuários ou conteúdo.

## Execução

Na raiz do projeto:

```powershell
C:\xampp\php\php.exe tests\security\verify.php
```

Em Linux ou quando o PHP estiver no `PATH`:

```bash
php tests/security/verify.php
```

Código de saída `0` significa que todos os controles inspecionados passaram. Código `1` indica ao menos uma falha que deve ser analisada. O teste não substitui revisão humana, atualização do servidor, backup testado ou avaliação de segurança externa autorizada.

- [Plano de cibersegurança](SECURITY_PLAN.md)
- [Verificação periódica](PERIODIC_CHECKLIST.md)

