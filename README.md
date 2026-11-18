# Sistema_CRUD
Sistema web desenvolvido em PHP e MySQL para gerenciamento de usuários e registros, com autenticação, operações CRUD e mensagens de confirmação.

Funcionalidades
Login e validação de usuário
Cadastro de registros
Alteração de registros
Exclusão de registros
Consulta de dados
Encerramento da sessão
Tecnologias
PHP
MySQL
HTML/CSS
SQL

Projeto desenvolvido para fins acadêmicos.

## Funcionalidades adicionadas

### Área administrativa
- Cadastro, listagem, edição e exclusão de administradores em `administradores.php`.
- A senha é armazenada com `password_hash()`; ao editar, deixe a senha em branco para manter a atual.
- Um administrador não pode excluir a própria conta pela tela de gerenciamento.
- O sistema impede a exclusão do último administrador.

### Área do cliente
- Edição do nome e e-mail em `minha_conta.php`.
- Alteração opcional de senha, mediante confirmação da senha atual.
- Exclusão da própria conta mediante confirmação da senha e token CSRF.

# 🌐 Projeto publicado

O sistema está hospedado online pelo InfinityFree.

**Acesse o projeto:** https://sistemacrud.freedev.app/Sistema_CRUD%20-%20Copia/inicio.php



