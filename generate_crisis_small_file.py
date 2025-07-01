import numpy as np
import random

# Path to the original CrisisMMD large file
crisis_original_file_path = 'data_dump/all_images_data_dump.npy'

# Load the original CrisisMMD .npy file
crisis_data = np.load(crisis_original_file_path, allow_pickle=True)

# Randomly select 50 keys if the data is a dictionary
sample_size = 50
selected_keys = random.sample(list(crisis_data.keys()), sample_size)

# Create a new dictionary with the selected keys
selected_samples = {key: crisis_data[key] for key in selected_keys}

# Save the selected samples
crisis_small_file_path = 'data_dump/small_crisis_images_data_dump.npy'
np.save(crisis_small_file_path, selected_samples)

print(f"Selected samples saved to {crisis_small_file_path}")
