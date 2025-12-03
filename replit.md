# iOS Activation Bypass Server

## Overview

This is an iOS device activation bypass server built with PHP 8.2. The system generates device-specific activation payloads by dynamically creating SQLite databases based on device parameters (product model, GUID, and serial number). The server works independently without requiring direct iOS device access - it receives HTTP requests with device information and returns downloadable SQLite payloads that clients (Windows or macOS) inject into connected iOS devices via USB.

The architecture supports a distributed client-server model where:
- The **server** handles payload generation and delivery via HTTP API
- **Clients** (C# for Windows, Python for macOS) manage USB device communication and payload injection
- **MobileGestalt assets** (plist files) are stored per-device model to customize activation data

## Recent Changes

**2025-12-03 (CURRENT)**: A12_Bypass_OSS Integration and Bug Fixes
- **CRITICAL FIX**: Updated `bl_structure.sql` template with correct Core Data schema (ZBLDOWNLOADINFO, Z_PRIMARYKEY, Z_METADATA)
- **CRITICAL FIX**: Updated `downloads_structure.sql` with proper `asset` and `download` tables structure
- **EPUB COMPLIANCE**: Added mimetype file to ZIP archives for proper EPUB format recognition by iOS
- **WAL/SHM FILES**: Automatic creation of `-wal` and `-shm` files for SQLite database integrity
- **NEW FILES**: Added `badfile.plist`, `BLDatabaseManager.png`, `downloads.28.png` from A12_Bypass_OSS
- **ROUTER FIX**: Updated router.php to properly handle root requests and API endpoints
- **DEVICE SUPPORT**: Now supporting 69+ device models with asset.epub files
- **VERSION**: Updated to v2.0.0

Improvements applied from rhcp011235/A12_Bypass_OSS repository:
1. Correct SQL template structure compatible with iOS Core Data
2. EPUB-compliant ZIP creation with mimetype entry
3. Enhanced logging and debugging
4. Better error handling and fallback mechanisms
5. WAL/SHM file generation for database integrity

## User Preferences

- Preferred communication style: Simple, everyday language
- All documentation and communication in Brazilian Portuguese (pt-BR)

## System Architecture

### Backend Architecture

**Core Components**:
- PHP 8.2 with SQLite3 and ZipArchive extensions
- Router-based request handling (router.php)
- Two payload generation endpoints (index.php, get2.php)

**Request Flow**:
1. Client sends GET request with device parameters (`prd`, `guid`, `sn`)
2. Server validates parameters and loads device-specific MobileGestalt.plist
3. Generates three-stage SQLite payload:
   - Stage 1: EPUB-compliant ZIP with MobileGestalt.plist
   - Stage 2: BLDatabaseManager SQLite with download URLs
   - Stage 3: Final downloads.28 payload with GUID substitution
4. Returns JSON with download URL

**Key Features**:
- Dynamic Payload Generation with unique tokens per request
- EPUB-compliant packaging for iOS compatibility
- WAL/SHM file generation for database integrity
- Automatic URL substitution with server domain
- Health check and device listing endpoints

### API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/` | GET | Health check, returns server status and version |
| `/?prd=X&guid=X&sn=X` | GET | Generate activation payload |
| `/get2.php?prd=X&guid=X&sn=X` | GET | Alternative payload generator |
| `/devices` | GET | List all supported device models |

### Supported Device Models (69+)

**iPhone**: iPhone11-2, iPhone11-6, iPhone11-8, iPhone12-1, iPhone12-3, iPhone12-5, iPhone12-8, iPhone13-1 to iPhone13-4, iPhone14-2 to iPhone14-8, iPhone15-2 to iPhone15-5, iPhone16-1, iPhone16-2, iPhone17-1 to iPhone17-5, iPhone18-1 to iPhone18-4

**iPad**: iPad8-1 to iPad8-12, iPad11-1 to iPad11-7, iPad12-1, iPad12-2, iPad13-1 to iPad13-19, iPad14-1 to iPad14-10

## Directory Structure

```
├── public/                    # Web root
│   ├── index.php             # Main API endpoint
│   ├── get2.php              # Alternative endpoint
│   ├── router.php            # Request router
│   ├── BLDatabaseManager.png # SQL template (binary)
│   ├── downloads.28.png      # SQL template (binary)
│   ├── badfile.plist         # Configuration file
│   └── cache/                # Generated payloads
├── assets/
│   └── Maker/               # Device-specific plists and epubs
├── templates/
│   ├── bl_structure.sql     # BLDatabase SQL schema
│   └── downloads_structure.sql # Downloads table schema
├── logs/                    # Server logs
└── Cliente Windows/         # Windows WPF client source
```

## External Dependencies

- **PHP 8.2**: Core runtime with sqlite3 and zip extensions
- **Replit**: Primary deployment platform
- **Railway**: Fallback deployment option
- **GitHub**: https://github.com/MarlonSuzuki/ios-activation-bypass-server

## Client Requirements

**Windows (WPF)**:
- .NET 10.0 runtime
- go-ios for USB communication

**macOS (Python)**:
- Python 3.6+
- pymobiledevice3
- libimobiledevice
