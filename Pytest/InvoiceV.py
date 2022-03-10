import json;
import datetime;
import re;
from IQML import ML ;



class InvoiceID:

    def __init__(self,cid,xml):
        self.__REGF = json.load(open("JSON_STORE/regular.json"))["InvoiceID"];
        self.CID = cid;
        self.XML = xml;
        self.__ML = ML();

    def __getMaxPID(self):
        ml = self.__ML;
        #print("RID:" + str(ml.getInfofromCID(self.CID)));

        history = ml.getInfofromCID(self.CID);

        maxLearned = {"learn":[]};
        max = 0;
        for x in history:
            if (x["cid"] == self.CID):
                count = x["count"];
                if (max < count):
                    max = count;
                    # print(max)
                    maxLearned["learn"].append({"cid":x["cid"],"pid":x["pid"],"type":x["type"],"count":x["count"]})
                    pid = x["pid"];
                elif max == x["count"]:
                    maxLearned["learn"].append(
                        { "cid": x["cid"],"pid": x["pid"], "type": x["type"], "count": x["count"]});

        self.__getSorted(maxLearned["learn"]);
        return maxLearned["learn"];
    def __getSorted(self,maxL):
        if len(maxL)-1 != -1:
            max = maxL[len(maxL)-1]["count"];
            for y in range(0,len(maxL)):
                min = -1;
                prev = {};
                for x in range(len(maxL)-1, -1,-1):
                    if maxL[x]["count"] == max:
                        if min == -1:
                            min = maxL[x]["pid"];
                            prev = maxL[x];
                        elif maxL[x]["pid"] < min:
                            temp = {"cid":maxL[x]["cid"],"pid":maxL[x]["pid"],"type":maxL[x]["type"],"count":maxL[x]["count"]};#last wala temp
                            maxL[x] = {"cid":prev["cid"],"pid":prev["pid"],"type":prev["type"],"count":prev["count"]}; #prev last me
                            maxL[x+1] = temp;
                        else:
                            prev = maxL[x];
        #print("MAXL:" + str(maxL));
        return maxL;



    def __matchFromLearn(self):
        pid = self.__getMaxPID();
        #print("pid:"+str(pid));
        leng = len(pid)-1;

        lmatched = {"pid": -1, "type": "", "item": ""};
        if leng != -1:

            '''
            for idx, x in self.__REGF["pre"]:
                if x["pri"] == pid[len]["pid"]:
                    matches = re.search(x["pat"], self.XML);
                    if matches:
                        lmatched = {"pid": x["pri"], "type": "pre", "item": matches.group()};
                        print(
                            "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                                x["pri"]) + ", Pattern:" + str(
                                x["pat"]));
                        print("Matched Item:" + matches.group());
            '''

            for x in self.__REGF["pro"]:
                if x["pri"] == pid[leng]["pid"]:
                    matches = re.search(x["pat"], self.XML);
                    if matches:
                        try:
                            d = datetime.datetime.strptime(matches.group().strip(),"%d/%m/%Y");
                        except ValueError:
                            lmatched = {"pid": x["pri"], "type": "pro", "item": matches.group()};
                            '''print("Search Type: Probable ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                                x["pri"]) + ", Pattern:" + str(x["pat"]));
                                print("Matched Item:" + matches.group());'''

            for x in self.__REGF["pos"]:
                if x["pri"] == pid[leng]["pid"]:
                    matches = re.search(x["pat"], self.XML);
                    if (matches):
                        lmatched = {"pid": x["pri"], "type": "pos", "item": matches.group()};
                        '''print("Search Type: Possible, Matched ID:" + str(x["id"]) + ", Priority:" + str(
                            x["pri"]) + ", Pattern:" + str(
                            x["pat"]));
                        print("Matched Item:" + matches.group());'''

        #print(lmatched);

        return lmatched;

    def __freshInvoiceMatch(self):
        ml = self.__ML;
        freshmatched = {"matching": []};

        for x in self.__REGF["pre"]:
            matches = re.search(x["pat"], self.XML);
            #print(x);
            if (matches):
                ml.Learn(self.CID, x["pri"], "pre");
                '''
                print(
                    "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                        x["pri"]) + ", Pattern:" + str(
                        x["pat"]));
                print("Matched Item:" + matches.group());'''
                freshmatched["matching"].append({"pid": x["pri"], "type": "pre", "item": matches.group()});

        for x in self.__REGF["pro"]:
            matches = re.search(x["pat"], self.XML);
            if (matches):
                try:
                    d = datetime.datetime.strptime(matches.group().strip(), "%d/%m/%Y");
                except ValueError:
                    ml.Learn(self.CID,x["pri"],"pro");

                    ''' print("Search Type: Probable ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                        x["pri"]) + ", Pattern:" + str(x["pat"]));
                        print("Matched Item:" + matches.group());'''

                    freshmatched["matching"].append({"pid": x["pri"], "type": "pro", "item": matches.group()});
        for x in self.__REGF["pos"]:
            matches = re.search(x["pat"], self.XML);

            if (matches):
                ml.Learn(self.CID, x["pri"], "pos");
                '''print("Search Type: Possible, Matched ID:" + str(x["id"]) + ", Priority:" + str(
                    x["pri"]) + ", Pattern:" + str(
                    x["pat"]));
                print("Matched Item:" + matches.group());'''
                freshmatched["matching"].append({"pid": x["pri"], "type": "pos", "item": matches.group()});
        return freshmatched;

    def PredictInvoiceNumber(self):
        '''
        ml = self.__ML;
        print("LAST RID:" + str(ml.getInfofromCID(self.CID)));

        freshmatched = self.__freshInvoiceMatch();
        lmatched = self.__matchFromLearn();
        '''
        ml = self.__ML;
        freshmatched = self.__freshInvoiceMatch();
        lmatched = self.__matchFromLearn();

        #print("Learrn"+str(lmatched));
        #print("flearn:"+str(freshmatched["matching"]));
        if len(freshmatched["matching"])!=0:
            if freshmatched["matching"][0]["type"] == "pre":
                if lmatched["pid"] != -1:
                    if freshmatched["matching"][0]["pid"] < lmatched["pid"]:
                        #pid = freshmatched["matching"][0]["pid"];
                        #type = freshmatched["matching"][0]["type"];
                        #print("Matched GR:" + str(freshmatched["matching"][0]["item"]));
                        return freshmatched["matching"][0]["item"];
                        #ml.Learn(self.CID, pid,type);
                    elif freshmatched["matching"][0]["pid"] == lmatched["pid"]:
                        #pid = freshmatched["matching"][0]["pid"];
                        #type = freshmatched["matching"][0]["type"];
                        #print("Matched EQ:" + str(freshmatched["matching"][0]["item"]));
                        return freshmatched["matching"][0]["item"];
                        #ml.Learn(self.CID, pid,type);
                    else:
                        #pid = lmatched["pid"];
                        #type = lmatched["type"];
                        #print("Matched LE:" + str(lmatched["item"]));
                        return lmatched["item"];
                        #ml.Learn(self.CID, pid,type);
                else:
                    #pid = freshmatched["matching"][0]["pid"];
                    #type = freshmatched["matching"][0]["type"];
                    #print("Matched First F:" + str(freshmatched["matching"][0]["item"]));
                    return freshmatched["matching"][0]["item"];
                    #ml.Learn(self.CID, pid,type);
            else:
                #pid = lmatched["pid"];
                #type = lmatched["type"];
                #print("Matched First L:" + str(lmatched["item"]));
                return lmatched["item"];
            #ml.Learn(self.CID, pid, type);

