import xml.etree.ElementTree as ET

class FetchXML:
	def __init__(self , filename):
		self.FILENAME = filename;

	def getXML(self):
		#print(self.FILENAME);
		tree = ET.parse(self.FILENAME);
		root = tree.getroot();
		mystring = "";
		#myarray = [];
		for dataarea in root:
			mystring += " " + str(dataarea.attrib['val']);
			#myarray.append(dataarea.attrib['val']);
		
		#print(myarray);
		#mystring = "";
		
		
		'''
		for dataarea in range(0,10):
			# print(dataarea.attrib['val']);
			#mystring += " " + str(dataarea.attrib['val']);
			mystring += " " + "Invoice No: " + str(dataarea);

		print(mystring);
		'''
		# subject = " Invoice Any Number of Words 068 ";
		# subject = mystring;
		
		return mystring;