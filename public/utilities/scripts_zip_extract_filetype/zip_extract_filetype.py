# -*- coding: utf-8 -*-
"""
zip_extract_filetype.py
-----------------------
Duyet mot thu muc (thu muc A) chua nhieu file .zip, lay ra cac file co duoi
mong muon (vd: jpg) ben trong tung file zip va do thang ve thu muc A.

2 cach dung:
  1) GUI (mac dinh, co dropdown chon loai file):
        python zip_extract_filetype.py
  2) CLI:
        python zip_extract_filetype.py --dir "D:\\Anh\\zips" --list
        python zip_extract_filetype.py --dir "D:\\Anh\\zips" --ext jpg
        python zip_extract_filetype.py --dir "D:\\Anh\\zips" --ext all --prefix
        python zip_extract_filetype.py --dir "D:\\Anh\\zips" --ext png --dry-run

Chi dung thu vien chuan cua Python 3.8+ (zipfile, tkinter). Khong can pip install.
"""

from __future__ import annotations

import argparse
import os
import sys
import zipfile
from dataclasses import dataclass, field
from pathlib import Path

ALL_LABEL = "* (tat ca cac loai file)"
NO_EXT_LABEL = "(khong co duoi)"
SKIP_DIR_PARTS = {"__MACOSX"}


# ---------------------------------------------------------------- ten file zip
def decode_member_name(info: zipfile.ZipInfo) -> str:
    """Doan lai ten file trong zip cho dung tieng Viet.

    zipfile decode ten bang cp437 khi zip khong bat co UTF-8, nen ten tieng Viet
    hay bi loi. Thu decode lai theo utf-8 roi cp1258.
    """
    name = info.filename
    if info.flag_bits & 0x800:  # zip da danh dau UTF-8, tin luon
        return name
    try:
        raw = name.encode("cp437")
    except UnicodeEncodeError:
        return name
    for enc in ("utf-8", "cp1258"):
        try:
            decoded = raw.decode(enc)
        except UnicodeDecodeError:
            continue
        if decoded != name and any(ord(c) > 127 for c in decoded):
            return decoded
    return name


def member_ext(name: str) -> str:
    """Duoi file (khong dau cham, chu thuong). Rong neu khong co duoi."""
    return Path(name).suffix.lower().lstrip(".")


def is_wanted(info: zipfile.ZipInfo, name: str, ext) -> bool:
    """True neu member la file thuc su va khop duoi can lay (ext=None: moi file)."""
    if info.is_dir() or name.endswith("/"):
        return False
    parts = [p.upper() for p in Path(name).parts[:-1]]
    if any(p in SKIP_DIR_PARTS for p in parts):
        return False
    if Path(name).name.startswith("._"):  # rac cua macOS
        return False
    if ext is None:
        return True
    return member_ext(name) == ext


def safe_target(dest_dir: Path, filename: str, taken: set) -> Path:
    """Duong dan dich chua bi trung ten (them _1, _2, ... neu can)."""
    base = Path(filename).name
    stem, suffix = os.path.splitext(base)
    candidate = base
    i = 1
    while candidate.lower() in taken or (dest_dir / candidate).exists():
        candidate = "{}_{}{}".format(stem, i, suffix)
        i += 1
    taken.add(candidate.lower())
    return dest_dir / candidate


# ------------------------------------------------------------------- quet/scan
def list_zips(folder: Path) -> list:
    return sorted(
        (p for p in folder.iterdir() if p.is_file() and p.suffix.lower() == ".zip"),
        key=lambda p: p.name.lower(),
    )


def scan_extensions(folder: Path):
    """Dem so file theo tung duoi trong tat ca zip cua thu muc -> (counts, errors)."""
    counts = {}
    errors = []
    for zp in list_zips(folder):
        try:
            with zipfile.ZipFile(zp) as zf:
                for info in zf.infolist():
                    name = decode_member_name(info)
                    if not is_wanted(info, name, None):
                        continue
                    ext = member_ext(name) or NO_EXT_LABEL
                    counts[ext] = counts.get(ext, 0) + 1
        except (zipfile.BadZipFile, OSError) as exc:
            errors.append("{}: {}".format(zp.name, exc))
    return counts, errors


