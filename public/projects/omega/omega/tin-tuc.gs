/**
 * OMEGA ERP – News CMS (Google Apps Script)
 *
 * Google Sheet structure (import từ omega-news-cms.xlsx):
 *   Sheet "Articles": hàng 1 = header, dữ liệu từ hàng 2
 *     A: id | B: slug | C: status | D: category | E: title | F: excerpt
 *     G: cover_image | H: author | I: published_date | J: read_time
 *     K: is_featured | L: tags | M: seo_title | N: seo_description | O: content
 *
 *   Sheet "Configs": A = key, B = value (hàng 1 = header)
 *     posts_per_page, site_url, cache_minutes, ...
 *
 * Deploy: Extensions → Apps Script → Deploy → New deployment
 *   Type: Web App | Execute as: Me | Who has access: Anyone
 * Sau khi deploy, copy URL dán vào const OMEGA_NEWS_GAS_URL ở tin-tuc.html
 *
 * Endpoints (GET):
 *   ?action=list                            → tất cả bài đã đăng (không có nội dung)
 *   ?action=list&cat=erp-quan-tri           → lọc theo danh mục
 *   ?action=list&page=2&per_page=9          → phân trang
 *   ?action=list&cat=erp-quan-tri&page=2    → lọc + phân trang
 *   ?action=post&slug=erp-la-gi             → bài viết đầy đủ (có nội dung)
 *   ?action=featured                        → 1 bài is_featured=TRUE mới nhất
 *   ?action=related&slug=erp-la-gi&cat=erp-quan-tri&limit=3  → bài liên quan
 *   ?action=sitemap                         → danh sách slug + date (cho SEO)
 */

// ── Tên sheets ─────────────────────────────────────────────────
const NEWS_SHEET     = 'Articles';
const CONFIGS_SHEET  = 'Configs';

// ── Chỉ số cột (0-based) ───────────────────────────────────────
const C = {
  id:           0,   // A
  slug:         1,   // B
  status:       2,   // C
  category:     3,   // D
  title:        4,   // E
  excerpt:      5,   // F
  cover_image:  6,   // G
  author:       7,   // H
  published_date: 8, // I
  read_time:    9,   // J
  is_featured:  10,  // K
  tags:         11,  // L
  seo_title:    12,  // M
  seo_desc:     13,  // N
  content:      14   // O
};

// ── Mặc định ───────────────────────────────────────────────────
const DEFAULT_PER_PAGE = 9;
const MAX_PER_PAGE     = 50;

// ============================================================
// ENTRY POINT
// ============================================================
function doGet(e) {
  try {
    const params = e.parameter || {};
    const action = params.action || 'list';

    const ss      = SpreadsheetApp.getActiveSpreadsheet();
    const configs = readConfigs(ss);

    let result;

    if (action === 'list') {
      result = handleList(ss, params, configs);

    } else if (action === 'post') {
      const slug = (params.slug || '').trim();
      if (!slug) return errorResponse('slug_required');
      result = handlePost(ss, slug);

    } else if (action === 'featured') {
      result = handleFeatured(ss);

    } else if (action === 'related') {
      result = handleRelated(ss, params);

    } else if (action === 'sitemap') {
      result = handleSitemap(ss);

    } else {
      return errorResponse('unknown_action');
    }

    return jsonpResponse(result, params.callback);

  } catch (err) {
    console.error('doGet error:', err.message, err.stack);
    return jsonpResponse({ success: false, error: err.message }, (e.parameter || {}).callback);
  }
}

// Hỗ trợ preflight OPTIONS (không cần thiết với no-cors nhưng phòng ngừa)
function doPost(e) {
  return jsonResponse({ status: 'ok', note: 'Use GET requests for this API' });
}

// ============================================================
// HANDLER: LIST
// ============================================================
function handleList(ss, params, configs) {
  const cat     = (params.cat      || '').trim();
  const page    = Math.max(1, parseInt(params.page    || 1,  10));
  const perPage = Math.min(MAX_PER_PAGE, Math.max(1,
                    parseInt(params.per_page || configs['posts_per_page'] || DEFAULT_PER_PAGE, 10)));

  const rows = getPublishedRows(ss);

  // Lọc theo danh mục
  const filtered = cat
    ? rows.filter(r => r[C.category] === cat)
    : rows;

  // Phân trang
  const total  = filtered.length;
  const pages  = Math.ceil(total / perPage);
  const start  = (page - 1) * perPage;
  const paged  = filtered.slice(start, start + perPage);

  return {
    success:  true,
    action:   'list',
    total:    total,
    page:     page,
    per_page: perPage,
    pages:    pages,
    cat:      cat || 'all',
    data:     paged.map(r => rowToCard(r))
  };
}

// ============================================================
// HANDLER: POST (single article with content)
// ============================================================
function handlePost(ss, slug) {
  const rows = getPublishedRows(ss);
  const row  = rows.find(r => String(r[C.slug] || '').trim() === slug);

  if (!row) return { success: false, error: 'not_found' };

  return {
    success: true,
    action:  'post',
    data:    rowToFull(row)
  };
}

