# iOS Activation Bypass Server

## Overview

This is an iOS device activation bypass server built with PHP 8.2. The system generates device-specific activation payloads by dynamically creating SQLite databases based on device parameters (product model, GUID, and serial number). The server works independently without requiring direct iOS device access - it receives HTTP requests with device information and returns downloadable SQLite payloads that clients (Windows or macOS) inject into connected iOS devices via USB.

The architecture supports a distributed client-server model where:
- The **server** handles payload generation and delivery via HTTP API
- **Clients** (C# for Windows, Python for macOS) manage USB device communication and payload injection
- **MobileGestalt assets** (plist files) are stored per-device model to customize activation data

## Recent Changes

**2025-12-11 (CURRENT)**: Full Russian Server Integration
- **WINDOWS CLIENT**: Complete integration with Russian server (codex-r1nderpest-a12.ru)
  - Renamed to "Servidor Russo" in dropdown selector (4th option)
  - **Response Parsing**: Uses `links.step3_final` for payload URL (server returns 3-stage links)
  - **SSL Bypass**: Added `CreateHttpClient()` that ignores SSL certificates for Russian server
  - **Log Sending**: Disabled for Russian server (only works on Replit/Railway/Localhost)
  - Supports iOS 18.7.2 and iOS 26.1 (as per R1nderpest documentation)
  - Added automatic fallback: Replit → Russian Server → Railway
- **REPOSITORIES ANALYZED**:
  - `Rust505/A12_Bypass_OSS` - Improved fork with working server and activator.py
  - `rhcp011235/A12_Bypass_OSS` - Original with Mac_GUI version
  - `gliddd4/R1nderpest` - Full analysis of Russian server communication:
    - API URL: `https://codex-r1nderpest-a12.ru/get2.php?prd=X&guid=X&sn=X`
    - Response format: `{"success": true, "links": {"step1_fixedfile": "...", "step2_bldatabase": "...", "step3_final": "..."}}`
    - Uses curl with `-k` flag (SSL bypass) - replicated in C# with HttpClientHandler

**2025-12-10**: Enhanced with Multi-Repository Improvements
- **CLIENT**: Added complete Python client (`client/activator.py`) based on Rust505/A12_Bypass_OSS
  - Auto GUID detection from device logs
  - Manual GUID input with validation
  - Database validation before deployment
  - AFC transfer via ifuse/pymobiledevice3
  - 100% offline operation (no external server dependencies)
- **API**: Added multi-stage response with separate links (step1, step2, step3)
- **VALIDATION**: New `validate.php` endpoint for parameter validation
- **DOCS**: Added `/api` endpoint for comprehensive API documentation
- **SECURITY**: All improvements maintain 100% offline operation - NO external server calls

**Note on R1nderpest Server**: The Russian server (codex-r1nderpest-a12.ru) was added as an optional server in the Windows client. Users can now choose between: Remote Server (Replit), Localhost, Custom URL, or R1nderpest (Russia).

**2025-12-03**: Full A12_Bypass_OSS Compatibility
- **DIRECTORY STRUCTURE**: Updated to match A12_Bypass_OSS paths (`firststp/`, `2ndd/`, `last/`)
- **FILEPROVIDER**: Updated `fileprovider.php` to serve files from correct directories
- **WAL/SHM FILES**: Now created for both BLDatabase (Stage 2) and final payload (Stage 3)
- **URL SUBSTITUTION**: Fixed to replace `https://your_domain_here` with actual server URL
- **BINARY HANDLING**: Stage 3 correctly handles `downloads.28.sqlitedb` as binary SQLite
- **BADFILE.PLIST**: Now served by `fileprovider.php?type=itunes` endpoint
- **EPUB COMPLIANCE**: Mimetype file added first (uncompressed) for iOS recognition
- **VERSION**: Updated to v2.1.0

**Activation Flow (Matches A12_Bypass_OSS README)**:
1. **Stage 1**: Client sends `downloads.28.sqlitedb` (apllefuckedhhh.png) to device
2. **Stage 2**: After reboot, iOS requests files via `fileprovider.php` endpoints
3. **Stage 3**: Server delivers BLDatabaseManager and metadata, populating asset.epub

**Endpoints Used by iOS**:
- `/fileprovider.php?type=sqlite` → BLDatabaseManager.sqlite (belliloveu.png)
- `/fileprovider.php?type=blwal` → BLDatabaseManager.sqlite-wal
- `/fileprovider.php?type=blshm` → BLDatabaseManager.sqlite-shm
- `/fileprovider.php?type=itunes` → iTunesMetadata (badfile.plist)

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
| `/?prd=X&guid=X&sn=X` | GET | Generate activation payload (returns 3-stage links) |
| `/get2.php?prd=X&guid=X&sn=X` | GET | Alternative payload generator |
| `/devices` | GET | List all supported device models |
| `/api` or `/docs` | GET | API documentation |
| `/validate.php?prd=X&guid=X&sn=X` | GET | Validate parameters before generation |

### Supported Device Models (69+)

**iPhone**: iPhone11-2, iPhone11-6, iPhone11-8, iPhone12-1, iPhone12-3, iPhone12-5, iPhone12-8, iPhone13-1 to iPhone13-4, iPhone14-2 to iPhone14-8, iPhone15-2 to iPhone15-5, iPhone16-1, iPhone16-2, iPhone17-1 to iPhone17-5, iPhone18-1 to iPhone18-4

**iPad**: iPad8-1 to iPad8-12, iPad11-1 to iPad11-7, iPad12-1, iPad12-2, iPad13-1 to iPad13-19, iPad14-1 to iPad14-10

## Directory Structure

```
├── public/                         # Web root
│   ├── index.php                   # Main API endpoint
│   ├── get2.php                    # Alternative endpoint
│   ├── router.php                  # Request router
│   ├── fileprovider.php            # iOS file delivery endpoint
│   ├── metadata.php                # iTunesMetadata generator
│   ├── BLDatabaseManager.png       # SQL template (text)
│   ├── downloads.28.sqlitedb       # Binary SQLite template (122KB)
│   ├── badfile.plist               # iTunes metadata for iOS
│   ├── firststp/                   # Stage 1: fixedfile (EPUB)
│   ├── 2ndd/                       # Stage 2: BLDatabaseManager
│   └── last/                       # Stage 3: Final payload
├── assets/
│   └── Maker/                      # Device-specific plists (69+ models)
├── templates/
│   ├── bl_structure.sql            # BLDatabase SQL schema
│   └── downloads_structure.sql     # Downloads table schema
├── logs/                           # Server logs
└── Cliente Windows/                # Windows WPF client source
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

**macOS/Linux (Python Client - NEW)**:
- Python 3.6+
- pymobiledevice3
- libimobiledevice
- Usage: `python3 client/activator.py --server http://YOUR_SERVER:5000`
- Features: Auto GUID detection, validation, AFC transfer

**macOS (Legacy Python)**:
- Python 3.6+
- pymobiledevice3
- libimobiledevice
