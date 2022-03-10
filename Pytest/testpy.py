# Python
import tensorflow as tf
# hello = tf.constant('Hello, TensorFlow!')
# sess = tf.Session()
# print(sess.run(hello))



import xml.etree.ElementTree as ET
tree = ET.parse('10823.pdf.xml')
root = tree.getroot()
#print(root.tag)
fullstring = ''
tokenarray = []
for dataarea in root:
    #print(dataarea.attrib['val'])
    fullstring += '\n' + dataarea.attrib['val']
    tokenarray.append(dataarea.attrib['val'])
print(fullstring);
#print(tokenarray);


def _floats_feature(value):
    return tf.train.Feature(float_list=tf.train.string_input_producer(value=value))






'''
train_filename = 'train.tfrecords'  # address to save the TFRecords file
writer = tf.python_io.TFRecordWriter(train_filename)

arr = range(1,100);

for c in range(0,1000):

    #get 2d and 3d coordinates and save in c2d and c3d

    feature = {'train/coord2d': _floats_feature(tokenarray),
                   'train/coord3d': _floats_feature(tokenarray)}
    sample = tf.train.Example(features=tf.train.Features(feature=feature))
    writer.write(sample.SerializeToString())

writer.close()
'''


train_filename = 'train.tfrecords'  # address to save the TFRecords file
writer = tf.python_io.TFRecordWriter(train_filename)



def _bytes_feature(value):
    return tf.train.Feature(bytes_list=tf.train.BytesList(value=[value]))


for dataarea in root:
    ann = dataarea.attrib['val']

    tf.compat.as_bytes(ann)

    example = tf.train.Example(features=tf.train.Features(feature={

        'annotation_raw': _bytes_feature(tf.compat.as_bytes(ann))
    }))

    writer.write(example.SerializeToString())

writer.close()

record_iterator = tf.python_io.tf_record_iterator(path=train_filename)

for string_record in record_iterator:
    example = tf.train.Example()
    example.ParseFromString(string_record)

    annotation_string = (example.features.feature['annotation_raw']
                         .bytes_list
                         .value[0])
    annotation_reconstructed = annotation_string.decode('utf-8')
    #print(annotation_reconstructed)

    # Recurrent Neural Network(RNN) and #Connectionist Text Proposal Network (CTPN)

