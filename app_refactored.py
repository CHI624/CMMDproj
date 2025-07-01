from __future__ import division, print_function
from random import Random
import pickle
import numpy as np
import pandas as pd
import json
import aidrtokenize as aidrtokenize
import data_process_multimodal_pair as data_process
from crisis_data_generator_image_optimized import DataGenerator
from keras.applications.vgg16 import preprocess_input
from keras.models import load_model, Model
from keras_preprocessing.sequence import pad_sequences
import tensorflow as tf
import keras.backend as K
import traceback
from tensorflow.keras.preprocessing import image as keras_image
from flask import Flask, request, render_template, redirect, url_for , jsonify
import matplotlib.pyplot as plt
import matplotlib
import matplotlib.cm as cm
import glob
import os

FAST_BOOT_MODE = False # The loading of the webpage becomes fast if set to be True as we use smaller dataset

matplotlib.use('Agg')

app = Flask(__name__)

# For TESTING
# Predefined list of indices for testing
predefined_indices = [0, 5, 10, 15, 20]
current_index_position = 0  # Global index position
task_available_indices = dict()
# Create a new Random instance with the same seed for reproducibility
rng = Random(7)

# Load performance measures for CrisisMMD
inf = pd.read_csv("performance_measures/informative.csv")
hum = pd.read_csv("performance_measures/humanitarian.csv")
sev = pd.read_csv("performance_measures/severity.csv")

# Model paths (model1 in app.py is model1 'model/model_info_x.hdf5' and model7 is 'model/model_severe_x.hdf5'
# In app.py model 1 is called for both informative and humanitarian tasks, model 7 is called for severity task
MODEL_PATHS = {
    "informative": ['model/model_info_x.hdf5', 'model/model_info_x1.hdf5', 'model/model_info_x2.hdf5'],
    "humanitarian": ['model/model_x.hdf5', 'model/model_x1.hdf5', 'model/model_x2.hdf5'],
    "severity": ['model/model_severe_x.hdf5', 'model/model_severe_x1.hdf5', 'model/model_severe_x2.hdf5'],
    "text_models": {
        "informative": 'model/informativeness_cnn_keras.hdf5',
        "humanitarian": 'model/humanitarian_cnn_keras_09-04-2022_05-10-03.hdf5',
        "severity": 'model/severity_cnn_keras_21-07-2022_08-14-32.hdf5'
    },
    "image_models": {
        "informative": 'model/informative_image.hdf5',
        "humanitarian": 'model/humanitarian_image_vgg16_ferda.hdf5',
        "severity": 'model/severity_image.hdf5'
    }
}

# Load smaller sized dataset if FAST_MODE is True for faster testing and development
if not FAST_BOOT_MODE:
    dmd_images_npy_data = np.load('data_dump/dmd_images_data_dump.npy', allow_pickle=True, mmap_mode='r')
    crisis_mmd_images_npy_data = np.load('data_dump/all_images_data_dump.npy', allow_pickle=True, mmap_mode='r')
    # Load images data for Incidents 1M if available
    incidents_images_npy_data = np.load('data_dump/incidents1m_images_data_dump.npy', allow_pickle=True)
elif FAST_BOOT_MODE:
    crisis_mmd_images_npy_data = np.load('data_dump/small_crisis_images_data_dump.npy', allow_pickle=True)
    dmd_images_npy_data = np.load('data_dump/small_dmd_images_data_dump.npy', allow_pickle=True)
    if not isinstance(crisis_mmd_images_npy_data, dict):
        crisis_mmd_images_npy_data = dict(crisis_mmd_images_npy_data.item())
    if not isinstance(dmd_images_npy_data, dict):
        dmd_images_npy_data = dict(dmd_images_npy_data.item())
    # Load images data for Incidents 1M if available
    incidents_images_npy_data = np.load('data_dump/small_incidents1m_images_data_dump.npy', allow_pickle=True)
    if not isinstance(incidents_images_npy_data, dict):
        incidents_images_npy_data = dict(incidents_images_npy_data.item())

# Load CrisisMMD metadata
crisis_mmd_metadata_paths = {
    "informative": "metadata/task_informative_text_img_agreed_lab_test.tsv",
    "humanitarian": "metadata/task_humanitarian_text_img_agreed_lab_test.tsv",
    "severity": "metadata/task_severity_test.tsv"
}

crisis_mmd_data_dict = {task: pd.read_csv(path, sep="\t") for task, path in crisis_mmd_metadata_paths.items()}

# Preprocess labels for humanitarian task in CrisisMMD
df1 = crisis_mmd_data_dict["humanitarian"]
df1.loc[df1["label"] == "missing_or_found_people", "label"] = "affected_individuals"
df1.loc[df1["label"] == "injured_or_dead_people", "label"] = "affected_individuals"
df1.loc[df1["label"] == "vehicle_damage", "label"] = "infrastructure_and_utility_damage"

# Extract data for CrisisMMD
crisis_mmd_data = {}
for task in ["informative", "humanitarian", "severity"]:
    df = crisis_mmd_data_dict[task]
    crisis_mmd_data[task] = {
        "images": list(df['image'].values),
        "texts": list(df['tweet_text'].values),
        "labels": list(df['label'].values)
    }


# Load dmd metadata
dmd_metadata_path = "dmd/dmd_metadata.csv"

dmd_metadata = pd.read_csv(dmd_metadata_path)

dmd_data = {}
for task in ["informative", "humanitarian", "severity"]:
    crisis_mmd_df = crisis_mmd_data_dict[task]

    # Load text content from the text files
    texts = []
    for text_path in dmd_metadata['text_path'].values:
        try:
            with open(text_path, 'r', encoding='utf-8') as file:
                texts.append(file.read().strip())
        except Exception as e:
            print(f"Error reading file {text_path}: {e}")
            texts.append("")  # Add empty string for missing or unreadable files


    #Note we still pass the labels in crisis_mmd. This is only for the label encoder to map to the right labels
    dmd_data[task] = {
        "images": list(dmd_metadata['image_path'].values),
        "texts": texts,
        "labels": list(crisis_mmd_df['label'].values)
    }

