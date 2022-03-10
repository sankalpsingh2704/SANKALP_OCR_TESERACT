import re;
import sys;

import json
import datetime
import xml.etree.ElementTree as ET
from pprint import pprint

# param1 = str(sys.argv[1]);


# 13726
# 31323
import xml.etree.ElementTree as ET
import InvoiceV;
import IQML;
import MyXML;

'''
class ML:

    def Learn(self,cid,rid):
        readjson = open("JSON_STORE/machinelearned.json", mode='r', encoding='utf-8')
        ml = json.load(readjson);
        match = 0;
        for x in ml["learned"]:
            if(x["cid"] == cid and x["rid"] == rid):
                count = x["count"];
                x["count"] = count + 1;

                match = 1;
                with open("JSON_STORE/machinelearned.json", mode='w', encoding='utf-8') as feedsjson:
                    json.dump(ml, feedsjson);
                    feedsjson.close();
        if(match == 0):
            ml["learned"].append({"cid":cid,"rid":rid,"count":0});
            with open("JSON_STORE/machinelearned.json", mode='w', encoding='utf-8') as feedsjson:
                json.dump(ml, feedsjson);
                feedsjson.close();
        readjson.close();

    def getInfofromCID(self,cid):
        with open("JSON_STORE/machinelearned.json", mode='r', encoding='utf-8') as feedsjson:
            ml = json.load(feedsjson);
            matching = [];
            feedsjson.close();
            for x in ml["learned"]:
                if(x["cid"] == cid):
                    matching.append(x);
        #print(matching);
        return matching;



'''




filename = str(sys.argv[1]);

cid = int(sys.argv[2]);

#print(str(filename)+str(cid));



#tree = ET.parse(filename);
#root = tree.getroot();


#print(root.tag);
#tokenarray.append(str(dataarea.attrib['val']));

'''
mystring = "";
tokenarray = [];

for dataarea in root:
	mystring += " " +  str(dataarea.attrib['val']);
	
	
print(mystring);

# subject = " Invoice Any Number of Words 068 ";
subject = mystring;
'''
t = MyXML.FetchXML(filename);

subject = t.getXML();

#print("Step 1");

# subject = param1;
# /d{1,4}-?/d{1,4}/s?\/\s?\d{1,4}\s
##print(subject);
# pattern = "\\s(INVOICE NUMBER|Invoice Number|invoice number|INVOICE NO|Invoice No|invoice no|Invoice|invoice|INVOICE){1}\\.?\\s*\\:?\\-?\\s*\\w*\\s*\\^d{1,10}\\s";
# pattern = " Invoice(\s|\w)*(\d+)";
# matches = re.search("(DC|dc)+\s?\/?-?\s?\d{3,6}\s", subject);
# matches = re.search(pattern, subject);
# print(matches);

#//regf = json.load(open("JSON_STORE/regular.json"));

# print(data["exp"][0]["pat"]);


#cid = 36;

#print(subject);
'''
xFile = json.load(open("JSON_STORE/regular.json"))["InvoiceID"];
for x in xFile["pro"]:
    matches = re.search(x["pat"], subject);
    print(x);
    if (matches):
        #ml.Learn(self.CID, x["pri"], "pre");

        print(
            "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                x["pri"]) + ", Pattern:" + str(
                x["pat"]));
        print("Matched Item:" + matches.group());
        #freshmatched["matching"].append({"pid": x["pri"], "type": "pre", "item": matches.group()});

'''


invoiceid = InvoiceV.InvoiceID(cid,subject);
#print("Step 2");

inv = invoiceid.PredictInvoiceNumber();

inv  = re.sub(r"\\","",inv);
#print("inv");
#print(inv);
#subject = " ABAPK263BK ";

#print(subject);

pan = InvoiceV.PAN(cid,subject);
p = pan.PredictPAN();
#print("pan");
#print(p);


gstin = InvoiceV.GSTIN(cid,subject);
g = gstin.PredictGSTIN();
g  = re.sub(r"GSTIN\/?UIN\s?:?.?\s?","",g);
#print("gstin");
#print(g);
if p == " ":
    p = g[3:len(g)-4];

#subject = " PO/17-18/0000198 ";
po = InvoiceV.PONumber(cid,subject);
vpo = po.PredictPO();
#print("po");
#print(vpo);


#subject = " 27-04-1992 ";

dat = InvoiceV.InvoiceDate(cid,subject);
da = dat.PredictDate();
#print("date");
#print(da);


#subject = " TOTAL INVOICE VALUE 38,836, ";




amount = InvoiceV.Amount(cid,subject);
am = amount.PredictAmount();
am = re.sub('[\sA-Za-z,/]+', '', am);
#print(am);

output = str(inv)+ "::"+str(p)+"::"+str(g)+"::"+str(vpo)+"::"+str(da)+"::"+str(am);

