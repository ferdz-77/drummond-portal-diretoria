# 🚀 Checklist de Deploy via SFTP - Portal Diretoria

## 📋 Pré-deploy (Local)

- [x] **Branch correta**: Estamos na `production` com todas as correções
- [x] **Arquivo ZIP criado**: `portal_diretoria_production.zip` (1.2MB)
- [x] **Testes locais**: Sistema funcionando perfeitamente no localhost

## 📤 Upload via WinSCP

### 1. Conectar ao servidor
```
Host: gestao-protocolos.drummond.com.br
Port: 22 (SFTP)
User: [seu_usuario_sftp]
Password: [sua_senha]
Diretório remoto: /d
```

### 2. Upload dos arquivos
- [ ] **Upload do ZIP**: `portal_diretoria_production.zip` → `/d/`
- [ ] **Extrair no servidor**: Descompactar o ZIP no diretório `/d/`
- [ ] **Verificar estrutura**: Todos os arquivos devem estar em `/d/`

## ⚙️ Configuração no Servidor

### 1. Permissões dos arquivos
```bash
# No servidor, via SSH ou terminal
cd /d
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 666 logs/*.log 2>/dev/null || true
```

### 2. Configurar .env (se necessário)
```bash
# Verificar se .env existe
ls -la .env

# Se não existir, copiar do exemplo
cp .env.example .env

# Editar configurações específicas do servidor
nano .env
```

### 3. Testar sintaxe PHP
```bash
# Verificar arquivos críticos
php -l dashboard.php
php -l includes/config_env_simplificado.php
php -l detalhes.php
```

### 4. Configurar Apache (se necessário)
```bash
# Copiar configuração do Apache
sudo cp apache-config.conf /etc/apache2/sites-available/gestao-protocolos.conf

# Editar domínio correto
sudo nano /etc/apache2/sites-available/gestao-protocolos.conf
# Mudar: ServerName gestao-protocolos.drummond.com.br

# Ativar site
sudo a2ensite gestao-protocolos.conf
sudo systemctl reload apache2
```

## 🧪 Testes Pós-deploy

### 1. Teste básico
```bash
# No servidor
cd /d
php test_prod_quick.php
```

### 2. Testes via navegador
- [ ] **Dashboard principal**: `https://gestao-protocolos.drummond.com.br/d/dashboard.php`
- [ ] **Página de detalhes**: `https://gestao-protocolos.drummond.com.br/d/detalhes.php?id=1`
- [ ] **API de filtros**: `https://gestao-protocolos.drummond.com.br/d/get_filtered_data.php`

### 3. Verificar logs
```bash
# Logs da aplicação
tail -f logs/error.log

# Logs do Apache
sudo tail -f /var/log/apache2/error.log
```

## 🔧 Troubleshooting

### Se der erro 500:
```bash
# Verificar sintaxe
php -l includes/config_env_simplificado.php

# Verificar conexões de banco
php test_prod_quick.php

# Verificar logs
tail -f logs/error.log
```

### Se não conectar ao banco:
- Verificar credenciais no `config_env_simplificado.php`
- Testar conexão manual: `mysql -h host -u user -p dbname`

### Se arquivos não carregarem:
- Verificar permissões: `ls -la`
- Verificar owner: `chown -R www-data:www-data .`

## ✅ Checklist Final

- [ ] Arquivos uploaded via WinSCP
- [ ] ZIP extraído no servidor
- [ ] Permissões configuradas
- [ ] .env configurado (se necessário)
- [ ] Apache configurado
- [ ] Sintaxe PHP OK
- [ ] Conexões de banco OK
- [ ] Dashboard acessível
- [ ] Todas as funcionalidades testadas

## 📞 Suporte

Em caso de problemas:
1. Verificar logs do Apache e aplicação
2. Executar `php test_prod_quick.php`
3. Testar conexões de banco individualmente
4. Verificar configurações de firewall/segurança

---
**Data do deploy:** $(date)
**Versão:** Production branch - Commit: $(git rev-parse --short HEAD)