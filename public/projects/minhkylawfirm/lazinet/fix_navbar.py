#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
fix_navbar.py — Batch fix all HTML files:
1. Preloader image: badge-enhanced_W256.webp → preloader_W512.webp
2. Remove "Trang chủ" from nav-menu and mobile-menu
3. Reorder nav: Giới thiệu, Dịch vụ, Luật sư, Tư vấn, Hoạt động, Video, Tin tức
4. Logo img: width="48" height="48" → width="72" height="72"
"""

import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

# Collect all HTML files
html_files = []
for fname in sorted(os.listdir(ROOT)):
    if fname.endswith('.html'):
        html_files.append(os.path.join(ROOT, fname))

services_dir = os.path.join(ROOT, 'services')
for fname in sorted(os.listdir(services_dir)):
    if fname.endswith('.html'):
        html_files.append(os.path.join(services_dir, fname))

print(f"Found {len(html_files)} HTML files\n")

# ── Nav orders ────────────────────────────────────────────────────────────────

ROOT_ORDER = [
    ('introduction.html', 'Giới thiệu'),
    ('services.html',     'Dịch vụ'),
    ('lawyers.html',      'Luật sư'),
    ('consulting.html',   'Tư vấn'),
    ('activities.html',   'Hoạt động'),
    ('videos.html',       'Video'),
    ('news.html',         'Tin tức'),
]

SUB_ORDER = [
    ('../introduction.html', 'Giới thiệu'),
    ('../services.html',     'Dịch vụ'),
    ('../lawyers.html',      'Luật sư'),
    ('../consulting.html',   'Tư vấn'),
    ('../activities.html',   'Hoạt động'),
    ('../videos.html',       'Video'),
    ('../news.html',         'Tin tức'),
]

# ── Helpers ───────────────────────────────────────────────────────────────────

def fix_preloader(html, is_sub):
    if is_sub:
        return html.replace(
            '../assets/logos/badge-enhanced_W256.webp',
            '../assets/logos/preloader_W512.webp'
        )
    else:
        return html.replace(
            'assets/logos/badge-enhanced_W256.webp',
            'assets/logos/preloader_W512.webp'
        )


def fix_logo_size(html):
    """Replace width="48" height="48" → width="72" height="72" on logo img only."""
    # The logo img is inside <a class="nav-logo"...> block
    # Strategy: replace within nav-logo anchor only
    def replace_in_nav_logo(m):
        block = m.group(0)
        block = re.sub(r'width="48"', 'width="72"', block)
        block = re.sub(r'height="48"', 'height="72"', block)
        return block
    # Match from <a class="nav-logo" to </a> (first occurrence)
    return re.sub(
        r'<a[^>]*class="nav-logo"[^>]*>.*?</a>',
        replace_in_nav_logo,
        html,
        count=1,
        flags=re.DOTALL
    )


def fix_nav_menu(html, fname, is_sub):
    """Replace <ul class="nav-menu" ...> content with correct order."""
    order = SUB_ORDER if is_sub else ROOT_ORDER

    def make_items(indent='      '):
        lines = []
        for href, label in order:
            bare = href.lstrip('/')
            bare = bare.replace('../', '')
            is_active = (bare == fname)
            cls = 'nav-link active' if is_active else 'nav-link'
            lines.append(f'{indent}  <li><a href="{href}" class="{cls}">{label}</a></li>')
        return '\n'.join(lines)

    def replacer(m):
        # Detect indentation from original
        opening = m.group(1)
        items = make_items('      ')
        closing = m.group(3)
        return opening + '\n' + items + '\n    ' + closing

    return re.sub(
        r'(<ul class="nav-menu"[^>]*>)(.*?)(</ul>)',
        replacer,
        html,
        count=1,
        flags=re.DOTALL
    )


def fix_mobile_menu(html, fname, is_sub):
    """Replace <ul role="list"> inside mobile-menu div content with correct order."""
    order = SUB_ORDER if is_sub else ROOT_ORDER

    def make_items(indent='    '):
        lines = []
        for href, label in order:
            bare = href.replace('../', '')
            is_active = (bare == fname)
            cls = 'nav-link active' if is_active else 'nav-link'
            lines.append(f'{indent}  <li><a href="{href}" class="{cls}">{label}</a></li>')
        return '\n'.join(lines)

    # Find the mobile-menu div, then replace first <ul role="list"> inside it
    def mobile_replacer(m):
        block = m.group(0)
        # Replace the <ul role="list">...</ul> inside
        def ul_replacer(um):
            items = make_items('    ')
            return um.group(1) + '\n' + items + '\n  ' + um.group(3)
        block = re.sub(
            r'(<ul role="list">)(.*?)(</ul>)',
            ul_replacer,
            block,
            count=1,
            flags=re.DOTALL
        )
        return block

    return re.sub(
        r'<div class="mobile-menu"[^>]*>.*?</div>\s*</div>',
        mobile_replacer,
        html,
        count=1,
        flags=re.DOTALL
    )


# ── Main ──────────────────────────────────────────────────────────────────────

for fpath in html_files:
    is_sub = os.path.dirname(fpath) != ROOT
    fname = os.path.basename(fpath)

    with open(fpath, 'r', encoding='utf-8') as f:
        html = f.read()

    original = html

    html = fix_preloader(html, is_sub)
    html = fix_logo_size(html)
    html = fix_nav_menu(html, fname, is_sub)
    html = fix_mobile_menu(html, fname, is_sub)

    if html != original:
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(html)
        print(f"  UPDATED: {os.path.relpath(fpath, ROOT)}")
    else:
        print(f"  (no change): {os.path.relpath(fpath, ROOT)}")

print("\nDone.")
