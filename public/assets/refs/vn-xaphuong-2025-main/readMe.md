# 📍 Danh sách Mã Xã/Phường Việt Nam (Cập nhật 01/07/2025)

Dự án nhỏ này cập nhật danh sách **địa giới hành chính** tại Việt Nam, được cập nhật sau đợt **sáp nhập đơn vị hành chính** theo Nghị quyết của Ủy ban Thường vụ Quốc hội, có hiệu lực từ **ngày 01/07/2025**.

> ✅ Nguồn: [EasyInvoice.vn](https://easyinvoice.vn/easyinvoice-cap-nhat-danh-sach-xa-phuong-moi-2025-sau-sap-nhap/)

---

## 🔍 Nội dung

-   **File Excel gốc**: chứa đầy đủ thông tin gồm:

    -   Mã tỉnh (BNV & TMS)
    -   Tên tỉnh/thành phố
    -   Mã & tên quận/huyện cũ
    -   Mã & tên xã/phường/thị trấn mới

-   **File JSON đã xử lý**: chuẩn hoá dữ liệu thành format JSON dễ dùng cho frontend/backend.

---

## 📦 Cấu trúc JSON xuất ra

```json
[
  {
    "matinhBNV": 1,
    "matinhTMS": 101,
    "tentinhmoi": "Thành phố Hà Nội",
    "phuongxa": [
      { "maphuongxa": 10105001, "tenphuongxa": "Phường Hoàn Kiếm" },
      { "maphuongxa": 10105002, "tenphuongxa": "Phường Cửa Nam" }
    ]
  },
  ...
]
```

## 📌 Lưu ý quan trọng

-   Dữ liệu có hiệu lực **chính thức từ ngày 01/07/2025** trên toàn quốc.
-   Một số xã/phường có thể **bị xoá hoặc gộp lại**, vì vậy hệ thống cũ có thể không còn phù hợp nếu không cập nhật.
-   Mã sử dụng theo chuẩn **TMS - [Tax Management System](https://vietnambiz.vn/he-thong-thong-tin-quan-li-thue-tap-trung-la-gi-20200511105340124.htm) (thuộc hệ thống Tổng cục Thuế)** và **BNV (Bộ Nội Vụ)**.

---

## 📁 File liên quan

-   `Final_Danh-muc-Phuong-xa_moi.xlsx` – File gốc do EasyInvoice cung cấp.
-   `danhmucxaphuong.json` – File JSON đã chuẩn hoá.
-   `convert.js` – Script xử lý bằng Node.js.

---

## 🤝 Đóng góp

Nếu bạn phát hiện sai lệch, có thể mở PR hoặc gửi thông tin về issue để mình cập nhật thêm.

---

## 📚 Tham khảo

-   [Danh sách xã/phường mới 2025 – EasyInvoice](https://easyinvoice.vn/easyinvoice-cap-nhat-danh-sach-xa-phuong-moi-2025-sau-sap-nhap/)
-   [Nghị quyết số 1171/NQ-UBTVQH15](https://vbpl.vn/bonoitvu/Pages/vbpq-toanvan.aspx?ItemID=167342)
