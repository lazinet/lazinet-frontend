/**
 * OMEGA ERP – Comment Handler
 * Google Apps Script – deploy riêng, độc lập với lien-he.gs
 *
 * Google Sheet cần có 2 sheets:
 *   • Configs       : A=key, B=value  (cấu hình email, v.v.)
 *   • Comment-Data  : 6 cột dữ liệu bình luận
 *
 * Cột Comment-Data:
 *   A: Thời gian  B: Slug  C: Tên  D: Email  E: SĐT  F: Nội dung  G: Đánh giá (0–5)  H: Visibility (TRUE/FALSE)
 *
 * Quy trình kiểm duyệt:
 *   - Mỗi bình luận mới được lưu với Visibility = TRUE (hiển thị ngay)
 *   - Admin mở Google Sheet, đổi cột H thành FALSE để ẩn bình luận tiêu cực
 *   - doGet chỉ trả về các dòng có Visibility = TRUE
 *
 * Deploy: Deploy → New deployment → Web App
 *   Execute as : Me
 *   Who has access : Anyone
 * Sau khi deploy, copy URL dán vào hằng số COMMENT_GAS_URL trong tool.
 *
 * Configs sheet (A1:B5):
 *   key             value
 *   logo-url        https://...logo.png
 *   signature-url   https://...signature.png
 *   cc-emails       email1@cty.com,email2@cty.com
 *   bcc-emails      bcc@cty.com
 *
 * Email thông báo bình luận gửi đến: tài khoản Google đang chạy GAS (Execute as: Me)
 * CC/BCC theo cấu hình trong Configs.
 */

// ── Tên sheets ──────────────────────────────────────────────
const COMMENT_SHEET = 'Comment-Data';
const CONFIG_SHEET  = 'Configs';

// ── Anti-spam thresholds ─────────────────────────────────
const MIN_TIME_SECS   = 5;     // ít nhất 5 giây trên trang
const MAX_TIME_SECS   = 3600;  // tối đa 1 giờ
const MIN_HUMAN_SCORE = 2;     // ngưỡng human score tối thiểu

// ── Rate limit ────────────────────────────────────────────
const RATE_WINDOW_HOURS = 24;  // 1 bình luận / email / bài / 24h

// ============================================================
// doPost — nhận bình luận mới
// ============================================================
function doPost(e) {
  try {
    const raw  = e.postData ? e.postData.contents : '{}';
    const data = JSON.parse(raw);

    // 1. Anti-spam
    if (isSpam(data)) {
      return jsonResp({ ok: false, error: 'spam_detected' });
    }

    // 2. Validate
    const slug    = (data.slug    || '').trim();
    const name    = (data.name    || '').trim();
    const email   = (data.email   || '').trim();
    const phone   = (data.phone   || '').trim();
    const content = (data.content || '').trim();
    const rating  = Math.min(5, Math.max(0, parseInt(data.rating || 0, 10)));

    if (!slug)              return jsonResp({ ok: false, error: 'missing_slug' });
    if (!name)              return jsonResp({ ok: false, error: 'missing_name' });
    if (!email || !isValidEmail(email)) return jsonResp({ ok: false, error: 'invalid_email' });
    if (content.length < 10) return jsonResp({ ok: false, error: 'content_too_short' });

    const ss      = SpreadsheetApp.getActiveSpreadsheet();
    const configs = readConfigs(ss);

    // 3. Rate limit: 1 bình luận / email / slug / 24h
    if (isDuplicate(ss, slug, email)) {
      return jsonResp({ ok: false, error: 'rate_limit' });
    }

    // 4. Lưu vào sheet
    saveComment(ss, { slug, name, email, phone, content, rating });

    // 5. Gửi email xác nhận cho người bình luận
    sendConfirmationToCommenter(configs, { slug, name, email, phone, content, rating });

    // 6. Gửi email thông báo cho admin
    sendNotification(configs, { slug, name, email, phone, content, rating });

    return jsonResp({ ok: true });

  } catch (err) {
    console.error('doPost error:', err.message, err.stack);
    return jsonResp({ ok: false, error: 'server_error' });
  }
}