print(output);
'''

ml = IQML.ML();
print("RID:" + str(ml.getInfofromCID(cid)));
history = ml.getInfofromCID(cid);

pid = -1;
max = 0;
for x in history:
    if(x["cid"] == cid):
        count = x["count"];
        if(max < count):
            max = count;
            #print(max)
            pid = x["pid"];
            #pid = history.index({"cid":x["cid"], "pid":x["pid"], "count":x["count"]});
#{"id":7,"pri":18,"pat":"\\s(INVOICE NUMBER|Invoice Number|invoice number|INVOICE NO|Invoice No|invoice no|Invoice|invoice|INVOICE){1}\\.?\\s*\\:?\\-?\\s*\\w*\\s*\\d{1,10}\\s"}
lmatched = {"pid":-1,"type":"","item":""};
print(pid);
for x in regf["pre"]:
    if x["pri"] == pid:
        matches = re.search(x["pat"], subject);
        if matches:
            lmatched = {"pid":x["pri"],"type":"pre","item":matches.group()};
            print(
                "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                    x["pri"]) + ", Pattern:" + str(
                    x["pat"]));
            print("Matched Item:" + matches.group());
for x in regf["pro"]:
    if x["pri"] == pid:
        matches = re.search(x["pat"], subject);
        if matches:
            try:
                d = datetime.datetime.strptime(matches.group().strip(), "%d/%m/%Y");
            except ValueError:
                lmatched = {"pid": x["pri"], "type": "pro","item":matches.group()};
                print("Search Type: Probable ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                    x["pri"]) + ", Pattern:" + str(x["pat"]));
                print("Matched Item:" + matches.group());

for x in regf["pos"]:
    if x["pri"] == pid:
        matches = re.search(x["pat"], subject);
        if (matches):
            lmatched = {"pid": x["pri"], "type": "pos","item":matches.group()};
            print("Search Type: Possible, Matched ID:" + str(x["id"]) + ", Priority:" + str(x["pri"]) + ", Pattern:" + str(
                x["pat"]));
            print("Matched Item:" + matches.group());

print(lmatched);

'''











'''
freshmatched = {"matching":[]};
for x in regf["pre"]:
    matches = re.search(x["pat"], subject);
    if (matches):
        print(
            "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(x["pri"]) + ", Pattern:" + str(
                x["pat"]));
        print("Matched Item:" + matches.group());
        freshmatched["matching"].append({"pid": x["pri"], "type": "pre", "item": matches.group()});

for x in regf["pro"]:
    matches = re.search(x["pat"], subject);
    if (matches):
        try:
            d = datetime.datetime.strptime(matches.group().strip(), "%d/%m/%Y");
        except ValueError:
            print("Search Type: Probable ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                x["pri"]) + ", Pattern:" + str(x["pat"]));
            print("Matched Item:" + matches.group());
            freshmatched["matching"].append({"pid": x["pri"], "type": "pro", "item": matches.group()});
for x in regf["pos"]:
    matches = re.search(x["pat"], subject);

    if (matches):
        print("Search Type: Possible, Matched ID:" + str(x["id"]) + ", Priority:" + str(x["pri"]) + ", Pattern:" + str(
            x["pat"]));
        print("Matched Item:" + matches.group());
        freshmatched["matching"].append({"pid": x["pri"], "type": "pos", "item": matches.group()});
        
        
 '''



'''
venf = json.load(open("JSON_STORE/vendormaster.json"));

for x in venf["company"]:
    print("Company ID:" + str(x["id"]) + ", Company Name:" + str(x["name"]));
'''
#ml = IQML.ML();
#ml.Learn(1,4);
#{"cid": 1, "pid": 1, "count": 5}, {"cid": 1, "pid": 2, "count": 3}, {"cid": 1, "pid": 16, "count": 8}, {"cid": 1, "pid": 4, "count": 2}

'''


print(freshmatched);


if lmatched["pid"] != -1:
    if  freshmatched["matching"][0]["pid"] > lmatched["pid"]:
        pid = freshmatched["matching"][0]["pid"];
        print("Matched GR:"+ str(freshmatched["matching"][0]["item"]) );
        ml.Learn(cid,pid);
    elif freshmatched["matching"][0]["pid"] == lmatched["pid"]:
        pid = freshmatched["matching"][0]["pid"];
        print("Matched EQ:" + str(freshmatched["matching"][0]["item"]));
        ml.Learn(cid, pid);
    else:
        pid = lmatched["pid"];
        print("Matched LE:" + str(lmatched["item"]));
        ml.Learn(cid, pid);
else:
    pid = freshmatched["matching"][0]["pid"];
    print("Matched First:" + str(freshmatched["matching"][0]["item"]));
    ml.Learn(cid, pid);


# Learn(1,6);
# Learn(1,3);
'''