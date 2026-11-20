/* SecureBox - layanan pengecek kata sandi.
 * INTENTIONALLY VULNERABLE: stack buffer overflow.
 * read() membaca sampai 128 byte ke dalam buf[32]. Variabel 'authed' berada
 * TEPAT setelah buf di memori, sehingga luapan menimpanya dari 0 menjadi
 * bukan-nol -> melewati pengecekan dan memanggil win().
 *
 * Dikompilasi dengan -fno-stack-protector -no-pie agar deterministik untuk belajar.
 */
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

void win(void) {
    char flag[160];
    FILE *f = fopen("flag.txt", "r");
    if (!f) { puts("[akses diberikan, tapi flag.txt tidak ditemukan]"); fflush(stdout); return; }
    if (fgets(flag, sizeof(flag), f)) printf("Akses diberikan! %s\n", flag);
    fclose(f);
    fflush(stdout);
}

int main(void) {
    struct { char buf[32]; volatile int authed; } s;
    s.authed = 0;
    setvbuf(stdout, NULL, _IONBF, 0);
    puts("=== SecureBox ===");
    puts("Masukkan kata sandi untuk membuka brankas:");
    read(0, s.buf, 128);               /* VULN: 128 byte ke buf[32] */
    if (s.authed != 0) {
        puts("Autentikasi berhasil!");
        win();
    } else {
        puts("Kata sandi salah. Akses ditolak.");
    }
    return 0;
}
