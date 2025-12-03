# Ícone + Instalador

## 1️⃣ ADICIONAR ÍCONE (Rápido)

### Passo 1: Gerar/Colocar ícone
- Arquivo: `app.ico`
- Local: `Cliente Windows/app.ico`

### Passo 2: Editar `.csproj`
Abra `Cliente Windows/Cliente Windows.csproj` e adicione após a última linha:

```xml
  <ItemGroup>
    <ApplicationIcon Include="app.ico" />
  </ItemGroup>
```

### Passo 3: Compilar
```powershell
dotnet publish -c Release --self-contained
```

Pronto! O .exe terá ícone!

---

## 2️⃣ CRIAR INSTALADOR (Escolha uma opção)

### Opção A: NSIS (Recomendado - Simples)

**Pré-requisito:** Instalar NSIS
- Download: https://nsis.sourceforge.io/Download
- Próximo, próximo, instalar

**Passo 1:** Colocar ícone em `dist/app.ico`

**Passo 2:** Usar script fornecido
- Arquivo: `CRIAR_INSTALADOR_NSIS.nsi` (já criado)

**Passo 3:** Compilar o instalador
```powershell
# Abra o NSIS e abra o arquivo .nsi
# Ou via linha de comando:
"C:\Program Files (x86)\NSIS\makensis.exe" "CRIAR_INSTALADOR_NSIS.nsi"
```

**Resultado:** `iOS_Activation_Bypass_Setup.exe` (~100MB)

---

### Opção B: Windows Package Manager (WiX Toolset)

Mais profissional mas mais complexo:

1. Instalar: https://github.com/wixtoolset/wix3/releases
2. Criar arquivo `.wxs` 
3. Compilar: `candle` + `light`

---

## 3️⃣ DISTRIBUIR

**Com NSIS:**
```
iOS_Activation_Bypass_Setup.exe (~100MB)
↓ (duplo clique)
↓ Instalador abre
↓ Escolhe pasta
↓ Instala
↓ Cria atalho no Menu Iniciar + Desktop
↓ Pronto!
```

**Resultado final:**
- ✅ Ícone no .exe
- ✅ Instalador profissional
- ✅ Atalhos automáticos
- ✅ Desinstalador próprio

---

## Resumo Rápido

1. **Ícone**: Adicionar arquivo `.ico` + editar `.csproj` + recompilar
2. **Instalador**: Instalar NSIS + compilar script `.nsi`
3. **Distribuir**: Compartilhar `Setup.exe`

Ambos são simples e profissionais! 🚀
