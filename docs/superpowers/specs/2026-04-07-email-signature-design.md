# Email Signature Design

## Overview

A standalone HTML email signature file for Reality Venture team members. Each person copies the file, replaces placeholder values with their info, then pastes the signature into their email client (Gmail, Outlook, Apple Mail, etc.).

## Format

Single self-contained HTML file at `public/email-signature.html`. No external dependencies, no JavaScript, no server required.

## Layout

Horizontal two-column table layout:

| Left Column | Divider | Right Column |
|---|---|---|
| RV horizontal logo (base64-encoded, ~120px wide) | Vertical line in brand purple (#4d3070) | Name, title, phone, website, social links |

Total width capped at 500px.

## Right Column Content (top to bottom)

1. **Full name** -- bold, dark (#181411), ~14px
2. **Job title** -- regular weight, ~12px, muted color
3. **Phone number** -- linked with `tel:` protocol
4. **Website** -- `rv.com.sa`, linked
5. **Social icons** -- LinkedIn and X, small base64/inline SVG icons, linked

## Editable Placeholders

Team members replace these before copying:

- `{{Full Name}}` -- their full name
- `{{Job Title}}` -- their role
- `{{Phone Number}}` -- their phone with country code
- `{{LinkedIn URL}}` -- personal LinkedIn profile URL
- `{{X URL}}` -- personal X profile URL

Each placeholder is accompanied by an HTML comment for easy identification.

## Branding

- **Primary purple**: #4d3070 (divider, name color)
- **Gold accent**: #C88B00 (subtle use on divider or links)
- **Text dark**: #181411
- **Font stack**: Arial, Helvetica, sans-serif (web-safe only; custom fonts like Public Sans do not work in email clients)

## Logo

The horizontal logo (`RVHorizonal.png`) is base64-encoded directly into the HTML. This ensures the image always renders regardless of email client image-blocking settings.

## Email Client Compatibility

Best practices enforced:

- Table-based layout (no flexbox, no grid)
- All CSS inline (no `<style>` blocks -- Gmail strips them)
- Base64-encoded images (no remote image dependencies)
- Social icons as inline SVG or base64 (no icon fonts)
- No JavaScript
- Web-safe fonts only
- Compact design (4-5 lines of info max)

## File Location

`public/email-signature.html`

## Usage Instructions (included as HTML comment in the file)

1. Open `email-signature.html` in a browser
2. Right-click > View Source or open in a text editor
3. Replace all `{{placeholder}}` values with your info
4. Save the file
5. Open the edited file in a browser
6. Select all (Cmd+A / Ctrl+A), copy (Cmd+C / Ctrl+C)
7. Paste into your email client's signature settings
