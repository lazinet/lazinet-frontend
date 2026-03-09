const xlsx = require("xlsx");
const fs = require("fs");

// Bước 1: Đọc file Excel
const workbook = xlsx.readFile(
    "Final_Danh-muc-Phuong-xa_moi.xlsx"
);
const sheetName = workbook.SheetNames[0];
const sheet = workbook.Sheets[sheetName];

// Bước 2: Chuyển sheet thành JSON
const rawData = xlsx.utils.sheet_to_json(sheet, {
    defval: "",
});

// Bước 3: Gom nhóm theo tỉnh
const grouped = {};

rawData.forEach((row) => {
    const matinhBNV = row["Mã tỉnh (BNV)"];
    const matinhTMS = row["Mã tỉnh (TMS)"];
    const tentinhmoi = row["Tên tỉnh/TP mới"];
    const maphuongxa = row["Mã phường/xã mới "];
    const tenphuongxa = row["Tên Phường/Xã mới"];

    const key = `${matinhBNV}_${matinhTMS}_${tentinhmoi}`;

    if (!grouped[key]) {
        grouped[key] = {
            matinhBNV: matinhBNV,
            matinhTMS: matinhTMS,
            tentinhmoi: tentinhmoi,
            phuongxa: [],
        };
    }

    grouped[key].phuongxa.push({
        maphuongxa: maphuongxa,
        tenphuongxa: tenphuongxa,
    });
});

// Bước 4: Chuyển object thành mảng
const finalOutput = Object.values(grouped);

// Bước 5: Ghi ra file JSON
fs.writeFileSync(
    "output.json",
    JSON.stringify(finalOutput, null, 2),
    "utf-8"
);

console.log("✅ Đã xuất file JSON: output.json");
