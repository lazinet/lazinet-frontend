#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
fix_icons_nav.py — Batch update all HTML files:
1. Wrap div.nav-cta + button.hamburger into div.nav-right
2. Add class="nav-phone-text" to phone number text span
3. badge-enhanced_W512.webp → mk-law-badge-02_W1024.webp
4. Replace emoji contact-info-icons with SVGs (same color)
5. Replace emoji footer contact icons with SVGs
6. Replace emoji in social buttons with SVG/image
7. Replace "Z" text in float-btn.zalo with zalo.png image
8. Replace emoji in mobile-menu phone buttons
"""

import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

html_files = []
for fname in sorted(os.listdir(ROOT)):
    if fname.endswith('.html'):
        html_files.append(os.path.join(ROOT, fname))
services_dir = os.path.join(ROOT, 'services')
for fname in sorted(os.listdir(services_dir)):
    if fname.endswith('.html'):
        html_files.append(os.path.join(services_dir, fname))

print(f"Found {len(html_files)} HTML files\n")

# ── SVG icon definitions (currentColor = inherits CSS color) ─────────────────

SVG_PHONE = (
    '<svg class="icon-svg" width="18" height="18" fill="currentColor" '
    'viewBox="0 0 24 24" aria-hidden="true">'
    '<path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 '
    '1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 '
    '0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/>'
    '</svg>'
)
SVG_LOCATION = (
    '<svg class="icon-svg" width="18" height="18" fill="currentColor" '
    'viewBox="0 0 24 24" aria-hidden="true">'
    '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z'
    'm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>'
    '</svg>'
)
SVG_EMAIL = (
    '<svg class="icon-svg" width="18" height="18" fill="currentColor" '
    'viewBox="0 0 24 24" aria-hidden="true">'
    '<path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z'
    'm0 4l-8 5-8-5V6l8 5 8-5v2z"/>'
    '</svg>'
)
SVG_CLOCK = (
    '<svg class="icon-svg" width="18" height="18" fill="currentColor" '
    'viewBox="0 0 24 24" aria-hidden="true">'
    '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z'
    'm.5 5v5.25l4.5 2.67-.75 1.23L11 13V7h1.5z"/>'
    '</svg>'
)
SVG_TIKTOK = (
    '<svg class="icon-svg" width="18" height="18" fill="currentColor" '
    'viewBox="0 0 24 24" aria-hidden="true">'
    '<path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5'
    ' 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28'
    ' 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 '
    '006.33-6.34V8.69a8.22 8.22 0 004.8 1.54V6.78a4.85 4.85 0 01-1.03-.09z"/>'
    '</svg>'
)
SVG_YOUTUBE = (
    '<svg class="icon-svg" width="18" height="18" fill="currentColor" '
    'viewBox="0 0 24 24" aria-hidden="true">'
    '<path d="M21.58 7.19c-.23-.86-.91-1.54-1.77-1.77C18.25 5 12 5 12 5s-6.25 0-7.81.42'
    'c-.86.23-1.54.91-1.77 1.77C2 8.75 2 12 2 12s0 3.25.42 4.81c.23.86.91 1.54 1.77 1.77'
    'C5.75 19 12 19 12 19s6.25 0 7.81-.42c.86-.23 1.54-.91 1.77-1.77C22 15.25 22 12 22 12'
    's0-3.25-.42-4.81zM10 15V9l5.2 3-5.2 3z"/>'
    '</svg>'
)
SVG_VIBER = (
    '<svg class="icon-svg" width="18" height="18" fill="currentColor" '
    'viewBox="0 0 24 24" aria-hidden="true">'
    '<path d="M12 1C5.9 1 1 5.9 1 12c0 3.1 1.3 5.9 3.3 7.9l-1 3.5 3.6-1c2 1 4.3 1.6 6.8'
    ' 1.6 5.5 0 10-4.5 10-10S17.5 1 12 1zm5.2 14.5c-.2.6-.9 1.1-1.6 1.3-.4.1-.9.1-1.3 0'
    '-2.4-.7-4.5-2-6.1-3.7-1.3-1.4-2.2-3.1-2.6-4.9-.1-.5 0-1 .2-1.4.3-.5.7-.9 1.2-1.1'
    '.5-.2 1 .1 1.3.5l1.1 1.7c.2.3.2.7-.1 1L8.9 10c.5 1.1 1.6 2.2 2.8 2.7l.7-.5c.3-.2'
    '.7-.2 1-.1l1.7 1.1c.4.3.6.8.4 1.3z"/>'
    '</svg>'
)

# ── Helpers ───────────────────────────────────────────────────────────────────

def zalo_img(prefix):
    return f'<img src="{prefix}assets/logos/zalo.png" width="20" height="20" alt="Zalo" loading="lazy" style="border-radius:4px;">'

def messenger_img(prefix):
    return f'<img src="{prefix}assets/logos/messenger.png" width="20" height="20" alt="Messenger" loading="lazy" style="border-radius:4px;">'


def fix_nav_right(html):
    """
    Wrap <div class="nav-cta">...</div> + <button class="hamburger"...>...</button>
    into <div class="nav-right">...</div>
    """
    # Already wrapped?
    if 'class="nav-right"' in html:
        return html

    # Pattern: nav-cta div followed (possibly whitespace) by hamburger button
    pattern = re.compile(
        r'(\s*<div class="nav-cta">.*?</div>)'
        r'(\s*<button class="hamburger".*?</button>)',
        re.DOTALL
    )
    def wrap(m):
        return f'\n    <div class="nav-right">{m.group(1)}{m.group(2)}\n    </div>'
    return pattern.sub(wrap, html, count=1)


def fix_badge512(html, is_sub):
    """badge-enhanced_W512.webp → mk-law-badge-02_W1024.webp"""
    if is_sub:
        return html.replace('../assets/logos/badge-enhanced_W512.webp',
                            '../assets/logos/mk-law-badge-02_W1024.webp')
    return html.replace('assets/logos/badge-enhanced_W512.webp',
                        'assets/logos/mk-law-badge-02_W1024.webp')


def fix_contact_info_icons(html):
    """Replace emoji inside .contact-info-icon divs with SVGs."""
    replacements = [
        ('<div class="contact-info-icon">📍</div>', f'<div class="contact-info-icon">{SVG_LOCATION}</div>'),
        ('<div class="contact-info-icon">📞</div>', f'<div class="contact-info-icon">{SVG_PHONE}</div>'),
        ('<div class="contact-info-icon">📧</div>', f'<div class="contact-info-icon">{SVG_EMAIL}</div>'),
        ('<div class="contact-info-icon">🕐</div>', f'<div class="contact-info-icon">{SVG_CLOCK}</div>'),
        ('<div class="contact-info-icon">📱</div>', f'<div class="contact-info-icon">{SVG_PHONE}</div>'),
    ]
    for old, new in replacements:
        html = html.replace(old, new)
    return html


def fix_footer_icons(html):
    """Replace emoji in footer contact items."""
    replacements = [
        ('<span class="icon">📍</span>', f'<span class="icon">{SVG_LOCATION}</span>'),
        ('<span class="icon">📞</span>', f'<span class="icon">{SVG_PHONE}</span>'),
        ('<span class="icon">📧</span>', f'<span class="icon">{SVG_EMAIL}</span>'),
        ('<span class="icon">🕐</span>', f'<span class="icon">{SVG_CLOCK}</span>'),
    ]
    for old, new in replacements:
        html = html.replace(old, new)
    return html


def fix_mobile_menu_phone(html):
    """Replace 📞 emoji in mobile-menu-bottom phone button."""
    html = html.replace(
        '>📞 0964 037 746<',
        f'>{SVG_PHONE} <span class="nav-phone-text">0964 037 746</span><'
    )
    return html


def fix_social_buttons(html, is_sub):
    """Replace emoji in social channel buttons with SVG/images."""
    p = '../' if is_sub else ''
    replacements = [
        # social-btn buttons
        ('>📞 Gọi điện ngay<', f'>{SVG_PHONE} Gọi điện ngay<'),
        ('>📞 Gọi điện<',      f'>{SVG_PHONE} Gọi điện<'),
        ('>💬 Chat Zalo<',     f'>{zalo_img(p)} Chat Zalo<'),
        ('>💬 Zalo: 0964 037 746<', f'>{zalo_img(p)} Zalo: 0964 037 746<'),
        ('>💬 Messenger<',     f'>{messenger_img(p)} Messenger<'),
        ('>📧 Email<',         f'>{SVG_EMAIL} Email<'),
        ('>🎵 TikTok<',        f'>{SVG_TIKTOK} TikTok<'),
        ('>▶️ YouTube<',       f'>{SVG_YOUTUBE} YouTube<'),
        ('>📱 Viber<',         f'>{SVG_VIBER} Viber<'),
        # CTA section phone buttons
        ('>📞 0964 037 746<',  f'>{SVG_PHONE} <span class="nav-phone-text">0964 037 746</span><'),
    ]
    for old, new in replacements:
        html = html.replace(old, new)
    return html


def fix_float_zalo(html, is_sub):
    """Replace 'Z' text in float-btn.zalo with zalo.png image."""
    p = '../' if is_sub else ''
    img = zalo_img(p)
    # Pattern: <a href="...zalo..." class="float-btn zalo" ...>Z</a>
    # or ...<a ...class="float-btn zalo"...>Z</a>
    html = re.sub(
        r'(<a[^>]*class="float-btn zalo"[^>]*>)\s*Z\s*(</a>)',
        lambda m: m.group(1) + img + m.group(2),
        html
    )
    return html


def fix_nav_phone_text(html):
    """Wrap phone number text in nav with span.nav-phone-text for responsive hiding."""
    # Pattern in nav: >SVG 0964 037 746</a>
    # The phone SVG is already there, just wrap the number text
    html = re.sub(
        r'(<a[^>]*class="nav-phone"[^>]*>.*?</svg>\s*)(0964 037 746)(\s*</a>)',
        r'\1<span class="nav-phone-text">\2</span>\3',
        html,
        flags=re.DOTALL
    )
    return html


# ── Main ──────────────────────────────────────────────────────────────────────

for fpath in html_files:
    is_sub = os.path.dirname(fpath) != ROOT

    with open(fpath, 'r', encoding='utf-8') as f:
        html = f.read()

    original = html

    html = fix_nav_right(html)
    html = fix_badge512(html, is_sub)
    html = fix_contact_info_icons(html)
    html = fix_footer_icons(html)
    html = fix_mobile_menu_phone(html)
    html = fix_social_buttons(html, is_sub)
    html = fix_float_zalo(html, is_sub)
    html = fix_nav_phone_text(html)

    if html != original:
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(html)
        print(f"  UPDATED: {os.path.relpath(fpath, ROOT)}")
    else:
        print(f"  (no change): {os.path.relpath(fpath, ROOT)}")

print("\nDone.")
