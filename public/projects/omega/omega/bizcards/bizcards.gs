// https://script.google.com/macros/s/AKfycbyHjEDp1PH7GkVa1tP497QILJh14XGb0i71TRAJesGZQ6phyPC_yX6fUq54zSQqZ-BCdg/exec
const SS = SpreadsheetApp.getActiveSpreadsheet();

// --- 1. CORE HANDLERS ---
function doGet(e) {
  const action = e.parameter.action;
  if (action === 'getData') {
    return createResponse(getAllData());
  }
  if (action === 'getHistory') {
    const sessionId = e.parameter.sessionId;
    if (!sessionId) return createResponse({ error: 'Missing sessionId' });
    return createResponse({ history: getHistoryBySession(sessionId) });
  }
  return createResponse({ error: 'Invalid action' });
}

function doPost(e) {
  const lock = LockService.getScriptLock();
  // Tăng thời gian chờ lock để tránh lỗi khi nhiều request
  if (!lock.tryLock(30000)) { 
    return createResponse({ success: false, message: "Hệ thống đang bận, vui lòng thử lại." });
  }

  try {
    const data = JSON.parse(e.postData.contents);
    const action = data.action;

    if (action === 'submitForm') {
      return handleFormSubmission(data);
    } else if (action === 'chat') {
      return handleChat(data);
    }
    return createResponse({ error: 'Invalid action' });

  } catch (err) {
    return createResponse({ error: err.toString() });
  } finally {
    lock.releaseLock();
  }
}

function createResponse(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}

// --- 2. DATA MANAGEMENT ---
function getAllData() {
  const configSheet = SS.getSheetByName('Config');
  const bizSheet = SS.getSheetByName('Bizcard');
  
  // Lấy dữ liệu động dựa trên số dòng thực tế
  // Sử dụng getLastRow() để lấy đúng vùng dữ liệu có thật
  const lastRowConfig = configSheet.getLastRow();
  const lastRowBiz = bizSheet.getLastRow();

  // Kiểm tra nếu sheet trống để tránh lỗi getRange
  const configValues = lastRowConfig > 1 ? configSheet.getRange(2, 1, lastRowConfig - 1, 2).getValues() : [];
  const bizValues = lastRowBiz > 1 ? bizSheet.getRange(2, 1, lastRowBiz - 1, 2).getValues() : [];
  
  const config = {};
  configValues.forEach(row => { if(row[0]) config[row[0]] = row[1] });
  
  const biz = {};
  bizValues.forEach(row => { if(row[0]) biz[row[0]] = row[1] });
  
  // --- PHẦN CODE MỚI ĐƯỢC TÍCH HỢP ---
  // Tự động tạo danh sách link từ A57 đến A91
  // Logic: Tạo mảng các key -> Map sang giá trị -> Lọc bỏ rỗng -> Join lại
  const startKeyIndex = 57;
  const endKeyIndex = 91;
  
  biz.webLinksContext = Array.from({ length: endKeyIndex - startKeyIndex + 1 }, (_, i) => {
    const key = 'A' + (startKeyIndex + i); // Tạo key: A57, A58...
    return biz[key]; // Lấy giá trị từ object biz
  })
  .filter(val => val && val.toString().trim() !== "") // Lọc bỏ null, undefined, chuỗi rỗng
  .join('\n'); // Nối lại xuống dòng
  // ------------------------------------

  return { config, biz };
}

