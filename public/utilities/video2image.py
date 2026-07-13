# Yêu cầu: pip install opencv-python
"""Video -> Ảnh (Frame Exporter): xuất các khung hình từ video thành file ảnh."""

import os
import sys
import time
import threading
import subprocess
import tkinter as tk
from tkinter import ttk, filedialog, messagebox

try:
    import cv2
except ImportError:
    cv2 = None

DEFAULT_QUALITY = 90
DEFAULT_INTERVAL_VALUE = 1
DEFAULT_INTERVAL_UNIT = "frame"  # "frame" | "second"
DEFAULT_MAX_FRAMES = 0  # 0 = không giới hạn
DEFAULT_FORMAT = "jpg"  # "jpg" | "png"

VIDEO_FILETYPES = [
    ("Video files", "*.mp4 *.avi *.mkv *.mov *.wmv *.flv *.webm *.m4v *.mpg *.mpeg *.ts"),
    ("Tất cả file", "*.*"),
]


def open_folder(path: str):
    if not path or not os.path.isdir(path):
        messagebox.showwarning("Không tìm thấy thư mục", f"Thư mục không tồn tại:\n{path}")
        return
    if sys.platform == "win32":
        os.startfile(path)
    elif sys.platform == "darwin":
        subprocess.Popen(["open", path])
    else:
        subprocess.Popen(["xdg-open", path])


class ExportJob:
    """Chạy trong thread riêng để không làm đứng giao diện."""

    def __init__(self, video_path, output_dir, prefix, fmt, quality,
                 interval_value, interval_unit, max_frames, progress_cb, done_cb):
        self.video_path = video_path
        self.output_dir = output_dir
        self.prefix = prefix
        self.fmt = fmt
        self.quality = quality
        self.interval_value = interval_value
        self.interval_unit = interval_unit
        self.max_frames = max_frames
        self.progress_cb = progress_cb
        self.done_cb = done_cb
        self.cancelled = False

    def cancel(self):
        self.cancelled = True

    @staticmethod
    def _folder_blocked_hint(output_dir, exc=None):
        detail = f"\n\nChi tiết: {exc}" if exc else ""
        return (
            f"Không thể ghi vào thư mục xuất ảnh:\n{output_dir}{detail}\n\n"
            "Nguyên nhân thường gặp: Windows 'Controlled folder access' (chống ransomware) "
            "đang chặn python.exe ghi file (đôi khi hiện nhầm thành lỗi 'không tìm thấy đường dẫn').\n\n"
            "Cách khắc phục:\n"
            "1. Windows Security > Virus & threat protection > Ransomware protection > "
            "Controlled folder access > Allow an app through Controlled folder access > "
            "thêm python.exe.\n"
            "2. Hoặc đổi Thư mục xuất sang nơi khác, ngoài Desktop/Documents/Pictures/Videos/Music."
        )

    def run(self):
        try:
            try:
                os.makedirs(self.output_dir, exist_ok=True)
            except OSError as exc:
                raise RuntimeError(self._folder_blocked_hint(self.output_dir, exc)) from exc

            cap = cv2.VideoCapture(self.video_path)
            if not cap.isOpened():
                raise RuntimeError("Không thể mở file video.")

            fps = cap.get(cv2.CAP_PROP_FPS) or 0
            total_frames = int(cap.get(cv2.CAP_PROP_FRAME_COUNT) or 0)

            if self.interval_unit == "second":
                interval_frames = max(1, round((fps or 25) * self.interval_value))
            else:
                interval_frames = max(1, int(self.interval_value))

            ext = "jpg" if self.fmt == "jpg" else "png"
            if self.fmt == "jpg":
                encode_params = [cv2.IMWRITE_JPEG_QUALITY, int(self.quality)]
            else:
                png_compression = round((100 - self.quality) / 100 * 9)
                encode_params = [cv2.IMWRITE_PNG_COMPRESSION, png_compression]

            frame_idx = 0
            saved_count = 0
            start_time = time.time()

            while True:
                if self.cancelled:
                    break
                ret, frame = cap.read()
                if not ret:
                    break

                if frame_idx % interval_frames == 0:
                    saved_count += 1
                    filename = f"{self.prefix}_{saved_count:04d}.{ext}"
                    out_path = os.path.join(self.output_dir, filename)
                    ok = cv2.imwrite(out_path, frame, encode_params)
                    if not ok:
                        raise RuntimeError(self._folder_blocked_hint(self.output_dir))

                    if self.max_frames > 0 and saved_count >= self.max_frames:
                        frame_idx += 1
                        break

                frame_idx += 1

                if frame_idx % 5 == 0 or (total_frames and frame_idx >= total_frames):
                    self.progress_cb(frame_idx, total_frames, saved_count, time.time() - start_time)

            cap.release()
            self.progress_cb(frame_idx, total_frames, saved_count, time.time() - start_time)
            self.done_cb(cancelled=self.cancelled, saved_count=saved_count, error=None)
        except Exception as exc:
            self.done_cb(cancelled=False, saved_count=0, error=str(exc))


