# Lazinet – Build Notes: Minh Kỳ Lawfirm Website

**Ngày khởi tạo:** 2026-04-06  
**Deployment:** Cloudflare Pages  
**Domain dự kiến:** minhkylawfirm.vn

---

## Cấu trúc dự án

```
minhkylawfirm/
├── index.html              ← Trang chủ (hero video, stats, services overview)
├── introduction.html       ← Giới thiệu công ty (từ company profile docx)
├── services.html           ← Danh sách 8 dịch vụ
├── lawyers.html            ← Đội ngũ luật sư (click-to-expand profile)
├── activities.html         ← 6 album slider hoạt động
├── videos.html             ← YouTube video grid theo chủ đề
├── consulting.html         ← Chatbot + FAQ + Booking form
├── news.html               ← Tin tức & Case study
├── contacts.html           ← Liên hệ đa kênh + Form + Google Maps
├── robots.txt
├── sitemap.xml
├── assets/
│   ├── css/style.css       ← Design system duy nhất
│   ├── js/main.js          ← JS duy nhất: Slider, Navbar, FAQ, Chatbot, Animations
│   ├── logos/              ← logo-enhanced_W512.webp (logo), badge-enhanced_W*
│   ├── favicon/            ← favicon.ico, site.webmanifest
│   ├── lawyers/            ← 01-ths-ls-levietky.png, 01-ths-ls-levietky-profile.png
│   ├── owner/              ← ky-finalized.jpg
│   ├── services/           ← thumbnails + sub-folders (thêm ảnh cho slider)
│   └── activities/         ← chuyen-nganh, dao-tao, hoat-dong, su-kien, to-tung, tu-van
├── services/               ← 8 trang con dịch vụ
│   ├── legal-services.html
│   ├── litigation-services.html
│   ├── enterprise-fdi.html
│   ├── invest-ma-ip.html
│   ├── labor-export.html
│   ├── tax-lifecycle.html
│   ├── non-litigation-representation.html
│   └── dissolution-bankruptcy.html
├── json/                   ← Data files
│   ├── company.json
│   ├── lawyers.json
│   ├── services.json
│   └── activities.json
└── lazinet/                ← Scripts & notes (không deploy)
    ├── initial-offer.xlsx
    ├── gen_service_pages.py
    ├── company-profile.txt
    └── lazinet.md (file này)
```

---

## Design System

### Colors
- Navy: `#0a1628` (primary dark)
- Gold: `#c8a84b` (accent)
- Off-white: `#f8f6f1` (section bg)
- Text: `#1a1916`

### Fonts (Google Fonts)
- Playfair Display – headings
- Inter – body text
- Cormorant Garamond – accent/tagline

### Breakpoints
- Large: > 1024px
- Medium: 641px – 1024px
- Small: ≤ 640px

---

## Key Components (assets/js/main.js)

| Component | Trigger |
|---|---|
| Preloader | `#preloader` → auto hide on window.load |
| Navbar scroll | `#navbar` → `.scrolled` after 60px scroll |
| Mobile menu | `#hamburger` toggle `#mobile-menu.open` |
| Slider | `.slider-wrapper` → class `Slider` |
| Scroll animation | `.animate-on-scroll` → IntersectionObserver |
| FAQ accordion | `.faq-question` → toggle `.open` |
| Lawyers panel | `.lawyer-card[data-lawyer-id]` → show panel |
| Chatbot | `#chatbot-company` / `#chatbot-legal` → rule-based |
| Counter | `[data-count]` → count up animation |
| Contact form | `#contact-form` → submit handler |

---

## Slider – Đặc tả kỹ thuật

**CSS (assets/css/style.css):**
```css
.slider-wrapper { width: 80%; } /* màn lớn */
@media (max-width:1024px) { .slider-wrapper { width: 90%; } }
@media (max-width:640px)  { .slider-wrapper { width: 100%; } }
```
- Ảnh: `width: 100%`, `height: auto` → slider height tự giãn theo ảnh
- Video slides: không auto-play, để user control
- Auto-advance: 4500ms, dừng khi hover, dừng khi video đang phát

**Thêm ảnh cho service sliders:**
```
assets/services/legal-services/        ← thêm ảnh .jpg/.webp
assets/services/litigation-services/
assets/services/enterprise-fdi/
assets/services/invest-ma-ip/
assets/services/labor-export/
assets/services/tax-lifecycle/
assets/services/non-litigation-representation/
assets/services/dissolution-bankruptcy/
```

---

## SEO

- Schema.org `LegalService` trên index.html
- Meta title/description/OG trên mọi trang
- robots.txt: allow all trừ /lazinet/ và /json/
- sitemap.xml: 17 URLs
- Semantic HTML: `<nav aria-label>`, `<section>`, `<article>`, `<footer role="contentinfo">`
- Alt text trên mọi ảnh

---

## Deployment – Cloudflare Pages

```bash
# Build command: không cần (static HTML)
# Output directory: / (root)
# Cấu hình domain: minhkylawfirm.vn

# _redirects file (tùy chọn, tạo nếu cần):
# /home  /index.html  301
```

**Bước deploy:**
1. Push code lên GitHub repo
2. Connect Cloudflare Pages → GitHub repo
3. Build settings: Framework = None, output = /
4. Custom domain: minhkylawfirm.vn → thêm CNAME/ALIAS

---

## TODO / Roadmap

- [ ] Thêm ảnh vào `assets/services/*/` để slider service sub-pages có nhiều ảnh
- [ ] Tích hợp Google Analytics 4 (thêm tag vào `<head>` mỗi trang)
- [ ] Tích hợp Microsoft Clarity  
- [ ] Setup Google Search Console
- [ ] Tích hợp backend form (Google Apps Script) để nhận form contact/booking qua email
- [ ] Thêm video hero background vào `assets/videos/hero.mp4`
- [ ] Cập nhật YouTube video IDs thực tế vào videos.html
- [ ] Thêm luật sư mới vào lawyers.html khi team mở rộng
- [ ] SSL Certificate → Cloudflare tự động (Universal SSL)
- [ ] WAF → Cloudflare WAF (free tier)

---

## Thông tin công ty

| Field | Value |
|---|---|
| Tên đầy đủ | CÔNG TY LUẬT TRÁCH NHIỆM HỮU HẠN MỘT THÀNH VIÊN MINH KỲ |
| Tên ngắn | MINH KY LAWFIRM |
| MST | 0319365453 |
| Ngày hoạt động | 16/01/2026 |
| Giám đốc | ThS LS Lê Viết Kỳ |
| Điện thoại | 0964037746 |
| Email | levietkylaw@gmail.com |
| Địa chỉ | Số F2.2, Tầng 2, Sabay Building, 38 Cộng Hòa, Tân Sơn Nhất, TP.HCM |