// --- 3. LOGIC XỬ LÝ FORM & CHAT ĐẦU TIÊN ---
function handleFormSubmission(payload) {
  const dataSheet = SS.getSheetByName('Data');
  const timestamp = new Date();
  
  // Ghi vào Sheet Data
  dataSheet.appendRow([
    timestamp,
    payload.name,
    payload.company,
    payload.phone,
    payload.email,
    payload.message,
    payload.chat // Boolean
  ]);

  const allData = getAllData();
  const biz = allData.biz;
  const config = allData.config;

 // Gửi Email thông báo (nếu có email)
  try {
    if (payload.email) {
      // MailApp.sendEmail(
      //   payload.email, 
      //   `Xác nhận thông tin từ ${bizData.bizcardPersonName}`, 
      //   `Chào ${payload.name},\n\nCảm ơn bạn đã quan tâm. Tôi sẽ phản hồi sớm nhất.\n\nTrân trọng,\n${bizData.bizcardPersonName}`
      // );
      GmailApp.sendEmail(
        payload.email,
        // SỬA 1: Tiêu đề cảm ơn TÊN KHÁCH HÀNG
        `Cám ơn ${payload.name} đã liên hệ với Mr. Nguyễn Văn Nhân - OMEGA ERP!`,
        '', // Nội dung thuần (để trống vì dùng htmlBody)
        {
          htmlBody: `
              <p><i>Kính gửi:</i> <b>Quý khách ${payload.name}</b>,</p> 
              <p>Cảm ơn bạn đã liên hệ với OMEGA. Chúng tôi đã nhận được tin nhắn của bạn từ Danh thiếp điện tử của Mr. Nhân.</p>
              
              <p><b><u>Nội dung bạn đã gửi</u>:</b></p>
              <blockquote style="margin-left: 20px; color: #555; border-left: 4px solid #187752; padding-left: 10px;">
                ${payload.message}
              </blockquote>

              <p>Chúng tôi sẽ phản hồi sớm nhất có thể. Nếu bạn cần hỗ trợ ngay lập tức, vui lòng liên hệ:</p>
              <ul>
                <li>Email: <a href="mailto:info@omega.com.vn">info@omega.com.vn</a></li>
                <li>SĐT: <b>+84-908303609</b> (<a href="https://wa.me/84908303609" target="_blank">WhatsApp</a>, <a href="http://zalo.me/0908303609" target="_blank">Zalo</a>)</li>
              </ul>

              <p>Trân trọng,</p>
              <p><b>${biz.bizcardPersonName.toUpperCase()}</b></p> 
              <a href="https://omega.com.vn">
                <img src="${biz.bizcardImageFront}" alt="Signature" style="width: 100%; max-width: 500px; margin-top: 10px; border-radius: 12px">
              <a>
            `,
          bcc: 'nhan.nguyen@omega.com.vn',
          // from: 'info@omega.com.vn', // Chỉ mở dòng này nếu Gmail của bạn ĐÃ CẤU HÌNH ALIAS
          name: `Nguyễn Văn Nhân - ${biz.bizcardCompanyName}`, // Tên người gửi hiển thị
          replyTo: 'info@omega.com.vn'
        }
      );
    }
  } catch (e) {
    console.log("Email error: " + e.message);
    // Không return lỗi ở đây để code vẫn chạy tiếp xuống phần Chatbot
  }

  let botResponse = null;

  // XỬ LÝ THÔNG MINH: Nếu user chọn Chat + Có nhập Message -> Gọi API trả lời luôn
  if (payload.chat === true && config.chatbotValidity === true) {
    if (payload.message && payload.message.trim() !== "") {
        // Coi form message là tin nhắn đầu tiên của user
        const firstUserMessage = `[Khách hàng ${payload.name} từ ${payload.company}]: ${payload.message}`;
        // Gọi hàm xử lý chat nội bộ
        botResponse = processLLMRequest(config, biz, [{role: 'user', content: firstUserMessage}]);
    } else {
        // Nếu không có message, dùng câu chào mặc định nhưng vẫn cá nhân hóa
        botResponse = `Dạ em chào anh/chị ${payload.name} ạ! Em là ${biz.chatbotName}, ${biz.chatbotPosition} của anh ${biz.bizcardPersonName}. Em có thể hỗ trợ thông tin gì về OMEGA cho mình ạ?`;
    }
  }

  // Lưu lịch sử chat vào sheet
  if (payload.sessionId) {
    if (payload.message && payload.message.trim()) {
      saveChatRecord(payload.sessionId, payload.cardSlug, payload.email, payload.name, payload.company, 'user', payload.message);
    }
    if (botResponse) {
      saveChatRecord(payload.sessionId, payload.cardSlug, payload.email, payload.name, payload.company, 'assistant', botResponse);
    }
  }

  return createResponse({ success: true, botResponse: botResponse });
}

// --- 4. LOGIC CHATBOT ---
function handleChat(payload) {
  const data = getAllData();
  const config = data.config;
  const biz = data.biz;

  if (!config.chatbotValidity) {
    return createResponse({ response: "Hệ thống Chatbot đang tạm ngưng phục vụ." });
  }

  const responseText = processLLMRequest(config, biz, payload.messages);

  // Lưu tin nhắn mới nhất + phản hồi vào ChatHistory sheet
  if (payload.sessionId) {
    const msgs = payload.messages || [];
    const lastUser = msgs.length > 0 ? msgs[msgs.length - 1] : null;
    if (lastUser && lastUser.role === 'user') {
      saveChatRecord(payload.sessionId, payload.cardSlug, payload.userEmail, payload.userName, '', 'user', lastUser.content);
    }
    saveChatRecord(payload.sessionId, payload.cardSlug, payload.userEmail, payload.userName, '', 'assistant', responseText);
  }

  return createResponse({ response: responseText });
}

