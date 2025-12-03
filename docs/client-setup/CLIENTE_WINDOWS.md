# 🪟 Como Configurar o Cliente Windows (C#)

Este guia mostra **passo a passo** como configurar o cliente Windows em C# para usar o seu servidor de ativação iOS.

---

## 📋 O Que Você Precisa

- ✅ Arquivo do cliente: `client_windows.cs` (ou executável já compilado)
- ✅ `iOS.exe` (ferramenta go-ios para Windows)
- ✅ Visual Studio ou qualquer compilador C# (se for compilar)
- ✅ URL do seu servidor (ex: `https://meu-projeto.replit.app`)

---

## 🎯 Passo 1: Encontrar o Código que Precisa Mudar

### Se você tem o arquivo `.cs` (código-fonte):

1. **Abra** o arquivo `client_windows.cs` no Visual Studio ou em qualquer editor de texto
   
2. **Procure** pelas primeiras linhas do arquivo, na seção "CONFIGURATION"

3. **Você vai encontrar** algo assim:

```csharp
class Program
{
    // ==========================================
    // CONFIGURATION
    // ==========================================
    private const string TOOL_EXEC = "iOS.exe";
    private const string REMOTE_API = "https://albert.ip-info.me/files/get.php";
    private const int TRIGGER_TIMEOUT = 300;
```

A linha que você precisa mudar é:
```csharp
private const string REMOTE_API = "https://albert.ip-info.me/files/get.php";
```

---

## ✏️ Passo 2: Modificar a URL da API

### Mude a linha para apontar ao seu servidor:

**ANTES:**
```csharp
private const string REMOTE_API = "https://albert.ip-info.me/files/get.php";
```

**DEPOIS:**
```csharp
private const string REMOTE_API = "https://SUA-URL-DO-REPLIT.app";
```

### Exemplo Real:

Se a URL do seu servidor Replit é `https://ios-bypass-servidor.replit.app`, ficaria:

```csharp
private const string REMOTE_API = "https://ios-bypass-servidor.replit.app";
```

⚠️ **IMPORTANTE**: 
- **NÃO** adicione barra `/` no final da URL
- **NÃO** adicione `/index.php` ou qualquer outro caminho
- Apenas a URL base do servidor

---

## 💾 Passo 3: Salvar e Compilar

### Opção A: Compilar no Visual Studio

1. **Salve** o arquivo (`Ctrl + S`)
2. Clique em **Build** → **Rebuild Solution** (ou `Ctrl + Shift + B`)
3. O executável estará em `bin/Debug/` ou `bin/Release/`

### Opção B: Compilar via linha de comando

Abra o **Command Prompt** ou **PowerShell** na pasta do projeto:

```powershell
# Compilar
csc client_windows.cs

# Isso gera o arquivo client_windows.exe
```

---

## 🚀 Passo 4: Executar o Cliente

1. **Certifique-se** que o `iOS.exe` está na mesma pasta que o executável
2. **Conecte** seu iPhone via USB
3. **Execute** o programa:

```powershell
.\client_windows.exe
```

---

## 🔍 Como Verificar se Está Funcionando

Quando você rodar o cliente, ele vai:

1. Detectar seu iPhone conectado
2. **Fazer uma requisição para o servidor** (aqui é onde usa a URL que você configurou)
3. Se tudo estiver certo, você verá algo como:

```
[*] Waiting for device...
Connected: iPhone 13 Pro (iPhone14,2)
UDID: XXXXXX...

[!] Press ENTER to begin the sequence...

=== Phase 1: Initial Reset ===
...
=== Phase 3: Server Authorization ===
[*] Payload URL: https://sua-url.replit.app/cache/downloads_abc123.sqlitedb
```

👆 **Esta linha** mostra que o cliente conseguiu se comunicar com o servidor!

---

## ❌ Problemas Comuns

### Erro: "Could not find iOS.exe"

**Solução**: Coloque o arquivo `iOS.exe` na mesma pasta que o executável do cliente.

### Erro: "No device detected"

**Soluções**:
- Certifique-se que o iPhone está conectado via USB
- Instale o iTunes ou os drivers Apple para Windows
- Confie no computador no iPhone quando aparecer o popup

### Erro: "Server refused connection" ou "404"

**Soluções**:
- Verifique se a URL do servidor está correta (sem `/` no final)
- Teste a URL no navegador: `https://sua-url.replit.app/health`
- Certifique-se que o servidor Replit está rodando (clique em "Run")

### Erro: "GUID not found in logs"

Isso é normal na primeira tentativa. O cliente precisa reiniciar o iPhone e escanear os logs. Aguarde alguns minutos.

---

## 📝 Exemplo de Arquivo Completo Modificado

Aqui está como deve ficar o início do seu arquivo após a modificação:

```csharp
using System;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Net.Http;
using System.Text.Json;
using System.Text.RegularExpressions;
using System.Threading;
using System.Threading.Tasks;
using System.Collections.Generic;

namespace GoIosBypassSuite
{
    public class GoIosDevice
    {
        public string Udid { get; set; }
        public string DeviceName { get; set; }
        public string ProductVersion { get; set; }
        public string ProductType { get; set; }
        public string HardwareModel { get; set; }
        public Dictionary<string, object> DeviceValues { get; set; }
    }

    class Program
    {
        // ==========================================
        // CONFIGURATION
        // ==========================================
        private const string TOOL_EXEC = "iOS.exe";
        
        // ✅ LINHA MODIFICADA AQUI:
        private const string REMOTE_API = "https://meu-servidor-ios.replit.app";
        
        private const int TRIGGER_TIMEOUT = 300;
        
        // ... resto do código ...
    }
}
```

---

## 🎓 Dicas Extras

### Testar a URL Manualmente

Antes de rodar o cliente, você pode testar se o servidor está respondendo:

1. Abra o navegador
2. Cole esta URL (substitua os valores):

```
https://sua-url.replit.app/?prd=iPhone14,2&guid=12345678-1234-1234-1234-123456789ABC&sn=SERIAL123
```

3. Se funcionar, você verá uma URL de download sendo retornada

### Modo Debug

Se quiser ver mais detalhes do que está acontecendo, você pode adicionar logs extras no código do cliente ou verificar os logs do servidor em `logs/` no Replit.

---

## 🆘 Precisa de Ajuda?

1. Verifique se a URL está correta (sem `/` no final)
2. Teste o endpoint `/health` do servidor no navegador
3. Certifique-se que recompilou o código após fazer as modificações
4. Verifique os logs do servidor no Replit para ver se as requisições estão chegando

---

**Pronto! Agora seu cliente Windows está configurado para usar o seu próprio servidor! 🎉**
