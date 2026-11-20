#!/usr/bin/env python3
# Fork-server sederhana: setiap koneksi TCP menjalankan ./vuln dengan socket
# sebagai stdin/stdout/stderr. Jadi mesin pwn ini bisa "dihubungi" pakai nc:
#     nc 127.0.0.1 9008
# Dipakai baik di mode lite (NetHunter) maupun di dalam container Docker.
import os, sys, socket, signal, subprocess

HOST = sys.argv[1] if len(sys.argv) > 1 else "127.0.0.1"
PORT = int(sys.argv[2]) if len(sys.argv) > 2 else 9008

HERE = os.path.dirname(os.path.abspath(__file__))
os.chdir(HERE)

# Kompilasi binary kalau belum ada (menyesuaikan arsitektur host).
if not os.path.exists("vuln"):
    subprocess.run(
        ["gcc", "-fno-stack-protector", "-no-pie", "-O0", "-o", "vuln", "vuln.c"],
        check=True,
    )

signal.signal(signal.SIGCHLD, signal.SIG_IGN)   # reap anak otomatis

srv = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
srv.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
srv.bind((HOST, PORT))
srv.listen(16)
sys.stderr.write(f"[serve] SecureBox mendengarkan di {HOST}:{PORT}\n")
sys.stderr.flush()

while True:
    try:
        conn, _ = srv.accept()
    except KeyboardInterrupt:
        break
    pid = os.fork()
    if pid == 0:
        srv.close()
        fd = conn.fileno()
        os.dup2(fd, 0); os.dup2(fd, 1); os.dup2(fd, 2)
        signal.alarm(60)                 # koneksi menggantung mati sendiri
        os.execv("./vuln", ["./vuln"])
        os._exit(1)
    conn.close()
