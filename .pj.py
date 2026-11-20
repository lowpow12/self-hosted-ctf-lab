#!/usr/bin/env python3
# Mini JSON query helper agar labctl tidak butuh `jq`.
# Pemakaian:
#   pj FILE get  PATH [default]   -> cetak nilai skalar (atau default/kosong)
#   pj FILE arr  PATH             -> cetak tiap elemen list per baris
#   pj FILE keys PATH             -> cetak key dari objek per baris
#   pj FILE has  PATH             -> exit 0 jika ada & tidak null, selain itu 1
# PATH pakai titik; indeks list berupa angka. Contoh: ports.0.host
import sys, json

def walk(obj, path):
    if path == "":
        return obj
    for part in path.split("."):
        if isinstance(obj, list):
            try:
                obj = obj[int(part)]
            except (ValueError, IndexError):
                return None
        elif isinstance(obj, dict):
            if part not in obj:
                return None
            obj = obj[part]
        else:
            return None
    return obj

def main():
    f, op, path = sys.argv[1], sys.argv[2], sys.argv[3]
    default = sys.argv[4] if len(sys.argv) > 4 else ""
    with open(f) as fh:
        data = json.load(fh)
    node = walk(data, path)
    if op == "get":
        if node is None:
            print(default)
        elif isinstance(node, (dict, list)):
            print(default)
        else:
            print(node)
    elif op == "arr":
        if isinstance(node, list):
            for x in node:
                print(x)
    elif op == "keys":
        if isinstance(node, dict):
            for k in node:
                print(k)
    elif op == "has":
        sys.exit(0 if node is not None else 1)
    else:
        sys.exit(2)

if __name__ == "__main__":
    main()
