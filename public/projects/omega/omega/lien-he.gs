/**
 * OMEGA ERP – Contact Form Handler
 * Google Apps Script (GAS) – triển khai trên script.google.com
 *
 * Liên kết với Google Sheet có 2 sheets:
 *   • Configs      : A=key, B=value (dòng 1 là tiêu đề, dữ liệu từ dòng 2)
 *   • Contact-Data : 7 cột dữ liệu form
 *
 * Deploy: Deploy → New deployment → Web App
 *   Execute as : Me
 *   Who has access : Anyone
 * Sau khi deploy, copy URL dán vào const GAS_URL ở omega.js
 */

// ── Tên sheets ──────────────────────────────────────────────
const SHEET_CONFIGS  = 'Configs';
const SHEET_CONTACTS = 'Contact-Data';

// ── Anti-spam thresholds ──────────────────────────────────
const MIN_TIME_SECONDS = 3;    // ít nhất 3 giây trên trang
const MAX_TIME_SECONDS = 600;  // tối đa 10 phút
const MIN_HUMAN_SCORE  = 3;    // ngưỡng human score tối thiểu

// ============================================================
// ENTRY POINT – nhận POST từ form lien-he.html
// ============================================================
function doPost(e) {
  try {
    const raw  = e.postData ? e.postData.contents : '{}';
    const data = JSON.parse(raw);

    // 1. Kiểm tra spam
    const spam = checkAntiSpam(data);
    if (spam.isBot) {
      console.warn('Bot detected:', spam);
      return jsonResponse({ success: false, error: 'spam_detected' });
    }

    // 2. Lấy & validate các trường bắt buộc
    const name     = (data.name     || '').trim();
    const phone    = (data.phone    || '').trim();
    const email    = (data.email    || '').trim();
    const company  = (data.company  || '').trim();
    const industry = (data.industry || '').trim();
    const message  = (data.message  || '').trim();

    if (!name || !phone || !email || message.length < 3) {
      return jsonResponse({ success: false, error: 'missing_required_fields' });
    }

    // 3. Đọc cấu hình từ sheet Configs
    const ss      = SpreadsheetApp.getActiveSpreadsheet();
    const configs = readConfigs(ss);

    // 4. Ghi vào sheet Contact-Data
    saveContact(ss, { name, phone, email, company, industry, message });

    // 5. Gửi email xác nhận
    sendConfirmationEmail({ name, phone, email, company, industry, message }, configs);

    return jsonResponse({ success: true });

  } catch (err) {
    console.error('doPost error:', err.message, err.stack);
    return jsonResponse({ success: false, error: err.message });
  }
}

// ── Cho phép OPTIONS (preflight CORS) ──────────────────────
function doGet(e) {
  return jsonResponse({ status: 'ok', service: 'OMEGA Contact Form' });
}

// ============================================================
// ANTI-SPAM
// ============================================================
function checkAntiSpam(data) {
  const honeypot   = (data.website_omega || '').trim();
  const token      = (data.token_bot     || '').trim();
  const startTime  = parseInt(data.form_start_time || 0, 10);
  const humanScore = parseInt(data.human_score     || 0, 10);
  const now        = Date.now();
  const timeSpent  = startTime > 0 ? (now - startTime) / 1000 : 0;

  const isBot =
    honeypot !== ''                  || // honeypot bị điền → bot
    token    === ''                  || // không có token → submit thẳng, bỏ qua browser
    timeSpent < MIN_TIME_SECONDS     || // submit quá nhanh
    timeSpent > MAX_TIME_SECONDS     || // để form quá lâu (timeout)
    humanScore < MIN_HUMAN_SCORE;       // ít tương tác như người thật

  return { isBot, timeSpent, humanScore, honeypot: honeypot !== '' };
}

// ============================================================
// ĐỌC CONFIGS  (Sheet "Configs": A=key, B=value, hàng 1 là tiêu đề)
// ============================================================
function readConfigs(ss) {
  const sheet  = ss.getSheetByName(SHEET_CONFIGS);
  const result = {};
  if (!sheet) return result;

  const rows = sheet.getDataRange().getValues();
  // Hàng 0 là tiêu đề (key / value), dữ liệu từ hàng 1 trở đi
  for (let i = 1; i < rows.length; i++) {
    const key = String(rows[i][0] || '').trim();
    const val = String(rows[i][1] || '').trim();
    if (key) result[key] = val;
  }
  /*
   * Cấu trúc sheet Configs:
   *   A1: key          B1: value
   *   A2: logo-url     B2: https://...logo.png
   *   A3: signature-url  B3: https://...signature.png
   *   A4: cc-emails    B4: email1@cty.com,email2@cty.com
   *   A5: bcc-emails   B5: bcc@cty.com
   */
  return result;
}

