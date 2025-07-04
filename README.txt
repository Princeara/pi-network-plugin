# 🪙 Pi Network WordPress Integration

Integrate Pi Network login and payments into your WordPress site. Let users authenticate via Pi SDK, process Web3 payments, verify receipts, and manage everything from a sleek dashboard.

![Built for WordPress](https://img.shields.io/badge/Platform-WordPress-blue)
![PHP >= 7.2](https://img.shields.io/badge/PHP-7.2%2B-green)
![Plugin Status](https://img.shields.io/badge/Status-Active-success)

---

## ✨ Features

- 🔐 Login with Pi Network SDK
- 💸 Verify Pi payments and store securely in DB
- 📩 Send email receipts to users and/or admin
- 📊 Admin dashboard widget with recent transactions
- 📤 Export payments to CSV
- 🔧 Sandbox mode toggle for test environments
- 🌐 Translation-ready (`__()`, `.pot` included)

---

## 🚀 Installation

1. Upload the plugin folder to `/wp-content/plugins/pi-network-plugin`
2. Activate via the **Plugins** menu
3. Navigate to **Settings → Pi Network** and enter your:
   - ✅ App ID
   - ✅ Private Key
4. Adjust toggles (sandbox mode, email preferences)
5. Add shortcodes to your pages as needed

---

## 🧩 Shortcodes

| Purpose | Shortcode |
|--------|-----------|
| Pi Login Button | `[pi_login_button]` |
| Pi Login + Pay Button | `[pi_pay_now]` |

Embed these into any post, page, or widget to display interactive buttons for users.

---

## 🌐 Translation Support

All UI strings are wrapped with `__()` and `_e()` functions. A `.pot` template file is included in `/languages`.  
Use tools like [Poedit](https://poedit.net/) or [Loco Translate](https://wordpress.org/plugins/loco-translate/) to create `.po` and `.mo` files for your language.

---

## 📦 Export & Admin Tools

- Recent payments visible via **Dashboard Widget**
- Export all transactions via **Tools → Export CSV**
- Toggle receipt and notification emails in settings

---

## 🛠 Developer Notes

- Payments are stored in `wp_pi_payments`
- Plugin structure is modular:
  - `/includes/` handles core logic
  - `/admin/` contains settings UI
- Validated with WordPress Plugin Check plugin
- ZIP and ship-ready ✨

---

## 🕓 Changelog

### 1.0
- Initial stable release
- Pi login and payment integration
- Email toggle settings added
- Dashboard widget and export added
- Internationalization support with `.pot`

---

## 📬 Credits

**Author:** Olatoye  
**Plugin Companion:** Microsoft Copilot 🤖

---

## 📜 License

GPLv2 or later. See `/LICENSE.txt` for full license.

