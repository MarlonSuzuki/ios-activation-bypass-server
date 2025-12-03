# 🪟 Cliente Windows - iOS Activation Bypass

**Tutorial passo a passo para compilar e executar o cliente Windows (C#)**

---

## 📋 Pré-requisitos

Você precisa ter instalado:
- **.NET 10 SDK** (ou superior)
  - Baixar em: https://dotnet.microsoft.com/en-us/download
  - Verificar: Abra PowerShell e digite `dotnet --version`

- **iOS.exe** (ferramenta go-ios para Windows)
  - Arquivo já deve estar nesta pasta

---

## 🚀 Como Compilar

### Passo 1: Abrir PowerShell

Clique com botão direito na pasta "Cliente Windows" > "Abrir Terminal"

Ou abra o PowerShell e navegue:
```powershell
cd "C:\Users\[Seu Usuário]\Desktop\certificado\MITEstudo\Cliente Windows"
```

### Passo 2: Compilar o Projeto

Execute no PowerShell:
```powershell
dotnet build -c Release
```

Espere aparecer: `✓ Construir êxito`

### Passo 3: Executar o Cliente

```powershell
cd bin\Release\net10.0
dotnet "Cliente Windows.dll"
```

---

## 💻 Como Usar

Após executar, você verá um menu:

```
╔════════════════════════════════════════╗
║   iOS Activation Bypass - Cliente      ║
╚════════════════════════════════════════╝

Selecione o servidor:
[1] Replit (Cloud)
[2] Localhost (Local)
[3] URL Customizada
[4] Sair

Escolha: 
```

### Opções:

| Opção | O que faz | Quando usar |
|-------|-----------|-------------|
| **[1] Replit** | Conecta ao servidor em nuvem | Quando o servidor está rodando no Replit |
| **[2] Localhost** | Conecta ao servidor local | Quando você está rodando o servidor PHP localmente |
| **[3] URL Customizada** | Permite digitar URL personalizada | Para testar com servidores customizados |
| **[4] Sair** | Encerra o programa | Para sair |

---

## 🔍 Solução de Problemas

### ❌ Erro: "iOS.exe não encontrado"
**Solução**: Certifique-se de que o arquivo `iOS.exe` está na mesma pasta que o executável compilado.

### ❌ Erro: "iPhone NÃO DETECTADO"
**Verifique:**
- ✓ O iPhone está conectado via USB?
- ✓ Você liberou a confiança no computador?
- ✓ O iTunes ou Finder está aberto?
- ✓ O driver USB está funcionando?

### ❌ Erro: "dotnet: command not found"
**Solução**: Instale o .NET 10 SDK de https://dotnet.microsoft.com/en-us/download

---

## 📝 Estrutura de Pastas

Após compilar, você terá:

```
Cliente Windows/
├── Program.cs                  (código fonte)
├── Cliente Windows.csproj      (projeto .NET)
├── iOS.exe                     (ferramenta de linha de comando)
├── LEIA-ME.md                  (este arquivo)
└── bin/
    └── Release/
        └── net10.0/
            ├── Cliente Windows.dll    (executável compilado)
            └── (outros arquivos de runtime)
```

---

---

## 🌐 Opção [2] - Localhost (Servidor Local)

### ⚙️ Pré-requisitos

Você precisa ter instalado no seu Windows:

1. **PHP 8.0+** com extensão SQLite3
   - Baixar: https://windows.php.net/download/
   - Ou usar: XAMPP (https://www.apachefriends.org/)

2. **Git** (opcional, para clonar)
   - Baixar: https://git-scm.com/download/win

### 📋 Passo 1: Preparar a Pasta do Servidor

**Opção A: Usar a pasta do MITEstudo**

Se você já tem o arquivo `MITEstudo.zip` extraído:

```
C:\Users\[Seu Usuário]\Desktop\certificado\MITEstudo\
├── public/
├── config/
├── assets/
├── docs/
└── Cliente Windows/
```

Perfeito! O servidor já está pronto.

**Opção B: Extrair só o servidor**

Se quer colocar o servidor em outro lugar:

```powershell
# Extrair MITEstudo.zip
# Copiar tudo EXCETO a pasta "Cliente Windows" para onde quiser

# Exemplo:
# C:\xampp\htdocs\MITEstudo\
```

### 📋 Passo 2: Iniciar o Servidor PHP Localmente

#### **Opção A: Usando XAMPP (Mais Fácil)**

1. Abra o **XAMPP Control Panel**
2. Clique em **"Start"** ao lado de **Apache**
3. Clique em **"Start"** ao lado de **MySQL** (se quiser)
4. Navegue para: `http://localhost/MITEstudo/public/`
5. Se vir `{"status":"healthy"}`, está funcionando! ✓

#### **Opção B: Usando PHP Diretamente (Sem XAMPP)**

Abra o PowerShell na pasta raiz do MITEstudo:

```powershell
cd "C:\Users\Tributos Consultoria\Desktop\certificado\MITEstudo"
php -S localhost:5000 -t public
```

Você verá:
```
PHP 8.x.x Development Server
Listening on http://localhost:5000
```

**NÃO FECHE ESTE TERMINAL!** O servidor precisa ficar rodando.

### 📋 Passo 3: Testar o Servidor

Abra o navegador e vá para:

```
http://localhost:5000/health
```

Você deve ver:
```json
{"status":"healthy"}
```

Se vir isso, o servidor está 100% funcionando! ✓

### 📋 Passo 4: Usar o Cliente Windows com Localhost

1. Abra **outro PowerShell** (deixe o servidor rodando no primeiro)

2. Navegue para a pasta Cliente Windows:
```powershell
cd "C:\Users\Tributos Consultoria\Desktop\certificado\MITEstudo\Cliente Windows\bin\Release\net10.0"
dotnet "Cliente Windows.dll"
```

3. Selecione a opção **[2] Localhost**:
```
Escolha: 2
```

4. Você verá:
```
Servidor: http://localhost:5000
Conecte o iPhone e pressione ENTER...
```

5. **Conecte o iPhone via USB** e pressione ENTER

6. O cliente vai automaticamente:
   - Detectar o iPhone
   - Fazer o bypass
   - Tudo direto do seu computador! 🎉

### 🔍 Troubleshooting - Localhost

| Problema | Solução |
|----------|---------|
| "Connection refused" | O servidor PHP não está rodando. Execute `php -S localhost:5000 -t public` |
| "404 Not Found" | Verifique o caminho do servidor. Deve ser a pasta `public/` |
| "iPhone não detectado" | Verifique se o iPhone está conectado e confiável |
| Porta 5000 já está em uso | Use outra porta: `php -S localhost:8000 -t public` |

### 📝 Estrutura de Pastas (Localhost)

```
MITEstudo/
├── public/
│   └── index.php           ← API do servidor
├── config/
│   ├── PayloadGenerator.php
│   └── templates/
├── assets/
│   └── Maker/
├── cache/                  ← Payloads gerados
├── logs/                   ← Logs do servidor
└── Cliente Windows/        ← Cliente C#
    └── bin/Release/net10.0/
        └── Cliente Windows.dll
```

---

## ✅ Resumo: Localhost vs Replit

| Aspecto | Localhost | Replit |
|---------|-----------|--------|
| **Setup** | Manual (PHP ou XAMPP) | Automático |
| **Velocidade** | Mais rápido (local) | Normal (nuvem) |
| **Disponibilidade** | Só quando seu PC está ligado | 24/7 |
| **Ideal para** | Testes e desenvolvimento | Uso contínuo |

---

## 🎯 Próximas Etapas

1. **Escolha**: Localhost ou Replit
2. **Configure**: Siga o guia acima
3. **Conecte**: O iPhone via USB
4. **Execute**: O cliente Windows
5. **Deixe rodar**: O sistema faz tudo automaticamente!

---

## 📞 Suporte

Se tiver problemas, verifique:
- [Localhost Setup Completo](../docs/LOCALHOST_SETUP.md)
- [Troubleshooting Completo](../docs/TROUBLESHOOTING.md)
- [Cliente Windows Detalhado](../docs/client-setup/CLIENTE_WINDOWS.md)
- [API Reference](../docs/API_REFERENCE.md)

**Tudo pronto! 🚀**
