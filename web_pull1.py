from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
import pandas as pd
from bs4 import BeautifulSoup
import time

# Set up the Selenium driver
options = webdriver.ChromeOptions()
options.add_argument('--headless')  # Run in background
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)

# Load the page
url = "https://www.federalpay.org/jobs/gs"
driver.get(url)
time.sleep(3)  # wait for JS to load content

# Extract page source and parse with BeautifulSoup
soup = BeautifulSoup(driver.page_source, 'lxml')
tables = soup.find_all("table")

if tables:
    df = pd.read_html(str(tables))[0]
    df.to_csv("/Users/victoragbara/Downloads/civ_jobs_table.csv", index=False)
    print(df.head())
else:
    print("No tables found after JS render.")

driver.quit()
