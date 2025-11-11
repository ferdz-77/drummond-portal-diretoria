# 🚀 Guia de Deploy - Portal Diretoria

## 📋 Pré-requisitos para Produção

### Servidor
- **PHP**: 8.2 ou superior
- **MySQL/MariaDB**: 5.7 ou superior
- **Apache**: 2.4+ com mod_rewrite
- **SSL**: Certificado HTTPS válido
- **Espaço em disco**: Mínimo 500MB

### Configurações de Segurança
- **Firewall**: Apenas portas 80, 443 abertas
- **SSH**: Acesso restrito por chave
- **PHP**: `display_errors = Off` em produção
- **MySQL**: Usuário dedicado com permissões mínimas

---

## 🔧 Checklist de Deploy

### ✅ Fase 1: Preparação Local
- [x] **Backup criado**: `portal_diretoria_backup_YYYY-MM-DD_HH-mm-ss.zip`
- [x] **Git inicializado**: Repositório local configurado
- [x] **README atualizado**: Documentação completa
- [x] **Sintaxe validada**: Todos os arquivos PHP testados
- [x] **Funcionalidades testadas**: Dashboard, filtros, detalhes

### ✅ Fase 2: Servidor de Produção
- [ ] **Servidor provisionado**: Instâncias EC2/Lightsail/equivalente
- [ ] **PHP instalado**: Versão 8.2+ com extensões necessárias
- [ ] **MySQL configurado**: Banco de dados criado e usuário configurado
- [ ] **Apache configurado**: VirtualHost criado
- [ ] **SSL configurado**: Certificado Let's Encrypt ou pago

### ✅ Fase 3: Deploy do Código
- [ ] **Código enviado**: Via Git clone ou FTP/SFTP
- [ ] **Permissões ajustadas**: Pastas 755, arquivos 644
- [ ] **.env configurado**: Variáveis de produção definidas
- [ ] **.htaccess ativo**: Regras de rewrite funcionando

### ✅ Fase 4: Banco de Dados
- [ ] **Tabelas criadas**: Scripts SQL executados
- [ ] **Dados migrados**: Importação de dados de produção
- [ ] **Conexões testadas**: Todos os portais conectando
- [ ] **Backup de produção**: Backup do estado atual

### ✅ Fase 5: Testes em Produção
- [ ] **Login funcionando**: Acesso ao sistema
- [ ] **Dashboard carregando**: Métricas aparecendo
- [ ] **Filtros operacionais**: Período, status, portal
- [ ] **Detalhes dos portais**: Páginas individuais funcionando
- [ ] **Gráficos renderizando**: Chart.js funcionando
- [ ] **Responsividade**: Teste em mobile/desktop

### ✅ Fase 6: Otimizações
- [ ] **Cache configurado**: OPcache habilitado
- [ ] **Compressão GZIP**: Habilitada no Apache
- [ ] **Minificação**: CSS/JS otimizados
- [ ] **CDN**: Recursos estáticos (opcional)

### ✅ Fase 7: Monitoramento
- [ ] **Logs configurados**: Error log e access log
- [ ] **Alertas ativos**: Notificações de erro
- [ ] **Backup automático**: Rotina diária configurada
- [ ] **Monitor de uptime**: Serviço de monitoramento

---

## 📝 Comandos Essenciais para Deploy

### 1. Configuração Inicial do Servidor
```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Apache, PHP, MySQL
sudo apt install apache2 php8.2 php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip mysql-server -y

# Iniciar serviços
sudo systemctl enable apache2
sudo systemctl enable mysql
sudo systemctl start apache2
sudo systemctl start mysql
```

### 2. Configuração do MySQL
```bash
# Criar banco e usuário
sudo mysql -u root -p
CREATE DATABASE portal_ouvidoria;
CREATE DATABASE portal_ead;
CREATE DATABASE portal_processo_seletivo;
CREATE DATABASE portal_secretaria_academica;
CREATE DATABASE portal_financeiro;
CREATE DATABASE portal_exaluno;
CREATE USER 'portal_user'@'localhost' IDENTIFIED BY 'SENHA_FORTE_AQUI';
GRANT ALL PRIVILEGES ON portal_*.* TO 'portal_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Configuração do Apache
```bash
# Criar VirtualHost
sudo nano /etc/apache2/sites-available/portal.conf