# --------------------------------------------------------------------- extract
@dataclass
class Result:
    zips: int = 0
    extracted: int = 0
    skipped: int = 0
    errors: list = field(default_factory=list)


def extract(folder, ext, prefix_zip_name=False, overwrite=False, dry_run=False, log=print):
    """Lay cac file co duoi `ext` (None = tat ca) tu moi zip trong `folder`."""
    folder = Path(folder)
    res = Result()
    zips = list_zips(folder)
    if not zips:
        log("[!] Khong tim thay file .zip nao trong: {}".format(folder))
        return res

    label = ext if ext else "tat ca"
    log("Thu muc    : {}".format(folder))
    log("Loai file  : {}".format(label))
    log("So file zip: {}".format(len(zips)))
    if dry_run:
        log("(dry-run: chi liet ke, khong ghi file)")
    log("-" * 62)

    taken = set()
    for zp in zips:
        res.zips += 1
        try:
            with zipfile.ZipFile(zp) as zf:
                members = [(i, decode_member_name(i)) for i in zf.infolist()]
                members = [(i, n) for i, n in members if is_wanted(i, n, ext)]
                if not members:
                    log("  - {}: khong co file {}".format(zp.name, label))
                    continue
                log("  + {}: {} file".format(zp.name, len(members)))
                for info, name in members:
                    out_name = Path(name).name
                    if prefix_zip_name:
                        out_name = "{}__{}".format(zp.stem, out_name)
                    if overwrite:
                        target = folder / out_name
                    else:
                        target = safe_target(folder, out_name, taken)
                    if dry_run:
                        log("      -> {}".format(target.name))
                        res.extracted += 1
                        continue
                    try:
                        with zf.open(info) as src, open(target, "wb") as dst:
                            while True:
                                chunk = src.read(256 * 1024)
                                if not chunk:
                                    break
                                dst.write(chunk)
                        res.extracted += 1
                    except (OSError, zipfile.BadZipFile, RuntimeError) as exc:
                        res.skipped += 1
                        res.errors.append("{} / {}: {}".format(zp.name, name, exc))
                        log("      [loi] {}: {}".format(name, exc))
        except (zipfile.BadZipFile, OSError) as exc:
            res.errors.append("{}: {}".format(zp.name, exc))
            log("  [loi] {}: {}".format(zp.name, exc))

    log("-" * 62)
    log("XONG. Da lay {} file tu {} zip vao: {}".format(res.extracted, res.zips, folder))
    if res.errors:
        log("Co {} loi (xem chi tiet o tren).".format(len(res.errors)))
    return res


