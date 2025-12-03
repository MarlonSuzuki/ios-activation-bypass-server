# Como Distribuir - Guia Completo

## Compilação Completa (Recomendado)

### Passo 1: Preparar PHP Portable

```powershell
# Download PHP (escolha a versão mais recente)
# https://windows.php.net/download

# Extraia em:
# Cliente Windows/php.exe

# A estrutura deve ser:
# Cliente Windows/
# ├── php.exe
# ├── php.ini
# ├── ext/
# │   ├── php_sqlite3.dll
# │   └── (outras DLLs)
# └── (outros arquivos)
```

### Passo 2: Compilar Tudo

**Opção 1: Script Automático (Recomendado)**
```powershell
# Execute no PowerShell (como administrador)
.\COMPILAR_TUDO.ps1
```

**Opção 2: Manual**
```powershell
# 1. Compilar cliente
cd "Cliente Windows"
dotnet publish -c Release --self-contained -o ..\dist

# 2. Copiar servidor
Copy-Item "..\public" "..\dist\public" -Recurse -Force
Copy-Item "..\config" "..\dist\config" -Recurse -Force
Copy-Item "..\assets" "..\dist\assets" -Recurse -Force

# 3. Copiar PHP
Copy-Item "php.exe" "..\dist\" -Force
Copy-Item "php*.ini" "..\dist\" -Force
Copy-Item "ext" "..\dist\ext" -Recurse -Force
```

### Passo 3: Verificar Estrutura

A pasta `dist/` deve ter:

```
dist/
├── ClienteWindows.exe          ← Execute este
├── php.exe                     ← PHP Portable
├── php.ini
├── ext/
│   └── php_sqlite3.dll
├── config/                     ← Servidor
├── public/
└── assets/
```

### Passo 4: Criar Pacote ZIP

```powershell
# Na pasta raiz do projeto
Compress-Archive -Path "dist" -DestinationPath "iOS_Bypass_Standalone.zip" -Force
```

## Resultado Final

📦 **iOS_Bypass_Standalone.zip** (~80-100MB)

**Conteúdo:**
- ✅ Cliente Windows compilado (.exe)
- ✅ Servidor PHP portável
- ✅ Todos os arquivos necessários
- ✅ Pronto para usar

## Como o Usuário Final Usa

1. **Baixa o ZIP**
2. **Extrai em qualquer pasta**
3. **Duplo clique em: `ClienteWindows.exe`**
4. **Pronto!** Servidor + Cliente funcionando

## Checklist de Distribuição

- [ ] PHP Portable extraído em `Cliente Windows/`
- [ ] Script `COMPILAR_TUDO.ps1` executado com sucesso
- [ ] Pasta `dist/` criada com todos os arquivos
- [ ] ZIP gerado com tamanho ~80-100MB
- [ ] Testou em outro PC (opcional mas recomendado)
- [ ] Compartilhou o ZIP

## Notas Importantes

**Tamanho Final:**
- Cliente Windows compilado: ~30MB
- PHP Portable: ~50-70MB
- Arquivos do servidor: ~5MB
- **Total: ~80-120MB**

**Compatibilidade:**
- Windows 7, 8, 10, 11 (64-bit)
- Sem dependências externas
- Sem instalação necessária

**Segurança:**
- O cliente só acessa o servidor local (offline-first)
- A opção "Remote Server" (Replit) é opcional
- Nada é enviado sem consentimento

---

**Conclusão:** Um único arquivo ZIP que contém tudo necessário para funcionar completamente independente!
