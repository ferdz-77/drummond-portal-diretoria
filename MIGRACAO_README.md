# 🔄 Migração de Dados - Produção → Desenvolvimento

## 📋 Visão Geral

Este conjunto de scripts permite migrar dados reais dos bancos de produção para o ambiente de desenvolvimento, garantindo testes mais realistas e validação precisa das métricas do dashboard.

## 📁 Arquivos Criados

### 1. `setup_databases.php`

- **Função**: Cria os bancos de dados necessários no ambiente local
- **Quando usar**: Antes da primeira migração ou se algum banco estiver faltando

### 2. `backup_dev_data.php`

- **Função**: Faz backup completo dos dados atuais de desenvolvimento
- **Quando usar**: Sempre antes de executar uma migração (segurança!)

### 3. `migrate_data.php`

- **Função**: Migra dados da produção para desenvolvimento
- **Quando usar**: Para atualizar dados de desenvolvimento com dados reais

## 🚀 Como Usar

### Passo 1: Preparar os Bancos Locais

```bash
php setup_databases.php
```

### Passo 2: Fazer Backup dos Dados Atuais

```bash
php backup_dev_data.php
```

### Passo 3: Configurar Credenciais de Produção

1. Abra o arquivo `migrate_data.php`
2. Configure as credenciais no array `$prod_config`:

```php
$prod_config = [
    'host' => 'SEU_SERVIDOR_PRODUCAO', // IP ou domínio
    'user' => 'USUARIO_PRODUCAO',
    'password' => 'SENHA_PRODUCAO',
    'databases' => [
        'portal_ouvidoria' => 'portal_ouvidoria',
        'portal_ead' => 'portal_ead',
        // ... outros bancos
    ]
];
```

### Passo 4: Executar a Migração

1. No arquivo `migrate_data.php`, descomente a linha:

```php
migrateAllDatabases($prod_config, $dev_config);
```

2. Execute o script:

```bash
php migrate_data.php
```

## 📊 O que é Migrado

- ✅ **Estrutura completa das tabelas** (CREATE TABLE)
- ✅ **Todos os dados** (INSERT INTO)
- ✅ **Relacionamentos e constraints**
- ✅ **Índices e chaves**
- ✅ **Tipos de dados corretos**

## 🔒 Segurança

- 🔐 **Backup automático** antes de sobrescrever dados
- ⚠️ **Confirmação necessária** para executar migração
- 📁 **Backups salvos** na pasta `backups/`
- 🚫 **Não sobrescreve** dados de produção

## 📈 Benefícios

### Para Desenvolvimento:

- 🎯 **Testes realistas** com dados reais
- 📊 **Validação de métricas** precisa
- 🐛 **Identificação de bugs** que só aparecem com dados reais
- 📈 **Melhor qualidade** do código

### Para o Dashboard:

- ✅ **Métricas precisas** baseadas em dados reais
- 📊 **Gráficos representativos** da realidade
- 🎨 **Interface validada** com dados reais
- 🚀 **Performance testada** com volume real

## 🔧 Comandos Rápidos

```bash
# Setup completo (backup + migração)
php backup_dev_data.php && php migrate_data.php

# Apenas verificar bancos
php setup_databases.php

# Restaurar backup se necessário
mysql -u root < backups/backup_dev_2025-09-19_14-30-00.sql
```

## ⚠️ Avisos Importantes

1. **Configure as credenciais de produção** antes de executar
2. **Sempre faça backup** antes da migração
3. **Teste em ambiente controlado** primeiro
4. **Verifique conectividade** com servidor de produção
5. **Monitore o processo** durante a migração

## 📞 Suporte

- 📧 **Configuração**: Verifique credenciais de produção
- 🔌 **Conectividade**: Teste conexão com servidor remoto
- 💾 **Espaço**: Verifique espaço em disco disponível
- ⏱️ **Tempo**: Grandes bancos podem demorar para migrar

---

**Última atualização**: 19/09/2025
**Versão**: 1.0</content>
<parameter name="filePath">c:\xampp\htdocs\portal_diretoria\MIGRACAO_README.md
