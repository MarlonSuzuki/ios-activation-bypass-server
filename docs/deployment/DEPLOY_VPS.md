# 🚀 Guia de Deploy em VPS (Virtual Private Server)

Este guia explica como hospedar o Servidor de Ativação iOS em uma VPS (como DigitalOcean, AWS EC2, Linode, Vultr, ou Oracle Cloud) usando **Docker**. Esta é a maneira mais segura e fácil de garantir que todas as dependências funcionem corretamente.

## Pré-requisitos

1.  Uma VPS com Linux (Ubuntu 20.04/22.04 recomendado).
2.  Acesso SSH à sua VPS.
3.  Domínio configurado apontando para o IP da VPS (opcional, mas recomendado).

---

## Passo 1: Instalar o Docker

Conecte-se à sua VPS via SSH e execute os seguintes comandos para instalar o Docker:

```bash
# Atualizar lista de pacotes
sudo apt update
sudo apt upgrade -y

# Instalar dependências
sudo apt install -y apt-transport-https ca-certificates curl software-properties-common

# Adicionar chave GPG do Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Adicionar repositório do Docker
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Instalar Docker Engine
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io

# Verificar instalação
sudo docker --version
```

---

## Passo 2: Baixar o Projeto

Clone o repositório na sua VPS:

```bash
# Instalar git se não tiver
sudo apt install -y git

# Clonar o repositório
git clone https://github.com/SEU_USUARIO/SEU_REPOSITORIO.git ios-bypass-server

# Entrar na pasta
cd ios-bypass-server
```

*(Substitua a URL pelo seu repositório ou faça upload dos arquivos via SFTP)*

---

## Passo 3: Configurar e Rodar com Docker

O projeto já inclui um `Dockerfile`. Vamos construir e rodar o container.

### 1. Construir a Imagem

```bash
sudo docker build -t ios-bypass .
```

### 2. Rodar o Container

Vamos rodar o servidor na porta **80** (HTTP padrão).

```bash
# Rodar em segundo plano (detached mode) mapeando a porta 80 da VPS para a porta 5000 do container
sudo docker run -d \
  --name bypass-server \
  --restart unless-stopped \
  -p 80:5000 \
  ios-bypass
```

**Verifique se está rodando:**
```bash
sudo docker ps
```

Agora você pode acessar `http://SEU_IP_DA_VPS` no navegador. Se vir o JSON de status, está funcionando!

---

## Passo 4: Configurar HTTPS (Opcional, mas Recomendado)

Para HTTPS gratuito, recomendamos usar o **Caddy** ou **Nginx** como proxy reverso.

### Opção Fácil: Usando Caddy (Automático)

1.  Pare o container atual: `sudo docker stop bypass-server && sudo docker rm bypass-server`
2.  Rode o container na porta interna (ex: 5000):
    ```bash
    sudo docker run -d --name bypass-server --restart unless-stopped -p 5000:5000 ios-bypass
    ```
3.  Instale o Caddy na VPS e configure o proxy reverso para `localhost:5000`.

---

## Solução de Problemas

**Erro: "Port already in use"**
Verifique se o Apache ou Nginx já não estão rodando na porta 80.
```bash
sudo systemctl stop apache2
sudo systemctl disable apache2
```

**Ver Logs do Servidor**
```bash
sudo docker logs -f bypass-server
```

**Atualizar o Servidor**
Se você mudou o código:
```bash
git pull
sudo docker build -t ios-bypass .
sudo docker stop bypass-server
sudo docker rm bypass-server
sudo docker run -d --name bypass-server --restart unless-stopped -p 80:5000 ios-bypass
```
