import numpy as np
np.random.seed(1337)  # for reproducibility

import os
from keras.preprocessing.text import Tokenizer
from tensorflow.keras.preprocessing.sequence import pad_sequences
import sys
from sklearn import preprocessing
import re
import aidrtokenize as aidrtokenize
from collections import Counter
import random
random.seed(1337)

#stop_words = set(stopwords.words('english'))


def file_exist(file_name):
    if os.path.exists(file_name):
        return True
    else:
        return False

def read_stop_words(file_name):
    if(not file_exist(file_name)):
        print("Please check the file for stop words, it is not in provided location "+file_name)
        sys.exit(0)
    stop_words =[]
    with open(file_name, 'rU') as f:
        for line in f:
            line = line.strip()
            if (line == ""):
                continue
            stop_words.append(line)
    return stop_words;

stop_words_file="stop_words/stop_words_english.txt"
stop_words = read_stop_words(stop_words_file)

def clean_str(string):
    """
    Tokenization/string cleaning for all datasets except for SST.
    Original taken from https://github.com/yoonkim/CNN_sentence/blob/master/process_data.py
    """
    string = re.sub(r"[^A-Za-z0-9(),!?\'\`]", " ", string)
    string = re.sub(r"\'s", " \'s", string)
    string = re.sub(r"\'ve", " \'ve", string)
    string = re.sub(r"n\'t", " n\'t", string)
    string = re.sub(r"\'re", " \'re", string)
    string = re.sub(r"\'d", " \'d", string)
    string = re.sub(r"\'ll", " \'ll", string)
    string = re.sub(r",", " , ", string)
    string = re.sub(r"!", " ! ", string)
    string = re.sub(r"\(", " \( ", string)
    string = re.sub(r"\)", " \) ", string)
    string = re.sub(r"\?", " \? ", string)
    string = re.sub(r"\s{2,}", " ", string)
    return string.strip().lower()

def get_tokenizer(data, MAX_NB_WORDS, MAX_SEQUENCE_LENGTH):
    """
    Prepare the data
    """  
    # Finally, vectorize the text samples into a 2D integer tensor
    tokenizer = Tokenizer(nb_words=MAX_NB_WORDS)
    tokenizer.fit_on_texts(data)
    sequences = tokenizer.texts_to_sequences(data)    
    word_index = tokenizer.word_index
    print('Found %s unique tokens.' % len(word_index))    
    data = pad_sequences(sequences, maxlen=MAX_SEQUENCE_LENGTH)    
    print('Shape of data tensor:', data.shape)
    return word_index,tokenizer


def read_train_data_multimodal(data_file, MAX_NB_WORDS, MAX_SEQUENCE_LENGTH, label_index, delim):
    """
    Prepare the data
    """
    data = []
    image_list = []
    lab = []
    with open(data_file, 'rb') as f:
        next(f)
        for line in f:
            line = line.decode(encoding='utf-8', errors='strict')
            line = line.strip()
            if (line == ""):
                continue
            row = line.split(delim)
            txt = row[3].strip()
            image_path = str(row[4].strip())
            label = str(row[int(label_index)])
            txt = aidrtokenize.tokenize(txt)
            text = " ".join(txt)
            if (len(txt) < 1):
                print ("TEXT SIZE:" + str(txt))
                continue
            data.append(text)
            lab.append(label)
            image_list.append(image_path)
    counts = Counter(lab)
    print(counts)
    print(len(data))
    data_shuf = []
    lab_shuf = []
    image_list_shuf=[]
    index_shuf = [ i for i in range(len(data))]
    random.shuffle(index_shuf)
    for i in index_shuf:
        data_shuf.append(data[i])
        lab_shuf.append(lab[i])
        image_list_shuf.append(image_list[i])


    #print(data[0])
    le = preprocessing.LabelEncoder()
    yL = le.fit_transform(lab_shuf)
    labels = list(le.classes_)
    print("training classes: "+" ".join(labels))
    label = yL.tolist()
    yC = len(set(label))
    yR = len(label)
    y = np.zeros((yR, yC))
    y[np.arange(yR), yL] = 1
    y = np.array(y, dtype=np.int32)

    # finally, vectorize the text samples into a 2D integer tensor
    tokenizer = Tokenizer(num_words=MAX_NB_WORDS, oov_token="OOV_TOK")
    tokenizer.fit_on_texts(data_shuf)
    sequences = tokenizer.texts_to_sequences(data_shuf)

    word_index = tokenizer.word_index
    print('Found %s unique tokens.' % len(word_index))

    data = pad_sequences(sequences, maxlen=MAX_SEQUENCE_LENGTH, padding='post')

    # labels = to_categorical(np.asarray(labels))
    print('Shape of data tensor:', data.shape)
    # print('Shape of label tensor:', labels.shape)
    # return data,labels,word_index,dim;
    return data, image_list_shuf, y, le, labels, word_index, tokenizer


