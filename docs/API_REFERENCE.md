# 📡 Referência da API

Documentação completa da API REST do servidor de ativação iOS.

---

## 🌐 Base URL

```
https://seu-servidor.replit.app
```

---

## 📍 Endpoints

### 1. Health Check

Verifica o status do servidor e suas dependências.

**Endpoint:** `GET /health`

**Parâmetros:** Nenhum

**Resposta de Sucesso (200 OK):**

```json
{
  "status": "healthy",
  "timestamp": "2025-11-21T11:25:03-03:00",
  "server": "iOS Activation Bypass API",
  "version": "1.0.0",
  "checks": {
    "cache_directory": {
      "exists": true,
      "writable": true,
      "path": "/home/runner/workspace/public/../cache"
    },
    "assets_directory": {
      "exists": true,
      "readable": true,
      "path": "/home/runner/workspace/public/../assets/Maker"
    },
    "sqlite3": {
      "available": true,
      "version": "3.46.0"
    },
    "zip": {
      "available": true
    }
  }
}
```

**Exemplo de Uso:**

```bash
curl https://seu-servidor.replit.app/health
```

---

### 2. Gerar Payload de Ativação

Gera um payload SQLite customizado para ativação do dispositivo iOS.

**Endpoint:** `GET /`

**Parâmetros Obrigatórios:**

| Parâmetro | Tipo   | Descrição                                                      | Exemplo                                  |
|-----------|--------|----------------------------------------------------------------|------------------------------------------|
| `prd`     | string | Código do produto do iPhone (ProductType)                     | `iPhone14,5`                             |
| `guid`    | string | GUID do dispositivo (obtido dos logs do sistema)              | `12345678-1234-1234-1234-123456789ABC`   |
| `sn`      | string | Número de série do dispositivo                                | `SERIAL123456`                           |

**Formatos Aceitos:**

- **prd**: `iPhoneXX,Y` ou `iPadX,Y` (vírgula é convertida automaticamente para hífen)
- **guid**: UUID no formato `XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX`
- **sn**: String alfanumérica (normalmente 11-12 caracteres)

**Resposta de Sucesso (200 OK):**

```json
{
  "success": true,
  "message": "Payload gerado com sucesso",
  "download_url": "https://seu-servidor.replit.app/cache/downloads_abc123def456.sqlitedb",
  "device_info": {
    "product": "iPhone14,5",
    "guid": "12345678-1234-1234-1234-123456789ABC",
    "serial": "SERIAL123456"
  },
  "generated_at": "2025-11-21T11:30:45-03:00"
}
```

**Resposta de Erro (400 Bad Request):**

```json
{
  "error": "Parâmetros obrigatórios ausentes",
  "required": ["prd", "guid", "sn"],
  "received": {
    "prd": "ausente",
    "guid": "ausente",
    "sn": "ausente"
  },
  "example": "http://127.0.0.1:5000?prd=iPhone14,5&guid=XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX&sn=SERIAL123"
}
```

**Resposta de Erro (404 Not Found - Asset Não Encontrado):**

```json
{
  "error": "Asset MobileGestalt não encontrado para iPhone14-5",
  "path_expected": "assets/Maker/iPhone14-5/com.apple.MobileGestalt.plist",
  "available_models": [
    "iPhone13-2",
    "iPhone14-2",
    "iPhone14-5"
  ],
  "note": "Adicione o arquivo com.apple.MobileGestalt.plist para este modelo"
}
```

**Resposta de Erro (500 Internal Server Error):**

```json
{
  "error": "Falha ao gerar payload",
  "details": "Mensagem de erro detalhada (apenas em modo debug)"
}
```

**Exemplo de Uso:**

```bash
# cURL
curl "https://seu-servidor.replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=SERIAL123"

# wget
wget "https://seu-servidor.replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=SERIAL123"

# Python
import requests
response = requests.get("https://seu-servidor.replit.app/", params={
    "prd": "iPhone14,5",
    "guid": "12345678-1234-1234-1234-123456789ABC",
    "sn": "SERIAL123"
})
print(response.json())

# JavaScript/Node.js
fetch("https://seu-servidor.replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=SERIAL123")
  .then(res => res.json())
  .then(data => console.log(data));

# C# (HttpClient)
var url = "https://seu-servidor.replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=SERIAL123";
var response = await httpClient.GetAsync(url);
var content = await response.Content.ReadAsStringAsync();
```

---

### 3. Download do Payload

Faz o download do arquivo SQLite gerado.

**Endpoint:** `GET /cache/{filename}.sqlitedb`

**Parâmetros:** Nenhum (arquivo retornado pela API principal)

**Resposta de Sucesso:**
- **Content-Type:** `application/x-sqlite3`
- **Content-Disposition:** `attachment; filename="{filename}.sqlitedb"`
- **Corpo:** Binário (arquivo SQLite)

**Resposta de Erro (404 Not Found):**

```json
{
  "error": "Arquivo não encontrado",
  "file": "{filename}.sqlitedb"
}
```

