# 🔧 Versões do Cliente Windows

Este diretório contém **DUAS VERSÕES** do cliente Windows para iOS Activation Bypass:

---

## 📦 **MainWindow.xaml.cs** (VERSÃO FINAL - DISTRIBUIÇÃO)
**Para usuários finais**

- ✅ Funcionalidade completa de bypass (9 fases)
- ✅ Ativação Hello, Passcode, Process OFF
- ✅ Modo manual de injeção
- ✅ Dual-server (Replit + Railway)
- ❌ **SEM** botão "Extract Bypass" (segurança)

**Use esta versão para distribuir aos usuários.**

---

## 🔐 **MainWindow.xaml.cs.DEV** (VERSÃO DESENVOLVIMENTO - APENAS VOCÊ)
**Para engenharia reversa e testes**

- ✅ Tudo da versão final
- ✅ **MAIS** botão roxo "Extract Bypass"
- ✅ Extrai arquivos injetados do iPhone para `C:\Users\[você]\iPhone_Extracted_Bypass\`

**Use esta versão APENAS para testes e análise técnica.**

---

## 🔄 Como Usar

### Para Compilar a Versão Final (Distribuição):
```bash
# Compilar MainWindow.xaml.cs normalmente no Visual Studio
# Resultado: Exe SEM "Extract Bypass"
```

### Para Usar a Versão DEV (Seu PC):
```bash
# 1. Copiar MainWindow.xaml.cs.DEV → MainWindow.xaml.cs
cp MainWindow.xaml.cs.DEV MainWindow.xaml.cs

# 2. Compilar no Visual Studio
# Resultado: Exe COM "Extract Bypass" roxo

# 3. Use para testes!

# 4. Não faça push dessa versão - deixe a versão final
cp MainWindow.xaml.cs.bak MainWindow.xaml.cs
```

---

## ⚠️ IMPORTANTE

- **MainWindow.xaml.cs** = Distribuição (GitHub)
- **MainWindow.xaml.cs.DEV** = Seu backup privado
- Nunca faça push da versão DEV
- Ambas funcionam 100% idênticas (exceto Extract Bypass)

---

## 📝 Histórico

- **Nov 30, 2025**: Separação de versões para segurança
  - DEV: Com "Extract Bypass" para testes
  - Final: Sem "Extract Bypass" para usuários

---

**Autorizado por você! ✅**
