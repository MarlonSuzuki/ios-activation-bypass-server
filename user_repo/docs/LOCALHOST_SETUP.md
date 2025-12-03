# 🖥️ Executar Localmente (sem Servidor Remoto)

## 📋 Opção 1: Servidor PHP Local (Recomendado)

### No Windows (PowerShell com PHP instalado):

1. **Instale PHP** (se não tiver):
   - Baixe em: https://www.php.net/downloads.php
   - Ou use Windows Package Manager: `winget install PHP.PHP`

2. **Na pasta do projeto, inicie o servidor PHP:**
   ```powershell
   cd C:\caminho\do\projeto
   php -S localhost:5000 -t public
   ```

3. **Use a URL no cliente:**
   - Abra o cliente GUI
   - Selecione "Localhost" no modo de servidor
   - URL ficará: `http://localhost:5000`

---

## 📋 Opção 2: Docker (Portátil em Qualquer PC)

### Instale Docker Desktop:
- Windows: https://www.docker.com/products/docker-desktop

### Crie um arquivo `Dockerfile` na raiz do projeto:
```dockerfile
FROM php:8.1-cli
WORKDIR /app
COPY . .
EXPOSE 5000
CMD ["php", "-S", "0.0.0.0:5000", "-t", "public"]
```

### Execute no PowerShell:
```powershell
docker build -t bypass-server .
docker run -p 5000:5000 bypass-server
```

---

## 📋 Opção 3: Vercel (Deploy Rápido)

1. Crie conta em: https://vercel.com
2. Conecte seu repositório GitHub
3. Deploy automático!
4. Use a URL do Vercel no cliente

---

## 🚀 Usar a GUI com Localhost

1. **Compile a versão GUI:**
   ```powershell
   dotnet new console --force
   Remove-Item Program.cs
   Copy-Item client_windows_gui.cs Program.cs
   dotnet build -c Release
   ```

2. **Inicie o servidor PHP local** (em outro PowerShell):
   ```powershell
   php -S localhost:5000 -t public
   ```

3. **Execute o cliente GUI:**
   ```powershell
   .\bin\Release\net10.0\Cliente\ Windows.exe
   ```

4. **No cliente GUI:**
   - Selecione "Localhost" no dropdown
   - Clique em "INICIAR"

---

## 🧪 Testar Servidor Local

**PowerShell:**
```powershell
Invoke-WebRequest http://localhost:5000/health
```

**Resposta esperada:**
```json
{
  "status":"healthy",
  "timestamp":"...",
  "server":"iOS Activation Bypass API",
  "version":"1.0.0"
}
```

---

## ⚠️ Firewall

Se não conseguir conectar:
1. **Windows Defender Firewall** → Permitir PHP/Porta 5000
2. **Antivírus** → Adicione `public/` à whitelist

---

## 📊 Comparação

| Opção | Vantagem | Desvantagem |
|-------|----------|-----------|
| **PHP Local** | Simples, sem dependências | Precisa PHP instalado |
| **Docker** | Funciona em qualquer PC | Requer Docker |
| **Vercel** | Sem configuração | URL muda, depende internet |
| **Replit** | Gratuito, permanente | Sem controle total |

---

**Escolha a que preferir! 👍**