# We'll load data for DMD in a similar manner
DMD_BASE_PATH = "dmd/multimodal"

# Load Incidents1M metadata
incidents_metadata_path = "static/incidents_1m/incidents_metadata.csv"

incidents_metadata = pd.read_csv(incidents_metadata_path)

incidents_data = {}
for task in ["informative", "humanitarian", "severity"]:
    crisis_mmd_df = crisis_mmd_data_dict[task]

    # Load text content from the text files
    texts = []
    for text_path in incidents_metadata['text_path'].values:
        try:
            with open(text_path, 'r', encoding='utf-8') as file:
                texts.append(file.read().strip())
        except Exception as e:
            print(f"Error reading file {text_path}: {e}")
            texts.append("")  # Add empty string for missing or unreadable files


    #Note we still pass the labels in crisis_mmd. This is only for the label encoder to map to the right labels
    incidents_data[task] = {
        "images": list(incidents_metadata['image_path'].values),
        "texts": texts,
        "labels": list(crisis_mmd_df['label'].values)
    }

# We'll load data for Incidents1M in a similar manner
INCIDENTS_BASE_PATH = "static/incidents_1m/multimodal"

print('Models will be loaded as needed.')

def preprocess_input_vgg(x):
    X = np.expand_dims(x, axis=0)
    X = preprocess_input(X)
    return X[0]

def save_image(image_path, heatmap):
    img = keras_image.load_img(image_path)
    img = keras_image.img_to_array(img)
    heatmap = np.uint8(255 * heatmap)
    jet = cm.get_cmap("jet")
    jet_colors = jet(np.arange(256))[:, :3]
    jet_heatmap = jet_colors[heatmap]
    jet_heatmap = keras_image.array_to_img(jet_heatmap)
    jet_heatmap = jet_heatmap.resize((img.shape[1], img.shape[0]))
    jet_heatmap = keras_image.img_to_array(jet_heatmap)
    superimposed_img = jet_heatmap * 0.4 + img
    superimposed_img = keras_image.array_to_img(superimposed_img)
    plt.clf()
    plt.matshow(superimposed_img)
    plt.colorbar()
    plt.savefig('./static/visualize.jpg')

def _get_text_xticks(sentence):
    tokens = [word_.strip() for word_ in sentence.split(' ')]
    return tokens

def _plot_score(vec, pred_text, xticks):
    plt.clf()
    fig = plt.figure(figsize=(5, 4))
    plt.yticks([])
    plt.xticks(range(0, len(vec)), xticks, fontsize=15, rotation='vertical')
    img = plt.imshow([vec], vmin=0, vmax=1, origin="lower")
    plt.colorbar()
    plt.savefig('./static/text.jpg')

def load_models(task):
    models = [load_model(path) for path in MODEL_PATHS[task]]
    return models

def run_predictions(dataset_name, data, images_npy_data, task, indices, model_index):
    # Get image and text file lists
    image_file_list = []
    text_file_list = []
    for index in indices:
        image_file_list.append(data[task]["images"][index])
        text_file_list.append(data[task]["texts"][index])

    # Only CrisisMMD has predefined labels; for DMD or Incidents1M, this will be set to None
    if dataset_name == "CrisisMMD":
        labels = []
        for index in indices:
            labels.append(data[task]["labels"][index])
    else:
        labels = None
    print("Labels in run_predictions:", labels)
    tokenizer = pickle.load(open("model/info_multimodal_paired_agreed_lab.tokenizer", "rb"))

    test_x, test_image_list, test_y, test_le, test_labels = data_process.read_dev_data_multimodal(
        image_file_list, text_file_list, labels, tokenizer, 25, "\t", data[task]["labels"]
    )

    # Create data generator for test images
    params = {
        "max_seq_length": 25,
        "batch_size": 4,
        "n_classes": len(test_labels) if test_labels else 0,
        "shuffle": False
    }
    test_data_generator = DataGenerator(test_image_list, test_x, images_npy_data, test_y, **params)

    # Load models for the specific task
    models = load_models(task)
    output_preds = [model.predict(test_data_generator, verbose=1) for model in models]

    # Sum up the predictions across models
    summed = np.sum(output_preds, axis=0)
    predicted_indices = np.argmax(summed, axis=1)
    output_labels = [test_labels[idx] for idx in predicted_indices]

    print("Predict", output_preds)
    print("Predict", summed)
    # # If using CrisisMMD, map predictions to predefined labels
    # if dataset_name == "CrisisMMD":
    #     output_labels = [test_labels[idx] for idx in predicted_indices]
    # else:
    #     # For DMD, generate dynamic label names based on predictions
    #     output_labels = [f"predicted_label_{idx}" for idx in predicted_indices]

    # Construct result dictionaries for each model output (for UI display)
    result_dicts = []
    for i in range(len(summed)):
        result = {test_labels[j]: summed[i][j] for j in range(len(test_labels))}
        result_dicts.append(result)

    # Ensure model_index is within range
    if model_index >= len(result_dicts):
        model_index = 0

    selected_result = result_dicts[model_index]

    # Model-specific results for display
    m1 = {test_labels[i]: output_preds[0][model_index][i] for i in range(len(test_labels))}
    m2 = {test_labels[i]: output_preds[1][model_index][i] for i in range(len(test_labels))}
    m3 = {test_labels[i]: output_preds[2][model_index][i] for i in range(len(test_labels))}

    return output_labels, selected_result, m1, m2, m3, test_labels


def get_selected_dataset(selected_dataset):
    # Returns appropriate data dictionary and images array for the selected dataset
    if selected_dataset == "dmd":
        # If user selected DMD dataset
        dataset_name = "DMD"
        data = dmd_data
        images_data = dmd_images_npy_data
    elif selected_dataset == "incidents_1m":
        # If user selected Incidents1M dataset
        dataset_name = "INCIDENTS"
        data = incidents_data
        images_data = incidents_images_npy_data
    else:
        # Default or user selected CrisisMMD dataset
        dataset_name = "CrisisMMD"
        data = crisis_mmd_data
        images_data = crisis_mmd_images_npy_data
    return data, images_data, dataset_name

