# iOS Activation Bypass - Cliente Windows

## Instruções de Uso

### Requisitos
- Windows 10/11
- .NET Runtime 10.0 (incluído nos DLLs)
- iPhone com iOS 18.x ou inferior
- Cabo USB

### Como Usar

1. **Extrair os arquivos** desta pasta em um local seguro
2. **Duplo clique em `EXECUTAR.bat`**
3. A interface gráfica abrirá
4. Selecione o servidor:
   - **Replit**: Usar servidor remoto
   - **Localhost**: Usar servidor PHP local
   - **Custom URL**: URL personalizada do servidor

### Conectar iPhone

1. Conecte o iPhone via USB
2. Na interface, clique em **"Detect iPhone"**
3. Se detectado, clique em uma das opções:
   - **Hello Activation** - Ativa sem código
   - **Passcode Activation** - Ativa com código
   - **Process OFF** - Desativa processos

### Solução de Problemas

- **"iOS.exe não encontrado"**: Verifique se todos os arquivos foram extraídos
- **"iPhone não detecta"**: 
  - Feche iTunes/Finder
  - Libere confiança no iPhone
  - Tente outro cabo USB
- **"Servidor não responde"**: Verifique a URL do servidor

### Arquivos Necessários

```
ClienteWindows.exe         ← Executável principal
ClienteWindows.dll         ← Dependências
ClienteWindows.deps.json   ← Configuração
ClienteWindows.runtimeconfig.json
iOS.exe                    ← Detecção de dispositivo
EXECUTAR.bat               ← Inicia a aplicação
LEIA-ME.md                 ← Este arquivo
```

**Não delete nenhum arquivo!** Todos são necessários.

---

**Versão:** 1.0  
**Desenvolvido para:** MIT Estudo  
**Linguagem:** Português do Brasil
