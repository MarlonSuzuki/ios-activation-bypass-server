using System;
using System.Diagnostics;
using System.IO;
using System.Net.Http;
using System.Threading.Tasks;
using System.Windows;

namespace ClienteWindows
{
    public class ServerLauncher
    {
        private static Process serverProcess;
        private static string baseDir = AppDomain.CurrentDomain.BaseDirectory;
        private static string phpPath = Path.Combine(baseDir, "php.exe");
        private static string publicDir = Path.Combine(baseDir, "public");

        public static void StartServer()
        {
            try
            {
                if (!File.Exists(phpPath))
                {
                    throw new FileNotFoundException($"PHP não encontrado em: {phpPath}\n\nBaixe PHP portable de: https://windows.php.net/download");
                }

                if (!Directory.Exists(publicDir))
                {
                    throw new DirectoryNotFoundException($"Diretório public não encontrado em: {publicDir}");
                }

                serverProcess = new Process
                {
                    StartInfo = new ProcessStartInfo
                    {
                        FileName = phpPath,
                        Arguments = $"-S 0.0.0.0:5000 -t \"{publicDir}\"",
                        UseShellExecute = false,
                        RedirectStandardOutput = true,
                        RedirectStandardError = true,
                        CreateNoWindow = true,
                        WorkingDirectory = baseDir
                    }
                };

                serverProcess.Start();
                System.Threading.Thread.Sleep(2000);

                if (!IsServerRunning())
                {
                    throw new Exception("Servidor PHP falhou ao iniciar. Verifique se a porta 5000 está disponível.");
                }

                Console.WriteLine("[OK] Servidor PHP iniciado na porta 5000");
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erro ao iniciar servidor:\n{ex.Message}", "Erro", MessageBoxButton.OK, MessageBoxImage.Error);
                throw;
            }
        }

        public static bool IsServerRunning()
        {
            try
            {
                using (var client = new HttpClient())
                {
                    var response = client.GetAsync("http://localhost:5000/health").Result;
                    return response.IsSuccessStatusCode;
                }
            }
            catch
            {
                return false;
            }
        }

        public static void StopServer()
        {
            try
            {
                if (serverProcess != null && !serverProcess.HasExited)
                {
                    serverProcess.Kill();
                    serverProcess.WaitForExit(5000);
                    Console.WriteLine("[OK] Servidor PHP encerrado");
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[AVISO] Erro ao encerrar servidor: {ex.Message}");
            }
        }
    }
}
