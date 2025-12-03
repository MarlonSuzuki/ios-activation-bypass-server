# 🍎 Como Configurar o Cliente macOS (Python)

Este guia mostra **passo a passo** como configurar o cliente Python para macOS para usar o seu servidor de ativação iOS (se necessário).

---

## ⚠️ IMPORTANTE: Você Provavelmente NÃO Precisa Deste Guia!

O script Python (`offline_bypass.py`) já tem **geração de payloads OFFLINE** integrada! Ele **NÃO precisa** de um servidor remoto para funcionar.

O script Python funciona assim:
1. ✅ Gera os payloads **localmente** no seu Mac
2. ✅ Inicia um servidor HTTP **temporário** no seu Mac
3. ✅ O iPhone baixa os arquivos via Wi-Fi **do seu próprio Mac**

**Você só precisa deste guia se quiser forçar o uso de um servidor remoto.**

---

## 📋 Quando Usar um Servidor Remoto

Você pode querer usar um servidor remoto se:

- 🌍 Precisa compartilhar o servidor com outras pessoas
- ☁️ Quer centralizar a geração de payloads
- 🔧 Está desenvolvendo/testando o servidor PHP

Se esse não é o seu caso, **pule este guia e use o script Python original sem modificações!**

---

## 🎯 Como Modificar o Script Python (Se Realmente Precisar)

### Passo 1: Abrir o Script

```bash
# Abra o arquivo no seu editor favorito
nano offline_bypass.py
# ou
code offline_bypass.py
# ou
vim offline_bypass.py
```

### Passo 2: Encontrar a Classe `BypassAutomation`

Procure pela linha que tem `class BypassAutomation:` (aproximadamente linha 296):

```python
class BypassAutomation:
    def __init__(self):
        self.timeouts = {'asset_wait': 300, 'asset_delete_delay': 15, ...}
        # ... mais código ...
```

### Passo 3: Adicionar Variável do Servidor

Logo após `def __init__(self):`, adicione:

```python
class BypassAutomation:
    def __init__(self):
        # ADICIONE ESTA LINHA:
        self.remote_server = "https://sua-url-do-replit.app"
        self.use_remote = True  # True para usar servidor remoto, False para local
        
        self.timeouts = {'asset_wait': 300, 'asset_delete_delay': 15, ...}
        # ... resto do código original ...
```

### Passo 4: Modificar a Função `run()`

Procure pela função `run()` (aproximadamente linha 401) e encontre o trecho que gera o payload:

**CÓDIGO ORIGINAL:**
```python
# 3. Generate Payloads (Offline Logic)
self.log("Generating Payload (Offline)...", "step")
final_db_path = self.generator.generate(
    self.device_info['ProductType'], 
    self.guid, 
    self.device_info['SerialNumber'],
    self.server
)
```

**MODIFICAÇÃO:**
```python
# 3. Generate Payloads
if self.use_remote:
    # Usar servidor remoto
    self.log("Downloading Payload from Remote Server...", "step")
    import urllib.request
    url = f"{self.remote_server}?prd={self.device_info['ProductType']}&guid={self.guid}&sn={self.device_info['SerialNumber']}"
    
    with urllib.request.urlopen(url) as response:
        download_url = response.read().decode('utf-8').strip()
    
    # Baixar o arquivo final
    final_db_path = "downloads.28.sqlitedb"
    with urllib.request.urlopen(download_url) as response:
        with open(final_db_path, 'wb') as f:
            f.write(response.read())
else:
    # Gerar localmente (modo original)
    self.log("Generating Payload (Offline)...", "step")
    final_db_path = self.generator.generate(
        self.device_info['ProductType'], 
        self.guid, 
        self.device_info['SerialNumber'],
        self.server
    )
```

---

## 💾 Passo 5: Salvar e Testar

```bash
# Salvar o arquivo (no nano: Ctrl+O, Enter, Ctrl+X)

# Executar
sudo python3 offline_bypass.py
```

---

## 📝 Exemplo Completo de Modificação

Aqui está um exemplo de como ficaria a classe modificada:

```python
class BypassAutomation:
    def __init__(self):
        # ===== CONFIGURAÇÃO DO SERVIDOR REMOTO =====
        self.remote_server = "https://meu-servidor-ios.replit.app"
        self.use_remote = True  # Mude para False para voltar ao modo offline
        # ===========================================
        
        self.timeouts = {'asset_wait': 300, 'asset_delete_delay': 15, 'reboot_wait': 300, 'syslog_collect': 180}
        self.mount_point = os.path.join(os.path.expanduser("~"), f".ifuse_mount_{os.getpid()}")
        self.afc_mode = None
        self.device_info = {}
        self.guid = None
        
        # Server Components (só usados se use_remote = False)
        if not self.use_remote:
            self.server = LocalServer()
            self.generator = PayloadGenerator(self.server.serve_dir, os.getcwd())
        
        atexit.register(self._cleanup)
```

---

## 🔍 Como Verificar se Está Funcionando

Quando você rodar o script modificado:

```bash
sudo python3 offline_bypass.py
```

Você deve ver algo como:

```
iOS Offline Activator (Python Edition)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
▶ Verifying System Requirements...
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[✓] AFC Transfer Mode: pymobiledevice3

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
▶ Detecting Device...
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Device: iPhone14,2 (iOS 17.1)
UDID: XXXXXXXXX...

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
▶ Downloading Payload from Remote Server...  ← ESTA LINHA INDICA USO DO SERVIDOR REMOTO
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## ❌ Problemas Comuns

### Erro: "urllib.error.HTTPError: HTTP Error 404"

**Solução**: A URL do servidor está incorreta. Verifique:
- URL não tem `/` no final
- Servidor está rodando (teste `/health` no navegador)

### Erro: "urllib.error.URLError: <urlopen error [Errno 8] nodename nor servname provided, or not known>"

**Solução**: Problema de conexão de rede. Verifique:
- Você está conectado à internet
- A URL está digitada corretamente
- Firewall não está bloqueando

### Script não encontra o dispositivo

Isso não tem a ver com o servidor. Verifique:
- iPhone está conectado via USB
- Você confiou no computador (popup no iPhone)
- `libimobiledevice` está instalado: `brew install libimobiledevice`

---

## 🔄 Como Voltar ao Modo Offline (Original)

Simples! Mude uma linha:

```python
self.use_remote = False  # Era True, mude para False
```

Ou simplesmente use o script original sem modificações.

---

## 🎓 Comparação: Local vs Remoto

### Modo Local (Original):
- ✅ Não precisa de internet
- ✅ Mais rápido
- ✅ Mais privado
- ❌ Precisa dos assets MobileGestalt no Mac

### Modo Remoto (Com Servidor):
- ✅ Assets centralizados no servidor
- ✅ Pode compartilhar com outros
- ❌ Precisa de internet
- ❌ Mais lento (download dos arquivos)

---

## 🆘 Precisa de Ajuda?

1. **Primeiro**, tente usar o script original sem modificações (modo offline)
2. Se precisar usar servidor remoto, verifique se a URL está correta
3. Teste a URL no navegador: `https://sua-url/health`
4. Verifique os logs do servidor no Replit

---

**Na dúvida, use o script Python original sem modificações! Ele funciona melhor no macOS. 🍎**