// ============================================================
// doGet — trả về danh sách bình luận theo slug
// Endpoint: ?slug=ten-bai-viet
// ============================================================
function doGet(e) {
  try {
    const slug = (e.parameter && e.parameter.slug)
      ? String(e.parameter.slug).trim()
      : '';

    if (!slug) return jsonResp({ ok: true, comments: [] });

    const ss    = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = ss.getSheetByName(COMMENT_SHEET);

    if (!sheet || sheet.getLastRow() <= 1) {
      return jsonResp({ ok: true, comments: [] });
    }

    const rows     = sheet.getDataRange().getValues();
    const comments = [];

    for (let i = 1; i < rows.length; i++) {
      const rowSlug = String(rows[i][1] || '').trim();
      if (rowSlug !== slug) continue;

      const ts   = rows[i][0];
      const date = ts instanceof Date ? formatDate(ts) : String(ts);

      // Chỉ hiển thị khi Visibility = TRUE (staff đã duyệt)
      const visible = rows[i][7];
      if (visible !== true && String(visible).toUpperCase() !== 'TRUE') continue;

      // CHỈ trả về tên, ngày, nội dung, đánh giá — KHÔNG trả email/SĐT (bảo mật)
      comments.push({
        name:    String(rows[i][2] || '').trim(),
        date:    date,
        content: String(rows[i][5] || '').trim(),
        rating:  parseInt(rows[i][6] || 0, 10)
      });
    }

    // Thứ tự cũ → mới (đã theo thứ tự sheet, nhưng sort lại cho chắc)
    comments.sort(function(a, b) { return a.date < b.date ? -1 : 1; });

    // Thống kê đánh giá sao
    const ratedList  = comments.filter(function(c) { return c.rating > 0; });
    const avgRating  = ratedList.length
      ? Math.round(ratedList.reduce(function(s, c) { return s + c.rating; }, 0) / ratedList.length * 10) / 10
      : 0;

    return jsonResp({ ok: true, comments: comments, avg_rating: avgRating, rated_count: ratedList.length });

  } catch (err) {
    console.error('doGet error:', err.message);
    return jsonResp({ ok: true, comments: [] });
  }
}

// ============================================================
// ANTI-SPAM
// ============================================================
function isSpam(data) {
  const honeypot   = (data.website_omega || '').trim();
  const token      = (data.token_bot     || '').trim();
  const startTime  = parseInt(data.form_start_time || 0, 10);
  const humanScore = parseInt(data.human_score     || 0, 10);
  const timeSpent  = startTime > 0 ? (Date.now() - startTime) / 1000 : 0;

  return (
    honeypot !== ''               ||  // honeypot bị điền → bot
    token    === ''               ||  // không có token → bypass browser
    timeSpent < MIN_TIME_SECS     ||  // submit quá nhanh
    timeSpent > MAX_TIME_SECS     ||  // để form quá lâu
    humanScore < MIN_HUMAN_SCORE      // ít tương tác như người thật
  );
}

// ============================================================
// RATE LIMIT — 1 bình luận / email / slug trong RATE_WINDOW_HOURS
// ============================================================
function isDuplicate(ss, slug, email) {
  const sheet = ss.getSheetByName(COMMENT_SHEET);
  if (!sheet || sheet.getLastRow() <= 1) return false;

  const rows      = sheet.getDataRange().getValues();
  const cutoff    = new Date(Date.now() - RATE_WINDOW_HOURS * 3600 * 1000);
  const emailLower = email.toLowerCase();

  for (let i = 1; i < rows.length; i++) {
    const rowDate  = rows[i][0] instanceof Date ? rows[i][0] : new Date(rows[i][0]);
    const rowSlug  = String(rows[i][1] || '').trim();
    const rowEmail = String(rows[i][3] || '').trim().toLowerCase();

    if (rowSlug === slug && rowEmail === emailLower && rowDate >= cutoff) {
      return true;
    }
  }
  return false;
}