class PAN:
    def __init__(self, cid, xml):
        self.__REGF = json.load(open("JSON_STORE/regular.json"))["PAN"];
        self.CID = cid;
        self.XML = xml;
        self.__ML = ML();
    def __freshPANMatch(self):
        ml = self.__ML;
        freshPAN = {"matching": []};
        for x in self.__REGF:
            matches = re.search(x["pat"], self.XML);
            #print(x);
            if (matches):
                '''
                print(
                    "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                        x["pri"]) + ", Pattern:" + str(
                        x["pat"]));
                print("Matched Item:" + matches.group());'''
                freshPAN["matching"].append({"pid": x["pri"], "type": "pre", "item": matches.group()});
        return freshPAN;

    def PredictPAN(self):
        pan =  self.__freshPANMatch();
        if len(pan["matching"]) > 0:
            return pan["matching"][0]["item"];
        else:
            return " ";
        #print(pan);

class InvoiceDate:
    def __init__(self, cid, xml):
        self.__REGF = json.load(open("JSON_STORE/regular.json"))["InvoiceDate"];
        self.CID = cid;
        self.XML = xml;
        self.__ML = ML();
    def __freshDateMatch(self):
        ml = self.__ML;
        freshDate = {"matching": []};
        for x in self.__REGF["pro"]:
            matches = re.search(x["pat"], self.XML);

            if (matches):
                #print("matches:"+str(matches));
                try:
                    #d1 = datetime.datetime.strptime(matches.group().strip(), "%d/%m/%Y");
                    #d2 = datetime.datetime.strptime(matches.group().strip(), "%d-%m-%Y");
                    #d3 = datetime.datetime.strptime(matches.group().strip(), "%m/%d/%Y");
                    #d4 = datetime.datetime.strptime(matches.group().strip(), "%m-%d-%Y");or d2 or d3 or d4
                    #d = datetime.datetime.strptime(matches.group().strip(),"%d/%m/%Y|%d-%m-%Y|%m/%d/%Y|%m-%d-%Y");
                    '''print(
                        "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                            x["pri"]) + ", Pattern:" + str(
                            x["pat"]));
                    print("Matched Item:" + matches.group());'''
                    freshDate["matching"].append({"pid": x["pri"], "type": "pro", "item": matches.group()});
                except ValueError:
                    print("Not a Valid DateTime")
        for x in self.__REGF["pos"]:
            matches = re.search(x["pat"], self.XML);

            if (matches):
                #print("matches:"+str(matches));
                try:
                    #d1 = datetime.datetime.strptime(matches.group().strip(), "%d/%m/%Y");
                    #d2 = datetime.datetime.strptime(matches.group().strip(), "%d-%m-%Y");
                    #d3 = datetime.datetime.strptime(matches.group().strip(), "%m/%d/%Y");
                    #d4 = datetime.datetime.strptime(matches.group().strip(), "%m-%d-%Y");or d2 or d3 or d4
                    #d = datetime.datetime.strptime(matches.group().strip(),"%d/%m/%Y|%d-%m-%Y|%m/%d/%Y|%m-%d-%Y");
                    ''' print(
                        "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                            x["pri"]) + ", Pattern:" + str(
                            x["pat"]));
                    print("Matched Item:" + matches.group());'''
                    freshDate["matching"].append({"pid": x["pri"], "type": "pos", "item": matches.group()});
                except ValueError:
                    print("Not a Valid DateTime")
        return freshDate;
    def PredictDate(self):
        da =  self.__freshDateMatch();
        #return da["matching"][0]["item"];
        if len(da["matching"]) > 0:
            return da["matching"][0]["item"];
        else:
            return "INVOICEDATE";
        #print(pan);
