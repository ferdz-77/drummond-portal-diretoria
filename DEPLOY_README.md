# 🚀 Guia de Deploy - Portal da Diretoria

## 📋 Pré-requisitos

Antes de fazer o deploy, certifique-se de que:

1. **Subdomínio criado**: `https://portal-diretoria.drummond.com.br`
2. **Bancos de dados criados** no servidor de produção
3. **Credenciais de acesso** (banco e FTP) fornecidas pelo administrador

## 🗄️ Bancos de Dados Necessários

### Produção
- `portal_diretoria_prod` - Para notificações e dados do portal
- `portal_ouvidoria_prod` - Dados da Ouvidoria
- `portal_ead_prod` - Dados do EAD
- `portal_processo_seletivo_prod` - Dados do Processo Seletivo
- `portal_secretaria_academica_prod` - Dados da Secretaria Acadêmica
- `portal_financeiro_prod` - Dados do Financeiro
- `portal_exaluno_prod` - Dados dos Ex-Alunos

## ⚙️ Configuração para Produção

### 2. Credenciais de Produção Já Configuradas

As seguintes credenciais de produção já estão implementadas no `config_env.php`:

- **Ouvidoria**: `bdsolicita_atendimento` (user: `bdsolatend`)
- **EAD**: `dbead` (user: `eaduseroot`)
- **Processo Seletivo**: `pseldb` (user: `pseluserdb`)
- **Secretaria Acadêmica**: `dbsecretacad` (user: `dbsecretacaduser`)
- **Financeiro**: `fini` (user: `userfinidb`)
- **Ex-Aluno**: `bdsolicita_atendimento` (mesmas credenciais da Ouvidoria)
- **Portal da Diretoria**: `dbgproto` (user: `usergprotoc`) ✅ **CONFIGURADO**

### 3. Ativar Modo Produção

```bash
# Edite o arquivo .env
APP_ENV=production
```

### 4. Testar Conexões

```bash
# Teste rápido das conexões
php test_prod_quick.php
```

## 📁 Estrutura de Deploy

### Arquivos a subir:
```
portal-diretoria/
├── index.php
├── login.php
├── dashboard.php
├── logout.php
├── includes/
│   ├── config.php (ou config_env.php)
│   └── ...
├── data/
├── css/
├── js/
├── backups/
├── PHPMailer/
└── .env (criar em produção)
```

### Arquivos NÃO subir:
- `*.sql` (backups locais)
- `setup_databases.php`
- `migrate_data.php`
- `test_connection.php`
- `check_notifications.php`
- `notifications_status.php`
- `solicitacao_acesso_portal.txt`

## 🔧 Passos para Deploy

1. **Fazer backup** dos bancos de dados locais
2. **Exportar dados** dos bancos locais (se necessário migrar)
3. **Configurar ambiente** de produção (`.env` ou `config.php`)
4. **Testar conexão** com bancos de produção
5. **Subir arquivos** via FTP/SFTP
6. **Importar dados** nos bancos de produção (se necessário)
7. **Testar funcionalidades** no ambiente de produção

## 🧪 Testes Pós-Deploy

- [ ] Login funciona
- [ ] Dashboard carrega dados
- [ ] Notificações aparecem
- [ ] Filtros de período funcionam
- [ ] E-mails são enviados (se configurado)

## 🔒 Segurança

- **Nunca subir** arquivos `.env` com dados reais para repositórios
- **Usar HTTPS** sempre
- **Configurar CORS** se necessário
- **Validar inputs** em todos os formulários

## 📞 Suporte

Em caso de problemas, verificar:
1. Logs do servidor web
2. Conexão com bancos de dados
3. Permissões de arquivo/pasta
4. Configurações do PHP

## 🚨 Troubleshooting - Erro 500 em Produção

Se você receber **HTTP ERROR 500** no servidor de produção:

### 1. Execute o Diagnóstico
```bash
# No servidor de produção, acesse:
https://gestao-protocolos.drummond.com.br/debug_production.php
```

### 2. Teste Conexões
```bash
# Teste hosts alternativos:
https://gestao-protocolos.drummond.com.br/diagnose_production.php

# Teste host alternativo específico:
https://gestao-protocolos.drummond.com.br/emergency_host_test.php
```

### 3. Possíveis Causas e Soluções

#### ❌ Host Incorreto
- **Sintoma**: Conexão falha com "localhost"
- **Solução**: Use `127.0.0.1` ou IP real do MySQL
- **Arquivo**: `includes/config_env.php` → alterar host do portal_diretoria

#### ❌ Credenciais Incorretas
- **Sintoma**: "Access denied for user"
- **Solução**: Verificar usuário/senha com administrador

#### ❌ Arquivo .env Não Carregado
- **Sintoma**: Ambiente permanece em "development"
- **Solução**: Verificar permissões do arquivo `.env`

#### ❌ Permissões de Arquivo
- **Sintoma**: "Permission denied"
- **Solução**: Ajustar permissões para 644 nos arquivos PHP

#### ❌ PHP Extensions Faltando
- **Sintoma**: "Undefined function"
- **Solução**: Verificar se mysqli/pdo estão habilitados

### 4. Logs do Servidor
Verifique os logs do Apache/PHP no servidor:
```bash
# Geralmente em:
/var/log/apache2/error.log
/var/log/php/error.log
```

### 5. Teste Passo a Passo
1. ✅ Acesse `debug_production.php` - deve mostrar configurações
2. ✅ Execute `diagnose_production.php` - deve encontrar host funcionando
3. ✅ Atualize `config_env.php` com host correto
4. ✅ Teste `dashboard.php` novamente