import numpy as np
import pickle
import pandas as pd
from tqdm import tqdm
import time
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.sequence import pad_sequences
from tensorflow.keras.preprocessing import image
from keras.applications.vgg16 import preprocess_input
import aidrtokenize
import os
import traceback

# Task and tokenizer
TASK = "severity"  # Options: "informative", "humanitarian", "severity"
DMD_METADATA_FILE = "dmd/dmd_metadata.csv"  # CSV with columns: image_path,text_path,label

# Model + label config
TASK_CONFIG = {
    "informative": {
        "model_paths": [
            "model/model_info_x.hdf5",
            "model/model_info_x1.hdf5",
            "model/model_info_x2.hdf5"
        ],
        "label_mapping": {
            0: "not_informative",
            1: "informative"
        }
    },
    "humanitarian": {
        "model_paths": [
            "model/model_x.hdf5",
            "model/model_x1.hdf5",
            "model/model_x2.hdf5"
        ],
        "label_mapping": {
            0: "affected_individuals",
            1: "infrastructure_and_utility_damage",
            2: "not_humanitarian",
            3: "other_relevant_information",
            4: "rescue_volunteering_or_donation_effort"
        }
    },
    "severity": {
        "model_paths": [
            "model/model_severe_x.hdf5",
            "model/model_severe_x1.hdf5",
            "model/model_severe_x2.hdf5"
        ],
        "label_mapping": {
            0: "little_or_no_damage",
            1: "mild_damage",
            2: "severe_damage"
        }
    }
}

# Load tokenizer
with open("model/info_multimodal_paired_agreed_lab.tokenizer", "rb") as handle:
    tokenizer = pickle.load(handle)

# Preprocess text + image
def load_and_preprocess(text_file, image_file, max_seq_length=25):
    start_time = time.time()

    with open(text_file, 'r') as f:
        text = f.read().strip()
    tokens = aidrtokenize.tokenize(text)
    seq = tokenizer.texts_to_sequences([tokens])
    padded_text = pad_sequences(seq, maxlen=max_seq_length, padding='post')

    img = image.load_img(image_file, target_size=(224, 224))
    img_arr = image.img_to_array(img)
    img_arr = np.expand_dims(img_arr, axis=0)
    img_arr = preprocess_input(img_arr)

    elapsed = time.time() - start_time
    return padded_text, img_arr, elapsed

# Load models once
def load_models(model_paths):
    return [load_model(path) for path in model_paths]

# Inference for one sample
def ensemble_predict(models, text, image):
    start_time = time.time()
    preds = [model.predict([image, text], verbose=0) for model in models]
    mean_pred = np.mean(preds, axis=0)
    elapsed = time.time() - start_time
    return mean_pred, elapsed

if __name__ == "__main__":
    df = pd.read_csv(DMD_METADATA_FILE)

    config = TASK_CONFIG[TASK]
    label_mapping = config["label_mapping"]
    reverse_label_map = {v: k for k, v in label_mapping.items()}
    models = load_models(config["model_paths"])

    all_preds = []
    true_labels = []
    preprocessing_times = []
    inference_times = []
    error_log = []

    print(f"Running inference on {len(df)} DMD samples ({TASK})...")

    for idx, row in tqdm(df.iterrows(), total=len(df)):
        image_path = row['image_path']
        text_path = row['text_path']
        label = row['label'] if TASK != "informative" else "unknown"

        if not os.path.isfile(image_path) or not os.path.isfile(text_path):
            all_preds.append("missing")
            true_labels.append(label)
            continue

        try:
            # Preprocess
            text_input, image_input, prep_time = load_and_preprocess(text_path, image_path)
            preprocessing_times.append(prep_time)

            # Inference
            preds, inf_time = ensemble_predict(models, text_input, image_input)
            inference_times.append(inf_time)

            pred_idx = np.argmax(preds[0])
            pred_label = label_mapping[pred_idx]

            all_preds.append(pred_label)
            true_labels.append(label)

        except Exception as e:
            all_preds.append("error")
            true_labels.append(label)
            error_log.append({
                "index": idx,
                "image_path": image_path,
                "text_path": text_path,
                "error": str(e),
                "traceback": traceback.format_exc()
            })
            continue

    # Final timing
    print("\n========================= SUMMARY =========================")
    print(f"Total Samples Evaluated: {len(all_preds)}")
    print(f"Successful Predictions: {len([p for p in all_preds if p not in ['missing', 'error']])}")
    print(f"Errors Encountered: {len(error_log)}")
    print(f"Avg Preprocessing Time per Sample: {np.mean(preprocessing_times):.4f} sec")
    print(f"Avg Inference Time per Sample:     {np.mean(inference_times):.4f} sec")
    print(f"Total Time:                         {np.sum(preprocessing_times) + np.sum(inference_times):.2f} sec")
    print("===========================================================")

    # Save predictions
    df["predicted_label"] = all_preds
    df["true_label"] = true_labels[:len(all_preds)]
    output_path = f"dmd_predictions_{TASK}.csv"
    df.to_csv(output_path, index=False)
    print(f"Saved predictions to: {output_path}")

    # Save error log if needed
    if error_log:
        error_df = pd.DataFrame(error_log)
        error_df.to_csv(f"errors_{TASK}.csv", index=False)
        print(f"Saved error log to: errors_{TASK}.csv")