class GSTIN:
    def __init__(self, cid, xml):
        self.__REGF = json.load(open("JSON_STORE/regular.json"))["GSTIN"];
        self.CID = cid;
        self.XML = xml;
        self.__ML = ML();

    def __freshGSTINMatch(self):
        ml = self.__ML;
        freshPAN = {"matching": []};
        for x in self.__REGF:
            matches = re.search(x["pat"], self.XML);
            #print(x);
            if(matches):
                '''
                print(
                    "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                        x["pri"]) + ", Pattern:" + str(
                        x["pat"]));
                print("Matched Item:" + matches.group());'''
                freshPAN["matching"].append({"pid": x["pri"], "type": "pre", "item": matches.group()});
        return freshPAN;
    def PredictGSTIN(self):
        gstin =  self.__freshGSTINMatch();
        #print(gst);
        #return gstin;
        if len(gstin["matching"]) > 0:
            return gstin["matching"][0]["item"];
        else:
            return "GSTIN";

class PONumber:
    def __init__(self, cid, xml):
        self.__REGF = json.load(open("JSON_STORE/regular.json"))["PO"];
        self.CID = cid;
        self.XML = xml;
        self.__ML = ML();
    def __freshPOMatch(self):
        ml = self.__ML;
        freshPO = {"matching": []};
        for x in self.__REGF:
            matches = re.search(x["pat"], self.XML);
            #print(x);
            if(matches):
                '''
                print(
                    "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                        x["pri"]) + ", Pattern:" + str(
                        x["pat"]));
                print("Matched Item:" + matches.group());'''
                freshPO["matching"].append({"pid": x["pri"], "type": "pre", "item": matches.group()});
        return freshPO;
    def PredictPO(self):
        po =  self.__freshPOMatch();
        #return po;
        if len(po["matching"]) > 0:
            return po["matching"][0]["item"];
        else:
            return "PONUMBER";
        #print(pan);
class Amount:
    def __init__(self, cid, xml):
        self.__REGF = json.load(open("JSON_STORE/regular.json"))["Amount"];
        self.CID = cid;
        self.XML = xml;
        self.__ML = ML();
    def __freshAmountMatch(self):
        ml = self.__ML;
        freshAmount = {"matching": []};
        for x in self.__REGF:
            matches = re.search(x["pat"], self.XML);
            #print(x);
            if(matches):
                '''
                print(
                    "Search Type: Predefined ,Matched ID:" + str(x["id"]) + ", Priority:" + str(
                        x["pri"]) + ", Pattern:" + str(
                        x["pat"]));
                print("Matched Item:" + matches.group());'''
                freshAmount["matching"].append({"pid": x["pri"], "type": "pre", "item": matches.group()});
        return freshAmount;
    def PredictAmount(self):
        am =  self.__freshAmountMatch();
        #print(pan);
        #return am["matching"][0]["item"];
        if len(am["matching"]) > 0:
            return am["matching"][0]["item"];
        else:
            return "AMOUNT";


