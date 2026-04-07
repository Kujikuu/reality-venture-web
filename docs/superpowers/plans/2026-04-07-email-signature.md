# Email Signature Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create a standalone HTML email signature file that Reality Venture team members can customize and paste into their email clients.

**Architecture:** Single self-contained HTML file using table-based layout with all CSS inline and the logo base64-encoded. No external dependencies.

**Tech Stack:** HTML tables, inline CSS, base64-encoded PNG, web-safe fonts (Arial/Helvetica)

---

### Task 1: Resize the logo for email use

The source logo (`public/assets/images/RVHorizonal.png`) is 2614x639px. At 120px display width, the base64 string from the full-size image is ~58KB -- too heavy for an email signature. Resize to 240px wide (2x for retina) to keep the encoded size reasonable.

**Files:**
- Source: `public/assets/images/RVHorizonal.png`
- Create: `public/assets/images/RVHorizonal-email.png`

- [ ] **Step 1: Resize the logo using sips**

```bash
sips --resampleWidth 240 "public/assets/images/RVHorizonal.png" --out "public/assets/images/RVHorizonal-email.png"
```

Expected: A 240x~59px PNG file created.

- [ ] **Step 2: Verify the resized file**

```bash
sips -g pixelWidth -g pixelHeight "public/assets/images/RVHorizonal-email.png"
```

Expected: pixelWidth: 240, pixelHeight: ~59

- [ ] **Step 3: Generate the base64 string and note it for Task 2**

```bash
base64 -i "public/assets/images/RVHorizonal-email.png" | tr -d '\n'
```

Save this output -- it will be embedded directly in the HTML file in the next task.

---

### Task 2: Create the email signature HTML file

**Files:**
- Create: `public/email-signature.html`

- [ ] **Step 1: Create `public/email-signature.html`**

Write the full HTML file with this structure. Replace `BASE64_LOGO_HERE` with the actual base64 string from Task 1, Step 3.

```html
<!--
  Reality Venture - Email Signature Template
  ===========================================

  HOW TO USE:
  1. Open this file in a text editor (VS Code, Sublime, Notepad, etc.)
  2. Find and replace these placeholders with your info:
     - {{Full Name}}    => Your full name (e.g., Ahmed Afifi)
     - {{Job Title}}    => Your role (e.g., Chief Technology Officer)
     - {{Phone Number}} => Your phone with country code (e.g., +966 50 123 4567)
     - {{LinkedIn URL}} => Your LinkedIn profile URL (e.g., https://linkedin.com/in/yourname)
     - {{X URL}}        => Your X profile URL (e.g., https://x.com/yourhandle)
  3. Save the file
  4. Open the saved file in a web browser (Chrome, Safari, Firefox)
  5. Press Cmd+A (Mac) or Ctrl+A (Windows) to select all
  6. Press Cmd+C (Mac) or Ctrl+C (Windows) to copy
  7. Go to your email client's signature settings and paste
-->
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:0;">

<table cellpadding="0" cellspacing="0" border="0" style="font-family:Arial,Helvetica,sans-serif;max-width:500px;">
  <tr>
    <!-- Left Column: Logo -->
    <td style="vertical-align:middle;padding-right:16px;">
      <img
        src="data:image/png;base64,BASE64_LOGO_HERE"
        alt="Reality Venture"
        width="120"
        style="display:block;width:120px;height:auto;"
      />
    </td>

    <!-- Divider -->
    <td style="width:2px;background-color:#4d3070;font-size:0;line-height:0;" width="2">&nbsp;</td>

    <!-- Right Column: Contact Info -->
    <td style="vertical-align:middle;padding-left:16px;">
      <!-- Full Name -->
      <!-- Replace {{Full Name}} with your name -->
      <span style="display:block;font-size:14px;font-weight:bold;color:#4d3070;margin:0 0 2px 0;">{{Full Name}}</span>

      <!-- Job Title -->
      <!-- Replace {{Job Title}} with your role -->
      <span style="display:block;font-size:12px;color:#666666;margin:0 0 8px 0;">{{Job Title}}</span>

      <!-- Phone -->
      <!-- Replace {{Phone Number}} with your phone (e.g., +966 50 123 4567) -->
      <a href="tel:{{Phone Number}}" style="display:block;font-size:12px;color:#181411;text-decoration:none;margin:0 0 2px 0;">{{Phone Number}}</a>

      <!-- Website -->
      <a href="https://rv.com.sa" style="display:block;font-size:12px;color:#C88B00;text-decoration:none;margin:0 0 8px 0;">rv.com.sa</a>

      <!-- Social Links -->
      <table cellpadding="0" cellspacing="0" border="0">
        <tr>
          <!-- LinkedIn -->
          <!-- Replace {{LinkedIn URL}} with your LinkedIn profile URL -->
          <td style="padding-right:8px;">
            <a href="{{LinkedIn URL}}" style="text-decoration:none;">
              <img
                src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxOCIgaGVpZ2h0PSIxOCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjNGQzMDcwIj48cGF0aCBkPSJNMTkgMEg1QTUgNSAwIDAgMCAwIDV2MTRhNSA1IDAgMCAwIDUgNWgxNGE1IDUgMCAwIDAgNS01VjVhNSA1IDAgMCAwLTUtNXpNOCAxOUg1VjhoM3Yyem0tMS41LTEyLjI2OGMtLjk2NiAwLTEuNzUtLjc5LTEuNzUtMS43NjRzLjc4NC0xLjc2NCAxLjc1LTEuNzY0IDEuNzUuNzkgMS43NSAxLjc2NC0uNzgzIDEuNzY0LTEuNzUgMS43NjR6TTE5IDE5aC0zdi01LjYwNGMwLTMuMzY4LTQtMy4xMTMtNCAwVjE5aC0zVjhoM3YxLjc2NWMxLjM5Ni0yLjU4NiA3LTIuNzc3IDcgMi40NzZ2Ni43NTl6Ii8+PC9zdmc+"
                alt="LinkedIn"
                width="18"
                height="18"
                style="display:block;"
              />
            </a>
          </td>
          <!-- X (Twitter) -->
          <!-- Replace {{X URL}} with your X profile URL -->
          <td>
            <a href="{{X URL}}" style="text-decoration:none;">
              <img
                src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxOCIgaGVpZ2h0PSIxOCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjNGQzMDcwIj48cGF0aCBkPSJNMTguMjQ0IDIuMjVoMy4zMDhsLTcuMjI3IDguMjYgOC41MDIgMTEuMjRIMTYuMTdsLTUuMjE0LTYuODE3TDQuOTkgMjEuNzVIMS42OGw3LjczLTguODM1TDEuMjU0IDIuMjVINy44bDQuNzEzIDYuMjMxem0tMS4xNjEgMTcuNTJoMS44MzNMNy4wODQgNC4xMjZINS4xMTd6Ii8+PC9zdmc+"
                alt="X"
                width="18"
                height="18"
                style="display:block;"
              />
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</body>
</html>
```

- [ ] **Step 2: Verify the file renders correctly**

```bash
open "public/email-signature.html"
```

Expected: The file opens in the default browser showing the signature layout with the logo on the left, purple divider, and placeholder text on the right.

---

### Task 3: Commit

- [ ] **Step 1: Stage and commit**

```bash
git add public/email-signature.html public/assets/images/RVHorizonal-email.png
git commit -m "feat: add email signature HTML template for team members"
```