@app.route('/', methods=['GET', 'POST'])
def index():
    # Check both form data and query string for dataset selection
    selected_dataset = request.form.get("datasetOption") or request.args.get("dataset", "crisis_mmd")

    # Get the correct data and images based on the selected dataset
    # first of all, incients_1m seems like there is no labels for the dataset (i.e., informative, humanitarian, severity)
    # secondly, due to the exceeding number of images, we only included N(=10) images per each category (i.e., earthquake, flooded, tornado, tropical_cyclone, wildfire)
    # in other words, the images_npy_data only contains embeddings for only 5*10 images. So we need to limit our indexes between 0 and 50.
    data, images_npy_data, dataset_name = get_selected_dataset(selected_dataset)

    # Generate random indices as before for sample display
    # This comment is an old and naive approach that induces zero vectors when images are not found in npy embedding data
    # random_index1 = rng.randint(0, max(0, len(data["informative"]["images"]) - 4))
    # random_index2 = rng.randint(0, max(0, len(data["humanitarian"]["images"]) - 4))
    # random_index3 = rng.randint(0, max(0, len(data["severity"]["images"]) - 4))

    # This index selection approach focus on only images sources that maps to the embedding vector
    # Therefore, there is no concern about using the default zero vector for unknown images within the selected dataset
    img_paths = images_npy_data.keys()
    informative_idx_lst = [i for i, img in enumerate(data["informative"]["images"]) if img in img_paths]
    humanitarian_idx_lst = [i for i, img in enumerate(data["humanitarian"]["images"]) if img in img_paths]
    severity_idx_lst = [i for i, img in enumerate(data["severity"]["images"]) if img in img_paths]
    if selected_dataset == "incidents_1m":
        random_indices_task1 = rng.sample(informative_idx_lst,4)
        random_indices_task2 = rng.sample(humanitarian_idx_lst,4)
        random_indices_task3 = rng.sample(severity_idx_lst,4)
    else:
        random_index1 = rng.choice(informative_idx_lst)
        random_index2 = rng.choice(humanitarian_idx_lst)
        random_index3 = rng.choice(severity_idx_lst)

        index = min([random_index1, random_index2, random_index3])
        random_indices_task1 = [index + i for i in range(4)]
        random_indices_task2 = [index + i for i in range(4)]
        random_indices_task3 = [index + i for i in range(4)]

    task_available_indices["informative"] = random_indices_task1
    task_available_indices["humanitarian"] = random_indices_task2
    task_available_indices["severity"] = random_indices_task3

    # in fact, there should be a separate render where the task option is selected, but the prediction is not made
    # in this scenario, the random_indices should be actually will vary among random_indices_task1, random_indices_task2, and random_indices_task3
    # right now, we just assume that the indices of random_indices_task1 also exists in other image dataset (i.e., random_indices_task2 and random_indices_task3)
    # and just use this default indices to load images for task 2 and task 3 (it might fail if there is no indices available in other tasks)
    return render_template(
        'index.html',
        datasetOption=selected_dataset,  # Pass selected dataset to template
        img1=data["informative"]["images"],
        img2=data["humanitarian"]["images"],
        img3=data["severity"]["images"],
        text1=data["informative"]["texts"],
        text2=data["humanitarian"]["texts"],
        text3=data["severity"]["texts"],
        radio=1, m1={}, m2={}, m3={}, l1=0, l2=0, l3=0,
        indices=random_indices_task1, output=None, result={}, len=0, labels=[], i="0"
    )
@app.route('/index.html')
def redirect_to_home():
    return redirect(url_for('index'))  # Redirects to '/'


# @app.route('/', methods=['GET', 'POST'])
# def index():
#     selected_dataset = request.form.get("datasetOption", "crisis_mmd")
#     data, images_npy_data, dataset_name = get_selected_dataset(selected_dataset)

#     # Debug: Print lengths of each image and text list
#     print(f"{dataset_name} - Dataset lengths:")
#     for task_key in data.keys():
#         print(f"Length of data['{task_key}']['images']: {len(data[task_key]['images'])}")

#     # Generate random indices using the local RNG instance
#     random_index1 = rng.randint(0, max(0, len(data["informative"]["images"]) - 4))
#     random_index2 = rng.randint(0, max(0, len(data["humanitarian"]["images"]) - 4))
#     random_index3 = rng.randint(0, max(0, len(data["severity"]["images"]) - 4))

#     indices = [random_index1, random_index2, random_index3]
#     indices = [idx for idx in indices if idx >= 0]  # Ensure non-negative
#     index = min(indices) if indices else 0

#     # Debug: Print the selected random indices
#     print(f"Selected random indices for {dataset_name}:")
#     print(f"Random Index 1 (Informative): {random_index1}")
#     print(f"Random Index 2 (Humanitarian): {random_index2}")
#     print(f"Random Index 3 (Severity): {random_index3}")
#     print(f"Final index used for slicing: {index}")

#     # Debug: Print the selected images and texts for display
#     print(f"{dataset_name} - Images and texts selected for display:")
#     for task_key in data.keys():
#         print(f"Images ({task_key}): {data[task_key]['images'][index:index+4]}")
#         print(f"Texts ({task_key}): {data[task_key]['texts'][index:index+4]}")

#     return render_template(
#         'index.html',
#         datasetOption=dataset_name,
#         img1=data["informative"]["images"],
#         img2=data["humanitarian"]["images"],
#         img3=data["severity"]["images"],
#         text1=data["informative"]["texts"],
#         text2=data["humanitarian"]["texts"],
#         text3=data["severity"]["texts"],
#         radio=1, m1={}, m2={}, m3={}, l1=0, l2=0, l3=0,
#         index=index, output=None, result={}, len=0, labels=[], i="0"
#     )

