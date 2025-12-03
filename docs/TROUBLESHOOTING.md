# 🔧 Solução de Problemas (Troubleshooting)

Guia completo para resolver os problemas mais comuns do servidor de ativação iOS.

---

## 🔍 Diagnóstico Rápido

### Passo 1: Verificar se o Servidor Está Respondendo

```bash
curl https://sua-url.replit.app/health
```

**✅ Se funcionar:** Você verá um JSON com `"status": "healthy"`  
**❌ Se não funcionar:** Vá para [Servidor Não Responde](#servidor-não-responde)

### Passo 2: Testar Geração de Payload

```bash
curl "https://sua-url.replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=TEST123"
```

**✅ Se funcionar:** Você verá uma URL de download  
**❌ Se retornar erro:** Veja a seção do erro específico abaixo

---

## ❌ Problemas do Servidor

### Servidor Não Responde

**Sintomas:**
- Timeout ao acessar qualquer URL
- "ERR_CONNECTION_REFUSED"
- Página em branco

**Soluções:**

1. **No Replit:**
   - Clique no botão **"Run"** para iniciar o servidor
   - Aguarde até ver "PHP Development Server started"
   - Verifique se há erros na aba "Console"

2. **Hospedagem Tradicional:**
   - Verifique se o servidor Apache/Nginx está rodando
   - Verifique logs do servidor: `/var/log/apache2/error.log`
   - Certifique-se que o PHP está instalado: `php -v`

3. **Vercel:**
   - Acesse o dashboard e veja se há erros de build
   - Verifique os logs de deployment
   - Certifique-se que `vercel.json` está configurado corretamente

---

### Erro 500 - Internal Server Error

**Sintomas:**
- Página branca com "Internal Server Error"
- Erro genérico do servidor

**Diagnóstico:**

1. **Ative o modo debug:**
   ```bash
   # No Replit: vá em Secrets e adicione
   DEBUG_MODE=true
   ```

2. **Verifique os logs:**
   ```bash
   # Leia o arquivo de log de hoje
   cat logs/server_$(date +%Y-%m-%d).log
   ```

**Causas Comuns:**

#### Extensão SQLite3 não disponível

**Como verificar:**
```bash
php -m | grep sqlite3
```

**Solução (hospedagem tradicional):**
```bash
# Ubuntu/Debian
sudo apt-get install php-sqlite3

# CentOS/RHEL
sudo yum install php-sqlite3

# Reinicie o servidor
sudo service apache2 restart
```

#### Extensão ZIP não disponível

**Como verificar:**
```bash
php -m | grep zip
```

**Solução:**
```bash
# Ubuntu/Debian
sudo apt-get install php-zip

# CentOS/RHEL
sudo yum install php-zip
```

#### Permissões de Diretório

**Como verificar:**
```bash
ls -la cache/ logs/
```

**Solução:**
```bash
# Dar permissões de escrita
chmod 755 cache/ logs/

# Ou permissões totais (se necessário)
chmod 777 cache/ logs/
```

---

### Erro 400 - Bad Request

**Sintomas:**
- JSON retornando `"error": "Parâmetros obrigatórios ausentes"`

**Causa:** Faltam parâmetros na requisição

**Solução:** Certifique-se de enviar todos os parâmetros:
```
?prd=iPhone14,5&guid=XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX&sn=SERIAL123
```

**Exemplo correto:**
```bash
# ✅ CORRETO
curl "https://sua-url.replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=TEST123"

# ❌ ERRADO (falta guid e sn)
curl "https://sua-url.replit.app/?prd=iPhone14,5"
```

---

### Erro 404 - Asset Não Encontrado

**Sintomas:**
```json
{
  "error": "Asset MobileGestalt não encontrado para iPhone14-5",
  "path_expected": "assets/Maker/iPhone14-5/com.apple.MobileGestalt.plist"
}
```

**Causa:** Arquivo MobileGestalt para aquele modelo não existe no servidor

**Solução:**

1. **Verifique quais modelos estão disponíveis:**
   ```bash
   ls assets/Maker/
   ```

2. **Adicione o arquivo para o modelo desejado:**
   ```bash
   # Criar pasta (use hífen, não vírgula!)
   mkdir -p assets/Maker/iPhone14-5
   
   # Copie o arquivo .plist
   cp /caminho/para/com.apple.MobileGestalt.plist assets/Maker/iPhone14-5/
   ```

3. **Verifique se foi adicionado corretamente:**
   ```bash
   ls -la assets/Maker/iPhone14-5/
   # Deve mostrar: com.apple.MobileGestalt.plist
   ```

**⚠️ IMPORTANTE:** Use **hífen** `-` no nome da pasta, não vírgula `,`:
- ✅ Correto: `iPhone14-5`
- ❌ Errado: `iPhone14,5`

---

## 🔌 Problemas do Cliente

### Cliente Não Conecta ao Servidor

**Sintomas (Windows C#):**
```
Server refused connection
HTTP Error 404
Connection timeout
```

**Sintomas (Mac Python):**
```
urllib.error.URLError
Connection refused
```

**Diagnóstico:**

1. **Verifique se a URL está correta:**
   ```csharp
   // C# - deve estar assim:
   private const string REMOTE_API = "https://sua-url.replit.app";
   // ❌ NÃO adicione / no final!
   // ❌ NÃO adicione /index.php!
   ```

2. **Teste a URL no navegador:**
   ```
   https://sua-url.replit.app/health
   ```
   Deve retornar JSON com status "healthy"

3. **Certifique-se que recompilou o cliente:**
   ```bash
   # Windows
   csc client_windows.cs
   
   # Verifique se o .exe foi atualizado
   ls -l client_windows.exe
   ```

**Soluções:**

- **URL Incorreta:** Copie a URL exata do Replit (sem adicionar `/` ou `/index.php`)
- **Servidor Offline:** Reinicie o servidor no Replit (botão "Run")
- **Firewall:** Verifique se o firewall não está bloqueando a conexão

---

### Cliente Não Detecta o iPhone

**Sintomas:**
```
No device detected
Waiting for device... (infinitamente)
Could not find iOS device
```

**⚠️ Importante:** Isso **não é problema do servidor!** É problema de comunicação USB.

**Soluções:**

#### Windows:
1. **Instale iTunes ou Apple Mobile Device Support:**
   - Baixe iTunes: https://www.apple.com/itunes/
   - Ou apenas os drivers: https://support.apple.com/downloads/

2. **Verifique se o iPhone aparece:**
   ```bash
   .\iOS.exe list
   ```

3. **Confie no computador:**
   - Conecte o iPhone
   - Quando aparecer popup no iPhone: "Confiar neste computador?"
   - Toque em **"Confiar"**
   - Digite a senha do iPhone

#### macOS:
1. **Instale libimobiledevice:**
   ```bash
   brew install libimobiledevice
   ```

2. **Verifique se detecta o iPhone:**
   ```bash
   idevice_id -l
   ```

3. **Confie no computador** (mesmo procedimento do Windows)

---

### Erro "GUID not found in logs"

**Sintomas:**
```
[!] Could not locate GUID in syslog
GUID extraction failed
```

**Causa:** O GUID só aparece nos logs do sistema **após** o primeiro reset do iPhone.

**Solução (Processo Normal):**

1. Cliente faz o **primeiro reset** do iPhone
2. iPhone reinicia
3. Cliente **aguarda** e coleta logs do sistema
4. **GUID é extraído** dos logs
5. Cliente faz a requisição ao servidor **com o GUID**

**Se continuar falhando:**
- Aguarde mais tempo (pode levar 2-3 minutos)
- Verifique se o syslog está sendo coletado
- No Mac, certifique-se que tem permissões: `sudo python3 offline_bypass.py`

---

## 📦 Problemas de Assets

### Lista de Modelos Disponíveis Está Vazia

**Sintomas:**
```json
{
  "available_models": []
}
```

**Causa:** Nenhum arquivo MobileGestalt foi adicionado ao servidor

**Solução:**

1. **Adicione pelo menos um modelo:**
   ```bash
   mkdir -p assets/Maker/iPhone14-5
   # Copie o arquivo .plist para dentro desta pasta
   ```

2. **Verifique a estrutura:**
   ```bash
   tree assets/Maker/
   # Deve mostrar:
   # assets/Maker/
   # └── iPhone14-5/
   #     └── com.apple.MobileGestalt.plist
   ```

---

### Arquivo .plist Corrompido

**Sintomas:**
```
Failed to parse plist
Invalid MobileGestalt file
```

**Diagnóstico:**
```bash
# Verifique se é um arquivo plist válido
file assets/Maker/iPhone14-5/com.apple.MobileGestalt.plist
# Deve mostrar: XML 1.0 document text, ASCII text

# Ou use plutil (macOS)
plutil -lint assets/Maker/iPhone14-5/com.apple.MobileGestalt.plist
```

**Solução:**
- Obtenha uma cópia válida do arquivo
- Certifique-se que não foi corrompido durante upload/download
- Verifique se não é um arquivo de texto comum renomeado

---

## 🗂️ Problemas de Cache

### Cache Não é Criado

**Sintomas:**
```
Failed to write cache file
Permission denied on cache/
```

**Diagnóstico:**
```bash
ls -la cache/
# Verifique as permissões
```

**Solução:**
```bash
# Dar permissões de escrita
chmod 755 cache/

# Se ainda não funcionar
chmod 777 cache/

# Verifique se há espaço em disco
df -h
```

---

### Arquivos de Cache Não São Servidos

**Sintomas:**
```
404 Not Found ao baixar do /cache/
```

**Causa (hospedagem tradicional):** Apache/Nginx bloqueando acesso

**Solução (.htaccess):**
```apache
# Em public/.htaccess
<Directory "../cache">
    Options -Indexes
    Require all granted
</Directory>
```

**Solução (Nginx):**
```nginx
location /cache/ {
    alias /caminho/completo/para/cache/;
    autoindex off;
}
```

---

## 🌐 Problemas de Deploy

### Vercel: Arquivos Desaparecem

**Causa:** Vercel é serverless, arquivos em `/cache` são **efêmeros**

**Solução:** Use Vercel Blob Storage para persistência:
```bash
npm install @vercel/blob
```

Veja detalhes em `docs/deployment/DEPLOY_VERCEL.md`

---

### Hospedagem Tradicional: Document Root Errado

**Sintomas:**
- Código PHP é exibido como texto
- Download de arquivos `.php` ao invés de executar

**Solução:**

1. **Configure Document Root para `/public`:**
   - No cPanel: Domínios → Editar → Document Root: `/public_html/public`

2. **Adicione .htaccess:**
   ```apache
   # Em public/.htaccess
   AddHandler application/x-httpd-php .php
   ```

---

## 📊 Como Ver Logs Detalhados

### No Replit:
1. Ative modo debug em "Secrets":
   ```
   DEBUG_MODE=true
   ```
2. Veja logs na aba "Console"
3. Leia o arquivo: `logs/server_YYYY-MM-DD.log`

### Hospedagem Tradicional:
```bash
# Logs do PHP
tail -f /var/log/php_errors.log

# Logs do Apache
tail -f /var/log/apache2/error.log

# Logs da aplicação
tail -f logs/server_$(date +%Y-%m-%d).log
```

### Vercel:
1. Acesse: https://vercel.com/seu-usuario/seu-projeto
2. Vá em "Deployments"
3. Clique no deployment mais recente
4. Vá em "Functions" → Veja os logs

---

## ✅ Checklist de Diagnóstico Completo

Use este checklist quando tiver problemas:

**Servidor:**
- [ ] Endpoint `/health` retorna status "healthy"?
- [ ] Extensão SQLite3 está habilitada? (`php -m | grep sqlite3`)
- [ ] Extensão ZIP está habilitada? (`php -m | grep zip`)
- [ ] Diretórios `cache/` e `logs/` têm permissão de escrita?
- [ ] Há espaço em disco disponível?

**Assets:**
- [ ] Pasta `assets/Maker/` existe?
- [ ] Há pelo menos um modelo com arquivo `.plist`?
- [ ] Nome da pasta usa hífen (iPhone14-5) e não vírgula?
- [ ] Arquivo `.plist` é válido e não está corrompido?

**Cliente:**
- [ ] URL do servidor está correta (sem `/` no final)?
- [ ] Cliente foi recompilado após mudar a URL?
- [ ] iPhone está conectado via USB?
- [ ] iPhone confia no computador?
- [ ] Drivers Apple (Windows) ou libimobiledevice (Mac) instalados?

**Rede:**
- [ ] Firewall não está bloqueando?
- [ ] Servidor é acessível pela internet?
- [ ] CORS está configurado (se necessário)?

---

## 🆘 Ainda Não Resolveu?

1. **Ative DEBUG_MODE** e capture os logs completos
2. **Reproduza** o problema e anote a mensagem de erro exata
3. **Verifique** a seção específica deste guia
4. **Consulte** a documentação em `README.md` e `docs/API_REFERENCE.md`

---

**Lembre-se:** A maioria dos problemas é relacionada a permissões, configuração de URL ou falta de assets!