// ============================================================
// LƯU BÌNH LUẬN
// ============================================================
function saveComment(ss, c) {
  let sheet = ss.getSheetByName(COMMENT_SHEET);
  if (!sheet) {
    sheet = ss.insertSheet(COMMENT_SHEET);
  }

  if (sheet.getLastRow() === 0) {
    sheet.appendRow(['Thời gian', 'Slug', 'Tên', 'Email', 'SĐT', 'Nội dung', 'Đánh giá', 'Visibility']);
    const hdr = sheet.getRange(1, 1, 1, 8);
    hdr.setFontWeight('bold');
    hdr.setBackground('#00A651');
    hdr.setFontColor('#ffffff');
  }

  // Visibility mặc định TRUE — hiển thị ngay, admin tắt FALSE nếu cần
  sheet.appendRow([new Date(), c.slug, c.name, c.email, c.phone, c.content, c.rating || 0, true]);
}

// ============================================================
// GỬI EMAIL XÁC NHẬN CHO NGƯỜI BÌNH LUẬN
// ============================================================
function sendConfirmationToCommenter(configs, c) {
  const logoUrl      = configs['logo-url']      || '';
  const signatureUrl = configs['signature-url'] || '';

  const subject = `[OMEGA ERP] Cảm ơn bạn đã bình luận – ${c.name}`;

  const logoBlock = logoUrl
    ? `<img src="${logoUrl}" alt="OMEGA ERP" style="max-width:180px;height:auto;margin-bottom:8px;">`
    : `<span style="font-size:24px;font-weight:800;color:#00A651;font-family:Arial,sans-serif;">OMEGA ERP</span>`;

  const sigBlock = signatureUrl
    ? `<br><img src="${signatureUrl}" alt="Chữ ký" style="width:100%;max-width:600px;height:auto;margin-top:12px;display:block;">`
    : '';

  const articleUrl = `https://omega.com.vn/tin-tuc/${escGs(c.slug)}.html`;

  const excerpt = c.content.length > 300
    ? c.content.substring(0, 300) + '…'
    : c.content;

  const htmlBody = `<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<style>
  @media only screen and (max-width:600px){
    .ow  { padding: 12px 4px !important; }
    .pad { padding-left: 16px !important; padding-right: 16px !important; }
    .h1  { font-size: 18px !important; }
    .box { padding: 12px !important; }
  }
</style>
</head>
<body style="margin:0;padding:0;background:#f0f4f0;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" class="ow"
       style="background:#f0f4f0;padding:32px 16px;">
  <tr><td align="center">
  <table width="600" cellpadding="0" cellspacing="0"
         style="background:#ffffff;border-radius:14px;overflow:hidden;
                box-shadow:0 6px 24px rgba(0,0,0,0.09);max-width:600px;width:100%;">

    <!-- Logo -->
    <tr>
      <td align="center" class="pad" style="padding:32px 40px 20px;background:#ffffff;">
        ${logoBlock}
      </td>
    </tr>

    <!-- Accent bar -->
    <tr>
      <td style="background:linear-gradient(90deg,#00A651 0%,#7ed957 100%);
                 height:4px;line-height:4px;font-size:0;">&nbsp;</td>
    </tr>

    <!-- Greeting -->
    <tr>
      <td class="pad" style="padding:28px 40px 12px;">
        <h1 class="h1" style="margin:0 0 10px;font-size:22px;font-weight:700;
                               color:#1a1a1a;line-height:1.3;">
          Xin chào ${escGs(c.name)},
        </h1>
        <p style="margin:0;font-size:15px;color:#555;line-height:1.75;">
          Cảm ơn bạn đã dành thời gian bình luận trên blog của
          <strong>OMEGA ERP</strong>! Bình luận của bạn đã được ghi nhận thành công.
        </p>
      </td>
    </tr>

    <!-- Comment summary box -->
    <tr>
      <td class="pad" style="padding:8px 40px 20px;">
        <table width="100%" cellpadding="0" cellspacing="0"
               style="background:#f6fbf7;border:1px solid #c8e6d0;
                      border-radius:10px;border-collapse:collapse;">
          <tr>
            <td class="box" style="padding:16px 20px;">
              <p style="margin:0 0 12px;font-size:11px;font-weight:700;color:#00A651;
                         text-transform:uppercase;letter-spacing:1.5px;">
                Bình luận của bạn
              </p>
              <p style="margin:0 0 6px;font-size:14px;color:#333;">
                <strong>Bài viết:</strong>&nbsp;
                <a href="${articleUrl}" style="color:#00A651;text-decoration:none;">
                  ${escGs(c.slug)}.html
                </a>
              </p>
              <p style="margin:10px 0 6px;font-size:11px;font-weight:700;color:#00A651;
                         text-transform:uppercase;letter-spacing:1.5px;">Nội dung</p>
              <p style="margin:0;font-size:14px;color:#444;line-height:1.8;
                         border-left:3px solid #00A651;padding-left:12px;font-style:italic;">
                &ldquo;${escGs(excerpt)}&rdquo;
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Message -->
    <tr>
      <td class="pad" style="padding:0 40px 24px;">
        <p style="margin:0;font-size:15px;color:#444;line-height:1.75;">
          Nếu có bất kỳ câu hỏi nào về nội dung bài viết hoặc muốn tìm hiểu thêm
          về giải pháp ERP, đừng ngần ngại liên hệ với chúng tôi.
        </p>
      </td>
    </tr>

    <!-- Dark block -->
    <tr>
      <td class="pad" style="padding:0 40px 32px;">
        <table width="100%" cellpadding="0" cellspacing="0"
               style="background:#1a3a2a;border-radius:10px;border-collapse:collapse;">
          <tr>
            <td style="padding:20px 24px;">
              <p style="margin:0 0 10px;font-size:11px;font-weight:700;color:#7ed957;
                         text-transform:uppercase;letter-spacing:1.5px;">Về OMEGA ERP</p>
              <p style="margin:0;font-size:14px;color:rgba(255,255,255,0.88);line-height:1.8;">
                Hơn <strong style="color:#7ed957;">16 năm kinh nghiệm</strong> triển khai
                ERP cho <strong style="color:#7ed957;">1.000+ doanh nghiệp</strong>
                sản xuất và phân phối tại Việt Nam.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Signature -->
    <tr>
      <td class="pad" style="padding:0 40px 28px;border-top:1px solid #eee;">
        <p style="margin:20px 0 4px;font-size:14px;color:#666;">Trân trọng,</p>
        <p style="margin:0 0 2px;font-size:15px;font-weight:700;color:#1a1a1a;">
          Đội ngũ OMEGA ERP
        </p>
        <p style="margin:0;font-size:13px;color:#888;line-height:1.8;">
          Tel: <a href="tel:+84835128448" style="color:#00A651;text-decoration:none;">
            (84-8) 3512 8448
          </a>
          &nbsp;|&nbsp;
          Web: <a href="https://omega.com.vn" style="color:#00A651;text-decoration:none;">
            omega.com.vn
          </a>
        </p>
        ${sigBlock}
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td class="pad" style="background:#f6f9f6;padding:16px 40px;border-top:1px solid #e8eee8;">
        <p style="margin:0;font-size:11px;color:#aaa;text-align:center;line-height:1.7;">
          Email này được gửi tự động khi bạn bình luận tại
          <a href="${articleUrl}" style="color:#00A651;">${escGs(c.slug)}.html</a>.<br>
          Cần hỗ trợ: gọi
          <a href="tel:+84835128448" style="color:#00A651;">(84-8) 3512 8448</a>
          hoặc email
          <a href="mailto:info@omega.com.vn" style="color:#00A651;">info@omega.com.vn</a>.
        </p>
      </td>
    </tr>

  </table>
  </td></tr>
</table>

</body>
</html>`;

  const plainText = [
    `Xin chào ${c.name},`,
    '',
    'Cảm ơn bạn đã bình luận trên blog OMEGA ERP!',
    `Bài viết: https://omega.com.vn/tin-tuc/${c.slug}.html`,
    '',
    'Bình luận của bạn:',
    excerpt,
    '',
    'Trân trọng,',
    'Đội ngũ OMEGA ERP',
    'Tel: (84-8) 3512 8448  |  Web: omega.com.vn'
  ].join('\n');

  GmailApp.sendEmail(c.email, subject, plainText, {
    htmlBody: htmlBody,
    name:     'OMEGA ERP',
    replyTo:  'info@omega.com.vn'
  });
}

