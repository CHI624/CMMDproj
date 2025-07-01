import pandas as pd

url = "https://www.federalpay.org/jobs/gs"

tables = pd.read_html(url)

df1 = tables[0]

first_row = df1.iloc[0]

cell = df1.iloc[0, 1]  

df1.to_csv("/Users/victoragbara/Downloads/civ_jobs_table.csv", index=False)


print(df1.head()) 
print(first_row)
print(cell)