@app.route('/predict', methods=['POST'])
def predict():
    if request.method == 'POST':

        selected_dataset = request.form.get("datasetOption", "crisis_mmd")
        print("Request form:", request.form)
        print("Selected dataset in /predict:", selected_dataset)
        data, images_npy_data, dataset_name = get_selected_dataset(selected_dataset)

        print(f"Selected dataset (Nickname): {dataset_name}")
        task_option = request.form['inlineRadioOptions']
        task_map = {"option1": "informative", "option2": "humanitarian", "option3": "severity"}
        task = task_map.get(task_option, "informative")

        indices = request.form.getlist('indices[]')  # Gets a list of values as strings
        indices = [int(idx) for idx in indices]  # Convert to integers
        model_index = int(request.form['index1'])

        output_labels, result, m1, m2, m3, test_labels = run_predictions(dataset_name, data, images_npy_data, task, indices, model_index)
        m1 = dict(sorted(m1.items(), reverse=True))
        m2 = dict(sorted(m2.items(), reverse=True))
        m3 = dict(sorted(m3.items(), reverse=True))
        result = dict(sorted(result.items(), reverse=True))

        return render_template(
            'index.html',
            datasetOption=selected_dataset,
            img1=data["informative"]["images"],
            img2=data["humanitarian"]["images"],
            img3=data["severity"]["images"],
            text1=data["informative"]["texts"],
            text2=data["humanitarian"]["texts"],
            text3=data["severity"]["texts"],
            radio={"informative": 1, "humanitarian": 2, "severity": 3}.get(task, 1),
            m1=m1, m2=m2, m3=m3, l1=len(m1), l2=len(m2), l3=len(m3),
            indices=indices, output=output_labels, result=result, len=len(result), labels=test_labels, i=model_index
        )

@app.route('/details', methods=['POST'])
def details():
    try:
        print("Entered /details route")

        selected_dataset = request.form.get("datasetOption", "crisis_mmd")
        data, images_npy_data, dataset_name = get_selected_dataset(selected_dataset)

        indices = request.form.getlist('indices[]')  # Gets a list of values as strings
        indices = [int(idx) for idx in indices]  # Convert to integers
        model_index = int(request.form.get('index1', 0))
        task_option = request.form.get('inlineRadioOptions', '')
        task_map = {"option1": "informative", "option2": "humanitarian", "option3": "severity"}
        task = task_map.get(task_option, "informative")
        print(f"{dataset_name} details - index: {indices}, model_index: {model_index}, task_option: {task_option}")

        image_file_list = []
        text_file_list = []
        for index in indices:
            image = data[task]["images"][index]
            text = data[task]["texts"][index]
            image_file_list.append(image)
            text_file_list.append(text)
        labels = []
        if dataset_name == "CrisisMMD":
            for index in indices:
                label = data[task]["labels"][index]
                labels.append(label)
        else:
            labels = None
        # Parse 'selected_result' from form data
        result_string = request.form.get('index2', '{}')
        print(f"Result string: {result_string}")

        # Safely parse the result string into a dictionary
        try:
            selected_result = json.loads(result_string.replace("'", '"'))
        except json.JSONDecodeError as e:
            print(f"JSON decoding error: {e}")
            selected_result = {}
        print(f"Selected result: {selected_result}")

        tokenizer_filenames = {
            "informative": "model/informativeness_cnn_keras_09-04-2022_04-26-49.tokenizer",
            "humanitarian": "model/humanitarian_cnn_keras_09-04-2022_05-10-03.tokenizer",
            "severity": "model/severity_cnn_keras_21-07-2022_08-14-32.tokenizer"
        }
        tokenizer_path = tokenizer_filenames.get(task)
        if not tokenizer_path or not os.path.isfile(tokenizer_path):
            error_message = f"Tokenizer file not found: {tokenizer_path}"
            print(error_message)
            return error_message, 500

        with open(tokenizer_path, "rb") as handle:
            tokenizer = pickle.load(handle)
        print("Tokenizer loaded successfully.")

        test_x, _, test_y, test_le, test_labels = data_process.read_dev_data_multimodal(
            image_file_list, text_file_list, labels, tokenizer, 25, "\t", data[task]["labels"]
        )

        text_model_path = MODEL_PATHS["text_models"][task]
        if not os.path.isfile(text_model_path):
            error_message = f"Text model file not found: {text_model_path}"
            print(error_message)
            return error_message, 500
        text_model = load_model(text_model_path)
        print("Text model loaded successfully.")

        output1 = text_model.predict(test_x, batch_size=128, verbose=1)

        test_images = []
        for img_name in image_file_list:
            if images_npy_data is not None and img_name in images_npy_data:
                image_data = images_npy_data.get(img_name)
                if image_data is not None:
                    if len(image_data.shape) == 4 and image_data.shape[0] == 1:
                        image_data = np.squeeze(image_data, axis=0)
                    if image_data.shape != (224, 224, 3):
                        img = keras_image.array_to_img(image_data)
                        img = img.resize((224, 224))
                        image_data = keras_image.img_to_array(img)
                        image_data = preprocess_input(image_data)
                else:
                    print(f"Image data not found for: {img_name} in {dataset_name} dataset.")
                    image_data = np.zeros((224, 224, 3), dtype=np.float32)
            else:
                # If image data is not found in dictionary or dictionary is None
                # Load from disk if possible
                img_path = img_name if os.path.isfile(img_name) else os.path.join('/Users/umanggoel/Desktop/test/demo_assets1/demo_crisis_mmd/', img_name)
                if not os.path.isfile(img_path):
                    print(f"Image file not found: {img_path}")
                    image_data = np.zeros((224, 224, 3), dtype=np.float32)
                else:
                    img = keras_image.load_img(img_path, target_size=(224, 224))
                    image_data = keras_image.img_to_array(img)
                    image_data = preprocess_input(image_data)
            test_images.append(image_data)

        test_images = np.array(test_images)
        print(f"Image data prepared for details. Shape: {test_images.shape}")

        image_model_path = MODEL_PATHS["image_models"][task]
        if not os.path.isfile(image_model_path):
            error_message = f"Image model file not found: {image_model_path}"
            print(error_message)
            return error_message, 500
        image_model = load_model(image_model_path)
        print("Image model loaded successfully for details.")

        output2 = image_model.predict(test_images, batch_size=128, verbose=1)

        # If `test_labels` is empty (which could happen with DMD or INCIDENTS if no labels), provide a default
        if not test_labels:
            test_labels = [f'label_{i}' for i in range(len(output1[model_index]))]

        m1 = {test_labels[i]: float(output1[model_index][i]) for i in range(len(test_labels))}
        m2 = {test_labels[i]: float(output2[model_index][i]) for i in range(len(test_labels))}
        output_label_text = test_labels[np.argmax(output1[model_index])] if test_labels else 'unknown'
        output_label_image = test_labels[np.argmax(output2[model_index])] if test_labels else 'unknown'

        performance_data = {"informative": inf, "humanitarian": hum, "severity": sev}.get(task, pd.DataFrame())

        result = selected_result
        m1 = dict(sorted(m1.items(), reverse=True))
        m2 = dict(sorted(m2.items(), reverse=True))
        m3 = selected_result # somebody set this as selected result previously, just following it...
        m3 = dict(sorted(m3.items(), reverse=True))

        print("About to render result.html template from details")

        return render_template(
            'result.html',
            datasetOption=selected_dataset,
            result=result,
            m1=m1,
            m2=m2,
            m3=m3,
            img=data[task]["images"],
            text=data[task]["texts"],
            labels=test_labels,
            output1=output_label_image,
            output=output_label_text,
            l1=len(m1),
            l2=len(m2),
            l3=len(selected_result),
            column_names=performance_data.columns.values if not performance_data.empty else [],
            row_data=list(performance_data.values.tolist()) if not performance_data.empty else [],
            model_name=task.capitalize(),
            indices=indices,
            output2=request.form.get('index3', ''),
        )

    except Exception as e:
        print(f"Error in /details route: {e}")
        traceback.print_exc()
        return "An error occurred in the details route.", 500

