# video2image.py — Xuất video thành ảnh (frame)

Công cụ GUI (tkinter) để tách khung hình từ video ra thành các file ảnh.

## Cài đặt (chỉ cần làm 1 lần)

- Cần có Python 3 (đã cài sẵn trên Windows thì bỏ qua bước tải Python).
- Cài thư viện xử lý video:
  ```
  pip install opencv-python
  ```

## Cách chạy

- Mở terminal tại thư mục chứa file, hoặc chạy trực tiếp:
  ```
  python video2image.py
  ```
- Hoặc double-click file `video2image.py` nếu máy đã gán sẵn Python để mở file `.py`.

## Cách sử dụng

- **1. Chọn video đầu vào**: bấm "Duyệt..." và chọn video từ bất kỳ thư mục nào (mp4, avi, mkv, mov, wmv, flv, webm, m4v...).
- **2. Thiết lập xuất ảnh**:
  - *Thư mục xuất*: mặc định tự tạo thư mục cùng tên video, nằm ngang cấp với file video. Có thể bấm "Duyệt..." để đổi.
  - *Tiền tố tên file*: mặc định lấy theo tên video. Ảnh xuất ra sẽ có dạng `tienTo_0001.jpg`, `tienTo_0002.jpg`, ...
  - *Định dạng*: chọn `jpg` (nhẹ, có nén) hoặc `png` (không mất chi tiết).
  - *Chất lượng (1-100)*: kéo thanh trượt, số càng cao ảnh càng nét nhưng dung lượng càng lớn.
  - *Tần suất trích xuất*: chọn xuất "mỗi N khung hình" hoặc "mỗi N giây" của video.
  - *Số lượng ảnh tối đa*: giới hạn số ảnh xuất ra, để `0` nếu muốn xuất hết.
- **3. Tiến trình**: bấm **Bắt đầu xuất** để chạy, thanh tiến trình và dòng trạng thái sẽ cập nhật theo thời gian thực. Có thể bấm **Hủy** để dừng giữa chừng (ảnh đã lưu vẫn được giữ lại).
- Sau khi chạy xong, dùng nút **Mở thư mục video** hoặc **Mở thư mục ảnh** để xem kết quả ngay trong File Explorer.

## Ghi chú

- Nếu chưa cài `opencv-python`, chương trình sẽ hiện thông báo lỗi ngay khi mở.
- Có thể chạy nhiều lần với cùng một video, cứ đổi tiền tố/thư mục xuất để không ghi đè ảnh cũ.

## Xử lý lỗi thường gặp

### "Controlled folder access blocked python.exe" / lỗi "không tìm thấy đường dẫn" khi tạo thư mục

Nguyên nhân: tính năng chống ransomware **Controlled folder access** của Windows chặn `python.exe` ghi file vào các thư mục mặc định được bảo vệ (Desktop, Documents, Pictures, **Videos**, Music). Khi bị chặn, Windows đôi khi báo nhầm thành lỗi "không tìm thấy đường dẫn" (WinError 2) thay vì "từ chối quyền truy cập".

Cách khắc phục (chọn 1 trong 2):

- **Cho phép python.exe**: Windows Security → Virus & threat protection → Ransomware protection → Controlled folder access → Allow an app through Controlled folder access → Add an allowed app → chọn `python.exe`.
- **Đổi thư mục xuất ảnh**: bấm "Duyệt..." ở mục Thư mục xuất, chọn nơi khác ngoài Desktop/Documents/Pictures/Videos/Music (ví dụ ổ `D:\` hoặc thư mục con trong `Downloads`).