**Exemplo de Uso:**

```bash
# Obter URL de download primeiro
URL=$(curl -s "https://seu-servidor.replit.app/?prd=iPhone14,5&guid=ABC&sn=123" | jq -r '.download_url')

# Baixar arquivo
wget "$URL" -O payload.sqlitedb
```

---

## 🔐 Autenticação

**Atualmente não implementada.** A API é aberta para qualquer cliente.

Para ambientes de produção, recomenda-se:
- Configurar autenticação no nível do hosting (Vercel Password Protection, .htaccess, etc.)
- Usar API Keys via headers customizados
- Implementar rate limiting

---

## 📊 Rate Limiting

**Não implementado por padrão.**

Para hospedagens que precisam de rate limiting:
- **Vercel:** Automático no plano Pro
- **Cloudflare:** Disponível através de regras WAF
- **.htaccess:** Módulo `mod_ratelimit` do Apache

---

## 🧪 Exemplos de Integração

### Cliente Windows (C#)

```csharp
using System.Net.Http;
using System.Text.Json;

class ApiClient
{
    private const string API_URL = "https://seu-servidor.replit.app";
    private static readonly HttpClient client = new HttpClient();
    
    public async Task<string> GetPayloadUrl(string product, string guid, string serial)
    {
        var url = $"{API_URL}?prd={product}&guid={guid}&sn={serial}";
        var response = await client.GetAsync(url);
        var json = await response.Content.ReadAsStringAsync();
        var data = JsonSerializer.Deserialize<JsonDocument>(json);
        return data.RootElement.GetProperty("download_url").GetString();
    }
}
```

### Cliente Python (macOS/Linux)

```python
import requests

API_URL = "https://seu-servidor.replit.app"

def get_payload_url(product, guid, serial):
    response = requests.get(API_URL, params={
        "prd": product,
        "guid": guid,
        "sn": serial
    })
    return response.json()["download_url"]

# Uso
url = get_payload_url("iPhone14,5", "12345678-1234-...", "SERIAL123")
print(f"Download URL: {url}")
```

### JavaScript/TypeScript (Node.js ou Browser)

```typescript
interface PayloadResponse {
  success: boolean;
  download_url: string;
  device_info: {
    product: string;
    guid: string;
    serial: string;
  };
}

async function getPayloadUrl(
  product: string,
  guid: string,
  serial: string
): Promise<string> {
  const params = new URLSearchParams({ prd: product, guid, sn: serial });
  const response = await fetch(`https://seu-servidor.replit.app/?${params}`);
  const data: PayloadResponse = await response.json();
  return data.download_url;
}
```

---

## 🛠️ Códigos de Status HTTP

| Código | Significado                           | Quando Ocorre                                      |
|--------|---------------------------------------|----------------------------------------------------|
| 200    | OK                                    | Requisição processada com sucesso                 |
| 400    | Bad Request                           | Parâmetros obrigatórios ausentes ou inválidos     |
| 404    | Not Found                             | Asset MobileGestalt não encontrado / Arquivo cache não existe |
| 500    | Internal Server Error                 | Erro ao gerar payload (problema no servidor)      |

---

## 📝 Notas Importantes

### Sobre os Arquivos Gerados

- Os arquivos em `/cache` são **temporários** e podem ser deletados após uso
- Em ambientes serverless (Vercel), os arquivos podem expirar rapidamente
- Para uso em produção, considere implementar storage persistente

### Sobre os Assets MobileGestalt

- São **necessários** para gerar payloads válidos
- Devem ser organizados em: `assets/Maker/{modelo}/com.apple.MobileGestalt.plist`
- O nome da pasta deve usar **hífen** ao invés de vírgula: `iPhone14-5` (não `iPhone14,5`)

### Sobre o GUID

- O GUID é obtido dos **logs do sistema** após resetar o dispositivo
- Cada dispositivo tem um GUID único por sessão de ativação
- Sem o GUID correto, a ativação não funcionará

---

## 🐛 Debug e Logs

### Ativar Modo Debug

Configure a variável de ambiente:

```bash
DEBUG_MODE=true
```

No modo debug, as respostas de erro incluem:
- Stack traces completos
- Detalhes de exceções PHP
- Informações sobre caminhos de arquivo

### Verificar Logs

Os logs são salvos em:
```
logs/server_YYYY-MM-DD.log
```

Formato de log:
```
[2025-11-21 11:30:45] [INFO] Payload gerado: iPhone14,5 | GUID: 12345... | SN: SERIAL123
[2025-11-21 11:30:45] [ERROR] Asset não encontrado: assets/Maker/iPhone15-2/com.apple.MobileGestalt.plist
```

---

## 🆘 Suporte

Para problemas ou dúvidas:

1. Verifique se o endpoint `/health` está respondendo
2. Consulte os logs em `logs/`
3. Ative `DEBUG_MODE=true` para mais informações
4. Revise a documentação em `README.md`

---

**API Version:** 1.0.0  
**Última Atualização:** Novembro 2025
