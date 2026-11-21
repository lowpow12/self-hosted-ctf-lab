#!/usr/bin/env bash
# setup-github.sh — siapkan koneksi GitHub untuk repo ini dari NetHunter/Termux.
# Setelah ini selesai, Anda push SENDIRI (manual):  git push -u origin master
#
# Pemakaian:
#   ./setup-github.sh                         # interaktif (tanya-jawab)
#   ./setup-github.sh <user> <repo> [ssh|https]
#
# Contoh:
#   ./setup-github.sh distinctlow ctf-lab ssh
#
# Script ini TIDAK menyimpan password/token dan TIDAK melakukan push otomatis
# (kecuali Anda jawab 'y' di akhir).

set -euo pipefail

c_grn=$'\e[32m'; c_ylw=$'\e[33m'; c_red=$'\e[31m'; c_cyn=$'\e[36m'; c_rst=$'\e[0m'
info(){ printf '%s[*]%s %s\n' "$c_cyn" "$c_rst" "$*"; }
ok(){   printf '%s[+]%s %s\n' "$c_grn" "$c_rst" "$*"; }
warn(){ printf '%s[!]%s %s\n' "$c_ylw" "$c_rst" "$*"; }
err(){  printf '%s[x]%s %s\n' "$c_red" "$c_rst" "$*" >&2; }
ask(){  # ask <prompt> <var> [default]
  local p="$1" __v="$2" d="${3:-}" a
  if [ -n "$d" ]; then printf '%s [%s]: ' "$p" "$d"; else printf '%s: ' "$p"; fi
  read -r a || true
  [ -z "$a" ] && a="$d"
  printf -v "$__v" '%s' "$a"
}

# --- pastikan di dalam git repo -------------------------------------------
git rev-parse --is-inside-work-tree >/dev/null 2>&1 || {
  err "Jalankan dari dalam folder repo git (mis. cd ke ctf-lab)."; exit 1; }
ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"
info "Repo: $ROOT  (branch: $(git branch --show-current))"

# --- identitas git ---------------------------------------------------------
if ! git config user.email >/dev/null 2>&1; then
  warn "Identitas git belum di-set."
  ask "Nama untuk commit" GN "$(whoami)"
  ask "Email untuk commit" GE
  git config user.name "$GN"
  git config user.email "$GE"
  ok "Identitas git di-set (lokal repo ini)."
fi
EMAIL="$(git config user.email)"

# --- argumen / tanya-jawab -------------------------------------------------
USER_GH="${1:-}"; REPO="${2:-}"; METHOD="${3:-}"
[ -n "$USER_GH" ] || ask "Username GitHub Anda" USER_GH
[ -n "$REPO" ]    || ask "Nama repository di GitHub" REPO "$(basename "$ROOT")"
if [ -z "$METHOD" ]; then
  ask "Metode auth (ssh/https)" METHOD "ssh"
fi
METHOD="$(printf '%s' "$METHOD" | tr '[:upper:]' '[:lower:]')"

# --- siapkan auth ----------------------------------------------------------
case "$METHOD" in
  ssh)
    command -v ssh-keygen >/dev/null 2>&1 || { err "ssh-keygen tidak ada. Install openssh dulu."; exit 1; }
    KEY="$HOME/.ssh/id_ed25519"
    if [ ! -f "$KEY" ]; then
      info "Membuat SSH key baru (ed25519, tanpa passphrase)..."
      mkdir -p "$HOME/.ssh"; chmod 700 "$HOME/.ssh"
      ssh-keygen -t ed25519 -C "$EMAIL" -f "$KEY" -N "" >/dev/null
      ok "SSH key dibuat: $KEY"
    else
      info "SSH key sudah ada: $KEY"
    fi
    echo
    warn "TAMBAHKAN kunci publik ini ke GitHub:"
    echo "   Buka  https://github.com/settings/ssh/new"
    echo "   Title: nethunter   |   Key type: Authentication Key"
    echo "   Tempel baris di bawah ini:"
    echo "${c_grn}"; cat "${KEY}.pub"; echo "${c_rst}"
    ask "Sudah ditambahkan? tekan Enter untuk tes koneksi (atau ketik 'skip')" GO ""
    if [ "$GO" != "skip" ]; then
      info "Menguji: ssh -T git@github.com"
      if ssh -o StrictHostKeyChecking=accept-new -T git@github.com 2>&1 | grep -q "successfully authenticated"; then
        ok "Autentikasi SSH ke GitHub berhasil."
      else
        warn "Belum terautentikasi. Pastikan kunci sudah ditambahkan, lalu ulangi tes:"
        echo "   ssh -T git@github.com"
      fi
    fi
    REMOTE_URL="git@github.com:${USER_GH}/${REPO}.git"
    ;;
  https)
    REMOTE_URL="https://github.com/${USER_GH}/${REPO}.git"
    info "Mengaktifkan penyimpanan kredensial (agar token tak diminta terus)..."
    git config --global credential.helper store || true
    echo
    warn "Mode HTTPS: saat 'git push' pertama, isi:"
    echo "   Username: ${USER_GH}"
    echo "   Password: Personal Access Token (BUKAN password akun)"
    echo "   Buat token: https://github.com/settings/tokens  (scope: repo)"
    ;;
  *)
    err "Metode tidak dikenal: '$METHOD' (pakai ssh atau https)"; exit 1 ;;
esac

# --- set remote ------------------------------------------------------------
if git remote get-url origin >/dev/null 2>&1; then
  info "Remote 'origin' sudah ada → diperbarui ke: $REMOTE_URL"
  git remote set-url origin "$REMOTE_URL"
else
  git remote add origin "$REMOTE_URL"
  ok "Remote 'origin' ditambahkan: $REMOTE_URL"
fi

# --- penutup ---------------------------------------------------------------
echo
warn "Pastikan repo '${USER_GH}/${REPO}' SUDAH DIBUAT di GitHub (KOSONG, tanpa README/.gitignore)."
echo "   Buat di: https://github.com/new  (Repository name: ${REPO})"
echo "   Kalau punya 'gh': gh repo create ${USER_GH}/${REPO} --private"
echo
info "Catatan: flags.env TIDAK ikut (sengaja di-gitignore). Simpan terpisah."
echo
BR="$(git branch --show-current)"
ok "Setup selesai. Push manual Anda:"
echo "   ${c_grn}git push -u origin ${BR}${c_rst}"
echo
ask "Mau push sekarang? (y/N)" DOPUSH "N"
case "$DOPUSH" in
  y|Y|ya|yes) info "Mendorong..."; git push -u origin "$BR" ;;
  *) info "Oke, push nanti dengan perintah di atas." ;;
esac