class App(tk.Tk):
    def __init__(self):
        super().__init__()
        self.title("Video → Ảnh (Frame Exporter)")
        self.geometry("640x580")
        self.resizable(False, False)

        self.video_path_var = tk.StringVar()
        self.output_dir_var = tk.StringVar()
        self.prefix_var = tk.StringVar()
        self.format_var = tk.StringVar(value=DEFAULT_FORMAT)
        self.quality_var = tk.IntVar(value=DEFAULT_QUALITY)
        self.interval_unit_var = tk.StringVar(value=DEFAULT_INTERVAL_UNIT)
        self.interval_value_var = tk.DoubleVar(value=DEFAULT_INTERVAL_VALUE)
        self.max_frames_var = tk.IntVar(value=DEFAULT_MAX_FRAMES)
        self.status_var = tk.StringVar(value="Chưa chọn video.")

        self.job = None
        self.job_thread = None

        self._build_ui()

        if cv2 is None:
            messagebox.showerror(
                "Thiếu thư viện",
                "Chưa cài đặt opencv-python.\nHãy chạy: pip install opencv-python",
            )

    def _build_ui(self):
        pad = {"padx": 10, "pady": 6}

        frm_video = ttk.LabelFrame(self, text="1. Chọn video đầu vào")
        frm_video.pack(fill="x", **pad)
        frm_video.columnconfigure(0, weight=1)

        ttk.Entry(frm_video, textvariable=self.video_path_var, width=70).grid(
            row=0, column=0, padx=8, pady=8, sticky="we"
        )
        ttk.Button(frm_video, text="Duyệt...", command=self.browse_video).grid(
            row=0, column=1, padx=8, pady=8
        )

        frm_out = ttk.LabelFrame(self, text="2. Thiết lập xuất ảnh")
        frm_out.pack(fill="x", **pad)
        frm_out.columnconfigure(1, weight=1)

        ttk.Label(frm_out, text="Thư mục xuất:").grid(row=0, column=0, sticky="w", padx=8, pady=4)
        ttk.Entry(frm_out, textvariable=self.output_dir_var, width=48).grid(
            row=0, column=1, sticky="we", padx=4, pady=4
        )
        ttk.Button(frm_out, text="Duyệt...", command=self.browse_output_dir).grid(
            row=0, column=2, padx=8, pady=4
        )

        ttk.Label(frm_out, text="Tiền tố tên file:").grid(row=1, column=0, sticky="w", padx=8, pady=4)
        ttk.Entry(frm_out, textvariable=self.prefix_var, width=30).grid(
            row=1, column=1, sticky="w", padx=4, pady=4
        )

        ttk.Label(frm_out, text="Định dạng:").grid(row=2, column=0, sticky="w", padx=8, pady=4)
        ttk.Combobox(
            frm_out, textvariable=self.format_var, values=["jpg", "png"],
            state="readonly", width=8,
        ).grid(row=2, column=1, sticky="w", padx=4, pady=4)

        ttk.Label(frm_out, text="Chất lượng (1-100):").grid(row=3, column=0, sticky="w", padx=8, pady=4)
        quality_frame = ttk.Frame(frm_out)
        quality_frame.grid(row=3, column=1, columnspan=2, sticky="w", padx=4, pady=4)
        ttk.Scale(
            quality_frame, from_=1, to=100, variable=self.quality_var,
            orient="horizontal", length=220,
        ).pack(side="left")
        ttk.Label(quality_frame, textvariable=self.quality_var, width=4).pack(side="left", padx=6)

        ttk.Label(frm_out, text="Tần suất trích xuất:").grid(row=4, column=0, sticky="w", padx=8, pady=4)
        freq_frame = ttk.Frame(frm_out)
        freq_frame.grid(row=4, column=1, columnspan=2, sticky="w", padx=4, pady=4)
        ttk.Label(freq_frame, text="Mỗi").pack(side="left")
        ttk.Spinbox(
            freq_frame, from_=0.1, to=100000, increment=1,
            textvariable=self.interval_value_var, width=8,
        ).pack(side="left", padx=4)
        ttk.Radiobutton(
            freq_frame, text="khung hình", variable=self.interval_unit_var, value="frame"
        ).pack(side="left", padx=(4, 0))
        ttk.Radiobutton(
            freq_frame, text="giây", variable=self.interval_unit_var, value="second"
        ).pack(side="left", padx=4)

        ttk.Label(frm_out, text="Số lượng ảnh tối đa:").grid(row=5, column=0, sticky="w", padx=8, pady=4)
        max_frame = ttk.Frame(frm_out)
        max_frame.grid(row=5, column=1, columnspan=2, sticky="w", padx=4, pady=4)
        ttk.Spinbox(
            max_frame, from_=0, to=1000000, textvariable=self.max_frames_var, width=10
        ).pack(side="left")
        ttk.Label(max_frame, text="(0 = không giới hạn)").pack(side="left", padx=6)

        frm_progress = ttk.LabelFrame(self, text="3. Tiến trình")
        frm_progress.pack(fill="x", **pad)

        self.progress_bar = ttk.Progressbar(
            frm_progress, orient="horizontal", length=580, mode="determinate"
        )
        self.progress_bar.pack(padx=8, pady=8, fill="x")
        ttk.Label(frm_progress, textvariable=self.status_var, wraplength=580, justify="left").pack(
            padx=8, pady=(0, 8), anchor="w"
        )

        frm_btn = ttk.Frame(self)
        frm_btn.pack(fill="x", **pad)

        self.start_btn = ttk.Button(frm_btn, text="Bắt đầu xuất", command=self.start_export)
        self.start_btn.pack(side="left", padx=4)

        self.cancel_btn = ttk.Button(frm_btn, text="Hủy", command=self.cancel_export, state="disabled")
        self.cancel_btn.pack(side="left", padx=4)

        ttk.Button(frm_btn, text="Mở thư mục video", command=self.open_video_folder).pack(
            side="left", padx=4
        )
        ttk.Button(frm_btn, text="Mở thư mục ảnh", command=self.open_output_folder).pack(
            side="left", padx=4
        )

    def browse_video(self):
        path = filedialog.askopenfilename(title="Chọn video", filetypes=VIDEO_FILETYPES)
        if not path:
            return
        self.video_path_var.set(path)
        video_dir = os.path.dirname(path)
        stem = os.path.splitext(os.path.basename(path))[0]
        self.prefix_var.set(stem)
        self.output_dir_var.set(os.path.join(video_dir, stem))
        self.status_var.set(f"Đã chọn: {os.path.basename(path)}")

    def browse_output_dir(self):
        initial = self.output_dir_var.get() or os.path.dirname(self.video_path_var.get()) or "."
        path = filedialog.askdirectory(title="Chọn thư mục xuất ảnh", initialdir=initial)
        if path:
            self.output_dir_var.set(path)

    def open_video_folder(self):
        video_path = self.video_path_var.get()
        if not video_path:
            messagebox.showwarning("Chưa chọn video", "Hãy chọn video trước.")
            return
        open_folder(os.path.dirname(video_path))

    def open_output_folder(self):
        open_folder(self.output_dir_var.get())

    def start_export(self):
        if cv2 is None:
            messagebox.showerror("Thiếu thư viện", "Cần cài opencv-python: pip install opencv-python")
            return

        video_path = self.video_path_var.get().strip()
        if not video_path or not os.path.isfile(video_path):
            messagebox.showwarning("Chưa chọn video", "Hãy chọn file video hợp lệ.")
            return

        output_dir = self.output_dir_var.get().strip()
        if not output_dir:
            messagebox.showwarning("Thiếu thư mục xuất", "Hãy nhập thư mục xuất ảnh.")
            return

        prefix = self.prefix_var.get().strip() or "frame"

        interval_value = self.interval_value_var.get()
        if interval_value <= 0:
            messagebox.showwarning("Giá trị không hợp lệ", "Tần suất phải lớn hơn 0.")
            return

        self.job = ExportJob(
            video_path=video_path,
            output_dir=output_dir,
            prefix=prefix,
            fmt=self.format_var.get(),
            quality=self.quality_var.get(),
            interval_value=interval_value,
            interval_unit=self.interval_unit_var.get(),
            max_frames=self.max_frames_var.get(),
            progress_cb=self._on_progress,
            done_cb=self._on_done,
        )
        self.start_btn.config(state="disabled")
        self.cancel_btn.config(state="normal")
        self.progress_bar["value"] = 0
        self.status_var.set("Đang xử lý...")

        self.job_thread = threading.Thread(target=self.job.run, daemon=True)
        self.job_thread.start()

    def cancel_export(self):
        if self.job:
            self.job.cancel()
            self.status_var.set("Đang hủy...")

    def _on_progress(self, frame_idx, total_frames, saved_count, elapsed):
        def update():
            if total_frames:
                pct = min(100, frame_idx / total_frames * 100)
                self.progress_bar["value"] = pct
                self.status_var.set(
                    f"Khung hình: {frame_idx}/{total_frames} ({pct:.1f}%) — "
                    f"Đã lưu: {saved_count} ảnh — {elapsed:.1f}s"
                )
            else:
                self.status_var.set(
                    f"Khung hình: {frame_idx} — Đã lưu: {saved_count} ảnh — {elapsed:.1f}s"
                )
        self.after(0, update)

    def _on_done(self, cancelled, saved_count, error):
        def update():
            self.start_btn.config(state="normal")
            self.cancel_btn.config(state="disabled")
            if error:
                self.status_var.set(f"Lỗi: {error}")
                messagebox.showerror("Lỗi", error)
            elif cancelled:
                self.status_var.set(f"Đã hủy. Đã lưu {saved_count} ảnh.")
            else:
                self.status_var.set(f"Hoàn tất! Đã lưu {saved_count} ảnh vào:\n{self.output_dir_var.get()}")
                messagebox.showinfo("Hoàn tất", f"Đã xuất {saved_count} ảnh.")
        self.after(0, update)


if __name__ == "__main__":
    App().mainloop()
