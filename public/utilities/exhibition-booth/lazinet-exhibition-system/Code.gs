/**
 * LAZINET EXHIBITION SYSTEM - CORE BACKEND
 * Setup:
 * 1. Tạo Google Sheet với 3 sheet: "Configs", "Organizer", "Booths"
 * 2. Deploy as Web App -> Execute as: Me -> Access: Anyone
 * 3. Copy Web App URL dán vào biến API_URL trong 2 file HTML
 */

const SS = SpreadsheetApp.getActiveSpreadsheet();

function doPost(e) {
  const lock = LockService.getScriptLock();
  lock.tryLock(15000); 
  
  try {
    const request = JSON.parse(e.postData.contents);
    const action = request.action;
    const configSh = SS.getSheetByName('Configs');

    // Bypass check active nếu là admin đang login
    const isActive = configSh.getRange('B2').getValue();
    if (isActive !== true && action !== 'checkPass' && action !== 'saveFullData') {
      return response({ success: false, message: configSh.getRange('B3').getValue() || "Hệ thống đang bảo trì." });
    }

    switch(action) {
      case 'getInitData': 
        return response(getInitData());
        
      case 'checkPass': 
        const adminPass = configSh.getRange('B4').getValue();
        // So sánh string để tránh lỗi định dạng
        return response({ valid: String(request.pass).trim() === String(adminPass).trim() });
        
      case 'saveFullData': 
        return response(saveFullData(request));
        
      case 'bookBooth': 
        return response(bookBooth(request));
        
      case 'chatAI': 
        return response(handleChatAI(request.message));
        
      default: 
        return response({ success: false, message: "Action invalid" });
    }
  } catch (err) {
    return response({ success: false, message: "Error: " + err.toString() });
  } finally {
    lock.releaseLock();
  }
}

// Xử lý CORS cho fetch từ client
function doGet(e) {
  return ContentService.createTextOutput("Backend Online. Use POST method.").setMimeType(ContentService.MimeType.TEXT);
}

function getInitData() {
  const configSh = SS.getSheetByName('Configs');
  const orgSh = SS.getSheetByName('Organizer');
  const boothSh = SS.getSheetByName('Booths');

  const configs = {
    botName: configSh.getRange('B8').getValue(),
    botLogo: configSh.getRange('B9').getValue(),
    themeColor1: configSh.getRange('B10').getValue(),
    themeColor2: configSh.getRange('B11').getValue(),
    waitingMsg: configSh.getRange('B13').getValue()
  };

  const orgRaw = orgSh.getRange('B2:B22').getValues();
  // Map dữ liệu từ sheet Organizer theo đúng thứ tự row
  const organizer = {
    shortName: orgRaw[0][0],
    fullName: orgRaw[1][0],
    logo: orgRaw[3][0],
    title: orgRaw[11][0] || "Exhibition Title", // B13
    location: orgRaw[12][0], // B14
    address: orgRaw[13][0], // B15
    width: Number(orgRaw[15][0]) || 2000, // B17
    height: Number(orgRaw[16][0]) || 1500, // B18
    ratio: Number(orgRaw[17][0]) || 1
  };

  // Lấy danh sách Booths
  const bData = boothSh.getDataRange().getValues();
  const headers = bData[0].map(h => String(h).trim());
  
  // Mapping columns linh hoạt
  const col = (name) => headers.indexOf(name);
  
  const booths = bData.slice(1).map(row => ({
    id: row[col('Booth ID')],
    x: Number(row[col('X')]),
    y: Number(row[col('Y')]),
    width: Number(row[col('Width')]),
    height: Number(row[col('Height')]),
    status: row[col('Trạng thái')] || 'Available',
    rank: row[col('Hạng')],
    price: row[col('Giá (VND)')]
  }));

  return { success: true, configs, organizer, booths };
}

