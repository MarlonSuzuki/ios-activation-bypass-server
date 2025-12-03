# 🍎 Servidor de Ativação iOS - Backend PHP

![Status](https://img.shields.io/badge/status-ativo-success) ![PHP](https://img.shields.io/badge/PHP-8.x-blue) ![License](https://img.shields.io/badge/license-MIT-green)

**Servidor backend portável para geração de payloads de ativação iOS**. Este projeto implementa a parte do servidor do sistema de bypass de ativação, gerando dinamicamente os databases SQLite necessários baseados no modelo do dispositivo, GUID e serial number.

---

## 📋 Índice

- [Como Funciona](#-como-funciona)
- [Configuração Rápida](#-configuração-rápida)
- [Como Configurar a URL da API no Cliente](#-como-configurar-a-url-da-api-no-cliente)
- [Exemplos de Uso](#-exemplos-de-uso)
- [Deploy em Outras Plataformas](#-deploy-em-outras-plataformas)
- [Suporte e Troubleshooting](#-suporte-e-troubleshooting)

## 📚 Documentação Completa

| Guia | Descrição |
|------|-----------|
| **[🚀 Início Rápido](INICIO_RAPIDO.md)** | Configure tudo em 5 minutos |
| **[🪟 Cliente Windows - Pronto](Cliente%20Windows/)** | Cliente C# já compilado e pronto para usar! |
| **[🪟 Cliente Windows Setup](docs/client-setup/CLIENTE_WINDOWS.md)** | Como configurar o cliente C# no Windows |
| **[🍎 Cliente macOS](docs/client-setup/CLIENTE_MACOS_PYTHON.md)** | Como configurar o cliente Python no Mac |
| **[☁️ Deploy Vercel](docs/deployment/DEPLOY_VERCEL.md)** | Deploy serverless no Vercel |
| **[🌐 Deploy Tradicional](docs/deployment/DEPLOY_TRADICIONAL.md)** | Deploy em hospedagem Apache/Nginx |
| **[📡 API Reference](docs/API_REFERENCE.md)** | Referência completa da API REST |
| **[🔧 Troubleshooting](docs/TROUBLESHOOTING.md)** | Solução de problemas comuns |

---

## 🔧 Como Funciona

Este servidor **NÃO** precisa de acesso ao dispositivo iOS. Ele apenas:

1. **Recebe** uma requisição HTTP com 3 parâmetros:
   - `prd`: Modelo do dispositivo (ex: `iPhone14,5`)
   - `guid`: GUID extraído dos logs do sistema
   - `sn`: Serial number do dispositivo

2. **Gera** três arquivos SQLite especializados:
   - `fixedfile`: ZIP contendo MobileGestalt.plist
   - `belliloveu.png`: BLDatabaseManager.sqlite
   - `downloads.sqlitedb`: Database final para injeção

3. **Retorna** a URL de download do payload final

4. O **cliente** (rodando no seu Windows ou Mac) faz o download e injeta no dispositivo via USB

---

## ⚡ Configuração Rápida

### No Replit (Já está configurado! 🎉)

1. Clique no botão **"Run"** no topo da página
2. Aguarde o servidor iniciar
3. Copie a URL que aparece no webview (algo como `https://seu-projeto.replit.app`)
4. **Esta URL é sua API!** Anote ela para configurar no cliente

### Verificar se está funcionando

Abra esta URL no navegador:

```
https://sua-url-do-replit.app/health
```

Você deve ver uma resposta JSON indicando que o servidor está saudável.

---

## 🎯 Como Configurar a URL da API no Cliente

### 📱 No Windows (C#)

Você precisa editar o arquivo do cliente Windows e mudar a linha onde diz `REMOTE_API`:

**Arquivo**: `client_windows.cs` (ou similar)

**Encontre esta linha:**
```csharp
private const string REMOTE_API = "https://albert.ip-info.me/files/get.php";
```

**Mude para:**
```csharp
private const string REMOTE_API = "https://SUA-URL-DO-REPLIT.app";
```

#### Passo a Passo Detalhado (Windows):

1. **Abra** o arquivo `client_windows.cs` no Visual Studio ou em qualquer editor de código
2. **Procure** pela linha que começa com `private const string REMOTE_API`
3. **Substitua** a URL antiga pela URL do seu servidor Replit
4. **Salve** o arquivo
5. **Recompile** o projeto (Build → Rebuild Solution no Visual Studio)
6. **Execute** o programa compilado normalmente

**Exemplo Prático:**

```csharp
// ANTES:
private const string REMOTE_API = "https://albert.ip-info.me/files/get.php";

// DEPOIS:
private const string REMOTE_API = "https://meu-servidor-ios.replit.app";
```

---

### 🍎 No macOS (Python)

Se você usar o cliente Python, a configuração é ainda mais simples!

**O cliente Python NÃO PRECISA DE SERVIDOR REMOTO** - ele gera os payloads localmente. Mas se quiser usar este servidor mesmo assim:

**Arquivo**: `offline_bypass.py` (procure no início do arquivo)

**Adicione/Modifique:**
```python
# No início do arquivo, adicione:
SERVER_URL = "https://SUA-URL-DO-REPLIT.app"

# E na função que faz requisição HTTP, use:
url = f"{SERVER_URL}?prd={modelo}&guid={guid}&sn={serial}"
```

**IMPORTANTE**: O script Python original já tem geração offline embutida, então você provavelmente não precisa modificá-lo.

---

## 📚 Exemplos de Uso

### Testar Manualmente no Navegador

```
https://sua-url-replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=SERIAL123
```

**Você deve receber de volta**: Uma URL para download do payload, algo como:
```
https://sua-url-replit.app/cache/downloads_abc123xyz.sqlitedb
```

### Usando cURL (Terminal)

```bash
curl "https://sua-url-replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=SERIAL123"
```

### Com Formato JSON (Opcional)

```bash
curl "https://sua-url-replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=SERIAL123&format=json"
```

Resposta:
```json
{
  "success": true,
  "download_url": "https://sua-url.app/cache/downloads_abc123.sqlitedb",
  "device": {
    "model": "iPhone14,5",
    "guid": "12345678-1234-1234-1234-123456789ABC",
    "serial": "SERIAL123"
  },
  "timestamp": "2025-11-21T14:30:00+00:00"
}
```

---

## 🚀 Deploy em Outras Plataformas

Este servidor é **100% portável** e funciona em qualquer lugar que suporte PHP!

### Opções de Hospedagem:

- ✅ **Replit** (Recomendado) - Já configurado neste projeto
- ✅ **Vercel** - [Ver guia de deployment](docs/deployment/DEPLOY_VERCEL.md)
- ✅ **Hospedagem Tradicional** (Locaweb, Hostinger, etc) - [Ver guia](docs/deployment/DEPLOY_TRADICIONAL.md)
- ✅ **Servidor VPS** (DigitalOcean, AWS, etc) - [Ver guia](docs/deployment/DEPLOY_VPS.md)

### Migrar do Replit para Outro Servidor

1. **Baixe** todo o projeto (botão "Download as ZIP" no Replit)
2. **Extraia** o arquivo ZIP
3. **Faça upload** para o seu novo servidor
4. **Configure** a variável `BASE_URL` (se necessário)
5. **Teste** o endpoint `/health`

---

## 📂 Estrutura de Arquivos

```
.
├── public/              # Ponto de entrada do servidor
│   └── index.php        # API principal
├── config/              # Configurações e lógica
│   ├── config.php       # Configurações gerais
│   ├── PayloadGenerator.php  # Gerador de payloads
│   ├── Logger.php       # Sistema de logs
│   └── templates/       # Templates SQL
│       ├── bl_database.sql
│       └── downloads_database.sql
├── assets/              # ⚠️ IMPORTANTE: Adicione seus arquivos MobileGestalt aqui
│   └── Maker/           # Organize por modelo (ex: iPhone14,5/)
├── cache/               # Arquivos temporários gerados (auto-criado)
├── logs/                # Logs do servidor (auto-criado)
└── README.md            # Este arquivo
```

---

## ⚠️ IMPORTANTE: Assets MobileGestalt

Para que o servidor funcione, você **PRECISA** adicionar os arquivos MobileGestalt para cada modelo de iPhone/iPad que deseja suportar.

### Estrutura Esperada:

```
assets/
└── Maker/
    ├── iPhone14,5/
    │   └── com.apple.MobileGestalt.plist
    ├── iPhone13,2/
    │   └── com.apple.MobileGestalt.plist
    ├── iPhone12,1/
    │   └── com.apple.MobileGestalt.plist
    └── ... (outros modelos)
```

**Como obter estes arquivos**: Eles normalmente vêm no pacote completo do projeto original. Se você não os tem, o servidor retornará um erro informando quais modelos estão disponíveis.

---

## 🔍 Suporte e Troubleshooting

### Servidor não inicia?

```bash
# Verifique se o PHP está instalado
php -v

# Teste manualmente
cd public
php -S 0.0.0.0:5000
```

### Erro "Asset não encontrado"?

Significa que você não tem o arquivo MobileGestalt para aquele modelo de iPhone. Verifique a estrutura em `assets/Maker/`.

### Como ver os logs?

Os logs ficam em `logs/server_YYYY-MM-DD.log`

```bash
# Ver logs do dia atual
cat logs/server_$(date +%Y-%m-%d).log

# Ver em tempo real
tail -f logs/server_$(date +%Y-%m-%d).log
```

### Endpoints Úteis para Debug:

- `/health` - Verifica status do servidor
- `/status` - Informações detalhadas e estatísticas
- `/?prd=...&guid=...&sn=...` - Gera payload

---

## 📝 Variáveis de Ambiente (Opcional)

Você pode personalizar o comportamento do servidor:

```bash
# Ativar modo debug (mostra erros detalhados)
DEBUG_MODE=true

# Definir URL base manualmente
BASE_URL=https://meu-dominio.com

# Tempo de vida do cache (em segundos)
CACHE_LIFETIME=3600

# Timezone
TIMEZONE=America/Sao_Paulo
```

**No Replit**: Use a aba "Secrets" para adicionar estas variáveis.

---

## 📄 Licença

Este projeto é fornecido sob a licença MIT. Consulte o arquivo `LICENSE` para mais detalhes.

---

## ⚠️ Disclaimer

Este software é fornecido apenas para fins educacionais e de pesquisa. Os autores não se responsabilizam por qualquer uso indevido ou danos causados. Certifique-se de ter autorização antes de realizar operações em qualquer dispositivo.

---

## 🆘 Precisa de Ajuda?

1. Verifique a seção [Troubleshooting](#-suporte-e-troubleshooting) acima
2. Verifique os logs em `logs/`
3. Teste o endpoint `/health` para diagnóstico
4. Consulte os guias de deployment em `docs/deployment/`

---

**Desenvolvido com ❤️ para estudos de TI no MIT**
