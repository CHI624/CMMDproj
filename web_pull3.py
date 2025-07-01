import pandas as pd
from striprtf.striprtf import rtf_to_text

# Read the RTF file and convert to plain text
with open('/Users/victoragbara/Downloads/civ_rank.rtf', 'r', encoding='utf-8') as f:
    rtf_content = f.read()
    text = rtf_to_text(rtf_content)

# Split the plain text into lines and strip empty ones
lines = [line.strip() for line in text.splitlines() if line.strip()]

# Parse every 5 lines: code, title, (3 more we skip)
records = []
for i in range(0, len(lines), 5):
    try:
        code = lines[i]
        title = lines[i + 1]
        records.append({'Series Code': code, 'Job Series Title': title})
    except IndexError:
        continue  # skip incomplete block

# Convert to DataFrame and save
df = pd.DataFrame(records)
csv_path = '/Users/victoragbara/Downloads/civ_rank1.csv'
df.to_csv(csv_path, index=False)

print("Saved to:", csv_path)
print(df.head())
