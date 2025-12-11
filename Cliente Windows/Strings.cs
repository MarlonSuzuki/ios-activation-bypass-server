using System;
using System.Collections.Generic;

namespace ClienteWindows
{
    public class Strings
    {
        public enum Language { English, PortugueseBR }
        private static Language currentLanguage = Language.PortugueseBR;

        public static Language CurrentLanguage
        {
            get => currentLanguage;
            set => currentLanguage = value;
        }

        // ========== UI Labels & Buttons ==========
        public static string DeviceInformation => GetString("Device Information", "Informações do Dispositivo");
        public static string Connection => GetString("Connection", "Conexão");
        public static string Model => GetString("Model", "Modelo");
        public static string Serial => GetString("Serial", "Serial");
        public static string IMEI => GetString("IMEI", "IMEI");
        public static string Status => GetString("Status", "Status");
        public static string iOS => GetString("iOS", "iOS");
        public static string ProductTypeLabel => GetString("P/T Model", "P/T Modelo");
        public static string ECID => GetString("ECID", "ECID");
        public static string IMEI2 => GetString("IMEI2", "IMEI2");
        public static string Activated => GetString("Activated", "Ativado");
        public static string NotConnected => GetString("Not Connected", "Não Conectado");
        public static string NA => GetString("N/A", "N/D");

        // ========== Server Setup ==========
        public static string Server => GetString("Server", "Servidor");
        public static string RemoteServer => GetString("Remote Server", "Servidor Remoto");
        public static string Localhost => GetString("Localhost", "Localhost");
        public static string CustomURL => GetString("Custom URL", "URL Customizada");
        public static string CustomURLLabel => GetString("Custom URL", "URL Customizada");
        public static string RussianServer => GetString("Russian Server", "Servidor Russo");
        public static string LocalhostSetup => GetString("Localhost Setup", "Configuração Localhost");
        public static string LocalhostInfoText => GetString(
            "Execute INICIAR_SERVIDOR.bat to run the server locally after installing PHP.",
            "Execute INICIAR_SERVIDOR.bat para rodar o servidor localmente após instalar PHP.");
        public static string StartLocalServer => GetString("Start Local Server", "Iniciar Servidor Local");
        public static string RussianServerSelected => GetString(
            "Russian Server selected - codex-r1nderpest-a12.ru",
            "Servidor Russo selecionado - codex-r1nderpest-a12.ru");

        // ========== Language & Actions ==========
        public static string Language_Label => GetString("Language", "Idioma");
        public static string Actions => GetString("Actions", "Ações");
        public static string DetectDevice => GetString("Detect iPhone", "Detectar iPhone");
        public static string HelloActivation => GetString("Hello Activation", "Ativação Hello");
        public static string PasscodeActivation => GetString("Passcode Activation", "Ativação Passcode");
        public static string ManualBypass => GetString("Manual Bypass", "Bypass Manual");
        public static string ProcessOFF => GetString("Process OFF", "Processo OFF");
        public static string Toolbox => GetString("Toolbox", "Caixa de Ferramentas");
        public static string Exit => GetString("Exit", "Sair");

        // ========== Log Messages ==========
        public static string AppStarted => GetString("Application started...", "Aplicação iniciada...");
        public static string SystemInitialized => GetString("System initialized", "Sistema inicializado");
        public static string DetectingDevice => GetString("[INICIO] Detecting device...", "[INICIO] Detectando dispositivo...");
        public static string RemoteServerSelected => GetString("Remote Server selected.", "Servidor Remoto selecionado.");
        public static string LocalhostSelected => GetString("Localhost selected. Use INICIAR_SERVIDOR.bat to run the server.", 
            "Localhost selecionado. Use INICIAR_SERVIDOR.bat para rodar o servidor.");
        public static string CustomURLSelected => GetString("Custom URL selected.", "URL Customizada selecionada.");

        // ========== Console Output ==========
        public static string ConsoleOutput => GetString("Console Output", "Saída do Console");
        public static string Normal => GetString("Normal", "Normal");
        public static string Debug => GetString("Debug", "Debug");

        // ========== Manual Bypass Messages ==========
        public static string ManualBypassTitle => GetString("Manual Bypass Process", "Processo de Bypass Manual");
        public static string ManualBypassStep1 => GetString(
            "[MANUAL] Injecting payload to /Downloads/downloads.28.sqlitedb",
            "[MANUAL] Injetando payload para /Downloads/downloads.28.sqlitedb");
        public static string ManualBypassStep2 => GetString(
            "[MANUAL] Please perform the following steps manually:",
            "[MANUAL] Por favor, execute os seguintes passos manualmente:");
        public static string ManualBypassReboot => GetString(
            "  1. Reboot device (via Settings or hardware buttons)",
            "  1. Reinicie o dispositivo (via Configurações ou botões hardware)");
        public static string ManualBypassMetadata => GetString(
            "  2. Check if /iTunes_Control/iTunes/iTunesMetadata.plist appeared",
            "  2. Verifique se /iTunes_Control/iTunes/iTunesMetadata.plist apareceu");
        public static string ManualBypassCopy => GetString(
            "  3. Copy it to /Books/iTunesMetadata.plist (see commands in console)",
            "  3. Copie para /Books/iTunesMetadata.plist (veja comandos no console)");
        public static string ManualBypassFinal => GetString(
            "  4. Reboot again to trigger bookassetd stage",
            "  4. Reinicie novamente para ativar o estágio bookassetd");
        public static string PayloadInjected => GetString(
            "✅ Payload injected successfully at /Downloads/downloads.28.sqlitedb",
            "✅ Payload injetado com sucesso em /Downloads/downloads.28.sqlitedb");
        public static string ManualProcessComplete => GetString(
            "✅ Manual Bypass ready for user continuation",
            "✅ Bypass Manual pronto para continuação pelo usuário");

        // ========== Error Messages ==========
        public static string iOSExeNotFound => GetString(
            "[CRITICAL ERROR] iOS.exe NOT FOUND!",
            "[ERRO CRÍTICO] iOS.exe NÃO ENCONTRADO!");

        private static string GetString(string enUS, string ptBR)
        {
            return currentLanguage == Language.English ? enUS : ptBR;
        }
    }
}
