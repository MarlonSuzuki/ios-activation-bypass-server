using System;
using System.Diagnostics;
using System.IO;
using System.Net.Http;
using System.Text.RegularExpressions;
using System.Threading;
using System.Threading.Tasks;

class Program
{
    static async Task Main()
    {
        while (true)
        {
            Console.Clear();
            Console.WriteLine("╔════════════════════════════════════════╗");
            Console.WriteLine("║   iOS Activation Bypass - Cliente      ║");
            Console.WriteLine("╚════════════════════════════════════════╝\n");
            Console.WriteLine("Selecione o servidor:");
            Console.WriteLine("[1] Replit (Cloud)");
            Console.WriteLine("[2] Localhost (Local)");
            Console.WriteLine("[3] URL Customizada");
            Console.WriteLine("[4] Sair\n");
            Console.Write("Escolha: ");

            string choice = Console.ReadLine();
            if (choice == "4") return;

            string api = choice switch
            {
                "1" => "https://64aebe5a-bacf-4267-901a-e999548dfc6e-00-1n1v4ownae3r2.worf.replit.dev",
                "2" => "http://localhost:5000",
                "3" => (Console.Write("\nDigite a URL: "), Console.ReadLine()),
                _ => null
            };

            if (api == null)
            {
                Console.WriteLine("\n[-] Opção inválida!");
                await Task.Delay(2000);
                continue;
            }

            await RunBypass(api);
        }
    }

    static async Task RunBypass(string api)
    {
        try
        {
            if (!File.Exists("iOS.exe"))
            {
                Console.WriteLine("\n[-] ERRO: iOS.exe não encontrado na pasta!");
                Console.WriteLine("[*] Pressione ENTER para voltar...");
                Console.ReadLine();
                return;
            }

            Console.WriteLine($"\n✓ Servidor: {api}");
            Console.WriteLine("[*] Procurando dispositivo iOS (30 segundos)...\n");

            string device = await DetectDevice();

            if (device == null)
            {
                Console.ForegroundColor = ConsoleColor.Red;
                Console.WriteLine("\n[-] ⚠️  iPhone NÃO DETECTADO!");
                Console.ResetColor();
                Console.WriteLine("\nVerifique:");
                Console.WriteLine("  • O iPhone está conectado via USB?");
                Console.WriteLine("  • O iTunes/Finder está aberto?");
                Console.WriteLine("  • Você permitiu a confiança no computador?");
                Console.WriteLine("\n[*] Pressione ENTER para tentar novamente...");
                Console.ReadLine();
                return;
            }

            Console.ForegroundColor = ConsoleColor.Green;
            Console.WriteLine($"\n[✓] iPhone detectado: {device}");
            Console.ResetColor();

            Console.WriteLine("\n[!] Pressione ENTER para iniciar o bypass...");
            Console.ReadLine();

            Console.WriteLine("\n▶ Iniciando sequência de bypass...");
            await Task.Delay(2000);
            Console.WriteLine("[✓] Sistema pronto!");

            Console.WriteLine("\n[*] Pressione ENTER para voltar...");
            Console.ReadLine();
        }
        catch (Exception ex)
        {
            Console.ForegroundColor = ConsoleColor.Red;
            Console.WriteLine($"\n[-] ERRO: {ex.Message}");
            Console.ResetColor();
            Console.WriteLine("\n[*] Pressione ENTER para voltar...");
            Console.ReadLine();
        }
    }

    static async Task<string> DetectDevice()
    {
        var end = DateTime.Now.AddSeconds(30);
        
        while (DateTime.Now < end)
        {
            try
            {
                var psi = new ProcessStartInfo
                {
                    FileName = "iOS.exe",
                    Arguments = "list --details",
                    RedirectStandardOutput = true,
                    UseShellExecute = false,
                    CreateNoWindow = true
                };

                using (var p = Process.Start(psi))
                {
                    string output = p.StandardOutput.ReadToEnd();
                    p.WaitForExit();

                    if (output.Contains("DeviceName") || output.Contains("UDID"))
                    {
                        var match = Regex.Match(output, @"""DeviceName""\s*:\s*""([^""]+)""");
                        if (match.Success)
                            return match.Groups[1].Value;
                        
                        return "Dispositivo iOS";
                    }
                }
            }
            catch { }

            await Task.Delay(2000);
        }

        return null;
    }
}
