// =====================================================
// HATHYO.GS - Google Apps Script cho hệ thống HATHYO Bizcards
// Cấu trúc bám sát qtbc.gs (đã thành công)
// =====================================================
// HƯỚNG DẪN DEPLOY:
// 1. Upload hathyo.xlsx → mở bằng Google Sheets
// 2. Lấy Spreadsheet ID từ URL → điền vào SHEET_ID bên dưới
// 3. Extensions > Apps Script → dán toàn bộ code này
// 4. Deploy > New deployment > Web app
//    - Execute as: Me
//    - Who has access: Anyone
// 5. Copy URL deploy → dán vào hathyo.html dòng const GAS_URL
// =====================================================

const SHEET_ID = ''; // ← ĐIỀN SPREADSHEET ID VÀO ĐÂY

// Keys nhạy cảm, không trả về frontend
const PRIVATE_CONFIG_KEYS = ['LLM_Model', 'LLM_API_Key', 'LLM_Max_Token', 'LLM_Guide', 'App_Password'];

// =====================================================
// MAIN HANDLERS (giống qtbc.gs)
// =====================================================

function doGet(e) {
  const action   = e.parameter.action;
  const callback = e.parameter.callback; // JSONP support
  let result;

  try {
    if (action === 'list') {
      result = getListResponse();
    } else if (action === 'detail' || action === 'getMember') {
      const id = e.parameter.id || e.parameter.c;
      result = getDetailResponse(id);
    } else {
      result = { status: 'error', message: 'Invalid action' };
    }
  } catch (err) {
    result = { status: 'error', message: err.toString() };
  }

  const json = JSON.stringify(result);
  if (callback) {
    return ContentService.createTextOutput(callback + '(' + json + ')')
      .setMimeType(ContentService.MimeType.JAVASCRIPT);
  }
  return ContentService.createTextOutput(json)
    .setMimeType(ContentService.MimeType.JSON);
}

function doPost(e) {
  try {
    const data   = JSON.parse(e.postData.contents);
    const action = data.action;

    // Read-only actions — không cần lock
    if (action === 'list')   return createJsonResponse(getListResponse());
    if (action === 'detail') return createJsonResponse(getDetailResponse(data.c || data.id));

    // Write actions — dùng lock để tránh ghi đè đồng thời
    const lock = LockService.getScriptLock();
    if (!lock.tryLock(30000)) {
      return createJsonResponse({ success: false, message: 'Hệ thống đang bận, vui lòng thử lại.' });
    }
    try {
      if (action === 'chat')           return createJsonResponse(handleChat(data));
      if (action === 'submitContact')  return createJsonResponse(handleContact(data));
      if (action === 'verifyPassword') return createJsonResponse(handleVerifyPassword(data));
      return createJsonResponse({ success: false, error: 'Invalid action: ' + action });
    } finally {
      lock.releaseLock();
    }
  } catch (err) {
    return createJsonResponse({ success: false, error: err.toString() });
  }
}

function createJsonResponse(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}

// =====================================================
// DATA FUNCTIONS
// =====================================================

function getConfigData(configSheet) {
  if (!configSheet) {
    configSheet = SpreadsheetApp.openById(SHEET_ID).getSheetByName('Configs');
  }
  const data   = configSheet.getDataRange().getValues();
  const config = {};
  for (let i = 1; i < data.length; i++) {
    if (data[i][0]) config[data[i][0]] = data[i][1];
  }
  return config;
}

function getSafeConfigs(config) {
  const safe = {};
  for (const key in config) {
    if (!PRIVATE_CONFIG_KEYS.includes(key)) safe[key] = config[key];
  }
  return safe;
}

// Trả về danh sách nhân viên (chỉ trường cần cho listing) + configs
function getListResponse() {
  const ss      = SpreadsheetApp.openById(SHEET_ID);
  const sheet   = ss.getSheetByName('Bizcards');
  const configs = getConfigData(ss.getSheetByName('Configs'));
  const data    = sheet.getDataRange().getValues();
  // Row 1 = ghi chú mô tả, Row 2 = tên cột thật, Row 3+ = data
  const headers = data[1];
  const members = [];

  for (let i = 2; i < data.length; i++) {
    const row    = data[i];
    const member = {};
    for (let j = 0; j < headers.length; j++) {
      member[headers[j]] = row[j] !== undefined ? row[j] : '';
    }
    const visible = member['Visibility'];
    if (visible === true || visible === 'TRUE' || visible === 'true') {
      members.push({
        Member_ID:           member['Member_ID'],
        Member_Title:        member['Member_Title'],
        Member_Name:         member['Member_Name'],
        Member_Avatar_Small: member['Member_Avatar_Small'] || member['Member_Avatar'],
        Member_Position:     member['Member_Position'],
        Member_Department:   member['Member_Department'],
        Company_Name:        member['Company_Name'],
        Company_Short:       member['Company_Short']
      });
    }
  }

  return {
    status:  'success',
    configs: getSafeConfigs(configs),
    data:    { members: members, total_members: members.length }
  };
}

