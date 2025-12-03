# Servidor Standalone - Compilação

## Como Compilar Tudo em Um Pacote Único

O cliente Windows agora **inicia o servidor PHP automaticamente** quando você abre o `.exe`!

### Pré-requisitos

1. **PHP Portable** (sem instalação necessária)
   - Download: https://windows.php.net/download
   - Escolha a versão mais recente (ex: PHP 8.3 x64)
   - Extraia em: `Cliente Windows/php.exe`

2. **.NET SDK** já instalado (para compilação)

### Compilação

```powershell
cd "Cliente Windows"
dotnet clean
dotnet publish -c Release --self-contained
```

### Estrutura Final

```
dist/
├── ClienteWindows.exe          ← Execute este arquivo
├── php.exe                     ← PHP Portable
├── php.ini
├── ext/
│   └── php_sqlite3.dll
└── (outras DLLs do PHP)
```

### Uso

1. **Copie a pasta `Cliente Windows` com tudo:**
   ```
   Cliente Windows/
   ├── ClienteWindows.exe
   ├── php.exe
   ├── php.ini
   ├── ext/
   └── (...)
   ```

2. **Execute `ClienteWindows.exe`:**
   - ✅ Servidor PHP inicia automaticamente na porta 5000
   - ✅ Cliente Windows abre com interface
   - ✅ Selecione **"Remote Server"** (será localhost na verdade)
   - ✅ Clique em **"Detect iPhone"**

3. **Ao fechar o cliente:**
   - Servidor PHP encerra automaticamente

### Como Preparar Distribuição

```powershell
# 1. Publicar
cd "Cliente Windows"
dotnet publish -c Release --self-contained -o dist

# 2. Copiar PHP (já deve estar lá)
Copy-Item "php.exe" "dist/" -Force
Copy-Item "php-*.ini" "dist/" -Force
Copy-Item "ext/" "dist/ext/" -Recurse -Force

# 3. Copiar servidor PHP
Copy-Item "../public" "dist/public" -Recurse -Force
Copy-Item "../config" "dist/config" -Recurse -Force
Copy-Item "../assets" "dist/assets" -Recurse -Force

# 4. Criar ZIP
Compress-Archive -Path "dist" -DestinationPath "iOS_Bypass_Servidor.zip" -Force
```

### Resultado

Um arquivo `.zip` de ~50-100MB que contém:
- ✅ Cliente Windows compilado (.exe)
- ✅ Servidor PHP portável
- ✅ Todos os arquivos necessários
- ✅ **Funciona em qualquer Windows sem instalação**

### Alternativa: Script de Inicialização Único

Se quiser apenas um atalho visual:

```bat
@echo off
cd /d "%~dp0"
start ClienteWindows.exe
```

Salve como `INICIAR_TUDO.bat` e execute!

---

**Nota:** O servidor roda em background enquanto o cliente está aberto. Ao fechar o cliente, tudo se encerra automaticamente.