# ------------------------------------------------------------------------- GUI
def run_gui(initial_dir=""):
    import tkinter as tk
    from tkinter import filedialog, messagebox, ttk

    root = tk.Tk()
    root.title("Zip Extract by File Type - Lazinet")
    root.geometry("860x580")
    root.minsize(720, 480)

    dir_var = tk.StringVar(value=initial_dir)
    ext_var = tk.StringVar()
    prefix_var = tk.BooleanVar(value=False)
    overwrite_var = tk.BooleanVar(value=False)
    dry_var = tk.BooleanVar(value=False)

    frm = ttk.Frame(root, padding=12)
    frm.pack(fill="both", expand=True)
    frm.columnconfigure(1, weight=1)

    def log(msg=""):
        out.configure(state="normal")
        out.insert("end", "{}\n".format(msg))
        out.see("end")
        out.configure(state="disabled")
        root.update_idletasks()

    def current_folder():
        raw = dir_var.get().strip().strip('"')
        if not raw:
            messagebox.showwarning("Thieu thu muc", "Hay chon thu muc A chua cac file zip.")
            return None
        folder = Path(raw)
        if not folder.is_dir():
            messagebox.showerror("Sai duong dan", "Khong ton tai thu muc:\n{}".format(folder))
            return None
        return folder

    def refresh_types(show_msg=True):
        folder = current_folder()
        if folder is None:
            return
        zips = list_zips(folder)
        if not zips:
            combo.configure(values=[ALL_LABEL])
            ext_var.set(ALL_LABEL)
            log("[!] Khong co file .zip nao trong: {}".format(folder))
            if show_msg:
                messagebox.showinfo("Khong co zip", "Thu muc nay khong co file .zip:\n{}".format(folder))
            return
        log("Dang quet {} file zip trong: {} ...".format(len(zips), folder))
        counts, errors = scan_extensions(folder)
        for e in errors:
            log("  [loi doc zip] {}".format(e))
        if not counts:
            combo.configure(values=[ALL_LABEL])
            ext_var.set(ALL_LABEL)
            log("[!] Cac zip khong chua file nao.")
            return
        items = sorted(counts.items(), key=lambda kv: (-kv[1], kv[0]))
        values = ["{}  ({} file)".format(e, n) for e, n in items] + [ALL_LABEL]
        combo.configure(values=values)
        ext_var.set(values[0])
        log("Cac loai file tim thay: " + ", ".join("{}={}".format(e, n) for e, n in items))

    def browse():
        chosen = filedialog.askdirectory(title="Chon thu muc A (chua cac file zip)")
        if chosen:
            dir_var.set(os.path.normpath(chosen))
            refresh_types(show_msg=False)

    def selected_ext():
        """None = lay tat ca; nguoc lai tra ve duoi file da chon."""
        raw = ext_var.get().strip()
        if not raw or raw == ALL_LABEL or raw.startswith("*"):
            return None
        return raw.split("(")[0].strip().lstrip(".").lower()

    def do_run():
        folder = current_folder()
        if folder is None:
            return
        ext = selected_ext()
        if ext == NO_EXT_LABEL.strip("()").lower() or ext == "khong co duoi":
            messagebox.showwarning(
                "Khong ho tro",
                "Nhom '(khong co duoi)' chi de xem thong ke.\nHay chon mot duoi cu the hoac '*'.",
            )
            return
        btn_run.configure(state="disabled")
        try:
            log("")
            res = extract(
                folder,
                ext,
                prefix_zip_name=prefix_var.get(),
                overwrite=overwrite_var.get(),
                dry_run=dry_var.get(),
                log=log,
            )
            if res.extracted and not dry_var.get():
                messagebox.showinfo(
                    "Xong",
                    "Da lay {} file vao:\n{}".format(res.extracted, folder),
                )
        finally:
            btn_run.configure(state="normal")

    def open_folder():
        folder = current_folder()
        if folder is None:
            return
        try:
            os.startfile(str(folder))
        except AttributeError:  # khong phai Windows
            messagebox.showinfo("Thu muc", str(folder))

    # hang 0: thu muc
    ttk.Label(frm, text="Thu muc A:").grid(row=0, column=0, sticky="w", pady=4)
    ttk.Entry(frm, textvariable=dir_var).grid(row=0, column=1, sticky="ew", padx=6, pady=4)
    ttk.Button(frm, text="Chon...", command=browse).grid(row=0, column=2, pady=4)

    # hang 1: loai file (dropdown)
    ttk.Label(frm, text="Loai file:").grid(row=1, column=0, sticky="w", pady=4)
    combo = ttk.Combobox(frm, textvariable=ext_var, values=[ALL_LABEL])
    combo.grid(row=1, column=1, sticky="ew", padx=6, pady=4)
    ttk.Button(frm, text="Quet lai", command=refresh_types).grid(row=1, column=2, pady=4)

    # hang 2: tuy chon
    opts = ttk.Frame(frm)
    opts.grid(row=2, column=0, columnspan=3, sticky="w", pady=(8, 4))
    ttk.Checkbutton(opts, text="Them ten zip vao dau ten file", variable=prefix_var).pack(side="left")
    ttk.Checkbutton(opts, text="Ghi de file trung ten", variable=overwrite_var).pack(side="left", padx=14)
    ttk.Checkbutton(opts, text="Chay thu (khong ghi file)", variable=dry_var).pack(side="left")

    # hang 3: nut chay
    acts = ttk.Frame(frm)
    acts.grid(row=3, column=0, columnspan=3, sticky="w", pady=(4, 8))
    btn_run = ttk.Button(acts, text="Extract ngay", command=do_run)
    btn_run.pack(side="left")
    ttk.Button(acts, text="Mo thu muc", command=open_folder).pack(side="left", padx=8)
    ttk.Button(acts, text="Thoat", command=root.destroy).pack(side="left")

    # hang 4: log
    ttk.Label(frm, text="Ket qua:").grid(row=4, column=0, sticky="nw")
    box = ttk.Frame(frm)
    box.grid(row=4, column=1, columnspan=2, sticky="nsew", padx=6)
    frm.rowconfigure(4, weight=1)
    out = tk.Text(box, height=18, wrap="none", state="disabled")
    bar = ttk.Scrollbar(box, orient="vertical", command=out.yview)
    out.configure(yscrollcommand=bar.set)
    out.pack(side="left", fill="both", expand=True)
    bar.pack(side="right", fill="y")

    log("1) Chon thu muc A chua cac file .zip")
    log("2) Chon loai file o dropdown (danh sach tu dong quet tu cac zip)")
    log("3) Bam 'Extract ngay' - file se do thang vao thu muc A")

    if initial_dir:
        refresh_types(show_msg=False)
    root.mainloop()


