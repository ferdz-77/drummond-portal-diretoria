#!/bin/bash

# 🚀 Script de Deploy - Portal Diretoria
# Execute este script no servidor de produção

echo "🚀 Iniciando deploy do Portal Diretoria..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para log
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERRO: $1${NC}"
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] AVISO: $1${NC}"
}

# Verificar se estamos no diretório correto
if [ ! -d ".git" ]; then
    error "Este script deve ser executado dentro do repositório Git"
    exit 1
fi

log "Verificando branch atual..."
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "production" ]; then
    warning "Você não está na branch production. Mudando..."
    git checkout production
    if [ $? -ne 0 ]; then
        error "Falha ao mudar para branch production"
        exit 1
    fi
fi

log "Fazendo pull das últimas mudanças..."
git pull origin production
if [ $? -ne 0 ]; then
    error "Falha ao fazer pull da branch production"
    exit 1
fi

log "Verificando se há arquivos de configuração..."
if [ ! -f ".env" ]; then
    warning "Arquivo .env não encontrado. Copiando exemplo..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
        warning "Configure o arquivo .env com suas credenciais de produção!"
    fi
fi

log "Ajustando permissões..."
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 666 logs/*.log 2>/dev/null || true

log "Limpando cache do PHP (se OPcache estiver ativo)..."
php -r "if(function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache limpo\n'; } else { echo 'OPcache não ativo\n'; }"

log "Verificando sintaxe PHP..."
PHP_ERRORS=$(find . -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors detected")
if [ ! -z "$PHP_ERRORS" ]; then
    error "Erros de sintaxe PHP encontrados:"
    echo "$PHP_ERRORS"
    exit 1
fi

log "Reiniciando Apache..."
sudo systemctl reload apache2
if [ $? -ne 0 ]; then
    warning "Falha ao recarregar Apache. Tente manualmente: sudo systemctl reload apache2"
fi

log "Verificando conectividade..."
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/dashboard.php)
if [ "$RESPONSE" = "200" ]; then
    log "✅ Deploy concluído com sucesso! Sistema respondendo."
else
    warning "⚠️  Sistema retornou código HTTP: $RESPONSE"
    warning "Verifique os logs do Apache: sudo tail -f /var/log/apache2/error.log"
fi

echo ""
echo "========================================"
echo "🎉 DEPLOY CONCLUÍDO!"
echo "========================================"
echo ""
echo "📊 Status:"
echo "  - Branch: $(git branch --show-current)"
echo "  - Commit: $(git rev-parse --short HEAD)"
echo "  - Data: $(date)"
echo ""
echo "🔍 Verificações manuais recomendadas:"
echo "  1. Acesse: http://localhost/dashboard.php"
echo "  2. Teste login e navegação"
echo "  3. Verifique logs: tail -f logs/error.log"
echo ""
echo "🚨 Em caso de problemas:"
echo "  1. Verifique logs do Apache"
echo "  2. Teste conectividade com banco"
echo "  3. Execute: php -l includes/config_env_simplificado.php"
echo "========================================"