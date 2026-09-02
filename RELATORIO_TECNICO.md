# Relatório técnico - Garimpo Brechó

## Tema
Garimpo Brechó é um sistema de gerenciamento de peças para lojas de segunda mão. O recorte permite demonstrar autenticação, CRUD, filtros, controle de estoque e relatório em um contexto realista e pequeno.

## Arquitetura
Foi adotada uma estrutura PHP procedural organizada por responsabilidade. As páginas são controladores das requisições, o PDO e os helpers ficam isolados da apresentação, e os parciais concentram o layout. A solução não depende de framework de back-end.

## Banco de dados
A relação é 1:N: um usuário possui muitas peças e contatos recorrentes; uma peça pode apontar para um contato que fornece ou compra. A chave estrangeira com `ON DELETE CASCADE` preserva os dados da conta e `ON DELETE SET NULL` mantém a peça quando o contato é removido. Índices compostos apoiam consultas por usuário, data e status. O arquivo `database.sql` é o artefato de instalação.

O código da peça usa uma classificação inspirada no Dewey: cada categoria recebe uma faixa (`100` para roupas, `200` para calçados etc.) e a sequência individual é gerada pelo sistema. A origem é armazenada em um único campo para aceitar doação, consignação, compra ou outras fontes.

## Segurança
Entradas são validadas com regras no servidor, queries usam prepared statements, senhas usam `password_hash`, saídas HTML passam por `htmlspecialchars`, formulários de mutação usam token CSRF e IDs sempre são limitados ao usuário autenticado. A sessão é regenerada após o login.

## Experiência
Bootstrap fornece grid responsivo e componentes acessíveis. O JavaScript aplica validação visual nativa, enquanto o CSS combina tipografia Space Grotesk/DM Sans e contraste em uma interface leve para uso recorrente.

## Relatório
A exportação CSV suporta filtros por status e intervalo de datas e é compatível com Excel/Google Sheets. O formato foi escolhido por não exigir biblioteca externa e ser facilmente auditável.
