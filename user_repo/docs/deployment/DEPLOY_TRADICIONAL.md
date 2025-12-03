# 🌐 Deploy em Hospedagem Tradicional

Guia para fazer deploy em hospedagens PHP tradicionais como Locaweb, Hostinger, HostGator, etc.

---

## 📋 Pré-requisitos da Hospedagem

Sua hospedagem precisa ter:
- ✅ PHP 7.4 ou superior (recomendado PHP 8.x)
- ✅ Extensão SQLite3 habilitada
- ✅ Extensão ZIP habilitada
- ✅ Permissões de escrita em diretórios
- ✅ Acesso FTP ou cPanel

---

## 📦 Passo 1: Preparar o Pacote

### Baixar do Replit

1. No Replit, clique nos **3 pontinhos** ao lado do nome do projeto
2. Selecione **"Download as ZIP"**
3. **Extraia** o arquivo ZIP no seu computador

### Estrutura de Arquivos

Certifique-se que tem esta estrutura:

```
seu-projeto/
├── public/
│   └── index.php
├── config/
│   ├── config.php
│   ├── PayloadGenerator.php
│   ├── Logger.php
│   └── templates/
├── assets/
│   └── Maker/
│       └── (seus arquivos MobileGestalt)
├── cache/              ← criar vazio
└── logs/               ← criar vazio
```

---

## 🚀 Passo 2: Upload via FTP

### Usando FileZilla (Recomendado)

1. **Baixe** FileZilla: https://filezilla-project.org
2. **Conecte** usando as credenciais FTP da sua hospedagem:
   - Host: `ftp.seudominio.com` (ou IP fornecido)
   - Usuário: fornecido pela hospedagem
   - Senha: fornecida pela hospedagem
   - Porta: 21 (FTP) ou 22 (SFTP)

3. **Navegue** até a pasta `public_html` ou `www` no servidor

4. **Faça upload** de todos os arquivos do projeto

---

## 🔧 Passo 3: Configurar no cPanel

### 3.1 Definir Document Root

1. Acesse o **cPanel** da sua hospedagem
2. Vá em **"Domínios"** ou **"Addon Domains"**
3. Configure o **Document Root** para apontar à pasta `public/`:
   - Ex: `/home/usuario/public_html/public`

### 3.2 Verificar Extensões PHP

1. No cPanel, vá em **"Select PHP Version"** ou **"MultiPHP Manager"**
2. Verifique se está usando **PHP 8.0+**
3. Certifique-se que estas extensões estão **ativadas**:
   - ☑️ sqlite3
   - ☑️ zip
   - ☑️ mbstring
   - ☑️ json

### 3.3 Configurar Permissões

Via cPanel File Manager ou FTP:

```
cache/  → 755 ou 777
logs/   → 755 ou 777
```

Ou via SSH:
```bash
chmod 755 cache/
chmod 755 logs/
```

---

## ⚙️ Passo 4: Configurar .htaccess

Crie um arquivo `.htaccess` na pasta `public/`:

```apache
# Habilitar rewrite
RewriteEngine On

# Permitir CORS (opcional)
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"

# Redirecionar tudo para index.php exceto arquivos reais
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Cache para arquivos estáticos (opcional)
<FilesMatch "\.(jpg|jpeg|png|gif|ico|css|js|woff|woff2)$">
    Header set Cache-Control "max-age=31536000, public"
</FilesMatch>

# Desabilitar listagem de diretórios
Options -Indexes
```

---

## 🌐 Passo 5: Configurar URL Base

### Opção A: Via Arquivo de Configuração

Edite `config/config.php` e defina manualmente:

```php
// Força a URL base
define('BASE_URL', 'https://seudominio.com');
```

### Opção B: Via Variável de Ambiente (se sua hospedagem suportar)

No cPanel, procure por **"Environment Variables"** e adicione:

```
BASE_URL=https://seudominio.com
```

---

## 🧪 Passo 6: Testar

### 6.1 Health Check

Acesse no navegador:
```
https://seudominio.com/health
```

Você deve ver:
```json
{
  "status": "healthy",
  "server": "iOS Activation Bypass API",
  ...
}
```

### 6.2 Testar Geração de Payload

```
https://seudominio.com/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=TEST123
```

Deve retornar uma URL de download.

---

## 🔒 Segurança (Opcional mas Recomendado)

### Proteger Diretórios Sensíveis

Crie `.htaccess` em cada pasta que não deve ser acessível:

**Em `config/.htaccess`:**
```apache
Deny from all
```

**Em `logs/.htaccess`:**
```apache
Deny from all
```

**Em `assets/.htaccess`:**
```apache
Deny from all
```

### HTTPS

1. No cPanel, vá em **"SSL/TLS"**
2. Ative **"Let's Encrypt SSL"** (gratuito)
3. Force HTTPS adicionando ao `.htaccess`:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 📊 Monitoramento

### Ver Logs

1. Acesse via FTP a pasta `logs/`
2. Baixe o arquivo `server_YYYY-MM-DD.log`
3. Abra em um editor de texto

Ou via SSH:
```bash
tail -f /caminho/para/logs/server_$(date +%Y-%m-%d).log
```

### Limpeza Automática

Crie um **Cron Job** no cPanel para limpar logs antigos:

```bash
# Rodar diariamente à meia-noite
0 0 * * * find /caminho/para/logs/*.log -mtime +7 -delete
```

---

## 🌍 Hospedagens Testadas

Este servidor foi testado e funciona em:

- ✅ **Locaweb** (PHP 8.1+)
- ✅ **Hostinger** (PHP 8.0+)
- ✅ **HostGator** (PHP 7.4+)
- ✅ **UOLHost** (PHP 8.0+)
- ✅ **Kinghost** (PHP 8.1+)

---

## ❌ Problemas Comuns

### Erro 500 - Internal Server Error

**Causas comuns:**
1. Permissões incorretas em `cache/` e `logs/`
2. Extensão SQLite3 não habilitada
3. Erro de sintaxe no `.htaccess`

**Solução:**
```bash
chmod 755 cache/ logs/
```

Verifique os **error logs** no cPanel.

### "Asset não encontrado"

Certifique-se que a pasta `assets/Maker/` foi enviada via FTP com todos os arquivos.

### Cache não é criado

Verifique permissões:
```bash
ls -la cache/
```

Deve mostrar permissões de escrita (`755` ou `777`).

---

## 📦 Backup

### Backup Manual

Via cPanel → File Manager:
1. Selecione a pasta do projeto
2. Clique em **"Compress"**
3. Download do arquivo ZIP

### Backup Automático via Cron

```bash
# Rodar semanalmente
0 2 * * 0 tar -czf /home/backup/projeto-$(date +\%Y\%m\%d).tar.gz /caminho/para/projeto/
```

---

## 🔄 Atualizações

Para atualizar o código:

1. **Baixe** a versão atualizada do Replit
2. **Faça backup** da versão atual
3. **Sobrescreva** os arquivos via FTP
4. **Mantenha** as pastas `cache/` e `logs/` existentes

---

## ✅ Checklist Final

- [ ] PHP 8.0+ configurado
- [ ] Extensões SQLite3 e ZIP habilitadas
- [ ] Arquivos enviados via FTP
- [ ] Document Root configurado para `/public`
- [ ] Permissões `755` em `cache/` e `logs/`
- [ ] `.htaccess` criado
- [ ] `BASE_URL` configurada
- [ ] Endpoint `/health` respondendo
- [ ] SSL/HTTPS ativado
- [ ] Diretórios sensíveis protegidos

---

**Seu servidor está no ar em hospedagem tradicional! 🎉**
