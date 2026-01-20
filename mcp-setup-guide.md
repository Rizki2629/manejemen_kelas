# Browser MCP Setup untuk VS Code

## Prerequisites
- Node.js (versi LTS terbaru)
- VS Code dengan ekstensi GitHub Copilot

## Langkah Setup

### 1. Verifikasi Node.js
```powershell
node --version
npm --version
```

### 2. Test Browser MCP
```powershell
npx @browsermcp/mcp@latest
```
Tekan Ctrl+C setelah server mulai untuk stop.

### 3. Konfigurasi VS Code

#### Cara 1: Via Command Palette
1. Buka VS Code
2. Tekan `Ctrl+Shift+P`
3. Ketik "Preferences: Open User Settings (JSON)"
4. Tambahkan konfigurasi:

```json
{
  "mcpServers": {
    "browsermcp": {
      "command": "npx",
      "args": ["@browsermcp/mcp@latest"]
    }
  }
}
```

#### Cara 2: Manual File Edit
Buka file: `%APPDATA%\Code\User\settings.json`
Tambahkan konfigurasi di atas.

### 4. Restart VS Code
Setelah menyimpan settings, restart VS Code.

### 5. Verifikasi
- Buka GitHub Copilot Chat
- MCP server "browsermcp" harus muncul di daftar available servers
- Test dengan command yang menggunakan browser functionality

## Troubleshooting

### Error "command not found"
- Pastikan Node.js dalam PATH
- Test: `where node` dan `where npx`

### Server tidak muncul
- Periksa syntax JSON valid
- Restart VS Code completely
- Check VS Code output panel untuk error messages

### Timeout/Connection issues
- Pertama kali npx akan download package (tunggu)
- Check firewall/antivirus settings
- Try installing package globally: `npm install -g @browsermcp/mcp`

## Alternative: Local Installation
```powershell
npm install --save-dev @browsermcp/mcp
```

Ubah konfigurasi ke:
```json
{
  "mcpServers": {
    "browsermcp": {
      "command": "node",
      "args": ["./node_modules/@browsermcp/mcp/dist/index.js"]
    }
  }
}
```
