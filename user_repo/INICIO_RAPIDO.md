# 🚀 Início Rápido - 5 Minutos!

**Guia super direto para colocar o servidor no ar e configurar o cliente.**

---

## ✅ O Que Você Precisa

1. ⚡ **Servidor rodando** (já está no Replit!)
2. 📱 **Cliente configurado** (Windows ou Mac)
3. 📦 **Assets MobileGestalt** (arquivos .plist por modelo de iPhone)

---

## 🎯 PASSO 1: Copiar a URL do Servidor (30 segundos)

### No Replit:

1. ✅ Clique no botão **"Run"** (se ainda não estiver rodando)
2. ✅ Aguarde aparecer o webview
3. ✅ **COPIE** a URL que aparece (algo como `https://seu-projeto.replit.app`)

**Teste se está funcionando:**
- Abra em outra aba: `https://sua-url.replit.app/health`
- Deve mostrar `"status": "healthy"`

✅ **Se funcionou, prossiga!**

---

## 🎯 PASSO 2: Configurar o Cliente (2 minutos)

### 🪟 Se você está no **WINDOWS**:

1. **Abra** o arquivo `client_windows.cs` em qualquer editor
2. **Procure** a linha (está no topo do arquivo):
   ```csharp
   private const string REMOTE_API = "https://albert.ip-info.me/files/get.php";
   ```
3. **MUDE PARA**:
   ```csharp
   private const string REMOTE_API = "https://SUA-URL-DO-REPLIT.app";
   ```
4. **Salve** o arquivo
5. **Recompile** (se necessário):
   ```bash
   csc client_windows.cs
   ```

**Pronto! Cliente configurado!** ✅

---

### 🍎 Se você está no **MAC**:

**ATENÇÃO**: O script Python **NÃO PRECISA** do servidor! Ele gera tudo localmente.

Simplesmente execute:
```bash
sudo python3 offline_bypass.py
```

**Só use o servidor remoto se realmente quiser centralizar a geração de payloads.**

---

## 🎯 PASSO 3: Adicionar Assets (Se Necessário)

### O servidor precisa dos arquivos MobileGestalt para cada modelo de iPhone.

**Onde colocar:**
```
assets/Maker/iPhone14-5/com.apple.MobileGestalt.plist
assets/Maker/iPhone13-2/com.apple.MobileGestalt.plist
assets/Maker/iPhone12-1/com.apple.MobileGestalt.plist
```

**Observação:** Use hífen `-` ao invés de vírgula `,` no nome da pasta.

**Se você não tem estes arquivos:**
- O servidor vai informar quais modelos estão disponíveis quando você fizer uma requisição
- Você precisa obter estes arquivos do pacote original do projeto

---

## 🎯 PASSO 4: Testar Tudo (1 minuto)

### Testar Manualmente

Cole no navegador (substitua os valores):

```
https://sua-url-replit.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=SERIAL123
```

**O que deve acontecer:**
- Se o modelo existe: Retorna uma URL de download
- Se o modelo NÃO existe: Retorna erro com lista de modelos disponíveis

---

## 🎯 PASSO 5: Executar o Cliente

### Windows:

1. Certifique-se que `iOS.exe` está na mesma pasta
2. Conecte o iPhone via USB
3. Execute:
   ```bash
   .\client_windows.exe
   ```

### Mac:

```bash
sudo python3 offline_bypass.py
```

---

## 📊 Verificar se Está Funcionando

Quando o cliente rodar, você deve ver:

```
=== Phase 3: Server Authorization ===
[*] Payload URL: https://sua-url.replit.app/cache/downloads_abc123.sqlitedb
                 ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
             ESTA É A PROVA QUE ESTÁ FUNCIONANDO!
```

Se você ver a URL do seu servidor ali, **FUNCIONOU!** 🎉

---

## ❌ Não Funcionou?

### Cliente não conecta ao servidor

**Sintomas:**
- `Server refused connection`
- `HTTP Error 404`
- `Connection timeout`

**Soluções:**
1. ✅ Certifique-se que o servidor Replit está **rodando** (clique em "Run")
2. ✅ Verifique se a URL está **correta** (sem `/` no final)
3. ✅ Teste a URL no navegador: `/health`
4. ✅ Verifique se compilou o cliente após mudar a URL

### Erro "Asset não encontrado"

**Significa:** Você não tem o arquivo MobileGestalt para aquele modelo de iPhone.

**Solução:** Adicione o arquivo na pasta `assets/Maker/ModeloDoIphone/`

### Cliente não detecta o iPhone

**Nada a ver com o servidor!** Isso é problema de USB/drivers.

**Soluções:**
- Windows: Instale iTunes ou drivers Apple
- Mac: Instale `brew install libimobiledevice`
- Confie no computador quando aparecer popup no iPhone

---

## 🎓 Próximos Passos

Agora que está funcionando:

- 📖 Leia o **README.md completo** para entender melhor
- 🚀 Veja os **guias de deployment** em `docs/deployment/`
- 🔧 Configure **variáveis de ambiente** conforme necessário
- 🔒 Adicione **HTTPS** se for usar em produção

---

## 🆘 Precisa de Ajuda Urgente?

**Checklist rápido:**

- [ ] Servidor Replit está rodando? (botão "Run")
- [ ] `/health` retorna `"status": "healthy"`?
- [ ] URL no cliente está correta? (sem `/` no final)
- [ ] Cliente foi recompilado após mudança?
- [ ] iPhone está conectado e confiando no PC?
- [ ] Assets MobileGestalt foram adicionados?

**Se todos estão ✅ e ainda não funciona:**
- Veja os **logs** do servidor em `logs/`
- Ative `DEBUG_MODE=true` temporariamente
- Leia a documentação completa nos guias específicos

---

**Dica:** Imprima esta página ou mantenha aberta enquanto configura! 📄