// ============================================================
// LƯU DỮ LIỆU (Sheet "Contact-Data" – 7 cột)
// ============================================================
function saveContact(ss, contact) {
  let sheet = ss.getSheetByName(SHEET_CONTACTS);
  if (!sheet) {
    sheet = ss.insertSheet(SHEET_CONTACTS);
  }

  // Tạo hàng tiêu đề nếu sheet trống
  if (sheet.getLastRow() === 0) {
    sheet.appendRow([
      'Thời gian', 'Họ và tên', 'Điện thoại',
      'Email', 'Tên công ty', 'Ngành nghề', 'Nội dung tư vấn'
    ]);
    // Định dạng tiêu đề
    const header = sheet.getRange(1, 1, 1, 7);
    header.setFontWeight('bold');
    header.setBackground('#00A651');
    header.setFontColor('#ffffff');
  }

  sheet.appendRow([
    new Date(),
    contact.name,
    contact.phone,
    contact.email,
    contact.company,
    resolveIndustryLabel(contact.industry),
    contact.message
  ]);
}

// ============================================================
// GỬI EMAIL XÁC NHẬN CHO KHÁCH HÀNG
// ============================================================
function sendConfirmationEmail(contact, configs) {
  const { name, phone, email, company, industry, message } = contact;

  const logoUrl      = configs['logo-url']      || '';
  const signatureUrl = configs['signature-url'] || '';
  const ccEmails     = configs['cc-emails']     || '';
  const bccEmails    = configs['bcc-emails']    || '';

  const industryLabel = resolveIndustryLabel(industry);
  const subject = `[OMEGA ERP] Xác nhận yêu cầu tư vấn – ${name}`;

  // Trích dẫn nội dung (tối đa 250 ký tự)
  const excerpt = message.length > 250
    ? message.substring(0, 250) + '…'
    : message;

  const htmlBody = buildEmailHtml({
    name, phone, email, company,
    industryLabel, excerpt,
    logoUrl, signatureUrl
  });

  const mailOptions = {
    to:       email,
    subject:  subject,
    htmlBody: htmlBody,
    name:    'OMEGA.ERP Contact',
    replyTo: 'info@omega.com.vn'
  };

  if (ccEmails)  mailOptions.cc  = ccEmails;
  if (bccEmails) mailOptions.bcc = bccEmails;

  GmailApp.sendEmail(email, subject, /* plaintext fallback */ buildPlainText(contact, industryLabel), mailOptions);
}

// ============================================================
// PLAIN TEXT FALLBACK (cho email client không hiển thị HTML)
// ============================================================
function buildPlainText(contact, industryLabel) {
  return [
    `Xin chào ${contact.name},`,
    '',
    'Chúng tôi đã nhận được yêu cầu tư vấn của bạn từ website omega.com.vn.',
    'Chuyên gia OMEGA sẽ liên hệ lại trong vòng 2 giờ làm việc.',
    '',
    '--- Thông tin bạn gửi ---',
    `Họ và tên : ${contact.name}`,
    contact.company ? `Công ty    : ${contact.company}` : '',
    `Ngành nghề : ${industryLabel}`,
    `Điện thoại : ${contact.phone}`,
    `Email      : ${contact.email}`,
    '',
    'Nội dung tư vấn:',
    contact.message,
    '',
    '--- Về OMEGA ERP ---',
    'OMEGA ERP là phần mềm quản trị doanh nghiệp với 16+ năm kinh nghiệm,',
    'phục vụ 1.000+ doanh nghiệp sản xuất và phân phối tại Việt Nam.',
    '',
    'Trân trọng,',
    'Đội ngũ Tư vấn OMEGA ERP',
    'Tel: (84-8) 3512 8448  |  Web: omega.com.vn'
  ].filter(l => l !== null).join('\n');
}

