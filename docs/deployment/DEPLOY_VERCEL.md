# 🚀 Deploy no Vercel

Guia passo a passo para fazer deploy do servidor de ativação iOS no Vercel.

---

## 📋 Pré-requisitos

- Conta no Vercel (gratuita): https://vercel.com
- Conta no GitHub (para conectar o projeto)
- Este projeto baixado ou clonado

---

## ⚡ Deploy Rápido (3 minutos)

### Passo 1: Preparar o Projeto

1. **Crie** um repositório no GitHub com o código deste projeto
2. **Adicione** um arquivo `vercel.json` na raiz do projeto:

```json
{
  "version": 2,
  "builds": [
    {
      "src": "public/index.php",
      "use": "@vercel/php"
    }
  ],
  "routes": [
    {
      "src": "/cache/(.*)",
      "dest": "/cache/$1"
    },
    {
      "src": "/(.*)",
      "dest": "/public/index.php"
    }
  ],
  "env": {
    "DEBUG_MODE": "false",
    "TIMEZONE": "America/Sao_Paulo"
  }
}
```

### Passo 2: Deploy no Vercel

1. Acesse https://vercel.com/new
2. **Importe** seu repositório GitHub
3. **Configure**:
   - Framework Preset: **Other**
   - Build Command: (deixe vazio)
   - Output Directory: `public`
4. Clique em **"Deploy"**

### Passo 3: Configurar Variáveis de Ambiente

Após o deploy:

1. Vá em **Settings** → **Environment Variables**
2. Adicione:
   - `BASE_URL`: `https://seu-projeto.vercel.app`
   - `DEBUG_MODE`: `false`

3. **Redeploy** o projeto

---

## 🔧 Configuração Avançada

### Estrutura de Arquivos para Vercel

```
.
├── public/              # Código PHP
│   └── index.php
├── config/              # Classes PHP
├── assets/              # MobileGestalt files
├── vercel.json          # Configuração Vercel
└── .vercelignore        # Arquivos ignorados
```

### Arquivo `.vercelignore`

Crie um arquivo `.vercelignore` para otimizar o deploy:

```
logs/
cache/
.git/
.cache/
*.log
```

---

## ⚠️ Limitações do Vercel

### Cache e Arquivos Temporários

⚠️ **IMPORTANTE**: O Vercel é **serverless**, então:
- Arquivos em `/cache` são **temporários**
- Cada requisição pode rodar em um servidor diferente
- Logs não persistem entre execuções

### Solução: Usar Vercel Blob Storage

Para persistir os arquivos gerados, você pode usar o Vercel Blob:

1. Instale o SDK:
```bash
composer require vercel/blob
```

2. Modifique `PayloadGenerator.php`:
```php
use Vercel\Blob\Storage;

// Ao invés de salvar em /cache:
$storage = new Storage(getenv('BLOB_READ_WRITE_TOKEN'));
$url = $storage->put('downloads_' . $token . '.sqlitedb', $fileContent);
```

3. Configure a variável de ambiente `BLOB_READ_WRITE_TOKEN` no Vercel

---

## 🧪 Testar o Deploy

```bash
# Testar health check
curl https://seu-projeto.vercel.app/health

# Testar geração de payload
curl "https://seu-projeto.vercel.app/?prd=iPhone14,5&guid=12345678-1234-1234-1234-123456789ABC&sn=TEST123"
```

---

## 🌐 Domínio Personalizado

1. Vá em **Settings** → **Domains**
2. Adicione seu domínio (ex: `api-ios.seudominio.com`)
3. Configure o DNS conforme instruções do Vercel
4. Atualize a variável `BASE_URL` para o novo domínio

---

## 📊 Monitoramento

O Vercel oferece:
- ✅ Logs em tempo real
- ✅ Analytics de requisições
- ✅ Métricas de performance

Acesse em: https://vercel.com/seu-usuario/seu-projeto/analytics

---

## 💰 Custos

- **Hobby (Grátis)**: 
  - 100GB de largura de banda/mês
  - Ilimitadas requisições
  - Suficiente para uso pessoal

- **Pro ($20/mês)**:
  - 1TB de largura de banda
  - Analytics avançado
  - Mais recursos serverless

---

## 🔄 Atualizações

Para atualizar o código:

1. **Faça push** para o GitHub:
```bash
git add .
git commit -m "Atualização"
git push
```

2. O Vercel **faz deploy automático**!

---

## 🆘 Troubleshooting

### Erro 500 no Vercel

1. Verifique os **logs** no dashboard do Vercel
2. Ative `DEBUG_MODE=true` temporariamente
3. Verifique se as extensões PHP necessárias estão disponíveis

### Assets MobileGestalt não encontrados

Certifique-se que a pasta `assets/Maker/` está no repositório:

```bash
git add assets/Maker/
git commit -m "Add assets"
git push
```

### Cache não funciona

Isso é esperado no Vercel. Use Vercel Blob Storage ou aceite que os arquivos são temporários.

---

## ✅ Checklist Final

- [ ] Repositório no GitHub criado
- [ ] `vercel.json` adicionado
- [ ] Deploy realizado no Vercel
- [ ] Variáveis de ambiente configuradas
- [ ] Endpoint `/health` respondendo
- [ ] Assets MobileGestalt no repositório
- [ ] URL do servidor atualizada no cliente

---

**Deploy concluído! Seu servidor está no ar com Vercel! 🎉**