// ============================================================
// GỬI EMAIL THÔNG BÁO CHO ADMIN
// Email TO: tài khoản đang chạy GAS (Execute as: Me)
// CC/BCC: theo Configs
// ============================================================
function sendNotification(configs, c) {
  const toEmail      = Session.getActiveUser().getEmail();
  const logoUrl      = configs['logo-url']      || '';
  const signatureUrl = configs['signature-url'] || '';
  const ccEmails     = configs['cc-emails']     || '';
  const bccEmails    = configs['bcc-emails']    || '';

  const subject = `[OMEGA Blog] Bình luận mới – ${c.name} – /${c.slug}.html`;

  const logoBlock = logoUrl
    ? `<img src="${logoUrl}" alt="OMEGA ERP" style="max-width:160px;height:auto;">`
    : `<span style="font-size:22px;font-weight:800;color:#00A651;font-family:Arial,sans-serif;">OMEGA ERP</span>`;

  const sigBlock = signatureUrl
    ? `<br><img src="${signatureUrl}" alt="Chữ ký" style="width:100%;max-width:560px;height:auto;margin-top:10px;display:block;">`
    : '';

  const phoneRow = c.phone
    ? `<p style="margin:0 0 4px;font-size:14px;color:#333;"><strong>SĐT:</strong> <a href="tel:${escGs(c.phone)}" style="color:#00A651;">${escGs(c.phone)}</a></p>`
    : '';

  const htmlBody = `<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f0f4f0;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f0;padding:28px 12px;">
  <tr><td align="center">
  <table width="580" cellpadding="0" cellspacing="0"
         style="background:#fff;border-radius:14px;overflow:hidden;max-width:580px;width:100%;box-shadow:0 6px 24px rgba(0,0,0,0.09);">

    <!-- Logo -->
    <tr><td align="center" style="padding:28px 36px 18px;">${logoBlock}</td></tr>

    <!-- Accent bar -->
    <tr><td style="background:linear-gradient(90deg,#00A651 0%,#7ed957 100%);height:4px;line-height:4px;font-size:0;">&nbsp;</td></tr>

    <!-- Header -->
    <tr><td style="padding:24px 36px 10px;">
      <h2 style="margin:0 0 6px;font-size:20px;font-weight:700;color:#1a1a1a;">
        💬 Bình luận mới trên blog
      </h2>
      <p style="margin:0;font-size:14px;color:#666;">
        Bài viết: <strong><a href="https://omega.com.vn/tin-tuc/${escGs(c.slug)}.html"
          style="color:#00A651;text-decoration:none;">/${escGs(c.slug)}.html</a></strong>
      </p>
    </td></tr>

    <!-- Info box -->
    <tr><td style="padding:8px 36px 20px;">
      <table width="100%" cellpadding="0" cellspacing="0"
             style="background:#f6fbf7;border:1px solid #c8e6d0;border-radius:10px;">
        <tr><td style="padding:16px 20px;">
          <p style="margin:0 0 10px;font-size:11px;font-weight:700;color:#00A651;
                     text-transform:uppercase;letter-spacing:1.5px;">Thông tin người bình luận</p>
          <p style="margin:0 0 4px;font-size:14px;color:#333;"><strong>Tên:</strong> ${escGs(c.name)}</p>
          <p style="margin:0 0 4px;font-size:14px;color:#333;">
            <strong>Email:</strong> <a href="mailto:${escGs(c.email)}" style="color:#00A651;">${escGs(c.email)}</a>
          </p>
          ${phoneRow}
          <p style="margin:14px 0 8px;font-size:11px;font-weight:700;color:#00A651;
                     text-transform:uppercase;letter-spacing:1.5px;">Nội dung bình luận</p>
          <p style="margin:0;font-size:14px;color:#444;line-height:1.8;
                     border-left:3px solid #00A651;padding-left:12px;font-style:italic;">
            &ldquo;${escGs(c.content)}&rdquo;
          </p>
        </td></tr>
      </table>
    </td></tr>

    <!-- Footer hint -->
    <tr><td style="padding:0 36px 20px;border-top:1px solid #eee;">
      <p style="margin:16px 0 4px;font-size:13px;color:#999;">
        Quản lý toàn bộ bình luận trong Google Sheet liên kết với GAS này.
      </p>
      ${sigBlock}
    </td></tr>

    <!-- Footer bar -->
    <tr><td style="background:#f6f9f6;padding:14px 36px;border-top:1px solid #e8eee8;">
      <p style="margin:0;font-size:11px;color:#bbb;text-align:center;">
        OMEGA ERP &nbsp;|&nbsp;
        <a href="https://omega.com.vn" style="color:#00A651;text-decoration:none;">omega.com.vn</a>
        &nbsp;|&nbsp; Tel: (84-8) 3512 8448
      </p>
    </td></tr>

  </table>
  </td></tr>
</table>
</body></html>`;

  const plainText = [
    `Bình luận mới từ: ${c.name}`,
    `Bài viết: /${c.slug}.html`,
    '',
    `Tên    : ${c.name}`,
    `Email  : ${c.email}`,
    c.phone ? `SĐT    : ${c.phone}` : '',
    '',
    'Nội dung:',
    c.content,
    '',
    'Xem chi tiết trong Google Sheet đính kèm GAS.'
  ].filter(l => l !== null).join('\n');

  const mailOpts = {
    to:       toEmail,
    subject:  subject,
    htmlBody: htmlBody,
    name:     'OMEGA Blog Comments',
    replyTo:  c.email
  };
  if (ccEmails)  mailOpts.cc  = ccEmails;
  if (bccEmails) mailOpts.bcc = bccEmails;

  GmailApp.sendEmail(toEmail, subject, plainText, mailOpts);
}

// ============================================================
// HELPERS
// ============================================================
function readConfigs(ss) {
  const sheet  = ss.getSheetByName(CONFIG_SHEET);
  const result = {};
  if (!sheet) return result;
  const rows = sheet.getDataRange().getValues();
  for (let i = 1; i < rows.length; i++) {
    const k = String(rows[i][0] || '').trim();
    const v = String(rows[i][1] || '').trim();
    if (k) result[k] = v;
  }
  return result;
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function formatDate(date) {
  const d = date.getDate().toString().padStart(2, '0');
  const m = (date.getMonth() + 1).toString().padStart(2, '0');
  const y = date.getFullYear();
  const h = date.getHours().toString().padStart(2, '0');
  const min = date.getMinutes().toString().padStart(2, '0');
  return `${d}/${m}/${y} ${h}:${min}`;
}

function escGs(str) {
  return String(str || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function jsonResp(obj) {
  return ContentService
    .createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}
