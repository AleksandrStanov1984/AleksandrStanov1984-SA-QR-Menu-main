import json
from pathlib import Path

# =========================================
# PATH TO JSON
# =========================================
JSON_PATH = r"C:\AleksDev\SA-projekts\Projects\SA-QR-Menu-Main фото для сайта\Objects\Салан красоты\menu_beauty-style-salon_2026-05-06_16-04.json"

# =========================================
# LOAD JSON
# =========================================
path = Path(JSON_PATH)

with open(path, "r", encoding="utf-8") as f:
    data = json.load(f)

# =========================================
# UPDATE ITEM KEYS
# =========================================
updated = 0

for category in data.get("categories", []):

    category_key = category.get("key", "").strip()

    for item in category.get("items", []):

        old_key = item.get("key", "").strip()

        # skip already updated
        if old_key.startswith(category_key + "."):
            continue

        new_key = f"{category_key}.{old_key}"

        item["key"] = new_key

        updated += 1

        print(f"[UPDATED] {old_key} -> {new_key}")

# =========================================
# SAVE
# =========================================
backup = path.with_suffix(".backup.json")

# backup
with open(backup, "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)

# overwrite original
with open(path, "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)

print("\n====================")
print(f"UPDATED: {updated}")
print(f"BACKUP: {backup}")
print("====================")
