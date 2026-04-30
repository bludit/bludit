# Security Policy

  ## Reporting a Vulnerability

  If you believe you have found a security vulnerability in Bludit, please **do
  not** open a public GitHub issue, post in discussions, or disclose it on social
  media before it has been addressed.

  Instead, report it privately through GitHub's Private Vulnerability Reporting:

  **https://github.com/bludit/bludit/security/advisories/new**

  This opens a private advisory visible only to the Bludit maintainers.

  When reporting, please include:

  - A clear description of the vulnerability and its impact.
  - Steps to reproduce, including a proof of concept if possible.
  - The Bludit version, PHP version, and webserver where you reproduced it.
  - Any suggested mitigation or patch.

  ## What to Expect

  - We aim to acknowledge new reports within **5 business days**.
  - We will keep you informed as we investigate and work on a fix.
  - Once a fix is released, we will publish a GitHub Security Advisory crediting
    the reporter (unless you prefer to remain anonymous) and, where appropriate,
    request a CVE.

  ## Supported Versions

  Security fixes are provided for the **latest stable release** of Bludit on the
  `master` branch. Older versions are not supported — please upgrade before
  reporting issues against them.

  ## Scope

  In scope:

  - The Bludit core (`bl-kernel/`, `bl-plugins/` shipped with core, `bl-themes/`
    shipped with core).
  - The official admin interface.

  Out of scope:

  - Third-party plugins and themes not maintained in this repository.
  - Issues that require an already-compromised admin account or server.
  - Denial of service, rate-limiting, and brute-force concerns — these should be
    handled at the infrastructure layer (reverse proxy / WAF / Cloudflare).
  - Self-XSS, missing security headers without a demonstrated impact, and other
    best-practice findings without a concrete exploit.

  Thank you for helping keep Bludit and its users safe.