@app.route('/visualize', methods=['POST'])
def visualize():
    try:
        selected_dataset = request.form.get("datasetOption", "crisis_mmd")
        data, images_npy_data, dataset_name = get_selected_dataset(selected_dataset)

        indices = request.form.getlist('indices[]')  # Gets a list of values as strings
        indices = [int(idx) for idx in indices]  # Convert to integers
        # index_form = int(request.form.get('index', 0))
        index_form = indices[0]
        index1_form = int(request.form.get('index1', 0))
        index = index_form + index1_form

        task_option = request.form.get('inlineRadioOptions', '')
        task_map = {"option1": "informative", "option2": "humanitarian", "option3": "severity"}
        task = task_map.get(task_option, "informative")

        if task is None:
            print("Invalid task option for visualize.")
            return "Invalid task option.", 400

        if index < 0 or index >= len(data[task]["images"]):
            print(f"Index {index} out of range for {task} in {dataset_name} dataset.")
            return "Index out of range.", 400

        if not data[task]["images"]:
            print(f"No images available for task {task} in {dataset_name} dataset.")
            return "No images available for the selected task and dataset.", 500

        image_path = data[task]["images"][index]
        text = data[task]["texts"][index]
        label = data[task]["labels"][index] if index < len(data[task]["labels"]) else "unknown"

        print(f"Visualization for {dataset_name} - Task: {task}, Index: {index}")
        print(f"Image Path: {image_path}")
        print(f"Text: {text}")
        print(f"Label: {label}")

        if not os.path.isfile(image_path):
            # If image path is not found directly, attempt to reconstruct or default path
            image_path_on_disk = os.path.join('/Users/umanggoel/Desktop/test/demo_assets1/demo_crisis_mmd/static/', image_path)
            if not os.path.isfile(image_path_on_disk):
                print(f"Image file not found for visualization: {image_path}")
                return f"Image file not found for visualization: {image_path}", 500
            image_path = image_path_on_disk
        img = keras_image.load_img(image_path, target_size=(224, 224))
        x = keras_image.img_to_array(img)
        x = np.expand_dims(x, axis=0)
        x = preprocess_input(x)
        print(f"Image shape after preprocessing: {x.shape}")
        txt = aidrtokenize.tokenize(text)
        tokenizer = pickle.load( open("model/info_multimodal_paired_agreed_lab.tokenizer", "rb"))
        sequences = tokenizer.texts_to_sequences(txt)
        x_pad_txt = pad_sequences(sequences, maxlen=25, padding='post')

        if task == "informative":
            model = load_models(task)[0]
            conv_layer = model.get_layer("block5_conv3")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            print("Model and heatmap model loaded successfully.")
            # Get gradient of the winner class w.r.t. the output of the (last) conv. layer
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                pooled_grads = K.mean(grads, axis=(0, 1, 2))
            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            heatmap=np.squeeze(heatmap)
            save_image(image_path,heatmap)
            conv_layer = model.get_layer("concatenate")

            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                grads /= (np.max(grads) + K.epsilon())
                pooled_grads = K.mean(grads, axis=(0, 1, 2))
            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            _grad_CAM=tf.squeeze(heatmap)
            _plot_score(vec=_grad_CAM[:len(_get_text_xticks(txt))], pred_text="Informative", xticks=_get_text_xticks(txt))

        elif task == "humanitarian":
            # model = load_models(task)[0]
            # conv_layer = model.get_layer("block5_conv3")
            # heatmap_model = Model([model.inputs], [conv_layer.output, model.output])

            # in fact, model_x.hdf5 has some encoding issue as it was trained only on
            # input with the first dimension size is 1 (I guess it is squeezed or batched)
            # if we squeeze it or batch the input into dimension 1, it will not be able to
            # show the gradient on each token. Therefore, we substitute the model with the
            # architecture that we desire to use for gradient visualization
            model = load_model('model/model_x1.hdf5')
            conv_layer = model.get_layer("block5_conv3")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            print("Model and heatmap model loaded successfully.")

            # Get gradient of the winner class w.r.t. the output of the (last) conv. layer
            # flat_sequence = [item for sublist in sequences for item in sublist]
            # x_pad_flat_txt = pad_sequences([flat_sequence], maxlen=25, padding='post')
            # print("Flat Input Shapes: ", x.shape, x_pad_txt.shape)

            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                pooled_grads = K.mean(grads, axis=(0, 1, 2))

            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat

            heatmap=np.squeeze(heatmap)
            print(heatmap.shape)

            save_image(image_path,heatmap)
            conv_layer = model.get_layer("concatenate")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            # This is the gradient of the predicted class with regard to
            # the output feature map of selected block
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                grads /= (np.max(grads) + K.epsilon())
                pooled_grads = K.mean(grads, axis=(0, 1, 2))
            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            _grad_CAM=tf.squeeze(heatmap)
            print(_grad_CAM.shape)

            _plot_score(vec=_grad_CAM[:len(_get_text_xticks(txt))], pred_text="Informative", xticks=_get_text_xticks(txt))

        elif task == "severity":
            model = load_models(task)[0]
            conv_layer = model.get_layer("block5_conv3")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            print("Model and heatmap model loaded successfully.")
            # Get gradient of the winner class w.r.t. the output of the (last) conv. layer
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                pooled_grads = K.mean(grads, axis=(0, 1, 2))
            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            heatmap=np.squeeze(heatmap)
            save_image(image_path,heatmap)
            conv_layer = model.get_layer("concatenate")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            # This is the gradient of the predicted class with regard to
            # the output feature map of selected block
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                grads /= (np.max(grads) + K.epsilon())
                pooled_grads = K.mean(grads, axis=(0, 1, 2))

            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            _grad_CAM=tf.squeeze(heatmap)

            _plot_score(vec=_grad_CAM[:len(_get_text_xticks(txt))], pred_text="Informative", xticks=_get_text_xticks(txt))
        else:
            raise ValueError(f"Invalid task option for visualization: {task}")

        print("Finished Visualization Saving Under Static Folder text.jpg and visualize.jpg")
        return render_template(
            'visualize.html',
            datasetOption=selected_dataset,
            image=image_path,
            text=text,
            img1=data["informative"]["images"],
            img2=data["humanitarian"]["images"],
            img3=data["severity"]["images"],
            text1=data["informative"]["texts"],
            text2=data["humanitarian"]["texts"],
            text3=data["severity"]["texts"],
            radio={"informative": 1, "humanitarian": 2, "severity": 3}.get(task, 1),
            m1={}, m2={}, m3={}, l1=0, l2=0, l3=0,
            indices=indices, output=None, result={}, len=0, labels=[], i="0"
        )
    except Exception as e:
        print(f"[ERROR] Error in /visualize route: {e}")
        traceback.print_exc()
        return "An error occurred in the visualize route.", 500
       