// Hàm chung để build prompt và gọi API
function processLLMRequest(config, biz, messagesHistory) {
  // Xây dựng System Prompt cực kỹ
  const systemPrompt = `
    BẠN LÀ: ${biz.chatbotName}, ${biz.chatbotPosition} của ${biz.bizcardCompanyName} (OMEGA ERP).
    NGƯỜI QUẢN LÝ CỦA BẠN: Ông ${biz.bizcardPersonName} (${biz.bizcardPersonPosition}).

    PHONG CÁCH GIAO TIẾP (BẮT BUỘC):
    - Bạn là nữ, thư ký chuyên nghiệp nhưng dịu dàng, khéo léo.
    - Luôn xưng "em" và gọi người chat là "Quý khách" hoặc "anh/chị".
    - Tuyệt đối lịch sự, ân cần, "dạ/vâng" đầy đủ.
    - ${biz.chatBotGuide}

    THÔNG TIN VỀ ÔNG ${biz.bizcardPersonName}:
    ${biz.personProfile}

    DỊCH VỤ CỦA CÔNG TY ${biz.bizcardCompanyName}:
    1. ${biz.bizcardBusiness1}
    2. ${biz.bizcardBusiness2}
    3. ${biz.bizcardBusiness3}
    4. ${biz.bizcardBusiness4}
    5. ${biz.bizcardBusiness5}
    6. ${biz.bizcardBusiness6}
    7. ${biz.bizcardBusiness7}
    8. ${biz.bizcardBusiness8}

    CÁC LIÊN KẾT WEBSITE QUAN TRỌNG (Dùng để dẫn chứng):
    ${biz.webLinksContext}

    QUY TẮC TRẢ LỜI:
    - Bám sát câu hỏi, trả lời ngắn gọn 1 câu, đúng trọng tâm, chỉ khi yêu cầu giải thích/ chi tiết mới nói dài (không quá 5 câu).
    - Luôn bám sát chủ đề nhằm tạo lead kinh doanh, không để khách hỏi lan man, cần điều hướng về mục ${biz.surveyQuestion}
    - Nếu câu hỏi liên quan đến dịch vụ cụ thể, hãy cung cấp link website tương ứng trong câu trả lời.
    - Nếu không biết thông tin, hãy khéo léo xin số điện thoại để anh ${biz.bizcardPersonName} liên hệ lại, hoặc mời xem thêm tại ${biz['Web Trang chủ']}.
    - Trong trường hợp không thể tìm kiếm câu trả lời hoặc quá nhạy cảm, khó quyết định, hãy khéo léo trả lời đã ghi nhận thông tin để báo cáo Giám đốc.
    - Với các thông tin đã được cung cấp, gồm thông tin cá nhân như email và số điện thoại ... của ${biz.bizcardPersonName}, bạn vẫn có thể cung cấp nếu câu hỏi liên quan đến business.
    - Sử dụng Markdown (in đậm **, gạch đầu dòng) để trình bày đẹp.
  `;

  const model = config.llmModel;
  const apiKey = config.llmAPI;

  if (!apiKey) return "Lỗi: Chưa cấu hình API Key.";

  try {
    if (model === 'Gemini') {
      return callGeminiAPI(apiKey, systemPrompt, messagesHistory);
    } else if (model === 'DeepSeek') {
      return callDeepSeekAPI(apiKey, systemPrompt, messagesHistory);
    } else if (model === 'Grok') {
      return callGrokAPI(apiKey, systemPrompt, messagesHistory);
    } else {
      return `Model ${model} chưa được hỗ trợ.`;
    }
  } catch (err) {
    return `Xin lỗi, em đang gặp chút trục trặc kỹ thuật (${err.message}). Anh/chị chờ em một lát nhé.`;
  }
}

// --- 5. API CALLS ---

