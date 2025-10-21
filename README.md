# Portal Diretoria - Sistema de Gestão de Chamados

## 📋 Descrição

Sistema web completo para gestão e monitoramento de chamados de atendimento em múltiplos portais institucionais. Desenvolvido em PHP com interface responsiva usando Bootstrap e Chart.js para visualizações.

## 🚀 Funcionalidades

### Dashboard Executivo
- **Visualização Consolidada**: Métricas de todos os portais em uma única tela
- **Gráficos Interativos**: Status de chamados, SLA e tipos de manifestação
- **Sistema de Filtros**: Por período, status e portal específico
- **Notificações em Tempo Real**: Alertas sobre novos chamados e SLA crítico

### Portais Integrados
- **Ouvidoria**: Manifestações e reclamações
- **EAD**: Ensino a Distância
- **Processo Seletivo**: Concursos e seleções
- **Secretaria Acadêmica**: Serviços administrativos
- **Financeiro**: Cobranças e pagamentos
- **Ex-Aluno**: Relacionamento com egressos

### Recursos Técnicos
- **Autenticação Segura**: Sistema de login com sessões
- **Conexão Multi-Banco**: Suporte a diferentes bancos de dados
- **API REST**: Endpoints para dados filtrados
- **Interface Responsiva**: Compatível com desktop e mobile
- **Sistema de Logs**: Rastreamento de erros e atividades

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 8.2+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework CSS**: Bootstrap 5.3
- **Gráficos**: Chart.js
- **Banco de Dados**: MySQL/MariaDB
- **Servidor Web**: Apache 2.4+
- **Versionamento**: Git

## 📦 Instalação e Configuração

### Pré-requisitos
- PHP 8.2 ou superior
- MySQL/MariaDB 5.7+
- Apache 2.4+ com mod_rewrite
- Composer (opcional, para dependências)

### Passos de Instalação

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/SEU_USERNAME/portal-diretoria.git
   cd portal-diretoria
   ```

2. **Configure o ambiente**:
   - Copie `.env.example` para `.env`
   - Configure as variáveis de ambiente no arquivo `.env`

3. **Configure o banco de dados**:
   - Execute os scripts SQL em `backups/` para criar as tabelas
   - Configure as conexões nos arquivos `includes/config_env_simplificado.php`

4. **Configure o servidor web**:
   - Aponte o DocumentRoot para a pasta do projeto
   - Certifique-se de que o `.htaccess` está sendo processado

5. **Teste a instalação**:
   - Acesse `http://localhost/login.php`
   - Faça login com as credenciais padrão

## 🔧 Configuração de Produção

### Variáveis de Ambiente (.env)
```env
APP_ENV=production
DB_HOST=localhost
DB_USER=seu_usuario
DB_PASS=sua_senha
DB_OUVIDORIA=nome_banco_ouvidoria
DB_EAD=nome_banco_ead
DB_PROCESSO_SELETIVO=nome_banco_processo
DB_SECRETARIA=nome_banco_secretaria
DB_FINANCEIRO=nome_banco_financeiro
DB_EXALUNO=nome_banco_exaluno
```

### Permissões de Arquivo
```bash
chmod 755 /caminho/para/portal-diretoria
chmod 644 /caminho/para/portal-diretoria/*.php
chmod 666 /caminho/para/portal-diretoria/logs/*.log
```

### Configuração Apache
```apache
<VirtualHost *:80>
    ServerName portal.diretoria.local
    DocumentRoot /caminho/para/portal-diretoria

    <Directory /caminho/para/portal-diretoria>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 🚀 Checklist para Deploy em Produção

### ✅ Preparação
- [ ] **Backup completo**: Criado backup .zip do projeto
- [ ] **Controle de versão**: Projeto versionado no Git
- [ ] **Testes funcionais**: Todas as funcionalidades testadas
- [ ] **Sintaxe validada**: Todos os arquivos PHP verificados

### ✅ Configuração do Servidor
- [ ] **PHP 8.2+**: Versão compatível instalada
- [ ] **MySQL/MariaDB**: Banco de dados configurado
- [ ] **Apache**: Servidor web com mod_rewrite
- [ ] **Permissões**: Arquivos e pastas com permissões corretas

### ✅ Segurança
- [ ] **Variáveis de ambiente**: Arquivo .env configurado
- [ ] **Credenciais**: Senhas fortes e únicas
- [ ] **HTTPS**: Certificado SSL instalado
- [ ] **Firewall**: Portas desnecessárias fechadas

### ✅ Otimização
- [ ] **Cache**: Sistema de cache configurado
- [ ] **Compressão**: GZIP habilitado
- [ ] **Minificação**: CSS/JS otimizados
- [ ] **CDN**: Recursos estáticos em CDN

### ✅ Monitoramento
- [ ] **Logs**: Sistema de logs configurado
- [ ] **Alertas**: Notificações de erro ativas
- [ ] **Backup**: Rotina de backup automática
- [ ] **Performance**: Monitoramento de recursos

## 📊 Estrutura do Projeto

```
portal-diretoria/
├── css/                    # Folhas de estilo
├── data/                   # Arquivos de dados por portal
├── includes/               # Arquivos de configuração e funções
├── js/                     # Scripts JavaScript
├── backups/                # Backups de banco de dados
├── PHPMailer/              # Biblioteca de envio de emails
├── .env.example           # Exemplo de configuração
├── .gitignore             # Arquivos ignorados pelo Git
├── dashboard.php          # Página principal do dashboard
├── detalhes.php           # Página de detalhes por portal
├── login.php              # Página de autenticação
└── README.md              # Esta documentação
```

## 🔍 Monitoramento e Logs

### Arquivos de Log
- `logs/error.log`: Erros do PHP
- `logs/access.log`: Acessos ao sistema
- `production_error.log`: Erros em produção

### Comandos Úteis para Debug
```bash
# Verificar sintaxe PHP
php -l arquivo.php

# Verificar logs em tempo real
tail -f logs/error.log

# Testar conectividade com banco
php teste_conexao.php
```

## 🐛 Troubleshooting

### Problemas Comuns

**Erro 500 Internal Server Error**
- Verifique os logs em `logs/error.log`
- Confirme se todas as dependências estão instaladas
- Verifique permissões de arquivo

**Problemas de Conexão com Banco**
- Teste a conectividade com `teste_conexao.php`
- Verifique as credenciais no arquivo de configuração
- Confirme se o banco está acessível

**Gráficos não Carregam**
- Verifique se Chart.js está sendo carregado
- Confirme se os dados estão sendo retornados pela API
- Verifique o console do navegador (F12)

## 📈 Performance

### Otimizações Implementadas
- **Cache de Consultas**: Resultados armazenados em sessão
- **Lazy Loading**: Dados carregados sob demanda
- **Compressão GZIP**: Redução do tamanho das respostas
- **Minificação**: CSS e JS otimizados

### Monitoramento
- SLA médio por portal
- Tempo de resposta das consultas
- Taxa de erro das requisições
- Uso de memória e CPU

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Para suporte técnico ou dúvidas:
- Email: suporte@portal.diretoria.local
- Documentação: [Wiki do Projeto](https://github.com/SEU_USERNAME/portal-diretoria/wiki)

---

**Última atualização**: Outubro 2025
**Versão**: 1.0.0
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