// ============================================================
// BUILD HTML EMAIL
// ============================================================
function buildEmailHtml(p) {
  const { name, phone, email, company, industryLabel, excerpt, logoUrl, signatureUrl } = p;

  const logoBlock = logoUrl
    ? `<img src="${logoUrl}" alt="OMEGA ERP" style="max-width:180px;height:auto;margin-bottom:8px;">`
    : `<span style="font-size:24px;font-weight:800;color:#00A651;font-family:Arial,sans-serif;">OMEGA ERP</span>`;

  const sigBlock = signatureUrl
    ? `<br><img src="${signatureUrl}" alt="Chữ ký" style="width:100%;max-width:600px;height:auto;margin-top:12px;display:block;">`
    : '';

  const companyRow = company
    ? `<tr><td style="padding:4px 0;font-size:14px;color:#333;"><strong>Công ty:</strong> ${esc(company)}</td></tr>`
    : '';

  return `<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Xác nhận yêu cầu tư vấn – OMEGA ERP</title>
  <style>
    @media only screen and (max-width:600px){
      .ow  { padding: 12px 4px !important; }
      .pad { padding-left: 16px !important; padding-right: 16px !important; }
      .h1  { font-size: 18px !important; }
      .box { padding: 12px !important; }
      .dark-box { padding: 16px !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#f0f4f0;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation"
       class="ow" style="background:#f0f4f0;padding:32px 16px;">
  <tr><td align="center">
  <table width="600" cellpadding="0" cellspacing="0" role="presentation"
         style="background:#ffffff;border-radius:14px;overflow:hidden;
                box-shadow:0 6px 24px rgba(0,0,0,0.09);max-width:600px;width:100%;">

    <!-- ── LOGO ── -->
    <tr>
      <td align="center" class="pad" style="padding:32px 40px 20px;background:#ffffff;">
        ${logoBlock}
      </td>
    </tr>

    <!-- ── GREEN ACCENT ── -->
    <tr>
      <td style="background:linear-gradient(90deg,#00A651 0%,#7ed957 100%);height:4px;line-height:4px;font-size:0;">&nbsp;</td>
    </tr>

    <!-- ── GREETING ── -->
    <tr>
      <td class="pad" style="padding:28px 40px 12px;">
        <h1 class="h1" style="margin:0 0 10px;font-size:22px;font-weight:700;color:#1a1a1a;line-height:1.3;">
          Xin chào ${esc(name)},
        </h1>
        <p style="margin:0;font-size:15px;color:#555;line-height:1.75;">
          Chúng tôi đã nhận được yêu cầu tư vấn của bạn từ website
          <a href="https://omega.com.vn" style="color:#00A651;text-decoration:none;font-weight:600;">omega.com.vn</a>.
          Cảm ơn bạn đã tin tưởng và liên hệ với <strong>OMEGA ERP</strong>!
        </p>
      </td>
    </tr>

    <!-- ── INFO BOX ── -->
    <tr>
      <td class="pad" style="padding:8px 40px 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
               style="background:#f6fbf7;border:1px solid #c8e6d0;border-radius:10px;border-collapse:collapse;">
          <tr>
            <td class="box" style="padding:16px;">
              <p style="margin:0 0 12px;font-size:11px;font-weight:700;color:#00A651;
                         text-transform:uppercase;letter-spacing:1.5px;">
                Thông tin bạn đã gửi
              </p>
              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr><td style="padding:3px 0;font-size:14px;color:#333;">
                  <strong>Họ và tên:</strong>&nbsp; ${esc(name)}
                </td></tr>
                ${companyRow}
                <tr><td style="padding:3px 0;font-size:14px;color:#333;">
                  <strong>Ngành nghề:</strong>&nbsp; ${esc(industryLabel)}
                </td></tr>
                <tr><td style="padding:3px 0;font-size:14px;color:#333;">
                  <strong>Điện thoại:</strong>&nbsp; ${esc(phone)}
                </td></tr>
                <tr><td style="padding:3px 0;font-size:14px;color:#333;">
                  <strong>Email:</strong>&nbsp; ${esc(email)}
                </td></tr>
                <tr><td style="padding:10px 0 4px;">
                  <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:#00A651;
                             text-transform:uppercase;letter-spacing:1.5px;">Nội dung tư vấn</p>
                  <p style="margin:0;font-size:14px;color:#444;line-height:1.75;font-style:italic;
                             border-left:3px solid #00A651;padding-left:12px;">
                    &ldquo;${esc(excerpt)}&rdquo;
                  </p>
                </td></tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- ── COMMITMENT ── -->
    <tr>
      <td class="pad" style="padding:0 40px 24px;">
        <p style="margin:0;font-size:15px;color:#444;line-height:1.75;">
          Chuyên gia OMEGA sẽ liên hệ lại với bạn
          <strong style="color:#00A651;">trong vòng 2 giờ làm việc</strong>
          để trao đổi và tư vấn giải pháp ERP phù hợp nhất với đặc thù doanh nghiệp của bạn.
        </p>
      </td>
    </tr>

    <!-- ── OMEGA INTRO (dark block) ── -->
    <tr>
      <td class="pad" style="padding:0 40px 32px;">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
               style="background:#1a3a2a;border-radius:10px;border-collapse:collapse;">
          <tr>
            <td class="dark-box" style="padding:20px 24px;">
              <p style="margin:0 0 10px;font-size:11px;font-weight:700;color:#7ed957;
                         text-transform:uppercase;letter-spacing:1.5px;">Về OMEGA ERP</p>
              <p style="margin:0;font-size:14px;color:rgba(255,255,255,0.88);line-height:1.8;">
                OMEGA ERP là phần mềm quản trị doanh nghiệp toàn diện với hơn
                <strong style="color:#7ed957;">16 năm kinh nghiệm</strong> triển khai cho
                <strong style="color:#7ed957;">1.000+ doanh nghiệp</strong> sản xuất và phân phối
                tại Việt Nam. Bộ sản phẩm gồm <strong style="color:#7ed957;">15 phân hệ ERP</strong>,
                3 giải pháp chuyên sâu và 6 Mobile App — tích hợp liền mạch, tối ưu cho đặc thù
                doanh nghiệp Việt.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- ── SIGNATURE ── -->
    <tr>
      <td class="pad" style="padding:0 40px 28px;border-top:1px solid #eee;">
        <p style="margin:20px 0 4px;font-size:14px;color:#666;">Trân trọng,</p>
        <p style="margin:0 0 2px;font-size:15px;font-weight:700;color:#1a1a1a;">
          Đội ngũ Tư vấn OMEGA ERP
        </p>
        <p style="margin:0;font-size:13px;color:#888;line-height:1.8;">
          Tel: <a href="tel:+84835128448" style="color:#00A651;text-decoration:none;">(84-8) 3512 8448</a>
          &nbsp;|&nbsp;
          Web: <a href="https://omega.com.vn" style="color:#00A651;text-decoration:none;">omega.com.vn</a>
        </p>
        ${sigBlock}
      </td>
    </tr>

    <!-- ── FOOTER ── -->
    <tr>
      <td class="pad" style="background:#f6f9f6;padding:16px 40px;border-top:1px solid #e8eee8;">
        <p style="margin:0;font-size:11px;color:#aaa;text-align:center;line-height:1.7;">
          Email này được gửi tự động khi bạn điền form tại
          <a href="https://omega.com.vn/lien-he.html" style="color:#00A651;">omega.com.vn/lien-he.html</a>.<br>
          Cần hỗ trợ: gọi <a href="tel:+84835128448" style="color:#00A651;">(84-8) 3512 8448</a>.
        </p>
      </td>
    </tr>

  </table>
  </td></tr>
</table>

</body>
</html>`;
}

// ============================================================
// HELPERS
// ============================================================

/** Map value → nhãn hiển thị tiếng Việt */
function resolveIndustryLabel(value) {
  const map = {
    'nganh-nhua':        'Ngành nhựa',
    'nganh-go-noi-that': 'Ngành gỗ – Nội thất',
    'nganh-fb':          'Ngành F&B (Thực phẩm & Đồ uống)',
    'nganh-fmcg':        'Ngành FMCG (Hàng tiêu dùng nhanh)',
    'nganh-thuy-san':    'Ngành thủy – hải sản',
    'nganh-y-te':        'Thiết bị y tế',
    'nganh-thoi-trang':  'Ngành thời trang',
    'nganh-phan-phoi':   'Phân phối – Bán sỉ',
    'nganh-khac':        'Ngành khác',
  };
  return map[value] || (value || 'Chưa chọn');
}

/** Escape HTML entities cho nội dung người dùng nhập */
function esc(str) {
  return String(str || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

/** Trả về JSON response */
function jsonResponse(obj) {
  return ContentService
    .createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}
