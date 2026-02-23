# GDPRLocal Partner Landing Pages

A WordPress plugin that generates custom, branded landing pages for GDPR Local partners — each with a unique URL, partner discount, and pricing display. Partners are managed via a secure REST API.

---

## Requirements

- WordPress 5.8+
- PHP 7.4+
- [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) (Free or PRO)

---

## Installation

1. Clone or download this repository into your WordPress plugins directory:
   ```
   wp-content/plugins/gdprlocal-partner-landing-pages/
   ```
2. Activate the plugin from **Plugins → Installed Plugins** in the WordPress admin.
3. Ensure **Advanced Custom Fields** is installed and active — the plugin will show an admin notice if it's missing.
4. Go to **Settings → Permalinks** and click **Save Changes** to flush rewrite rules (required on first activation).

---

## Configuration

Open the main plugin file and update the API key constant near the top:

```php
define('GDPRLOCAL_API_KEY', 'your-secret-api-key-here');
```

> ⚠️ Do not commit your real API key to version control. Consider moving this to `wp-config.php` or an environment variable.

---

## How It Works

### Partner Pages
Each partner gets a landing page at `yourdomain.com/{slug}` (e.g. `yourdomain.com/acme-corp`). The page displays:
- Partner logo alongside the GDPRLocal logo
- A personalised discount banner
- EU and UK Article 27 Representative pricing cards with monthly/yearly billing toggle
- Sign Up / Sign In CTAs that append the partner code to the portal URL

### Custom Post Type
Partners are stored as a custom post type (`partner`) in WordPress. Each partner post holds all configuration via ACF fields — logo, pricing, discount, portal URLs, and partner code.

### Asset Enqueueing
CSS and JS are only loaded on partner pages (`is_singular('partner')`), keeping the rest of your site unaffected.

---

## REST API

### Authentication

All API requests must include the following header:

```
X-GDPRLOCAL-KEY: your-secret-api-key-here
```

Requests without a valid key will receive a `403 Forbidden` response.

---

### Create a Partner

**Endpoint**
```
POST /wp-json/gdprlocal/v1/partner
```

**Headers**
```
Content-Type: application/json
X-GDPRLOCAL-KEY: your-secret-api-key-here
```

**Success Response**
```json
{
  "success": true,
  "url": "https://yourdomain.com/acme-corp"
}
```

---

### Request Body

| Field | Type | Required | Description |
|---|---|---|---|
| `CompanyName` | string | ✅ | Display name of the partner company |
| `PartnerCode` | string | ✅ | Unique code appended to portal URLs (must be unique) |
| `LandingPageUrl` | string | ✅ | URL slug for the landing page (must be unique) |
| `Logo` | string | ✅ | Base64-encoded PNG or JPG image (with data URI prefix) |
| `LogoType` | string | ✅ | Logo format descriptor (e.g. `"png"`, `"svg"`) |
| `DiscountPercent` | number | ✅ | Discount percentage (0–100) |
| `BasePrices` | object | ✅ | Original prices before discount |
| `DiscountedPrices` | object | ✅ | Prices after discount is applied |
| `PortalUrlRegister` | string | ✅ | Base URL for the partner registration page |
| `PortalUrlLogin` | string | ✅ | Base URL for the partner login page |

**Price Object Structure** (same shape for both `BasePrices` and `DiscountedPrices`):

```json
{
  "Article27": {
    "EuMonthly": 0.00,
    "EuYearly": 0.00,
    "UkMonthly": 0.00,
    "UkYearly": 0.00
  }
}
```

---

### Example Request

```json
{
  "CompanyName": "Acme Corp",
  "PartnerCode": "ACME2024",
  "LandingPageUrl": "acme-corp",
  "Logo": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==",
  "LogoType": "png",
  "DiscountPercent": 20,
  "PortalUrlRegister": "https://portal.gdprlocal.com/register",
  "PortalUrlLogin": "https://portal.gdprlocal.com/login",
  "BasePrices": {
    "Article27": {
      "EuMonthly": 49.99,
      "EuYearly": 479.99,
      "UkMonthly": 44.99,
      "UkYearly": 431.99
    }
  },
  "DiscountedPrices": {
    "Article27": {
      "EuMonthly": 39.99,
      "EuYearly": 383.99,
      "UkMonthly": 35.99,
      "UkYearly": 345.59
    }
  }
}
```

**Example cURL**

```bash
curl -X POST https://yourdomain.com/wp-json/gdprlocal/v1/partner \
  -H "Content-Type: application/json" \
  -H "X-GDPRLOCAL-KEY: your-secret-api-key-here" \
  -d @payload.json
```

---

### Error Responses

| HTTP Code | Error Code | Reason |
|---|---|---|
| `400` | `missing_field` | A required field is absent or empty |
| `400` | `invalid_discount` | Discount is not a number between 0–100 |
| `400` | `invalid_price` | A price value is non-numeric or negative |
| `400` | `invalid_logo` | Logo is not a valid Base64 PNG or JPG |
| `400` | `duplicate_slug` | `LandingPageUrl` already in use |
| `400` | `duplicate_code` | `PartnerCode` already in use |
| `403` | `forbidden` | Missing or invalid API key |
| `500` | `upload_error` | WordPress failed to save the logo to the media library |

---

## File Structure

```
gdprlocal-partner-landing-pages/
├── gdprlocal-partner-landing-pages.php   # Main plugin file
├── templates/
│   └── partner-template.php              # Front-end landing page template
├── assets/
│   ├── css/
│   │   └── style.css                     # Landing page styles
│   ├── js/
│   │   └── scripts.js                    # Billing toggle and interactivity
│   └── images/
│       └── gdprlogowhite.webp            # GDPRLocal logo asset
└── README.md
```

---

## Notes

- Partner landing pages use a fully custom HTML shell (not the active theme) to avoid inheriting theme headers, menus, and footers, while still firing `wp_head()` and `wp_footer()` so SEO plugins (e.g. Yoast) and enqueued assets work correctly.