// Trả về đầy đủ thông tin 1 nhân viên + config
function getDetailResponse(memberId) {
  if (!memberId) return { status: 'error', message: 'Thiếu mã nhân viên' };
  const ss      = SpreadsheetApp.openById(SHEET_ID);
  const configs = getConfigData(ss.getSheetByName('Configs'));
  const member  = getMemberById(ss.getSheetByName('Bizcards'), memberId);
  if (!member) return { status: 'error', message: 'Không tìm thấy nhân viên: ' + memberId };
  return { status: 'success', member: member, configs: getSafeConfigs(configs) };
}

function getMemberById(sheet, memberId) {
  if (!sheet) sheet = SpreadsheetApp.openById(SHEET_ID).getSheetByName('Bizcards');
  const data    = sheet.getDataRange().getValues();
  // Row 1 = ghi chú mô tả, Row 2 = tên cột thật, Row 3+ = data
  const headers = data[1];
  for (let i = 2; i < data.length; i++) {
    const row    = data[i];
    const member = {};
    for (let j = 0; j < headers.length; j++) {
      member[headers[j]] = row[j] !== undefined ? row[j] : '';
    }
    if (String(member['Member_ID']).trim() === String(memberId).trim()) {
      return member;
    }
  }
  return null;
}

// =====================================================
// CHAT HANDLER (cấu trúc giống qtbc.gs)
// =====================================================

function handleChat(data) {
  const source    = data.source;
  const sessionId = data.sessionId || data.session_id || '';
  const messages  = data.messages;

  if (!source || !sessionId || !messages || messages.length === 0) {
    return { success: false, message: 'Thiếu thông tin chat.' };
  }

  const ss     = SpreadsheetApp.openById(SHEET_ID);
  const config = getConfigData(ss.getSheetByName('Configs'));

  const chatbotOn = config['Chatbot_Validity'];
  if (chatbotOn !== true && String(chatbotOn).toUpperCase() !== 'TRUE') {
    return { success: false, response: 'Chatbot đang tạm ngưng phục vụ.' };
  }

  const member = getMemberById(ss.getSheetByName('Bizcards'), source);
  if (!member) return { success: false, response: 'Không tìm thấy thông tin nhân viên.' };

  const systemPrompt = buildChatSystemPrompt(config, member);
  const lastMsg      = messages[messages.length - 1];
  if (lastMsg.role !== 'user') return { success: false, response: 'Tin nhắn không hợp lệ.' };

  let botResponse;
  try {
    botResponse = callLLM(config, systemPrompt, messages);
  } catch (err) {
    botResponse = 'Xin lỗi, em đang gặp sự cố kỹ thuật. Anh/chị vui lòng thử lại sau nhé.';
    console.error('LLM Error:', err.toString());
  }

  try {
    saveChatHistory(ss.getSheetByName('Chat-Data'), source, sessionId, lastMsg.content, botResponse);
  } catch (err) {
    console.error('Save chat error:', err.toString());
  }

  return { response: botResponse };
}

function buildChatSystemPrompt(config, member) {
  const assistantName = member['Assistant_Name'] || config['Chatbot_Name'] || 'Trợ lý HATHYO';
  const memberName    = member['Member_Name']    || '';
  const memberTitle   = member['Member_Title']   || 'Anh/Chị';
  const position      = member['Member_Position'] || '';
  const company       = member['Company_Name']   || 'HATHYO';
  const guide         = config['LLM_Guide']      || 'Chuyên nghiệp, ngắn gọn, thân thiện.';
  const maxToken      = parseInt(config['LLM_Max_Token']) || 300;

  // Lĩnh vực kinh doanh
  const businesses = [];
  for (let i = 1; i <= 6; i++) {
    const val = member['Business' + i];
    if (val) businesses.push('• ' + val);
  }

  return `BẠN LÀ: ${assistantName}, trợ lý ảo thân thiết của ${memberTitle} ${memberName} — ${position}, ${company}.

PHONG CÁCH GIAO TIẾP: ${guide}

THÔNG TIN VỀ ${memberName.toUpperCase()}:
${member['Member_Profile'] || member['Slogan'] || 'Thành viên đội ngũ ' + company + '.'}

LĨNH VỰC HOẠT ĐỘNG:
${businesses.join('\n') || '• Sức khỏe & lành mạnh\n• Thương mại điện tử'}

THÔNG TIN LIÊN HỆ:
- SĐT: ${member['Mobile'] || config['Company_Phone'] || 'N/A'}
- Email: ${member['Email'] || config['Company_Email'] || 'N/A'}
- Website: ${member['Website'] || config['Company_Website'] || 'N/A'}
- Địa chỉ: ${member['Company_Address'] || config['Company_Address'] || 'N/A'}

QUY TẮC TRẢ LỜI:
- Trả lời ngắn gọn, tối đa ${maxToken} tokens.
- Chỉ tư vấn về ${memberTitle} ${memberName} và ${company}.
- Nếu không biết, khéo léo đề nghị khách để lại thông tin để ${memberTitle} ${memberName} liên hệ lại.
- Dùng Markdown (** ** để in đậm, - gạch đầu dòng) khi cần thiết.`;
}

