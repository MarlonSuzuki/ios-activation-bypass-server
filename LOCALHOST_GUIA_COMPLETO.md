# 🌐 Guia Completo - Localhost com Cliente Windows

**Tutorial detalhado para rodar o servidor PHP localmente e usar o cliente Windows**

---

## 📋 Índice

- [O que é Localhost?](#o-que-é-localhost)
- [Pré-requisitos](#pré-requisitos)
- [Instalação](#instalação)
- [Como Iniciar](#como-iniciar)
- [Usar com Cliente Windows](#usar-com-cliente-windows)
- [Troubleshooting](#troubleshooting)

---

## 🤔 O que é Localhost?

**Localhost** = Seu próprio computador funcionando como servidor

- Nenhum internet necessário
- Mais rápido que Replit (dados locais)
- Só funciona quando seu PC está ligado
- Ideal para testes e desenvolvimento

---

## 📋 Pré-requisitos

### Opção 1: XAMPP (Recomendado - Mais Fácil)

**O que é?** Um pacote que instala PHP + Apache + MySQL

**Como instalar:**

1. Vá para: https://www.apachefriends.org/
2. Clique em **"Download XAMPP"** (versão Windows)
3. Execute o instalador
4. Aceite as instruções
5. Pronto! ✓

**Verificar instalação:**

Abra o PowerShell:
```powershell
xampp-control.exe
```

Você verá uma janela com botões de controle.

### Opção 2: PHP Puro (Avançado)

Se você quer instalar só PHP sem XAMPP:

1. Vá para: https://windows.php.net/download/
2. Baixe a versão **Non-Thread Safe (NTS)**
3. Extraia em: `C:\php\` (ou onde quiser)
4. Adicione ao PATH do Windows (busque "environment variables")

**Verificar instalação:**

```powershell
php --version
```

Você deve ver a versão do PHP.

---

## 🚀 Instalação

### Passo 1: Baixe o MITEstudo.zip

Já deve estar no seu Desktop em:
```
C:\Users\[Seu Usuário]\Desktop\certificado\MITEstudo\
```

### Passo 2: Verifique a Estrutura

```
MITEstudo/
├── public/index.php          ← Servidor API
├── config/
│   ├── PayloadGenerator.php
│   ├── Logger.php
│   └── templates/
├── assets/Maker/             ← Configurações de dispositivos
├── cache/                    ← Payloads gerados aqui
├── logs/                     ← Logs do servidor
├── .htaccess
└── ...
```

### Passo 3: Teste de Saúde (Health Check)

Abra o navegador e vá para:

```
http://localhost:5000/health
```

Você deve ver:
```json
{"status":"healthy"}
```

Se não funcionar, siga os passos abaixo.

---

## 🔧 Como Iniciar o Servidor

### Método A: XAMPP (Recomendado)

1. Abra o **XAMPP Control Panel**:
   ```powershell
   "C:\xampp\xampp-control.exe"
   ```

2. Clique em **"Start"** ao lado de **Apache**

3. Pronto! O servidor está em: `http://localhost/`

4. Para o MITEstudo especificamente:
   - Copie a pasta MITEstudo para: `C:\xampp\htdocs\`
   - Acesse: `http://localhost/MITEstudo/public/`

### Método B: PHP Diretamente

Abra o PowerShell na pasta raiz do MITEstudo:

```powershell
cd "C:\Users\Tributos Consultoria\Desktop\certificado\MITEstudo"
php -S localhost:5000 -t public
```

Você verá:
```
PHP 8.2.x Development Server started at Mon Nov 21 17:00:00 2025
Listening on http://localhost:5000
Press Ctrl-C to quit
```

**IMPORTANTE**: Não feche este terminal! Ele precisa ficar rodando enquanto você usa o cliente.

---

## 🪟 Usar com Cliente Windows

### Passo 1: Verifique o Servidor

Navegador - teste se funciona:
```
http://localhost:5000/health
```

Deve retornar: `{"status":"healthy"}`

### Passo 2: Abra Outro PowerShell

**Deixe o servidor rodando no primeiro PowerShell!**

Abra um **segundo PowerShell** para o cliente:

```powershell
cd "C:\Users\Tributos Consultoria\Desktop\certificado\MITEstudo\Cliente Windows\bin\Release\net10.0"
dotnet "Cliente Windows.dll"
```

### Passo 3: Selecione Localhost

Menu do cliente:
```
╔════════════════════════════════════════╗
║   iOS Activation Bypass - Cliente      ║
╚════════════════════════════════════════╝

Selecione o servidor:
[1] Replit (Cloud)
[2] Localhost (Local)
[3] URL Customizada
[4] Sair

Escolha: 2
```

Digite **2** e pressione ENTER.

### Passo 4: Conecte o iPhone

```
Servidor: http://localhost:5000
Conecte o iPhone e pressione ENTER...
```

1. **Conecte o iPhone via USB**
2. **Abra o iTunes ou Finder**
3. **Autorize o computador no iPhone** (se pedido)
4. **Pressione ENTER** no cliente

O client vai automaticamente:
- ✓ Detectar o iPhone
- ✓ Extrair dados do sistema
- ✓ Gerar o payload
- ✓ Injetar no dispositivo
- ✓ Rebootar e ativar

**Pronto! 🎉**

---

## 🔍 Troubleshooting

### ❌ "Connection refused"

**Problema**: O servidor PHP não está rodando

**Solução**:
```powershell
cd "C:\Users\Tributos Consultoria\Desktop\certificado\MITEstudo"
php -S localhost:5000 -t public
```

Aguarde ver:
```
Listening on http://localhost:5000
```

### ❌ "Address already in use"

**Problema**: A porta 5000 já está sendo usada

**Solução**: Use outra porta
```powershell
php -S localhost:8000 -t public
```

Depois no cliente, escolha **[3] URL Customizada** e digite:
```
http://localhost:8000
```

### ❌ "404 Not Found"

**Problema**: O caminho está errado

**Solução**: Verifique que você está usando a flag `-t public`:
```powershell
php -S localhost:5000 -t public
```

A pasta `public` contém o `index.php` que é o servidor.

### ❌ "iPhone não detectado"

**Problema**: O cliente não consegue ver o iPhone

**Solução**:
- ✓ iPhone conectado via USB?
- ✓ iTunes ou Finder aberto?
- ✓ Permitiu a confiança no computador?
- ✓ iOS.exe está na pasta Client Windows?

### ❌ Erro de permissões na pasta cache

**Problema**: Sem permissão para escrever em `cache/`

**Solução**: Dê permissão para escrever
```powershell
# No PowerShell como administrador:
icacls "C:\Users\Tributos Consultoria\Desktop\certificado\MITEstudo\cache" /grant "%username%":(OI)(CI)F /T
```

---

## 📊 Comparação: Localhost vs Replit

| Feature | Localhost | Replit |
|---------|-----------|--------|
| **Setup** | Manual (PHP/XAMPP) | Automático |
| **Velocidade** | Muito rápida (local) | Normal (nuvem) |
| **Internet necessário** | Não | Sim |
| **Disponibilidade** | Só quando PC está ligado | 24/7 |
| **Segurança** | Só sua rede | Replit gerencia |
| **Ideal para** | Desenvolvimento/Testes | Produção/Uso contínuo |

---

## 🎯 Checklist Final

- ✅ PHP instalado ou XAMPP rodando
- ✅ Servidor rodando em `localhost:5000`
- ✅ Health check mostra `{"status":"healthy"}`
- ✅ Cliente Windows compilado
- ✅ iPhone conectado via USB
- ✅ iTunes/Finder aberto
- ✅ Duas janelas PowerShell abertas (servidor + cliente)

---

## 💡 Dicas

1. **Deixe o servidor rodando em background**: Use XAMPP em vez de PowerShell
2. **Teste sem iPhone**: Selecione Localhost e deixe passar os 30 segundos (vai dizer "iPhone não detectado")
3. **Veja os logs**: Abra a pasta `logs/` do MITEstudo para ver o que aconteceu
4. **Cache de payloads**: Cada payload gerado fica em `cache/` para referência

---

## ❓ Perguntas Frequentes

**P: Preciso deixar o PowerShell aberto o tempo todo?**

R: Sim, enquanto você quer usar o servidor local. Se usar XAMPP, ele roda em background.

**P: Posso usar Android em vez de iPhone?**

R: Não, este sistema é específico para iOS/iPhone.

**P: Funciona sem internet?**

R: Sim! Localhost não precisa de internet. Você pode desconectar a rede.

**P: Posso usar isso em outro computador?**

R: Não com localhost. Use Replit para isso (acesso remoto). Para rede local, adicione o IP do seu PC.

---

## 📞 Suporte

Se ainda tiver problemas:

1. Verifique [TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md)
2. Verifique os logs em `MITEstudo/logs/`
3. Releia este guia (às vezes a resposta está aqui!)

---

**Tudo configurado? Conecte o iPhone e divirta-se! 🚀**