# Conteúdo do arquivo:
<VirtualHost *:80>
    ServerName portal.diretoria.local
    DocumentRoot /var/www/html/portal_diretoria

    <Directory /var/www/html/portal_diretoria>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/portal_error.log
    CustomLog ${APACHE_LOG_DIR}/portal_access.log combined
</VirtualHost>

# Habilitar site
sudo a2ensite portal.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### 4. Deploy do Código
```bash
# Clonar repositório
cd /var/www/html
sudo git clone https://github.com/SEU_USERNAME/portal-diretoria.git
cd portal_diretoria

# Configurar permissões
sudo chown -R www-data:www-data /var/www/html/portal_diretoria
sudo chmod -R 755 /var/www/html/portal_diretoria
sudo chmod -R 666 /var/www/html/portal_diretoria/logs/

# Configurar .env
cp .env.example .env
sudo nano .env
# Editar variáveis de produção
```

### 5. Configuração SSL (Let's Encrypt)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache -y

# Obter certificado
sudo certbot --apache -d portal.diretoria.local

# Configurar renovação automática
sudo crontab -e
# Adicionar: 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## 🔍 Scripts de Verificação

### Teste de Conectividade
```bash
# Criar arquivo de teste
nano /var/www/html/portal_diretoria/teste_producao.php

<?php
// Teste básico de produção
echo "✅ PHP funcionando<br>";
echo "✅ Versão: " . PHP_VERSION . "<br>";

// Teste de banco
try {
    $pdo = new PDO("mysql:host=localhost;dbname=portal_ouvidoria", "portal_user", "SENHA");
    echo "✅ Conexão MySQL funcionando<br>";
} catch (Exception $e) {
    echo "❌ Erro MySQL: " . $e->getMessage() . "<br>";
}

// Teste de arquivos
$arquivos = ['dashboard.php', 'login.php', 'includes/config_env_simplificado.php'];
foreach ($arquivos as $arquivo) {
    if (file_exists($arquivo)) {
        echo "✅ Arquivo $arquivo encontrado<br>";
    } else {
        echo "❌ Arquivo $arquivo não encontrado<br>";
    }
}
?>
```

### Verificação de Segurança
```bash
# Verificar permissões
find /var/www/html/portal_diretoria -type f -name "*.php" -exec ls -l {} \;
find /var/www/html/portal_diretoria -type d -exec ls -ld {} \;

# Verificar se .env não é acessível
curl -I http://portal.diretoria.local/.env

# Verificar PHP info (remover depois)
php -r "phpinfo();"
```

---

## 🚨 Plano de Rollback

### Cenário: Deploy com Problemas
1. **Parar imediatamente** qualquer alteração adicional
2. **Restaurar backup** do código anterior
3. **Restaurar backup** do banco de dados
4. **Verificar logs** para identificar causa raiz
5. **Testar novamente** em ambiente de staging
6. **Re-deploy gradual** com correções

### Backup Automático
```bash
# Script de backup diário
nano /usr/local/bin/backup_portal.sh

#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/portal"

# Backup do banco
mysqldump -u portal_user -pSENHA --all-databases > $BACKUP_DIR/db_$DATE.sql

# Backup dos arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html/portal_diretoria

# Manter apenas últimos 7 dias
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

# Agendar no crontab
# 0 2 * * * /usr/local/bin/backup_portal.sh
```

---

## 📊 Monitoramento Pós-Deploy

### Métricas Essenciais
- **Disponibilidade**: Uptime do serviço
- **Performance**: Tempo de resposta das páginas
- **Erros**: Taxa de erro 5xx
- **Uso de Recursos**: CPU, memória, disco

### Ferramentas Recomendadas
- **UptimeRobot**: Monitoramento de disponibilidade
- **New Relic**: Monitoramento de performance
- **Sentry**: Rastreamento de erros
- **Grafana**: Dashboards de métricas

---

## 🎯 Próximos Passos

Após deploy bem-sucedido:

1. **Monitorar por 24-48h** o comportamento em produção
2. **Coletar feedback** dos usuários
3. **Otimizar queries** que estiverem lentas
4. **Implementar melhorias** baseadas no uso real
5. **Planejar próximos releases** com novas funcionalidades

---

**📅 Data do Deploy**: _____/_____/_____
**👤 Responsável**: ____________________
**✅ Status**: ☐ Planejado ☐ Em Andamento ☐ Concluído ☐ Rollback