function callLLM(config, systemPrompt, messages) {
  const model    = config['LLM_Model'];
  const apiKey   = config['LLM_API_Key'];
  const maxToken = parseInt(config['LLM_Max_Token']) || 300;

  if (!apiKey) throw new Error('Chưa cấu hình API Key trong sheet Configs.');
  if (model === 'Gemini')   return callGeminiAPI(apiKey, systemPrompt, messages, maxToken);
  if (model === 'DeepSeek') return callDeepSeekAPI(apiKey, systemPrompt, messages, maxToken);
  if (model === 'Grok')     return callGrokAPI(apiKey, systemPrompt, messages, maxToken);
  throw new Error('Model ' + model + ' chưa được hỗ trợ. Dùng Grok / DeepSeek / Gemini.');
}

function callGeminiAPI(key, system, history, maxToken) {
  const url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' + key;
  const historyText = history.map(function(m) {
    return (m.role === 'user' ? 'Khách' : 'Trợ lý') + ': ' + m.content;
  }).join('\n');
  const fullPrompt = system + '\n\n=== LỊCH SỬ TRÒ CHUYỆN ===\n' + historyText + '\n\nTrợ lý:';
  const payload = {
    contents: [{ parts: [{ text: fullPrompt }] }],
    generationConfig: { temperature: 0.7, maxOutputTokens: maxToken }
  };
  const options = {
    method: 'post', contentType: 'application/json',
    payload: JSON.stringify(payload), muteHttpExceptions: true
  };
  const resp = UrlFetchApp.fetch(url, options);
  const json = JSON.parse(resp.getContentText());
  if (json.candidates && json.candidates[0] && json.candidates[0].content) {
    return json.candidates[0].content.parts[0].text;
  }
  return 'Em chưa hiểu ý anh/chị, anh/chị nói lại giúp em nhé.';
}

function callDeepSeekAPI(key, system, history, maxToken) {
  const url      = 'https://api.deepseek.com/chat/completions';
  const messages = [{ role: 'system', content: system }].concat(history);
  const payload  = { model: 'deepseek-chat', messages: messages, temperature: 0.7, max_tokens: maxToken };
  const options  = {
    method: 'post',
    headers: { 'Authorization': 'Bearer ' + key, 'Content-Type': 'application/json' },
    payload: JSON.stringify(payload), muteHttpExceptions: true
  };
  const resp = UrlFetchApp.fetch(url, options);
  const json = JSON.parse(resp.getContentText());
  if (json.choices && json.choices.length > 0) return json.choices[0].message.content;
  return 'Kết nối DeepSeek bị gián đoạn.';
}

function callGrokAPI(key, system, history, maxToken) {
  const url      = 'https://api.x.ai/v1/chat/completions';
  const messages = [{ role: 'system', content: system }].concat(history);
  const payload  = { model: 'grok-4-latest', messages: messages, temperature: 0.7, max_tokens: maxToken };
  const options  = {
    method: 'post',
    headers: { 'Authorization': 'Bearer ' + key, 'Content-Type': 'application/json' },
    payload: JSON.stringify(payload), muteHttpExceptions: true
  };
  const resp = UrlFetchApp.fetch(url, options);
  const json = JSON.parse(resp.getContentText());
  if (json.choices && json.choices.length > 0) return json.choices[0].message.content;
  return 'Kết nối Grok bị gián đoạn.';
}

// Lưu lịch sử chat vào sheet Chat-Data (ngang: mỗi row = 1 session)
function saveChatHistory(sheet, source, sessionId, userMsg, botMsg) {
  if (!sheet) sheet = SpreadsheetApp.openById(SHEET_ID).getSheetByName('Chat-Data');
  const data = sheet.getDataRange().getValues();
  let rowIndex = -1;

  for (let i = 1; i < data.length; i++) {
    if (String(data[i][0]).trim() === String(source).trim() &&
        String(data[i][1]).trim() === String(sessionId).trim()) {
      rowIndex = i + 1;
      break;
    }
  }

  if (rowIndex === -1) {
    sheet.appendRow([source, sessionId, new Date(), userMsg, botMsg]);
  } else {
    const lastCol  = sheet.getLastColumn();
    const rowData  = sheet.getRange(rowIndex, 1, 1, lastCol).getValues()[0];
    let lastFilled = 1;
    for (let j = 0; j < rowData.length; j++) {
      if (rowData[j] !== '' && rowData[j] !== null && rowData[j] !== undefined) {
        lastFilled = j + 1;
      }
    }
    sheet.getRange(rowIndex, lastFilled + 1).setValue(userMsg);
    sheet.getRange(rowIndex, lastFilled + 2).setValue(botMsg);
  }
}