# ------------------------------------------------------------------------ main
def fix_console_encoding():
    """Tranh loi UnicodeEncodeError khi in ten file tieng Viet ra cmd (cp1252/cp1258)."""
    for stream in (sys.stdout, sys.stderr):
        try:
            stream.reconfigure(encoding="utf-8", errors="replace")
        except (AttributeError, OSError, ValueError):
            pass


def main(argv=None):
    fix_console_encoding()
    ap = argparse.ArgumentParser(
        description="Lay file theo duoi (vd jpg) tu cac file zip trong 1 thu muc, do ve chinh thu muc do."
    )
    ap.add_argument("-d", "--dir", help="Thu muc A chua cac file .zip")
    ap.add_argument("-e", "--ext", help="Duoi file can lay (jpg, png, pdf...) hoac 'all' de lay tat ca")
    ap.add_argument("--list", action="store_true", help="Chi liet ke cac loai file co trong cac zip")
    ap.add_argument("--prefix", action="store_true", help="Them ten zip vao dau ten file lay ra")
    ap.add_argument("--overwrite", action="store_true", help="Ghi de khi trung ten (mac dinh tu doi ten _1, _2)")
    ap.add_argument("--dry-run", action="store_true", help="Chay thu, khong ghi file")
    ap.add_argument("--gui", action="store_true", help="Bat buoc mo GUI")
    args = ap.parse_args(argv)

    # Khong truyen gi -> mo GUI
    if args.gui or (not args.dir and not args.ext and not args.list):
        run_gui(args.dir or "")
        return 0

    if not args.dir:
        ap.error("thieu --dir")
    folder = Path(args.dir.strip().strip('"'))
    if not folder.is_dir():
        print("[loi] Khong ton tai thu muc: {}".format(folder))
        return 2

    if args.list or not args.ext:
        counts, errors = scan_extensions(folder)
        print("Thu muc: {}".format(folder))
        print("So file zip: {}".format(len(list_zips(folder))))
        if not counts:
            print("Khong tim thay file nao trong cac zip.")
        for ext, n in sorted(counts.items(), key=lambda kv: (-kv[1], kv[0])):
            print("  {:<18} {} file".format(ext, n))
        for e in errors:
            print("  [loi] {}".format(e))
        if not args.ext:
            print("\nChay lai voi --ext <duoi> de extract. Vi du: --ext jpg")
            return 0
        if args.list:
            return 0

    ext = None if args.ext.lower() in ("all", "*") else args.ext.strip().lstrip(".").lower()
    res = extract(
        folder,
        ext,
        prefix_zip_name=args.prefix,
        overwrite=args.overwrite,
        dry_run=args.dry_run,
    )
    return 0 if not res.errors else 1


if __name__ == "__main__":
    try:
        sys.exit(main())
    except KeyboardInterrupt:
        print("\nDa huy.")
        sys.exit(130)
