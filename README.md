# Dashboard Executivo - Portais Drummond

Sistema de dashboard executivo para monitoramento dos portais do Grupo Drummond.

## Funcionalidades

- **Autenticação**: Sistema de login seguro
- **Dashboard Consolidado**: Visão geral de todos os portais
- **Indicadores por Portal**:
  - Ouvidoria: Chamados abertos/resolvidos, SLA médio, tipos de manifestação
  - EAD: Chamados abertos/resolvidos, SLA médio, serviços solicitados
  - Processo Seletivo: Chamados abertos/resolvidos, SLA médio, serviços solicitados
  - Secretaria Acadêmica: Solicitações abertas/concluídas, tempo médio, serviços solicitados
  - Financeiro: Chamados abertos/resolvidos, SLA médio
  - Ex-Aluno: Chamados abertos/resolvidos, SLA médio, serviços solicitados

## Como Usar

1. **Iniciar o XAMPP**:

   - Abra o painel de controle do XAMPP
   - Inicie os módulos Apache e MySQL

2. **Acessar o Sistema**:

   - Abra o navegador e acesse: `http://localhost/portal_diretoria`
   - Você será redirecionado para a tela de login

3. **Credenciais de Acesso**:

   - **Usuário**: admin
   - **Senha**: 123456

4. **Configuração do Banco de Dados**:
   - Edite `includes/config.php` para ajustar:
     - Credenciais de conexão com o banco
     - Nomes das tabelas e colunas conforme seu banco de dados

## Estrutura do Projeto

```
portal_diretoria/
├── css/
│   ├── style.css      # Estilos do dashboard
│   └── login.css      # Estilos da tela de login
├── js/
│   └── dashboard.js   # Scripts dos gráficos
├── includes/
│   └── config.php     # Configurações do banco de dados
├── data/
│   ├── data_ouvidoria.php
│   ├── data_ead.php
│   ├── data_processo_seletivo.php
│   ├── data_secretaria.php
│   ├── data_financeiro.php
│   └── data_exaluno.php
├── index.php          # Página inicial (redirecionamento)
├── login.php          # Tela de login
├── dashboard.php      # Dashboard principal
├── logout.php         # Logout do sistema
└── README.md          # Este arquivo
```

## Tecnologias Utilizadas

- **Backend**: PHP 7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Banco de Dados**: MySQL
- **Gráficos**: Chart.js
- **Sessões**: PHP Sessions

## Personalização

### Alterar Credenciais de Login

Edite o arquivo `login.php` na seção de autenticação:

```php
if ($usuario === 'admin' && $senha === '123456') {
    // Altere para suas credenciais
}
```

### Configurar Banco de Dados

Edite `includes/config.php` para ajustar:

- Nomes dos bancos de dados
- Credenciais de acesso
- Nomes de tabelas e colunas

### Estilização

- `css/style.css`: Estilos do dashboard
- `css/login.css`: Estilos da tela de login

## Suporte

Para dúvidas ou problemas, verifique:

1. Se o XAMPP está rodando corretamente
2. Se os bancos de dados existem e estão acessíveis
3. Se as tabelas e colunas estão com os nomes corretos
4. Se há erros nos logs do Apache/PHP
