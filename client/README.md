# iOS Activation Bypass - Python Client

## Overview

This is the official Python client for the iOS Activation Bypass server. It works **100% OFFLINE** with your self-hosted server - no external dependencies or Russian servers required.

## Requirements

### macOS
```bash
# Install libimobiledevice
brew install libimobiledevice

# Install Python dependencies
pip3 install pymobiledevice3
```

### Linux (Ubuntu/Debian)
```bash
# Install libimobiledevice
sudo apt-get install libimobiledevice-utils

# Install Python dependencies
pip3 install pymobiledevice3
```

## Usage

### 1. Start Your Server First

Make sure your PHP server is running:

```bash
# From the server directory
php -S YOUR_IP:8000 -t public public/router.php
```

### 2. Run the Client

```bash
# Basic usage
python3 activator.py --server http://YOUR_SERVER_IP:8000

# Examples
python3 activator.py --server http://192.168.1.100:8000
python3 activator.py --server http://localhost:5000
```

## Features

- **Auto GUID Detection**: Automatically scans device logs to find BLDatabaseManager GUID
- **Manual GUID Input**: Enter GUID manually with validation
- **Database Validation**: Validates SQLite payload before deployment
- **AFC Transfer**: Deploys payload directly to device via USB
- **100% Offline**: No external server connections required

## GUID Acquisition

The tool can auto-detect the GUID from device logs, but if that fails, you can get it manually from:

https://hanakim3945.github.io/posts/download28_sbx_escape/

## Workflow

1. Connect iOS device via USB
2. Run the client with your server URL
3. Choose GUID detection method (auto or manual)
4. Wait for payload generation
5. Deploy to device
6. Wait for reboot and asset.epub to appear
7. **Keep server running** until process completes

## Troubleshooting

### "Device not found"
- Make sure device is connected via USB
- Trust the computer on your iOS device
- Check that libimobiledevice is installed: `ideviceinfo`

### "Server request failed"
- Verify your server is running
- Check the server URL is correct
- Test: `curl http://YOUR_SERVER:PORT/`

### "Database validation failed"
- Server may have template issues
- Check server logs for errors

## Security Note

This tool is designed to work **entirely offline** with your self-hosted server. Unlike some other tools, it:
- Does NOT communicate with external/Russian servers
- Does NOT download payloads from unknown sources
- Does NOT send your device data anywhere
- All payloads are generated locally on YOUR server

## Credits

Based on A12_Bypass_OSS by rhcp011235/Rust505
Enhanced for security and offline operation