// ============================================================
// HANDLER: FEATURED (1 bài nổi bật mới nhất)
// ============================================================
function handleFeatured(ss) {
  const rows = getPublishedRows(ss);
  const feat = rows.find(r => String(r[C.is_featured]).toLowerCase() === 'true');

  return {
    success: true,
    action:  'featured',
    data:    feat ? rowToCard(feat) : null
  };
}

// ============================================================
// HANDLER: RELATED (bài liên quan cùng danh mục)
// ============================================================
function handleRelated(ss, params) {
  const slug  = (params.slug  || '').trim();
  const cat   = (params.cat   || '').trim();
  const limit = Math.min(6, parseInt(params.limit || 3, 10));

  const rows    = getPublishedRows(ss);
  const related = rows
    .filter(r => String(r[C.slug] || '') !== slug && (!cat || r[C.category] === cat))
    .slice(0, limit);

  return {
    success: true,
    action:  'related',
    data:    related.map(r => rowToCard(r))
  };
}

// ============================================================
// HANDLER: SITEMAP
// ============================================================
function handleSitemap(ss) {
  const rows = getPublishedRows(ss);
  return {
    success: true,
    action:  'sitemap',
    data:    rows.map(r => ({
      slug:           String(r[C.slug] || ''),
      published_date: String(r[C.published_date] || ''),
      title:          String(r[C.title] || '')
    }))
  };
}

// ============================================================
// HELPERS
// ============================================================

/**
 * Lấy tất cả hàng đã published, sắp xếp mới nhất trước
 */
function getPublishedRows(ss) {
  const sheet = ss.getSheetByName(NEWS_SHEET);
  if (!sheet) return [];

  const data = sheet.getDataRange().getValues();
  if (data.length < 2) return [];

  // Hàng 0 là header, dữ liệu từ hàng 1
  const rows = data.slice(1).filter(r =>
    String(r[C.status] || '').trim().toLowerCase() === 'published' &&
    String(r[C.slug]   || '').trim() !== ''
  );

  // Sắp xếp theo published_date giảm dần (format DD/MM/YYYY)
  rows.sort((a, b) => {
    const da = parseDate(String(a[C.published_date] || ''));
    const db = parseDate(String(b[C.published_date] || ''));
    return db - da;
  });

  return rows;
}

/**
 * Row → card object (không có content, dùng cho listing)
 */
function rowToCard(r) {
  return {
    id:             String(r[C.id]           || ''),
    slug:           String(r[C.slug]         || '').trim(),
    category:       String(r[C.category]     || '').trim(),
    title:          String(r[C.title]        || '').trim(),
    excerpt:        String(r[C.excerpt]      || '').trim(),
    cover_image:    String(r[C.cover_image]  || '').trim(),
    author:         String(r[C.author]       || '').trim(),
    published_date: String(r[C.published_date] || '').trim(),
    read_time:      String(r[C.read_time]    || '').trim(),
    is_featured:    String(r[C.is_featured]  || '').toLowerCase() === 'true',
    tags:           String(r[C.tags]         || '').trim()
  };
}

/**
 * Row → full article object (có content, dùng cho trang bài viết)
 */
function rowToFull(r) {
  const card = rowToCard(r);
  card.seo_title    = String(r[C.seo_title] || '').trim() || card.title;
  card.seo_desc     = String(r[C.seo_desc]  || '').trim() || card.excerpt;
  card.content      = String(r[C.content]   || '').trim();
  return card;
}

/**
 * Parse DD/MM/YYYY → Date timestamp
 */
function parseDate(str) {
  if (!str || !str.includes('/')) return 0;
  const parts = str.split('/');
  if (parts.length !== 3) return 0;
  return new Date(
    parseInt(parts[2], 10),
    parseInt(parts[1], 10) - 1,
    parseInt(parts[0], 10)
  ).getTime();
}

/**
 * Đọc configs sheet (A=key, B=value, hàng 1=header)
 */
function readConfigs(ss) {
  const sheet  = ss.getSheetByName(CONFIGS_SHEET);
  const result = {};
  if (!sheet) return result;
  const rows = sheet.getDataRange().getValues();
  for (let i = 1; i < rows.length; i++) {
    const key = String(rows[i][0] || '').trim();
    const val = String(rows[i][1] || '').trim();
    if (key) result[key] = val;
  }
  return result;
}

/**
 * JSON hoặc JSONP response – JSONP khi có ?callback=fnName
 */
function jsonpResponse(obj, callback) {
  const json = JSON.stringify(obj);
  if (callback && /^[a-zA-Z_$][a-zA-Z0-9_$]*$/.test(callback)) {
    return ContentService
      .createTextOutput(callback + '(' + json + ')')
      .setMimeType(ContentService.MimeType.JAVASCRIPT);
  }
  return ContentService
    .createTextOutput(json)
    .setMimeType(ContentService.MimeType.JSON);
}

function jsonResponse(obj) { return jsonpResponse(obj, null); }
function errorResponse(message) { return jsonpResponse({ success: false, error: message }, null); }