// =====================================================
// PASSWORD & FORM HANDLERS
// =====================================================

function handleVerifyPassword(data) {
  const config = getConfigData();
  const master = String(config['App_Password'] || '');
  if (!master) return { success: false, message: 'Chưa cấu hình mật khẩu (App_Password) trong sheet Configs.' };
  if (String(data.password || '').trim() === master) return { success: true };
  return { success: false, message: 'Mật khẩu không đúng.' };
}

function handleContact(data) {
  const source    = data.source;
  const sessionId = data.sessionId || data.session_id || '';
  const name      = data.name;
  const purpose   = data.purpose;
  const mobile    = data.mobile;
  const email     = data.email;
  const title     = data.title;
  const message   = data.message;

  if (!source || !name) {
    return { success: false, message: 'Vui lòng điền đầy đủ thông tin bắt buộc.' };
  }
  try {
    const ss          = SpreadsheetApp.openById(SHEET_ID);
    const contactSheet = ss.getSheetByName('Contact-Data');
    contactSheet.appendRow([
      source, sessionId || '', new Date(),
      name || '', purpose || '', mobile || '', email || '', title || '', message || '',
      'Mới'
    ]);

    // Gửi email xác nhận nếu được bật
    const config    = getConfigData(ss.getSheetByName('Configs'));
    const sendEmail = config['Confirm_Email_Active'];
    if ((sendEmail === true || String(sendEmail).toUpperCase() === 'TRUE') && email) {
      try { sendConfirmEmail(config, name, email, purpose, message); }
      catch (emailErr) { console.error('Email lỗi:', emailErr.toString()); }
    }

    return { success: true, message: 'Đã gửi thành công! Chúng tôi sẽ liên hệ bạn sớm.' };
  } catch (err) {
    return { success: false, message: 'Lỗi lưu dữ liệu: ' + err.toString() };
  }
}

function sendConfirmEmail(config, toName, toEmail, purpose, message) {
  const company = String(config['Company_Name'] || 'HATHYO').split(' - ')[0];
  const subject = '[' + company + '] Xác nhận nhận được liên hệ của bạn';
  const body    =
    'Kính gửi ' + toName + ',\n\n' +
    'Cảm ơn bạn đã liên hệ với chúng tôi!\n\n' +
    'Chúng tôi đã nhận được yêu cầu:\n' +
    '- Mục đích: ' + (purpose || 'Liên hệ chung') + '\n' +
    '- Nội dung: ' + (message || '') + '\n\n' +
    'Đội ngũ ' + company + ' sẽ phản hồi trong thời gian sớm nhất.\n\n' +
    'Trân trọng,\n' + company + '\n' +
    'Website: ' + (config['Company_Website'] || 'https://hathyo.com') + '\n' +
    'Điện thoại: ' + (config['Company_Phone'] || '');
  GmailApp.sendEmail(toEmail, subject, body, { name: company });
}

// =====================================================
// TEST FUNCTIONS (chạy thủ công từ Apps Script editor)
// =====================================================

function testGetList() {
  const result = getListResponse();
  console.log('Total members:', result.data.total_members);
  console.log('First member:', JSON.stringify(result.data.members[0]));
}

function testGetDetail() {
  const result = getDetailResponse('phunghm');
  console.log('Status:', result.status);
  console.log('Member:', JSON.stringify(result.member).substring(0, 500));
}

function testVerifyPassword() {
  const result = handleVerifyPassword({ password: 'hathyo2026' });
  console.log('Auth result:', JSON.stringify(result));
}

function testSaveChat() {
  const ss = SpreadsheetApp.openById(SHEET_ID);
  saveChatHistory(ss.getSheetByName('Chat-Data'), 'phunghm', 'test_001', 'Xin chào!', 'Chào bạn, HATHYO có thể giúp gì ạ?');
  console.log('Chat saved OK');
}

function testSaveContact() {
  const result = handleContact({
    source: 'phunghm', sessionId: 'test_001',
    name: 'Nguyễn Văn A', purpose: 'Hợp tác',
    mobile: '0900000000', email: 'test@test.com',
    title: 'Test', message: 'Test message'
  });
  console.log('Contact result:', JSON.stringify(result));
}