function callGeminiAPI(key, system, history) {
  const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=${key}`;
  
  // Convert history to text block for Gemini (Context caching style)
  const historyText = history.map(m => `${m.role === 'user' ? 'Khách' : 'Em'}: ${m.content}`).join('\n');
  const fullPrompt = `${system}\n\n=== LỊCH SỬ TRÒ CHUYỆN ===\n${historyText}\n\nEm trả lời:`;

  const payload = {
    contents: [{ parts: [{ text: fullPrompt }] }],
    generationConfig: { temperature: 0.7, maxOutputTokens: 1000 }
  };

  const options = {
    method: 'post',
    contentType: 'application/json',
    payload: JSON.stringify(payload),
    muteHttpExceptions: true
  };

  const response = UrlFetchApp.fetch(url, options);
  const json = JSON.parse(response.getContentText());

  if (json.candidates && json.candidates[0].content) {
    return json.candidates[0].content.parts[0].text;
  }
  return "Em chưa hiểu ý mình lắm, anh/chị nói lại giúp em nhé.";
}

function callDeepSeekAPI(key, system, history) {
  const url = 'https://api.deepseek.com/chat/completions';
  
  const messages = [{ role: "system", content: system }, ...history];

  const payload = {
    model: "deepseek-chat",
    messages: messages,
    temperature: 0.7
  };

  const options = {
    method: 'post',
    headers: { 
      "Authorization": "Bearer " + key,
      "Content-Type": "application/json"
    },
    payload: JSON.stringify(payload),
    muteHttpExceptions: true
  };

  const response = UrlFetchApp.fetch(url, options);
  const json = JSON.parse(response.getContentText());
  
  if (json.choices && json.choices.length > 0) {
     return json.choices[0].message.content;
  }
  return "Kết nối DeepSeek bị gián đoạn.";
}

function callGrokAPI(key, system, history) {
  const url = 'https://api.x.ai/v1/chat/completions';
  const modelId = "grok-4-latest"; // Hoặc "grok-beta"

  const messages = [{ role: "system", content: system }, ...history];

  const payload = {
    model: modelId,
    messages: messages,
    temperature: 0.7
  };

  const options = {
    method: 'post',
    headers: { 
      "Authorization": "Bearer " + key,
      "Content-Type": "application/json"
    },
    payload: JSON.stringify(payload),
    muteHttpExceptions: true
  };

  const response = UrlFetchApp.fetch(url, options);
  const json = JSON.parse(response.getContentText());

  if (json.choices && json.choices.length > 0) {
    return json.choices[0].message.content;
  }
  return "Kết nối Grok bị gián đoạn.";
}


// --- 7. CHAT HISTORY PERSISTENCE ---
function getChatHistorySheet() {
  let sheet = SS.getSheetByName('ChatHistory');
  if (!sheet) {
    sheet = SS.insertSheet('ChatHistory');
    sheet.appendRow(['SessionID', 'Timestamp', 'CardSlug', 'UserEmail', 'UserName', 'Company', 'Role', 'Message']);
    sheet.setFrozenRows(1);
    sheet.setColumnWidth(1, 200);
    sheet.setColumnWidth(8, 500);
  }
  return sheet;
}

function saveChatRecord(sessionId, cardSlug, userEmail, userName, company, role, message) {
  if (!sessionId || !message) return;
  const sheet = getChatHistorySheet();
  sheet.appendRow([
    sessionId,
    new Date(),
    cardSlug  || '',
    userEmail || '',
    userName  || '',
    company   || '',
    role,
    message
  ]);
}

function getHistoryBySession(sessionId) {
  const sheet = getChatHistorySheet();
  const data = sheet.getDataRange().getValues();
  if (data.length <= 1) return [];
  return data.slice(1)
    .filter(r => r[0] === sessionId)
    .map(r => ({ timestamp: r[1], role: r[6], message: r[7] }));
}

// --- 6. HÀM TEST QUYỀN (CHẠY THỦ CÔNG) ---
function testEmailPermission() {
  // Tự động lấy email của người đang chạy script (là bạn)
  const myEmail = Session.getActiveUser().getEmail();
  
  console.log("Đang bắt đầu test gửi mail tới: " + myEmail);

  try {
    MailApp.sendEmail({
      to: myEmail,
      subject: "Test Quyền gửi Email từ Apps Script",
      htmlBody: `
        <h3>Chúc mừng! Quyền gửi email đã hoạt động.</h3>
        <p>Đây là email test để xác nhận script của bạn đã được cấp phép gửi mail.</p>
        <p>Thời gian: ${new Date()}</p>
      `,
      name: "OMEGA ERP Test System",
      replyTo: myEmail
    });
    
    console.log("✅ Gửi mail thành công! Hệ thống đã có đủ quyền.");
    console.log("Bây giờ bạn có thể Deploy lại Web App.");
    
  } catch (e) {
    console.error("❌ LỖI: " + e.toString());
    console.log("Vui lòng kiểm tra lại xem bạn đã bấm 'Review Permissions' và 'Allow' chưa.");
  }
}