def read_dev_data_multimodal(data_file, text_file, label, tokenizer, MAX_SEQUENCE_LENGTH, delim, labels):
    """
    Prepare the data for both CrisisMMD, DMD and INCIDENTS.
    CrisisMMD uses ground truth labels, while DMD and INCIDENTS generates dummy labels.
    """
    ids = []
    data = []
    image_list = []
    lab = []

    for i in range(len(data_file)):
        txt = aidrtokenize.tokenize(text_file[i])
        text = " ".join(txt)
        if len(txt) < 1:
            print("TEXT SIZE:" + text)
            continue
        data.append(text)
        image_list.append(data_file[i])

        # Append labels only if they exist (CrisisMMD pathway)
        if label is not None:
            lab.append(label[i])

    # Initialize LabelEncoder and label-related structures
    le = preprocessing.LabelEncoder()
    le.fit(labels)
    labels = list(le.classes_)
    print("training classes: " + " ".join(labels))

    # Handle labels based on the dataset type
    if label is not None:
        # CrisisMMD pathway: use provided labels
        yL = le.transform(lab)
        yC = len(set(labels))  # Number of classes
        yR = len(lab)  # Number of samples
        y = np.zeros((yR, yC))
        y[np.arange(yR), yL] = 1
        y = np.array(y, dtype=np.int32)
    else:
        # DMD/INCIDENTS pathway: create dummy labels
        print("DMD/INCIDENTS: Generating dummy labels.")
        yC = len(labels)  # Use the number of classes from CrisisMMD to build label encoder
        yR = len(data_file)  # Number of samples
        y = np.zeros((yR, yC))  # All zero labels (no ground truth)

    # Tokenize text data
    sequences = tokenizer.texts_to_sequences(data)
    word_index = tokenizer.word_index
    print('Found %s unique tokens.' % len(word_index))

    # Pad sequences
    data = pad_sequences(sequences, maxlen=MAX_SEQUENCE_LENGTH, padding='post')
    print('Shape of data tensor:', data.shape)

    return data, image_list, y, le, labels


def convert_to_vector(inst_list, tokenizer, MAX_SEQUENCE_LENGTH, delim):
    """
    Prepare the data
    """
    #ids = []
    data = []
    lab = []
    for inst in inst_list:
        txt = aidrtokenize.tokenize(inst.text)
        text = " ".join(txt)
        if (len(txt) < 1):
            print ("TEXT SIZE:" + txt)
            continue
        data.append(text)
        lab.append(inst.label)


    le = preprocessing.LabelEncoder()
    yL = le.fit_transform(lab)
    labels = list(le.classes_)

    label = yL.tolist()
    yC = len(set(label))
    yR = len(label)
    y = np.zeros((yR, yC))
    y[np.arange(yR), yL] = 1
    y = np.array(y, dtype=np.int32)

    sequences = tokenizer.texts_to_sequences(data)
    word_index = tokenizer.word_index
    print('Found %s unique tokens.' % len(word_index))
    data = pad_sequences(sequences, maxlen=MAX_SEQUENCE_LENGTH)
    print('Shape of data tensor:', data.shape)
    return data, y, le, labels

    
def load_embedding(fileName):
    print('Indexing word vectors.')    
    embeddings_index = {}    
    f = open(fileName)
    for line in f:
        values = line.split()
        word = values[0]
        coefs = np.asarray(values[1:], dtype='float32')
        embeddings_index[word] = coefs
    f.close()    
    print('Found %s word vectors.' % len(embeddings_index))
    return embeddings_index;

def prepare_embedding(word_index, model, MAX_NB_WORDS, EMBEDDING_DIM):
    # prepare embedding matrix
    nb_words = min(MAX_NB_WORDS, len(word_index)+1)    
    embedding_matrix = np.zeros((nb_words, EMBEDDING_DIM),dtype=np.float32)
    print(len(embedding_matrix))
    for word, i in word_index.items():
        if i >= nb_words:
            continue
        try:
            embedding_vector = model[word][0:EMBEDDING_DIM] #embeddings_index.get(word)
            embedding_matrix[i] = np.asarray(embedding_vector,dtype = np.float32)
        except KeyError:
            try:
                rng = np.random.RandomState()        	
                embedding_vector = rng.randn(EMBEDDING_DIM) #np.random.random(num_features)
                embedding_matrix[i] = np.asarray(embedding_vector,dtype = np.float32)
            except KeyError:    
                continue      
    return embedding_matrix;