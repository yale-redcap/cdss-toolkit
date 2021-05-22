<?php

$cdss_functions = <<<EOJSON
[
   {
      "id": "1",
      "name": "report_interaction",
      "params": "'drug1', 'drug2', 'consequence'",
      "label": "add an entry to the interactions report",
      "code": "",
      "comments":""
   },
   {
      "id": "2",
      "name": "report_overtreatment",
      "params": "'recommendation', 'disease/condition', 'disease/condition', ...",
      "label": "add an entry to the overtreatment report",
      "code": "",
      "comments":""
   },
   {
      "id": "3",
      "name": "report_dosing",
      "params": "'medication', 'recommendation'",
      "label": "add an entry to the dosing report",
      "code": "",
      "comments":""
   },
   {
      "id": "4",
      "name": "report_renal",
      "params": "'medication', 'recommendation', 'kidney function metric'",
      "label": "add an entry to the renal dosing report",
      "code": "",
      "comments":""
   },
   {
      "id": "5",
      "name": "report_comment",
      "params": "'free-text comment'",
      "label": "add a freetext entry to the medications management report",
      "code": "",
      "comments":""
   },
   {
      "id": "6",
      "name": "disease",
      "params": "'CDSS disease'",
      "label": "returns TRUE if the indicated CDSS disease diagnosis is present",
      "code": "",
      "comments":""
   },
   {
      "id": "7",
      "name": "medication",
      "params": "'CDSS medication'",
      "label": "returns TRUE if the indicated CDSS medication is present",
      "code": "",
      "comments":""
   },
   {
      "id": "8",
      "name": "dose",
      "params": "'CDSS medication'",
      "label": "returns the observed daily dose of the indicated CDSS medication",
      "code": "",
      "comments":""
   },
   {
      "id": "9",
      "name": "condition",
      "params": "'CDSS condition'",
      "label": "returns TRUE if the indicated CDSS condition is present",
      "code": "",
      "comments":""
   },
   {
      "id": "10",
      "name": "value",
      "params": "'CDSS variable or Study Field'",
      "label": "returns the observed value of the indicated CDSS variable or Study Field",
      "code": "",
      "comments":""
   },
   {
      "id": "11",
      "name": "valuetext",
      "params": "'CDSS variable or Study Field'",
      "label": "returns a report-friendly string containing the label and the observed value of the indicated CDSS variable or Study Field",
      "code": "",
      "comments":""
   }
]
EOJSON;
?>