def clear_keras_session():
    tf.keras.backend.clear_session()

@app.route('/user_data', methods=['GET', 'POST'])
def user_data():
    try:
        if request.method == 'POST':
            from werkzeug.utils import secure_filename

            # 1) Retrieve inputs
            user_image = request.files.get('userImage')
            user_text = request.form.get('userText', '').strip()
            task_option = request.form.get('inlineRadioOptions', 'option1')

            # Map the radio to the actual task
            task_map = {
                "option1": "informative",
                "option2": "humanitarian",
                "option3": "severity"
            }
            task = task_map.get(task_option, "informative")

            if not user_image or user_image.filename == '':
                return "No image uploaded.", 400
            if not user_text:
                return "No text provided.", 400

            # 2) Save the user image
            filename = secure_filename(user_image.filename)
            save_dir = os.path.join('static', 'uploads')
            os.makedirs(save_dir, exist_ok=True)
            image_path = os.path.join(save_dir, filename)
            user_image.save(image_path)
            user_image_url = url_for('static', filename=f'uploads/{filename}')

            # 3) Preprocess the text
            tokenizer_path = "model/info_multimodal_paired_agreed_lab.tokenizer"
            if not os.path.isfile(tokenizer_path):
                return "Tokenizer file not found.", 500

            tokenizer = pickle.load(open(tokenizer_path, "rb"))
            tokens = aidrtokenize.tokenize(user_text)
            seqs = tokenizer.texts_to_sequences([tokens])
            padded_seq = pad_sequences(seqs, maxlen=25, padding='post')

            # 4) Preprocess the image
            img = keras_image.load_img(image_path, target_size=(224, 224))
            img_array = keras_image.img_to_array(img)
            img_array = np.expand_dims(img_array, axis=0)
            img_array = preprocess_input(img_array)

            # 5) Define labels and select models based on the task
            if task == "informative":
                test_labels = ["not_informative", "informative"]
                model_files = [
                    'model/model_info_x.hdf5',
                    'model/model_info_x1.hdf5',
                    'model/model_info_x2.hdf5'
                ]
            elif task == "humanitarian":
                test_labels = ["not_humanitarian",
                               "infrastructure_and_utility_damage",
                               "affected_individuals",
                               "other_relevant_info"]
                model_files = [
                    'model/model_x.hdf5',
                    'model/model_x1.hdf5',
                    'model/model_x2.hdf5'
                ]
            else:  # severity
                test_labels = ["not_severe", "mild", "severe"]
                model_files = [
                    'model/model_severe_x.hdf5',
                    'model/model_severe_x1.hdf5',
                    'model/model_severe_x2.hdf5'
                ]

            # Load the models for the selected task
            models = []
            for model_path in model_files:
                if not os.path.isfile(model_path):
                    return f"Model file not found: {model_path}", 500
                mdl = load_model(model_path)
                models.append(mdl)

            # 6) Generate predictions with each model (using the single sample)
            output_preds = []
            for mdl in models:
                preds = mdl.predict([img_array, padded_seq], verbose=1)
                output_preds.append(preds)
            output_preds = np.array(output_preds)

            # 7) Sum predictions to form the ensemble output
            summed_preds = np.sum(output_preds, axis=0)
            predicted_index = np.argmax(summed_preds, axis=1)[0]
            output_label = test_labels[predicted_index]

            # 8) Build dictionary for ensemble display
            result = {lbl: float(summed_preds[0][i]) for i, lbl in enumerate(test_labels)}

            # 9) Build per-model prediction breakdown
            m1, m2, m3 = {}, {}, {}
            if len(models) >= 3:
                for i, lbl in enumerate(test_labels):
                    m1[lbl] = float(output_preds[0][0][i])
                    m2[lbl] = float(output_preds[1][0][i])
                    m3[lbl] = float(output_preds[2][0][i])

            # For the image model prediction (if needed)
            predicted_index_m2 = np.argmax(output_preds[1][0])
            output1 = test_labels[predicted_index_m2]

            # For the causal/ensemble model prediction (the third model)
            predicted_index_m3 = np.argmax(output_preds[2][0])
            output2 = test_labels[predicted_index_m3]

            m1 = dict(sorted(m1.items(), reverse=True))
            m2 = dict(sorted(m2.items(), reverse=True))
            m3 = dict(sorted(m3.items(), reverse=True))
            result = dict(sorted(result.items(), reverse=True))

            # 10) Render template with results
            return render_template(
                "user_data.html",
                result=result,
                output1=output1,            # predicted label for the image model
                output2=output2,
                output_label=output_label,
                m1=m1,
                m2=m2,
                m3=m3,
                l1=len(m1),
                l2=len(m2),
                l3=len(m3),
                len_ensemble=len(result),
                labels=test_labels,
                user_image_url=user_image_url,
                user_text=user_text,
                radio=task_option
            )

        # GET => Render empty placeholders
        return render_template(
            "user_data.html",
            result={},
            output_label="",
            m1={}, m2={}, m3={},
            l1=0, l2=0, l3=0,
            len_ensemble=0,
            labels=[],
            user_image_url="",
            user_text="",
            radio="option1"
        )

    except Exception as e:
        print(f"[ERROR] Error in /user_data route: {e}")
        traceback.print_exc()
        return "An error occurred in user_data route.", 500