function saveFullData(req) {
  const orgSh = SS.getSheetByName('Organizer');
  const boothSh = SS.getSheetByName('Booths');
  const o = req.organizer;

  // Lưu Config Organizer
  orgSh.getRange('B13').setValue(o.title);
  orgSh.getRange('B14').setValue(o.location);
  orgSh.getRange('B17').setValue(o.width);
  orgSh.getRange('B18').setValue(o.height);

  // Lưu Booths
  // Logic: Giữ nguyên thông tin khách đặt (nếu có), chỉ cập nhật tọa độ/giá/trạng thái từ admin
  const oldData = boothSh.getDataRange().getValues();
  const headers = oldData[0];
  const idIdx = headers.indexOf('Booth ID');
  
  // Tạo map dữ liệu cũ để preserve booking info
  const oldMap = {};
  oldData.slice(1).forEach(row => {
    oldMap[row[idIdx]] = row;
  });

  // Xóa cũ, ghi mới header
  boothSh.clearContents();
  boothSh.appendRow(headers);

  const newRows = req.booths.map(b => {
    let row = oldMap[b.id];
    if (!row) {
      // Booth mới hoàn toàn
      row = new Array(headers.length).fill("");
      row[idIdx] = b.id;
    }
    
    // Cập nhật các cột cấu hình
    const setVal = (k, v) => { const i = headers.indexOf(k); if(i>-1) row[i] = v; };
    setVal('X', b.x);
    setVal('Y', b.y);
    setVal('Width', b.width);
    setVal('Height', b.height);
    setVal('Trạng thái', b.status);
    
    return row;
  });

  if(newRows.length > 0) {
    boothSh.getRange(2, 1, newRows.length, headers.length).setValues(newRows);
  }

  return { success: true, message: "Đã lưu dữ liệu thành công!" };
}

function bookBooth(req) {
  const sh = SS.getSheetByName('Booths');
  const data = sh.getDataRange().getValues();
  const headers = data[0].map(h => String(h).trim());
  const idIdx = headers.indexOf('Booth ID');
  const statusIdx = headers.indexOf('Trạng thái');

  for (let i = 1; i < data.length; i++) {
    if (String(data[i][idIdx]) === String(req.boothId)) {
      if (data[i][statusIdx] !== 'Available') return { success: false, message: "Gian hàng này vừa có người đặt!" };
      
      const row = i + 1;
      sh.getRange(row, statusIdx + 1).setValue('Booked');
      
      // Ghi thông tin khách (Cần đảm bảo sheet có các cột này)
      const setCol = (name, val) => {
        const idx = headers.indexOf(name);
        if (idx > -1) sh.getRange(row, idx + 1).setValue(val);
      };
      
      setCol('Tên công ty ngắn gọn', req.info.compShort);
      setCol('Tên người đặt', req.info.name);
      setCol('SĐT người đặt', req.info.phone);
      setCol('Thời gian đặt', new Date());

      return { success: true, message: "Đặt chỗ thành công! Vui lòng chờ xác nhận." };
    }
  }
  return { success: false, message: "Không tìm thấy gian hàng." };
}

function handleChatAI(userMsg) {
  const cfg = SS.getSheetByName('Configs');
  const model = cfg.getRange('B5').getValue(); // "Gemini"
  const apiKey = cfg.getRange('B6').getValue();
  const systemPrompt = cfg.getRange('B14').getValue();
  
  // Lấy dữ liệu booth trống để đưa vào context
  const bData = SS.getSheetByName('Booths').getDataRange().getValues();
  const header = bData[0];
  const statusIdx = header.indexOf('Trạng thái');
  const idIdx = header.indexOf('Booth ID');
  const available = bData.slice(1).filter(r => r[statusIdx] === 'Available').map(r => r[idIdx]).slice(0, 20).join(', ');
  
  const fullContext = `System: ${systemPrompt}. \nReal-time Data: Các booth còn trống: ${available}. \nUser Question: ${userMsg}`;

  try {
    if (model === "Gemini" && apiKey) {
      const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=${apiKey}`;
      const payload = { contents: [{ parts: [{ text: fullContext }] }] };
      const options = {
        method: 'post',
        contentType: 'application/json',
        payload: JSON.stringify(payload),
        muteHttpExceptions: true
      };
      const res = UrlFetchApp.fetch(url, options);
      const json = JSON.parse(res.getContentText());
      
      if(json.error) return { answer: "Lỗi AI: " + json.error.message };
      return { answer: json.candidates[0].content.parts[0].text };
    }
    return { answer: "Chưa cấu hình API Key hoặc Model trong sheet Configs." };
  } catch (e) {
    return { answer: "Xin lỗi, hệ thống AI đang bận. (" + e.toString() + ")" };
  }
}

function response(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj)).setMimeType(ContentService.MimeType.JSON);
}