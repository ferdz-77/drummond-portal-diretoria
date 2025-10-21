# 🚀 Estratégia de Branches - Portal Diretoria

## 📋 Estrutura de Branches

### 🌐 **Branch: `localhost`**
- **Propósito**: Desenvolvimento e testes locais
- **Conteúdo**: Código em desenvolvimento, testes, experimentos
- **Status**: Pode ter código instável ou em desenvolvimento
- **Deploy**: Nunca vai direto para produção

### 🏭 **Branch: `production`**
- **Propósito**: Código pronto para produção
- **Conteúdo**: Apenas código testado e aprovado
- **Status**: Sempre estável e pronto para deploy
- **Deploy**: Esta branch que vai para o servidor de produção

### 📚 **Branch: `master` (main)**
- **Propósito**: Backup histórico e releases
- **Conteúdo**: Versões estáveis liberadas
- **Status**: Código de versões específicas

---

## 🔄 Workflow de Desenvolvimento

### **Fluxo Normal:**
```
Desenvolvimento → Testes → localhost → production → Servidor
```

### **Passos Detalhados:**

#### 1. **Desenvolvimento Local**
```bash
# Sempre trabalhar na branch localhost
git checkout localhost

# Fazer suas mudanças
# ... editar arquivos ...

# Commit das mudanças
git add .
git commit -m "feat: descrição da funcionalidade"
```

#### 2. **Testes e Validação**
```bash
# Testar todas as funcionalidades
# Verificar sintaxe PHP
php -l arquivo.php

# Testar no navegador localhost
# Verificar logs de erro
```

#### 3. **Merge para Production**
```bash
# Quando estiver pronto para produção
git checkout production
git merge localhost

# Resolver conflitos se houver
# Testar novamente
git push origin production
```

#### 4. **Deploy em Produção**
```bash
# No servidor de produção
ssh user@servidor
cd /var/www/html/portal_diretoria
git pull origin production

# Reiniciar serviços se necessário
sudo systemctl reload apache2
```

---

## 🛡️ Regras de Ouro

### ✅ **O que FAZER:**
- [x] **Desenvolver sempre na `localhost`**
- [x] **Testar tudo antes do merge**
- [x] **Fazer commits pequenos e descritivos**
- [x] **Usar a `production` apenas para código estável**
- [x] **Fazer backup antes de qualquer deploy**

### ❌ **O que NÃO FAZER:**
- [ ] **Nunca commitar direto na `production`**
- [ ] **Não fazer merge sem testar**
- [ ] **Não misturar desenvolvimento com produção**
- [ ] **Não fazer deploy sem backup**

---

## 📝 Comandos Essenciais

### **Verificar branch atual:**
```bash
git branch
```

### **Trocar de branch:**
```bash
git checkout localhost    # Para desenvolvimento
git checkout production   # Para produção
```

### **Fazer merge:**
```bash
git checkout production
git merge localhost
```

### **Ver status:**
```bash
git status
git log --oneline -5
```

### **Push das branches:**
```bash
git push origin localhost
git push origin production
```

---

## 🚨 Cenários de Emergência

### **Problema na Produção:**
```bash
# Voltar para versão anterior
git checkout production
git reset --hard HEAD~1  # Volta 1 commit
git push origin production --force
```

### **Conflito no Merge:**
```bash
# Resolver conflitos manualmente
git status  # Ver arquivos com conflito
# Editar arquivos e resolver
git add arquivo_resolvido.php
git commit -m "fix: resolver conflitos de merge"
```

### **Perdeu mudanças locais:**
```bash
# Recuperar do Git
git reflog
git checkout <commit-hash>
```

---

## 📊 Monitoramento

### **Branches no GitHub:**
- **localhost**: https://github.com/ferdz-77/drummond-portal-diretoria/tree/localhost
- **production**: https://github.com/ferdz-77/drummond-portal-diretoria/tree/production

### **Comparação entre branches:**
```bash
git diff localhost..production
```

---

## 🎯 Próximos Passos

1. **Configurar servidor de produção**
2. **Fazer primeiro deploy da branch `production`**
3. **Configurar monitoramento**
4. **Criar rotina de backup automático**
5. **Documentar processos específicos do projeto**

---

**📅 Criado em**: Outubro 2025
**🎯 Status**: Branches configuradas e prontas para uso