@app.route('/user_data_result', methods=['POST'])
def user_data_result():
    try:
        # 1) Retrieve inputs
        user_image_path = request.form.get('user_image_url', '')
        user_text = request.form.get('user_text', '')
        task_option = request.form.get('radio', 'option1')

        # Map the radio to the actual task
        task_map = {
            "option1": "informative",
            "option2": "humanitarian",
            "option3": "severity"
        }
        task = task_map.get(task_option, "informative")

        # 3) Preprocess the text
        tokenizer_path = "model/info_multimodal_paired_agreed_lab.tokenizer"
        if not os.path.isfile(tokenizer_path):
            return "Tokenizer file not found.", 500

        tokenizer = pickle.load(open(tokenizer_path, "rb"))
        tokens = aidrtokenize.tokenize(user_text)
        seqs = tokenizer.texts_to_sequences([tokens])
        padded_seq = pad_sequences(seqs, maxlen=25, padding='post')

        # 4) Preprocess the image
        img = keras_image.load_img("."+user_image_path, target_size=(224, 224))
        img_array = keras_image.img_to_array(img)
        img_array = np.expand_dims(img_array, axis=0)
        img_array = preprocess_input(img_array)

        # 5) Define labels and select models based on the task
        if task == "informative":
            test_labels = ["not_informative", "informative"]
            model_files = [
                'model/model_info_x.hdf5',
                'model/model_info_x1.hdf5',
                'model/model_info_x2.hdf5'
            ]
        elif task == "humanitarian":
            test_labels = ["not_humanitarian",
                           "infrastructure_and_utility_damage",
                           "affected_individuals",
                           "other_relevant_info"]
            model_files = [
                'model/model_x.hdf5',
                'model/model_x1.hdf5',
                'model/model_x2.hdf5'
            ]
        else:  # severity
            test_labels = ["not_severe", "mild", "severe"]
            model_files = [
                'model/model_severe_x.hdf5',
                'model/model_severe_x1.hdf5',
                'model/model_severe_x2.hdf5'
            ]

        # Load the models for the selected task
        models = []
        for model_path in model_files:
            if not os.path.isfile(model_path):
                return f"Model file not found: {model_path}", 500
            mdl = load_model(model_path)
            models.append(mdl)

        # 6) Generate predictions with each model (using the single sample)
        output_preds = []
        for mdl in models:
            preds = mdl.predict([img_array, padded_seq], verbose=1)
            output_preds.append(preds)
        output_preds = np.array(output_preds)

        # 7) Sum predictions to form the ensemble output
        summed_preds = np.sum(output_preds, axis=0)
        predicted_index = np.argmax(summed_preds, axis=1)[0]
        output_label = test_labels[predicted_index]

        # 8) Build dictionary for ensemble display
        result = {lbl: float(summed_preds[0][i]) for i, lbl in enumerate(test_labels)}

        # 9) Build per-model prediction breakdown
        m1, m2, m3 = {}, {}, {}
        if len(models) >= 3:
            for i, lbl in enumerate(test_labels):
                m1[lbl] = float(output_preds[0][0][i])
                m2[lbl] = float(output_preds[1][0][i])
                m3[lbl] = float(output_preds[2][0][i])

        # For the image model prediction (if needed)
        predicted_index_m2 = np.argmax(output_preds[1][0])
        output1 = test_labels[predicted_index_m2]

        # For the causal/ensemble model prediction (the third model)
        predicted_index_m3 = np.argmax(output_preds[2][0])
        output2 = test_labels[predicted_index_m3]

        m1 = dict(sorted(m1.items(), reverse=True))
        m2 = dict(sorted(m2.items(), reverse=True))
        m3 = dict(sorted(m3.items(), reverse=True))
        result = dict(sorted(result.items(), reverse=True))
        performance_data = {"informative": inf, "humanitarian": hum, "severity": sev}.get(task, pd.DataFrame())

        # Render the result page.
        # In your template, you’ll use:
        #   - m1 for the Text Model (first pie chart)
        #   - m2 for the Image Model (second pie chart)
        #   - m3 for the Causal/Ensemble model (third pie chart)
        return render_template(
            "user_data_result.html",
            user_image_url="."+user_image_path,
            user_text=user_text,
            result=result,
            m1=m1,
            m2=m2,
            m3=m3,
            labels=test_labels,
            column_names=performance_data.columns.values if not performance_data.empty else [],
            row_data=list(performance_data.values.tolist()) if not performance_data.empty else [],
            task_name=task.capitalize(),
        )
    except Exception as e:
        print("[ERROR] user_data_result route:", e)
        traceback.print_exc()
        return "An error occurred in user_data_result route.", 500


