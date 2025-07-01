import numpy as np
import random
# Path to the original DMD large file
dmd_original_file_path = 'data_dump/dmd_images_data_dump.npy'

# Load the original DMD .npy file
dmd_data = np.load(dmd_original_file_path, allow_pickle=True)

sample_size = 50
selected_keys = random.sample(list(dmd_data.keys()), sample_size)

# Create a new dictionary with the selected keys
selected_samples = {key: dmd_data[key] for key in selected_keys}


# Save the selected samples
dmd_small_file_path = 'data_dump/small_dmd_images_data_dump.npy'
np.save(dmd_small_file_path, selected_samples)

print(f"Selected samples saved to {dmd_small_file_path}")