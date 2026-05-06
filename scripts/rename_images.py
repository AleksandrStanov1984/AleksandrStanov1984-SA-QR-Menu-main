import json
from pathlib import Path

# =========================================
# PATHS
# =========================================

JSON_PATH = r"C:\AleksDev\SA-projekts\Projects\SA-QR-Menu-Main фото для сайта\Objects\Салан красоты\menu_beauty-style-salon_2026-05-06_16-04.json"

IMAGES_DIR = r"C:\AleksDev\SA-projekts\Projects\SA-QR-Menu-Main фото для сайта\Objects\Салан красоты\Iteam"

# =========================================
# LOAD JSON
# =========================================

with open(JSON_PATH, "r", encoding="utf-8") as f:
    data = json.load(f)

# =========================================
# BUILD MAP
# old_key => category.old_key
# =========================================

mapping = {}

for category in data.get("categories", []):

    category_key = category.get("key", "").strip()

    for item in category.get("items", []):

        item_key = item.get("key", "").strip()

        if not item_key:
            continue

        new_key = f"{category_key}.{item_key}"

        mapping[item_key.lower()] = new_key

# =========================================
# RENAME FILES
# =========================================

renamed = 0
missing = []
skipped = []

for file in Path(IMAGES_DIR).glob("*"):

    if not file.is_file():
        continue

    ext = file.suffix.lower()

    if ext not in [".jpg", ".jpeg", ".png", ".webp", ".svg"]:
        continue

    old_name = file.stem.lower()

    if old_name not in mapping:
        missing.append(file.name)
        continue

    new_name = mapping[old_name] + ext

    new_path = file.with_name(new_name)

    if new_path.exists():
        skipped.append(new_name)
        continue

    print(f"RENAMED: {file.name} -> {new_name}")

    file.rename(new_path)

    renamed += 1

# =========================================
# RESULT
# =========================================

print("\n=========================")
print(f"RENAMED: {renamed}")
print(f"MISSING: {len(missing)}")
print(f"SKIPPED: {len(skipped)}")

if missing:
    print("\n--- NOT FOUND IN JSON ---")
    for x in missing:
        print(x)

if skipped:
    print("\n--- ALREADY EXISTS ---")
    for x in skipped:
        print(x)
