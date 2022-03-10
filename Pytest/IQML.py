import json;




class ML:
    def __init__(self):
        self.FILENAME = "machinelearned.json";
    def Learn(self,cid,pid,type):
        readjson = open("JSON_STORE/"+self.FILENAME, mode='r', encoding='utf-8');
        ml = json.load(readjson);
        match = 0;
        for x in ml["InvoiceID"]:
            if(x["cid"] == cid and x["pid"] == pid and x["type"] == type ):
                count = x["count"];
                x["count"] = count + 1;
                match = 1;
                with open("JSON_STORE/"+self.FILENAME, mode='w', encoding='utf-8') as feedsjson:
                    json.dump(ml, feedsjson,indent=4, sort_keys=True);
                    feedsjson.close();
        if(match == 0):
            ml["InvoiceID"].append({"cid":cid,"pid":pid,"type":type,"count":0});
            with open("JSON_STORE/"+self.FILENAME, mode='w', encoding='utf-8') as feedsjson:
                json.dump(ml, feedsjson,indent=4, sort_keys=True);
                feedsjson.close();
        readjson.close();

    def getInfofromCID(self,cid):
        with open("JSON_STORE/"+self.FILENAME, mode='r', encoding='utf-8') as feedsjson:
            ml = json.load(feedsjson);
            matching = [];
            feedsjson.close();
            for x in ml["InvoiceID"]:
                if(x["cid"] == cid):
                    matching.append(x);
        return matching;