@app.route('/user_data_visualise', methods=['POST'])
def user_data_visualise():
    try:
        # Retrieve the saved image URL, user text, and task option from the form.
        user_image_url = request.form.get("user_image_url", "")
        user_text = request.form.get("user_text", "")
        task_option = request.form.get("radio", "option1")  # e.g., "option1" for informative

        # Map the radio option to the actual task.
        task_map = {"option1": "informative", "option2": "humanitarian", "option3": "severity"}
        task = task_map.get(task_option, "informative")

        # Construct the local file path.
        image_filename = user_image_url.split("uploads/")[-1]
        image_path = os.path.join("static", "uploads", image_filename)
        if not os.path.isfile(image_path):
            print(f"User image file not found: {image_path}")
            return f"User image file not found: {image_path}", 500

        # Preprocess the image.
        img = keras_image.load_img(image_path, target_size=(224, 224))
        x = keras_image.img_to_array(img)
        x = np.expand_dims(x, axis=0)
        x = preprocess_input(x)
        print(f"Image shape after preprocessing: {x.shape}")
        txt = aidrtokenize.tokenize(user_text)
        tokenizer = pickle.load( open("model/info_multimodal_paired_agreed_lab.tokenizer", "rb"))
        sequences = tokenizer.texts_to_sequences(txt)
        x_pad_txt = pad_sequences(sequences, maxlen=25, padding='post')

        if task == "informative":
            model = load_models(task)[0]
            conv_layer = model.get_layer("block5_conv3")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            print("Model and heatmap model loaded successfully.")
            # Get gradient of the winner class w.r.t. the output of the (last) conv. layer
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                pooled_grads = K.mean(grads, axis=(0, 1, 2))
            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            heatmap=np.squeeze(heatmap)
            save_image(image_path,heatmap)
            conv_layer = model.get_layer("concatenate")

            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                grads /= (np.max(grads) + K.epsilon())
                pooled_grads = K.mean(grads, axis=(0, 1, 2))
            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            _grad_CAM=tf.squeeze(heatmap)
            _plot_score(vec=_grad_CAM[:len(_get_text_xticks(txt))], pred_text="Informative", xticks=_get_text_xticks(txt))

        elif task == "humanitarian":
            # model = load_models(task)[0]
            # conv_layer = model.get_layer("block5_conv3")
            # heatmap_model = Model([model.inputs], [conv_layer.output, model.output])

            # in fact, model_x.hdf5 has some encoding issue as it was trained only on
            # input with the first dimension size is 1 (I guess it is squeezed or batched)
            # if we squeeze it or batch the input into dimension 1, it will not be able to
            # show the gradient on each token. Therefore, we substitute the model with the
            # architecture that we desire to use for gradient visualization
            model = load_model('model/model_x1.hdf5')
            conv_layer = model.get_layer("block5_conv3")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            print("Model and heatmap model loaded successfully.")

            # Get gradient of the winner class w.r.t. the output of the (last) conv. layer
            # flat_sequence = [item for sublist in sequences for item in sublist]
            # x_pad_flat_txt = pad_sequences([flat_sequence], maxlen=25, padding='post')
            # print("Flat Input Shapes: ", x.shape, x_pad_txt.shape)

            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                pooled_grads = K.mean(grads, axis=(0, 1, 2))

            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat

            heatmap=np.squeeze(heatmap)
            print(heatmap.shape)

            save_image(image_path,heatmap)
            conv_layer = model.get_layer("concatenate")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            # This is the gradient of the predicted class with regard to
            # the output feature map of selected block
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                grads /= (np.max(grads) + K.epsilon())
                pooled_grads = K.mean(grads, axis=(0, 1, 2))
            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            _grad_CAM=tf.squeeze(heatmap)
            print(_grad_CAM.shape)

            _plot_score(vec=_grad_CAM[:len(_get_text_xticks(txt))], pred_text="Informative", xticks=_get_text_xticks(txt))

        elif task == "severity":
            model = load_models(task)[0]
            conv_layer = model.get_layer("block5_conv3")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            print("Model and heatmap model loaded successfully.")
            # Get gradient of the winner class w.r.t. the output of the (last) conv. layer
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                pooled_grads = K.mean(grads, axis=(0, 1, 2))
            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            heatmap=np.squeeze(heatmap)
            save_image(image_path,heatmap)
            conv_layer = model.get_layer("concatenate")
            heatmap_model = Model([model.inputs], [conv_layer.output, model.output])
            # This is the gradient of the predicted class with regard to
            # the output feature map of selected block
            with tf.GradientTape() as gtape:
                conv_output, predictions = heatmap_model([x,x_pad_txt])
                loss = predictions[:, np.argmax(predictions[0])]
                grads = gtape.gradient(loss, conv_output)
                grads /= (np.max(grads) + K.epsilon())
                pooled_grads = K.mean(grads, axis=(0, 1, 2))

            heatmap = tf.reduce_mean(tf.multiply(pooled_grads, conv_output), axis=-1)
            heatmap = np.maximum(heatmap, 0)
            max_heat = np.max(heatmap)
            if max_heat == 0:
                max_heat = 1e-10
            heatmap /= max_heat
            _grad_CAM=tf.squeeze(heatmap)

            _plot_score(vec=_grad_CAM[:len(_get_text_xticks(txt))], pred_text="Informative", xticks=_get_text_xticks(txt))
        else:
            raise ValueError(f"Invalid task option for visualization: {task}")


        return render_template(
            'user_data_visualise.html',
            user_image_url=user_image_url,
            text=user_text,
            result={},    # default empty dictionary for result
            m1={},        # default empty dict for m1
            m2={},        # default empty dict for m2
            m3={},        # default empty dict for m3
            labels=[],    # default empty list for labels if needed
            output_label=""  # default empty string for output_label if needed
        )

    except Exception as e:
        print(f"[ERROR] Error in /user_data_visualise route: {e}")
        traceback.print_exc()
        return "An error occurred in the user_data_visualise route.", 500

if __name__ == '__main__':
    app.run()#debug=True)
