# Instalação Manual de PHP Puro no Windows

## Passo 1: Baixar PHP

1. Acesse: https://windows.php.net/download/
2. Procure pela versão mais recente (ex: PHP 8.3.14)
3. **Importante**: Baixe a versão **"Non Thread Safe (NTS) x64"**
   - Link direto (pode variar): https://windows.php.net/downloads/releases/php-8.3.14-nts-Win32-vs16-x64.zip

## Passo 2: Descompactar

1. Crie uma pasta: `C:\php`
2. Descompacte o arquivo `.zip` dentro de `C:\php`
3. Você deve ter arquivos como `php.exe` em `C:\php\php.exe`

## Passo 3: Adicionar ao PATH do Windows

**Opção A - PowerShell (Recomendado)**

Abra PowerShell como ADMINISTRADOR e execute:

```powershell
[Environment]::SetEnvironmentVariable("PATH", "$env:PATH;C:\php", [EnvironmentVariableTarget]::User)
```

Feche e abra o PowerShell novamente.

**Opção B - Manual (Se não funcionar acima)**

1. Clique no botão "Iniciar" e pesquise "Variáveis de ambiente"
2. Clique em "Editar as variáveis de ambiente do sistema"
3. Clique em "Variáveis de ambiente..." (botão no canto inferior direito)
4. Na seção "Variáveis do sistema", clique em "Path" e depois "Editar"
5. Clique em "Novo" e adicione: `C:\php`
6. Clique em "OK" várias vezes
7. Reinicie o computador

## Passo 4: Verificar Instalação

Abra um **novo** PowerShell (não ADMIN) e execute:

```powershell
php -v
```

Você deve ver algo como:
```
PHP 8.3.14 (cli) (built: Oct 2 2024 20:28:17) ( NTS Visual C++ 2022 x64 )
Copyright (c) The PHP Group
Zend Engine v4.3.14, Copyright (c) Zend Technologies
```

Se aparecer "php não é reconhecido", tente reiniciar o computador e repita o teste.

## Passo 5: Usar com o Projeto

Agora que PHP está instalado, basta executar:

```powershell
cd "C:\Users\Tributos Consultoria\Desktop\certificado\MITEstudo\Cliente Windows"
.\INICIAR_SERVIDOR.bat
```

O servidor iniciará em `http://localhost:8000`

## Solução de Problemas

### "php não é reconhecido"
- Reinicie o PowerShell/computador
- Verifique se `C:\php\php.exe` existe
- Verifique se o PATH foi adicionado corretamente (Passo 3 - Opção B)

### "Port already in use"
- Algo está usando a porta 8000
- Tente: `netstat -ano | findstr :8000` no PowerShell
- Ou mude a porta em `INICIAR_SERVIDOR.bat` (linha com `php -S`)

### Cliente Windows não conecta
- Certifique-se que o servidor está rodando
- Verifique se selecionou "Localhost" no cliente
- Teste acessando `http://localhost:8000` no navegador

## Pronto!

PHP instalado e pronto para usar com o cliente WPF! 🚀
